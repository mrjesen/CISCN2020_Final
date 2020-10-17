<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: RBAC.class.php 2504 2011-12-28 07:35:29Z liu21st $

/**
  +------------------------------------------------------------------------------
 * 基于角色的数据库方式验证类
  +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: RBAC.class.php 2504 2011-12-28 07:35:29Z liu21st $
  +------------------------------------------------------------------------------
 */
// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
// RBAC_DB_DSN  数据库连接DSN
// RBAC_ROLE_TABLE 角色表名称
// RBAC_USER_TABLE 用户表名称
// RBAC_ACCESS_TABLE 权限表名称
// RBAC_NODE_TABLE 节点表名称
class RBAC {

    // 认证方法
    static public function authenticate($map, $model = '') {
        if (empty($model))
            $model = C('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        return M($model)->where($map)->find();
    }

    //用于检测用户权限的方法,并保存到Session中，登陆成功以后，注册有权限
    static function saveAccessList($authId = null) {
        if (null === $authId)
            $authId = session(C('USER_AUTH_KEY'));
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开发所有权限
        if (C('USER_AUTH_TYPE') != 2 && !session(C('ADMIN_AUTH_KEY')))
            session("_ACCESS_LIST", RBAC::getAccessList($authId));
        return;
    }

    //检查当前操作是否需要认证 第二步
    static function checkAccess() {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (C('USER_AUTH_ON')) {
            //模块
            $_module = array();
            //动作
            $_action = array();
            if ("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_MODULE')));
            } else {
                //无需认证的模块
                $_module['no'] = explode(',', strtoupper(C('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if ((!empty($_module['no']) && !in_array(strtoupper(MODULE_NAME), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(MODULE_NAME), $_module['yes']))) {
                if ("" != C('REQUIRE_AUTH_ACTION')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_ACTION')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(C('NOT_AUTH_ACTION')));
                }
                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper(ACTION_NAME), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    // 登录检查
    static public function checkLogin() {
        //检查当前操作是否需要认证
        if (RBAC::checkAccess()) {
            //检查认证识别号
            if (!session(C("USER_AUTH_KEY")) || !session("username") || !session("adminverify")) {
                return false;
            }
        }
        return true;
    }

    //权限认证的过滤器方法 第一步
    static public function AccessDecision($appName = APP_NAME) {
        //检查是否需要认证
        if (RBAC::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5($appName . MODULE_NAME . ACTION_NAME);
            //判断是否超级管理员，是无需进行权限认证
            $ADMIN_AUTH_KEY = session(C('ADMIN_AUTH_KEY'));
            if (empty($ADMIN_AUTH_KEY)) {
                //认证类型 1 登录认证 2 实时认证
                if (C('USER_AUTH_TYPE') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = RBAC::getAccessList(session(C('USER_AUTH_KEY')));
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if (session($accessGuid)) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = session("_ACCESS_LIST");
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                $module = defined('P_MODULE_NAME') ? P_MODULE_NAME : MODULE_NAME;
                if (!isset($accessList[strtoupper($appName)][strtoupper($module)][strtoupper(ACTION_NAME)])) {

                    if (self::checkLogin() == true) {

                        if ($appName == "Admin" && in_array(MODULE_NAME, array("Index", "Main")) && in_array(ACTION_NAME, array("index"))) {
                            session($accessGuid, true);
                            return true;
                        }

                        //如果是public_开头的验证通过。
                        if (substr(ACTION_NAME, 0, 7) == 'public_') {
                            session($accessGuid, true);
                            return true;
                        }

                        //如果是内容模块，直接验证通过，交给内容模块自己控制权限
                        if ("Contents" == $appName && "Content" == MODULE_NAME) {
                            session($accessGuid, true);
                            return true;
                        }
                    }

                    session($accessGuid, false);
                    return false;
                } else {
                    session($accessGuid, true);
                }
            } else {
                //进行登陆检测
                if(self::checkLogin()){
                    return true;
                }
                return false;
            }
        }
        return true;
    }

    /**
      +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
      +----------------------------------------------------------
     * @param integer $authId 用户ID
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     */
    static public function getAccessList($authId) {
        //角色表
        $role = M("Role");
        //实例化角色与用户对应关系表
        $role_user = M("Role_user");
        //权限列表
        $access = M("Access");
        //角色ID
        $role_id = $role_user->where(array("user_id" => $authId))->getField("role_id");
        //检查角色
        $roleinfo = $role->where(array("id" => $role_id))->find();
        if (!$roleinfo || $roleinfo['status'] != 1) {
            return false;
        }
        //全部权限
        $accessDATA = $access->where(array("role_id" => $role_id ,'status' => 1))->select();
        $accessList = array();
        foreach ($accessDATA as $acc) {
            $g = strtoupper($acc['g']);
            $m = strtoupper($acc['m']);
            $a = strtoupper($acc['a']);
            $accessList[$g][$m][$a] = $a;
        }
        return $accessList;
    }

}