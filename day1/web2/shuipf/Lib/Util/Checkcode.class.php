<?php

/**
 * 验证码生成类
 * File Name：Checkcode.class.php
 * File Encoding：UTF-8
 * File New Time：2014-3-30 19:56:51
 * Author：水平凡
 * Mailbox：admin@abc3210.com
 */
class Checkcode {

    //随机因子
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    //验证码
    private $code;
    //验证码长度
    private $codelen = 4;
    //宽度
    private $width = 100;
    //高度
    private $height = 30;
    //图形资源句柄
    private $img;
    //指定的字体
    private $font;
    //指定字体大小
    private $fontsize = 15;
    //指定字体颜色
    private $fontcolor;
    //设置背景色
    private $background = '#EDF7FF';

    //构造方法初始化
    public function __construct() {
        $this->font = LIB_PATH . 'Font/elephant.ttf';
    }

    //魔术方法，设置
    public function __set($name, $value) {
        if (empty($name) || in_array($name, array('code', 'img'))) {
            return false;
        }
        $this->$name = $value;
    }

    //生成随机码
    private function createCode() {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    //生成背景
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        if (empty($this->background)) {
            $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        } else {
            //设置背景色
            $color = imagecolorallocate($this->img, hexdec(substr($this->background, 1, 2)), hexdec(substr($this->background, 3, 2)), hexdec(substr($this->background, 5, 2)));
        }
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    //生成文字
    private function createFont() {
        $_x = $this->width / $this->codelen;
        $isFontcolor = false;
        if ($this->fontcolor && !$isFontcolor) {
            $this->fontcolor = imagecolorallocate($this->img, hexdec(substr($this->fontcolor, 1, 2)), hexdec(substr($this->fontcolor, 3, 2)), hexdec(substr($this->fontcolor, 5, 2)));
            $isFontcolor = true;
        }
        for ($i = 0; $i < $this->codelen; $i++) {
            if (!$isFontcolor) {
                $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            }
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }

    //生成线条、雪花
    private function createLine() {
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }

    //输出
    public function output() {
        header('Content-type:image/png');
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        imagepng($this->img);
        imagedestroy($this->img);
    }

    //获取验证码
    public function getCode() {
        return strtolower($this->code);
    }

}
