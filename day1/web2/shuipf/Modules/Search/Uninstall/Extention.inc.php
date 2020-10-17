<?php 
/**
 * 模块卸载，菜单/权限配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
defined('UNINSTALL') or exit('Access Denied');
//删除菜单/权限数据
M("Menu")->where(array("app"=>"Search"))->delete();
M("Access")->where(array("g" => "Search"))->delete();
?>