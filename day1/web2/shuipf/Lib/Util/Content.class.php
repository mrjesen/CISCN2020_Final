<?php

/**
 * 内容:添加/修改/删除
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Content {

    //内容数据模型
    protected $contentModel = NULL;
    //模型ID
    protected $modelid = 0;
    //错误信息
    protected $error = NULL;
    //URL对象
    protected $url = NULL;

    /**
     * 构造函数
     */
    function __construct() {
        import('Url');
        $this->url = get_instance_of('Url');
    }

    /**
     * 获取错误提示
     * @return type
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 添加内容
     * @param type $data 需要添加的数据
     * @return type 成功返回新增的信息ID
     */
    public function add($data) {
        $this->catid = (int) $data['catid'];
        $this->modelid = getCategory($this->catid, 'modelid');
        //取得表单令牌验证码
        $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
        //标签
        tag("content_add_begin", $data);
        //栏目数据
        $catidinfo = getCategory($this->catid);
        if (empty($catidinfo)) {
            $this->error = '获取不到栏目数据！';
            return false;
        }
        //setting配置
        $catidsetting = $catidinfo['setting'];
        //前台投稿状态判断
        if (defined('IN_ADMIN') && IN_ADMIN == false) {
            //前台投稿，根据栏目配置和用户配置
            $Member_group = F("Member_group");
            $groupid = AppframeAction::$Cache['User']['groupid'];
            //如果会员组设置中设置，投稿不需要审核，直接无视栏目设置
            if ($Member_group[$groupid]['allowpostverify']) {
                $data['status'] = 99;
            } else {
                //前台投稿是否需要审核
                if ($catidsetting['member_check']) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 99;
                }
            }
        }
        //检查真实发表时间，如果有时间转换为时间戳
        if ($data['inputtime'] && !is_numeric($data['inputtime'])) {
            $data['inputtime'] = strtotime($data['inputtime']);
        } elseif (!$data['inputtime']) {
            $data['inputtime'] = time();
        }
        //更新时间处理
        if ($data['updatetime'] && !is_numeric($data['updatetime'])) {
            $data['updatetime'] = strtotime($data['updatetime']);
        } elseif (!$data['updatetime']) {
            $data['updatetime'] = time();
        }
        //添加用户名
        $data['username'] = AppframeAction::$Cache['username'];
        //标识是否后台发布
        $data['sysadd'] = (defined('IN_ADMIN') && IN_ADMIN) ? 1 : 0;
        //自动提取摘要，如果有设置自动提取，且description为空，且有内容字段才执行
        if (isset($_POST['add_introduce']) && $data['description'] == '' && isset($data['content'])) {
            $content = $data['content'];
            $introcude_length = intval($_POST['introcude_length']);
            $data['description'] = str_cut(str_replace(array("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;'), '', strip_tags($content)), $introcude_length);
        }
        //数据模型对象
        $this->contentModel = ContentModel::getInstance($this->modelid);
        //载入数据处理类
        require_cache(RUNTIME_PATH . 'content_input.class.php');
        $content_input = new content_input($this->modelid);
        //保存一份旧数据
        $oldata = $data;
        $data = $content_input->get($data, 1);
        if ($data) {
            $data = $this->contentModel->create($data, 1);
            if (false == $data) {
                $this->error = $this->contentModel->getError();
                $this->contentModel->tokenRecovery($data);
                return false;
            }
        } else {
            $this->error = $content_input->getError();
            $this->contentModel->tokenRecovery($data);
            return false;
        }
        //取得副表下标
        $getRelationName = $this->contentModel->getRelationName();
        //检查content字段在主表还是副表
        $isContent = isset($data['content']) ? 1 : 0;
        //自动提取缩略图，从content 中提取
        if (empty($data['thumb'])) {
            $content = $isContent ? $data['content'] : $data[$getRelationName]['content'];
            $auto_thumb_no = I('.auto_thumb_no', 1, 'intval') - 1;
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $oldata['thumb'] = $data['thumb'] = $matches[3][$auto_thumb_no];
            }
        }
        //插入成功返回ID
        $id = $data['id'] = $oldata['id'] = $this->contentModel->relation(true)->add($data);
        if (false == $id) {
            $this->error = $this->contentModel->getError();
            $this->contentModel->tokenRecovery($data);
            return false;
        }
        //转向地址
        $urls = array();
        if ($data['islink'] == 1) {
            $urls['url'] = $_POST['linkurl'];
        } else {
            //生成该篇地址
            $urls = $this->url->show($data);
        }
        $oldata['url'] = $data['url'] = $urls['url'];
        //更新url
        $this->contentModel->token(false)->where(array('id' => $id))->save(array('url' => $data['url']));
        //更新到全站搜索
        if ($data['status'] == 99) {
            $this->search_api($id, $data);
        }

        //调用 update
        require_cache(RUNTIME_PATH . 'content_update.class.php');
        $content_update = new content_update($this->modelid);
        $updateStatus = $content_update->update($oldata);
        if (false == $updateStatus) {
            $this->error = $content_update->getError();
            return false;
        }

        //发布到其他栏目,只能后台发布才可以使用该功能
        if (defined('IN_ADMIN') && IN_ADMIN) {
            if (is_array($_POST['othor_catid'])) {
                foreach ($_POST['othor_catid'] as $classid => $v) {
                    if ($this->catid == $classid) {
                        continue;
                    }
                    $othor_catid[] = $classid;
                }
                //去除重复
                $othor_catid = array_unique($othor_catid);
                $this->othor_catid($othor_catid, $urls['url'], $data, $this->modelid);
            }
        }

        //字段合并
        $this->contentModel->dataMerger($data);

        //更新附件状态，把相关附件和文章进行管理
        $attachment = service("Attachment");
        $attachment->api_update('', 'c-' . $data['catid'] . '-' . $id, 2);

        //标签
        tag("content_add_end", $data);

        //生成相关
        $generatelish = 0;
        import('Html');
        $html = get_instance_of('Html');
        if (defined('IN_ADMIN') && IN_ADMIN) {
            //是否生成内容页
            if ($catidsetting['generatehtml']) {
                //生成静态
                if ($catidsetting['content_ishtml'] && $data['status'] == 99) {
                    $html->show($data, 0, 'add');
                }
            }
            //生成列表
            if ((int) $catidsetting['generatelish'] > 0) {
                $generatelish = (int) $catidsetting['generatelish'];
            }
        } else {
            //投稿内容页生成，直接审核通过的直接生成内容页
            if ($data['status'] == 99) {
                //生成静态
                if ($catidsetting['content_ishtml']) {
                    $html->show($data, 0, 'add');
                }
            }
            //列表生成
            if ((int) $catidsetting['member_generatelish'] > 0) {
                $generatelish = (int) $catidsetting['member_generatelish'];
            }
        }
        //列表生成
        switch ($generatelish) {
            //生成当前栏目
            case 1:
                $html->create_relation_html($data['catid']);
                break;
            //生成首页
            case 2:
                $html->index();
                break;
            //生成父栏目
            case 3:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成当前栏目与父栏目
            case 4:
                $html->category($data['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成父栏目与首页
            case 5:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
            //生成当前栏目、父栏目与首页
            case 6:
                $html->create_relation_html($data['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
        }
        //生成上一篇下一篇
        if ($data['status'] == 99) {
            $this->related_content($data['catid'], $id, "add");
        }
        return $id;
    }

    /**
     * 修改内容 
     * @param array $data 数据
     * @param type $id 信息ID
     * @return boolean 
     */
    public function edit($data, $id) {
        $data['id'] = $id;
        $this->catid = (int) $data['catid'];
        $this->modelid = getCategory($this->catid, 'modelid');
        //取得表单令牌验证码
        $data[C("TOKEN_NAME")] = $_POST[C("TOKEN_NAME")];
        //标签
        tag("content_edit_begin", $data);
        //栏目数据
        $catidinfo = getCategory($this->catid);
        if (empty($catidinfo)) {
            $this->error = '获取不到栏目数据！';
            return false;
        }
        //setting配置
        $catidsetting = $catidinfo['setting'];
        //前台投稿状态判断
        if (defined('IN_ADMIN') && IN_ADMIN == false) {
            //前台投稿编辑是否需要审核
            if ($catidsetting['member_editcheck']) {
                $data['status'] = 1;
            }
        }
        //数据模型对象
        $this->contentModel = ContentModel::getInstance($this->modelid);
        $data['inputtime'] = $inputtime = $this->contentModel->where(array("id" => $id))->getField("inputtime");
        //更新时间处理
        if ($data['updatetime'] && !is_numeric($data['updatetime'])) {
            $data['updatetime'] = strtotime($data['updatetime']);
        } elseif (!$data['updatetime']) {
            $data['updatetime'] = time();
        }
        //自动提取摘要，如果有设置自动提取，且description为空，且有内容字段才执行
        if (isset($_POST['add_introduce']) && $data['description'] == '' && isset($data['content'])) {
            $content = $data['content'];
            $introcude_length = intval($_POST['introcude_length']);
            $data['description'] = str_cut(str_replace(array("\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;'), '', strip_tags($content)), $introcude_length);
        }
        //转向地址
        if ($data['islink'] == 1) {
            $urls["url"] = $_POST['linkurl'];
        } else {
            //生成该篇地址
            $urls = $this->url->show($data);
        }
        $data['url'] = $urls["url"];
        //inputtime为真实发表时间，不允许修改
        if (isset($data['inputtime'])) {
            unset($data['inputtime']);
        }
        require_cache(RUNTIME_PATH . 'content_input.class.php');
        $content_input = new content_input($this->modelid);
        //保存一份旧数据
        $oldata = $data;
        $data = $content_input->get($data, 2);
        if ($data) {
            //数据验证
            $data = $this->contentModel->create($data, 2);
            if (false == $data) {
                $this->error = $this->contentModel->getError();
                $this->contentModel->tokenRecovery($data);
                return false;
            }
        } else {
            $this->error = $content_input->getError();
            return false;
        }
        //取得副表下标
        $getRelationName = $this->contentModel->getRelationName();
        //检查content字段在主表还是副表
        $isContent = isset($data['content']) ? 1 : 0;
        //自动提取缩略图，从content 中提取
        if (empty($data['thumb'])) {
            $content = $isContent ? $data['content'] : $data[$getRelationName]['content'];
            $auto_thumb_no = I('.auto_thumb_no', 1, 'intval') - 1;
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $oldata['thumb'] = $data['thumb'] = $matches[3][$auto_thumb_no];
            }
        }
        //数据修改，这里使用关联操作
        $status = $this->contentModel->relation(true)->where(array('id' => $id))->save($data);
        if (false === $status) {
            $this->error = $this->contentModel->getError();
            $this->contentModel->tokenRecovery($data);
            return false;
        }
        //字段合并
        $this->contentModel->dataMerger($data);
        $oldata['inputtime'] = $data['inputtime'] = $inputtime;
        //调用 update
        require_cache(RUNTIME_PATH . 'content_update.class.php');
        $content_update = new content_update($this->modelid);
        $updateStatus = $content_update->update($oldata);
        if (false == $updateStatus) {
            $this->error = $content_update->getError();
            return false;
        }
        //更新附件状态，把相关附件和文章进行管理
        $attachment = service("Attachment");
        $attachment->api_update('', 'c-' . $data['catid'] . '-' . $id, 2);
        //更新到全站搜索
        if ($data['status'] == 99) {
            $this->search_api($id, $data, "updata");
        } else {
            $this->search_api($id, $data, "delete");
        }

        //标签
        tag("content_edit_end", $data);
        //生成相关
        $generatelish = 0;
        import('Html');
        $html = get_instance_of('Html');
        if (defined('IN_ADMIN') && IN_ADMIN) {
            //是否生成内容页
            if ($catidsetting['generatehtml']) {
                //生成静态
                if ($catidsetting['content_ishtml'] && $data['status'] == 99) {
                    $html->show($data, 0, 'edit');
                }
            }
            //如果是未审核，删除已经生成
            if ($catidsetting['content_ishtml'] && !$data['islink'] && $data['status'] == 1) {
                $this->deleteHtml($data['catid'], $id, $inputtime, $data['prefix'], $data);
            }
            //生成列表
            if ((int) $catidsetting['generatelish'] > 0) {
                $generatelish = (int) $catidsetting['generatelish'];
            }
        } else {
            //投稿内容页生成，直接审核通过的直接生成内容页
            if ($data['status'] == 99) {
                //生成静态
                if ($catidsetting['content_ishtml']) {
                    $html->show($data, 0, 'edit');
                }
            } else {
                if ($catidsetting['content_ishtml'] && !$data['islink']) {
                    $this->deleteHtml($data['catid'], $id, $inputtime, $data['prefix'], $data);
                }
            }
            //列表生成
            if ((int) $catidsetting['member_generatelish'] > 0) {
                $generatelish = (int) $catidsetting['member_generatelish'];
            }
        }
        //列表生成
        switch ($generatelish) {
            //生成当前栏目
            case 1:
                $html->create_relation_html($data['catid']);
                break;
            //生成首页
            case 2:
                $html->index();
                break;
            //生成父栏目
            case 3:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成当前栏目与父栏目
            case 4:
                $html->create_relation_html($data['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                break;
            //生成父栏目与首页
            case 5:
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
            //生成当前栏目、父栏目与首页
            case 6:
                $html->create_relation_html($data['catid']);
                if ($catidinfo['parentid']) {
                    $html->create_relation_html($catidinfo['parentid']);
                }
                $html->index();
                break;
        }
        //生成上一篇下一篇
        $this->related_content($data['catid'], $id, "edit");
        return true;
    }

    /**
     * 删除内容
     * @param $id 内容id
     * @param $catid 栏目id
     */
    public function delete($id, $catid) {
        require_cache(RUNTIME_PATH . 'content_delete.class.php');
        $this->catid = (int) $catid;
        //模型ID
        $this->modelid = getCategory($this->catid, 'modelid');
        if (getCategory($this->catid) == false) {
            $this->error = '获取不到栏目信息！';
            return false;
        }
        //栏目配置信息
        $setting = getCategory($this->catid, 'setting');
        //内容页是否生成静态
        $content_ishtml = $setting['content_ishtml'];
        $this->contentModel = ContentModel::getInstance($this->modelid);
        $data = $this->contentModel->relation(true)->where(array("id" => $id))->find();
        if (empty($data)) {
            $this->error = '该信息不存在！';
            return false;
        }
        $this->contentModel->dataMerger($data);
        tag('content_delete_begin', $data);
        if ($content_ishtml && !$data['islink']) {
            $this->deleteHtml($this->catid, $id, $data['inputtime'], $data['prefix'], $data);
        }
        //删除内容
        $this->contentModel->relation(true)->where(array('id' => $id))->delete();
        //调用 content_delete
        $content_update = new content_delete($this->modelid);
        $content_update->get($data);
        //删除评论
        $comment_id = "c-$this->catid-$id";
        D('Comments')->deleteCommentsMark($comment_id);
        //删除附件
        $Attachment = service("Attachment");
        $Attachment->api_delete('c-' . $this->catid . '-' . $id);
        //删除全站搜索数据
        $this->search_api($id, $data, "delete");
        //删除推荐位的信息
        if (!empty($data['posid'])) {
            D('Position_data')->deleteByModeId($this->modelid, $id);
        }
        //标签
        tag("content_delete_end", $data);
        return true;
    }

    /**
     * 同步发布
     * @param type $othor_catid 需要同步发布到的栏目ID
     * @param type $linkurl 原信息地址
     * @param type $data 原数据，以关联表的数据格式
     * @param type $modelid 原信息模型ID
     * @return boolean
     */
    public function othor_catid($othor_catid, $linkurl, $data, $modelid) {
        //数据检测
        if (!$linkurl || !$othor_catid || !$data || !$modelid) {
            return false;
        }
        C('TOKEN_ON', false);
        //去除ID
        unset($data['id']);
        import('Html');
        $html = get_instance_of('Html');
        import('Url');
        $this->url = get_instance_of('Url');
        $this->contentModel = ContentModel::getInstance($modelid);
        //循环需要同步发布的栏目
        foreach ($othor_catid as $cid) {
            //获取需要同步栏目所属模型ID
            $mid = getCategory($cid, 'modelid');
            //判断模型是否相同
            if ($modelid == $mid) {//相同
                $data['catid'] = $cid;
                $_categorys = getCategory($data['catid']);
                //修复当被推送的文章是推荐位的文章时，推送后会把相应属性也推送过去
                $data['posid'] = 0;
                $newid = $this->contentModel->relation(true)->add($data);
                if (!$newid) {
                    continue;
                }
                $othordata = $data;
                $othordata['id'] = $newid;
                if (isset($othordata[$this->contentModel->getRelationName()]['id'])) {
                    $othordata[$this->contentModel->getRelationName()]['id'] = $newid;
                }
                //更新URL地址
                if ((int) $othordata['islink'] == 1) {
                    $nurls = $othordata['url'];
                    //更新地址
                    $this->contentModel->where(array('id' => $newid))->save(array('url' => $nurls));
                    $othordata['url'] = $nurls;
                } else {
                    $nurls = $this->url->show($othordata);
                    //更新地址
                    $this->contentModel->where(array('id' => $newid))->save(array('url' => $nurls['url']));
                    $othordata['url'] = $nurls['url'];
                }
                if (is_array($nurls) && $_categorys['setting']['content_ishtml'] && $othordata['status'] == 99) {
                    //生成静态
                    $html->show($othordata, 0, "add");
                }
            } else {
                $contentModel = ContentModel::getInstance($mid);
                //不同模型，则以链接的形式添加，也就是转向地址
                $dataarray = array('title' => $data['title'],
                    'style' => $data['style'],
                    'thumb' => $data['thumb'],
                    'keywords' => $data['keywords'],
                    'description' => $data['description'],
                    'status' => $data['status'],
                    'catid' => $cid,
                    'url' => $linkurl,
                    'sysadd' => 1,
                    'username' => $data['username'],
                    'inputtime' => $data['inputtime'],
                    'updatetime' => $data['updatetime'],
                    'islink' => 1
                );
                $newid = $contentModel->relation(true)->add($dataarray);
            }
        }
        return true;
    }

    /**
     * 信息审核
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $status 1为未审核，99为审核通过
     * @return boolean 
     */
    public function check($catid, $id, $status = 99) {
        C('TOKEN_ON', false);
        //模型ID
        $this->modelid = getCategory($catid, 'modelid');
        //是否生成HTML
        $sethtml = getCategory($catid, 'sethtml');
        //栏目配置信息
        $setting = $this->categorys[$catid]['setting'];
        $content_ishtml = $setting['content_ishtml'];
        $this->Content = ContentModel::getInstance($this->modelid);
        $r = $this->Content->relation(true)->where(array('id' => $id, 'catid' => $catid))->find();
        $this->Content->dataMerger($r);
        tag('content_check_begin', $r);
        if ($r) {
            if ($this->Content->where(array('id' => $id, 'catid' => $catid))->save(array("status" => $status))) {
                //判断是否前台投稿
                if ($r['sysadd'] == 0 && $status == 99 && isModuleInstall('Member')) {
                    //检查是否已经赠送过积分
                    $integral = M("MemberContent")->where(array("content_id" => $id, "catid" => $catid))->getField("integral");
                    if (!$integral) {
                        if (service("Passport")->user_integral($r['username'], $setting['member_addpoint'])) {
                            M("MemberContent")->where(array("content_id" => $id, "catid" => $catid))->save(array("integral" => 1));
                        }
                    }
                }
                //生成内容页
                if ($content_ishtml && !$r['islink'] && $status == 99) {
                    import('Html');
                    $html = get_instance_of('Html');
                    $html->show($r, 0, 'edit');
                    //生成上下篇
                    $this->related_content($catid, $id);
                }
                //如果是取消审核
                if ($content_ishtml && $status != 99) {
                    //则删除生成静态的文件
                    $this->deleteHtml($catid, $id, $r['inputtime'], $r['prefix'], $r);
                    //删除全站搜索数据
                    $this->search_api($id, $r, "delete");
                    //删除tags
                    D("Tags")->deleteAll($r['id'], $r['catid'], $this->modelid);
                } elseif ($status == 99) {
                    //更新全站搜索数据
                    $this->search_api($id, $r);
                    //更新tags
                    if (strpos($r['tags'], ',') === false) {
                        $tags = explode(' ', $r['tags']);
                    } else {
                        $tags = explode(',', $r['tags']);
                    }
                    $tags = array_unique($tags);
                    D("Tags")->updata($tags, $r['id'], $r['catid'], $this->modelid, array(
                        "url" => $r['url'],
                        "title" => $r['title'],
                    ));
                }
            }
        }
        tag('content_check_end', $r);
        return true;
    }

    /**
     * 删除静态生成的文章文件 
     * @param type $catid 栏目ID
     * @param type $id 信息ID
     * @param type $inputtime 真实发布时间
     * @param type $prefix 自定义文件名
     * @return type 
     */
    public function deleteHtml($catid, $id, $inputtime, $prefix = '', $data = false) {
        if ($data == false) {
            //模型ID
            $this->modelid = getCategory($catid, 'modelid');
            $this->Content = ContentModel::getInstance($this->modelid);
            $data = $this->Content->relation(true)->where(array('id' => $id, 'catid' => $catid))->find();
            $this->Content->dataMerger($data);
        }
        //获取信息生成地址和url
        $urls = $this->url->show($data);
        $fileurl = $urls['path'];
        //删除静态文件
        $lasttext = strrchr($fileurl, '.');
        $len = -strlen($lasttext);
        $path = substr($fileurl, 0, $len);
        $path = ltrim($path, '/');
        $filelist = glob(SITE_PATH . "/" . $path . '*');
        foreach ($filelist as $delfile) {
            $lasttext = strrchr($delfile, '.');
            if (!in_array($lasttext, array('.htm', '.html', '.shtml')))
                continue;
            @unlink($delfile);
        }
        return true;
    }

    /**
     * 上下篇生成
     * @param type $catid
     * @param type $id 
     */
    public function related_content($catid, $id, $action = "edit") {
        if (!$catid || !$id) {
            return;
        }
        $modelid = getCategory($catid, 'modelid');
        $db = ContentModel::getInstance($modelid);
        $where = array();
        $where['catid'] = $catid;
        $where['status'] = array("EQ", "99");
        $where['id'] = array("LT", $id);
        $data[] = $db->relation(true)->where($where)->order(array("id" => "DESC"))->find();
        if ($action == "edit") {
            $where['id'] = array("GT", $id);
            $data[] = $db->relation(true)->where($where)->find();
        }
        import('Html');
        $html = get_instance_of('Html');
        foreach ($data as $r) {
            if ($r['islink'] || empty($r['id']))
                continue;
            $db->dataMerger($r);
            $setting = getCategory($r['catid'], 'setting');
            $content_ishtml = $setting['content_ishtml'];
            if (!$content_ishtml) {
                continue;
            }
            $html->show($r, 1, 'edit');
        }
        return true;
    }

    /**
     * 更新搜索数据
     * @param type $id 信息id
     * @param type $data 数据
     * @param type $action 动作
     */
    private function search_api($id = 0, $data = array(), $action = 'add') {
        $App = F("App");
        if (!in_array("Search", $App)) {
            return false;
        }
        $db = D("Search/Search");
        //检查当前模型是否有在搜索数据源中
        $searchConfig = F("Search_config");
        //判断缓存，如果不存在，尝试更新缓存
        if (empty($searchConfig)) {
            $db->search_cache();
            $searchConfig = F("Search_config");
        }
        if (!in_array($this->modelid, $searchConfig['modelid'])) {
            return false;
        }
        return $db->search_api($id, $data, $this->modelid, $action);
    }

}
