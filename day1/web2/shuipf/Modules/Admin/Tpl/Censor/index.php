<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">功能说明</div>
  <div class="prompt_text">
    <ul>
      <li>替换前的内容可以使用限定符 {x} 以限定相邻两字符间可忽略的文字，x 是忽略的字节数。如 "a{1}s{2}s"(不含引号) 可以过滤 "ass" 也可过滤 "axsxs" 和 "axsxxs" 等等。对于中文字符，使用 UTF-8 版本，每个中文字符相当于 3 个字节。</li>
      <li><font color="#FF0000">为不影响程序效率，请不要设置过多不需要的过滤内容。</font></li>
      <li>不良词语如果以"/"(不含引号)开头和结尾则表示格式为正则表达式，这时替换内容可用"(n)"引用正则中的子模式，如"/1\d{10}([^\d]+|$)/"替换为"手机(1)"。</li>
    </ul>
  </div>
  <div class="h_a">搜索</div>
  <div class="search_type cc mb10">
    <form action="{$config_siteurl}index.php" method="get">
      <input type="hidden" value="Admin" name="g">
      <input type="hidden" value="Censor" name="m">
      <input type="hidden" value="index" name="a">
      <input type="hidden" value="1" name="search">
      <div class="search_type cc mb10">
        <div class="mb10"> <span class="mr20"> 关键字：
          <input type="text" class="input length_2" name="keyword" style="width:200px;" value="{$keyword}" placeholder="请输入关键字...">
          <select class="select_2" name="type">
            <option value='0'>默认分类</option>
            <volist name="typedata" id="tvo"> <option value='{$tvo.id}' 
              <if condition=" $_GET['type'] eq $tvo['id'] ">selected</if>
              >{$tvo.name}
              </option>
            </volist>
          </select>
          <button class="btn">搜索</button>
          </span> </div>
      </div>
    </form>
  </div>
  <form class="J_ajaxForm" action="{:U('Admin/Censor/index')}" method="post">
    <div class="table_list">
      <table width="100%">
        <colgroup>
        <col width="80">
        <col width="100">
        <col>
        <col width="200">
        <col width="200">
        </colgroup>
        <thead>
          <tr>
            <td>删除</td>
            <td>不良词语</td>
            <td>过滤动作</td>
            <td>词语分类</td>
            <td>操作者</td>
          </tr>
        </thead>
        <volist name="data" id="vo">
          <tr>
            <td ><input type="checkbox" name="delete[]" value="{$vo.id}" ></td>
            <td><input type="text" class="input" size="30" name="find[{$vo.id}]" value="{$vo.find}" ></td>
            <td><select name="replacement[{$vo.id}]" onChange="replaces({$vo.id},this.value);" >
                <option value="{BANNED}" <if condition=" $vo['replacement'] eq '{BANNED}' ">selected</if> >禁止关键词</option>
                <option value="{MOD}" <if condition=" $vo['replacement'] eq '{MOD}' ">selected</if>>审核关键词</option>
                <option value="{REPLACE}" <if condition=" !in_array($vo['replacement'],array('{BANNED}','{MOD}')) ">selected</if>>替换关键词</option>
              </select>
              <input class="input" type="text" size="30" name="replacontent[{$vo.id}]" id="replacontent_{$vo.id}" <if condition=" in_array($vo['replacement'],array('{BANNED}','{MOD}'))  "> style="display:none" value="" disabled<else/>value="{$vo['replacement']}"</if>  >
              </td>
            <td>
            <select name='type[{$vo.id}]'>
                <option value='0' <if condition=" $vo['type'] eq '0' ">selected</if>>默认分类</option>
                <volist name="typedata" id="tvo">
                <option value='{$tvo.id}' <if condition=" $vo['type'] eq $tvo['id'] ">selected</if>>{$tvo.name}</option>
                </volist>
              </select>
			  </td>
            <td>{$vo.admin}</td>
          </tr>
        </volist>
      </table>
      <div class="p10">
        <div class="pages"> {$Page} </div>
      </div>
    </div>
    <div class="btn_wrap">
      <div class="btn_wrap_pd">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript">
function replaces(id,value){
	if( value == '{REPLACE}' ){
		$("#replacontent_"+id).show().removeAttr("disabled");
	}else{
		$("#replacontent_"+id).hide().attr("disabled","disabled");
	}
}
</script>
</body>
</html>