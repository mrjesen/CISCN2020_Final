<?php

/**
 * 后台首页
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends AdminbaseAction {

    //后台框架首页
    public function index() {
        $this->assign("SUBMENU_CONFIG", json_encode(D("Menu")->menu_json()));
        $this->display();
    }

    //缓存更新
    public function public_cache() {
        if (isset($_GET['type'])) {
            import("Dir");
            $Dir = new Dir();
            $cache = D('Cache');
            $type = I('get.type');
            set_time_limit(0);
            switch ($type) {
                case "site":
                    //开始刷新缓存
                    $stop = I('get.stop', 0, 'intval');
                    if (empty($stop)) {
                        try {
                            //已经清除过的目录
                            $dirList = explode(',', I('get.dir', ''));
                            //删除缓存目录下的文件
                            $Dir->del(RUNTIME_PATH);
                            //获取子目录
                            $subdir = glob(RUNTIME_PATH . '*', GLOB_ONLYDIR | GLOB_NOSORT);
                            if (is_array($subdir)) {
                                foreach ($subdir as $path) {
                                    $dirName = str_replace(RUNTIME_PATH, '', $path);
                                    //忽略目录
                                    if (in_array($dirName, array('Cache', 'Logs'))) {
                                        continue;
                                    }
                                    if (in_array($dirName, $dirList)) {
                                        continue;
                                    }
                                    $dirList[] = $dirName;
                                    //删除目录
                                    $Dir->delDir($path);
                                    //防止超时，清理一个从新跳转一次
                                    $this->assign("waitSecond", 200);
                                    $this->success("清理缓存目录[{$dirName}]成功！", U('Index/public_cache', array('type' => 'site', 'dir' => implode(',', $dirList))));
                                    exit;
                                }
                            }
                            //更新开启其他方式的缓存
                            Cache::getInstance()->clear();
                        } catch (Exception $exc) {
                            
                        }
                    }
                    if ($stop) {
                        $modules = $cache->getCacheList();
                        //需要更新的缓存信息
                        $cacheInfo = $modules[$stop - 1];
                        if ($cacheInfo) {
                            if ($cache->runUpdate($cacheInfo) !== false) {
                                $this->assign("waitSecond", 200);
                                $this->success($cacheInfo['name'], U('Index/public_cache', array('type' => 'site', 'stop' => $stop + 1)));
                                exit;
                            } else {
                                $this->error('缓存[' . $cacheInfo['name'] . ']更新失败！', U('Index/public_cache', array('type' => 'site', 'stop' => $stop + 1)));
                            }
                        } else {
                            $this->success('缓存更新完毕！', U('Index/public_cache'));
                            exit;
                        }
                    }
                    $this->success("即将更新站点缓存！", U('Index/public_cache', array('type' => 'site', 'stop' => 1)));
                    break;
                case "template":
                    //删除缓存目录下的文件
                    $Dir->del(RUNTIME_PATH);
                    $Dir->delDir(RUNTIME_PATH . "Cache/");
                    $Dir->delDir(RUNTIME_PATH . "Temp/");
                    //更新开启其他方式的缓存
                    Cache::getInstance()->clear();
                    $this->success("模板缓存清理成功！", U('Index/public_cache'));
                    break;
                case "logs":
                    $Dir->delDir(RUNTIME_PATH . "Logs/");
                    $this->success("站点日志清理成功！", U('Index/public_cache'));
                    break;
                default:
                    $this->error("请选择清楚缓存类型！");
                    break;
            }
        } else {
            $this->display("Index:cache");
        }
    }

    //后台框架首页菜单搜索
    public function public_find() {
        $keyword = I('get.keyword');
        if (!$keyword) {
            $this->error("请输入需要搜索的关键词！");
        }
        $where = array();
        $where['name'] = array("LIKE", "%$keyword%");
        $where['status'] = array("EQ", 1);
        $where['type'] = array("EQ", 1);
        $data = M("Menu")->where($where)->select();
        $menuData = $menuName = array();
        $Module = F("Module");
        foreach ($data as $k => $v) {
            $menuData[ucwords($v['app'])][] = $v;
            $menuName[ucwords($v['app'])] = $Module[ucwords($v['app'])]['name'];
        }
        $this->assign("menuData", $menuData);
        $this->assign("menuName", $menuName);
        $this->assign("keyword", $keyword);
        $this->display();
    }

}
