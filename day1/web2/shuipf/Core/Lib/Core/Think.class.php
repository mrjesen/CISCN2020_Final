<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP Portal类
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class Think {

    private static $_instance = array();

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function start() {
        // 设定错误和异常处理
        register_shutdown_function(array('Think', 'fatalError'));
        set_error_handler(array('Think', 'appError'));
        set_exception_handler(array('Think', 'appException'));
        // 注册AUTOLOAD方法
        spl_autoload_register(array('Think', 'autoload'));
        //[RUNTIME]
        Think::buildApp();         // 预编译项目
        //[/RUNTIME]
        // 运行应用
        App::run();
        return;
    }

    //[RUNTIME]
    /**
     * 读取配置信息 编译项目
     * @access private
     * @return string
     */
    static private function buildApp() {

        // 读取运行模式
        if (defined('MODE_NAME')) { // 读取模式的设置
            $mode = include MODE_PATH . strtolower(MODE_NAME) . '.php';
        } else {
            $mode = array();
            //加载基本配置
            $version = include SITE_PATH . '/shuipf/Conf/version.php';
            $version = $version ? $version : array();
            $dataconfig = include SITE_PATH . '/shuipf/Conf/dataconfig.php';
            $dataconfig = $dataconfig ? $dataconfig : array();
            $addition = include SITE_PATH . '/shuipf/Conf/addition.php';
            $addition = $addition ? $addition : array();
            $mode['config'] = array_merge($version, $addition, $dataconfig);
        }
        // 加载核心惯例配置文件
        C(include THINK_PATH . 'Conf/convention.php');
        if (isset($mode['config'])) {// 加载模式配置文件
            C(is_array($mode['config']) ? $mode['config'] : include $mode['config'] );
        }

        // 加载项目配置文件
        if (is_file(CONF_PATH . 'config.php'))
            C(include CONF_PATH . 'config.php');

        // 加载框架底层语言包
        L(include THINK_PATH . 'Lang/' . strtolower(C('DEFAULT_LANG')) . '.php');

        $compile = '';
        // 读取核心编译文件列表
        if (isset($mode['core'])) {
            $list = $mode['core'];
        } else {
            $list = array(
                THINK_PATH . 'Common/functions.php', // 标准模式函数库
                CORE_PATH . 'Core/Log.class.php', // 日志处理类
                CORE_PATH . 'Core/Dispatcher.class.php', // URL调度类
                CORE_PATH . 'Core/App.class.php', // 应用程序类
                CORE_PATH . 'Core/Action.class.php', // 控制器类
                CORE_PATH . 'Core/View.class.php', // 视图类
            );
        }
        // 项目追加核心编译列表文件
        if (is_file(CONF_PATH . 'core.php')) {
            $list = array_merge($list, include CONF_PATH . 'core.php');
        }
        foreach ($list as $file) {
            if (is_file($file)) {
                require_cache($file);
                if (!APP_DEBUG)
                    $compile .= compile($file);
            }
        }

        // 加载项目公共文件
        if (is_file(COMMON_PATH . 'common.php')) {
            include COMMON_PATH . 'common.php';
            // 编译文件
            if (!APP_DEBUG)
                $compile .= compile(COMMON_PATH . 'common.php');
        }

        // 加载模式别名定义
        if (isset($mode['alias'])) {
            $alias = is_array($mode['alias']) ? $mode['alias'] : include $mode['alias'];
            alias_import($alias);
            if (!APP_DEBUG)
                $compile .= 'alias_import(' . var_export($alias, true) . ');';
        }

        // 加载项目别名定义
        if (is_file(CONF_PATH . 'alias.php')) {
            $alias = include CONF_PATH . 'alias.php';
            alias_import($alias);
            if (!APP_DEBUG)
                $compile .= 'alias_import(' . var_export($alias, true) . ');';
        }

        //###########################################
        //加载相关配置，需要连数据库的情况下
        //初始化网站基本配置
        $Config = F("Config");
        //产品版本号
        define("SHUIPF_VERSION", C("SHUIPF_VERSION"));
        //产品流水号
        define("SHUIPF_BUILD", C("SHUIPF_BUILD"));
        //产品名称
        define("SHUIPF_APPNAME", C("SHUIPF_APPNAME"));
        //网站访问地址
        define("SITEURL", $Config['siteurl']);
        foreach ($Config as $k => $v) {
            define('CONFIG_' . strtoupper($k), $v);
        }
        //取得已安装模块缓存
        $App = F("App");
        //配置已安装模块列表
        C("APP_GROUP_LIST", implode(",", $App));
        //获取行为列表
        $behaviorCache = F('Behavior');
        C('tags', $behaviorCache);
        //###########################################

        if (APP_DEBUG) {
            // 调试模式加载系统默认的配置文件
            C(include THINK_PATH . 'Conf/debug.php');
            // 读取调试模式的应用状态
            $status = C('APP_STATUS');
            // 加载对应的项目配置文件
            if (is_file(CONF_PATH . $status . '.php'))
            // 允许项目增加开发模式配置定义
                C(include CONF_PATH . $status . '.php');
        }else {
            // 部署模式下面生成编译文件
            build_runtime_cache($compile);
        }
        return;
    }

    //[/RUNTIME]

    /**
     * 系统自动加载ThinkPHP类库
     * 并且支持配置自动加载路径
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        // 检查是否存在别名定义
        if (alias_import($class))
            return;
        $libPath = defined('BASE_LIB_PATH') ? BASE_LIB_PATH : LIB_PATH;
        $group = defined('GROUP_NAME') && C('APP_GROUP_MODE') == 0 ? GROUP_NAME . '/' : '';
        $file = $class . '.class.php';
        if (substr($class, -8) == 'Behavior') { // 加载行为
            if (require_array(array(
                        CORE_PATH . 'Behavior/' . $file,
                        EXTEND_PATH . 'Behavior/' . $file,
                        LIB_PATH . 'Behavior/' . $file,
                        $libPath . 'Behavior/' . $file), true) || (defined('MODE_NAME') && require_cache(MODE_PATH . ucwords(MODE_NAME) . '/Behavior/' . $file))) {
                return;
            }
        } elseif (substr($class, -5) == 'Model') { // 加载模型
            $model = ucwords(str_replace('Model', "", $class));
            if (require_array(array(
                        LIB_PATH . 'Model/' . $group . $file,
                        $libPath . 'Model/' . $file,
                        EXTEND_PATH . 'Model/' . $file,
                        APP_PATH . C("APP_GROUP_PATH") . "/{$model}/Model/{$class}.class.php",), true)) {
                return;
            }
        } elseif (substr($class, -6) == 'Action') { // 加载控制器
            if (require_array(array(
                        LIB_PATH . 'Action/' . $group . $file,
                        $libPath . 'Action/' . $file,
                        EXTEND_PATH . 'Action/' . $file), true)) {
                return;
            }
        } elseif (substr($class, 0, 5) == 'Cache') { // 加载缓存驱动
            if (require_array(array(
                        EXTEND_PATH . 'Driver/Cache/' . $file,
                        CORE_PATH . 'Driver/Cache/' . $file), true)) {
                return;
            }
        } elseif (substr($class, 0, 2) == 'Db') { // 加载数据库驱动
            if (require_array(array(
                        EXTEND_PATH . 'Driver/Db/' . $file,
                        CORE_PATH . 'Driver/Db/' . $file), true)) {
                return;
            }
        } elseif (substr($class, 0, 8) == 'Template') { // 加载模板引擎驱动
            if (require_array(array(
                        EXTEND_PATH . 'Driver/Template/' . $file,
                        CORE_PATH . 'Driver/Template/' . $file), true)) {
                return;
            }
        } elseif (substr($class, 0, 6) == 'TagLib') { // 加载标签库驱动
            if (require_array(array(
                        EXTEND_PATH . 'Driver/TagLib/' . $file,
                        CORE_PATH . 'Driver/TagLib/' . $file,
                        LIB_PATH . "/TagLib/{$class}.class.php"), true)) {
                return;
            }
        } elseif (substr($class, -6) == 'TagLib') {//加载 TagLib
            if (require_cache(LIB_PATH . "/TagLib/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, -7) == 'Service') {//加载 Service
            if (require_cache(LIB_PATH . "/Service/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, 0, 10) == 'Attachment') { // 加载附件方案
            if (require_cache(LIB_PATH . "/Driver/Attachment/{$class}.class.php")) {
                return;
            }
        } elseif (substr($class, 0, 8) == 'Passport') { // 加载通行证
            if (require_cache(LIB_PATH . "/Driver/Passport/{$class}.class.php")) {
                return;
            }
        } elseif (in_array($class, array('content_update', 'content_output', 'content_input', 'content_form', 'content_delete'))) { // 加载内容模块相关处理类
            if (require_cache(RUNTIME_PATH . "{$class}.class.php")) {
                return;
            } else {
                //生成
                D("Content_cache")->model_content_cache();
                if (is_file(RUNTIME_PATH . "{$class}.class.php")) {
                    include RUNTIME_PATH . "{$class}.class.php";
                }
            }
        } else {//加载 Util下 自定义类
            if (require_cache(LIB_PATH . "/Util/{$class}.class.php")) {
                return;
            }
        }

        // 根据自动加载路径设置进行尝试搜索
        $paths = explode(',', C('APP_AUTOLOAD_PATH'));
        foreach ($paths as $path) {
            if (import($path . '.' . $class))
            // 如果加载类成功则返回
                return;
        }
    }

    /**
     * 取得对象实例 支持调用类的静态方法
     * @param string $class 对象类名
     * @param string $method 类的静态方法名
     * @return object
     */
    static public function instance($class, $method = '') {
        $identify = $class . $method;
        if (!isset(self::$_instance[$identify])) {
            if (class_exists($class)) {
                $o = new $class();
                if (!empty($method) && method_exists($o, $method))
                    self::$_instance[$identify] = call_user_func_array(array(&$o, $method));
                else
                    self::$_instance[$identify] = $o;
            } else
                halt(L('_CLASS_NOT_EXIST_') . ':' . $class);
        }
        return self::$_instance[$identify];
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('throw_exception' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        Log::record($error['message'], Log::ERR);
        halt($error);
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                // 页面压缩输出支持
                if (C('OUTPUT_ENCODE')) {
                    $zlib = ini_get('zlib.output_compression');
                    if (empty($zlib))
                        ob_start('ob_gzhandler');
                }
                $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
                if (C('LOG_RECORD'))
                    Log::write("[$errno] " . $errorStr, Log::ERR);
                function_exists('halt') ? halt($errorStr) : exit('ERROR:' . $errorStr);
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                trace($errorStr, '', 'NOTIC');
                break;
        }
    }

    // 致命错误捕获
    static public function fatalError() {
        // 保存日志记录
        if (C('LOG_RECORD'))
            Log::save();
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    function_exists('halt') ? halt($e) : exit('ERROR:' . $e['message'] . ' in <b>' . $e['file'] . '</b> on line <b>' . $e['line'] . '</b>');
                    break;
            }
        }
    }

}
