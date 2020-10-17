<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
<link rel="stylesheet" href="./css/install.css?v=9.0" />
</head>
<body>
<div class="wrap">
  <?php require './templates/header.php';?>
  <section class="section">
    <div class="">
      <div class="success_tip cc"> <a href="<?php echo $domain ?>admin.php" class="f16 b">安装完成，进入后台管理</a>
		<p>进入后台以后，第一件事是<font color="#FF0000">更新网站缓存</font>，不然有些功能不正常！<p>
		<p>为了您站点的安全，安装完成后即可将网站根目录下的“Install”文件夹删除。<p>
        <p style="color:#F00"><b>如果本程序对您有所帮助，那么我非常期待您能够热情的捐赠鼓励～正如您支持其他开源项目一样。</b><p>
        <p><b>捐赠鼓励地址：<a href="https://me.alipay.com/shuipf" target="_blank" style="color:#F00">https://me.alipay.com/shuipf</a> 有支持，才有持续更新的动力o(∩_∩)o </b><p>
      </div>
      <div class=""> </div>
    </div>
  </section>
</div>
<?php require './templates/footer.php';?>
</body>
</html>