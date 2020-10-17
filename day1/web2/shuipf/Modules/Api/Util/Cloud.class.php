<?php

/**
 * Cloud 云平台 
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Cloud {

    //临时文件存储目录
    public $filehash;
    //最后一个操作文件
    public $lastfile;
    //错误信息
    public $error;

    public function __construct() {
        //初始化临时文件存储目录
        $this->filehash = RUNTIME_PATH . 'Cloud/filehash/';
    }

    /**
     * 获取临时目录地址
     * @param type $file 远程地址
     * @return type
     */
    public function get_temp_file($file) {
        $basename = pathinfo($file);
        return $this->filehash . md5($basename['filename']) . '/';
    }

    /**
     * 下载文件包地址
     * @param type $file 远程地址
     * @return type
     */
    public function get_pack_file($file) {
        return $this->filehash . md5(basename($file)) . '.zip';
    }

    /**
     * 验证文件哈希
     * @param type $file 文件路径
     * @param type $hash MD5 值
     * @return type
     */
    public function valid_file($file, $hash) {
        return file_exists($file) && md5_file($file) == $hash;
    }

    /**
     * 安装插件
     * @param type $file 地址
     * @param type $md5 文件md5
     * @param type $name 插件名称
     * @param type $option 其他参数选项
     * @return boolean
     */
    public function install_addons($file, $md5, $name, $option = array()) {
        //插件模型
        $addonModel = D('Addons/Addons');
        //获取插件目录
        $addonPath = $addonModel->getAddonsPath() . $name . '/';
        //存储更新包
        $fileList = $this->store_file($file, $md5);
        //发生错误
        if (is_int($fileList) && $fileList < 1) {
            return $fileList;
        }
        //临时目录名
        $tmp = $this->get_temp_file($file);
        //编码处理
        $this->trans_file($tmp);
        //处理文件，清理不需要的文件
        $status = $this->moved_file($tmp, $addonPath, $file);
        if ($status < 1) {
            return $status;
        }
        //载入Addon类
        import('Util.Addon', APP_PATH . C('APP_GROUP_PATH') . '/Addons/');
        //执行插件安装
        if (false !== $addonModel->installAddon($name)) {
            return 1;
        } else {
            $this->error = $addonModel->getError();
            //插件安装失败
            return -10009;
        }
    }

    /**
     * 安装模块
     * @param type $file 地址
     * @param type $md5 文件md5
     * @param type $appid 模块名称
     * @param type $option 其他参数选项
     * @return boolean
     */
    public function install_module($file, $md5, $appid, $option = array()) {
        //模块路径
        $modulePath = APP_PATH . C('APP_GROUP_PATH') . '/' . $appid . '/';
        //存储更新包
        $fileList = $this->store_file($file, $md5);
        //发生错误
        if (is_int($fileList) && $fileList < 1) {
            return $fileList;
        }
        //临时目录名
        $tmp = $this->get_temp_file($file);
        //编码处理
        $this->trans_file($tmp);
        //处理文件，清理不需要的文件
        $status = $this->moved_file($tmp, $modulePath, $file);
        if ($status < 1) {
            return $status;
        }
        //执行模块安装
        if (false !== D('Module')->install($appid)) {
            //清理
            $Dir = get_instance_of('Dir');
            $Dir->delDir($modulePath . 'Install/');
            return 1;
        }
        $this->error = D('Module')->getError();
        //模块安装失败
        return -10008;
    }

    /**
     * 升级系统(暂不可用)
     * @param type $file 升级包地址
     * @param type $md5 升级包md5
     * @return boolean
     */
    public function upgrade_system($file, $md5) {
        //存储更新包
        $fileList = $this->store_file($file, $md5);
        //发生错误
        if (is_int($fileList) && $fileList < 1) {
            return $fileList;
        }
        //检查是否有更新文件
        if (empty($fileList)) {
            //文件数目为空
            return -10020;
        }
        //临时目录名
        $tmp = $this->get_temp_file($file);
        //验证文件权限
        $stat = $this->valid_perm($tmp);
        if (count($stat)) {
            //记录在案
            $this->lastfile = str_replace(RUNTIME_PATH, '', $tmp);
            //文件缺少读写权限
            return -10007;
        }
        //进行文件处理
        foreach ($fileList as $key => $info) {
            //是否为目录
            if ($info['folder']) {
                //检查目标目录是否存在 
                if (!is_dir(SITE_PATH . '/' . $info['stored_filename'])) {
                    //创建目录
                    if (!mkdir(SITE_PATH . '/' . $info['stored_filename'], 0777, TRUE)) {
                        //记录在案
                        $this->lastfile = '/' . $info['stored_filename'];
                        //不能创建临时目录
                        return -10013;
                    }
                }
            } else {
                //文件，直接拷贝过去
                if (!copy($info['filename'], SITE_PATH . '/' . $info['stored_filename'])) {
                    //记录在案
                    $this->lastfile = '/' . $info['stored_filename'];
                    //不能创建临时目录
                    return -10005;
                }
            }
        }
        return true;
    }

    /**
     * 升级插件
     * @param type $file 升级包地址
     * @param type $md5 升级包md5
     * @param type $name 插件名称
     * @param type $option 其他参数选项
     * @return boolean
     */
    public function upgrade_addons($file, $md5, $name, $option = array()) {
        //插件模型
        $addonModel = D('Addons/Addons');
        //获取插件目录
        $addonPath = $addonModel->getAddonsPath() . $name . '/';
        //存储更新包
        $fileList = $this->store_file($file, $md5);
        //发生错误
        if (is_int($fileList) && $fileList < 1) {
            return $fileList;
        }
        //临时目录名
        $tmp = $this->get_temp_file($file);
        //转换编码
        $this->trans_file($tmp);
        //处理文件，清理不需要的文件
        $status = $this->moved_file($tmp, $addonPath, $file);
        if ($status < 1) {
            return $status;
        }
        //载入Addon类
        import('Util.Addon', APP_PATH . C('APP_GROUP_PATH') . '/Addons/');
        //升级脚本
        $status = $addonModel->upgradeAddon($name);
        if ($status === true) {
            return 1;
        } else {
            //错误信息
            $this->error = $addonModel->getError() ? $addonModel->getError() : "插件升级失败！";
            return -10027;
        }
    }

    /**
     * 升级模块
     * @param type $file 升级包地址
     * @param type $md5 升级包md5
     * @param type $appid 模块名称
     * @param type $option 其他参数选项
     * @return boolean
     */
    public function upgrade_module($file, $md5, $appid, $option = array()) {
        //模块路径
        $modulePath = APP_PATH . C('APP_GROUP_PATH') . '/' . $appid . '/';
        //存储更新包
        $fileList = $this->store_file($file, $md5);
        //发生错误
        if (is_int($fileList) && $fileList < 1) {
            return $fileList;
        }
        //临时目录名
        $tmp = $this->get_temp_file($file);
        //转换编码
        $this->trans_file($tmp);
        //处理文件，清理不需要的文件
        $status = $this->moved_file($tmp, $modulePath, $file);
        if ($status < 1) {
            return $status;
        }
        //执行升级程序
        $status = D('Module')->upgrade($appid);
        if ($status === true) {
            //清理升级目录
            $Dir = get_instance_of('Dir');
            $Dir->delDir($modulePath . 'Upgrade/');
            return 1;
        } else {
            //错误信息
            $this->error = D('Module')->getError() ? D('Module')->getError() : "模块升级失败！";
            return -10024;
        }
    }

    /**
     * 处理文件，对文件进行更新，创建
     * @param type $tmpdir 临时目录
     * @param type $newdir 目标目录
     * @param type $pack 下载文件包
     * @return type
     */
    public function moved_file($tmpdir, $newdir, $pack) {
        //删除文件包
        unlink($this->get_pack_file($pack));
        $Dir = get_instance_of('Dir');
        $list = $this->rglob($tmpdir . '*', GLOB_BRACE);
        //批量迁移文件
        foreach ($list as $file) {
            $newd = str_replace($tmpdir, $newdir, $file);
            //检查缓存包中的文件如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($file) && is_writable($file) == false) {
                //记录在案
                $this->lastfile = $file;
                //错误信息
                $this->error = "文件或者目录{$file}，不可写！";
                //文件缺少读写权限
                return -10007;
            }
            //检查目标文件是否存在，如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($newd) && is_writable($newd) == false) {
                //记录在案
                $this->lastfile = $newd;
                //错误信息
                $this->error = "文件或者目录{$newd}，不可写！";
                //文件缺少读写权限
                return -10007;
            }
            //检查缓存包对应的文件是否文件夹，如果是，则创建文件夹
            if (is_dir($file)) {
                //文件夹不存在则创建
                if (file_exists($newd) == false && mkdir($newd, 0777, TRUE) == false) {
                    //记录在案
                    $this->lastfile = str_replace($newdir, '', $newd);
                    //错误信息
                    $this->error = "创建文件夹{$newd}失败！";
                    //不能创建临时目录
                    return -10002;
                }
            } else {
                //========文件处理！=============
                if (file_exists($newd)) {
                    //删除旧文件（winodws 环境需要）
                    if (!unlink($newd)) {
                        //记录在案
                        $this->lastfile = $newd;
                        //错误信息
                        $this->error = "无法删除{$newd}文件！";
                        //无法删除原来的旧文件
                        return -10021;
                    }
                }
                //生成新文件，也就是把下载的，生成到新的路径中去
                if (!rename($file, $newd)) {
                    //记录在案
                    $this->lastfile = $newd;
                    //错误信息
                    $this->error = "无法生成{$newd}文件！";
                    //不能移动文件到新目录
                    return -10005;
                }
            }
        }
        //删除临时目录
        $Dir->delDir($tmpdir);
        return true;
    }

    /**
     * 移除文件
     * @param type $tmpdir 目录
     * @param type $list 需要删除的文件列表
     * @return type
     */
    public function clean_file($tmpdir, $list) {
        $Dir = get_instance_of('Dir');
        //过滤空白数组
        $list = array_filter($list);
        //批量转换编码
        foreach ($list as $file) {
            $object = $tmpdir . $file;
            //忽略不存在的文件
            if (file_exists($object) === FALSE)
                continue;
            //删除目录
            if (is_dir($object)) {
                $Dir->delDir($object);
            }
            //删除文件出错
            if (is_file($object) && unlink($object) === FALSE) {
                //记录在案
                $this->lastfile = $object;
                //错误信息
                $this->error = "无法删除{$object}文件！";
                return -10006;
            }
        }
        return true;
    }

    /**
     * 转换编码
     * @param type $tmpdir
     */
    public function trans_file($tmpdir) {
        
    }

    /**
     * 存储文件
     * @param type $file 远程文件
     * @param type $md5 文件哈希
     * @return type
     */
    public function store_file($file, $md5) {
        //创建临时目录
        $tmpdir = $this->get_temp_file($file);
        if (!file_exists($tmpdir)) {
            //发生错误
            if (mkdir($tmpdir, 0777, TRUE) === FALSE) {
                //记录在案
                $this->lastfile = str_replace(RUNTIME_PATH, '', $this->lastfile);
                //错误信息
                $this->error = "创建目录{$tmpdir}失败！";
                //不能创建临时目录
                return -10002;
            }
        }
        //本地文件名
        $locale = $this->get_pack_file($file);
        if ($this->valid_file($locale, $md5)) {
            //直接使用已有包
            $package = $locale;
        } else {
            //下载文件包
            $package = $this->download($file, $locale);
        }
        //发生错误
        if ($package === FALSE) {
            $basename = pathinfo($file);
            //记录在案
            $this->lastfile = $basename['filename'];
            //错误信息
            $this->error = "无法获取远程文件！";
            //不能下载远程附件
            return -10001;
        }
        import('Pclzip');
        //实例zip类
        $zip = new PclZip($package);
        //解压到临时目录
        $stat = $zip->extract(PCLZIP_OPT_PATH, $tmpdir);
        //返回文件数量 不能正常解压附件
        if ($stat) {
            return $stat;
        } else {
            //错误信息
            $this->error = "不能正常解压文件！";
            return -10004;
        }
    }

    /**
     * 验证文件权限
     * @param type $dire 文件名
     * @param type $ignore 需要忽略的目录
     * @return type
     */
    public function valid_perm($dire, $ignore = array('')) {
        if (!empty($ignore)) {
            foreach ($ignore as $k => $r) {
                $ignore[$k] = $dire . $r;
            }
        }
        //获取文件清单
        $list = $this->rglob($dire . '*', GLOB_BRACE, $ignore);
        //无读写权限清单
        $stat = array();
        //将目标加入清单
        array_unshift($list, $dire);
        //批量测试权限
        foreach ($list as $file) {
            if (is_writeable($file) === FALSE) {
                array_push($stat, str_replace(SITE_PATH, '', $file));
            }
        }
        return $stat;
    }

    /**
     * 远程保存
     * @param type $url 远程地址
     * @param type $file 保存地址
     * @return type
     */
    public function download($url, $file = "", $timeout = 60) {
        //提取文件名
        $filename = pathinfo($url, PATHINFO_BASENAME);
        if ($file && is_dir($file)) {
            //构造存储名称
            $file = $file . $filename;
        } else {
            //提取文件名
            $file = empty($file) ? $filename : $file;
            //提取目录名
            $dir = pathinfo($file, PATHINFO_DIRNAME);
            //目录不存在时创建
            !is_dir($dir) && mkdir($dir, 0755, true);
            $url = str_replace(" ", "%20", $url);
        }
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $temp = curl_exec($ch);
            if (file_put_contents($file, $temp) && !curl_error($ch)) {
                return $file;
            } else {
                return false;
            }
        } else {
            //PHP 5.3 兼容
            if (PHP_VERSION >= '5.3') {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $opts = array(
                    "http" => array(
                        "method" => "GET",
                        "header" => $userAgent,
                        "timeout" => $timeout)
                );
                $context = stream_context_create($opts);
                $res = copy($url, $file, $context);
            } else {
                $res = copy($url, $file);
            }
            if ($res) {
                return $file;
            }
            return false;
        }
    }

    /**
     * 遍历文件目录，返回目录下所有文件列表
     * @param type $pattern 路径及表达式
     * @param type $flags 附加选项
     * @param type $ignore 需要忽略的文件
     * @return type
     */
    public function rglob($pattern, $flags = 0, $ignore = array()) {
        //获取子文件
        $files = glob($pattern, $flags);
        //修正部分环境返回 FALSE 的问题
        if (is_array($files) === FALSE)
            $files = array();
        //获取子目录
        $subdir = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
        if (is_array($subdir)) {
            foreach ($subdir as $dir) {
                if ($ignore && in_array($dir, $ignore))
                    continue;
                $files = array_merge($files, $this->rglob($dir . '/' . basename($pattern), $flags, $ignore));
            }
        }
        return $files;
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }

}
