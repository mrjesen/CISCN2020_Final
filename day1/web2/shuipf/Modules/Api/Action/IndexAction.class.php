<?php

/**
 * Cloud 云平台 API操作
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 * 错误状态信息
 * -10000		授权失败
 * -10001		不能下载远程附件
 * -10002		不能创建临时目录
 * -10003		附件可能被篡改
 * -10004		不能正常解压附件
 * -10005		不能移动文件到新目录
 * -10006		不能删除临时文件
 * -10007		文件缺少读写权限
 * -10008		模块安装失败
 * -10009		插件安装失败
 * -10010		没有开启安全通信
 * -10011		缺少相关参数
 * -10012		该模块已经存在
 * -10013		创建目录失败
 * -10014		该模块不存在
 * -10015		Addons插件管理模块没有安装
 * -10016		该插件已经存在
 * -10017		该插件不存在
 * -10018		获取不到网站配置
 * -10019		更新网站密钥失败
 * -10020		文件数目为空
 * -10021		无法删除原来的旧文件
 * -10022		该模块没有安装，无法升级
 * -10023		无法获取模块安装信息
 * -10024		执行模块升级脚本错误，升级未完成
 * -10025		插件没进行安装，无法进行插件升级
 * -10026		无法获取插件安装信息
 * -10027		执行插件升级脚本错误，升级未完成
 */
class IndexAction extends Action {

    //操作
    private $action = NULL;
    //命令
    private $execute = NULL;
    private $Cloud = NULL;

    protected function _initialize() {
        //获取命令
        $execute = I('execute', '', '');
        $execute = authcode($execute, 'DECODE');
        if (empty($execute)) {
            exit(serialize(array('error' => -10000, 'status' => 'fail')));
        }
        //反序列化
        $execute = unserialize($execute);
        //获取操作名
        $this->action = $execute['action'];
        $this->execute = $execute['execute'];
        //判断操作是否可用
        import('Util.Cloud', BASE_LIB_PATH);
        $this->Cloud = new Cloud();
        $action = $this->action;
        $this->$action();
    }

    //魔术方法
    public function __call($method, $args) {
        return true;
    }

    //是否开启安全通信
    private function connect() {
        $open = C('CLOUD_ON');
        if ($open) {
            return true;
        } else {
            return false;
        }
    }

    //是否开启安全通信
    private function isconnect() {
        //是否开启安全通信
        if ($this->connect() == false) {
            exit(serialize(array('error' => -10010, 'status' => 'fail')));
        } else {
            exit(serialize(array('status' => 'success')));
        }
    }

    //系统操作相关
    private function system() {
        //是否开启安全通信
        if ($this->connect() == false) {
            exit(serialize(array('error' => -10010, 'status' => 'fail')));
        }
        //获取命令，命令是以序列化的方式
        $command = unserialize($this->execute);
        switch ($command['execute']) {
            //系统小更新，不涉及数据库
            case 'upgrade':
                //缺少必要参数
                if (empty($command['package'])) {
                    exit(serialize(array('error' => -10011, 'package' => $command['package'], 'status' => 'fail')));
                }
                //升级
                $status = $this->Cloud->upgrade_system($command['package'], $command['hash']);
                //升级成功
                if ($status > 0) {
                    exit(serialize(array('status' => 'success')));
                } else {
                    exit(serialize(array('error' => $status, 'status' => 'fail', 'lastfile' => $this->Cloud->lastfile)));
                }
                break;
        }
    }

    //更新网站密钥
    private function keys() {
        $path = APP_PATH . 'Conf/dataconfig.php';
        if (is_writable($path) == false) {
            exit(serialize(array('error' => -10007, 'status' => 'fail')));
        }
        //读取数据
        $config = include $path;
        if (empty($config)) {
            exit(serialize(array('error' => -10018, 'status' => 'fail')));
        }
        //开始更新
        $config['AUTHCODE'] = genRandomString(30);
        if (F('dataconfig', $config, APP_PATH . 'Conf/')) {
            //删除缓存
            $Dir = new Dir();
            $Dir->del(RUNTIME_PATH);
            exit(serialize(array('authcode' => $config['AUTHCODE'], 'status' => 'success')));
        } else {
            exit(serialize(array('error' => -10019, 'status' => 'fail')));
        }
    }

