<?php

/**
 * 本模块用于后台用户的资料修改等个人信息相关操作
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AdminmanageAction extends AdminbaseAction {

    //修改当前登陆状态下的用户个人信息
    public function myinfo() {
        if (IS_POST) {
            $User = D("User");
            $data = $User->create();
            //判断是否修改的是否本人
            if (I("post.id") != AppframeAction::$Cache['uid']) {
                $this->error("只能修改本人！");
            }
            if ($data) {
                //过滤数据，防止自己修改自己信息给自己提权，比如，角色提升
                $noq = array("password", "last_login_time", "last_login_ip", "login_count", "role_id", "verify");
                foreach ($noq as $key => $value) {
                    unset($data[$value]);
                }
                if ($User->save($data) !== false) {
                    $this->success("资料修改成功！", U("Adminmanage/myinfo"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($User->getError());
            }
        } else {
            $data = M("User")->where(array("id" => AppframeAction::$Cache['uid']))->find();
            $this->assign("data", $data);
            $this->display();
        }
    }

    //后台登陆状态下修改当前登陆人密码
    public function chanpass() {
        $pass = I("post.new_password");
        $username = AppframeAction::$Cache['User']['username'];
        $userid = AppframeAction::$Cache['uid'];
        if (IS_POST) {
            if (I("post.password") == "") {
                $this->error("请输入旧密码！");
            }
            //检验原密码是否正确
            $user = service("PassportAdmin")->getLocalAdminUser($username, I("post.password", "", "trim"));
            if ($user == false) {
                $this->error("旧密码输入错误！");
            }
            if ($pass != I("post.new_pwdconfirm")) {
                $this->error("两次密码不相同！");
            }
            $up = D("User")->ChangePassword($userid, $pass);
            if ($up) {
                $this->assign("jumpUrl", U("Admin/Public/login"));
                //退出登陆
                service("PassportAdmin")->logoutLocalAdmin();
                $this->success("密码已经更新，请从新登陆！", U("Admin/Public/login"));
            } else {
                $this->error("密码更新失败！");
            }
        } else {
            $this->display();
        }
    }

    //验证密码是否正确
    public function verifypass() {
        $pass = I("get.password");
        $username = AppframeAction::$Cache['User']['username'];
        if (empty($pass)) {
            $this->error("密码不能为空！");
        }
        $user = service("PassportAdmin")->getLocalAdminUser($username, $pass);
        if ($user != false) {
            $this->success("密码正确！");
        } else {
            $this->error("密码错误！");
        }
    }

}