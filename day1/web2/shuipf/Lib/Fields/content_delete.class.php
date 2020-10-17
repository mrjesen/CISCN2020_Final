<?php

/**
 * 数据删除时回调
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class content_delete {

    //信息ID
    protected $id = 0;
    //栏目ID
    protected $catid = 0;
    //模型ID
    protected $modelid = 0;
    //字段信息
    protected $fields = array();
    //模型缓存
    protected $model = array();
    //数据
    protected $data = array();
    //最近错误信息
    protected $error = '';
    // 数据表名（不包含表前缀）
    protected $tablename = '';

    function __construct($modelid) {
        $this->model = F("Model");
        $this->modelid = $modelid;
        if (empty($this->model[$this->modelid])) {
            return false;
        }
        $this->fields = F("Model_field_" . $this->modelid);
        $this->tablename = trim($this->model[$this->modelid]['tablename']);
    }

    /**
     * 魔术方法，获取配置
     * @param type $name
     * @return type
     */
    public function __get($name) {
        return isset($this->data[$name]) ? $this->data[$name] : (isset($this->$name) ? $this->$name : NULL);
    }

    /**
     *  魔术方法，设置options参数
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function get($data) {
        if (empty($data)) {
            $this->error = '数据不能为空！';
            return false;
        }
        $this->data = $data;
        $this->id = (int) $data['id'];
        $this->catid = (int) $data['catid'];
        $info = array();
        foreach ($this->fields as $field => $fieldInfo) {
            //字段类型
            $func = $fieldInfo['formtype'];
            if (!$func || !isset($this->data[$field])) {
                continue;
            }
            //字段内容
            $value = $this->data[$field];
            if (method_exists($this, $func)) {
                $this->$func($field, $value);
            }
        }
        return true;
    }

    ##{字段处理函数}##
}