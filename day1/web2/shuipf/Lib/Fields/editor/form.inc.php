<?php

/**
 * 编辑器字段 表单组合处理
 * @param type $field 字段名
 * @param type $value 字段内容
 * @param type $fieldinfo 字段配置
 * @return type
 */
function editor($field, $value, $fieldinfo) {
    $setting = unserialize($fieldinfo['setting']);
    //是否禁用分页和子标题 基本没用。。。
    $disabled_page = isset($disabled_page) ? $disabled_page : 0;
    //编辑器高度
    $height = $setting['height'];
    if (empty($setting['height'])) {
        $height = 300;
    }

    if (defined('IN_ADMIN') && IN_ADMIN) {
        //是否允许上传
        $allowupload = 1;
        //编辑器类型，简洁型还是标准型
        $toolbar = $setting['toolbar'];
    } else {
        $Member_group = F("Member_group");
        //获取当前登陆会员组id
        $groupid = SiteCookie('groupid');
        //是否允许上传
        $allowupload = $Member_group[$groupid]['allowattachment'] ? 1 : 0;
        //编辑器类型，简洁型还是标准型
        $toolbar = $setting['mbtoolbar'] ? $setting['mbtoolbar'] : "basic";
    }

    //内容
    if (empty($value)) {
        $value = $setting['defaultvalue'] ? $setting['defaultvalue'] : '<p></p>';
    }
    if ($setting['minlength'] || $fieldinfo['pattern']){
        $allow_empty = '';
    }
    //模块
    $module = ( in_array(GROUP_NAME, array("Contents", "contents")) ) ? 'Contents' : GROUP_NAME;
    $form = Form::editor($field, $toolbar, $module, $this->catid, $allowupload, $allowupload, '', 10, $height, $disabled_page);
    //javascript
    $this->formJavascript .= "
            //编辑器
            //editor{$field} = new baidu.editor.ui.Editor(editor_config_{$field});
            //editor{$field}.render( '{$field}' );
            //增加编辑器验证规则
            jQuery.validator.addMethod('editor{$field}',function(){
                try{editor{$field}.sync();}catch(err){};
                return " . ($fieldinfo['minlength'] ? "editor{$field}.hasContents();" : "true") . "
            });
    ";
    //错误提示
    $errortips = $this->fields[$field]['errortips'];
    //20130428 由于没有设置必须输入时，ajax提交会造成获取不到编辑器的值。所以这里强制进行验证，使其触发编辑器的sync()方法
    // if ($minlength){
    //验证规则
    $this->formValidateRules['info[' . $field . ']'] = array("editor$field" => "true");
    //验证不通过提示
    $this->formValidateMessages['info[' . $field . ']'] = array("editor$field" => $errortips ? $errortips : $fieldinfo['name'] . "不能为空！");
    // }
    return "<div id='{$field}_tip'></div>" . '<script type="text/plain" id="' . $field . '" name="info[' . $field . ']">' . $value . '</script>' . $form;
}