<?php

/**
 * 管理员配置管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class ManagementAction extends AdminbaseAction {

    protected $UserMod;

    function _initialize() {
        parent::_initialize();
        $this->UserMod = D("User");
    }

    //管理员列表
    public function manager() {
        //角色id
        $role_id = I('get.role_id');
        $UserView = D("UserView");
        if (empty($role_id)) {
            $count = $UserView->count();
            $page = $this->page($count, 20);
            $User = $UserView->limit($page->firstRow . ',' . $page->listRows)->select();
        } else {
            $count = $UserView->where(array("role_id" => $role_id))->count();
            $page = $this->page($count, 20);
            $User = $UserView->limit($page->firstRow . ',' . $page->listRows)->where(array("role_id" => $role_id))->select();
        }
        $this->assign("Userlist", $User);
        $this->assign("Page", $page->show('Admin'));
        $this->display();
    }

    //编辑信息
    public function edit() {
        $id = I('request.id', 0, 'intval');
        if (empty($id)) {
            $this->error("请选择需要编辑的信息！");
        }
        if ($id == 1) {
            $this->error("该帐号不支持非本人修改！");
        }
        //判断是否修改本人，在此方法，不能修改本人相关信息
        if (AppframeAction::$Cache['uid'] == $id) {
            $this->error("不能修改本人信息！");
        }
        if (IS_POST) {
            if (false !== $this->UserMod->editUser($_POST)) {
                $this->success("更新成功！", U("Management/manager"));
            } else {
                $this->error($this->UserMod->getError());
            }
        } else {
            $data = $this->UserMod->where(array("id" => $id))->find();
            $role = M("Role")->select();
            $this->assign("role", $role);
            $this->assign("data", $data);
            $this->display();
        }
    }

    //添加管理员
    public function adminadd() {
        if (IS_POST) {
            if ($this->UserMod->addUser($_POST)) {
                $this->success("添加管理员成功！", U('Management/manager'));
            } else {
                $this->error($this->UserMod->getError());
            }
        } else {
            $data = M("Role")->select();
            $this->assign("role", $data);
            $this->display();
        }
    }

    //管理员删除
    public function delete() {
        $id = I('get.id');
        if (empty($id)) {
            $this->error("没有指定删除对象！");
        }
        if ((int) $id == AppframeAction::$Cache["uid"]) {
            $this->error("你不能删除你自己！");
        }
        //执行删除
        if ($this->UserMod->delUser($id)) {
            $this->success("删除成功！");
        } else {
            $this->error($this->UserMod->getError());
        }
    }

}