<?php

//ShuipFCMS安装程序
if (file_exists('./install.lock')) {
    echo '
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
        你已经安装过该系统，如果想重新安装，请先删除站点Install目录下的 install.lock 文件，然后再安装。
        </body>
        </html>';
    exit;
}
@set_time_limit(1000);
if (phpversion() <= '5.3.0')
    set_magic_quotes_runtime(0);
if ('5.2.0' > phpversion())
    exit('您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！');

date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=UTF-8');
define('SITEDIR', _dir_path(substr(dirname(__FILE__), 0, -8)));
$version = include(SITEDIR . "/shuipf/Conf/version.php");
define("SHUIPF_VERSION", $version['SHUIPF_VERSION']);
include(SITEDIR . "/shuipf/Common/common.php");
include(SITEDIR . "/shuipf/Lib/Util/Dir.class.php");
$Dir = new Dir(SITEDIR);
//数据库
$sqlFile = 'shuipfblog.sql';
$sqlFileDemo = 'shuipfblog_demo.sql';
$configFile = 'config.php';
if (!file_exists(SITEDIR . 'install/' . $sqlFile) || !file_exists(SITEDIR . 'install/' . $configFile)) {
    echo '缺少必要的安装文件!';
    exit;
}
$Title = $version['SHUIPF_APPNAME'];
$Powered = "Powered by abc3210.com";
$steps = array(
    '1' => '安装许可协议',
    '2' => '运行环境检测',
    '3' => '安装参数设置',
    '4' => '安装详细过程',
    '5' => '安装完成',
);
$step = isset($_GET['step']) ? $_GET['step'] : 1;

//地址
$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
$rootpath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "/", $scriptName);
$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
$domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
if ((int) $_SERVER['SERVER_PORT'] != 80) {
    $domain .= ":" . $_SERVER['SERVER_PORT'];
}
$domain = $sys_protocal . $domain . $rootpath;

