<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <div class="nav">
    <ul class="cc">
      <li class="current"><a href="{:U('Style/index')}">模板管理</a></li>
      <li><a href="{:U("Template/Style/add",array("dir"=>urlencode(str_replace('/','-',$dir))    ))}">在此目录下添加模板</a></li>
    </ul>
  </div>
  <form action="{:U("Style/updatefilename")}" method="post" class="J_ajaxForm">
  <div class="table_list">
  <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <td align="left" width="30%">目录列表</td>
            <td align="left" width="55%" >说明</td>
            <td align="left"  width="15%">操作</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td align="left" colspan="3">当前目录：{$local}</td>
          </tr>
          <if condition="$dir neq '' && $dir neq '.' "> 
          <tr>
            <td align="left" colspan="3"><a href="{:U("Template/Style/index",array("dir"=>urlencode(  str_replace(basename($dir).'-','',str_replace('/','-',$dir))   )     )   )}"><img src="{$config_siteurl}statics/images/folder-closed.gif" />上一层目录</a></td>
          </tr>
          </if>
          <volist name="tplist" id="vo">
          <tr>
            <td align="left">
            <if condition=" '.'.fileext(basename($vo)) == C('TMPL_TEMPLATE_SUFFIX')">
            <img src="{$tplextlist[$vo]}" />
            <a href="{:U("Template/Style/edit_file",array("dir"=>urlencode(str_replace('/','-',$dir)),"file"=>basename($vo)))}"><b>{$vo|basename}</b></a></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[{$encode_local}][{$vo|basename}]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td> <a href="{:U("Template/Style/edit_file",array("dir"=>urlencode(str_replace('/','-',$dir)) ,"file"=>basename($vo)))}">[修改]</a> | <a href="javascript:confirmurl('{:U("Template/Style/delete",array("dir"=>urlencode(str_replace('/','-',$dir)) ,"file"=>basename($vo)))}','确认要删除吗？')">[删除]</a></td>
            <elseif condition="substr($tplextlist[$vo],-strlen($dirico))!=$dirico" />
            <img src="{$tplextlist[$vo]}" />
            <b>{$vo|basename}</b></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[{$encode_local}][{$vo|basename}]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td></td>
            <else />
            <img src="{$tplextlist[$vo]}" />
            <a href="{:U("Template/Style/index",array("dir"=>urlencode(str_replace('/','-',$dir).basename($vo).'-') ))}"><b>{$vo|basename}</b></a></td>
            <td align="left"><input type="text" class="input length_6 " name="file_explan[{$encode_local}][{$vo|basename}]" value="<?php echo (isset($file_explan[$encode_local][basename($vo)]) ? $file_explan[$encode_local][basename($vo)] : "")?>"></td>
            <td></td>
            </if>
          </tr>
          </volist>
        </tbody>
      </table>
  </div>
   <div class="btn_wrap">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">更新</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
</body>
</html>