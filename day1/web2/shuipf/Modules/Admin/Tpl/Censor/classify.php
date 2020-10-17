<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
<Admintemplate file="Common/Nav"/>
<div class="h_a">说明</div>
<div class="prompt_text">
  <p>删除分类会同时删除该分类下的信息！</p>
</div>
<form class="J_ajaxForm" action="{:U('Admin/Censor/classify')}" method="post">
  <div class="table_list">
    <table width="100%" cellspacing="0">
      <thead>
        <tr>
          <td width="80" align="left">删除</td>
          <td  align="left">分类名称</td>
        </tr>
      </thead>
      <tbody>
        <volist name="typedata" id="vo">
          <tr>
            <td><input class="input-text" type="checkbox" name="delete[]" value="{$vo.id}" ></td>
            <td><input type="text" class="input" size="30" name="name[{$vo.id}]" value="{$vo.name}" ></td>
          </tr>
        </volist>
      </tbody>
    </table>
  </div>
  <div class="">
    <div class="btn_wrap_pd">
      <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
    </div>
  </div>
</form>
<div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>