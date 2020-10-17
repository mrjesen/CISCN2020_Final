<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>展厅设置</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript" src="/js/gg.js"></script>
<script>
function  checkmobile(){ 
var strP=/^\d+$/;//定义正则表达式部分
if(!strP.test(document.myform.mobile.value)) {
alert("此处必须为数字！"); 
document.myform.mobile.focus(); 
return false; 
}
}
function  checkbannerheight(){ 
var strP=/^\d+$/;
if(!strP.test(document.myform.bannerheight.value)) {
alert("此处必须为数字！"); 
document.myform.bannerheight.focus(); 
return false; 
}
}
</script>
<?php
$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
if($action=="modify"){
$daohang="";
if(!empty($_POST['daohang'])){
    for($i=0; $i<count($_POST['daohang']);$i++){
    $daohang=$daohang.($_POST['daohang'][$i].',');
    }
	$daohang=substr($daohang,0,strlen($daohang)-1);//去除最后面的","
}
$bannerbg=isset($_POST["img"])?$_POST["img"]:'';
$oldbannerbg=isset($_POST["oldimg"])?$_POST["oldimg"]:'';
checkstr($oldbannerbg,"upload");
if (isset($_POST["nobannerbg"])){
$bannerbg="";
}
$bannerheight=@$_POST["bannerheight"];
$tongji=str_replace('"','',str_replace("'",'',trim($_POST['tongji'])));
$baidu_map=str_replace('"','',str_replace("'",'',trim($_POST['baidu_map'])));
$isok=query("update zzcms_usersetting set comanestyle='$comanestyle',comanecolor='$comanecolor',daohang='$daohang',bannerbg='$bannerbg',bannerheight='$bannerheight',tongji='$tongji',baidu_map='$baidu_map' where username='".$username."'");		

if($oldbannerbg<>$bannerbg && $oldbannerbg<>"/image/nopic.gif" && $oldbannerbg<>"" ) {
	$f="../".$oldbannerbg;
	if(file_exists($f)){
	unlink($f);
	}
}
	if ($isok){	
	echo "<script>alert('成功更新设置');location.href='ztconfig.php'</script>";	
	}
}

