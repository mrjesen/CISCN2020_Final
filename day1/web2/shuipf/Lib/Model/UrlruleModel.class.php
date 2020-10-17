<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class UrlruleModel extends CommonModel {

    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected $_validate = array(
        array('file', 'require', 'URL规则名称必须填写！'),
        array('module', 'require', '模块名称必须填写！'),
        array('ishtml', 'require', '是否生成静态必须填写！'),
        array('example', 'require', 'URL示例必须填写！'),
        array('urlrule', 'require', 'URL规则必须填写！'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
    );

    /**
     * 更新URL规则 缓存
     */
    public function public_cache_urlrule() {
        $datas = $this->select();
        //完整数据缓存
        $data = array();
        //只有规则缓存
        $basic_data = array();
        foreach ($datas as $roleid => $r) {
            $data[$r['urlruleid']] = $r;
            $basic_data[$r['urlruleid']] = $r['urlrule'];
        }
        F("urlrules_detail", $data);
        F("urlrules", $basic_data);
        unset($data, $datas, $basic_data);
        return $basic_data;
    }

    /**
     * 后台有更新则删除缓存
     * @param type $data
     */
    public function _before_write($data) {
        parent::_before_write($data);
        F("urlrules_detail", NULL);
        F("urlrules", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->public_cache_urlrule();
    }
    
    //更新数据后更新缓存
    public function _after_update($data, $options) {
        parent::_after_update($data, $options);
        $this->public_cache_urlrule();
    }

    //插入数据后更新缓存
    public function _after_insert($data, $options) {
        parent::_after_insert($data, $options);
        $this->public_cache_urlrule();
    }
}

?>
