<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="table_list">
  <table width="100%" cellspacing="0" >
      <thead>
        <tr>
          <td width="60">ID</td>
          <td width="100">所属模块</td>
          <td width="60">名称</td>
          <td width="50">生成静态</td>
          <td>URL示例</td>
          <td>URL规则</td>
          <td width="80">管理操作</td>
        </tr>
      </thead>
      <tbody>
        <volist name="info" id="r">
        <tr>
          <td align='center'>{$r.urlruleid}</td>
          <td align="center">{$Module[$r['module']]['name']}</td>
          <td align="center">{$r.file}</td>
          <td align="center"><if condition="$r['ishtml']"><font color="red">√</font><else /><font color="blue">×</font></if></td>
          <td>{$r.example}</td>
          <td>{$r.urlrule}</td>
          <td align='center' ><a href="{:U("Urlrule/edit",array("urlruleid"=>$r['urlruleid']))}">编辑</a> | <a class="J_ajax_del" href="{:U("Urlrule/delete",array("urlruleid"=>$r['urlruleid']))}">删除</a></td>
        </tr>
        </volist>
      </tbody>
    </table>
  </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>