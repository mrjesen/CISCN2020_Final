<?php

/**
 * 附件上传
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class AttachmentsAction extends BaseAction {

    //上传用户
    public $upname = null;
    //上传用户ID
    public $upuserid = 0;
    //会员组
    public $groupid = 0;
    //是否后台
    public $isadmin = 0;
    //上传模块
    public $module = "Contents";

    function _initialize() {
        //获取session中是否有后台登陆标识
        $isadmin = session("isadmin");
        if ($isadmin) {
            define('IN_ADMIN', true);
            $this->isadmin = 1;
            $usDb = service("PassportAdmin")->isLogged();
            if ($usDb) {
                $this->upname = $usDb['username'];
                $this->upuserid = (int) $usDb['userid'];
            }
            parent::_initialize();
        } else {
            parent::_initialize();
            $this->upname = AppframeAction::$Cache['username'];
            $this->upuserid = AppframeAction::$Cache['uid'];
            $this->groupid = AppframeAction::$Cache['User']['groupid'] ? AppframeAction::$Cache['User']['groupid'] : 8;
        }
    }

    //检查是否有上传权限，json
    public function competence() {
        //上传个数,允许上传的文件类型,是否允许从已上传中选择,图片高度,图片高度,是否添加水印1是
        $args = I('get.args');
        //参数验证码
        $authkey = I('get.authkey');
        //模块
        $module = I('get.module', 'contents');
        //兼容
        if ('content' == $module) {
            $module = 'contents';
        }
        //验证是否可以上传
        $info = $this->isUpload($module, $args, $authkey);
        if (true !== $info) {
            $status = false;
        } else {
            $status = true;
        }

        // jsonp callback
        $callback = I('get.callback');
        $this->ajaxReturn(array(
            'data' => '',
            'info' => $info,
            'status' => $status,
                ), (isset($_GET['callback']) && $callback ? 'JSONP' : 'JSON'));
    }

    /**
     * swfupload 上传 
     * 通过swf上传成功以后回调处理时会调用swfupload_json方法增加cookies！
     */
    public function swfupload() {
        //上传个数,允许上传的文件类型,是否允许从已上传中选择,图片高度,图片高度,是否添加水印1是
        $args = I('get.args');
        //参数验证码
        $authkey = I('get.authkey');
        //模块
        $module = I('get.module', 'contents');
        //兼容
        if ('content' == $module) {
            $module = 'contents';
        }
        //栏目id
        $catid = I('get.catid', 0, 'intval');
        //验证是否可以上传
        $status = $this->isUpload($module, $args, $authkey);
        if (true !== $status) {
            $this->error($status);
        }

        //具体配置参数
        $info = explode(",", $args);
        //是否有已上传文件
        $att_not_used = cookie('att_json');
        if (empty($att_not_used)) {
            $tab_status = ' class="on"';
        }
        if (!empty($att_not_used)) {
            $div_status = ' hidden';
        }
        //参数补充完整
        if (empty($info[1])) {
            //如果允许上传的文件类型为空，启用网站配置的 uploadallowext
            if ($this->isadmin) {
                $info[1] = CONFIG_UPLOADALLOWEXT;
            } else {
                $info[1] = CONFIG_QTUPLOADALLOWEXT;
            }
        }

        //获取临时未处理的图片
        $att = $this->att_not_used();
        //var_dump($att);exit;
        $this->assign("initupload", initupload($this->module, $catid, $info, $this->upuserid, $this->groupid, $this->isadmin));
        //上传格式显示
        $this->assign("file_types", implode(",", explode("|", $info[1])));
        $this->assign("file_size_limit", $this->isadmin ? CONFIG_UPLOADMAXSIZE : CONFIG_QTUPLOADMAXSIZE);
        $this->assign("file_upload_limit", (int) $info[0]);
        //临时未处理的图片
        $this->assign("att", $att);
        $this->assign("tab_status", $tab_status);
        $this->assign("div_status", $div_status);
        $this->assign("att_not_used", $att_not_used);
        $this->assign('module', $this->module);
        $this->assign('catid', $catid);
        $this->assign('upuserid', $this->upuserid);
        $this->assign('upname', $this->upname);
        $this->assign('groupid', $this->groupid);
        $this->assign('isadmin', $this->isadmin);
        //是否添加水印
        $this->assign("watermark_enable", (int) $info[5]);
        $this->display(BASE_LIB_PATH . 'Tpl/Attachments/swfupload.php');
    }

    /**
     * 设置swfupload上传的json格式cookie 
     */
    public function swfupload_json() {
        $arr = array();
        $arr['aid'] = I('get.aid', 0, 'intval');
        $arr['src'] = I('get.src', '', 'trim');
        $arr['filename'] = urlencode(I('get.filename'));
        return $this->upload_json($arr['aid'], $arr['src'], $arr['filename']);
    }

    /**
     * 删除swfupload上传的json格式cookie 
     */
    public function swfupload_json_del() {
        $arr['aid'] = intval($_GET['aid']);
        $arr['src'] = trim($_GET['src']);
        $arr['filename'] = urlencode($_GET['filename']);
        $json_str = json_encode($arr);
        $att_arr_exist = cookie('att_json');
        cookie('att_json', NULL);
        $att_arr_exist = str_replace(array($json_str, '||||'), array('', '||'), $att_arr_exist);
        $att_arr_exist = preg_replace('/^\|\|||\|\|$/i', '', $att_arr_exist);
        cookie('att_json', $att_arr_exist);
    }

    /**
     * 设置upload上传的json格式cookie 
     * @param type $aid 附件id
     * @param type $src 附件路径
     * @param type $filename 附件名称
     * @return type
     */
    protected function upload_json($aid, $src, $filename) {
        return service("Attachment")->upload_json($aid, $src, $filename);
    }

    //检查是否可以上传
    protected function isUpload($module, $args, $authkey) {
        //兼容
        if ('content' == $module) {
            $module = 'contents';
        }
        $Module_list = F("Module");
        if ($Module_list[ucwords($module)]) {
            $this->module = strtolower($module);
        } else {
            return '该模块未安装，不允许上传！';
        }
        //验证参数是否合法
        if (empty($args) || upload_key($args) != $authkey) {
            return '参数非法！';
        }
        //如果是前台上传，判断用户组权限
        if ($this->isadmin == 0) {
            $Member_group = F("Member_group");
            if ((int) $Member_group[$this->groupid]['allowattachment'] < 1) {
                return "所在的用户组没有附件上传权限！";
            }
        }

        return true;
    }

    //获取临时未处理的图片
    protected function att_not_used() {
        //获取临时未处理文件列表
        // 水平凡 修复如果cookie里面有加反斜杠，去除
        $att_json = Input::getVar(cookie('att_json'));
        if ($att_json) {
            if ($att_json) {
                $att_cookie_arr = explode('||', $att_json);
            }
            foreach ($att_cookie_arr as $_att_c)
                $att[] = json_decode($_att_c, true);
            if (is_array($att) && !empty($att)) {
                foreach ($att as $n => $v) {
                    $ext = fileext($v['src']);
                    if (in_array($ext, array('jpg', 'gif', 'png', 'bmp', 'jpeg'))) {
                        $att[$n]['fileimg'] = $v['src'];
                        $att[$n]['width'] = '80';
                        $att[$n]['filename'] = urldecode($v['filename']);
                    } else {
                        $att[$n]['fileimg'] = file_icon($v['src']);
                        $att[$n]['width'] = '64';
                        $att[$n]['filename'] = urldecode($v['filename']);
                    }
                    $this->cookie_att .= '|' . $v['src'];
                }
            }
        }
        return $att;
    }

    /**
     * 用于图片附件上传加水印回调方法
     * @param type $_this
     * @param type $fileInfo
     * @param type $params 
     */
    public static function water($_this, $fileInfo, $params) {
        if ((int) CONFIG_WATERMARKENABLE == 0) {
            return false;
        }
        import("Image");
        //水印文件
        $water = SITE_PATH . CONFIG_WATERMARKIMG;
        //水印位置
        $waterPos = (int) CONFIG_WATERMARKPOS;
        //水印透明度
        $alpha = (int) CONFIG_WATERMARKPCT;
        //jpg图片质量
        $quality = (int) CONFIG_WATERMARKQUALITY;

        foreach ($fileInfo as $file) {
            //原图文件
            $source = $file['savepath'] . $file['savename'];
            //图像信息
            $sInfo = Image::getImageInfo($source);
            //如果图片小于系统设置，不进行水印添加
            if ($sInfo["width"] < (int) CONFIG_WATERMARKMINWIDTH || $sInfo['height'] < (int) CONFIG_WATERMARKMINHEIGHT) {
                continue;
            }
            Image::water($source, $water, $source, $alpha, $waterPos, $quality);
        }
    }

}