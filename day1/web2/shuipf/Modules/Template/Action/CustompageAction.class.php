<?php

/**
 * 自定义页面模板
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CustompageAction extends AdminbaseAction {

    //显示自定义页面列表 
    public function index() {
        $count = M("Customtemp")->count();
        $page = $this->page($count, 20);
        $data = M("Customtemp")->order(array("tempid" => "DESC"))->select();
        $this->assign("data", $data);
        $this->assign("pages", $page->show("Admin"));
        $this->display();
    }

    //增加自定义页面 
    public function add() {
        if (IS_POST) {
            $Db = D("Customtemp");
            if ($Db->create()) {
                $tempid = $Db->add();
                if ($tempid) {
                    //生成自定义页面到指定路径
                    $r = $Db->where(array("tempid" => $tempid))->find();
                    import('Html');
                    $html = new Html();
                    $status = $html->createhtml($r['temptext'], $r);
                    if ($status) {
                        $this->success("添加自定义页面成功！", U("Custompage/index"));
                    } else {
                        $this->error("自定义页面生成失败，检查目录是否有可写权限！");
                    }
                } else {
                    $this->error("添加自定义页面失败！");
                }
            } else {
                $this->error($Db->getError());
            }
        } else {
            $this->display();
        }
    }

    //删除自定义页面 
    public function delete() {
        $tempid = I('get.tempid');
        $Db = D("Customtemp");
        $r = $Db->where(array("tempid" => $tempid))->find();
        if ($r) {
            unlink(SITE_PATH . $r['temppath'] . $r['tempname']);
            $status = $Db->where(array("tempid" => $tempid))->delete();
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            $this->error("需要删除的自定义页面不存在！");
        }
    }

    //编辑自定义页面 
    public function edit() {
        $Db = D("Customtemp");
        if (IS_POST) {
            if ($Db->create()) {
                $tempid = I('post.tempid');
                $status = $Db->where(array("tempid" => $tempid))->save();
                if (false !== $status) {
                    //生成自定义页面到指定路径
                    $r = $Db->where(array("tempid" => $tempid))->find();
                    import('Html');
                    $html = new Html();
                    $status = $html->createhtml($r['temptext'], $r);
                    if ($status) {
                        $this->success("自定义页面编辑成功！", U("Custompage/index"));
                    } else {
                        $this->error("自定义页面生成失败，检查目录是否有可写权限！");
                    }
                } else {
                    $this->error("编辑自定义页面失败！");
                }
            } else {
                $this->error($Db->getError());
            }
        } else {
            $tempid = I('get.tempid');
            $r = $Db->where(array("tempid" => $tempid))->find();
            if ($r) {
                $r['temptext'] = Input::forTarea($r['temptext']);
                $this->assign($r);
                $this->display();
            } else {
                $this->error("需要编辑的自定义页面不存在！");
            }
        }
    }

    //生成自定义页面 
    public function createhtml() {
        $Db = D("Customtemp");
        import('Html');
        $html = new Html();
        if (IS_POST) {
            $tempid = $_POST['tempid'];
            foreach ($tempid as $id) {
                $r = $Db->where(array("tempid" => $id))->find();
                if ($r) {
                    $html->createhtml($r['temptext'], $r);
                }
            }
            $this->success("更新完成！", U("Custompage/index"));
        } else {
            if (isset($_GET['tempid'])) {
                $tempid = I('get.tempid');
                $r = $Db->where(array("tempid" => $tempid))->find();
                if ($r) {
                    $status = $html->createhtml($r['temptext'], $r);
                    if ($status) {
                        $this->success("更新完成！", U("Custompage/index"));
                    } else {
                        $this->error("更新失败！", U("Custompage/index"));
                    }
                } else {
                    $this->error("该自定义页面不存在！", U("Custompage/index"));
                }
            } else {
                //更新全部
                $r = $Db->select();
                foreach ($r as $k => $v) {
                    $html->createhtml($v['temptext'], $v);
                }
                $this->success("更新完成！", U("Custompage/index"));
            }
        }
    }

}
