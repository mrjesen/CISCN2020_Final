<?php

/**
 * 项目入口文件
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
//开启调试模式
define("APP_DEBUG", false);
define('SITE_PATH', getcwd());
define('APP_NAME', 'Shuipf');
define('APP_PATH', SITE_PATH . '/shuipf/');
define("RUNTIME_PATH", SITE_PATH . "/#runtime/");
define('TEMPLATE_PATH', APP_PATH . 'Template/');
//大小写忽略处理
foreach (array("g", "m") as $v) {
    if (isset($_GET[$v])) {
        $_GET[$v] = ucwords($_GET[$v]);
    }
}
if (!file_exists(APP_PATH . 'Conf/dataconfig.php')) {
    header("Location: install/");
    exit;
}
//载入框架核心文件
require APP_PATH . 'Core/ThinkPHP.php';