<?php

/**
 * 后台RBAC权限认证
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class RbacBehavior extends Behavior {

    public function run(&$rbac_status) {
        //载入RBAC权限控制类
        import('RBAC');
        //角色表名称
        C("RBAC_ROLE_TABLE", C("DB_PREFIX") . "role");
        //用户表名称
        C("RBAC_USER_TABLE", C("DB_PREFIX") . "role_user");
        //节点表名称
        C("RBAC_NODE_TABLE", C("DB_PREFIX") . "node");
        //后台用户模型
        C("USER_AUTH_MODEL", "User");
        //认证网关
        C("USER_AUTH_GATEWAY", U("Admin/Public/login"));
        if (!RBAC::AccessDecision(GROUP_NAME)) {
            //检查认证识别号
            if (!RBAC::checkLogin()) {
                $rbac_status['status'] = false;
                $rbac_status['error'] = "请登录后操作！";
                $rbac_status['url'] = C('USER_AUTH_GATEWAY');
                //记录当前页面地址到cookie中，用于登陆成功后跳转到该地址。
                cookie("forward", get_url());
                return ;
            }
            // 没有权限 抛出错误
            if (C('RBAC_ERROR_PAGE')) {
                // 定义权限错误页面
                redirect(C('RBAC_ERROR_PAGE'));
            } else {
                $rbac_status['status'] = false;
                $rbac_status['error'] = "您没有操作此项的权限！";
            }
        } else {
            $rbac_status['status'] = true;
        }
    }

}