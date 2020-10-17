<?php

/**
 * 各插件后台管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class EmptyAction extends Action {

    //方法
    public $act = 'index';
    //插件标识
    public $addonName = NULL;
    //插件路径
    protected $addonPath = NULL;

    protected function _initialize() {
        $this->act = ACTION_NAME;
        define('ADDON_ACT', $this->act);
        $this->addonName = MODULE_NAME;
        $this->addonPath = D('Addons')->getAddonsPath() . $this->addonName . '/';
    }

    //魔术方法
    public function __call($method, $args) {
        $isAdmin = I('get.isadmin');
        if ($isAdmin) {
            define('ADDON_MODULE_NAME', 'Admin');
            $this->admin();
        } else {
            define('ADDON_MODULE_NAME', 'Index');
            $this->index();
        }
    }

    //插件前台
    private function index() {
        import('Util.AddonsAction', BASE_LIB_PATH);
        //导入对应插件
        require_cache($this->addonPath . "Action/IndexAction.class.php");
        $indexAction = new IndexAction();
        $action = $this->act;
        $indexAction->$action();
    }

    //插件后台
    private function admin() {
        import('Util.AdminaddonbaseAction', BASE_LIB_PATH);
        //导入对应插件
        require_cache($this->addonPath . "Action/AdminAction.class.php");
        $adminAction = new AdminAction();
        $action = $this->act;
        $adminAction->$action();
    }

}
