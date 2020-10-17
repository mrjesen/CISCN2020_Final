<?php

/**
 * 网站配置信息管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ConfigAction extends AdminbaseAction {

    protected $site_config, $user_config, $Config;

    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->Config = D("Config");
        import('Form');
        if (false == IS_POST) {
            $config = $this->Config->select();
            foreach ($config as $key => $r) {
                $config[$r['varname']] = Input::forShow($r['value']);
            }
            $this->assign('Site', $config);
        }
    }

    //网站基本设置
    public function index() {
        if (IS_POST) {
            $this->dosite();
        } else {
            //首页模板
            $filepath = TEMPLATE_PATH . (empty(AppframeAction::$Cache["Config"]['theme']) ? "Default" : AppframeAction::$Cache["Config"]['theme']) . "/Contents/Index/";
            $indextp = str_replace($filepath, "", glob($filepath . 'index*'));
            $urlrules_detail = F("urlrules_detail");
            $IndexURL = array();
            $TagURL = array();
            foreach ($urlrules_detail as $k => $v) {
                if ($v['module'] == 'tags' && $v['file'] == 'tags') {
                    $TagURL[$v['urlruleid']] = $v['example'];
                }
                if ($v['module'] == 'content' && $v['file'] == 'index') {
                    $IndexURL[$v['ishtml']][$v['urlruleid']] = $v['example'];
                }
            }

            $this->assign("TagURL", $TagURL);
            $this->assign("IndexURL", $IndexURL);
            $this->assign("indextp", $indextp);
            $this->display();
        }
    }

    //邮箱参数
    public function mail() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $this->display();
        }
    }

    //附件参数
    public function attach() {
        if (IS_POST) {
            $this->dosite();
        } else {
            $this->display();
        }
    }

    //高级配置
    public function addition() {
        if (IS_POST) {
            if ($this->Config->addition($_POST)) {
                $this->success("修改成功，即将更新缓存！", U("Admin/Index/public_cache", "type=site"));
            } else {
                $error = $this->Config->getError();
                $this->error($error ? $error : "高级配置更新失败！");
            }
        } else {
            $addition = include SITE_PATH . '/shuipf/Conf/addition.php';
            if (empty($addition) || !is_array($addition)) {
                $addition = array();
            }
            $this->assign("addition", $addition);
            $this->display();
        }
    }

    //扩展配置
    public function extend() {
        if (IS_POST) {
            $action = I('post.action');
            if ($action) {
                //添加扩展项
                if ($action == 'add') {
                    $data = array(
                        'fieldname' => trim(I('post.fieldname')),
                        'type' => trim(I('post.type')),
                        'setting' => I('post.setting'),
                        C("TOKEN_NAME") => I('post.' . C("TOKEN_NAME")),
                    );
                    if ($this->Config->extendAdd($data) !== false) {
                        $this->success('扩展配置项添加成功！', U('Config/extend'));
                        return true;
                    } else {
                        $error = $this->Config->getError();
                        $this->error($error ? $error : '添加失败！');
                    }
                }
            } else {
                //更新扩展项配置
                if ($this->Config->saveExtendConfig($_POST)) {
                    $this->success("更新成功！");
                } else {
                    $error = $this->Config->getError();
                    $this->error($error ? $error : "配置更新失败！");
                }
            }
        } else {
            $action = I('get.action');
            $db = M('ConfigField');
            if ($action) {
                if ($action == 'delete') {
                    $fid = I('get.fid', 0, 'intval');
                    if ($this->Config->extendDel($fid)) {
                        $this->success("扩展配置项删除成功！");
                        return true;
                    } else {
                        $error = $this->Config->getError();
                        $this->error($error ? $error : "扩展配置项删除失败！");
                    }
                }
            }
            $extendList = $db->order(array('fid' => 'DESC'))->select();
            $this->assign('extendList', $extendList);
            $this->display();
        }
    }

    //更新配置
    protected function dosite() {
        if ($this->Config->saveConfig($_POST)) {
            $this->success("更新成功！");
        } else {
            $error = $this->Config->getError();
            $this->error($error ? $error : "配置更新失败！");
        }
    }

}