switch ($step) {

    case '1':
        include_once ("./templates/s1.php");
        exit();

    case '2':

        if (phpversion() < 5) {
            die('本系统需要PHP5+MYSQL >=4.1环境，当前PHP版本为：' . phpversion());
        }

        $phpv = @ phpversion();
        $os = PHP_OS;
        $os = php_uname();
        $tmp = function_exists('gd_info') ? gd_info() : array();
        $server = $_SERVER["SERVER_SOFTWARE"];
        $host = (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        $name = $_SERVER["SERVER_NAME"];
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $err = 0;
        if (empty($tmp['GD Version'])) {
            $gd = '<font color=red>[×]Off</font>';
            $err++;
        } else {
            $gd = '<font color=green>[√]On</font> ' . $tmp['GD Version'];
        }
        if (function_exists('mysql_connect')) {
            $mysql = '<span class="correct_span">&radic;</span> 已安装';
        } else {
            $mysql = '<span class="correct_span error_span">&radic;</span> 出现错误';
            $err++;
        }
        if (ini_get('file_uploads')) {
            $uploadSize = '<span class="correct_span">&radic;</span> ' . ini_get('upload_max_filesize');
        } else {
            $uploadSize = '<span class="correct_span error_span">&radic;</span>禁止上传';
        }
        if (function_exists('session_start')) {
            $session = '<span class="correct_span">&radic;</span> 支持';
        } else {
            $session = '<span class="correct_span error_span">&radic;</span> 不支持';
            $err++;
        }
        $folder = array('/',
            'd',
            'install',
            'shuipf/Conf',
            'shuipf/Conf/addition.php',
            'shuipf/Template',
        );
        include_once ("./templates/s2.php");
        exit();

    case '3':
        $parse_url = parse_url($domain);
        if ($_GET['testdbpwd']) {
            $dbHost = $_POST['dbHost'] . ':' . $_POST['dbPort'];
            $conn = @mysql_connect($dbHost, $_POST['dbUser'], $_POST['dbPwd']);
            if ($conn) {
                die("1");
            } else {
                die("");
            }
        }
        include_once ("./templates/s3.php");
        exit();


    case '4':
        if (intval($_GET['install'])) {
            $n = intval($_GET['n']);
            $arr = array();

            $dbHost = trim($_POST['dbhost']);
            $dbPort = trim($_POST['dbport']);
            $dbName = trim($_POST['dbname']);
            $dbHost = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;
            $dbUser = trim($_POST['dbuser']);
            $dbPwd = trim($_POST['dbpw']);
            $dbPrefix = empty($_POST['dbprefix']) ? 'think_' : trim($_POST['dbprefix']);

            $username = trim($_POST['manager']);
            $password = trim($_POST['manager_pwd']);
            //网站名称
            $site_name = addslashes(trim($_POST['sitename']));
            //网站域名
            $site_url = trim($_POST['siteurl']);
            $_site_url = parse_url($site_url);
            //附件地址
            $sitefileurl = $_site_url['path'] . "d/file/";
            //描述
            $seo_description = trim($_POST['siteinfo']);
            //关键词
            $seo_keywords = trim($_POST['sitekeywords']);
            //测试数据
            $testdata = (int) $_POST['testdata'];
            //邮箱地址
            $siteemail = trim($_POST['manager_email']);

            $conn = @ mysql_connect($dbHost, $dbUser, $dbPwd);
            if (!$conn) {
                $arr['msg'] = "连接数据库失败!";
                echo json_encode($arr);
                exit;
            }
            mysql_query("SET NAMES 'utf8'"); //,character_set_client=binary,sql_mode='';
            $version = mysql_get_server_info($conn);
            if ($version < 4.1) {
                $arr['msg'] = '数据库版本太低!';
                echo json_encode($arr);
                exit;
            }

            if (!mysql_select_db($dbName, $conn)) {
                //创建数据时同时设置编码
                if (!mysql_query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;", $conn)) {
                    $arr['msg'] = '数据库 ' . $dbName . ' 不存在，也没权限创建新的数据库！';
                    echo json_encode($arr);
                    exit;
                }
                if (empty($n)) {
                    $arr['n'] = 1;
                    $arr['msg'] = "成功创建数据库:{$dbName}<br>";
                    echo json_encode($arr);
                    exit;
                }
                mysql_select_db($dbName, $conn);
            }

            //读取数据文件
            $sqldata = file_get_contents(SITEDIR . 'install/' . $sqlFile);
            //读取测试数据
            if ($testdata) {
                $sqldataDemo = file_get_contents(SITEDIR . 'install/' . $sqlFileDemo);
                $sqldata = $sqldata . "\r\n" . $sqldataDemo;
            } else {
                //不加测试数据的时候，删除d目录的文件
                try {
                    $Dir->delDir(SITEDIR . 'd/file/contents/');
                } catch (Exception $exc) {
                    
                }
            }
            $sqlFormat = sql_split($sqldata, $dbPrefix);


            /**
              执行SQL语句
             */
            $counts = count($sqlFormat);

            for ($i = $n; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);

                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                    $ret = mysql_query($sql);
                    if ($ret) {
                        $message = '<li><span class="correct_span">&radic;</span>创建数据表' . $matches[1] . '，完成</li> ';
                    } else {
                        $message = '<li><span class="correct_span error_span">&radic;</span>创建数据表' . $matches[1] . '，失败</li>';
                    }
                    $i++;
                    $arr = array('n' => $i, 'msg' => $message);
                    echo json_encode($arr);
                    exit;
                } else {
                    $ret = mysql_query($sql);
                    $message = '';
                    $arr = array('n' => $i, 'msg' => $message);
                    //echo json_encode($arr); exit;
                }
            }

            if ($i == 999999)
                exit;
            //更新配置信息
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$site_name' WHERE varname='sitename'");
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$site_url' WHERE varname='siteurl' ");
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$sitefileurl' WHERE varname='sitefileurl' ");
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$seo_description' WHERE varname='siteinfo'");
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$seo_keywords' WHERE varname='sitekeywords'");
            mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$siteemail' WHERE varname='siteemail'");

            //读取配置文件，并替换真实配置数据
            $strConfig = file_get_contents(SITEDIR . 'install/' . $configFile);
            $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
            $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
            $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
            $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
            $strConfig = str_replace('#DB_PORT#', $dbPort, $strConfig);
            $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
            $strConfig = str_replace('#AUTHCODE#', genRandomString(18), $strConfig);
            $strConfig = str_replace('#COOKIE_PREFIX#', genRandomString(6) . "_", $strConfig);
            @file_put_contents(SITEDIR . '/shuipf/Conf/dataconfig.php', $strConfig);

            //插入管理员
            //生成随机认证码
            $verify = genRandomString(6);
            $time = time();
            $ip = get_client_ip();
            $password = md5($password . md5($verify));
            $query = "INSERT INTO `{$dbPrefix}user` VALUES ('1', '{$username}', '未知', '{$password}', '', '{$time}', '0.0.0.0', '{$verify}', 'admin@abc3210.com', '备注信息', '{$time}', '{$time}', '1', '1', '');";
            mysql_query($query);

            $message = '成功添加管理员<br />成功写入配置文件<br>安装完成．';
            $arr = array('n' => 999999, 'msg' => $message);
            echo json_encode($arr);
            exit;
        }
        include_once ("./templates/s4.php");
        exit();

    case '5':
        include_once ("./templates/s5.php");
        @touch('./install.lock');
        exit();
}

function sql_execute($sql, $tablepre) {
    $sqls = sql_split($sql, $tablepre);
    if (is_array($sqls)) {
        foreach ($sqls as $sql) {
            if (trim($sql) != '') {
                mysql_query($sql);
            }
        }
    } else {
        mysql_query($sqls);
    }
    return true;
}

function sql_split($sql, $tablepre) {

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

function _dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

//检查目录文件权限
function dir_create($path) {
    global $Dir;
    if (!$path) {
        return false;
    }
    $dir = array();
    $dir['isReadable'] = is_readable($path);
    $dir['isWritable'] = is_writable($path);
    return $dir;
}

function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

?>