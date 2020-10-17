<?php

/**
 * 模型管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends AdminbaseAction {

    protected $Model;

    function _initialize() {
        parent::_initialize();
        $this->Model = D("Model");
        load("@.adminfun");
    }

    //显示模型列表
    public function index() {
        $data = $this->Model->where(array("type" => 0))->select();
        $this->assign("data", $data);
        $this->display();
    }

    //添加模型
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            if (empty($data)) {
                $this->error('提交数据不能为空！');
            }
            if ($this->Model->addModel($data)) {
                $this->success("添加模型成功！");
            } else {
                $error = $this->Model->getError();
                $this->error($error ? $error : '添加失败！');
            }
        } else {
            $this->display();
        }
    }

    //编辑模型
    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            if (empty($data)) {
                $this->error('提交数据不能为空！');
            }
            if ($this->Model->editModel($data)) {
                $this->success('模型修改成功！', U('Index/index'));
            } else {
                $error = $this->Model->getError();
                $this->error($error ? $error : '修改失败！');
            }
        } else {
            $modelid = I('get.modelid', 0, 'intval');
            $data = $this->Model->where(array("modelid" => $modelid))->find();
            $this->assign("data", $data);
            $this->display();
        }
    }

    //删除模型
    public function delete() {
        $modelid = I('get.modelid', 0, 'intval');
        //检查该模型是否已经被使用
        $count = M("Category")->where(array("modelid" => $modelid))->count();
        if ($count) {
            $this->error("该模型已经在使用中，请删除栏目后再进行删除！");
        }
        //这里可以根据缓存获取表名
        $modeldata = $this->Model->where(array("modelid" => $modelid))->find();
        if (!$modeldata) {
            $this->error("要删除的模型不存在！");
        }
        if ($this->Model->deleteModel($modelid)) {
            $this->success("删除成功！", U("Models/Index/index"));
        } else {
            $this->error("删除失败！");
        }
    }

    //检查表是否已经存在
    public function public_check_tablename() {
        $tablename = I('get.tablename', '', 'trim');
        $count = $this->Model->where(array("tablename" => $tablename))->count();
        if ($count == 0) {
            $this->success('表名不存在！');
        } else {
            $this->error('表名已经存在！');
        }
    }

    //模型的禁用与启用
    public function disabled() {
        $modelid = I('get.modelid', 0, 'intval');
        $disabled = I('get.disabled') ? 0 : 1;
        $status = $this->Model->where(array('modelid' => $modelid))->save(array('disabled' => $disabled));
        if ($status !== false) {
            $this->success("操作成功！");
        } else {
            $this->error("操作失败！");
        }
    }

    //模型导入
    public function import() {
        if (IS_POST) {
            if (empty($_FILES['file'])) {
                $this->error("请选择上传文件！");
            }
            $filename = $_FILES['file']['tmp_name'];
            if (strtolower(substr($_FILES['file']['name'], -3, 3)) != 'txt') {
                $this->error("上传的文件格式有误！");
            }
            //读取文件
            $data = file_get_contents($filename);
            //删除
            @unlink($filename);
            //模型名称
            $name = I('post.name', NULL, 'trim');
            //模型表键名
            $tablename = I('post.tablename', NULL, 'trim');
            //导入
            $status = $this->Model->import($data, $tablename, $name);
            if ($status) {
                $this->success("模型导入成功，请及时更新缓存！");
            } else {
                $this->error($this->Model->getError() ? $this->Model->getError() : '模型导入失败！');
            }
        } else {
            $this->display();
        }
    }

    //模型导出
    public function export() {
        //需要导出的模型ID
        $modelid = I('get.modelid', 0, 'intval');
        if (empty($modelid)) {
            $this->error('请指定需要导出的模型！');
        }
        //导出模型
        $status = $this->Model->export($modelid);
        if ($status) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=spf_model_" . $modelid . '.txt');
            echo $status;
        } else {
            $this->error($this->Model->getError() ? $this->Model->getError() : '模型导出失败！');
        }
    }

}
