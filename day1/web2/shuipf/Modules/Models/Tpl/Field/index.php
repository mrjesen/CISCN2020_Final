<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <div class="nav">
    <ul class="cc">
      <li class="current"><a href="{:U("Field/index",array("modelid"=>$modelid))}">管理模型字段</a></li>
      <li ><a href="{:U("Field/add",array("modelid"=>$modelid))}">添加字段</a></li>
      <li ><a href="{:U("Field/priview",array("modelid"=>$modelid))}"  target="_blank">预览模型</a></li>
    </ul>
  </div>
  <form class="J_ajaxForm" action="{:U("Field/listorder")}" method="post">
  <div class="table_list">
  <table width="100%" cellspacing="0" >
        <thead>
          <tr>
            <td width="70" align='center'>排序</td>
            <td width="90" align='center'>字段名</td>
            <td align='center'>别名</td>
            <td width="100" align='center'>字段类型</td>
            <td width="100" align='center'>是否主表字段</td>
            <td width="50" align='center'>必填</td>
            <td width="50" align='center'>搜索</td>
            <td width="50" align='center'>排序</td>
            <td width="50" align='center'>投稿</td>
            <td width="200" align='center'>管理操作</td>
          </tr>
        </thead>
        <tbody class="td-line">
        <volist name="data" id="vo">
          <tr>
            <td align='center'><input name='listorders[{$vo.fieldid}]' type='text' size='3' value='{$vo.listorder}' class='input'></td>
            <td>{$vo.field}</td>
            <td>{$vo.name}</td>
            <td align='center'>{$vo.formtype}</td>
            <td align='center'><if condition="$vo['issystem'] eq 1"><font color="blue">√</font><else /> <font color="red">╳</font></if></td>
            <td align='center'><if condition="$vo['minlength'] eq 1"><font color="blue">√</font><else /> <font color="red">╳</font></if></td>
            <td align='center'><if condition="$vo['issearch'] eq 1"><font color="blue">√</font><else /> <font color="red">╳</font></if></td>
            <td align='center'><if condition="$vo['isorder'] eq 1"><font color="blue">√</font><else /> <font color="red">╳</font></if></td>
            <td align='center'><if condition="$vo['isadd'] eq 1"><font color="blue">√</font><else /> <font color="red">╳</font></if></td>
            <td align='center'>
            <a href="{:U("Field/edit",array("fieldid"=>$vo['fieldid'],"modelid"=>$vo['modelid']))}">修改</a> | 
            <if condition=" in_array($vo['field'],$forbid_fields) || in_array($vo['field'], $forbid_delete) ">
            <font color="#BEBEBE"> 禁用 </font>|
            <else />
                 <if condition=" $vo['disabled'] eq 0 ">
                 <a href="{:U("Field/disabled",array("fieldid"=>$vo['fieldid'],"modelid"=>$vo['modelid'],"disabled"=>0))}">禁用</a> |
                 <else />
                 <a href="{:U("Field/disabled",array("fieldid"=>$vo['fieldid'],"modelid"=>$vo['modelid'],"disabled"=>1))}"><font color="#FF0000">启用</font></a> |
                 </if>
            </if>
            <if condition=" in_array($vo['field'],$forbid_delete) ">
            <font color="#BEBEBE"> 删除</font>
            <else />
            <a class="J_ajax_del" href="{:U("Field/delete",array("fieldid"=>$vo['fieldid'],"modelid"=>$vo['modelid']))}">删除</a>
            </if>
            </td>
          </tr>
        </volist>
        </tbody>
      </table>
  </div>
  <div class="btn_wrap">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">排序</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>