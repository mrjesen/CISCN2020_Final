<?php

/**
 * 敏感词管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CensorAction extends AdminbaseAction {

    public function _initialize() {
        parent::_initialize();
        //取得分类信息
        $typedata = M("Terms")->where(array("module" => "censor"))->select();
        $this->assign("typedata", $typedata);
    }

    public function index() {
        $db = D("Censor");
        if (IS_POST) {
            $delete = $_POST['delete'];
            //敏感词
            $find = $_POST['find'];
            //不良词语类型
            $replacement = $_POST['replacement'];
            //分类
            $type = $_POST['type'];
            //替换内容
            $replacontent = $_POST['replacontent'];
            if (is_array($delete)) {
                foreach ($delete as $id) {
                    $db->where(array("id" => $id))->delete();
                    unset($find[$id], $replacement[$id], $type[$id], $replacontent[$id]);
                }
            }
            //进行数据更新
            if (is_array($find)) {
                foreach ($find as $id => $ti) {
                    $data = array();
                    if ($replacement[$id] == '{REPLACE}') {
                        $data['replacement'] = $replacontent[$id];
                    } else {
                        $data['replacement'] = $replacement[$id];
                    }
                    $data['type'] = $type[$id];
                    $data['find'] = $find[$id];
                    $db->where(array("id" => $id))->save($data);
                }
            }
            //缓存更新
            $db->censorword_cache();
            $this->success("操作成功！");
        } else {
            $where = array();
            if (isset($_GET['search'])) {
                $type = (int) $this->_get("type");
                if ($type) {
                    $where['type'] = array("EQ", $type);
                }
                $keyword = $this->_get("keyword");
                if ($keyword) {
                    $where['find'] = array("LIKE", "%{$keyword}%");
                    $this->assign("keyword", $keyword);
                }
            }
            $count = $db->where($where)->count();
            $page = $this->page($count, 20);
            $data = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "DESC"))->select();

            $this->assign("Page", $page->show('Admin'));
            $this->assign("data", $data);
            $this->display();
        }
    }

    /**
     *  添加规则
     */
    public function add() {
        $db = D("Censor");
        if (IS_POST) {
            $name = $_POST['name'];
            $replacement = $_POST['replacement'];
            $type = (int) $_POST['type'];
            if (!$name) {
                $this->error("请输入不良词语！");
            }
            //创建新分类
            if (isset($_POST['newtype']) && $_POST['newtype']) {
                $type = $this->addTerms($_POST['newtype']);
            }
            $data = array();
            if ($replacement == '{REPLACE}') {
                $data['replacement'] = $_POST['replacontent'];
            } else {
                $data['replacement'] = $replacement;
            }
            $data['find'] = $name;
            $data['type'] = $type;
            $data['admin'] = AppframeAction::$Cache['username'];
            if ($db->add($data)) {
                $db->censorword_cache();
                $this->success("添加成功！", U('Admin/Censor/index'));
            } else {
                $this->error("添加失败！");
            }
        } else {
            $this->display();
        }
    }

    /**
     * 分类管理
     */
    public function classify() {
        if (IS_POST) {
            $name = $_POST['name'];
            //删除
            if (isset($_POST['delete']) && is_array($_POST['delete'])) {
                foreach ($_POST['delete'] as $id) {
                    if (M("Terms")->where(array("id" => $id))->delete()) {
                        M("CensorWord")->where(array("type" => $id))->delete();
                    }
                    unset($name[$id]);
                }
            }
            //更新
            if (is_array($name)) {
                foreach ($name as $id => $v) {
                    M("Terms")->where(array("id" => $id))->data(array("name" => $v))->save();
                }
            }
            $this->success("操作成功！");
        } else {
            $this->display();
        }
    }

    /**
     * 添加分类
     * @param type $name 
     */
    protected function addTerms($name) {
        $name = trim($name);
        if (empty($name)) {
            $this->error("分类名称不能为空！");
        }
        $status = D("Censor")->addTerms();
        if ($status) {
            return $status;
        } else {
            $this->error("分类添加失败！");
        }
    }

}