<?php

/**
 * 后台Action
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//定义是后台
define('IN_ADMIN', true);

class AdminbaseAction extends AppframeAction {

    protected function _initialize() {
        parent::_initialize();
        $rbac_status = array(
            "status" => false,
            "url" => "",
            "error" => "",
        );
        tag("appframe_rbac_init", $rbac_status);
        if (false == $rbac_status['status']) {
            if ($rbac_status['url']) {
                $this->assign("jumpUrl", $rbac_status['url']);
            }
            $this->error($rbac_status['error']);
        } elseif (!isset($rbac_status['status'])) {
            $this->error("您没有操作此项的权限！");
        }
        //初始化当前登录用户信息
        $this->initAdminUser();
        $this->initMenu();
    }

    /**
     *  初始化当前登录用户信息
     * @return Boolean 没有登陆返回false，有登陆信息返回User Info
     */
    final protected function initAdminUser() {
        //当然登陆用户ID
        $usDb = service("PassportAdmin")->isLogged();
        if ($usDb == false) {
            return false;
        }

        self::$Cache['uid'] = (int) $usDb['userid'];
        self::$Cache['username'] = $usDb['username'];
        $this->assign("uid", self::$Cache['uid']);
        $this->assign("username", self::$Cache['username']);
        $User = $usDb;

        self::$Cache['User'] = $User;
        $this->assign("User", self::$Cache['User']);
        unset($usDb);
        return $User;
    }

    /**
     * 消息提示
     * @param type $message
     * @param type $jumpUrl
     * @param type $ajax 
     */
    public function success($message, $jumpUrl = '', $ajax = false) {
        parent::success($message, $jumpUrl, $ajax);
        $text = "应用：" . GROUP_NAME . ",模块：" . MODULE_NAME . ",方法：" . ACTION_NAME . "<br>提示语：" . $message;
        $this->addLogs($text);
    }

    /**
     * 模板显示
     * @param type $templateFile 指定要调用的模板文件
     * @param type $charset 输出编码
     * @param type $contentType 输出类型
     * @param string $content 输出内容
     * 此方法作用在于实现后台模板直接存放在各自项目目录下。例如Admin项目的后台模板，直接存放在Admin/Tpl/目录下
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '') {
        parent::display($templateFile, $charset, $contentType, $content);
    }

    /**
     * 初始化后台菜单
     */
    private function initMenu() {
        $Menu = F("Menu");
        if (!$Menu) {
            D("Menu")->menu_cache();
        }
    }

    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     *  弃用
     */
    protected function listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

    /**
     * 分页输出
     * @param type $total 信息总数
     * @param type $size 每页数量
     * @param type $number 当前分页号（页码）
     * @param type $config 配置，会覆盖默认设置
     * @return type
     */
    protected function page($total, $size = 0, $number = 0, $config = array()) {
        $Page = page($total, $size, $number, $config);
        $Page->SetPager('Admin', '<span class="all">共有{recordcount}条信息</span>{first}{prev}{liststart}{list}{listend}{next}{last}');
        return $Page;
    }

    /**
     * 获取菜单导航
     * @param type $app
     * @param type $model
     * @param type $action
     */
    public static function getMenu() {

        $menuid = (int) $_GET['menuid'];
        $menuid = $menuid ? $menuid : cookie("menuid", "", array("prefix" => ""));
        //cookie("menuid",$menuid);

        $db = D("Menu");
        $info = $db->cache(true, 60)->where(array("id" => $menuid))->getField("id,action,app,model,parentid,data,type,name");
        $find = $db->cache(true, 60)->where(array("parentid" => $menuid, "status" => 1))->getField("id,action,app,model,parentid,data,type,name");

        if ($find) {
            array_unshift($find, $info[$menuid]);
        } else {
            $find = $info;
        }
        foreach ($find as $k => $v) {
            $find[$k]['data'] = "menuid=$menuid&" . $find[$k]['data'];
        }

        return $find;
    }

    /**
     * 当前位置
     * @param $id 菜单id
     */
    final public static function current_pos($id) {
        $menudb = M("Menu");
        $r = $menudb->where(array('id' => $id))->find();
        $str = '';
        if ($r['parentid']) {
            $str = self::current_pos($r['parentid']);
        }
        return $str . $r['name'] . ' > ';
    }

}
