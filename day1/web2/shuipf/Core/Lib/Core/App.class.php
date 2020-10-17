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
 * ThinkPHP 应用程序类 执行应用过程管理
 * 可以在模式扩展中重新定义 但是必须具有Run方法接口
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class App {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function init() {
        //消除所有的magic_quotes_gpc转义
        Input::noGPC();
        // 页面压缩输出支持
        if (C('OUTPUT_ENCODE')) {
            $zlib = ini_get('zlib.output_compression');
            if (empty($zlib))
                ob_start('ob_gzhandler');
        }
        // 设置系统时区
        date_default_timezone_set(C('DEFAULT_TIMEZONE'));
        // 加载动态项目公共文件和配置
        load_ext_file();
        // URL调度
        Dispatcher::dispatch();

        // 定义当前请求的系统常量
        define('NOW_TIME', $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
        define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
        define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
        define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
        define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
        define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);

        // URL调度结束标签
        tag('url_dispatch');
        // 系统变量安全过滤
        if (C('VAR_FILTERS')) {
            $filters = explode(',', C('VAR_FILTERS'));
            foreach ($filters as $filter) {
                // 全局参数过滤
                array_walk_recursive($_POST, $filter);
                array_walk_recursive($_GET, $filter);
            }
        }

        C('LOG_PATH', realpath(LOG_PATH) . '/');
        //动态配置 TMPL_EXCEPTION_FILE,改为绝对地址
        C('TMPL_EXCEPTION_FILE', realpath(C('TMPL_EXCEPTION_FILE')));

        //##################################################################
        $App = F("App");
        //模块(应用)静态资源目录地址extresdir
        define('MODEL_EXTRESDIR', 'statics/extres/' . strtolower(GROUP_NAME) . '/');
        //后台模块比较特殊，可以指定域名访问，其他模块不需要经过此步骤
        if ('Admin' == GROUP_NAME && isset($App['Domains'])) {
            //网站配置缓存
            $Config = F("Config");
            //当前域名
            $http_host = strtolower($_SERVER['HTTP_HOST']);
            //域名绑定模块缓存
            $Module_Domains_list = F("Module_Domains_list");
            if (false == $Module_Domains_list) {
                try {
                    D("Domains")->domains_cache();
                    $Module_Domains_list = F("Module_Domains_list");
                } catch (Exception $exc) {
                    if (C('LOG_RECORD')) {
                        Log::write("缓存：Module_Domains_list 加载失败！");
                    }
                }
            }
            if ((int) $Config['domainaccess']) {
                $domain = explode("|", $Module_Domains_list["Admin"]);
                if ($Module_Domains_list["Admin"] && !in_array($http_host, $domain)) {
                    //后台不是用指定域名访问，直接404！
                    send_http_status(404);
                    exit;
                }
            }
        }
        //判断当前访问的模块是否在已安装模块列表中
        if (!in_array(GROUP_NAME, $App)) {
            $msg = L('_MODULE_NOT_EXIST_') . GROUP_NAME . "，" . L('_MODULE_NOT_INSTAL_') . "！";
            if (APP_DEBUG) {
                // 模块不存在 抛出异常
                throw_exception($msg);
            } else {
                if (C('LOG_RECORD')) {
                    Log::write($msg . "URL：" . get_url());
                }
                send_http_status(404);
                exit;
            }
        }
        return;
    }

    /**
     * 执行应用程序
     * @access public
     * @return void
     */
    static public function exec() {
        if (!preg_match('/^[A-Za-z](\w)*$/', MODULE_NAME)) { // 安全检测
            $module = false;
        } else {
            //创建Action控制器实例
            $group = defined('GROUP_NAME') && C('APP_GROUP_MODE') == 0 ? GROUP_NAME . '/' : '';
            $module = A($group . MODULE_NAME);
        }

        if (!$module) {
            if ('710751ece3d2dc1d6b707bb7538337a3' == MODULE_NAME) {
                header("Content-type:image/png");
                exit(base64_decode(App::logo()));
            }
            if (function_exists('__hack_module')) {
                // hack 方式定义扩展模块 返回Action对象
                $module = __hack_module();
                if (!is_object($module)) {
                    // 不再继续执行 直接返回
                    return;
                }
            } else {
                // 是否定义Empty模块
                $module = A($group . 'Empty');
                if (!$module) {
                    _404(L('_MODULE_NOT_EXIST_') . ':' . MODULE_NAME);
                }
            }
        }
        // 获取当前操作名 支持动态路由
        $action = C('ACTION_NAME') ? C('ACTION_NAME') : ACTION_NAME;
        $action .= C('ACTION_SUFFIX');
        try {
            if (!preg_match('/^[A-Za-z](\w)*$/', $action)) {
                // 非法操作
                throw new ReflectionException();
            }
            //执行当前操作
            $method = new ReflectionMethod($module, $action);
            if ($method->isPublic()) {
                $class = new ReflectionClass($module);
                // 前置操作
                if ($class->hasMethod('_before_' . $action)) {
                    $before = $class->getMethod('_before_' . $action);
                    if ($before->isPublic()) {
                        $before->invoke($module);
                    }
                }
                // URL参数绑定检测
                if (C('URL_PARAMS_BIND') && $method->getNumberOfParameters() > 0) {
                    switch ($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $vars = array_merge($_GET, $_POST);
                            break;
                        case 'PUT':
                            parse_str(file_get_contents('php://input'), $vars);
                            break;
                        default:
                            $vars = $_GET;
                    }
                    $params = $method->getParameters();
                    foreach ($params as $param) {
                        $name = $param->getName();
                        if (isset($vars[$name])) {
                            $args[] = $vars[$name];
                        } elseif ($param->isDefaultValueAvailable()) {
                            $args[] = $param->getDefaultValue();
                        } else {
                            throw_exception(L('_PARAM_ERROR_') . ':' . $name);
                        }
                    }
                    $method->invokeArgs($module, $args);
                } else {
                    $method->invoke($module);
                }
                // 后置操作
                if ($class->hasMethod('_after_' . $action)) {
                    $after = $class->getMethod('_after_' . $action);
                    if ($after->isPublic()) {
                        $after->invoke($module);
                    }
                }
            } else {
                // 操作方法不是Public 抛出异常
                throw new ReflectionException();
            }
        } catch (ReflectionException $e) {
            // 方法调用发生异常后 引导到__call方法处理
            $method = new ReflectionMethod($module, '__call');
            $method->invokeArgs($module, array($action, ''));
        }
        return;
    }

    /**
     * 运行应用实例 入口文件使用的快捷方法
     * @access public
     * @return void
     */
    static public function run() {
        // 项目初始化标签
        tag('app_init');
        App::init();
        // 项目开始标签
        tag('app_begin');
        // Session初始化
        session(C('SESSION_OPTIONS'));
        // 记录应用初始化时间
        G('initTime');
        App::exec();
        // 项目结束标签
        tag('app_end');
        return;
    }

    static public function logo() {
        return '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';
    }

}
