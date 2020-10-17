<?php

/**
 * 自定义列表模型
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class CustomlistModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('name', 'require', '自定义列表名称不能为空！'),
        array('name', '', '该自定义列表已经存在！', 0, 'unique', 1),
        array('title', 'require', '自定义列表页面标题不能为空！'),
        array('totalsql', 'require', '数据统计SQL不能为空！'),
        array('listsql', 'require', '数据查询SQL不能为空！'),
        array('lencord', 'require', '每页显示数量不能为空！'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
    );

    /**
     * 添加自定义列表
     * @param type $post 表单提交数据
     * @return boolean
     */
    public function addCustomlist($post) {
        if (empty($post)) {
            $this->error = '自定义列表名称不能为空！';
            return false;
        }
        //检查是否使用已有URL规则
        if ((int) $post['isurltype'] == 1) {
            //添加urlruleid自动验证规则
            array_push($this->_validate, array('urlruleid', 'require', 'URL规则不能为空！', 1, 'regex', 3));
        } else {
            //添加urlrule自动验证规则
            array_push($this->_validate, array('urlrule', 'require', 'URL规则不能为空！', 1, 'regex', 3));
        }
        //模板
        if (empty($post['listpath'])) {
            //添加template自动验证规则
            array_push($this->_validate, array('template', 'require', '模板内容不能为空！', 1, 'regex', 3));
        } else {
            //添加listpath自动验证规则
            array_push($this->_validate, array('listpath', 'require', '列表模板不能为空！', 1, 'regex', 3));
        }
        $data = $this->create($post, 1);
        if (!$data) {
            return false;
        }
        $id = $this->add($data);
        if ($id) {
            //更新访问地址
            $urlArray = $this->generateUrl($id);
            if ($urlArray !== false) {
                $this->where(array('id' => $id))->save(array('url' => $urlArray['url']));
            }
            return $id;
        } else {
            $this->error = '自定义列表添加失败！';
            return false;
        }
    }

    /**
     * 编辑自定义列表
     * @param type $post 表单提交数据
     * @return boolean
     */
    public function editCustomlist($post) {
        if (empty($post)) {
            $this->error = '自定义列表名称不能为空！';
            return false;
        }
        $id = $post['id'];
        //原本数据
        $info = $this->where(array('id' => $id))->find();
        if (empty($info)) {
            $this->error = '该自定义列表不存在！';
            return false;
        }
        unset($post['id']);
        //检查是否使用已有URL规则
        if ((int) $post['isurltype'] == 1) {
            //添加urlruleid自动验证规则
            array_push($this->_validate, array('urlruleid', 'require', 'URL规则不能为空！', 1, 'regex', 3));
            $post['urlrule'] = '';
        } else {
            //添加urlrule自动验证规则
            array_push($this->_validate, array('urlrule', 'require', 'URL规则不能为空！', 1, 'regex', 3));
            $post['urlruleid'] = 0;
        }
        //模板
        if (empty($post['listpath'])) {
            //添加template自动验证规则
            array_push($this->_validate, array('template', 'require', '模板内容不能为空！', 1, 'regex', 3));
            $post['listpath'] = '';
        } else {
            //添加listpath自动验证规则
            array_push($this->_validate, array('listpath', 'require', '列表模板不能为空！', 1, 'regex', 3));
            $post['template'] = '';
        }
        $data = $this->create($post, 2);
        if (!$data) {
            return false;
        }
        if ($this->where(array('id' => $id))->save($data) !== false) {
            //更新访问地址
            $urlArray = $this->generateUrl($id);
            if ($urlArray !== false) {
                $this->where(array('id' => $id))->save(array('url' => $urlArray['url']));
            }
            return true;
        } else {
            $this->error = '自定义列表修改失败！';
            return false;
        }
    }

    /**
     * 删除自定义列表
     * @param type $id 自定义列表ID
     * @return boolean
     */
    public function deleteCustomlist($id) {
        if (empty($id)) {
            $this->error = '请指定需要删除的自定义列表！';
            return false;
        }
        //查询出信息
        $info = $this->where(array('id' => $id))->find();
        if (empty($info)) {
            $this->error = '该自定义列表不存在！';
            return false;
        }
        //删除生成的静态文件
        //计算总数
        $countArray = $this->query($info['totalsql']);
        if (!empty($countArray)) {
            $count = $countArray[0]['total'];
            //分页总数
            $paging = ceil($count / $info['lencord']);
            for ($i = 1; $i <= $paging; $i++) {
                $customlistUrl = $this->generateUrl($id, $i, $info);
                if ($customlistUrl) {
                    //生成路径
                    $htmlpath = SITE_PATH . "/" . $customlistUrl["path"];
                    //删除
                    unlink($htmlpath);
                }
            }
        }

        if ($this->where(array('id' => $id))->delete() !== false) {
            return true;
        } else {
            $this->error = '删除失败！';
            return false;
        }
    }

    /**
     * 生成对应自定义列表URL
     * @param type $id 自定义列表ID
     * @param type $page 当前分页码
     * @return array Array
     * (
     *   [url] => http://news.abc.com/ 访问地址
     *   [path] => record/index.html 生成路径 动态木有
     *   [page] => Array 用于分页
     *  (
     *    [index] => http://news.abc.com/index_{$page}.html
     *    [list] => http://news.abc.com/index.html
     *   )
     *  )
     */
    public function generateUrl($id, $page = 1, $info = array()) {
        if (empty($id)) {
            $this->error = '请指定列表ID！';
            return false;
        }
        if (empty($info)) {
            //查询出自定义列表信息
            $info = $this->where(array('id' => $id))->find();
            if (empty($info)) {
                $this->error = '该自定义列表不存在！';
                return false;
            }
        }
        //页码
        $page = max(intval($page), 1);
        //取得规则
        if (empty($info['urlruleid'])) {
            $urlrule = $info['urlrule'];
        } else {
            $urlrules = F("urlrules");
            if (!$urlrules) {
                D("Urlrule")->public_cache_urlrule();
                $urlrules = F("urlrules");
            }
            $urlrule = $urlrules[$info['urlruleid']];
        }
        //如果规则为空
        if (empty($urlrule)) {
            $this->error = '该自定义列表URL规则为空！';
            return false;
        }

        //使用自定义函数生成规则
        if (substr($urlrule, 0, 1) == '=') {
            load("@.urlrule");
            $fun = str_replace(substr($urlrule, 0, 1), "", $urlrule);
            $urlrule = call_user_func_array(trim($fun), array(
                "id" => $id,
                "page" => $page,
            ));
        }

        $replace_l = array(); //需要替换的标签
        $replace_r = array(); //替换的内容
        //年份
        if (strstr($urlrule, '{$year}')) {
            $replace_l[] = '{$year}';
            $replace_r[] = date('Y', $info['createtime']);
        }
        //月份
        if (strstr($urlrule, '{$month}')) {
            $replace_l[] = '{$month}';
            $replace_r[] = date('m', $info['createtime']);
        }
        //日期
        if (strstr($urlrule, '{$day}')) {
            $replace_l[] = '{$day}';
            $replace_r[] = date('d', $info['createtime']);
        }
        $replace_l[] = '{$id}';
        $replace_r[] = $id;
        //标签替换
        $urlrule = str_replace($replace_l, $replace_r, $urlrule);

        $urlrule = explode("|", $urlrule);
        $url = array(
            "url" => ($page > 1 ? $urlrule[1] : $urlrule[0]),
            "path" => "",
        );

        //用于分页使用
        $url['page'] = array(
            "index" => $urlrule[0],
            "list" => $urlrule[1],
        );

        //如果绑定域名，分析真实的生成目录
        $parse_url = parse_url($url['url']);
        $url['path'] = "/" . str_replace(array("//", "\\"), '/', $parse_url['path']);

        //判断是否为首页文件，如果是，就不显示文件名，隐藏
        if (in_array(basename($url["url"]), array('index.html', 'index.htm', 'index.shtml'))) {
            $url["url"] = dirname($url["url"]) . '/';
        }

        //判断是否有加域名
        if (!isset($parse_url['host'])) {
            $url['url'] = CONFIG_SITEURL . $url['url'];
            $url['page']['index'] = CONFIG_SITEURL . $url['page']['index'];
            $url['page']['list'] = CONFIG_SITEURL . $url['page']['list'];
        }

        if (strpos($url["url"], '://') === false) {
            $url["url"] = str_replace('//', '/', $url["url"]);
        }

        $url["url"] = str_replace('{$page}', $page, $url["url"]);

        //把生成路径中的分页标签替换
        $url['path'] = str_replace('{$page}', $page, $url['path']);

        return $url;
    }

}
