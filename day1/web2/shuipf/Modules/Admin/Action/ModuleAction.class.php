<?php

/**
 * 模块管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ModuleAction extends AdminbaseAction {

    //模块所处目录路径
    protected $appPath = NULL;
    //模块模型
    protected $module = NULL;

    protected function _initialize() {
        parent::_initialize();
        $this->appPath = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR;
        $this->module = D('Module');
    }

    //模块管理首页
    public function index() {
        //取得模块目录名称
        $dirs = glob($this->appPath . '*');
        foreach ($dirs as $path) {
            if (is_dir($path)) {
                $path = basename($path);
                $dirs_arr[] = $path;
            }
        }
        //取得已安装模块列表
        $modulesdata = $this->module->select();
        foreach ($modulesdata as $v) {
            $modules[$v['module']] = $v;
            //检查是否系统模块，如果是，直接不显示
            if ($v['iscore']) {
                $key = array_keys($dirs_arr, $v['module']);
                unset($dirs_arr[$key[0]]);
            }
        }
        //数量
        $count = count($dirs_arr);
        //把一个数组分割为新的数组块
        $dirs_arr = array_chunk($dirs_arr, 10, true);
        //当前分页
        $page = max(I('get.' . C('VAR_PAGE'), 0, 'intval'), 1);
        //根据分页取到对应的模块列表数据
        $directory = $dirs_arr[intval($page - 1)];
        //读取配置
        $moduleList = array();
        foreach ($directory as $module) {
            $Config = array(
                //模块目录
                'module' => $module,
                //模块名称
                'modulename' => $module,
                //模块简介
                'introduce' => '',
                //模块作者
                'author' => '',
                //作者地址
                'authorsite' => '',
                //作者邮箱
                'authoremail' => '',
                //版本号，请不要带除数字外的其他字符
                'version' => '',
                //适配最低ShuipFCMS版本，
                'adaptation' => '',
            );
            if (file_exists($this->appPath . $module . '/Install/Config.inc.php') || file_exists($this->appPath . $module . '/Config.inc.php')) {
                if(file_exists($this->appPath . $module . '/Config.inc.php')){
                    $moduleConfig = include $this->appPath . $module . '/Config.inc.php';
                }else{
                    $moduleConfig = include $this->appPath . $module . '/Install/Config.inc.php';
                }
                if (is_array($moduleConfig)) {
                    $Config = $moduleConfig;
                    $Config['status'] = 1;
                } else {
                    $Config['status'] = 2;
                    //兼容处理
                    $Config['modulename'] = $modulename;
                    $Config['introduce'] = $introduce;
                    $Config['author'] = $author;
                    $Config['authorsite'] = $authorsite;
                    $Config['authoremail'] = $authoremail;
                    $Config['version'] = $version;
                }
            } else if (isset($modules[$module])) {
                $Config['status'] = 3;
                $Config['modulename'] = $modules[$module]['name'];
                $Config['introduce'] = $modules[$module]['description'];
                $Config['version'] = $modules[$module]['version'];
            } else {
                $Config['status'] = 4;
            }
            //如果有安装，显示安装时间
            if (isset($modules[$module])) {
                $Config['installdate'] = $modules[$module]['installdate'];
                $Config['disabled'] = $modules[$module]['disabled'];
            }
            $moduleList[$module] = $Config;
        }

        //进行分页
        $Page = $this->page($count, 10);

        $this->assign("Page", $Page->show("Admin"));
        $this->assign("data", $moduleList);
        $this->assign("modules", $modules);
        $this->display();
    }

    //模块安装 
    public function install() {
        if (IS_POST) {
            $post = I('post.');
            $module = $post['module'];
            if (empty($module)) {
                $this->error('请选择需要安装的模块！');
            }
            if ($this->module->install($module)) {
                $this->success('模块安装成功！', U('Admin/Module/index'));
            } else {
                $error = $this->module->getError();
                $this->error($error ? $error : '模块安装失败！');
            }
        } else {
            $module = I('get.module', '', 'trim,ucwords');
            if (empty($module)) {
                $this->error('请选择需要安装的模块！');
            }
            //检查是否已经安装过
            if ($this->module->isInstall($module)) {
                $this->error('该模块已经安装过了，无需重复执行安装！');
            }
            $config = $this->module->getModuleInstallConfig($module);
            //版本检查
            if ($config['adaptation']) {
                $version = version_compare(SHUIPF_VERSION, $config['adaptation'], '>=');
                $this->assign('version', $version);
            }
            $this->assign('config', $config);
            $this->display();
        }
    }

    //模块卸载 
    public function uninstall() {
        $module = I('get.module', '', 'trim,ucwords');
        if (empty($module)) {
            $this->error('请选择需要安装的模块！');
        }
        if ($this->module->uninstall($module)) {
            $this->success("模块卸载成功，请及时更新缓存！", U("Module/index"));
        } else {
            $this->error("模块卸载失败！", U("Module/index"));
        }
    }

    //模块状态转换
    public function disabled() {
        $module = I('get.module', '', 'trim,ucwords');
        if (empty($module)) {
            $this->error('请选择模块！');
        }
        if ($this->module->disabled($module)) {
            $this->success("状态转换成功，请及时更新缓存！", U("Module/index"));
        } else {
            $this->error("状态转换成功失败！", U("Module/index"));
        }
    }

}