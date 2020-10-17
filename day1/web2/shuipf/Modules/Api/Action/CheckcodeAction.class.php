<?php

/**
 * 验证码处理
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CheckcodeAction extends Action {

    public function index() {
        import("Util.Checkcode", LIB_PATH);
        $checkcode = get_instance_of('Checkcode');
        //验证码类型
        $type = I('get.type', 'verify', 'strtolower');
        //获取已有session
        $verify = session("_verify_");
        if (empty($verify)) {
            $verify = array();
        }
        //设置长度
        $codelen = I('get.code_len', 0, 'intval');
        if ($codelen) {
            if ($codelen > 8 || $codelen < 2) {
                $codelen = 4;
            }
            $checkcode->codelen = $codelen;
        }
        //设置验证码字体大小
        $fontsize = I('get.font_size', 0, 'intval');
        if ($fontsize) {
            $checkcode->fontsize = $fontsize;
        }
        //设置验证码图片宽度
        $width = I('get.width', 0, 'intval');
        if ($width) {
            $checkcode->width = $width;
        }
        //设置验证码图片高度
        $height = I('get.height', 0, 'intval');
        if ($height) {
            $checkcode->height = $height;
        }
        //设置背景颜色
        $background = I('get.background', '', '');
        if ($background) {
            $checkcode->background = $background;
        }
        //设置字体颜色
        $fontcolor = I('get.font_color', '', '');
        if($fontcolor){
            $checkcode->fontcolor = $fontcolor;
        }

        //显示图片
        $checkcode->output();
        $verify[$type] = $checkcode->getCode();
        session("_verify_", $verify);
        return true;
    }

}
