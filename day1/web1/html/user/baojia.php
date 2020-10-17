<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发报价</title>
<script src="../js/area.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.cp.value==""){alert("请填写产品！");document.myform.cp.focus();return false;}
if (document.myform.classid.value==""){alert("请选择产品类别！");document.myform.classid.focus();return false;}  
if (document.myform.province.value=="请选择省份"){alert("请选择意向省份！");document.myform.province.focus();return false;}
if (document.myform.price.value==""){alert("请填写价格！");document.myform.price.focus();return false;}
if (document.myform.danwei.value==""){alert("请填写计价单位！");document.myform.danwei.focus();return false;}
//定义正则表达式部分
var strP=/^\d+(\.\d+)?$/;
if(!strP.test(document.myform.price.value)) {
alert("价格只能填数字！"); 
document.myform.price.focus(); 
return false; 
}
if (document.myform.truename.value==""){alert("请填写真实姓名！");document.myform.truename.focus();return false;}  
if (document.myform.tel.value==""){alert("请填写联系电话！");document.myform.tel.focus();return false;}  
}
</SCRIPT>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
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
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}

if ($do=="save"){
$page = isset($_POST['page'])?$_POST['page']:1;//返回列表页用
checkid($page);
$id = isset($_POST['id'])?$_POST['id']:'0';
checkid($id,1);
if ($city=='请选择城区')$city='';
if ($xiancheng=='请选择县城')$xiancheng='';
$companyname=$_POST["company"];

if ($_POST["action"]=="add"){
if ($cp<>'' && $truename<>'' && $tel<>''){
$isok=query("Insert into zzcms_baojia(classid,cp,province,city,price,danwei,companyname,truename,tel,address,email,sendtime,editor) values('$classid','$cp','$province','$city','$price','$danwei','$companyname','$truename','$tel','$address','$email','".date('Y-m-d H:i:s')."','$username')") ;   
$id=insert_id();	
}	
}elseif ($_POST["action"]=="modify"){
checkstr($tel,'tel');
$isok=query("update zzcms_baojia set classid='$classid',cp='$cp',province='$province',city='$city',price='$price',danwei='$danwei',companyname='$companyname',truename='$truename',tel='$tel',address='$address',email='$email',sendtime='".date('Y-m-d H:i:s')."' where id='$id'");
}
passed("zzcms_baojia",$classid);	

?>
<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	名称：<?php echo $cp?><br/>
	报价地区：<?php echo $province.$city?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="baojiamanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("baojia",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
global $username;
$tablename="zzcms_baojia";
include("checkaddinfo.php");
?>
<div class="admintitle">发布报价信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">      
  <table width="100%" border="0" cellpadding="8" cellspacing="1">
    <tr> 
      <td width="18%" align="right" class="border">产品<font color="#FF0000">（必填）</font>：</td>
      <td width="82%" class="border"> <input name="cp" type="text" id="cp" class="biaodan" size="60" maxlength="60">	     </td>
    </tr>
    <tr> 
      <td align="right" class="border2">类别<font color="#FF0000">（必填）</font>：</td>
      <td class="border2">
	   <select name="classid" class="biaodan">
          <option value="0" selected>请选择类别 </option>
          <?php
		$sql="select * from zzcms_zsclass where parentid=0";
		$rs=query($sql);
		while($row= fetch_array($rs)){
			?>
          <option value="<?php echo $row["classid"]?>"><?php echo $row["classname"]?></option>
          <?php
		  }
		  ?>
        </select> </td>
    </tr>
    <tr> 
      <td align="right" class="border">区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border">
			<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan" onChange="addSrcToDestList()"></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '', '', '');
</script>			</td>
    </tr>
    <tr>
      <td align="right" class="border">价格<font color="#FF0000">（必填）</font>：</td>
      <td class="border"><input name="price" type="text" id="price" value="" size="10" maxlength="50" class="biaodan">
        (填数字) </td>
    </tr>
    <tr>
      <td align="right" class="border2">计价单位<font color="#FF0000">（必填）</font>：</td>
      <td class="border2"><input name="danwei" type="text" id="danwei" value="元/" size="10" maxlength="50" class="biaodan"/>
        (如：元/瓶)</td>
    </tr>
	<?php
	$sql="select * from zzcms_user where username='".$username."'";
	$rs=query($sql);
	$row= fetch_array($rs);
	?>
    <tr style="display:none" id='submenu1'>
      <td align="right" class="border">公司名称：</td>
      <td class="border"><input name="company" type="text" class="biaodan" value="<?php echo $row["comane"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border2">真实姓名<font color="#FF0000">（必填）</font>：</td>
      <td class="border2">
<input name="truename" type="text" id="truename" class="biaodan" value="<?php echo $row["somane"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">电话<font color="#FF0000">（必填）</font>：</td>
      <td class="border"><input name="tel" type="text" id="tel" class="biaodan" value="<?php echo $row["phone"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border2">地址：</td>
      <td class="border2">
<input name="address" type="text" id="address" class="biaodan" value="<?php echo $row["address"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">E-mail：</td>
      <td class="border"><input name="email" type="text" id="email" class="biaodan" value="<?php echo $row["email"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> 
        <input name="Submit" type="submit" class="buttons" value="发 布">
        <input name="action" type="hidden" id="action3" value="add"></td>
    </tr>
  </table>
</form>
<?php
}

