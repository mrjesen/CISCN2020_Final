<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">搜索</div>
  <form method="get" action="{$config_siteurl}index.php">
  <input type="hidden" value="Admin" name="g">
    <input type="hidden" value="Logs" name="m">
    <input type="hidden" value="index" name="a">
  <div class="search_type cc mb10">
    <div class="mb10"> <span class="mr20">
    搜索类型：
    <select class="select_2" name="status" style="width:70px;">
        <option value='' <if condition="$_GET['status'] eq ''">selected</if>>不限</option>
                <option value="1" <if condition="$_GET['status'] eq '1'">selected</if>>写入</option>
                <option value="2" <if condition="$_GET['status'] eq '2'">selected</if>>更新</option>
                <option value="3" <if condition="$_GET['status'] eq '3'">selected</if>>删除</option>
      </select>
      用户ID：<input type="text" class="input length_2" name="uid" size='10' value="{$Think.get.uid}" placeholder="用户ID">
      IP：<input type="text" class="input length_2" name="ip" size='20' value="{$Think.get.ip}" placeholder="IP">
    时间：
      <input type="text" name="start_time" class="input length_2 J_date" value="{$Think.get.start_time}" style="width:80px;">
      -
      <input type="text" class="input length_2 J_date" name="end_time" value="{$Think.get.end_time}" style="width:80px;">
      <button class="btn">搜索</button>
      <input type="button" class="btn" name="del_log_4" value="删除一月前数据" onclick="location='{:U("Logs/deletelog")}'"  />
      </span> </div>
  </div>
  <form class="J_ajaxForm" action="{:U('Menu/listorders')}" method="post">
    <div class="table_list">
      <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td align="center" width="30">ID</td>
            <td align="center" width="50" >用户ID</td>
            <td align="center" width="60">状态</td>
            <td align="center">说明</td>
            <td align="center" width="150">时间</td>
            <td align="center" width="120">IP</td>
          </tr>
        </thead>
        <tbody>
          <volist name="logs" id="vo">
            <tr>
              <td align="center">{$vo.id}</td>
              <td align="center">{$vo.uid}</td>
              <td align="center"><if condition="$vo['status'] eq '1'">写入</if>
                <if condition="$vo['status'] eq '2'">更新</if>
                <if condition="$vo['status'] eq '3'">删除</if></td>
              <td align="">{$vo.info}</td>
              <td align="center">{$vo.time}</td>
              <td align="center">{$vo.ip}</td>
            </tr>
          </volist>
        </tbody>
      </table>
      <div class="p10">
        <div class="pages"> {$Page} </div>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>