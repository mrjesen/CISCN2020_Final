<?php

/**
 * 模块安装，菜单/权限配置
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
defined('INSTALL') or exit('Access Denied');
//添加一个菜单到后台“模块->模块列表”ID等于常量 MENUID
$parentid = M("Menu")->add(
        array(
            //父ID
            "parentid" => 0,
            //模块目录名称，也是项目名称
            "app" => "Addons",
            //文件名称，比如LinksAction.class.php就填写 Links
            "model" => "Addons",
            //方法名称
            "action" => "index",
            //附加参数 例如：a=12&id=777
            "data" => "",
            //类型，1：权限认证+菜单，0：只作为菜单
            "type" => 0,
            //状态，1是显示，2是不显示
            "status" => 1,
            //名称
            "name" => "扩展",
            //备注
            "remark" => "扩展管理！",
            //排序
            "listorder" => 6,
        )
);
$kuozhanguanli = M("Menu")->add(array("parentid" => $parentid, "app" => "Addons", "model" => "Addons", "action" => "index", "data" => "", "type" => 0, "status" => 1, "name" => "扩展管理", "remark" => "", "listorder" => 0));
$addonsId = M("Menu")->add(array("parentid" => $kuozhanguanli, "app" => "Addons", "model" => "Addons", "action" => "index", "data" => "", "type" => 1, "status" => 1, "name" => "插件管理", "remark" => "", "listorder" => 0));
M("Menu")->add(array("parentid" => $parentid, "app" => "Addons", "model" => "Addons", "action" => "addonadmin", "data" => "", "type" => 0, "status" => 0, "name" => "插件后台列表", "remark" => "", "listorder" => 1));
M("Menu")->add(array("parentid" => $addonsId, "app" => "Addons", "model" => "Addons", "action" => "create", "data" => "", "type" => 1, "status" => 1, "name" => "创建新插件", "remark" => "", "listorder" => 0));
M("Menu")->add(array("parentid" => $addonsId, "app" => "Addons", "model" => "Addons", "action" => "local", "data" => "", "type" => 1, "status" => 1, "name" => "本地安装", "remark" => "", "listorder" => 0));
M("Menu")->add(array("parentid" => $kuozhanguanli, "app" => "Addons", "model" => "Addons", "action" => "unpack", "data" => "", "type" => 1, "status" => 0, "name" => "插件打包", "remark" => "", "listorder" => 0));