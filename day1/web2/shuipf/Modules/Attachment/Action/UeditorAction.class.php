<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UeditorAction extends AttachmentsAction {

    function _initialize() {
        define("Ueditor", true);
        parent::_initialize();
    }

    //图片在线浏览的处理地址
    public function imageManager() {
        $data = $this->att_not_used();
        $str = "";
        foreach ($data as $v) {
            $str .= $v['src'] . "ue_separate_ue";
        }
        echo $str;
    }

    /**
     * 编辑器图片上传
     * array (
      'pictitle' => '6.jpg',
      'Filename' => '6.jpg',
      'catid' => '73',
      'Upload' => 'Submit Query',
      )
     */
    public function imageUp() {
        if (IS_POST) {
            //是否后台
            if ($this->isadmin) {
                
            } else {
                //如果是非后台用户，进行权限判断
                $Member_group = F("Member_group");
                if ((int) $Member_group[$this->groupid]['allowattachment'] < 1) {
                    echo "{'url':'图片地址','state':'没有上传权限！','title':'标题'}";
                    exit;
                }
            }
            //描述
            $pictitle = I('post.pictitle');
            $catid = I('post.catid', I('get.catid', 0, 'intval'), 'intval');
            $module = $catid ? 'contents' : strtolower(GROUP_NAME);
            $Attachment = service("Attachment", array('module' => $module, 'catid' => $catid, 'userid' => $this->upuserid, 'isadmin' => $this->isadmin ? 1 : 0));
            //设置上传类型，强制为图片类型
            $Attachment->uploadallowext = array("jpg", "png", "gif", "jpeg");
            //回调函数
            $Callback = false;
            //是否添加水印 
            if (I('post.watermark_enable')) {
                $Callback = array(
                    array("AttachmentsAction", "water"),
                );
            }
            //开始上传
            $info = $Attachment->upload($Callback);
            if ($info) {
                $in = array(
                    "url" => "",
                    "state" => "",
                    "title" => ""
                );
                // 设置附件cookie
                $Attachment->upload_json($info[0]['aid'], $info[0]['url'], str_replace(array("\\", "/"), "", $info[0]['name']));
                $in['url'] = $info[0]['url'];
                $in['title'] = str_replace(array("\\", "/"), "", $pictitle ? $pictitle : $info[0]['name']);
                $in['state'] = "SUCCESS";
                echo json_encode($in);
                exit;
            }
        }
        echo "{'url':'图片地址','state':'上传失败！','title':'标题'}";
        exit;
    }

    /**
     * 视频搜索 
     */
    public function getMovie() {
        $key = $this->_post("searchKey");
        $type = $this->_post("videoType");
        $html = file_get_contents('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw=' . $key . '&pageNo=1&pageSize=20&channelId=' . $type . '&inDays=7&media=v&sort=s');
        echo $html;
    }

}