$rs=query("select * from zzcms_usersetting where username='".$username."'");
$row=num_rows($rs);
if(!$row){
query("INSERT INTO zzcms_usersetting (username,skin,daohang)VALUES('".$username."','blue1','网站首页, 招商信息, 公司简介, 资质证书, 联系方式, 在线留言')");//如不存在自动添加
echo "用户配置记录不存在，已自动修复，请刷新页面";
}else{
$row=fetch_array($rs);
?>
</head>
<body>
<div class="main">
<?php
include("top.php");
?>
<div class="pagebody">
<div class="left">
<?php
include("left.php");
?>
</div>
<div class="right">
<div class="content">
<div class="admintitle">展厅设置</div>
<?php 
if (str_is_inarr(usergr_power,'zt')=="no" && $usersf=='个人'){
echo "<script>alert('个人用户没有此权限');history.back()'</script>";
exit;
}
?>
<form name="myform" method="post" action="?action=modify"> 
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <?php if(check_user_power('set_zt')=='yes'){?>
          <tr> 
            <td width="20%" align="right" class="border2">banner背景图自定义： 
              <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["bannerbg"]?>" /> 
              <input name="img" type="hidden" id="img" value="<?php echo trim($row["bannerbg"])?>" size="50" maxlength="255"></td>
            <td width="80%" class="border2">  <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                <tr> 
                  <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"> 
                    <?php
				  if($row["bannerbg"]<>""){
				  echo "<img src='".$row["bannerbg"]."' border=0 width=120 /><br>点击可更换图片";
				  }else{
				  echo "<input name='Submit2' type='button'  value='上传图片'/>";
				  }
				  ?>                  </td>
                </tr>
              </table>
              <input name='nobannerbg[]' type='checkbox' id="nobannerbg" value='1' />
              <label for="nobannerbg">使用默认图片</label> </td>
          </tr>
          <?php 
		  }else{
		  ?>
          <tr> 
            <td align="right" class="border2">banner背景图自定义</td>
            <td class="border2">您所在的用户组没有权限，不能使用本功能</td>
          </tr>
		    <?php 
		  }
		  ?>
         
		   <tr> 
            <td align="right" class="border2">banner高度设置：</td>
            <td class="border2"><input name="bannerheight" type="text" id="bannerheight" class="biaodan"  value="<?php echo $row["bannerheight"]?>" size="10" maxlength="3" onBlur="checkbannerheight()" />
              px</td>
          </tr>
            <td align="right" class="border">公司名称样式：</td>
            <td class="border"> 
              <select name="comanestyle" id="comanestyle">
			  <option value="left">显式方式</option>
                <option value="left" <?php if ($row["comanestyle"]=="left" ){ echo"selected";}?>>左边</option>
                <option value="center" <?php if($row["comanestyle"]=="center" ){ echo"selected";}?>>居中</option>
                <option value="right" <?php if($row["comanestyle"]=="right" ){ echo"selected";}?>>右边</option>
				<option value="no" <?php if($row["comanestyle"]=="no" ){ echo"selected";}?>>不在banner上显示公司名</option>
              </select>
             
              <select name="comanecolor" id="comanecolor">
			  <option value="#FFFFFF">字体颜色</option>
                <option value="#FFFFFF" <?php if($row["comanecolor"]=="#FFFFFF" ){ echo"selected";}?>>白色</option>
                <option value="#000000" <?php if($row["comanecolor"]=="#000000" ){ echo"selected";}?>>黑色</option>
              </select> </td>
          </tr>
          <tr> 
            <td align="right" class="border2">导航栏目设置：</td>
            <td class="border2"> 
			<label><input name="daohang[]" type="checkbox" id="daohang" value="网站首页" <?php  if(strpos($row["daohang"],"网站首页")!==false ){ echo"checked";}?> />
              网站首页</label>
             <label> <input name="daohang[]" type="checkbox" id="daohang" value="招商信息" <?php if(strpos($row["daohang"],"招商信息")!==false ){ echo"checked";}?> />
              <?php echo channelzs?> </label>
			 <label> <input name="daohang[]" type="checkbox" id="daohang" value="品牌信息" <?php if(strpos($row["daohang"],"品牌信息")!==false ){ echo"checked";}?> />
               品牌信息</label>
              <label><input name="daohang[]" type="checkbox" id="daohang" value="公司简介" <?php if(strpos($row["daohang"],"公司简介")!==false ){ echo"checked";}?> />
             公司简介</label>
			 <label> <input name="daohang[]" type="checkbox" id="daohang" value="公司新闻" <?php if(strpos($row["daohang"],"公司新闻")!==false ){ echo"checked";}?> />
              公司新闻</label>
			  <label><input name="daohang[]" type="checkbox" id="daohang" value="招聘信息" <?php if(strpos($row["daohang"],"招聘信息")!==false ){ echo"checked";}?> />
               招聘信息</label>
              <label><input name="daohang[]" type="checkbox" id="daohang" value="资质证书" <?php if(strpos($row["daohang"],"资质证书")!==false ){ echo"checked";}?> />
             资质证书</label>
             <label> <input name="daohang[]" type="checkbox" id="daohang" value="联系方式" <?php if(strpos($row["daohang"],"联系方式")!==false ){ echo"checked";}?> />
              联系方式</label> 
             <label> <input name="daohang[]" type="checkbox" id="daohang" value="在线留言" <?php if(strpos($row["daohang"],"在线留言")>0 ){ echo"checked";}?> />
              在线留言</label> </td>
          </tr>
          <tr>
            <td align="right" class="border2"> 统计代码：</td>
            <td class="border2"><input name="tongji" type="text" id="tongji" class="biaodan" value="<?php echo $row["tongji"]?>" size="90" maxlength="200" />            </td>
          </tr>
          <tr>
            <td align="right" class="border2"> 百度地图代码：</td>
            <td class="border2"><input name="baidu_map" type="text" id="baidu_map" class="biaodan"  value="<?php echo $row["baidu_map"]?>" size="50" maxlength="200" />
              <a href="http://api.map.baidu.com/mapCard/" target="_blank" style="color:red"> 制做百度地图，获取地图代码 </a></td>
          </tr>
        
          <tr> 
            <td class="border2">&nbsp;</td>
            <td class="border2"> <input name="Submit2" type="submit" class="buttons" value=" 更新设置"></td>
          </tr>
        </table>
      </form>
</div>
</div>
</div>
</div>
</body>
</html>
<?php 
}
?>