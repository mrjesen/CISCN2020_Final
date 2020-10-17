<?php

/**
 * 系统行为管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class BehaviorAction extends AdminbaseAction {

    //行为模型
    protected $behavior = NULL;

    protected function _initialize() {
        parent::_initialize();
        $this->behavior = D('Behavior');
    }

    //行为列表
    public function index() {
        $where = array();
        //搜索行为标识
        $keyword = I('get.keyword');
        if (!empty($keyword)) {
            $where['name'] = array('like', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        //获取总数
        $count = $this->behavior->where($where)->count('id');
        $page = $this->page($count, 20);
        $action = $this->behavior->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "desc"))->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign('data', $action);
        $this->display();
    }

    //添加行为
    public function add() {
        if (IS_POST) {
            $post = I('post.', '', '');
            if ($this->behavior->addBehavior($post)) {
                $this->success('添加成功，需要更新缓存后生效！', U('Behavior/index'));
            } else {
                $this->error($this->behavior->getError());
            }
        } else {

            $this->display();
        }
    }

    //编辑行为
    public function edit() {
        if (IS_POST) {
            $post = I('post.', '', '');
            if ($this->behavior->editBehavior($post)) {
                $this->success('修改成功，需要更新缓存后生效！', U('Behavior/index'));
            } else {
                $this->error($this->behavior->getError());
            }
        } else {
            $id = I('get.id', 0, 'intval');
            if (empty($id)) {
                $this->error('请选择需要编辑的行为！');
            }
            //查询出行为信息
            $info = $this->behavior->getBehaviorById($id);
            if (empty($info)) {
                $error = $this->behavior->getError();
                $this->error($error ? $error : '该行为不存在！');
            }

            $this->assign('info', $info);
            $this->display();
        }
    }

    //删除行为
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->error('请指定需要删除的行为！');
        }
        //删除
        if ($this->behavior->delBehaviorById($id)) {
            $this->success('行为删除成功，需要更新缓存后生效！', U('Behavior/index'));
        } else {
            $error = $this->behavior->getError();
            $this->error($error ? $error : '删除失败！');
        }
    }

    //状态转换
    public function status() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->error('请指定需要状态转换的行为！');
        }
        //状态转换
        if ($this->behavior->statusBehaviorById($id)) {
            $this->success('行为状态转换成功，需要更新缓存后生效！', U('Behavior/index'));
        } else {
            $error = $this->behavior->getError();
            $this->error($error ? $error : '状态转换失败！');
        }
    }

}