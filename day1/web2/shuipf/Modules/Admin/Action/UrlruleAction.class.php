<?php

/**
 * 内容模型相关的URL规则管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UrlruleAction extends AdminbaseAction {

    protected $Urlrule;
    protected $Module;

    protected function _initialize() {
        parent::_initialize();
        $this->Urlrule = D("Urlrule");
        //可用模块缓存
        $this->Module = F("Module");
        if (!$this->Module) {
            D("Module")->module_cache();
            $this->Module = F("Module");
        }
        //可用模块列表
        $Module = array();
        foreach ($this->Module as $r) {
            $Module[strtolower($r['module'])] = array(
                "module" => strtolower($r['module']),
                "name" => $r['name']
            );
        }
        //兼容，由于规则早期使用content而实际模块叫contents，所以这里做处理
        if ($Module["contents"]) {
            $Module["content"] = $Module["contents"];
            $Module["content"]["module"] = "content";
            unset($Module["contents"]);
        }
        $this->assign("Module", $Module);
    }

    //URL规则显示
    public function index() {
        $this->assign("info", $this->Urlrule->order(array('urlruleid' => 'DESC'))->select());
        $this->display();
    }

    //添加新规则
    public function add() {
        if (IS_POST) {
            $data = $this->Urlrule->create();
            if ($data) {
                $data['urlrule'] = str_replace(' ', '', trim($data['urlrule']));
                $status = $this->Urlrule->add($data);
                if ($status) {
                    $this->success("添加成功！", U("Urlrule/index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->Urlrule->getError());
            }
        } else {
            $this->display();
        }
    }

    //编辑规则
    public function edit() {
        if (IS_POST) {
            $data = $this->Urlrule->create();
            if ($data) {
                $data['urlrule'] = str_replace(' ', '', trim($data['urlrule']));
                $status = $this->Urlrule->save($data);
                if ($status !== false) {
                    $this->success("更新成功！", U("Urlrule/index"));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->Urlrule->getError());
            }
        } else {
            $urlruleid = I('get.urlruleid', 0, 'intval');
            $data = $this->Urlrule->where(array("urlruleid" => $urlruleid))->find();
            if (empty($data)) {
                $this->error("该规则不存在！");
            }
            $this->assign("data", $data);
            $this->display();
        }
    }

    //删除规则
    public function delete() {
        $urlruleid = I('get.urlruleid', 0, 'intval');
        if (empty($urlruleid)) {
            $this->error('请指定需要删除的规则！');
        }
        $status = $this->Urlrule->where(array("urlruleid" => $urlruleid))->delete();
        if (false !== $status) {
            $this->success("删除成功！", U("Urlrule/index"));
        } else {
            $this->error("删除失败！");
        }
    }

}
