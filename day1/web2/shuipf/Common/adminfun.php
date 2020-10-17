<?php

/**
 * 后台相关函数
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

/**
 * 检查是否拥有权限
 * @param type $path 需要检查的操作 例如 GROUP/MODULE/ACTION
 * @param type $role_id 角色ID
 * @return boolean
 */
function isCompetence($path = null, $role_id = 0) {
    if ($role_id == 0) {
        //角色ID
        $role_id = (int) AppframeAction::$Cache['User']['role_id'];
    }
    //是否超级管理员组
    if ($role_id == 1) {
        return true;
    }
    if (empty($role_id)) {
        return false;
    }
    if (empty($path)) {
        $path = array(
            'g' => GROUP_NAME,
            'm' => MODULE_NAME,
            'a' => ACTION_NAME,
        );
    } else {
        if (!is_array($path)) {
            $path = explode('/', $path);
        }
        if (empty($path)) {
            return false;
        }
        $counts = count($path);
        if ($counts < 2) {
            $path = array(
                'g' => GROUP_NAME,
                'm' => MODULE_NAME,
                'a' => $path[0],
            );
        } else if ($counts < 3) {
            $path = array(
                'g' => GROUP_NAME,
                'm' => $path[0],
                'a' => $path[1],
            );
        } else {
            $path = array(
                'g' => $path[0],
                'm' => $path[1],
                'a' => $path[2],
            );
        }
    }
    $access = M('Access');
    $status = $access->where(array('role_id' => $role_id, 'g' => $path['g'], 'm' => $path['m'], 'a' => $path['a']))->getField('status');
    if ($status) {
        return true;
    }
    return false;
}
