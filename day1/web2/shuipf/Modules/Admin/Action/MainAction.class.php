<?php

/**
 * 后台环境页
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class MainAction extends AdminbaseAction {

    public function index() {
        //服务器信息
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            'MYSQL版本' => mysql_get_server_info(),
            '产品名称' => '<font color="#FF0000">' . SHUIPF_APPNAME . '</font>' . "&nbsp;&nbsp;&nbsp; [<a href='http://www.shuipfcms.com' target='_blank'>访问官网</a>]",
            '产品版本' => '<font color="#FF0000">' . SHUIPF_VERSION . '</font>',
            '产品流水号' => '<font color="#FF0000">' . SHUIPF_BUILD . '</font>',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
        );

        $this->assign('server_info', $info);
        $this->display();
    }

}