<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">功能说明</div>
  <div class="prompt_text">
    <ul>
      <li>替换前的内容可以使用限定符 {x} 以限定相邻两字符间可忽略的文字，x 是忽略的字节数。如 "a{1}s{2}s"(不含引号) 可以过滤 "ass" 也可过滤 "axsxs" 和 "axsxxs" 等等。对于中文字符，使用 UTF-8 版本，每个中文字符相当于 3 个字节。</li>
      <li><font color="#FF0000">为不影响程序效率，请不要设置过多不需要的过滤内容。</font></li>
      <li>不良词语如果以"/"(不含引号)开头和结尾则表示格式为正则表达式，这时替换内容可用"(n)"引用正则中的子模式，如"/1\d{10}([^\d]+|$)/"替换为"手机(1)"。</li>
    </ul>
  </div>
  <form class="J_ajaxForm" action="{:U('Admin/Censor/add')}" method="post" id="myform">
    <div class="h_a">基本属性</div>
    <div class="table_full">
      <table width="100%" >
           <tr>
              <th width="200">不良词语：</th>
              <th><input type="text" name="name" value="" class="input length_6"></th>
            </tr>
          <tr>
            <th>过滤动作</th>
            <th><select name="replacement" onchange="replaces(this.value);" >
                <option value="{BANNED}" selected >禁止关键词</option>
                <option value="{MOD}" >审核关键词</option>
                <option value="{REPLACE}" >替换关键词</option>
              </select>
              <input class="input" type="text"  name="replacontent" id="replacontent" style="display:none" value="" disabled  ></th>
          </tr>
          <tr>
            <th>所属分类</th>
            <th><select name="type">
                <option value='0' selected>默认分类</option>
                <volist name="typedata" id="tvo">
                  <option value='{$tvo.id}' >{$tvo.name}</option>
                </volist>
              </select>
              创建新分类：
              <input type="text" name="newtype" value="" class="input" size="30"></th>
          </tr>
      </table>
    </div>
    <div class="">
      <div class="btn_wrap_pd">             
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">提交</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script type="text/javascript">
function replaces(value){
	if( value == '{REPLACE}' ){
		$("#replacontent").show().removeAttr("disabled");
	}else{
		$("#replacontent").hide().attr("disabled","disabled");
	}
}
</script>
</body>
</html>