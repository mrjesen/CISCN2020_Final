<?php

/**
 * 附件管理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AtadminAction extends AdminbaseAction {

    //附件存在物理地址
    public $path = "";

    function _initialize() {
        parent::_initialize();
        //附件目录强制/d/file/ 后台设置的附件目录，只对网络地址有效
        $this->path = C("UPLOADFILEPATH");
    }

    /**
     * 附件管理 
     */
    public function index() {
        $db = M("Attachment");
        $where = array();
        $filename = I('get.filename','','trim');
        empty($filename) ? "" : $where['filename'] = array('like', '%' . $filename . '%');
        //时间范围搜索
        $start_uploadtime = I('get.start_uploadtime');
        $end_uploadtime = I('get.end_uploadtime');
        if (!empty($start_uploadtime)) {
            $where['_string'] = 'uploadtime >= ' . strtotime($start_uploadtime) . ' AND uploadtime <= ' . strtotime($end_uploadtime) . '';
        }
        $fileext = I('get.fileext');
        empty($fileext) ? "" : $where['fileext'] = array('eq', $fileext);
        //附件使用状态
        $status = I('get.status');
        $status == "" ? "" : $where['status'] = array('eq', $status);

        $count = $db->where($where)->count();
        $page = $this->page($count, 20);
        $data = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("uploadtime" => "DESC"))->select();

        foreach ($data as $k => $v) {
            $data[$k]['uploadtime'] = date("Y-m-d H:i:s", $data[$k]['uploadtime']);
            $data[$k]['filesize'] = round($data[$k]['filesize'] / 1024, 2);
            $data[$k]['thumb'] = glob(dirname($this->path . $data[$k]['filepath']) . '/thumb_*' . basename($data[$k]['filepath']));
        }
        $this->assign("category", F("Category"));
        $this->assign("filename", $filename);
        $this->assign("start_uploadtime", $start_uploadtime);
        $this->assign("end_uploadtime", $end_uploadtime);
        $this->assign("status", $status);
        $this->assign("fileext", $fileext);
        $this->assign("data", $data);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("show_header", true);
        $this->display();
    }

    /**
     * 删除附件 get单个删除 post批量删除 
     */
    public function delete() {
        $Attachment = service("Attachment");
        if (IS_POST) {
            $aid = $_POST['aid'];
            foreach ($aid as $k => $v) {
                if ($Attachment->delFile((int) $v)) {
                    //删除附件关系
                    M("AttachmentIndex")->where(array("aid" => $v))->delete();
                }
            }
            $status = true;
        } else {
            $aid = $this->_get('aid');
            if (empty($aid)) {
                $this->error("缺少参数！");
            }
            if ($Attachment->delFile((int) $aid)) {
                M("AttachmentIndex")->where(array("aid" => $aid))->delete();
                $status = true;
            } else {
                $status = false;
            }
        }
        if ($status) {
            $this->success("删除附件成功！");
        } else {
            $this->error("删除附件失败！");
        }
    }

}

?>
