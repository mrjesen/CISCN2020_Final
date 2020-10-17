<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<!doctype html>
<!--[if lt IE 8 ]> <html class="no-js ie6-7"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title>{$tag} - {$SEO['site_title']}</title>
<link rel="stylesheet" href="{$Config.siteurl}statics/blog/css/style.css" type="text/css" media="screen" />
<link rel='stylesheet' id='wp-recentcomments-css'  href='{$Config.siteurl}statics/blog/css/wp-recentcomments.css?ver=2.0.6' type='text/css' media='screen' />
<link rel="alternate" type="application/rss+xml" title="水平凡个人博客" href="{$Config.siteurl}index.php?m=content&c=rss&rssid={$catid}" />
<meta name="generator" content="ThinkPHP Shuipf" />
<meta name="description" content="{$SEO['description']}" />
<meta name="keywords" content="{$SEO['keyword']}" />
<link rel="canonical" href="{$Config.siteurl}" />
<!--[if IE 7]>
<style type="text/css">
#sidebar {
    padding-top:40px;
}
.cm #commentform p {
	float:none;
	clear:none;
}
</style>
<![endif]-->
<script type='text/javascript' src='{$Config.siteurl}statics/js/jquery.js'></script>
<script type='text/javascript' src='{$Config.siteurl}statics/blog/js/ls.js'></script>
<!--html5 SHIV的调用-->
<script type='text/javascript' src='{$Config.siteurl}statics/blog/js/html5.js'></script>
</head>
<body  class="home blog">
<!--header START-->
<template file="Contents/header.php"/>
<!--header END-->
<div id="main" class="grid">
  <div id="content" class="g-u" role="主内容">
    <div class="recommend block grid">
      <ul class="recommend-list g-u" style="display: inline-block; ">
        <!--推荐循环开始-->
        <position action="position" posid="1" num="6">
          <volist name="data" id="vo">
            <li class="grid g-u">
              <div class="image g-u"> <a title="点此前往《{$vo.data.title}》" href="{$vo.data.url}"> <img width="93" height="44" src="<if condition=" empty($vo['data']['thumb']) ">{$Config.siteurl}statics/blog/images/no-has-thumbnail.png
                <else />
                {$vo['data']['thumb']}
                </if>
                " class="attachment-96x44 wp-post-image" alt="{$vo.data.title}" title="{$vo.data.title}"> </a> </div>
              <div class="item-detail g-u">
                <h1><a class="title entry-title" role="title" href="{$vo.data.url}" title="点此前往《{$vo.data.title}》" rel="bookmark">{$vo.data.title}</a> </h1>
                <footer class="info"><a href="">前<b>
                  <?=commcount($vo['catid'],$vo['id']);?>
                  </b>个座位已被强势霸占！</a>共有<b>
                  {$vo.views}
                  </b>人围观</footer>
              </div>
            </li>
          </volist>
        </position>
        <!--推荐循环结束-->
      </ul>
      <s class="tag tag-recommend">推荐</s> </div>
    <!--内容循环-->
    <tags action="lists" tag="$tag" num="5" page="$page" cache="3600">
      <volist name="data" id="vo">
        <article id="post-{$vo.id}" class="post-{$vo.id} post type-post status-publish format-standard hentry category-css3 category-html5 category-js category-f2e tag-firefox4 post-digest block">
          <h1 class="J_Post_Title"><a class="title entry-title" role="title" href="{$vo.url}" title="{$vo.title}" target="_blank" rel="bookmark">{$vo.title}</a> </h1>
          <div class="bd grid entry-content">
            <p class="image g-u"> <a href="{$vo.url}" target="_blank" title="点此前{$vo.title}"> <img width="300" height="140" src="<if condition=" empty($vo['thumb']) ">{$Config.siteurl}statics/blog/images/no-has-thumbnail.png
              <else />
              {$vo['thumb']}
              </if>
              " class="attachment-post-thumbnail wp-post-image" alt="{$vo.title}" title="{$vo.title}"> </a> </p>
            <div class="digest g-u">
              <p>{$vo.description}...</p>
            </div>
          </div>
          <footer>
            <div class="author J_Author" data-weib="shuipf">
              <figure> <img alt="作者：{$vo.username}" src="<?php echo get_avatar(1);?>" class="avatar avatar-70 photo" height="70" width="70">
                <figcaption><b>{$vo.username}</b></figcaption>
              </figure>
            </div>
            <p class="info">发布于
              <time><b>{$vo.updatetime|date="Y-m-d H:i:s",###}</b></time>
              ，归属于<b>{$tag}</b>,
              <comment action="get_comment" catid="$vo['catid']" id="$vo['contentid']"> <a href="{$vo.url}" title="{$vo.title}" target="_blank">前<b>{$data.total}</b>个座位已被强势霸占！</a> </comment>
              共有<b><?php echo hits("c-".$vo['catid']."-".$vo['contentid']);?></b>人围观 </p>
            <s data-id="109" class="tag tag-already-read J_AlreadyRead">已阅</s> </footer>
        </article>
      </volist>
      <!--内容循环结束-->
      <div class="wp-pagenavi"> {$pages} </div>
    </tags>
  </div>
  <template file="Contents/sidebar.php"/> 
</div>
<template file="Contents/footer.php"/> 
<!--[if lte IE 6]>
<script src="http://letskillie6.googlecode.com/svn/trunk/2/zh_CN.js"></script>
<![endif]--> 
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F6a7ac600fcf5ef3f164732dcea2e2ba5' type='text/javascript'%3E%3C/script%3E"));
</script> 
<script type="text/javascript" charset="utf-8" src="{$Config.siteurl}statics/js/lazyload.js"></script> 
<script type="text/javascript">
$(function(){
	$("img").lazyload({
		placeholder:"{$Config.siteurl}statics/images/image-pending.gif",
		effect:"fadeIn"
	});
	$(".recommend-list a").click(function(){
		$(this).text('页面载入中……');
		window.location = $(this).attr('href');
	});
	var histories = new Histories();
	histories.appendTo('.J_Histories');
});
</script>
</body>
</html>