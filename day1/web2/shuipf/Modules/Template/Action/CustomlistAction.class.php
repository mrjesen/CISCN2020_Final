<?php

/**
 * 自定义列表
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CustomlistAction extends AdminbaseAction {

    private $db = NULL;

    //初始
    protected function _initialize() {
        parent::_initialize();
        $this->db = D('Template/Customlist');
    }

    //列表首页
    public function index() {
        $where = array();
        $count = $this->db->where($where)->count();
        $page = $this->page($count, 20);
        $data = $this->db->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "desc"))->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign('data', $data);
        $this->display();
    }

    //添加列表
    public function add() {
        if (IS_POST) {
            if ($this->db->addCustomlist($_POST)) {
                $this->success('添加成功！', U('index'));
            } else {
                $error = $this->db->getError();
                $this->error($error ? $error : '自定义列表添加失败！');
            }
        } else {
            $this->templateAndRule();
            $this->display();
        }
    }

    //编辑
    public function edit() {
        if (IS_POST) {
            if ($this->db->editCustomlist($_POST)) {
                $this->success('修改成功！', U('index'));
            } else {
                $error = $this->db->getError();
                $this->error($error ? $error : '自定义列表修改失败！');
            }
        } else {
            $id = I('get.id', 0, 'intval');
            $info = $this->db->where(array('id' => $id))->find();
            if (empty($info)) {
                $this->error('该自定义列表不存在！');
            }

            $this->templateAndRule($info);
            $this->assign('info', $info);
            $this->display();
        }
    }

    //删除
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if ($this->db->deleteCustomlist($id)) {
            $this->success('删除成功！');
        } else {
            $error = $this->db->getError();
            $this->error($error ? $error : '删除失败！');
        }
    }

    //生成列表
    public function generate() {
        if (IS_POST) {
            $ids = I('post.ids');
            if (empty($ids)) {
                $this->error('请指定需要生成的自定义列表！');
            }
            foreach ($ids as $id) {
                if ($this->generateHtml($id) == false) {
                    $this->error('生成失败！');
                }
            }
            $this->success('生成成功！');
        } else {
            $id = I('get.id', 0, 'intval');
            if (empty($id)) {
                $this->error('请指定需要生成的自定义列表！');
            }
            if ($this->generateHtml($id)) {
                $this->success('生成成功！');
            } else {
                $this->error('生成失败！');
            }
        }
    }

    /**
     * 生成自定义列表
     * @param type $id ID
     * @return boolean
     */
    private function generateHtml($id) {
        define('HTML', true);
        define('GROUP_MODULE', 'Contents');
        C('HTML_FILE_SUFFIX', "");
        //查询出自定义列表信息
        $info = $this->db->where(array('id' => $id))->find();
        if (empty($info)) {
            return false;
        }
        //计算总数
        $countArray = $this->db->query($info['totalsql']);
        if (!empty($countArray)) {
            $count = $countArray[0]['total'];
        } else {
            return false;
        }
        //分页总数
        $paging = ceil($count / $info['lencord']);
        import('Url');
        $pagehao = 1;
        do {
            //生成路径
            $customlistUrl = $this->db->generateUrl($id, $pagehao, $info);
            if ($customlistUrl == false) {
                return false;
            }
            //取得URL规则
            $urls = $customlistUrl['page'];
            $page = page($count, $info['lencord'], $pagehao, array(
                'isrule' => true,
                'rule' => $urls,
            ));
            $data = $this->db->query($info['listsql'] . " LIMIT {$page->firstRow},{$page->listRows}");
            //把分页分配到模板
            $this->assign(C("VAR_PAGE"), $pagehao);
            //seo分配到模板
            $seo = seo(0, $info['title'], $info['description'], $info['keywords']);
            $this->assign("SEO", $seo);
            $this->assign('listData', $data);
            $this->assign("pages", $page->show('Admin'));

            if (empty($info['listpath'])) {
                //生成路径
                $htmlpath = SITE_PATH . "/" . $customlistUrl["path"];
                // 页面缓存
                ob_start();
                ob_implicit_flush(0);
                //渲染模板
                $this->show($info['template']);
                // 获取并清空缓存
                $content = ob_get_clean();
                //检查目录是否存在
                if (!is_dir(dirname($htmlpath))) {
                    // 如果静态目录不存在 则创建
                    mkdir(dirname($htmlpath), 0777, true);
                }
                //写入文件
                if (false === file_put_contents($htmlpath, $content)) {
                    throw_exception("自定义列表生成失败：" . $htmlpath);
                }
            } else {
                //去除后缀开始
                $tpar = explode(".", "List:{$info['listpath']}", 2);
                //去除完后缀的模板
                $template = $tpar[0];
                unset($tpar);
                //模板检测
                $template = parseTemplateFile($template);
                //生成
                $this->buildHtml($customlistUrl["path"], SITE_PATH . "/", $template);
            }

            $pagehao++;
        } while ($pagehao <= $paging);

        return true;
    }

    /**
     * 初始模板和URL规则信息
     * @param type $info
     */
    private function templateAndRule($info = array('urlruleid' => '')) {
        $filepath = TEMPLATE_PATH . (empty(AppframeAction::$Cache["Config"]['theme']) ? "Default" : AppframeAction::$Cache["Config"]['theme']) . DIRECTORY_SEPARATOR . "Contents" . DIRECTORY_SEPARATOR;
        $tp_list = str_replace($filepath . "List" . DIRECTORY_SEPARATOR, "", glob($filepath . "List" . DIRECTORY_SEPARATOR . 'list*'));

        $this->assign('list_html_ruleid', Form::urlrule('content', 'category', 1, $info['urlruleid'], 'name="urlruleid"'));
        $this->assign('tp_list', $tp_list);
    }

}
