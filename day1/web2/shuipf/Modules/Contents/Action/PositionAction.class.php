<?php

/**
 * 推荐位管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class PositionAction extends AdminbaseAction {

    //推荐位列表
    public function index() {
        $db = M("Position");
        $data = $db->order(array("listorder" => "ASC", "posid" => "DESC"))->select();
        $this->assign("data", $data);
        $this->display();
    }

    //信息管理
    public function public_item() {
        if (IS_POST) {
            $items = count($_POST['items']) > 0 ? $_POST['items'] : $this->error("没有信息被选择！");
            $db = D("Position_data");
            if (is_array($items)) {
                foreach ($items as $item) {
                    $_v = explode('-', $item);
                    $db->delete_item((int) $_POST['posid'], (int) $_v[0], (int) $_v[1]);
                }
            }
            $this->success("移除成功！");
        } else {
            $posid = I('get.posid', 0, 'intval');
            $db = M("Position_data");
            $where = array();
            $where['posid'] = array("EQ", $posid);
            $count = $db->where($where)->count();
            $page = $this->page($count, 20);
            $data = $db->where($where)->order(array("listorder" => "DESC", "id" => "DESC"))->limit($page->firstRow . ',' . $page->listRows)->select();
            foreach ($data as $k => $v) {
                $data[$k]['data'] = unserialize($v['data']);
                $tab = ucwords(getModel(getCategory($v['catid'], 'modelid'), 'tablename'));
                $data[$k]['data']['url'] = M($tab)->where(array("id" => $v['id']))->getField("url");
            }

            $this->assign("Page", $page->show('Admin'));
            $this->assign("data", $data);
            $this->assign("posid", $posid);
            $this->display();
        }
    }

    //添加推荐位
    public function add() {
        if (IS_POST) {
            $db = D("Position");
            $_POST['info'] = array_merge($_POST['info'], array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));
            if ($db->positionAdd($_POST['info'])) {
                $this->success("添加成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
            } else {
                $this->error($db->getError());
            }
        } else {
            import('Form');
            $Model = F("ModelType_0");
            if (empty($Model)) {
                D('Model')->model_cache();
                $Model = F("ModelType_0");
            }
            foreach ($Model as $k => $v) {
                $modelinfo[$v['modelid']] = $v['name'];
            }
            $this->assign("modelinfo", $modelinfo);
            $this->display();
        }
    }

    //编辑推荐位
    public function edit() {
        $db = D("Position");
        if (IS_POST) {
            $_POST['info'] = array_merge($_POST['info'], array(C("TOKEN_NAME") => $_POST[C("TOKEN_NAME")]));
            if ($db->positionSave($_POST['info'])) {
                $this->success("更新成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
            } else {
                $this->error($db->getError());
            }
        } else {
            $posid = I('get.posid', 0, 'intval');
            $data = $db->where(array("posid" => $posid))->find();
            if (!$data) {
                $this->error('该推荐位不存在！');
            }
            import('Form');
            $Model = F("ModelType_0");
            if (empty($Model)) {
                D('Model')->model_cache();
                $Model = F("ModelType_0");
            }
            foreach ($Model as $k => $v) {
                $modelinfo[$v['modelid']] = $v['name'];
            }
            $this->assign($data);
            $this->assign("modelinfo", $modelinfo);
            $this->display();
        }
    }

    //删除 推荐位
    public function delete() {
        $posid = I('get.posid', 0, 'intval');
        $db = D("Position");
        if ($db->positionDel($posid)) {
            $this->success("删除成功！<font color=\"#FF0000\">请更新缓存！</font>", U("Contents/Position/index"));
        } else {
            $this->error($db->getError());
        }
    }

    //排序
    public function public_item_listorder() {
        if (IS_POST) {
            $db = M("Position_data");
            foreach ($_POST['listorders'] as $_k => $listorder) {
                $pos = array();
                $pos = explode('-', $_k);
                $db->where(array('id' => $pos[1], 'catid' => $pos[0], 'posid' => $_POST['posid']))->data(array('listorder' => $listorder))->save();
            }
            $this->success("排序更新成功！");
        } else {
            $this->error("请使用POST方式提交！");
        }
    }

    //信息管理编辑
    public function public_item_manage() {
        $db = D("Position_data");
        if (IS_POST) {
            if ($_POST['thumb']) {
                $_POST['data']['thumb'] = $_POST['thumb'];
                $_POST['thumb'] = 1;
            } else {
                $_POST['thumb'] = 0;
            }
            if ($db->Position_edit($_POST)) {
                $this->success("更新成功！");
            } else {
                $this->error("更新失败！");
            }
        } else {
            $id = I('get.id', 0, 'intval');
            $modelid = I('get.modelid', 0, 'intval');
            $posid = I('get.posid', 0, 'intval');
            $data = $db->where(array("id" => $id, "modelid" => $modelid, 'posid' => $posid))->find();
            if (empty($data)) {
                $this->error("该信息不存在！");
            }
            $data['data'] = unserialize($data['data']);
            import('Form');
            $this->assign($data);
            $this->display();
        }
    }

    ///推荐位添加栏目加载
    public function public_category_load() {
        $modelid = I('get.modelid', '', '');
        $modelidList = explode(',', $modelid);
        import('Form');
        $result = F("Category");
        if (is_array($result)) {
            $categorys = array();
            foreach ($result as $r) {
                $categorys[$r['catid']] = $r['catname'];
                if ($r['child'] != 0) {
                    unset($categorys[$r['catid']]);
                }
                if (!empty($modelid) && !in_array($r['modelid'], $modelidList)) {
                    unset($categorys[$r['catid']]);
                }
            }
        }
        echo Form::checkbox($categorys, I('get.catid', 0, ''), 'name="info[catid][]"', '', 1);
    }

}