function modify(){
global $username;
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sql="select * from zzcms_baojia where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<div class="admintitle">报价修改</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border" >产品<font color="#FF0000">（必填）</font>：</td>
            <td width="82%" class="border" >
			 <input name="cp" type="text" id="cp" class="biaodan" value="<?php echo $row["cp"]?>" size="60" maxlength="45" >			</td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border2" >类别<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" ><select name="classid" class="biaodan">
                <option value="0" selected="selected">请选择类别</option>
                <?php
		$sqln="select classid,classname from zzcms_zsclass where parentid=0";
		$rsn=query($sqln);
		while($rown= fetch_array($rsn)){
			if ($rown["classid"]==$row["classid"]){
			echo "<option value='".$rown['classid']."' selected>".$rown["classname"]."</option>";
			}else{
			echo "<option value='".$rown['classid']."'>".$rown["classname"]."</option>";
			}	
		 }
		  ?>
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border" >区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border" >
			<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan" onChange="addSrcToDestList()"></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>			</td>
          </tr>
          <tr>
            <td align="right" class="border">价格<font color="#FF0000">（必填）</font>：</td>
            <td class="border"><input name="price" type="text" id="price" value="<?php echo $row["price"]?>" size="10" maxlength="50" />
              (填数字) </td>
          </tr>
          <tr>
            <td align="right" class="border2">计价单位<font color="#FF0000">（必填）</font>：</td>
            <td class="border2"><input name="danwei" type="text" id="danwei" value="<?php echo $row["danwei"]?>" size="10" maxlength="50" />
              (如：元/瓶)</td>
          </tr>
          <tr> 
            <td align="right" class="border">公司名称：</td>
            <td class="border"><input name="company" type="text" id="company" class="biaodan" value="<?php echo $row["companyname"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border2">真实姓名<font color="#FF0000">（必填）</font>：</td>
            <td class="border2"> <input name="truename" type="text" id="truename" class="biaodan" value="<?php echo $row["truename"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border">电话<font color="#FF0000">（必填）</font>：</td>
            <td class="border"><input name="tel" type="text" id="tel" class="biaodan" value="<?php echo $row["tel"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border2">地址：</td>
            <td class="border2"> <input name="address" type="text" id="address" class="biaodan" value="<?php echo $row["address"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border">E-mail：</td>
            <td class="border"><input name="email" type="text" id="email" class="biaodan" value="<?php echo $row["email"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" > <input name="id" type="hidden" value="<?php echo $row["id"] ?>"> 
			
              <input name="action" type="hidden" id="action" value="modify"> 
              <input name="page" type="hidden" id="page" value="<?php echo $page ?>"> 
              <input name="Submit" type="submit" class="buttons" value="修改"></td>
          </tr>
        </table>
		</form>
		<?php
}
?>
</div>
</div>
</div>
</div>
</body>
</html>