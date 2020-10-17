<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="table_list">
    <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <td width="10%" align="center">排序</td>
          <td width="5%"  align="center">ID</td>
          <td width="15%" align="center">推荐位名称</td>
          <td width="15%" align="center">所属栏目</td>
          <td width="15%" align="center">所属模型</td>
          <td width="20%" align="center">管理操作</td>
        </tr>
      </thead>
      <tbody>
        <volist name="data" id="vo">
          <tr>
            <td align="center"><input name='listorders[{$vo.posid}]' type='text' size='2' value='{$vo.listorder}' class="input"></td>
            <td align="center">{$vo.posid}</td>
            <td align="center">{$vo.name}</td>
            <td align="center">
            <if condition=" empty($vo['catid']) ">
            <font color="#FF0000">无限制</font>
            <else />
            多栏目
            </if>
            </td>
            <td align="center">
            <if condition=" empty($vo['modelid']) ">
            <font color="#FF0000">无限制</font>
            <else />
            多模型
            </if>
            </td>
            <td align="center"><a href="{:U("Contents/Position/public_item",array("posid"=>$vo['posid']))}">信息管理</a> | <a href="{:U("Contents/Position/edit",array("posid"=>$vo['posid']))}">修改</a> | <a href="javascript:confirmurl('{:U("Contents/Position/delete",array("posid"=>$vo['posid']))}', '是否删除?')">删除</a></td>
          </tr>
        </volist>
      </tbody>
    </table>
  </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>