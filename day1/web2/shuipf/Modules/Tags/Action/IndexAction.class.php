<?php

/**
 * TAGS
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class IndexAction extends BaseAction {

    //URL规则
    protected $urlRules = array();
    protected $db = NULL;

    function _initialize() {
        //手动指定模块
        define("GROUP_MODULE", "Contents");
        parent::_initialize();
        $this->urlRules = F('urlrules');
        $this->db = M('Tags');
    }

    //显示tags或者首页
    public function index() {
        //tagid
        $tagid = I('get.tagid', 0, 'intval');
        //tagname
        $tag = I('get.tag', '', 'trim');
        $where = array();
        if (!empty($tagid)) {
            $where['tagid'] = array("EQ", $tagid);
        } else if (!empty($tag)) {
            $where['tag'] = array("EQ", $tag);
        }
        //如果条件为空，则显示标签首页
        if (empty($where)) {
            $key = 'Tags_Index_index';
            $dataCache = S($key);
            if (empty($dataCache)) {
                $data = $this->db->order(array('hits' => 'DESC'))->select();
                if (!empty($data)) {
                    //查询每个tag最新的一条数据
                    $tagsContent = M('tagsContent');
                    foreach ($data as $k => $r) {
                        $data[$k]['info'] = $tagsContent->where(array('tag' => $r['tag']))->order(array('updatetime' => 'DESC'))->find();
                    }
                    //进行缓存
                    S($key, $data, 3600);
                }
            }else{
                $data = $dataCache;
            }
            $SEO = seo('', '标签');
            //seo分配到模板
            $this->assign("SEO", $SEO);
            $this->assign('list', $data);
            $this->display("Tags:index");
            return true;
        }
        //分页号
        $page = isset($_GET[C("VAR_PAGE")]) ? $_GET[C("VAR_PAGE")] : 1;
        //根据条件获取tag信息
        $info = $this->db->where($where)->find();
        if (empty($info)) {
            $this->error('抱歉，沒有找到您需要的内容！');
        }
        //访问数+1
        $this->db->where($where)->setInc("hits");
        //更新最后访问时间
        $this->db->where($where)->save(array("lasthittime" => time()));
        $this->assign($data);
        //取得tag分页规则
        $urlrules = $this->urlRules[CONFIG_TAGURL];
        if (empty($urlrules)) {
            $urlrules = 'index.php?g=Tags&tagid={$tagid}|index.php?g=Tags&tagid={$tagid}&page={$page}';
        }
        $GLOBALS['URLRULE'] = str_replace('|', '~', str_replace(array('{$tag}', '{$tagid}'), array($info['tag'], $info['tagid']), $urlrules));
        $SEO = seo();
        //seo分配到模板
        $this->assign("SEO", $SEO);
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        $this->assign($info);
        $this->display("Tags:tag");
    }

}