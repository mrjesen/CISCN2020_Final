<?php

/**
 * 内容管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ContentAction extends AdminbaseAction {

    //模型缓存
    protected $model = array();
    //当前栏目ID
    protected $catid = 0;
    //内容数据模型
    private $contentModel = null;

    function _initialize() {
        parent::_initialize();
        //设置生成静态后缀为空，按URL规则生成相应后缀
        C('HTML_FILE_SUFFIX', "");
        //跳转时间
        $this->assign("waitSecond", 2000);
        $this->model = F("Model");
        //栏目ID
        $this->catid = I('request.catid', 0, 'intval');
        //所有的权限 都分为 add(添加) edit(编辑) delete(删除) index(默认操作) listorder(排序) remove(移动文章) push(推送)
        //权限判断  如果方法是以 public_开头的，也不验证权限
        $ADMIN_AUTH_KEY = session(C("ADMIN_AUTH_KEY"));
        //非超级管理员需要进行权限控制
        if (empty($ADMIN_AUTH_KEY) || $ADMIN_AUTH_KEY == false) {
            //如果是public_开头的方法通过验证
            if (strpos(ACTION_NAME, 'public_') === false && ACTION_NAME != "index") {
                //操作
                $action = getCategory($this->catid, 'type') == 0 ? ACTION_NAME : 'init';
                if ($action == "classlist") {
                    $action = "init";
                }
                $priv_datas = M("Category_priv")->where(array('catid' => $this->catid, 'is_admin' => 1, 'roleid' => session('roleid'), 'action' => $action))->select();
                if (!$priv_datas) {
                    $this->error("您没有操作该项的权限！");
                }
            }
        }
        import('Form');
        if (isset($_GET['catid']) && empty($this->model[getCategory($this->catid, 'modelid')]) && getCategory($this->catid, 'type') == 0) {
            $this->error("模型或者栏目不存在！！");
        }
    }

    //显示内容管理首页
    public function index() {
        $this->display();
    }

    //显示对应栏目信息列表 
    public function classlist() {
        $catInfo = getCategory($this->catid);
        //是否搜索
        $search = I('get.search');
        $where = array();
        $where["catid"] = array("EQ", $this->catid);
        if (!empty($catInfo)) {
            //栏目扩展配置
            $setting = $catInfo['setting'];
            //检查模型是否被禁用
            if ($this->model[$catInfo['modelid']]['disabled'] == 1) {
                $this->error("模型被禁用！");
            }
            $this->contentModel = ContentModel::getInstance($catInfo['modelid']);
            //搜索相关开始
            if (!empty($search)) {
                //添加开始时间
                $start_time = I('get.start_time');
                if (!empty($start_time)) {
                    $start_time = strtotime($start_time);
                    $where["inputtime"] = array("EGT", $start_time);
                }
                //添加结束时间
                $end_time = I('get.end_time');
                if (!empty($end_time)) {
                    $end_time = strtotime($end_time);
                    $where["inputtime"] = array("ELT", $end_time);
                }
                if ($end_time > 0 && $start_time > 0) {
                    $where['inputtime'] = array(array('EGT', $start_time), array('ELT', $end_time));
                }
                //推荐
                $posids = I('get.posids', 0, 'intval');
                if (!empty($posids)) {
                    $where["posid"] = array("EQ", $posids);
                }
                //搜索字段
                $searchtype = I('get.searchtype', null, 'intval');
                //搜索关键字
                $keyword = Input::getVar(I('get.keyword'));
                if (!empty($keyword)) {
                    $type_array = array('title', 'description', 'username');
                    if ($searchtype < 3) {
                        $searchtype = $type_array[$searchtype];
                        $where[$searchtype] = array("LIKE", "%{$keyword}%");
                    } elseif ($searchtype == 3) {
                        $where["id"] = array("EQ", (int) $keyword);
                    }
                }
                //状态
                $status = I('get.status', 0, 'intval');
                if ($status > 0) {
                    $where['status'] = array("EQ", $status);
                }
            }

            //信息总数
            $count = $this->contentModel->where($where)->count();
            $page = $this->page($count, 20);
            $data = $this->contentModel->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "DESC"))->select();

            //模板处理
            $template = "";
            if (!empty($setting['list_customtemplate'])) {
                $template = "Listtemplate:" . $setting['list_customtemplate'];
            }
        } else {
            $this->error("该栏目不存在！");
        }

        $this->assign("search", $search);
        $this->assign("start_time", $start_time);
        $this->assign("end_time", $end_time);
        $this->assign("posids", $posids);
        $this->assign("searchtype", $searchtype);
        $this->assign("keyword", $keyword);
        $this->assign($catInfo);
        $this->assign("count", $count);
        $this->assign("catid", $this->catid);
        $this->assign("Content", $data);
        $this->assign("Page", $page->show('Admin'));
        $this->display($template);
    }

    //添加信息 
    public function add() {
        if (IS_POST) {
            //栏目ID
            $catid = $_POST['info']['catid'] = intval($_POST['info']['catid']);
            if (empty($catid)) {
                $this->error("请指定栏目ID！");
            }
            if (trim($_POST['info']['title']) == '') {
                $this->error("标题不能为空！");
            }
            //获取当前栏目配置
            $category = getCategory($catid);
            //栏目类型为0
            if ($category['type'] == 0) {
                //模型ID
                $this->modelid = getCategory($catid, 'modelid');
                //检查模型是否被禁用
                if ($this->model[$this->modelid]['disabled'] == 1) {
                    $this->error("模型被禁用！");
                }
                //setting 配置
                $setting = $category['setting'];
                import('Content');
                $Content = get_instance_of('Content');
                $status = $Content->add($_POST['info']);
                if ($status) {
                    $this->success("添加成功！");
                } else {
                    $this->error($Content->getError());
                }
            } else if ($category['type'] == 1) {//单页栏目
                $db = D('Page');
                if ($db->savePage($_POST)) {
                    //扩展字段处理
                    if ($_POST['extend']) {
                        D("Category")->extendField($catid, $_POST);
                        //更新缓存
                        getCategory($this->catid, '', true);
                    }
                    import('Html');
                    $html = get_instance_of('Html');
                    $html->category($catid);
                    $this->success('操作成功！');
                } else {
                    $error = $db->getError();
                    $this->error($error ? $error : '操作失败！');
                }
            } else {
                $this->error("该栏目类型无法发布！");
            }
        } else {
            //取得对应模型
            $category = getCategory($this->catid);
            if (empty($category)) {
                $this->error("该栏目不存在！");
            }
            //判断是否终极栏目
            if ($category['child']) {
                $this->error("只有终极栏目可以发布文章！");
            }
            if ($category['type'] == 0) {
                //模型ID
                $modelid = $category['modelid'];
                //检查模型是否被禁用
                if ($this->model[$modelid]['disabled'] == 1) {
                    $this->error("该模型已被禁用！");
                }
                //引入输入表单处理类
                require_cache(RUNTIME_PATH . 'content_form.class.php');
                //实例化表单类 传入 模型ID 栏目ID 栏目数组
                $content_form = new content_form($modelid, $this->catid);
                //生成对应字段的输入表单
                $forminfos = $content_form->get();
                //生成对应的JS验证规则
                $formValidateRules = $content_form->formValidateRules;
                //js验证不通过提示语
                $formValidateMessages = $content_form->formValidateMessages;
                //js
                $formJavascript = $content_form->formJavascript;
                //取得当前栏目setting配置信息
                $setting = $category['setting'];
                //var_dump($category);exit;
                $this->assign("catid", $this->catid);
                $this->assign("uploadurl", CONFIG_SITEFILEURL);
                $this->assign("content_form", $content_form);
                $this->assign("forminfos", $forminfos);
                $this->assign("formValidateRules", $formValidateRules);
                $this->assign("formValidateMessages", $formValidateMessages);
                $this->assign("formJavascript", $formJavascript);
                $this->assign("setting", $setting);
                $this->assign("category", $category);
                $this->display();
            } else if ($category['type'] == 1) {//单网页模型
                $info = D('Page')->getPage($this->catid);
                if ($info && $info['style']) {
                    $style = explode(';', $info['style']);
                    $info['style_color'] = $style[0];
                    if ($style[1]) {
                        $info['style_font_weight'] = $style[1];
                    }
                }
                $extend = $category['setting']['extend'];

                $this->assign("catid", $this->catid);
                $this->assign("uploadurl", CONFIG_SITEFILEURL);
                $this->assign("setting", $setting);
                $this->assign('extend', $extend);
                $this->assign('info', $info);
                $this->assign("category", $category);
                //栏目扩展字段
                $this->assign('extendList', D("Category")->getExtendField($this->catid));
                $this->display('singlepage');
            }
        }
    }

    //编辑信息 
    public function edit() {
        $this->catid = empty($this->catid) ? (int) $_POST['info']['catid'] : $this->catid;
        //信息ID
        $id = I('request.id', 0, 'intval');
        $Categorys = getCategory($this->catid);
        if (empty($Categorys)) {
            $this->error("该栏目不存在！");
        }
        //栏目setting配置
        $cat_setting = $Categorys['setting'];
        //模型ID
        $modelid = $Categorys['modelid'];
        //检查模型是否被禁用
        if ($this->model[$Categorys['modelid']]['disabled'] == 1) {
            $this->error("模型被禁用！");
        }
        $this->contentModel = ContentModel::getInstance($modelid);
        //检查是否锁定
        if (false === $this->contentModel->locking($this->catid, $id)) {
            $this->error($this->contentModel->getError());
        }

        if (IS_POST) {
            if (trim($_POST['info']['title']) == '') {
                $this->error("标题不能为空！");
            }
            import('Content');
            $Content = get_instance_of('Content');
            //取得原有文章信息
            $data = $this->contentModel->where(array("id" => $id))->find();
            //如果有自定义文件名，需要删除原来生成的静态文件
            if ($_POST['info']['prefix'] != $data['prefix'] && $cat_setting['content_ishtml']) {
                //删除原来的生成的静态页面
                $Content->deleteHtml($this->catid, $id, $data['inputtime'], $data['prefix'], $data);
            }
            $status = $Content->edit($_POST['info'], $id);
            if ($status) {
                //解除信息锁定
                M("Locking")->where(array("userid" => AppframeAction::$Cache["uid"], "catid" => $catid, "id" => $id))->delete();
                $this->success("修改成功！");
            } else {
                $this->error($Content->getError());
            }
        } else {
            //取得数据，这里使用关联查询
            $data = $this->contentModel->relation(true)->where(array("id" => $id))->find();
            if (empty($data)) {
                $this->error("该信息不存在！");
            }
            $this->contentModel->dataMerger($data);
            //锁定信息
            M("Locking")->add(array(
                "userid" => AppframeAction::$Cache["uid"],
                "username" => AppframeAction::$Cache["username"],
                "catid" => $this->catid,
                "id" => $id,
                "locktime" => time()
            ));
            //引入输入表单处理类
            require_cache(RUNTIME_PATH . 'content_form.class.php');
            $content_form = new content_form($modelid, $this->catid);
            //字段内容
            $forminfos = $content_form->get($data);
            //生成对应的JS验证规则
            $formValidateRules = $content_form->formValidateRules;
            //js验证不通过提示语
            $formValidateMessages = $content_form->formValidateMessages;
            //js
            $formJavascript = $content_form->formJavascript;
            $this->assign("category", $Categorys);
            $this->assign("data", $data);
            $this->assign("catid", $this->catid);
            $this->assign("id", $id);
            $this->assign("uploadurl", CONFIG_SITEFILEURL);
            $this->assign("content_form", $content_form);
            $this->assign("forminfos", $forminfos);
            $this->assign("formValidateRules", $formValidateRules);
            $this->assign("formValidateMessages", $formValidateMessages);
            $this->assign("formJavascript", $formJavascript);
            if ($category['type'] == 1) {
                $this->display('singlepage_edit');
            } else {
                $this->display();
            }
        }
    }

    //删除
    public function delete() {
        if (IS_POST) {
            $this->catid = I('get.catid', 0, 'intval');
            $Categorys = getCategory($this->catid);
            if (empty($Categorys)) {
                $this->error("该栏目不存在！");
            }
            //模型ID
            $modelid = $Categorys['modelid'];
            if (empty($_POST['ids'])) {
                $this->error("没有信息被选中！");
            }
            $this->contentModel = ContentModel::getInstance($modelid);
            import('Content');
            $Content = get_instance_of('Content');
            foreach ($_POST['ids'] as $id) {
                //检查是否锁定
                if (false === $this->contentModel->locking($this->catid, $id)) {
                    $this->error($this->contentModel->getError());
                }
                $Content->delete($id, $this->catid);
            }
            $this->success("删除成功！");
        } else {
            $this->catid = I('get.catid', 0, 'intval');
            $id = I('get.id', 0, 'intval');
            $Categorys = getCategory($this->catid);
            if (empty($Categorys)) {
                $this->error("该栏目不存在！");
            }
            //模型ID
            $modelid = $Categorys['modelid'];
            $this->contentModel = ContentModel::getInstance($modelid);
            //检查是否锁定
            if (false === $this->contentModel->locking($this->catid, $id)) {
                $this->error($this->contentModel->getError());
            }
            import('Content');
            $Content = get_instance_of('Content');
            if ($Content->delete($id, $this->catid)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    //文章审核
    public function public_check() {
        import('Content');
        $Content = get_instance_of('Content');
        if (IS_POST) {
            $ids = $_POST['ids'];
            if (!$ids) {
                $this->error("没有信息被选中！");
            }
            foreach ($ids as $id) {
                $Content->check($this->catid, $id, 99);
            }
            $this->success("审核成功！");
        } else {
            $id = I('get.id', 0, 'intval');
            if (!$id) {
                $this->error("没有信息被选中！");
            }
            if ($Content->check($this->catid, $id, 99)) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
    }

    //取消审核
    public function public_nocheck() {
        import('Content');
        $Content = get_instance_of('Content');
        if (IS_POST) {
            $ids = $_POST['ids'];
            if (!$ids) {
                $this->error("没有信息被选中！");
            }
            foreach ($ids as $id) {
                $Content->check($this->catid, $id, 1);
            }
            $this->success("取消审核成功！");
        } else {
            $id = I('get.id', 0, 'intval');
            if (!$id) {
                $this->error("没有信息被选中！");
            }
            if ($Content->check($this->catid, $id, 1)) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    //排序
    public function listorder() {
        $listorders = $_POST['listorders'];
        if (is_array($listorders)) {
            $category = getCategory($this->catid);
            $modelid = $category['modelid'];
            $table_name = ucwords($this->model[$modelid]['tablename']);
            $db = M($table_name);
            foreach ($listorders as $id => $v) {
                $db->where(array("id" => $id))->save(array("listorder" => $v));
            }
            $this->success("更新成功！", U("Contents/Content/classlist", array('catid' => $this->catid)));
        } else {
            $this->error("参数错误！");
        }
    }

    //显示栏目菜单列表 
    public function public_categorys() {
        $priv_catids = array();
        //栏目权限 超级管理员例外
        if (session(C("ADMIN_AUTH_KEY")) == "") {
            $role_id = AppframeAction::$Cache['User']['role_id'];
            $priv_result = M("Category_priv")->where(array("roleid" => $role_id, 'action' => 'init'))->select();
            foreach ($priv_result as $_v) {
                $priv_catids[] = $_v['catid'];
            }
        }
        $json = array();
        $categorys = F("Category");
        foreach ($categorys as $rs) {
            if ($rs['type'] == 2 && $rs['child'] == 0) {
                continue;
            }
            //只显示有init权限的，超级管理员除外
            if (session(C("ADMIN_AUTH_KEY")) == "" && !in_array($rs['catid'], $priv_catids)) {
                $arrchildid = explode(',', $rs['arrchildid']);
                $array_intersect = array_intersect($priv_catids, $arrchildid);
                if (empty($array_intersect)) {
                    continue;
                }
            }
            $data = array(
                'catid' => $rs['catid'],
                'parentid' => $rs['parentid'],
                'catname' => $rs['catname'],
                'type' => $rs['type'],
            );
            //终极栏目
            if ($rs['child'] == 0) {
                $data['target'] = "right";
                $data['url'] = U("Contents/Content/classlist", array("catid" => $rs['catid']));
                //设置图标 
                $data['icon'] = CONFIG_SITEURL . "statics/js/zTree/zTreeStyle/img/diy/10.png";
            } else {
                $data['isParent'] = true;
            }
            //单页
            if ($rs['type'] == 1 && $rs['child'] == 0) {
                $data['url'] = U("Contents/Content/add", array("catid" => $rs['catid']));
                //设置图标 
                $data['icon'] = CONFIG_SITEURL . "statics/js/zTree/zTreeStyle/img/diy/2.png";
            }
            $json[] = $data;
        }
        $this->assign('json', json_encode($json));
        $this->display();
    }

    /**
     * 检测标题是否存在
     * @param type $title 标题
     * @param type $catid 栏目
     * @return boolean
     */
    public function public_check_title($title = "", $catid = "") {
        $title = empty($title) ? I('get.data') : $title;
        $catid = empty($catid) ? $this->catid : $catid;
        if (empty($title)) {
            $this->ajaxReturn("", "标题没有重复！", true);
            return false;
        }
        $tablename = ucwords($this->model[getCategory($catid, 'modelid')]['tablename']);
        $count = M($tablename)->where(array("title" => $title))->count();
        if ($count > 0) {
            $this->ajaxReturn("", "标题有重复！", false);
        } else {
            $this->ajaxReturn("", "标题没有重复！", true);
        }
    }

    //关文章选择
    public function public_relationlist() {
        if (!isset($_GET['modelid'])) {
            $this->error("缺少参数！");
        } else {
            $modelid = I('get.modelid', 0, 'intval');
            $this->table_name = ucwords($this->model[$modelid]['tablename']);
            $this->Content = M($this->table_name);
            $where = array();
            $catid = $this->catid;
            if ($catid) {
                $where['catid'] = array('eq', $catid);
            }
            $where['status'] = array('eq', 99);
            if (isset($_GET['keywords'])) {
                $keywords = trim($_GET['keywords']);
                $field = $_GET['searchtype'];
                if (in_array($field, array('id', 'title', 'keywords', 'description'))) {
                    if ($field == 'id') {
                        $where['id'] = array('eq', $keywords);
                    } else {
                        $where[$field] = array('like', '%' . $keywords . '%');
                    }
                }
            }
            $count = $this->Content->where($where)->count();
            $page = $this->page($count, 12);
            $data = $this->Content->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "DESC"))->select();
            $this->assign("Formcategory", Form::select_category($catid, 'name="catid"', "不限栏目", $modelid, 0, 1));
            $this->assign("data", $data);
            $this->assign("Page", $page->show('Admin'));
            $this->assign("modelid", $modelid);
            $this->display("relationlist");
        }
    }

    //文章预览 
    public function public_preview() {
        
    }

    //图片裁减 
    public function public_imagescrop() {
        $picurl = I('get.picurl');
        $catid = I('get.catid', $this->catid, 'intval');
        if (!$catid) {
            $this->error('栏目不存在！');
        }
        $module = I('get.module', GROUP_NAME);
        $this->assign("picurl", $picurl);
        $this->assign("catid", $catid);
        $this->assign("module", $module);
        $this->display("imagescrop");
    }

    //显示栏目列表，树状
    public function public_getsite_categorys() {
        $catid = $this->catid;
        import('Tree');
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $categorys = array();
        if (empty($_SESSION[C("ADMIN_AUTH_KEY")])) {
            $this->priv_db = M("Category_priv");
            $priv_result = $this->priv_db->where(array('action' => 'add', 'roleid' => $_SESSION['roleid'], 'is_admin' => 1))->select();
            $priv_catids = array();
            foreach ($priv_result as $_v) {
                $priv_catids[] = $_v['catid'];
            }
            if (empty($priv_catids))
                return '';
        }
        $categorysList = F("Category");
        foreach ($categorysList as $r) {
            if ($r['type'] != 0)
                continue;
            if (session("roleid") != 1 && !in_array($r['catid'], $priv_catids)) {
                $arrchildid = explode(',', $r['arrchildid']);
                $array_intersect = array_intersect($priv_catids, $arrchildid);
                if (empty($array_intersect))
                    continue;
            }
            $r['modelname'] = $this->model[$r['modelid']]['name'];
            $r['style'] = $r['child'] ? 'color:#8A8A8A;' : '';
            $r['click'] = $r['child'] ? '' : " id=\"cv" . $r['catid'] . "\" onclick=\"select_list(this,'" . safe_replace($r['catname']) . "'," . $r['catid'] . ")\" class='cu' title='" . safe_replace($r['catname']) . "'";
            $categorys[$r['catid']] = $r;
        }
        $str = "<tr \$click >
					<td align='center'>\$id</td>
					<td style='\$style'>\$spacer\$catname</td>
					<td align='center'>\$modelname</td>
				</tr>";
        $tree->init($categorys);
        $categorys = $tree->get_tree(0, $str);
        exit($categorys);
    }

    //加载相关文章列表 
    public function public_getjson_ids() {
        $modelid = I('get.modelid', 0, 'intval');
        $id = I('get.id', 0, 'intval');
        $this->Content = ContentModel::getInstance($modelid);
        if (false == $this->Content) {
            return false;
        }
        $r = $this->Content->where(array("id" => $id))->find();
        $this->Content->dataMerger($r);
        $where = array();
        if ($r['relation']) {
            $relation = str_replace('|', ',', $r['relation']);
            $where['id'] = array("in", $relation);
            $datas = $this->Content->where($where)->select();
            $this->Content->dataMerger($datas);
            foreach ($datas as $_v) {
                $_v['sid'] = 'v' . $_v['id'];
                $infos[] = $_v;
            }
        }
        $this->ajaxReturn($infos, "", true);
    }

    //批量移动文章
    public function remove() {
        if (IS_POST && isset($_POST['fromtype'])) {
            $catid = I('get.catid', '', 'intval');
            if (!$catid) {
                $this->error("请指定栏目！");
            }
            //移动类型
            $fromtype = I('post.fromtype', '', 'intval');
            //需要移动的信息ID集合
            $ids = $_POST['ids'];
            //需要移动的栏目ID集合
            $fromid = $_POST['fromid'];
            //目标栏目
            $tocatid = I('post.tocatid', '', 'intval');
            if (!$tocatid) {
                $this->error("目标栏目不正确！");
            }
            import('Content');
            $Content = get_instance_of('Content');
            switch ($fromtype) {
                //信息移动
                case 0:
                    if ($ids) {
                        if ($tocatid == $catid) {
                            $this->error("目标栏目和当前栏目是同一个栏目！");
                        }
                        $modelid = getCategory($tocatid, 'modelid');
                        if (!$modelid) {
                            $this->error("该模型不存在！");
                        }
                        $this->contentModel = ContentModel::getInstance($modelid);
                        import('Url');
                        $this->url = get_instance_of('Url');
                        //表名
                        $tablename = ucwords($this->model[$modelid]['tablename']);
                        if (!$ids) {
                            $this->error("请选择需要移动信息！");
                        }
                        $ids = array_filter(explode('|', $_POST['ids']), "intval");
                        //删除静态文件
                        foreach ($ids as $sid) {
                            $data = $this->contentModel->where(array('catid' => $catid, 'id' => $sid))->find();
                            $Content->deleteHtml($catid, $sid, $data['inputtime'], $data['prefix'], $data);
                            $data['catid'] = $tocatid;
                            $urls = $this->url->show($data);
                            $this->contentModel->where(array('catid' => $catid, 'id' => $sid))->save(array("catid" => $tocatid, 'url' => $urls['url']));
                        }
                        $this->success("移动成功！", U("Createhtml/update_urls"));
                    } else {
                        $this->error("请选择需要移动的信息！");
                    }
                    break;
                //栏目移动
                case 1:
                    if (!$fromid) {
                        $this->error("请选择需要移动的栏目！");
                    }
                    $where = array();
                    $where['catid'] = array("IN", $fromid);
                    $modelid = getCategory($catid, 'modelid');
                    if (!$modelid) {
                        $this->error("该模型不存在！");
                    }
                    $tablename = ucwords($this->model[$modelid]['tablename']);
                    //进行栏目id更改
                    if (M($tablename)->where($where)->save(array("catid" => $tocatid, 'url' => ''))) {
                        $this->success("移动成功，请使用《批量更新URL》更新新的地址！！", U("Createhtml/update_urls"));
                    } else {
                        $this->error("移动失败");
                    }
                    break;
                default:
                    $this->error("请选择移动类型！");
                    break;
            }
        } else {
            $ids = I('request.ids', '', '');
            $ids = is_array($ids) ? implode("|", $ids) : $ids;
            $catid = I('get.catid', '', 'intval');
            if (!$catid) {
                $this->error("请指定栏目！");
            }
            $modelid = getCategory($catid, 'modelid');
            import("Tree");
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;│ ', '&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;';
            $categorys = array();
            $categorysList = F("Category");
            foreach ($categorysList as $cid => $r) {
                if ($r['type'])
                    continue;
                if ($modelid && $modelid != $r['modelid'])
                    continue;
                $r['disabled'] = $r['child'] ? 'disabled' : '';
                $r['selected'] = $cid == $catid ? 'selected' : '';
                $categorys[$cid] = $r;
            }
            $str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            $tree->init($categorys);
            $string .= $tree->get_tree(0, $str);

            $str = "<option value='\$catid'>\$spacer \$catname</option>";
            $source_string = '';
            $tree->init($categorys);
            $source_string .= $tree->get_tree(0, $str);

            $this->assign("ids", $ids);
            $this->assign("string", $string);
            $this->assign("source_string", $source_string);
            $this->assign("catid", $catid);
            $this->display();
        }
    }

    //文章推送
    public function push() {
        if (IS_POST) {
            $id = I('post.id');
            $modelid = I('post.modelid');
            $catid = I('post.catid');
            $action = I('get.action');
            if (!$id || !$action || !$modelid || !$catid) {
                $this->error("参数不正确");
            }
            switch ($action) {
                //推荐位
                case "position_list":
                    $posid = $_POST['posid'];
                    if ($posid && is_array($posid)) {
                        $position_data_db = D('Position');
                        $fields = F("Model_field_" . $modelid);
                        $tablename = ucwords($this->model[$modelid]['tablename']);
                        if (!$tablename) {
                            $this->error("模型不能为空！");
                        }
                        $ids = explode("|", $id);
                        $Content = ContentModel::getInstance($modelid);
                        foreach ($ids as $k => $aid) {
                            //取得信息
                            $re = $Content->relation(true)->where(array("id" => $aid))->find();
                            if ($re) {
                                $Content->dataMerger($re);
                                //推送数据
                                $textcontent = array();
                                foreach ($fields AS $_key => $_value) {
                                    //判断字段是否入库到推荐位字段
                                    if ($_value['isposition']) {
                                        $textcontent[$_key] = $re[$_key];
                                    }
                                }
                                //样式进行特别处理
                                $textcontent['style'] = $re['style'];
                                //推送到推荐位
                                $status = $position_data_db->position_update($aid, $modelid, $catid, $posid, $textcontent, 0, 1);
                                $r = $re = null;
                            }
                        }
                        $this->success("推送到推荐位成功！");
                    } else {
                        $this->error("请选择推荐位！");
                    }
                    break;
                //同步发布到其他栏目
                case "push_to_category":
                    $ids = explode("|", $id);
                    $relation = I("post.relation");
                    if (!$relation) {
                        $this->error("请选择需要推送的栏目!");
                    }
                    $relation = explode("|", $relation);
                    if (is_array($relation)) {
                        //过滤相同栏目和自身栏目
                        foreach ($relation as $k => $classid) {
                            if ($classid == $catid) {
                                unset($relation[$k]);
                            }
                        }
                        //去除重复
                        $relation = array_unique($relation);
                        if (count($relation) < 1) {
                            $this->error("请选择需要推送的栏目！");
                        }
                        $tablename = ucwords($this->model[$modelid]['tablename']);
                        if (!$tablename) {
                            $this->error("模型不能为空！");
                        }
                        $Content = ContentModel::getInstance($modelid);
                        import('Content');
                        $ContentAPI = new Content();
                        foreach ($ids as $k => $aid) {
                            //取得信息
                            $r = $Content->relation(true)->where(array("id" => $aid))->find();
                            $linkurl = $r['url'];
                            if ($r) {
                                $ContentAPI->othor_catid($relation, $linkurl, $r, $modelid);
                            }
                        }
                        $this->success("推送其他栏目成功！");
                    } else {
                        $this->error("请选择需要推送的栏目！");
                    }
                    break;
                default:
                    $this->error("请选择操作！");
                    break;
            }
        } else {
            $id = I('get.id');
            $action = I('get.action');
            $modelid = I('get.modelid');
            $catid = I("get.catid");
            if (!$id || !$action || !$modelid || !$catid) {
                $this->error("参数不正确！");
            }
            $tpl = $action == "position_list" ? "push_list" : "push_to_category";

            switch ($action) {
                //推荐位
                case "position_list":
                    $position = F("Position");
                    if (!empty($position)) {
                        $array = array();
                        foreach ($position as $_key => $_value) {
                            //如果有设置模型，检查是否有该模型
                            if ($_value['modelid'] && !in_array($modelid, explode(',', $_value['modelid']))) {
                                continue;
                            }
                            //如果设置了模型，又设置了栏目
                            if ($_value['modelid'] && $_value['catid'] && !in_array($catid, explode(',', $_value['catid']))) {
                                continue;
                            }
                            //如果设置了栏目
                            if ($_value['catid'] && !in_array($catid, explode(',', $_value['catid']))) {
                                continue;
                            }
                            $array[$_key] = $_value['name'];
                        }
                        $this->assign("Position", $array);
                    }
                    break;
                //同步发布到其他栏目
                case "push_to_category":
                    break;
                default:
                    $this->error("请选择操作！");
                    break;
            }

            $this->assign("id", $id);
            $this->assign("action", $action);
            $this->assign("modelid", $modelid);
            $this->assign("catid", $catid);
            $this->assign("show_header", true);
            $this->display($tpl);
        }
    }

    //同时发布到其他栏目选择页面
    public function public_othors() {
        $catid = I('get.catid', 0, 'intval');
        $this->assign("catid", $catid);
        $this->display('add_othors');
    }

    //锁定时间续期
    public function public_lock_renewal() {
        $catid = I('get.catid', 0, 'intval');
        $id = I('get.id', 0, 'intval');
        $userid = AppframeAction::$Cache["uid"];
        $time = time();
        if ($catid && $id && $userid) {
            M("Locking")->where(array("id" => $id, "catid" => $catid, "userid" => $userid))->save(array("locktime" => $time));
        }
    }

}
