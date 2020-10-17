<?php

/**
 * 后台管理用户角色模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class RoleModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '角色名称不能为空！'),
        array('status', 'require', '缺少状态！'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
        array('name', '', '该名称已经存在！', 0, 'unique', 3),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('listorder', '0'),
    );

    /**
     * 删除角色
     * @param type $roleid 角色ID
     * @return boolean
     */
    public function delete_role($roleid) {
        if (!$roleid) {
            return false;
        }
        $status = $this->where(array("id" => $roleid))->delete();
        if ($status !== false) {
            //删除access中的授权信息
            D("Access")->where(array("role_id" => $roleid))->delete();
        }
        return $status;
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
    }

}