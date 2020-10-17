<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>修改注册信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript" src="../js/gg.js"></script>
<script src="../js/swfobject.js" type="text/javascript"></script>
<script>
function CheckForm(){
if (document.myform.province.value==""){
    alert("请选择公司所在省份！");
	document.myform.province.focus();
	return false;
  } 
  if (document.myform.city.value==""){
    alert("请选择公司所在城市！");
	document.myform.city.focus();
	return false;
  } 
if (document.myform.content.value==""){
    alert("请填写公司简介！");
	return false;
  }
if (document.myform.content.value=="该公司暂无简介信息"){
    alert("请填写公司简介！");
	document.myform.content.focus();
	return false;
  }
if (document.myform.b.value==""){
    alert("请选择大类！");
	document.myform.b.focus();
	return false;
  } 
//定义正则表达式部分
var strP=/^\d+$/;
if(!strP.test(document.myform.qq.value)  && document.myform.qq.value!="") {
alert("QQ只能填数字！"); 
document.myform.qq.focus(); 
return false; 
}   

if (document.myform.flv.value != "")//这里输入框不为空
{
var FileType = "flv,swf";   //这里是允许的后缀名，注意要小写
var FileName = document.myform.flv.value
FileName = FileName.substring(FileName.lastIndexOf('.')+1, FileName.length).toLowerCase(); //这里把后缀名转为小写了，不然一个后缀名会有很多种大小写组合
if (FileType.indexOf(FileName) == -1){
	document.myform.flv.focus();
	document.myform.flv.style.backgroundColor="FFCC00";
	alert("请填写flv或swf格式的文件地址！");
	return false;
	}
}
}
</script>
</head>
<body>
<?php
$founderr=0;
$errmsg="";
$action = isset($_GET['action'])?$_GET['action']:"";
	$sql="select * from zzcms_user where username='" .$username. "'";
	$rs=query($sql);
	$row=fetch_array($rs);	
