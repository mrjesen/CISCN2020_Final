<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
//本页用于初次注册本站的公司用户来完善公司信息（公司简介及公司形象图片信息）
$action = isset($_GET['action'])?$_GET['action']:"";	
if ($action=="modify") {
checkstr($img,"upload");//入库前查上传文件地址是否合格
			query("update zzcms_user set bigclassid='$b',smallclassid='$s',content='$content',img='$img',
			province='$province',city='$city',xiancheng='$xiancheng',sex='$sex',mobile='$mobile',address='$address',qq='$qq',
			homepage='$homepage' where username='".$username."'");
			if ($oldcontent=="" || $oldcontent=="&nbsp;"){//只有第一次完善时加分，修改信息不计分，这里需要加验证，不许改为空，防止刷分
				query("update zzcms_user set totleRMB=totleRMB+".jf_addreginfo." where username='".$username."'");
				query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('$username','完善注册信息','+".jf_addreginfo."','+".jf_addreginfo."','".date('Y-m-d H:i:s')."')");
			echo "<script>alert('成功完善了注册信息，获得金币".jf_addreginfo."')</script>";
			}		
			echo "<script language=JavaScript>alert('操作成功！进入下一步');location.href='daohang_skin.php'</script>";
}else{		
?>
<script language = "JavaScript" src="../js/gg.js"></script>
<script language = "JavaScript">
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
	document.myform.content.focus();
	return false;
  }
  if (document.myform.content.value=="" ||document.myform.content.value=="&nbsp;"){
    alert("请填写公司简介！");
	document.myform.content.focus();
	return false;
  }
 if (document.myform.kind.value==""){
    alert("请选择经营模式！");
	document.myform.kind.focus();
	return false;
  }
//定义正则表达式部分
var strP=/^\d+$/;
if(!strP.test(document.myform.qq.value) && document.myform.qq.value!="") {
alert("QQ只能填数字！"); 
document.myform.qq.focus(); 
return false; 
} 
}
</SCRIPT>
</head>
<body>
<div class="main">
<?php
include("top.php");
?>
<div class="pagebody" >
<div class="left">
<?php
include("left.php");
?>
</div>
<div class="right">
<div class="content">
<div class="admintitle">完善注册信息</div>
<?php
$sql="select * from zzcms_user where username='" .$username. "'";
$rs=query($sql);
$row=fetch_array($rs);

if ($row['logins']==0) {
echo "<div class='box'> 您好！<b>".$username."</b>恭喜您成为本站注册会员！<br>请完善您的公司简介信息，以便生成您公司的展厅页面。&gt;&gt;&gt; <a href='daohang_skin.php' target='_self'>跳过此步以后再填</a></div>";
}else{
echo "<div class='box'><font color='#FF0000'><strong>提示：</strong>公司简介信息尚未填写！<br>请完善您的公司简介信息，以提高公司诚信度。</font></div>";
}
?>

<FORM name="myform" action="?action=modify" method="post" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td align="right" class="border2">公司所在地区：</td>
                  <td class="border2">
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan"></select>
<script src="/js/area.js"></script>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>             
             		  </td>
          </tr>
          <tr > 
            <td align="right" class="border">详细地址：</td>
            <td class="border"> 
              <input name="address" id="address" tabindex="4" class="biaodan" value="<?php echo $row['address']?>" size="30" maxlength="50"> 
            </td>
          </tr>
          <tr > 
            <td align="right" class="border2">公司网站：</td>
            <td class="border2"> 
              <input name="homepage" id="homepage" class="biaodan" value="<?php if ($row["homepage"]<>'') { echo  $row["homepage"] ;}else{ echo siteurl.getpageurl('zt',$row['id']);}?>" tabindex="5" size="30" maxlength="100"></td>
          </tr>
          <tr> 
            <td align="right" class="border">企业类别：</td>
            <td class="border">
			<?php
$sqln = "select classid,parentid,classname from zzcms_userclass where parentid<>'0' order by xuhao asc";
$rsn=query($sqln);
?>
<script language = "JavaScript" type="text/JavaScript">
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
            if (subcat[i][1] == locationid){ 
                document.myform.s.options[document.myform.s.length] = new Option(subcat[i][0], subcat[i][2]);
            }        
        }
    }</script>
      <select name="b" size="1" id="b" class="biaodan" onChange="changelocation(document.myform.b.options[document.myform.b.selectedIndex].value)">
        <option value="" selected="selected">请选择大类</option>
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
	  <select name="s" class="biaodan">
      <option value="0">请选择小类</option>
      <?php	  
$sqln="select * from zzcms_userclass where parentid='" .$row["bigclassid"]."' order by xuhao asc";
$rsn=query($sqln);
while($rown = fetch_array($rsn)){
?>
<option value="<?php echo $rown["classid"]?>" <?php if ($rown["classid"]==$row["smallclassid"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
<?php 	  
}
?>
    </select>
			</td>
          </tr>
          <tr> 
           <td width="17%" align="right" class="border2">
		   公司简介：<input name="oldcontent" type="hidden" id="oldcontent" value="<?php echo stripfxg($row["content"])?>">
		   </td>
            <td width="83%" class="border2"> 
              <textarea name="content" id="content"><?php echo stripfxg($row["content"])?></textarea> 
			   <script type="text/javascript" src="/3/ckeditor/ckeditor.js"></script>
			  <script type="text/javascript">CKEDITOR.replace('content');</script> 
            </td>
          </tr>
          <tr> 
            <td height="50" align="right" class="border">上传公司形象图片：<br>（不要超过<?php echo maximgsize?>K） 
                    <input name="img" type="hidden" id="img" value="/image/nopic.gif" tabindex="8"></td>
            <td height="50" class="border">   

	  <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
          <tr align="center" bgcolor="#FFFFFF"> 
            <td id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> <input name="Submit2" type="button"  value="上传图片" /></td>
          </tr>
        </table>
			
            </td>
          </tr>
          <tr> 
            <td align="right" class="border2">联系人性别：</td>
            <td class="border2"> 
             <label> <input name="sex" type="radio" tabindex="9" value="1" <?php if ($row["sex"]==1) { echo 'checked';}?>/>
              先生</label>
              <label><input name="sex" type="radio" tabindex="10" value="0" <?php if ($row["sex"]==0) { echo 'checked';}?> />
             女士</label></td>
          </tr>
          <tr > 
            <td align="right" class="border">联系人QQ号：</td>
            <td class="border"> <input name="qq" id="qq" class="biaodan" value="<?php echo $row['qq']?>" tabindex="11" size="30" maxLength="50"></td>
          </tr>
          <tr > 
            <td align="right" class="border2">联系人手机：</td>
            <td class="border2"> 
              <input name="mobile" id="mobile" class="biaodan" value="<?php echo $row['mobile']?>" tabindex="12" size="30" maxLength="50"></td>
          </tr>
          <tr> 
            <td class="border">&nbsp;</td>
            <td class="border"> <input name="Submit"  type="submit" class="buttons" id="Submit" value="填好了，提交信息！" tabindex="13"> 
            </td>
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