<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP Behavior基础类
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author liu21st <liu21st@gmail.com>
 */
abstract class Behavior {

    // 行为参数 和配置参数设置相同
    protected $options = array();
    //当前行为所属模块
    protected $groupName = '';
    // 使用的模板引擎 每个行为可以单独配置不受系统影响
    protected $template = '';

    /**
     * 模板输出变量
     * @var tVar
     * @access protected
     */
    protected $tVar = array();

    /**
     * 架构函数
     * @access public
     */
    public function __construct() {
        if (!empty($this->options)) {
            foreach ($this->options as $name => $val) {
                if (NULL !== C($name)) { // 参数已设置 则覆盖行为参数
                    $this->options[$name] = C($name);
                } else { // 参数未设置 则传入默认值到配置
                    C($name, $val);
                }
            }
            array_change_key_case($this->options);
        }
    }

    // 获取行为参数
    public function __get($name) {
        return $this->options[strtolower($name)];
    }

    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function assign($name, $value = '') {
        if (is_array($name)) {
            $this->tVar = array_merge($this->tVar, $name);
        } else {
            $this->tVar[$name] = $value;
        }
    }

    /**
     * 渲染模板输出 供render方法内部调用
     * @access public
     * @param string $templateFile  模板文件
     * @return string
     */
    protected function renderFile($templateFile = '') {
        if (!file_exists_case($templateFile)) {
            // 自动定位模板文件
            $name = substr(get_class($this), 0, -8);
            //获取模板文件名称
            $filename = empty($templateFile) ? $name : $templateFile;
            $templateFile = APP_PATH . C('APP_GROUP_PATH') . '/' . $this->groupName . '/Behavior/' . $name . '/' . $filename . C('TMPL_TEMPLATE_SUFFIX');
            if (!file_exists_case($templateFile))
                throw_exception(L('_TEMPLATE_NOT_EXIST_') . '[' . $templateFile . ']');
        }
        ob_start();
        ob_implicit_flush(0);
        $template = strtolower($this->template ? $this->template : (C('TMPL_ENGINE_TYPE') ? C('TMPL_ENGINE_TYPE') : 'php'));
        if ('php' == $template) {
            // 使用PHP模板
            if (!empty($this->tVar))
                extract($this->tVar, EXTR_OVERWRITE);
            // 直接载入PHP模板
            include $templateFile;
        }elseif ('think' == $template) { // 采用Think模板引擎
            if ($this->checkCache($templateFile)) { // 缓存有效
                // 分解变量并载入模板缓存
                extract($this->tVar, EXTR_OVERWRITE);
                //载入模版缓存文件
                include C('CACHE_PATH') . md5($templateFile) . C('TMPL_CACHFILE_SUFFIX');
            } else {
                //如果取不到相关配置，尝试加载下ParseTemplate行为
                if (!C('TMPL_L_DELIM')) {
                    B('ParseTemplate');
                }
                $tpl = Think::instance('ThinkTemplate');
                // 编译并加载模板文件
                $tpl->fetch($templateFile, $this->tVar);
            }
        } else {
            $class = 'Template' . ucwords($template);
            if (is_file(CORE_PATH . 'Driver/Template/' . $class . '.class.php')) {
                // 内置驱动
                $path = CORE_PATH;
            } else { // 扩展驱动
                $path = EXTEND_PATH;
            }
            require_cache($path . 'Driver/Template/' . $class . '.class.php');
            $tpl = new $class;
            $tpl->fetch($templateFile, $this->tVar);
        }
        $content = ob_get_clean();
        return $content;
    }

    /**
     * 检查缓存文件是否有效
     * 如果无效则需要重新编译
     * @access public
     * @param string $tmplTemplateFile  模板文件名
     * @return boolen
     */
    protected function checkCache($tmplTemplateFile) {
        if (!C('TMPL_CACHE_ON')) // 优先对配置设定检测
            return false;
        $tmplCacheFile = C('CACHE_PATH') . md5($tmplTemplateFile) . C('TMPL_CACHFILE_SUFFIX');
        if (!is_file($tmplCacheFile)) {
            return false;
        } elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            // 模板文件如果有更新则缓存需要更新
            return false;
        } elseif (C('TMPL_CACHE_TIME') != 0 && time() > filemtime($tmplCacheFile) + C('TMPL_CACHE_TIME')) {
            // 缓存是否在有效期
            return false;
        }
        // 缓存有效
        return true;
    }
    
     /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @return void
     */
    protected function display($templateFile = '') {
        echo $this->renderFile($templateFile);
    }

    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params  行为参数
     * @return void
     */
    abstract public function run(&$params);
}