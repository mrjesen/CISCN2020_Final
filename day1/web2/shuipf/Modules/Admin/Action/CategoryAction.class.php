<?php

/**
 * 栏目管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CategoryAction extends AdminbaseAction {

    //模板文件夹
    private $filepath;
    //频道模板路径
    protected $tp_category;
    //列表页模板路径
    protected $tp_list;
    //内容页模板路径
    protected $tp_show;
    //单页模板路径
    protected $tp_page;
    //评论模板路径
    protected $tp_comment;

    //初始化
    protected function _initialize() {
        parent::_initialize();
        //取得当前内容模型模板存放目录
        $this->filepath = TEMPLATE_PATH . (empty(AppframeAction::$Cache["Config"]['theme']) ? "Default" : AppframeAction::$Cache["Config"]['theme']) . DIRECTORY_SEPARATOR . "Contents" . DIRECTORY_SEPARATOR; //
        //取得栏目频道模板列表
        $this->tp_category = str_replace($this->filepath . "Category" . DIRECTORY_SEPARATOR, "", glob($this->filepath . "Category" . DIRECTORY_SEPARATOR . 'category*'));
        //取得栏目列表模板列表
        $this->tp_list = str_replace($this->filepath . "List" . DIRECTORY_SEPARATOR, "", glob($this->filepath . "List" . DIRECTORY_SEPARATOR . 'list*'));
        //取得内容页模板列表
        $this->tp_show = str_replace($this->filepath . "Show" . DIRECTORY_SEPARATOR, "", glob($this->filepath . "Show" . DIRECTORY_SEPARATOR . 'show*'));
        //取得单页模板
        $this->tp_page = str_replace($this->filepath . "Page" . DIRECTORY_SEPARATOR, "", glob($this->filepath . "Page" . DIRECTORY_SEPARATOR . 'page*'));
        //取得评论模板列表
        $this->tp_comment = str_replace($this->filepath . "Comment" . DIRECTORY_SEPARATOR, "", glob($this->filepath . "Comment" . DIRECTORY_SEPARATOR . 'comment*'));
        import('Url');
        $this->Url = get_instance_of('Url');
        load("@.iconvfunc");
    }

    //显示栏目列表 
    public function index() {
        import('Tree');
        $tree = new Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $models = F("ModelType_0");
        $categorys = array();
        //栏目数据，可以设置为缓存的方式
        $result = F('Category');
        $siteurl = parse_url(CONFIG_SITEURL);
        $types = array(0 => '内部栏目', 1 => '<font color="blue">单网页</font>', 2 => '<font color="red">外部链接</font>');
        if (!empty($result)) {
            foreach ($result as $r) {
                $r = getCategory($r['catid']);
                $r['modelname'] = $models[$r['modelid']]['name'];
                $r['str_manage'] = '';
                if ($r['child']) {
                    $r['yesadd'] = '';
                } else {
                    $r['yesadd'] = 'blue';
                }
                if ($r['type'] != 2) {
                    if ($r['child']) {
                        if ($r['type'] == 1) {
                            $r['str_manage'] .= '<a href="' . U("Category/singlepage", array("parentid" => $r['catid'])) . '">添加子栏目</a> | ';
                        } else {
                            $r['str_manage'] .= '<a href="' . U("Category/add", array("parentid" => $r['catid'])) . '">添加子栏目</a> | ';
                        }
                    }
                }
                $r['str_manage'] .= '<a href="' . U("Category/edit", array("catid" => $r['catid'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("Category/delete", array("catid" => $r['catid'])) . '">删除</a>';
                //终极栏目转换
                if (in_array($r['type'], array(0, 1))) {
                    $r['str_manage'] .= ' | <a href="' . U("Category/categoryshux", array("catid" => $r['catid'])) . '">终极属性转换</a> ';
                }
                $r['typename'] = $types[$r['type']];
                $r['display_icon'] = $r['ismenu'] ? '' : ' <img src ="' . AppframeAction::$Cache['Config']['siteurl'] . 'statics/images/icon/gear_disable.png" title="不在导航显示">';

                $r['help'] = '';
                $setting = unserialize($r['setting']);
                if ($r['url']) {
                    $parse_url = parse_url($r['url']);
                    if ($parse_url['host'] != $siteurl['host'] && strpos($r['url'], '/index.php?') === false) {
                        $catdir = $r['catdir'];
                        //如果生成静态，将设置一个指定的静态目录
                        $catdir = '/' . $r['parentdir'] . $catdir;
                        if ($setting['ishtml'] && strpos($r['url'], '?') === false) {
                            $r['help'] = '<img src="' . AppframeAction::$Cache['Config']['siteurl'] . 'statics/images/icon/help.png" title="将域名：' . $r['url'] . '&#10;绑定到目录&#10;' . $catdir . '/">';
                        }
                    }
                    $r['url'] = "<a href='" . $r['url'] . "' target='_blank'>访问</a>";
                } else {
                    $r['url'] = "<a href='" . U("Admin/Category/public_cache") . "'><font color='red'>更新缓存</font></a>";
                }
                $categorys[$r['catid']] = $r;
            }
        }
        $str = "<tr>
	<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
	<td align='center'><font color='\$yesadd'>\$id</font></td>
	<td >\$spacer\$catname\$display_icon</td>
	<td  align='center'>\$typename</td>
	<td>\$modelname</td>
	<td align='center'>\$url</td>
	<td align='center'>\$help</td>
	<td align='center' >\$str_manage</td>
	</tr>";
        $tree->init($categorys);
        $categorydata = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorydata);
        $this->display();
    }

    //添加栏目 
    public function add() {
        if (IS_POST) {
            $Category = D("Category");
            //批量添加
            $isbatch = I('post.isbatch', 0, 'intval');
            if ($isbatch) {
                $post = $_POST;
                unset($post['isbatch'], $post['info']['catname'], $post['info']['catdir']);
                //需要批量添加的栏目
                $batch_add = explode("\n", $_POST['batch_add']);
                if (empty($batch_add) || empty($_POST['batch_add'])) {
                    $this->error('请填写需要添加的栏目！');
                }
                //关闭表单令牌验证
                C('TOKEN_ON', false);
                foreach ($batch_add as $rs) {
                    $cat = explode('|', $rs, 2);
                    if ($cat[0] && $cat[1]) {
                        $post['info']['catname'] = $cat[0];
                        $post['info']['catdir'] = $cat[1];
                        $catid = $Category->addCategory($post);
                        if ($catid) {
                            //更新角色栏目权限
                            $this->update_priv($catid, $_POST['priv_roleid']);
                            if (isModuleInstall('Member')) {
                                //更新会员组权限
                                $this->update_priv($catid, $_POST['priv_groupid'], 0);
                            }
                        }
                    }
                }
                //更新缓存
                $this->cache();
                $this->success("添加成功！", U("Category/index"));
            } else {
                $catid = $Category->addCategory($_POST);
                if ($catid) {
                    //更新角色栏目权限
                    $this->update_priv($catid, $_POST['priv_roleid']);
                    if (isModuleInstall('Member')) {
                        //更新会员组权限
                        $this->update_priv($catid, $_POST['priv_groupid'], 0);
                    }
                    //更新缓存
                    $this->cache();
                    $this->success("添加成功！", U("Category/index"));
                } else {
                    $error = $Category->getError();
                    $this->error($error ? $error : '栏目添加失败！');
                }
            }
        } else {
            $parentid = I('get.parentid', 0, 'intval');
            if (!empty($parentid)) {
                $Category = F("Category");
                $Ca = $Category[$parentid];
                if (empty($Ca)) {
                    $this->error("父栏目不存在！");
                }
                if ($Ca['child'] == '0') {
                    $this->error("终极栏目不能添加子栏目！");
                }
            }

            //输出可用模型
            $modelsdata = F("Model");
            $models = array();
            foreach ($modelsdata as $v) {
                if ($v['disabled'] == 0 && $v['type'] == 0) {
                    $models[] = $v;
                }
            }
            //栏目列表 可以用缓存的方式
            $array = F("Category");
            foreach ($array as $k => $v) {
                if ($v['child'] == '0') {
                    $array[$k]['disabled'] = "disabled";
                } else {
                    $array[$k]['disabled'] = "";
                }
            }

            import('Tree');
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            $tree->init($array);
            $categorydata = $tree->get_tree(0, $str, $parentid);
            $this->assign("tp_category", $this->tp_category);
            $this->assign("tp_list", $this->tp_list);
            $this->assign("tp_show", $this->tp_show);
            $this->assign("tp_comment", $this->tp_comment);
            $this->assign("tp_page", $this->tp_page);
            $this->assign("category", $categorydata);
            $this->assign("models", $models);
            $this->assign('parentid_modelid', $Ca['modelid']);

            $type = I('get.type', 0, 'intval');
            import('Form');
            $this->assign("category_php_ruleid", Form::urlrule('content', 'category', 0, "", 'name="category_php_ruleid"'));
            $this->assign("category_html_ruleid", Form::urlrule('content', 'category', 1, "", 'name="category_html_ruleid"'));
            $this->assign("show_php_ruleid", Form::urlrule('content', 'show', 0, "", 'name="show_php_ruleid"'));
            $this->assign("show_html_ruleid", Form::urlrule('content', 'show', 1, "", 'name="show_html_ruleid"'));
            if (isModuleInstall('Member')) {
                //会员组
                $this->assign("Member_group", F("Member_group"));
            }
            //角色组
            $this->assign("Role_group", M("Role")->order(array("id" => "ASC"))->select());
            $this->display();
        }
    }

    //添加外部链接栏目 
    public function wadd() {
        $this->add();
    }

    //添加单页
    public function singlepage() {
        $this->add();
    }

    //编辑栏目 
    public function edit() {
        if (IS_POST) {
            $catid = I("post.catid", "", "intval");
            if (empty($catid)) {
                $this->error('请选择需要修改的栏目！');
            }
            $Category = D("Category");
            $status = $Category->editCategory($_POST);
            if ($status) {
                //应用权限设置到子栏目
                if ($_POST['priv_child']) {
                    //子栏目
                    $arrchildid = $Category->where(array('catid' => $catid))->getField('arrchildid');
                    $arrchildid_arr = explode(',', $arrchildid);
                    foreach ($arrchildid_arr as $arr_v) {
                        $this->update_priv($arr_v, $_POST['priv_roleid']);
                        $this->update_priv($arr_v, $_POST['priv_groupid'], 0);
                    }
                } else {
                    //更新角色栏目权限
                    $this->update_priv($catid, $_POST['priv_roleid']);
                    if (isModuleInstall('Member')) {
                        //更新会员组权限
                        $this->update_priv($catid, $_POST['priv_groupid'], 0);
                    }
                }
                //更新缓存
                $this->cache();
                $this->success("更新成功！", U("Category/index"));
            } else {
                $error = $Category->getError();
                $this->error($error ? $error : '栏目修改失败！');
            }
        } else {
            $catid = I('get.catid', 0, 'intval');
            import('Tree');
            $tree = new Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $str = "<option value='\$catid' \$selected \$disabled>\$spacer \$catname</option>";
            $array = F("Category");
            foreach ($array as $k => $v) {
                if ($v['child'] == "0") {
                    $array[$k]['disabled'] = "disabled";
                } else {
                    $array[$k]['disabled'] = "";
                }
            }
            $data = getCategory($catid);
            $setting = $data['setting'];
            //输出可用模型
            $modelsdata = F("Model");
            $models = array();
            foreach ($modelsdata as $v) {
                if ($v['disabled'] == 0 && $v['type'] == 0) {
                    $models[] = $v;
                }
            }
            $tree->init($array);
            $categorydata = $tree->get_tree(0, $str, $data['parentid']);

            import('Form');
            $this->assign("category_php_ruleid", Form::urlrule('content', 'category', 0, $setting['category_ruleid'], 'name="category_php_ruleid"'));
            $this->assign("category_html_ruleid", Form::urlrule('content', 'category', 1, $setting['category_ruleid'], 'name="category_html_ruleid"'));

            $this->assign("show_php_ruleid", Form::urlrule('content', 'show', 0, $setting['show_ruleid'], 'name="show_php_ruleid"'));
            $this->assign("show_html_ruleid", Form::urlrule('content', 'show', 1, $setting['show_ruleid'], 'name="show_html_ruleid"'));

            $this->assign("tp_category", $this->tp_category);
            $this->assign("tp_list", $this->tp_list);
            $this->assign("tp_show", $this->tp_show);
            $this->assign("tp_comment", $this->tp_comment);
            $this->assign("tp_page", $this->tp_page);
            $this->assign("category", $categorydata);
            $this->assign("models", $models);
            $this->assign("data", $data);
            $this->assign("setting", $setting);
            if (isModuleInstall('Member')) {
                //会员组
                $this->assign("Member_group", F("Member_group"));
            }
            //栏目扩展字段
            $this->assign('extendList', D("Category")->getExtendField($catid));
            //角色组
            $this->assign("Role_group", M("Role")->order(array("id" => "ASC"))->select());
            $this->assign("big_menu", array(U("Category/index"), "栏目管理"));
            //权限数据
            $this->assign("privs", M("CategoryPriv")->where(array('catid' => $catid))->select());
            if ($data['type'] == 1) {//单页栏目
                $this->display("singlepage_edit");
            } else if ($data['type'] == 2) {//外部栏目
                $this->display("wedit");
            } else {
                $this->display();
            }
        }
    }

    //删除栏目 
    public function delete() {
        $catid = I("get.catid", "", "intval");
        if (!$catid) {
            $this->error("请指定需要删除的栏目！");
        }
        if (false == D("Category")->deleteCatid($catid)) {
            $this->error("栏目删除失败，错误原因可能是栏目下存在信息，无法删除！");
        }
        $this->success("栏目删除成功！", U("Category/public_cache"));
    }

    //更新栏目缓存并修复
    public function public_cache() {
        $db = D("Category");
        //当前
        $number = I('get.number', 1, 'intval');
        //每次处理多少栏目
        $handlesum = 100;
        //计算栏目总数
        $count = I('get.count', $db->count(), 'intval');
        //需要处理几次
        $handlecount = ceil($count / $handlesum);
        if ($number > $handlecount) {
            $this->cache();
            $this->success("缓存更新成功！", U("Category/index"));
            return true;
        }
        $page = $this->page($count, $handlesum, $number);
        //取出需要处理的栏目数据
        $data = $db->order(array('catid' => 'ASC'))->limit($page->firstRow . ',' . $page->listRows)->select();
        if (empty($data)) {
            $this->cache();
            $this->success("缓存更新成功！", U("Category/index"));
            return true;
        }
        $categorys = array();
        foreach ($data as $v) {
            $categorys[$v['catid']] = $v;
        }
        $this->repair($categorys);
        $this->assign("waitSecond", 200);
        //跳转到下一轮
        $this->success("栏目总数:<font color=\"#FF0000\">{$count}</font>,每次处理:<font color=\"#FF0000\">{$handlesum}</font>,进度:<font color=\"#FF0000\">{$number}/{$handlecount}</font>,栏目缓存更新中...", U('public_cache', array('count' => $count, 'number' => $number + 1)));
    }

    /**
     * 更新F('Category')栏目缓存
     */
    protected function cache() {
        D("Category")->category_cache();
    }

    /**
     * 修复栏目数据
     * @param type $categorys 需要修复的栏目数组
     * @return boolean
     */
    protected function repair($categorys) {
        if (is_array($categorys)) {
            foreach ($categorys as $catid => $cat) {
                //外部栏目无需修复
                if ($cat['type'] == 2) {
                    continue;
                }
                //获取父栏目ID列表
                $arrparentid = D("Category")->get_arrparentid($catid);
                //栏目配置信息反序列化
                $setting = unserialize($cat['setting']);
                //获取子栏目ID列表
                $arrchildid = D("Category")->get_arrchildid($catid);
                //检查所有父id 子栏目id 等相关数据是否正确，不正确更新
                if ($categorys[$catid]['arrparentid'] != $arrparentid || $categorys[$catid]['arrchildid'] != $arrchildid) {
                    D("Category")->where(array('catid' => $catid))->save(array('arrparentid' => $arrparentid, 'arrchildid' => $arrchildid));
                }
                //获取父栏目路径
                $parentdir = $this->get_parentdir($catid);
                //获取栏目名称
                $catname = iconv('utf-8', 'gbk', $cat['catname']);
                //返回拼音
                $letters = gbk_to_pinyin($catname);
                $letter = strtolower(implode('', $letters));
                //取得栏目相关地址和分页规则
                $category_url = $this->Url->category_url($catid);
                if (false == $category_url) {
                    return false;
                }
                $url = $category_url['url'];
                //更新数据
                $save = array();
                //更新URL
                if ($cat['url'] != $url) {
                    $save['url'] = $url;
                }
                if ($categorys[$catid]['parentdir'] != $parentdir || $categorys[$catid]['letter'] != $letter) {
                    $save['parentdir'] = $parentdir;
                    $save['letter'] = $letter;
                }
                if (count($save) > 0) {
                    D("Category")->where(array('catid' => $catid))->save($save);
                }
            }
        }
        return true;
    }

    /**
     * 获取父栏目路径
     * @param  $catid
     */
    private function get_parentdir($catid) {
        return $this->Url->get_categorydir($catid);
    }

    /**
     * 检查目录是否存在 
     * @param type $return_method 显示方式，1 ajax返回
     * @param type $catdir 栏目目录
     * @param type $catid 栏目id
     * @return boolean
     */
    public function public_check_catdir($return_method = 1, $catdir = '', $catid = 0) {
        $catid = $catid ? $catid : I('get.catid', 0, 'intval');
        //需要添加的目录
        $catdir = $catdir ? $catdir : I('get.catdir');
        //父ID
        $parentid = I('get.parentid', 0, 'intval');
        //旧目录
        $old_catdir = I('get.old_catdir');
        $status = D("Category")->checkCatdir($catdir, $catid, $parentid, $old_catdir);
        if ($status == false) {
            //当有信息且时表示目录存在
            if ($return_method) {
                $this->ajaxReturn("", "目录存在！", false);
            } else {
                return false;
            }
        } else {
            if ($return_method) {
                $this->ajaxReturn("", "目录不存在！", true);
            } else {
                return true;
            }
        }
    }

    //栏目属性转换  child 字段的转换
    public function categoryshux() {
        $catid = I('get.catid', 0, 'intval');
        $r = M("Category")->where(array("catid" => $catid))->find();
        if ($r) {
            //栏目类型非0，不允许使用属性转换
            if (!in_array($r['type'], array(0, 1))) {
                $this->error("该栏目类型不允许进行属性转换！", U('Category/index'));
            }
            $count = M("Category")->where(array("parentid" => $catid))->count();
            if ($count > 0) {
                $this->error("该栏目下已经存在栏目，无法转换！");
            } else {
                $Model = F("ModelType_0");
                $Category = F("Category");
                $count = M(ucwords($Model[$Category[$catid]['modelid']]['tablename']))->where(array("catid" => $catid))->count();
                if ($count > 0) {
                    $this->error("该栏目下已经存在数据，无法转换！");
                }
                $child = $r['child'] ? 0 : 1;
                $status = D("Category")->where(array("catid" => $catid))->save(array("child" => $child));
                if ($status !== false) {
                    $this->repair();
                    $this->cache();
                    $this->success("栏目属性转换成功！");
                } else {
                    $this->error("栏目属性转换失败！");
                }
            }
        } else {
            $this->error("栏目不存在！");
        }
    }

    //栏目排序 
    public function listorder() {
        if (IS_POST) {
            $Category = M("Category");
            foreach ($_POST['listorders'] as $id => $listorder) {
                $Category->where(array('catid' => $id))->save(array('listorder' => $listorder));
            }
            $this->cache();
            $this->success("排序更新成功！");
        } else {
            $this->error("信息提交有误！");
        }
    }

    /**
     * 更新权限
     * @param  $catid 栏目ID
     * @param  $priv_datas
     * @param  $is_admin 1为管理员
     */
    private function update_priv($catid, $priv_datas, $is_admin = 1) {
        D("Category_priv")->update_priv($catid, $priv_datas, $is_admin);
    }

    /**
     * 检查栏目权限
     * @param $privs 权限数据
     * @param $action 动作
     * @param $roleid 角色
     * @param $is_admin 是否为管理组
     */
    public static function check_category_priv($privs, $action, $roleid, $is_admin = 1) {
        return D("Category_priv")->check_category_priv($privs, $action, $roleid, $is_admin);
    }

}
