<?php

/**
 * 内容模块页面生成
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class Html extends BaseAction {

    private $url;

    public function _initialize() {
        //关闭由于启用域名绑定造成的前台域名出错
        define("APP_SUB_DOMAIN_NO", 1);
        parent::_initialize();
        import('Url');
        $this->url = get_instance_of('Url');
        define('HTML', true);
        C('HTML_FILE_SUFFIX', "");
    }

    /**
     * 生成内容页
     * @param  $data 数据
     * @param  $array_merge 是否合并
     * @param  $action 方法
     */
    public function show($data = '', $array_merge = 1, $action = 'add') {
        if (!$data['inputtime'] || !$data['id'] || !$data['catid']) {
            return false;
        }
        //初始化一些模板分配变量
        $this->assignInitialize();
        //取得信息ID
        $id = $data['id'];
        //栏目ID
        $catid = $data['catid'];
        //获取当前栏目数据
        $category = getCategory($catid);
        //模型ID
        $this->modelid = $category['modelid'];
        //检查数据是否合并
        if (!$array_merge) {
            ContentModel::getInstance($this->modelid)->dataMerger($data);
        }
        //分页方式
        if (isset($data['paginationtype'])) {
            //分页方式 
            $paginationtype = $data['paginationtype'];
            //自动分页字符数
            $maxcharperpage = (int) $data['maxcharperpage'];
        } else {
            //默认不分页
            $paginationtype = 0;
        }
        //载入字段数据处理类
        require_cache(RUNTIME_PATH . 'content_output.class.php');
        //tag
        tag('html_shwo_buildhtml', $data);
        $content_output = new content_output($this->modelid);
        //获取字段类型处理以后的数据
        $output_data = $content_output->get($data);
        $output_data['id'] = $id;
        $output_data['title'] = strip_tags($output_data['title']);
        //SEO
        $seo_keywords = '';
        if (!empty($output_data['keywords'])) {
            $seo_keywords = implode(',', $output_data['keywords']);
        }
        $seo = seo($catid, $output_data['title'], $output_data['description'], $seo_keywords);

        //内容页模板
        $template = $output_data['template'] ? $output_data['template'] : $category['setting']['show_template'];
        //去除模板文件后缀
        $newstempid = explode(".", $template);
        $template = $newstempid[0];
        unset($newstempid);
        //检测模板是否存在、不存在使用默认！
        $tempstatus = parseTemplateFile("Show:" . $template);
        if ($tempstatus == false && $template != "show") {
            //模板不存在，重新使用默认模板
            $template = "show";
            $tempstatus = parseTemplateFile("Show:" . $template);
            if ($tempstatus == false) {
                return false;
            }
        } else if ($tempstatus == false) {
            return false;
        }

        //分页处理
        $pages = $titles = '';
        //分页方式 0不分页 1自动分页 2手动分页
        if ($data['paginationtype'] == 1) {
            //自动分页
            if ($maxcharperpage < 10) {
                $maxcharperpage = 500;
            }
            //按字数分割成几页处理开始
            import('Contentpage', APP_PATH . C("APP_GROUP_PATH") . '/Contents/ORG');
            $contentpage = new Contentpage();
            $contentfy = $contentpage->get_data($output_data['content'], $maxcharperpage);
            //自动分页有时会造成返回空，如果返回空，就不分页了
            if (!empty($contentfy)) {
                $output_data['content'] = $contentfy;
            }
        }

        //分配解析后的文章数据到模板 
        $this->assign($output_data);
        //seo分配到模板
        $this->assign("SEO", $seo);
        //栏目ID
        $this->assign("catid", $catid);

        //分页生成处理
        //分页方式 0不分页 1自动分页 2手动分页
        if ($data['paginationtype'] > 0) {
            //手动分页
            $CONTENT_POS = strpos($output_data['content'], '[page]');
            if ($CONTENT_POS !== false) {
                $contents = array_filter(explode('[page]', $output_data['content']));
                $pagenumber = count($contents);
                for ($i = 1; $i <= $pagenumber; $i++) {
                    //URL地址处理
                    $urlrules = $this->url->show($data, $i);
                    //用于分页导航
                    if (!isset($pageurl['index'])) {
                        $pageurl['index'] = $urlrules['page']['index'];
                        $pageurl['list'] = $urlrules['page']['list'];
                    }
                    $pageurls[$i] = $urlrules;
                }
                $pages = "";
                //生成分页
                foreach ($pageurls as $page => $urls) {
                    //$pagenumber 分页总数
                    $_GET[C("VAR_PAGE")] = $page;
                    $pages = page($pagenumber, 1, $page, array(
                        'isrule' => true,
                        'rule' => $pageurl,
                            ))->show("default");
                    //判断[page]出现的位置是否在第一位 
                    if ($CONTENT_POS < 7) {
                        $content = $contents[$page];
                    } else {
                        $content = $contents[$page - 1];
                    }
                    //分页
                    $this->assign("pages", $pages);
                    $this->assign("content", $content);
                    $this->buildHtml($urls['path'], SITE_PATH . "/", $tempstatus);
                }
                return true;
            }
        }
        //对pages进行赋值null，解决由于上一篇有分页下一篇无分页的时候，会把上一篇的分页带到下一篇！
        $this->assign("pages", null);
        $this->assign("content", $output_data['content']);
        //当没有启用内容页分页时候（如果内容字段有启用分页，不会执行到此步骤），判断其他支持分页的标签进行分页处理
        unset($GLOBALS["Total_Pages"]);
        $page = 1;
        $j = 1;
        //开始生成列表
        do {
            $this->assign(C("VAR_PAGE"), $page);
            //生成路径
            $category_url = $this->url->show($data, $page);
            $GLOBALS['URLRULE'] = implode("~", $category_url['page']);
            //生成
            $this->buildHtml($category_url["path"], SITE_PATH . "/", $tempstatus);
            $page++;
            $j++;
            $total_number = isset($_GET['total_number']) ? (int) $_GET['total_number'] : (int) $GLOBALS["Total_Pages"];
        } while ($j <= $total_number);
        return true;
    }

    /**
     * 根据页码生成栏目
     * @param $catid 栏目id
     * @param $page 当前页数
     */
    public function category($catid, $page = 1) {
        if (!$catid) {
            return false;
        }
        //获取栏目数据
        $category = getCategory($catid);
        if (empty($category)) {
            return false;
        }
        //栏目扩展配置信息
        $setting = $category['setting'];
        //检查是否生成列表
        if (!$category['sethtml']) {
            return true;
        }
        //初始化一些模板分配变量
        $this->assignInitialize();
        //生成静态分页数
        $repagenum = (int) $setting['repagenum'];
        if ($repagenum && !$GLOBALS['dynamicRules']) {
            //设置动态访问规则给page分页使用
            $GLOBALS['Rule_Static_Size'] = $repagenum;
            $GLOBALS['dynamicRules'] = CONFIG_SITEURL_MODEL . "index.php?a=lists&catid={$catid}&page=*";
        }
        if ($repagenum && $page > $repagenum) {
            unset($GLOBALS['dynamicRules']);
            return true;
        }
        //分页
        $page = intval($page);
        //父目录
        $parentdir = $category['parentdir'];
        //目录
        $catdir = $category['catdir'];
        //生成路径
        $category_url = $this->url->category_url($catid, $page);
        //取得URL规则
        $urls = $category_url['page'];

        //生成类型为0的栏目
        if ($category['type'] == 0) {
            //栏目首页模板
            $template = $setting['category_template'] ? $setting['category_template'] : 'category';
            //栏目列表页模板
            $template_list = $setting['list_template'] ? $setting['list_template'] : 'list';
            //判断使用模板类型，如果有子栏目使用频道页模板，终极栏目使用的是列表模板
            $template = $category['child'] ? "Category:" . $template : "List:" . $template_list;
            //去除后缀开始
            $tpar = explode(".", $template, 2);
            //去除完后缀的模板
            $template = $tpar[0];
            unset($tpar);
            //模板检测
            $template = parseTemplateFile($template);
            $GLOBALS['URLRULE'] = $urls;
        } else if ($category['type'] == 1) {//单页
            $db = D('Page');
            $template = $setting['page_template'] ? $setting['page_template'] : 'page';
            //判断使用模板类型，如果有子栏目使用频道页模板，终极栏目使用的是列表模板
            $template = "Page:" . $template;
            //去除后缀开始
            $tpar = explode(".", $template, 2);
            //去除完后缀的模板
            $template = $tpar[0];
            unset($tpar);
            $GLOBALS['URLRULE'] = $urls;
            $info = $db->getPage($catid);
            $this->assign($category['setting']['extend']);
            $this->assign($info);
        }
        //把分页分配到模板
        $this->assign(C("VAR_PAGE"), $page);
        //分配变量到模板 
        $this->assign($category);
        //seo分配到模板
        $seo = seo($catid, $setting['meta_title'], $setting['meta_description'], $setting['meta_keywords']);
        $this->assign("SEO", $seo);
        //生成
        $this->buildHtml($category_url["path"], SITE_PATH . "/", $template);
    }

    /**
     * 生成栏目列表
     * @param $catid 栏目id
     */
    public function HtmlCategory($catid) {
        $page = 1;
        $j = 1;
        //开始生成列表
        unset($GLOBALS["Total_Pages"]);
        do {
            $this->category($catid, $page);
            $page++;
            $j++;
            //如果GET有total_number参数则直接使用GET的，如果没有则根据$GLOBALS["Total_Pages"]获取分页总数
            $total_number = isset($_GET['total_number']) ? (int) $_GET['total_number'] : $GLOBALS["Total_Pages"];
        } while ($j <= $total_number);

        return true;
    }

    /**
     * 更新首页
     * @param $page 页码，默认1
     */
    public function index($page = 1) {
        $page = max($page, 1);
        if (CONFIG_GENERATE == '0' || CONFIG_GENERATE < 1) {
            return false;
        }
        //初始化一些模板分配变量
        $this->assignInitialize();
        //模板处理
        $tp = explode(".", CONFIG_INDEXTP);
        $template = parseTemplateFile("Index:" . $tp[0]);
        if ($template == false && $tp[0] != "index") {
            //模板不存在，重新使用默认模板
            $template = "index";
            $template = parseTemplateFile("Index:" . $template);
            if ($template == false) {
                $this->error("首页模板不存在！");
            }
        } else if ($template == false) {
            $this->error("首页模板不存在！");
        }

        $SEO = seo("", "", AppframeAction::$Cache['Config']['siteinfo'], AppframeAction::$Cache['Config']['sitekeywords']);
        unset($GLOBALS["Total_Pages"]);
        $j = 1;
        //分页生成
        do {
            //把分页分配到模板
            $this->assign(C("VAR_PAGE"), $page);
            //seo分配到模板
            $this->assign("SEO", $SEO);
            //生成路径
            $urls = $this->url->index($page);
            $GLOBALS['URLRULE'] = $urls['page'];
            $filename = $urls['path'];
            //判断是否生成和入口文件同名，如果是，不生成！
            if ($filename != "/index.php") {
                $this->buildHtml($filename, SITE_PATH . "/", $template);
            }
            //如果GET有total_number参数则直接使用GET的，如果没有则根据$GLOBALS["Total_Pages"]获取分页总数
            $total_number = isset($_GET['total_number']) ? (int) $_GET['total_number'] : $GLOBALS["Total_Pages"];
            $page++;
            $j++;
        } while ($j <= $total_number);
    }

    /**
     * 生成相关栏目列表
     * @param $catid
     */
    public function create_relation_html($catid) {
        unset($GLOBALS["Total_Pages"]);
        $page = 1;
        $j = 1;
        //开始生成列表
        do {
            $this->category($catid, $page);
            $page++;
            $j++;
            //如果GET有total_number参数则直接使用GET的，如果没有则根据$GLOBALS["Total_Pages"]获取分页总数
            $total_number = isset($_GET['total_number']) ? (int) $_GET['total_number'] : $GLOBALS["Total_Pages"];
        } while ($j <= $total_number && $j < 7);
        //检查当前栏目的父栏目，如果存在则生成
        $arrparentid = getCategory($catid, 'arrparentid');
        if ($arrparentid) {
            $arrparentid = explode(',', $arrparentid);
            foreach ($arrparentid as $catid) {
                if ($catid)
                    $this->category($catid, 1);
            }
        }
    }

    /**
     * 生成自定义页面 
     * @param $temptext 模板内容
     * @param $data 数据
     */
    public function createhtml($temptext, $data) {
        if (!$temptext || !is_array($data)) {
            return false;
        }
        //初始化一些模板分配变量
        $this->assignInitialize();
        //生成文件名，包含后缀
        $filename = $data['tempname'];
        //生成路径
        $htmlpath = SITE_PATH . $data['temppath'] . $filename;
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        parent::show($temptext);
        // 获取并清空缓存
        $content = ob_get_clean();
        //检查目录是否存在
        if (!is_dir(dirname($htmlpath))) {
            // 如果静态目录不存在 则创建
            mkdir(dirname($htmlpath), 0777, true);
        }
        //写入文件
        if (false === file_put_contents($htmlpath, $content)) {
            throw_exception("自定义页面生成失败：" . $htmlpath);
        }
        return true;
    }

    /**
     * 另类的销毁分配给模板的变量
     * 防止生成不同类型的页面，造成参数乱窜！
     */
    protected function assignInitialize() {
        //栏目ID
        $this->assign('catid', NULL);
        //分页号
        $this->assign(C("VAR_PAGE"), NULL);
        //seo分配到模板
        $this->assign("SEO", NULL);
        $this->assign('content', NULL);
        $this->assign('pages', NULL);
    }

}
