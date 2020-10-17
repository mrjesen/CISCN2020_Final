<?php

/**
 * 模块管理模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ModuleModel extends CommonModel {

    //模块所处目录路径
    protected $appPath = NULL;
    //模板路径
    protected $templatePath;
    //静态资源
    protected $extresPath;
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('module', 'require', '模块目录名称不能为空！', 1, 'regex', 3),
        //array('module', '', '该模块已经安装过！', 0, 'unique', 1),
        array('name', 'require', '模块名称不能为空！', 1, 'regex', 3),
        array('version', 'require', '模块版本号不能为空！', 1, 'regex', 3),
    );
    //自动完成
    protected $_auto = array(
        array('iscore', 0),
        array('disabled', 1),
        array('installdate', 'date', 1, 'function', 'Y-m-d'),
        array('updatedate', 'date', 1, 'function', 'Y-m-d'),
    );

    protected function _initialize() {
        parent::_initialize();
        $this->appPath = APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR;
        //模板安装目录，模板安装目录强制在Default是出于，如果用户安装了模块后，又切换了主题，会造成找不到模板报错，只好强制安装在Default主题下！---水平凡
        $this->templatePath = TEMPLATE_PATH . "Default" . DIRECTORY_SEPARATOR;
        $this->extresPath = SITE_PATH . '/statics/extres/';
    }

    /**
     * 安装模块
     * @param type $module 模块名称
     * @return boolean
     */
    public function install($module) {
        if (empty($module)) {
            $this->error = '请选择需要安装的模块！';
            return false;
        }
        //设置脚本最大执行时间
        set_time_limit(0);
        //模板目录权限检测
        if ($this->chechmod($this->templatePath) == false) {
            $this->error = '目录 ' . $this->templatePath . ' 没有可写权限！';
            return false;
        }
        //静态资源目录权限检测
        if (!file_exists($this->extresPath)) {
            //创建目录
            if (mkdir($this->extresPath, 0777, true) == false) {
                $this->error = '目录 ' . $this->extresPath . ' 创建失败，请检查是否有可写权限！';
                return false;
            }
        }
        //权限检测
        if ($this->chechmod($this->extresPath) == false) {
            $this->error = '目录 ' . $this->extresPath . ' 没有可写权限！';
            return false;
        }
        define("INSTALL", true);
        //添加一个菜单到后台“模块->模块列表”ID=74
        define("MENUID", 74);
        //加载配置
        $config = $this->getModuleInstallConfig($module);
        if (empty($config)) {
            $this->error = '获取模块安装配置出错！';
            return false;
        }
        //版本检查
        if ($config['adaptation']) {
            if (version_compare(SHUIPF_VERSION, $config['adaptation'], '>=') == false) {
                $this->error = '该模块要求系统最低版本为：' . $config['adaptation'] . '！';
                return false;
            }
        }
        //静态资源文件
        if (file_exists($this->appPath . $module . "/Install/Extres/")) {
            //创建目录
            if (mkdir($this->extresPath . strtolower($config['module']) . '/', 0777, true) == false) {
                $this->error = '目录 ' . $this->extresPath . strtolower($config['module']) . '/' . ' 创建失败，如果该目录已存在请删除！';
                return false;
            }
        }
        //检查模块是否已经安装
        if ($this->isInstall($module)) {
            $this->error = '该模块已经安装过，无需进行重复安装！';
            return false;
        }
        //组合数据
        $data = array(
            "module" => $config['module'],
            "name" => $config['modulename'],
            "version" => $config['version'],
            "description" => $config['introduce'],
        );
        //验证数据
        $data = $this->token(false)->create($data, 1);
        if ($data) {
            //添加记录 
            if (false !== $this->add($data)) {
                import("Dir");
                $Dir = new Dir();
                //是否有php安装脚本
                $isPhpScript = false;
                //判断是否有自己的安装脚本
                if (file_exists($this->appPath . $module . '/Install/Install.class.php')) {
                    require_cache($this->appPath . $module . '/Install/Install.class.php');
                    //检查是否存在
                    if (class_exists('Install')) {
                        $isPhpScript = true;
                        $installObj = new Install();
                        //检查安装方法是否存在
                        if (method_exists($installObj, 'run')) {
                            //执行安装
                            if (false == $installObj->run()) {
                                //删除安装状态
                                $this->where(array('module' => $config['module']))->delete();
                                //获取错误
                                if (method_exists($installObj, 'getError')) {
                                    $this->error = $installObj->getError();
                                } else {
                                    $this->error = '模块安装失败！';
                                }
                                return false;
                            }
                        }
                    }
                }
                //判断是否有数据库安装脚本
                if (file_exists($this->appPath . $module . '/Install/' . $module . '.sql')) {
                    //读取
                    $sql = file_get_contents($this->appPath . $module . '/Install/' . $module . '.sql');
                    $sql = $this->sqlSplit($sql, C("DB_PREFIX"));
                    if (!empty($sql) && is_array($sql)) {
                        foreach ($sql as $sql_split) {
                            $this->execute($sql_split);
                        }
                    }
                }
                //判断是否有菜单安装项
                if (file_exists($this->appPath . $module . '/Install/Extention.inc.php')) {
                    try {
                        include $this->appPath . $module . '/Install/Extention.inc.php';
                    } catch (Exception $exc) {
                        $this->where(array('module' => $data['module']))->delete();
                        throw_exception("安装模块 {$data['name']} 出现错误！");
                    }
                }
                //前台模板
                if (file_exists($this->appPath . $module . "/Install/Template/")) {
                    //拷贝模板到前台模板目录中去
                    $Dir->copyDir($this->appPath . $module . "/Install/Template/", $this->templatePath);
                }
                //静态资源文件
                if (file_exists($this->appPath . $module . "/Install/Extres/")) {
                    //拷贝模板到前台模板目录中去
                    $Dir->copyDir($this->appPath . $module . "/Install/Extres/", $this->extresPath . strtolower($config['module']) . '/');
                }
                //安装行为
                if (!empty($config['tags'])) {
                    D('Behavior')->moduleBehaviorInstallation($config['module'], $config['tags']);
                }
                //安装结束，最后调用安装脚本完成
                if ($isPhpScript) {
                    //检查安装方法是否存在
                    if (method_exists($installObj, 'end')) {
                        if (!$installObj->end()) {
                            $this->error = '安装失败！';
                            if (method_exists($installObj, 'getError')) {
                                $this->error = $installObj->getError();
                            }
                            return false;
                        }
                    }
                }
                return true;
            } else {
                $this->error = '安装失败！';
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 执行模块升级脚本
     * @param type $module 模块名称
     * @return boolean
     */
    public function upgrade($module) {
        //检查模块是否安装
        if ($this->isInstall($module) == false) {
            $this->error = '模块没进行安装，无法进行模块升级！';
            return -10022;
        }
        //取得模块信息
        $info = $this->where(array('module' => $module))->find();
        if (empty($info)) {
            $this->error = '获取模块信息错误！';
            return -10023;
        }
        //模块路径
        $base = $this->appPath . $module . '/';
        //SQL脚本文件
        $exec = $base . 'Upgrade/upgrade.sql';
        //phpScript
        $phpScript = $base . 'Upgrade/Upgrade.class.php';
        //判断是否有数据库升级脚本
        if (file_exists($exec)) {
            //获取全部参数
            preg_match_all("/#\[version=(.*?)\](.+?)#\[\/version\]/ism", file_get_contents($exec), $match);
            //遍历
            foreach ($match[1] as $index => $version) {
                //比较模块版本，仅处理小于或等于当前版本
                if ($version && version_compare($version, $info['version'], '>=')) {
                    //记录最后一个更新的版本号
                    $upgradeVersion = $version;
                    $sql = $this->sqlSplit($sql, C("DB_PREFIX"));
                    if (!empty($sql) && is_array($sql)) {
                        foreach ($sql as $sql_split) {
                            $this->execute($sql_split);
                        }
                    }
                }
            }
        }
        //判断是否有升级程序脚本
        if (file_exists($phpScript)) {
            require_cache($phpScript);
            if (class_exists('Upgrade')) {
                $Upgrade = new Upgrade();
                if ($Upgrade->run() == false) {
                    $this->error = $Upgrade->getError() ? $Upgrade->getError() : "执行模块升级脚本错误，升级未完成！";
                    return -10024;
                }
            }
        }
        //加载配置
        $config = $this->getModuleInstallConfig($module);
        if (!empty($config)) {
            //更新版本号
            $this->where(array('module' => $module))->save(array('version' => $config['version'], 'updatedate' => date('Y-m-d')));
        }
        return true;
    }

    /**
     * 模块卸载
     * @param type $module 模块名称
     * @return boolean
     */
    public function uninstall($module) {
        if (empty($module)) {
            $this->error = '请选择需要卸载的模块！';
            return false;
        }
        //设置脚本最大执行时间
        set_time_limit(0);
        //取得该模块数据库中记录的安装信息
        $info = $this->where(array('module' => $module))->find();
        if (empty($info)) {
            $this->error = '该模块未安装，无需卸载！';
            return false;
        }
        //目录权限检测
        if ($this->chechmod($this->templatePath) == false) {
            $this->error = '目录 ' . $this->templatePath . ' 没有可写权限！';
            return false;
        }
        define("UNINSTALL", true);
        if ($this->where(array("module" => $module))->delete() === false) {
            $this->error = '模块卸载失败！';
            return false;
        }
        import("Dir");
        $Dir = new Dir();
        //是否有php卸载脚本
        $isPhpScript = false;
        //删除权限
        M("Access")->where(array("g" => $module))->delete();
        //判断是否有自己的卸载脚本
        if (file_exists($this->appPath . $module . '/Uninstall/Uninstall.class.php')) {
            require_cache($this->appPath . $module . '/Uninstall/Uninstall.class.php');
            //检查是否存在
            if (class_exists('Uninstall')) {
                $uninstallObj = new Uninstall();
                //检查安装方法是否存在
                if (method_exists($uninstallObj, 'run')) {
                    $isPhpScript = true;
                    //执行卸载
                    if (false == $uninstallObj->run()) {
                        //删除安装状态
                        $this->where(array('module' => $config['module']))->delete();
                        //获取错误
                        if (method_exists($uninstallObj, 'getError')) {
                            $this->error = $uninstallObj->getError();
                        } else {
                            $this->error = '模块卸载失败！';
                        }
                        return false;
                    }
                }
            }
        }
        //判断是否有数据库卸载脚本
        if (file_exists($this->appPath . $module . '/Uninstall/' . $module . '.sql')) {
            //读取
            $sql = file_get_contents($this->appPath . $module . '/Uninstall/' . $module . '.sql');
            $sql = $this->sqlSplit($sql, C("DB_PREFIX"));
            if (!empty($sql) && is_array($sql)) {
                foreach ($sql as $sql_split) {
                    $this->execute($sql_split);
                }
            }
        }
        //移除菜单项和权限项
        M("Menu")->where(array("app" => $module))->delete();
        M("Access")->where(array("g" => $module))->delete();
        //去除对应行为规则
        D('Behavior')->moduleBehaviorUninstall($module);
        //删除模块前台模板
        $Dir->delDir($this->templatePath . $module . DIRECTORY_SEPARATOR);
        //静态资源移除
        $Dir->delDir($this->extresPath . strtolower($module) . DIRECTORY_SEPARATOR);
        //卸载结束，最后调用卸载脚本完成
        if ($isPhpScript) {
            //检查安装方法是否存在
            if (method_exists($uninstallObj, 'end')) {
                if (!$uninstallObj->end()) {
                    $this->error = '卸载失败！';
                    if (method_exists($uninstallObj, 'getError')) {
                        $this->error = $uninstallObj->getError();
                    }
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 模块状态转换
     * @param type $module 模块
     * @return boolean
     */
    public function disabled($module) {
        if (empty($module)) {
            $this->error = '请选模块！';
            return false;
        }
        //取得该模块数据库中记录的安装信息
        $info = $this->where(array('module' => $module))->find();
        if (empty($info)) {
            $this->error = '该模块未安装，无需进行此操作！';
            return false;
        }
        $disabled = $info['disabled'] ? 0 : 1;
        if (false !== $this->where(array('module' => $module))->save(array('disabled' => $disabled))) {
            //更新缓存
            $this->module_cache();
            return true;
        } else {
            $this->error = '状态转换失败！';
            return false;
        }
    }

    /**
     * 检查模块是否安装过
     * @param type $module
     * @return type
     */
    public function isInstall($module) {
        if (empty($module)) {
            return false;
        }
        $count = $this->where(array("module" => $module))->count();
        return $count ? true : false;
    }

    /**
     * 检查某个模块是否存在
     * @param type $module 模块名称
     * @return boolean
     */
    public function exists($module) {
        return is_dir($this->appPath . $module) ? true : false;
    }

    /**
     * 加载模块安装配置文件
     * @param type $module
     * @return boolean
     */
    public function getModuleInstallConfig($module) {
        if (empty($module)) {
            return false;
        }
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
        //加载安装配置文件
        if (file_exists($this->appPath . $module . '/Install/Config.inc.php') || file_exists($this->appPath . $module . '/Config.inc.php')) {
            if (file_exists($this->appPath . $module . '/Config.inc.php')) {
                $moduleConfig = include $this->appPath . $module . '/Config.inc.php';
            } else {
                $moduleConfig = include $this->appPath . $module . '/Install/Config.inc.php';
            }
            if (is_array($moduleConfig)) {
                $Config = $moduleConfig;
            } else {
                //兼容处理
                $Config['modulename'] = $modulename;
                $Config['introduce'] = $introduce;
                $Config['author'] = $author;
                $Config['authorsite'] = $authorsite;
                $Config['authoremail'] = $authoremail;
                $Config['version'] = $version;
            }
        }
        return $Config;
    }

    /**
     * 检查对应目录是否有相应的权限
     * @param type $path 目录地址
     * @return boolean
     */
    protected function chechmod($path) {
        //检查模板文件夹是否有可写权限 TEMPLATE_PATH
        $tfile = "_test.txt";
        $fp = @fopen($path . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($path . $tfile);
        if (!$rs) {
            return false;
        }
        return true;
    }

    /**
     * 分析处理sql语句，执行替换前缀都功能。
     * @param string $sql 原始的sql
     * @param string $tablepre 表前缀
     */
    private function sqlSplit($sql, $tablepre) {
        if ($tablepre != "shuipfcms_")
            $sql = str_replace("shuipfcms_", $tablepre, $sql);
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        if ($r_tablepre != $s_tablepre)
            $sql = str_replace($s_tablepre, $r_tablepre, $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    /**
     * 更新缓存
     * @return type
     */
    public function module_cache() {
        $data = M("Module")->where(array("disabled" => 1))->select();
        $App = array();
        $Module = array();
        foreach ($data as $v) {
            $Module[$v['module']] = $v;
            $App[$v['module']] = $v['module'];
        }
        F("Module", $Module);
        F("App", $App);
        return $data;
    }

    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("Module", NULL);
        F("App", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->module_cache();
    }

    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->module_cache();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->module_cache();
    }

}
