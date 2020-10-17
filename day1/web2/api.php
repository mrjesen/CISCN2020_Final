<?php

/**
 * API调用
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
define('IS_API', true);
//判断是否请求Api模块
if( isset($_GET["g"]) ){
    $url = "./index.php?".http_build_query($_GET);
    header("location:$url");
    exit;
}else{
    $_GET["g"] = "Api";
}
require './index.php';