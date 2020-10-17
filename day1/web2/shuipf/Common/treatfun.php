<?php
/* * 
 * 自定义处理函数
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

/**
 * 自定义处理函数示例函数
 * @param type $modelid 模型ID
 * @param type $catid 栏目ID
 * @param type $id 信息ID，入库前是0，入库后是信息ID
 * @param type $value 字段内容
 * @param type $field 字段名
 * @param type $action 操作类型，add 增加； edit 编辑
 * @param type $param 附带参数
 * @return type  返回字段内容
 */
function backstagefun($modelid, $catid ,$id ,$value , $field ,$action ,$param){
    return $value."这是自定义函数处理功能附加文字！";
}
?>
