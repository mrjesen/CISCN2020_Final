<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body>
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="table_list">
  <form name="myform" action="{:U("Rbac/listorders")}" method="post">
    <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <td width="20">ID</td>
          <td width="200"  align="left" >角色名称</td>
          <td align="left" >角色描述</td>
          <td width="50"  align="left" >状态</td>
          <td width="300">管理操作</td>
        </tr>
      </thead>
      <tbody>
        <foreach name="data" item="vo">
        <tr>
          <td width="10%" align="center">{$vo.id}</td>
          <td width="15%"  >{$vo.name}</td>
          <td >{$vo.remark}</td>
          <td width="5%">
          <if condition="$vo['status'] eq 1"> 
          <font color="red">√</font>
          <else />
          <font color="red">╳</font>
          </if>
          </td>
          <td  class="text-c">
          <if condition="$vo['id'] eq 1"> 
          <font color="#cccccc">权限设置</font> | <font color="#cccccc">栏目权限</font> | <a href="{:U('Management/manager',array('role_id'=>$vo['id']))}">成员管理</a> | <font color="#cccccc">修改</font> | <font color="#cccccc">删除</font>
          <else />
          <a href="{:U("Rbac/authorize",array("id"=>$vo["id"]))}">权限设置</a> | <a href="{:U("Rbac/setting_cat_priv",array("roleid"=>$vo["id"]))}">栏目权限</a> |<a href="{:U('Management/manager',array('role_id'=>$vo['id']))}">成员管理</a> | <a href="{:U('Rbac/roleedit',array('id'=>$vo['id']))}">修改</a> | <a class="J_ajax_del" href="{:U('Rbac/roledelete',array('id'=>$vo['id']))}">删除</a>
          </if>
          </td>
        </tr>
        </foreach>
      </tbody>
    </table>
  </form>
  </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>