if ($action=="modify") {
			$qqid = isset($_POST['qqid'])?$_POST['qqid']:"";
            $b = isset($_POST['b'])?$_POST['b']:'0';
			$s = isset($_POST['s'])?$_POST['s']:'0';
			$flv = isset($_POST['flv'])?$_POST['flv']:'';
			$oldflv = isset($_POST['oldflv'])?$_POST['oldflv']:'';
			checkstr($img,"upload");//入库前查上传文件地址是否合格
			checkstr($flv,"upload");//入库前查上传文件地址是否合格
			checkstr($oldflv,"upload");//入库前查上传文件地址是否合格
			checkstr($oldimg,"upload");
			checkstr($somane,'quanhanzi','联系人');
			checkstr($mobile,'tel');
			checkstr($email,'email');	
			if ($row["usersf"]=="公司"){
				if ($content==""){//为防止输入空格
				$founderr=1;
				$errmsg=$errmsg ."<li>公司简介不能为空</li>";
				}
			}
			if (allowrepeatreg=='no'){
			$rsn=query("select * from zzcms_user where phone='" . $phone . "' and username!='$username'");
			$r=num_rows($rsn);
			if ($r){
			$founderr=1;
			$errmsg=$errmsg . "<li>此电话号码已被使用！</li>";
			}
			}
			
			if ($founderr==1){
			WriteErrMsg($errmsg);
			}else{
			query("update zzcms_user set bigclassid='$b',smallclassid='$s',content='$content',img='$img',flv='$flv',province='$province',city='$city',
			xiancheng='$xiancheng',somane='$somane',sex='$sex',phone='$phone',mobile='$mobile',fox='$fox',address='$address',
			email='$email',qq='$qq',qqid='$qqid',homepage='$homepage' where username='".$username."'");
			if ($oldimg<>$img && $oldimg<>"/image/nopic.gif"){
				$f="../".$oldimg;
				if (file_exists($f)){
				unlink($f);
				}
				$fs="../".str_replace(".","_small.",$oldimg);
				if (file_exists($fs)){
				unlink($fs);		
				}
			}
			if ($oldflv<>$flv){
				$f="../".$oldflv;
				if (file_exists($f)==true){
				unlink($f);
				}
			}
				if ($qq<>$oldqq) {
				query("Update zzcms_main set qq='" . $qq . "' where editor='" . $username . "'");
				}
				echo "<SCRIPT language=JavaScript>alert('会员资料修改成功！');location.href='manage.php'</SCRIPT>";
			}
}else{
?>
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
<div class="admintitle">修改注册信息</div>
<FORM name="myform" action="?action=modify" method="post" onSubmit="return CheckForm()">              
          <table width="100%" border="0" cellpadding="3" cellspacing="1">
            <tr> 
              <td width="15%" align="right" class="border2">用户名：</td>
              <td width="85%" class="border2"><?php echo $row["username"]?></td>
            </tr>
            <tr> 
              <td align="right" class="border">姓名：</td>
              <td class="border"> <input name="somane" class="biaodan" value="<?php echo $row["somane"]?>" size="30" maxLength="50"></td>
            </tr>
            <tr > 
              <td align="right" class="border2">性别：</td>
              <td class="border2"><label> <input type="radio" value="1" name="sex" <?php if ($row["sex"]==1) { echo "CHECKED";}?>>
               男</label>
<label> <input type="radio" value="0" name="sex" <?php if ($row["sex"]==0) { echo "CHECKED";}?>>
              女</label></td>
            </tr>
            <tr> 
              <td align="right" class="border">E-mail：</td>
              <td class="border"> <input name="email"  class="biaodan" value="<?php echo $row["email"]?>" size="30" maxLength="50"> 
              </td>
            </tr>
            <tr > 
              <td align="right" class="border2">QQ：</td>
              <td class="border2"> <input name="qq" id="qq"  class="biaodan" value="<?php echo $row["qq"]?>" size="30" maxLength="50">
                <input name="oldqq" type="hidden" id="oldqq" value="<?php echo $row["qq"]?>"></td>
            </tr>
            <tr> 
              <td align="right" class="border">QQ绑定登录网站：</td>
              <td class="border">
                <?php if ($row["qqid"]<>"") { ?>
                <input name="qqid" type="checkbox" id="qqid" value="1" checked>
               <?php echo "(已绑定。点击可取消绑定)";
				}else{
		echo "未绑定QQ登录";
	}
		?>
              </td>
            </tr>
            <tr > 
              <td align="right" class="border2">手机：</td>
              <td class="border2"> 
                <input name="mobile" id="mobile"  class="biaodan" value="<?php echo $row["mobile"]?>" size="30" maxLength="50"></td>
            </tr>
            <tr > 
              <td align="right" class="border">&nbsp;</td>
              <td class="border"> 
                <input name="Submit2"   type="submit" class="buttons" id="Submit2" value="修改"></td>
            </tr>
          </table>
	  <?php 
	  if ($row["usersf"]=="公司"){
	  ?>     
 <div class="admintitle">修改公司信息</div>
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="15%" align="right" class="border2">公司名称：</td>
            <td width="85%" class="border2"><?php echo $row["comane"]?></td>
          </tr>
          <tr> 
            <td align="right" class="border">企业类别：</td>
            <td class="border"><?php
$sqln = "select classid,parentid,classname from zzcms_userclass where parentid<>'0' order by xuhao asc";
$rsn=query($sqln);
?>
              <script language = "JavaScript" type="text/javascript">
var onecount;
subcat = new Array();
<?php 
$count = 0;
        while($rown = fetch_array($rsn)){
        ?>
subcat[<?php echo $count?>] = new Array("<?php echo trim($rown["classname"])?>","<?php echo trim($rown["parentid"])?>","<?php echo trim($rown["classid"])?>");
       <?php
		$count = $count + 1;
       }
        ?>
onecount=<?php echo $count ?>;
function changelocation(locationid){
    document.myform.s.length = 1; 
    for (i=0;i < onecount; i++){
            if (subcat[i][1] == locationid){ document.myform.s.options[document.myform.s.length] = new Option(subcat[i][0], subcat[i][2]);}        
    }
    }</script>
              <select name="b" size="1" id="b"  class="biaodan" onChange="changelocation(document.myform.b.options[document.myform.b.selectedIndex].value)">
                <option value="0" selected="selected">请选择大类</option>
                <?php
	$sqln = "select classid,classname from zzcms_userclass where  parentid='0' order by xuhao asc";
    $rsn=query($sqln);
	while($rown = fetch_array($rsn)){
	?>
<option value="<?php echo $rown["classid"]?>" <?php if ($rown["classid"]==$row["bigclassid"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
                <?php
				}
				?>
              </select>
              <select name="s"  class="biaodan">
                <option value="0">请选择小类</option>
                <?php
$sqln="select classid,classname from zzcms_userclass  where parentid='" .$row["bigclassid"]."' order by xuhao asc";
$rsn=query($sqln);
$rown= num_rows($rsn);//返回记录数
if(!$rown){
?>
                <option value="" >下无子类</option>
                <?php
}else{
while($rown = fetch_array($rsn)){
?>
<option value="<?php echo $rown["classid"]?>" <?php if ($rown["classid"]==$row["smallclassid"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
<?php 	  
}
}
?>
              </select></td>
          </tr>
          <tr class="border" > 
            <td align="right" class="border2">所在地区：</td>
            <td class="border2"><select name="province" id="province"  class="biaodan"></select>
<select name="city" id="city"  class="biaodan"></select>
<select name="xiancheng" id="xiancheng"  class="biaodan"></select>
<script src="../js/area.js"></script>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>
 
  </td>
          </tr>
          <tr> 
            <td align="right" class="border">公司地址：</td>
            <td class="border"> <input name="address" id="address" class="biaodan" value="<?php echo $row["address"]?>" size="30" maxlength="50"> 
            </td>
          </tr>
          <tr > 
            <td align="right" class="border2">公司网站：</td>
            <td class="border2"> <input name="homepage" id="homepage"  class="biaodan" value="<?php echo $row["homepage"]?>" size="30" maxLength="100"></td>
          </tr>
          <tr > 
            <td align="right" class="border">公司电话：</td>
            <td class="border"> <input name="phone"  class="biaodan" value="<?php echo $row["phone"]?>" size="30" maxLength="50"></td>
          </tr>
          <tr > 
            <td align="right" class="border2">公司传真：</td>
            <td class="border2"> <input name="fox"  class="biaodan" value="<?php echo $row["fox"]?>" size="30" maxLength="50"></td>
          </tr>
          <tr> 
            <td align="right" class="border2">公司简介：</td>
            <td class="border2"> 
			<textarea name="content" id="content"><?php echo stripfxg($row["content"],true) ?></textarea> 
             <script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
			  <script type="text/javascript">CKEDITOR.replace('content');</script>   
            </td>
          </tr>
          <tr> 
            <td height="50" align="right" class="border"> 公司形象图片： 
              <input name="img" type="hidden" id="img" value="<?php echo $row["img"]?>">
              <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["img"]?>">
             </td>
            <td height="50" class="border"> 
			 <table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
                <tr> 
                  <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> 
                    <?php
				  if($row["img"]<>"" && $row["img"]<>"/image/nopic.gif"){
				  echo "<img src='".$row["img"]."' border=0 width=120 /><br>点击可更换";
				  }else{
				  echo "<input name='Submit2' type='button'  value='上传图片'/>";
				  }
				  ?>
                  </td>
                </tr>
              </table></td>
          </tr>
      
          <tr> 
            <td align="right" class="border2" >公司形象视频上传： 
              <input name="flv" type="hidden" id="flv" value="<?php echo $row["flv"]?>" />
              <input name="oldflv" type="hidden" id="oldflv" value="<?php echo $row["flv"]?>">
              </td>
            <td class="border2" > 
			    <?php 
if (check_user_power("uploadflv")=="yes"){
?>
			<table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
                <tr> 
                  <td align="center" bgcolor="#FFFFFF" id="container" onClick="openwindow('/uploadflv_form.php',400,300)"> 
                    <?php
		if($row["flv"]<>""){
				  if (substr($row["flv"],-3)=="flv") {
				  ?>
				  
                    <script type="text/javascript">
          var s1 = new SWFObject("/image/player.swf","ply","200","200","9","#FFFFFF");
          s1.addParam("allowfullscreen","true");
          s1.addParam("allowscriptaccess","always");
          s1.addParam("flashvars","file=<?php echo $row["flv"] ?>&autostart=false");
          s1.write("container");
         </script> 
                    <?php 
				 }elseif (substr($row["flv"],-3)=="swf") {
				 echo "<embed src='".$row["flv"]."' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width=200 height=200></embed>";
				 }
			echo "<br/>点击重新上传视频";
			}else{
			echo "<input name='Submit2' type='button'  value='添加视频'/>";
			}
				  
				  ?>
                  </td>
                </tr>
              </table>
			  	    <?php
		   }else{
		  ?>
              <table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td onClick="javascript:window.location.href='vip_add.php'"> 
				  <p><img src="../image/jx.gif" width="48" height="48" /><br />仅限收费会员</p><p><span class='buttons'>现在审请？</span><br /></p>
				  </td>
                </tr>
              </table>
			 <?php
	}
	?>  
			  </td>
          </tr>
         
          <tr> 
            <td class="border">&nbsp;</td>
            <td height="40" class="border"> <input name=Submit   type=submit class="buttons" id="Submit" value="修改"> 
            </td>
          </tr>
        </table>
	  <?php
	  }
	  ?>
  </form>
 </div>
</div>
</div> 
</div>
<?php
}
?>
</body>
</html>