    //云市场
    private function market() {
        //是否开启安全通信
        if ($this->connect() == false) {
            exit(serialize(array('error' => -10010, 'status' => 'fail')));
        }
        //获取命令，命令是以序列化的方式
        $command = unserialize($this->execute);
        switch ($command['execute']) {
            //测试命令
            case 'testing':
                exit(serialize(array('string' => $command['string'], 'status' => 'success')));
                break;
            //模块处理
            case 'module':
                //模块路径
                $modulePath = APP_PATH . C('APP_GROUP_PATH') . '/' . $command['appid'] . '/';
                //IO操作类
                $Dir = get_instance_of('Dir');
                //操作
                switch ($command['method']) {
                    //模块安装
                    case 'install':
                        //缺少必要参数
                        if (empty($command['appid']) || empty($command['package'])) {
                            exit(serialize(array('error' => -10011, 'appid' => $command['appid'], 'package' => $command['package'], 'status' => 'fail')));
                        }
                        //检查模块是否存在
                        if (D('Module')->exists($command['appid'])) {
                            exit(serialize(array('error' => -10012, 'appid' => $command['appid'], 'status' => 'fail')));
                        }
                        //创建模块目录
                        if (mkdir($modulePath, 0777, TRUE) === FALSE) {
                            //====表明已经存在目录，尝试检测权限
                            //测试读写权限
                            $status = $this->Cloud->valid_perm($modulePath);
                            if (count($status)) {
                                exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                            } else {
                                exit(serialize(array('error' => -10013, 'catalog' => $modulePath, 'status' => 'fail')));
                            }
                        } else {
                            //测试读写权限
                            $status = $this->Cloud->valid_perm($modulePath);
                            if (count($status)) {
                                exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                            }
                        }
                        //安装模块
                        $status = $this->Cloud->install_module($command['package'], $command['hash'], $command['appid'], $command['option']);
                        //安装成功
                        if ($status > 0) {
                            exit(serialize(array('appid' => $command['appid'], 'status' => 'success')));
                        } else {
                            //删除模块目录
                            $Dir->delDir($modulePath);
                            exit(serialize(array('error' => $status, 'errorinfo' => $this->Cloud->getError(), 'appid' => $command['appid'], 'status' => 'fail', 'lastfile' => $this->Cloud->lastfile, 'info' => $this->Cloud->getError())));
                        }
                        break;
                    //模块升级
                    case 'upgrade':
                        //缺少必要参数
                        if (!$command['appid'] || !$command['package']) {
                            exit(serialize(array('error' => -10011, 'appid' => $command['appid'], 'package' => $command['package'], 'status' => 'fail')));
                        }
                        //无效模块，也就是检查模块是否存在
                        if (D('Module')->exists($command['appid']) === FALSE) {
                            exit(serialize(array('error' => -10014, 'appid' => $command['appid'], 'status' => 'fail')));
                        }
                        //测试读写权限
                        $status = $this->Cloud->valid_perm($modulePath);
                        if (count($status)) {
                            exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                        }
                        //升级模块
                        $status = $this->Cloud->upgrade_module($command['package'], $command['hash'], $command['appid'], $command['option']);
                        //升级成功
                        if ($status > 0) {
                            exit(serialize(array('appid' => $command['appid'], 'status' => 'success')));
                        } else {
                            exit(serialize(array('error' => $status, 'errorinfo' => $this->Cloud->getError(), 'appid' => $command['appid'], 'status' => 'fail', 'lastfile' => $this->Cloud->lastfile)));
                        }
                        break;
                    //模块搜索
                    case 'listing':
                        $module = array();
                        $moduleList = M('Module')->select();
                        if (empty($moduleList)) {
                            $moduleList = array();
                        }
                        foreach ($moduleList as $key => $app) {
                            unset($app['setting']);
                            $module[$app['module']] = $app;
                        }
                        exit(serialize(array('module' => $module, 'status' => 'success')));
                        break;
                }
                break;
            //插件处理
            case 'addons':
                //检测是否有安装插件管理模块
                $appCache = F('App');
                if (!isset($appCache['Addons'])) {
                    exit(serialize(array('error' => -10015, 'status' => 'fail')));
                }
                //插件路径
                $addonPath = D('Addons/Addons')->getAddonsPath() . $command['name'] . '/';
                //操作
                switch ($command['method']) {
                    //插件安装
                    case 'install':
                        //缺少必要参数
                        if (empty($command['name']) || empty($command['package'])) {
                            exit(serialize(array('error' => -10011, 'name' => $command['name'], 'package' => $command['package'], 'status' => 'fail')));
                        }
                        //检查插件是否存在
                        if (D('Addons/Addons')->exists($command['name'])) {
                            exit(serialize(array('error' => -10016, 'name' => $command['name'], 'status' => 'fail')));
                        }
                        //创建插件目录
                        if (mkdir($addonPath, 0777, TRUE) === FALSE) {
                            //====表明已经存在目录，尝试检测权限
                            //测试读写权限
                            $status = $this->Cloud->valid_perm($addonPath);
                            if (count($status)) {
                                exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                            } else {
                                exit(serialize(array('error' => -10013, 'catalog' => $addonPath, 'status' => 'fail')));
                            }
                        } else {
                            //测试读写权限
                            $status = $this->Cloud->valid_perm($addonPath);
                            if (count($status)) {
                                exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                            }
                        }
                        //安装模块
                        $status = $this->Cloud->install_addons($command['package'], $command['hash'], $command['name'], $command['option']);
                        //安装成功
                        if ($status > 0) {
                            exit(serialize(array('name' => $command['name'], 'status' => 'success')));
                        } else {
                            //删除模块目录
                            $Dir->delDir($modulePath);
                            exit(serialize(array('error' => $status, 'errorinfo' => $this->Cloud->getError(), 'name' => $command['name'], 'status' => 'fail', 'lastfile' => $this->Cloud->lastfile)));
                        }
                        break;
                    //插件升级
                    case 'upgrade':
                        //缺少必要参数
                        if (!$command['name'] || !$command['package']) {
                            exit(serialize(array('error' => -10011, 'name' => $command['name'], 'package' => $command['package'], 'status' => 'fail')));
                        }
                        //无效模块，也就是检查模块是否存在
                        if (D('Addons/Addons')->exists($command['name']) === FALSE) {
                            exit(serialize(array('error' => -10017, 'name' => $command['name'], 'status' => 'fail')));
                        }
                        //测试读写权限
                        $status = $this->Cloud->valid_perm($addonPath);
                        if (count($status)) {
                            exit(serialize(array('error' => -10007, 'catalog' => $status, 'status' => 'fail')));
                        }
                        //升级插件
                        $status = $this->Cloud->upgrade_addons($command['package'], $command['hash'], $command['name'], $command['option']);
                        //升级成功
                        if ($status > 0) {
                            exit(serialize(array('name' => $command['name'], 'status' => 'success')));
                        } else {
                            exit(serialize(array('error' => $status, 'errorinfo' => $this->Cloud->getError(), 'name' => $command['name'], 'status' => 'fail', 'lastfile' => $this->Cloud->lastfile)));
                        }
                        break;
                    //插件搜索
                    case 'listing':
                        $addon = array();
                        $addonList = M('Addons')->select();
                        if (empty($addonList)) {
                            $addonList = array();
                        }
                        foreach ($addonList as $key => $info) {
                            unset($info['config'], $info['description']);
                            $addon[$info['name']] = $info;
                        }
                        exit(serialize(array('addon' => $addon, 'status' => 'success')));
                        break;
                }
                break;
        }
    }

    //主版本号
    private function version() {
        //显示版本号
        if ($this->execute == 'schema') {
            echo SHUIPF_VERSION;
        } else {
            echo serialize(array(
                'appname' => SHUIPF_APPNAME,
                'build' => SHUIPF_BUILD,
                'version' => SHUIPF_VERSION,
            ));
        }
    }

}
