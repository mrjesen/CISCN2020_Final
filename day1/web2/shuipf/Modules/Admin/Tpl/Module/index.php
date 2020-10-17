<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body>
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">说明</div>
  <div class="prompt_text">
    <ul>
      <li>模块管理可以很好的扩展网站运营中所需功能！</li>
      <li><font color="#FF0000">获取更多模块请到官方网站模块扩展中下载安装！安装非官方发表模块需谨慎，有被清空数据库的危险！</font></li>
      <li>官网地址：<font color="#FF0000">http://www.shuipfcms.com</font>，<a href="http://www.shuipfcms.com" target="_blank">立即前往</a>！</li>
    </ul>
  </div>
  <div class="table_list">
    <table width="100%">
      <colgroup>
      <col width="90">
      <col>
      <col width="160">
      </colgroup>
      <thead>
        <tr>
          <td align="center">应用图标</td>
          <td>应用介绍</td>
          <td align="center">安装时间</td>
          <td align="center">操作</td>
        </tr>
      </thead>
      <volist name="data" id="vo">
      <tr>
        <td><div class="app_icon"><b></b><img src="{$config_siteurl}shuipf/Modules/{$vo.module|ucwords}/icon.png" onerror="this.onerror=null;this.src='{$config_siteurl}statics/images/modul.png'" alt="{$vo.modulename}" width="80" height="80"></div></td>
        <td valign="top"><h3 class="mb5 f12"><if condition=" $vo['authorsite'] "><a target="_blank" href="{$vo.authorsite}">{$vo.modulename}</a><else />{$vo.modulename}</if></h3>
          <div class="mb5"> <span class="mr15">版本：<b>{$vo.version}</b></span> <span>开发者：<if condition=" $vo['author'] "><a target="_blank" href="{$vo.authorsite}">{$vo.author}</a><else />匿名开发者</if></span> <span>适配 ShuipFCMS 最低版本：<if condition=" $vo['adaptation'] ">{$vo.adaptation}<else /><font color="#FF0000">没有标注，存在风险</font></if></span> </div>
          <div class="gray"><if condition=" $vo['introduce'] ">{$vo.introduce}<else />没有任何介绍</if></div>
          <div> <span class="mr20"><a href="{$vo.authorsite}" target="_blank">{$vo.authorsite}</a></span> </div></td>
        <td align="center"><span>{$vo.installdate}</span></td>
        <td align="center">
          <if condition=" in_array($vo['status'],array(1,2)) && !isset($modules[$vo['module']])">
          <a  href="{:U('Module/install', array('module'=>$vo['module'])  )}" class="btn btn_submit mr5">安装</a>
          </if>
          <if condition=" $modules[$vo['module']] && !$modules[$vo['module']]['iscore']">
          <if condition=" $vo['disabled']">
          <a  href="{:U('Module/disabled', array('module'=>$vo['module'])  )}" class="btn mr5">禁用</a>
          <else/>
          <a  href="{:U('Module/disabled', array('module'=>$vo['module'])  )}" class="btn btn_submit  mr5">启用</a>
          </if>
          <button data-action="{:U('Module/uninstall',array('module'=>$vo['module']))}" class="J_ajax_upgrade btn">卸载</button>
          </if>
          <if condition=" $modules[$vo['module']] && $modules[$vo['module']]['iscore']">
          系统模块
          </if>
         </td>
      </tr>
      </volist>
    </table>
  </div>
  <div class="p10">
        <div class="pages">{$Page}</div>
   </div>
</div>
<script src="{$config_siteurl}statics/js/common.js?v"></script>
<script>
if ($('button.J_ajax_upgrade').length) {
    Wind.use('artDialog', function () {
        $('.J_ajax_upgrade').on('click', function (e) {
            e.preventDefault();
           var $_this = this,$this = $(this), url = $this.data('action'), msg = $this.data('msg'), act = $this.data('act');
            art.dialog({
                title: false,
                icon: 'question',
                content: '确定要卸载吗？',
                follow: $_this,
                close: function () {
                    $_this.focus();; //关闭时让触发弹窗的元素获取焦点
                    return true;
                },
                ok: function () {
                    $.getJSON(url).done(function (data) {
                        if (data.state === 'success') {
                            if (data.referer) {
                                location.href = data.referer;
                            } else {
                                reloadPage(window);
                            }
                        } else if (data.state === 'fail') {
                            art.dialog.alert(data.info);
                        }
                    });
                },
                cancelVal: '关闭',
                cancel: true
            });
        });

    });
}
</script>
</body>
</html>