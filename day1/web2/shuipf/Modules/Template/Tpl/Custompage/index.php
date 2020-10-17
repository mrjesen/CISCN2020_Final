<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <form action="{:U("Custompage/createhtml")}" method="post">
    <div class="table_list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td align="center" width="50"><input type="checkbox" class="J_check_all" data-direction="x" data-checklist="J_check_x">全选</td>
            <td align="center" width="40">ID</td>
            <td align="left">名称</td>
            <td align="left">生成路径</td>
            <td align="center" width="120">操作</td>
          </tr>
        </thead>
        <tbody>
          <volist name="data" id="vo">
            <tr>
              <td align="center"><input class="J_check" data-yid="J_check_y" data-xid="J_check_x" name="tempid[]" value="{$vo.tempid}" type="checkbox"></td>
              <td align="center">{$vo.tempid}</td>
              <td align="left"><a href="{$Config.siteurl|substr=###,0,-1}{$vo.temppath}{$vo.tempname}" target="_blank" >{$vo.name}</a></td>
              <td align="left">{$vo.temppath}{$vo.tempname}</td>
              <td align="center"><a href="{:U("Custompage/edit",array("tempid"=>$vo['tempid']))}" >编辑</a> | <a href="javascript:confirmurl('{:U("Custompage/delete",array("tempid"=>$vo['tempid']))}','确认要删除吗？')" >删除</a> | <a href="{:U("Custompage/createhtml",array("tempid"=>$vo['tempid']))}" >更新</a></td>
            </tr>
          </volist>
        </tbody>
      </table>
      <div class="p10">
        <div class="pages"> {$Page} </div>
      </div>
    </div>
    <div class="btn_wrap">
      <div class="btn_wrap_pd">
        <label class="mr20"><input type="checkbox" class="J_check_all" data-direction="y" data-checklist="J_check_y">全选</label> 
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">生成自定义页面</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>