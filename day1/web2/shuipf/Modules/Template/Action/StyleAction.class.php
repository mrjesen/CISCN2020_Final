<?php

/**
 * 在线模板编辑
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class StyleAction extends AdminbaseAction {

    //模板文件夹
    private $filepath;
    //模板属性
    private $style_info;
    //主题
    private $theme;

    protected function _initialize() {
        parent::_initialize();
        $this->theme = AppframeAction::$Cache['Config']['theme'];
        if (empty($this->theme)) {
            $this->error("主题风格为空！");
        }
        //模板目录路径
        $this->filepath = TEMPLATE_PATH;
        if (file_exists($this->filepath . $this->theme . '/Config.php')) {
            //模板相关配置
            $this->style_info = json_decode(file_get_contents($this->filepath . $this->theme . '/Config.php'), true);
        }
    }

    /**
     * 显示文件目录 
     * @ 中华英雄 2010-7-1
     * @ note: 增加了对后台页面的友好显示
     */
    public function index() {
        //图标目录
        $ext = SITE_PATH . '/statics/images/ext/';
        //访问地址
        $ExtUrl = CONFIG_SITEURL . 'statics/images/ext/';
        //获取图标数组
        $extList = glob($ext . '*.*');
        $TplExtList = array();
        $dirico = 'dir.gif';
        //当前目录路径
        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\', '.',), '', trim(urldecode($_GET['dir']))) : '';
        if ($dir == ".") {
            $dir = "";
        }
        $dir = str_replace("-", "/", $dir);
        $filepath = $this->filepath . $dir;
        $list = glob($filepath . DIRECTORY_SEPARATOR . '*');
        if (!empty($list)) {
            ksort($list);
        }
        $local = str_replace(array(SITE_PATH, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR), array('', DIRECTORY_SEPARATOR), $filepath);
        if (substr($local, -1, 1) == '.') {
            $local = substr($local, 0, (strlen($local) - 1));
        }
        foreach ($list as $k => $v) {
            if (basename($v) == 'Config.php' || basename($v) == 'Thumbs.db') {
                unset($list[$k]);
            } else {
                //获取拓展名
                $thisExt = pathinfo($filepath . $v, PATHINFO_EXTENSION);
                //如果获取为空说明这是文件夹
                $thisExt == '' && $thisExt = 'dir';
                //检测是否有此类型的试图文件
                in_array($ext . $thisExt . '.jpg', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.jpg';
                in_array($ext . $thisExt . '.gif', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.gif';
                in_array($ext . $thisExt . '.png', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.png';
                in_array($ext . $thisExt . '.bmp', $extList) && $TplExtList[$v] = $ExtUrl . $thisExt . '.bmp';
                //兼容不存在视图的文件
                (!in_array($TplExtList[$v], $TplExtList) || $TplExtList[$v] == '') && $TplExtList[$v] = 'hlp.gif';
            }
        }
        $encode_local = str_replace(array('/', '\\'), '|', $local);
        $file_explan = $this->style_info['file_explan'];

        $this->assign("tplist", $list);
        $this->assign("dir", $dir);
        $this->assign("local", $local);
        $this->assign("file_explan", $file_explan);
        $this->assign("encode_local", $encode_local);
        $this->assign("tplextlist", $TplExtList);
        $this->assign("dirico", $dirico);
        $this->assign("diricolen", strlen($dirico));
        $this->display();
    }

    //主题Config.php文件更新 
    public function updatefilename() {
        $file_explan = I('post.file_explan', '', '');
        if (!isset($this->style_info['file_explan'])) {
            $this->style_info['file_explan'] = array();
        }
        $this->style_info['file_explan'] = array_merge($this->style_info['file_explan'], $file_explan);
        //检查文件是否可写
        if (!is_writable($this->filepath . $this->theme . '/Config.php')) {
            $this->error('文件：' . $this->filepath . $this->theme . '/Config.php ,不可写！');
        }
        if (file_put_contents($this->filepath . $this->theme . '/Config.php', json_encode($this->style_info))) {
            $this->success("更新成功！");
        } else {
            $this->error('更新失败！');
        }
    }

    //添加模板
    public function add() {
        if (IS_POST) {
            //取得文件名
            $file = pathinfo(I('post.file'));
            $file = $file['filename'] . C("TMPL_TEMPLATE_SUFFIX");
            //模板内容
            $content = Input::getVar(I('post.content', '', ''));
            //目录
            $dir = $this->filepath . I('post.dir', '', '');
            $dir = str_replace(array("//"), array("/"), $dir);
            //检查目录是否存在
            if (!file_exists($dir)) {
                $this->error("该目录不存在！");
            }
            //检查目录是否可写
            if (!is_writable($dir)) {
                $this->error('目录 ' . $dir . ' 不可写！');
            }
            //完整新增文件路径
            $filepath = $dir . $file;
            if (file_exists($filepath)) {
                $this->error("该文件已经存在！");
            }
            //写入文件
            $status = file_put_contents($filepath, htmlspecialchars_decode(stripslashes($content)));
            if ($status) {
                $this->success("保存成功！", U("Template/Style/index"));
            } else {
                $this->error("保存失败，请检查模板文件权限是否设置为可写！");
            }
        } else {
            //取得目录路径
            $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\', '.',), '', trim(urldecode($_GET['dir']))) : '';
            $dir = str_replace("-", "/", $dir);
            if (!file_exists($this->filepath . $dir)) {
                $this->error("该目录不存在！");
            }
            $this->assign("dir", $dir);
            $this->display();
        }
    }

    //删除模板
    public function delete() {
        //取得目录路径
        $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\'), '', urldecode(trim($_GET['dir']))) : '';
        $dir = str_replace("-", "/", $dir);
        $file = isset($_GET['file']) && trim($_GET['file']) ? trim($_GET['file']) : '';
        $path = $this->filepath . $dir . "/" . $file;
        $path = str_replace(array("//"), array("/"), $path);
        //检查文件是否可写
        if (!is_writable($path)) {
            $this->error("文件 {$path} 不可写！");
        }
        if (file_exists($path)) {
            if (unlink($path)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败，请检查模板文件权限是否设置为可写！");
            }
        } else {
            $this->error("文件 {$path} 不存在，无需删除！");
        }
    }

    //编辑文件
    public function edit_file() {
        if (IS_POST) {
            //文件
            $file = I('post.file', '', '');
            //目录
            $dir = I('post.dir', '', '');
            $dir = str_replace(array("//"), array("/"), $dir);
            //完整路径
            $path = $this->filepath . $dir . "/" . $file;
            $path = str_replace(array("//"), array("/"), $path);
            if (!file_exists($path)) {
                $this->error("文件 {$path} 不存在！");
            }
            //检查文件是否可写
            if (!is_writable($path)) {
                $this->error("文件 {$path} 不可写！");
            }
            //模板内容
            $content = Input::getVar(I('post.content', '', ''));
            $status = file_put_contents($path, htmlspecialchars_decode(stripslashes($content)));
            if ($status) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败，请检查模板文件权限是否设置为可写！");
            }
            exit;
        } else {
            //取得目录路径
            $dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\'), '', urldecode(trim($_GET['dir']))) : '';
            $dir = str_replace("-", "/", $dir);
            //文件名
            $file = isset($_GET['file']) && trim($_GET['file']) ? trim($_GET['file']) : '';
            //完整路径
            $path = $this->filepath . $dir . "/" . $file;
            //检查文件是否存在
            if (!file_exists($path)) {
                $this->error("文件 {$path} 不存在！");
            }
            //检查文件是否可写
            if (!is_writable($path)) {
                $this->error("文件 {$path} 不可写！");
            }
            //读取内容
            $content = file_get_contents($path);
            $content = Input::forTarea($content);

            $this->assign("content", $content);
            $this->assign("dir", $dir);
            $this->assign("file", $file);
        }
        $this->display();
    }

}
