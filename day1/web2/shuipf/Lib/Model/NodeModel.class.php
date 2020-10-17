<?php

/**
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */
class NodeModel extends CommonModel {
    
    //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
    protected  $_validate = array(
        array('title', 'require', '名称必须填写！'),
        array('pid', 'require', '所属上级权限没有填写！'),
        //array('name','','该项目/模块/方法已经存在！',0,'unique',1),
        array('name', 'require', '项目/模块/方法不能为空！'),
        array('status', array(0,1), '状态错误，状态只能是1或者0！',2,'in'),
        array('level', array(1,2,3), 'level等级错误，只能是1，2，3！',2,'in'),
    );
    
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected  $_auto = array(
        
    );
    
    /**
     * 读取全部node
     */
    public function node(){
        $data = $this->select();
        $da = array();
        foreach($data as $key => $value){
            $da[$value['id']] = $value;
        }
        return $da;
    }
    
    /**
     * 根据ID读取该ID下所属子节点树型数组
     */
    public function child($id=0,$data){
        if(!is_array($data)){
            return;
        }
        $do = array();
        foreach($data as $key=>$value){
            if($value['pid']==$id){
                $do[$value['id']] = $value;
                $do[$value['id']]["child"]=$this->child($value['id'],$data);
            }
        }
        return $do;
    }
}
?>