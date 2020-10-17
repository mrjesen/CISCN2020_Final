<?php

/**
 * 推荐位数据模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Position_dataModel extends CommonModel {

    //自动验证
    protected $_validate = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    );

    /**
     * 推荐位中被推送过来的信息编辑
     * @param type $data
     * @return boolean
     */
    public function Position_edit($data) {
        if (!is_array($data)) {
            return false;
        }
        if (!$data['posid'] || !$data['modelid'] || !$data['id']) {
            return false;
        } else {
            $posid = $data['posid'];
            $modelid = $data['modelid'];
            $id = $data['id'];
            unset($data['posid'], $data['modelid'], $data['id']);
        }
        //载入数据处理类
        require_cache(RUNTIME_PATH . 'content_input.class.php');
        $content_input = new content_input($modelid);
        $data['data'] = $content_input->get($data['data'], 2);
        $data['data'] = serialize($data['data']);
        if ($this->where(array('id' => $id, 'modelid' => $modelid, 'posid' => $posid))->save($data) !== false) {
            service("Attachment")->api_update('', 'position-' . $modelid . '-' . $id, 1);
            return true;
        }
        return false;
    }

    /**
     * 信息从推荐位中移除
     * @param type $posid 推荐位id
     * @param type $id 信息id
     * @param type $modelid] 模型id
     */
    public function delete_item($posid, $id, $modelid) {
        if (!$posid || !$id || !$modelid) {
            return false;
        }
        $where = array();
        $where['id'] = $id;
        $where['modelid'] = $modelid;
        $where['posid'] = intval($posid);
        if ($this->where($where)->delete() !== false) {
            $this->content_pos($id, $modelid);
            //删除相关联的附件
            service("Attachment")->api_delete('position-' . $modelid . '-' . $id);
            return false;
        } else {
            return false;
        }
    }

    /**
     * 根据模型ID和信息ID删除推荐信息
     * @param type $modelid
     * @param type $id
     * @return boolean\
     */
    public function deleteByModeId($modelid, $id) {
        if (empty($modelid) || empty($id)) {
            return false;
        }
        $where = array();
        $where['id'] = $id;
        $where['modelid'] = $modelid;
        if ($this->where($where)->delete() !== false) {
            $this->content_pos($id, $modelid);
            //删除相关联的附件
            service("Attachment")->api_delete('position-' . $modelid . '-' . $id);
            return false;
        } else {
            return false;
        }
    }

    /**
     * 更新信息推荐位状态
     * @param type $id 信息id
     * @param type $modelid 模型id
     * @return type
     */
    public function content_pos($id, $modelid) {
        $id = intval($id);
        $modelid = intval($modelid);
        $MODEL = F("Model");
        $tablename = ucwords($MODEL[$modelid]['tablename']);
        $db = M($tablename);
        $info = $this->where(array('id' => $id, 'modelid' => $modelid))->find();
        if ($info) {
            $posids = 1;
        } else {
            $posids = 0;
        }
        //更改文章推荐位状态
        $status = $db->where(array('id' => $id))->save(array('posid' => $posids));
        if (false !== $status && $status > 0) {
            return true;
        } else {
            //有可能副表
            return M($tablename . "_data")->where(array('id' => $id))->save(array('posid' => $posids)) !== false ? true : false;
        }
    }

}
