<?php

/**
 * Rss
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class RssAction extends BaseAction {

    public function index() {
        $where = array();
        $catid = I('get.catid', 0, 'intval');
        $rssid = I('get.rssid', 0, 'intval');
        if ($rssid) {
            header("Content-Type: text/xml; charset=" . C("DEFAULT_CHARSET"));
            //检测缓存
            $data = S("Rss_$rssid");
            if ($data) {
                echo $data;
                exit;
            }
            $Cat = getCategory($rssid);
            //检查栏目是否存在
            if (empty($Cat)) {
                $this->error('该栏目不存在！');
            }
            //检查栏目类型
            if ($Cat['type'] != 0) {
                $this->error('栏目类型不正确！');
            }
            $where['status'] = array("EQ", 99);
            //判断是否有子栏目
            if (getCategory($rssid, 'child')) {
                $where['catid'] = array("IN", getCategory($rssid, 'arrchildid'));
            } else {
                $where['catid'] = array("EQ", $rssid);
            }
            //模型ID
            $modelid = getCategory($rssid, 'modelid');
            //获取表名
            $tablename = ucwords(getModel($modelid, 'tablename'));
            if (empty($tablename)) {
                $this->error('出现错误！');
            }
            //栏目配置
            $setting = getCategory($rssid, 'setting');
            $data = M($tablename)->where($where)->order(array("updatetime" => "DESC", "id" => "DESC"))->limit(50)->select();
            import('@.ORG.Rss');
            $Rss = new Rss($this->XMLstr(getCategory($rssid, 'catname') . ' - ' . CONFIG_SITENAME), $this->XMLstr(getCategory($rssid, 'url')), $this->XMLstr(getCategory($rssid, 'description')), $this->XMLstr(getCategory($rssid, 'image')));
            foreach ($data as $k => $v) {
                $v = $this->XMLstr($v);
                $Rss->AddItem($v['title'], $v['url'], $v['description'], date("Y-m-d H:i:s A", $v['updatetime']));
            }
            //进行缓存
            S("Rss_$rssid", $Rss->Fetch(), 900);
            $Rss->Display();
        }

        $this->assign('catid', $catid);
        $this->assign('rssid', $rssid);
        $this->assign("SEO", seo(0, 'Rss订阅中心'));
        $this->display();
    }

    /**
     * 字符转义 
     */
    protected function XMLstr($dara) {
        if (is_array($dara)) {
            $XMLstr = array();
            foreach ($dara as $k => $value) {
                $XMLstr[$k] = $this->XMLstr($value);
            }
            return $XMLstr;
        } else {
            $dara = htmlspecialchars($dara, ENT_QUOTES);
            return $dara;
        }
    }

}
