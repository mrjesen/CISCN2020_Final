<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class LogsAction extends AdminbaseAction {

    //后台登陆日志查看
    public function loginlog() {
        $username = I('get.username');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        $loginip = I('get.loginip');
        $status = I('get.status');
        if (!empty($username)) {
            $data['username'] = array('like', '%' . $username . '%');
        }
        if (!empty($start_time) && !empty($end_time)) {
            $data['_string'] = " `logintime` >'$start_time' AND  `logintime`<'$end_time' ";
        }
        if (!empty($loginip)) {
            $data['loginip '] = array('like', '%' . $loginip . '%');
        }
        if (!empty($status) || $status == '0') {
            $data['status'] = array('eq', $status);
        }
        if (is_array($data)) {
            $data['_logic'] = 'or';
            $map['_complex'] = $data;
        } else {
            $map = array();
        }
        $count = M("Loginlog")->where($map)->count();
        $page = $this->page($count, 20);
        $logs = M("Loginlog")->where($map)->limit($page->firstRow . ',' . $page->listRows)->order(array("loginid" => "desc"))->select();
        $this->assign("Page", $page->show('Admin'));
        $this->assign("logs", $logs);
        $this->display();
    }

    //删除一个月前的登陆日志
    public function deleteloginlog() {
        $t = date("Y-m-d H:i:s", time() - 2592000);
        if (D("Loginlog")->where(array("logintime" => array("lt", $t)))->delete() !== false) {
            $this->success("删除登陆日志成功！");
        } else {
            $this->error("删除登陆日志失败！");
        }
    }

    //操作日志查看
    public function index() {
        $uid = I('get.uid');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        $ip = I('get.ip');
        $status = I('get.status');
        if (!empty($uid)) {
            $data['uid'] = array('eq', $uid);
        }
        if (!empty($start_time) && !empty($end_time)) {
            $data['_string'] = " `time` >'$start_time' AND  `time`<'$end_time' ";
        }
        if (!empty($ip)) {
            $data['ip '] = array('like', '%' . $ip . '%');
        }
        if (!empty($status)) {
            $data['status'] = array('eq', $status);
        }
        if (is_array($data)) {
            $data['_logic'] = 'or';
            $map['_complex'] = $data;
        } else {
            $map = array();
        }
        $count = M("Operationlog")->where($map)->count();
        $page = $this->page($count, 20);
        $Logs = M("Operationlog")->where($map)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "desc"))->select();
        $this->assign("Page", $page->show('Admin'));
        $this->assign("logs", $Logs);
        $this->display();
    }

    //删除一个月前的操作日志
    public function deletelog() {
        $t = date("Y-m-d H:i:s", time() - 2592000);
        if (D("Operationlog")->where(array("time" => array("lt", $t)))->delete() !== false) {
            $this->success("删除操作日志成功！");
        } else {
            $this->error("删除操作日志失败！");
        }
    }

}