<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" language="JavaScript">
$.ajaxSetup ({
cache: false //close AJAX cache
});

function CheckForm(){
if (document.myform.cp.value==""){
    alert("请填写您要求<?php echo channeldl?>的产品名称！");
	document.myform.cp.focus();
	return false;
  }
if (document.myform.classid.value==""){
    alert("请选择产品类别！");
	document.myform.classid.focus();
	return false;
  }  
  if (document.myform.province.value=="请选择省份"){
    alert("请选择要<?php echo channeldl?>的省份！");
	document.myform.province.focus();
	return false;
  }
  
if (document.myform.truename.value==""){
    alert("请填写真实姓名！");
	document.myform.truename.focus();
	return false;
}  
 
if (document.myform.tel.value==""){
    alert("请填写代联系电话！");
	document.myform.tel.focus();
	return false;
}  
  
if (document.myform.yzm.value==""){
    alert("请输入验证问题的答案！");
	document.myform.yzm.focus();
	return false;
}
  
var v = '';
for(var i = 0; i < document.myform.destList.length; i++){
if(i==0){
v = document.myform.destList.options[i].text;
}else{
v += ','+document.myform.destList.options[i].text;
}
}
//alert(v);
document.myform.cityforadd.value=v; 
}

function showsubmenu(sid){
whichEl = eval("submenu" + sid);
if (whichEl.style.display == "none"){
eval("submenu" + sid + ".style.display=\"\";");
}
}

function hidesubmenu(sid){
whichEl = eval("submenu" + sid);
if (whichEl.style.display == ""){
eval("submenu" + sid + ".style.display=\"none\";");
}
}
</SCRIPT>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}


if ($do=="save"){
$error=0;
$msg='';
$page=isset($_POST["page"])?$_POST["page"]:1;//只从修改页传来的值
checkid($page);
$id=isset($_POST["id"])?$_POST["id"]:0;
checkid($id,1);

$passed=isset($_POST["passed"])?$_POST["passed"]:0;
checkid($passed,1);

$classid=isset($_POST["classid"])?$_POST["classid"]:0;
checkid($classid,1);
$city=$_POST["cityforadd"];

$companyname=isset($_POST["companyname"])?$_POST["companyname"]:"";
if ($dlsf=="个人" ){$companyname="";}


if ($cp=='' || $truename=='' || $tel==''){
$error=1;
$msg=$msg.'<li>请完整填写表单内容</li>';
}

if ($error==1){
WriteErrMsg($msg);
}else{

	if ($_POST["action"]=="add"){
	checkadminisdo("dl_add");
	$isok=query("insert into zzcms_dl(classid,cpid,cp,province,city,content,company,companyname,dlsname,tel,address,email,sendtime) 		
	values('$classid',0,'$cp','$province','$city','$content','$dlsf','$companyname','$truename','$tel','$address','$email','".date('Y-m-d H:i:s')."')") ; 
	$id=insert_id();  

	}elseif ($_POST["action"]=="modify"){
	checkadminisdo("dl_modify");
	$oldprovince=trim($_POST["oldprovince"]);
	if ($province=='请选择省份'){$province=$oldprovince;}
	$isok=query("update zzcms_dl set classid='$classid',cp='$cp',province='$province',city='$city',content='$content',company='$dlsf',companyname='$companyname',
	dlsname='$truename',tel='$tel',address='$address',email='$email',sendtime='".date('Y-m-d H:i:s')."',passed='$passed' where id='$id'");
	}
	if ($isok){echo "<script>location.href='dl_manage.php?page=".$page."'</script>";}		
}

}

function add(){
?>
<div class="admintitle">发布<?php echo channeldl?>信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">      
  <table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr> 
      <td align="right" class="border">想要<?php echo channeldl?>的产品</td>
      <td class="border"> <input name="cp" type="text" id="cp" size="45" maxlength="45">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">产品类别</td>
      <td class="border">
	  <?php
	$sql="select * from zzcms_zsclass where parentid=0";
	$rs=query($sql);
	$row=num_rows($rs);
		if (!$row){
		echo "<a href='class2.php?tablename=zzcms_zsclass'>添加类别</a>";
		}else{
	  ?>
	   <select name="classid">
          <option value="0" selected>请选择类别</option>
          <?php
		while($row= fetch_array($rs)){
			?>
      <option value="<?php echo $row["classid"]?>"<?php if (@$_SESSION['bigclassid']==$row["classid"]){echo 'selected';}?>><?php echo $row["classname"]?></option>
          <?php
		  }
		  ?>
        </select>
		 <?php
		  }
		  ?>
		 </td>
    </tr>
    <tr> 
      <td width="130" align="right" class="border"><?php echo channeldl?>区域</td>
            <td class="border"><table border="0" cellpadding="3" cellspacing="0">
              <tr>
                <td><script language="JavaScript" type="text/javascript">
function addSrcToDestList() {
destList = window.document.forms[0].destList;
city = window.document.forms[0].xiancheng;
var len = destList.length;
for(var i = 0; i < city.length; i++) {
if ((city.options[i] != null) && (city.options[i].selected)) {
var found = false;
for(var count = 0; count < len; count++) {
if (destList.options[count] != null) {
if (city.options[i].text == destList.options[count].text) {
found = true;
break;
}
}
}
if (found != true) {
destList.options[len] = new Option(city.options[i].text);
len++;
}
}
}
}
function deleteFromDestList() {
var destList = window.document.forms[0].destList;
var len = destList.options.length;
for(var i = (len-1); i >= 0; i--) {
if ((destList.options[i] != null) && (destList.options[i].selected == true)) {
destList.options[i] = null;
}
}
} 


$(document).ready(function(){  
	$("#tel").change(function() { //jquery 中change()函数  
	$("#telcheck").load(encodeURI("/ajax/dltelcheck_ajax.php?id="+$("#tel").val()));//jqueryajax中load()函数 加encodeURI，否则IE下无法识别中文参数 
	});  
});  
</script>
                   <select name="province" id="province"></select>
<select name="city" id="city"></select>
<select name="xiancheng" id="xiancheng" onChange="addSrcToDestList()"></select>
<script src="/js/area.js"></script>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo @$_SESSION['province']?>', '<?php echo @$_SESSION["city"]?>', '');
</script>
                </td>
               
                <td width="100" align="center" valign="top">已选城市
                  <select name="destList" size="3" multiple="multiple" style='width:100px;font-size:13px'>
                      <?php if (isset($_SESSION['city'])){?>
                      <option value="<?php echo $_SESSION['city']?>" ><?php echo $_SESSION['city']?></option>
                      <?php
				  }
				  ?>
                  </select>
                    <input name="cityforadd" type="hidden" id="cityforadd" />
                    <input name="button" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市" /></td>
              </tr>
            </table></td>
    </tr>
    <tr> 
      <td width="130" align="right" class="border"><?php echo channeldl?>商介绍</td>
      <td class="border"> <textarea name="content" cols="45" rows="6" id="content"><?php echo @$_SESSION["content"]?></textarea>      </td>
    </tr>
	
    <tr> 
      <td align="right" class="border"><?php echo channeldl?>身份</td>
      <td class="border"><input name="dlsf" id="dlsf_company" type="radio" value="公司" onClick="showsubmenu(1)">
         <label for="dlsf_company">公司 </label>
        <input name="dlsf" type="radio" id="dlsf_person" onClick="hidesubmenu(1)" value="个人" checked>
          <label for="dlsf_person">个人</label></td>
    </tr>
    <tr style="display:none" id='submenu1'>
      <td align="right" class="border">公司名称</td>
      <td class="border"><input name="company" type="text" id="yzm2" value="" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">真实姓名</td>
      <td class="border">
<input name="truename" type="text" id="truename" value="" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">电话</td>
      <td class="border">
	  <input name="tel" type="text" id="tel" value="" size="45" maxlength="255" />
	  <span id="telcheck"></span>
	  </td>
    </tr>
    <tr> 
      <td align="right" class="border">地址</td>
      <td class="border">
<input name="address" type="text" id="address" value="" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">E-mail</td>
      <td class="border"><input name="email" type="text" id="email" value="" size="45" maxlength="255" /></td>
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
checkadminisdo("dl");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
$sql="select * from zzcms_dl where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle"> 修改<?php echo channeldl?>信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr> 
      <td align="right" class="border"><?php echo channeldl?>产品</td>
      <td class="border"> <input name="cp" type="text" id="cp" value="<?php echo $row["cp"]?>" size="45" maxlength="45">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">产品类别</td>
      <td class="border"> 
	   <?php
		$sqln = "select classid,classname from zzcms_zsclass where parentid=0 order by xuhao asc";
	    $rsn=query($sqln);
        $rown=num_rows($rsn);
		if (!$rown){
			echo "<a href='class2.php?tablename=zzcms_zsclass'>添加类别</a>";
		}else{
		?>
		<select name="classid" id="classid">
                <option value="0" selected="selected">请选择类别</option>
                <?php
		while($rown= fetch_array($rsn)){
			?>
                <option value="<?php echo $rown["classid"]?>" <?php if ($rown["classid"]==$row["classid"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
                <?php
		  }
		  ?>
              </select>
		<?php
		}
		?>         </td>
    </tr>
    <tr> 
      <td width="130" align="right" class="border"><?php echo channeldl?>区域</td>
      <td class="border"><table border="0" cellpadding="3" cellspacing="0">
        <tr>
          <td><script language="JavaScript" type="text/javascript">
function addSrcToDestList() {
destList = window.document.forms[0].destList;
city = window.document.forms[0].xiancheng;
var len = destList.length;
for(var i = 0; i < city.length; i++) {
if ((city.options[i] != null) && (city.options[i].selected)) {
var found = false;
for(var count = 0; count < len; count++) {
if (destList.options[count] != null) {
if (city.options[i].text == destList.options[count].text) {
found = true;
break;
}
}
}
if (found != true) {
destList.options[len] = new Option(city.options[i].text);
len++;
}
}
}
}
function deleteFromDestList() {
var destList = window.document.forms[0].destList;
var len = destList.options.length;
for(var i = (len-1); i >= 0; i--) {
if ((destList.options[i] != null) && (destList.options[i].selected == true)) {
destList.options[i] = null;
}
}
} 
              </script>
<select name="province" id="province"></select>
<select name="city" id="city"></select>
<select name="xiancheng" id="xiancheng" onChange="addSrcToDestList()"></select>
<script src="/js/area.js"></script>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '');
</script>            
              
            <input name="oldprovince" type="hidden" id="oldprovince" value="<?php echo $row["province"]?>" /></td>
        
          <td width="100" align="center" valign="top">已选城市
            <select style='width:100px;font-size:13px' size="4" name="destList" multiple="multiple">
                <?php 
		if ($row["city"]!="" &&  $row["city"]!="全国") {
			  if (strpos($row["city"],",")==0) {?>
                <option value="<?php echo $row["city"]?>"><?php echo $row["city"]?></option>
                <?php }else{
			  	$selectedcity=explode(",",$row["city"]);
				for ($i=0;$i<count($selectedcity);$i++){    
				?>
                <option value="<?php echo $selectedcity[$i]?>"><?php echo $selectedcity[$i]?></option>
                <?php }
				}
		}
			?>
              </select>
              <input name="cityforadd" type="hidden" id="cityforadd" />
              <input name="button2" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市" /></td>
        </tr>
      </table></td>
    </tr>
    <tr> 
      <td width="130" align="right" class="border">内容</td>
      <td class="border"> 
        <textarea name="content" cols="45" rows="6" id="content"><?php echo stripfxg($row["content"])?></textarea> 
        <input name="id" type="hidden" id="dlid" value="<?php echo $row["id"]?>">
        <input name="page" type="hidden" id="page" value="<?php echo $page?>">      </td>
    </tr>
    <tr> 
      <td align="right" class="border"><?php echo channeldl?>身份</td>
      <td class="border"><label>
	  <input name="dlsf" type="radio" value="公司" onClick="showsubmenu(1)" <?php if ($row["company"]=="公司") { echo "checked";}?>> 
        公司 </label> 
		<label>
		<input type="radio" name="dlsf" value="个人" onClick="hidesubmenu(1)" <?php if ($row["company"]=="个人"){ echo "checked";}?>> 
        个人</label></td>
    </tr>
    <tr <?php if ($row["company"]=="个人"){ echo " style='display:none'";}?> id='submenu1'> 
      <td align="right" class="border">公司名称</td>
      <td class="border"><input name="company" type="text" id="company" value="<?php echo $row["companyname"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">真实姓名</td>
      <td class="border"> 
        <input name="truename" type="text" id="truename" value="<?php echo $row["dlsname"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">电话</td>
      <td class="border"><input name="tel" type="text" id="tel" value="<?php echo $row["tel"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">地址</td>
      <td class="border"> 
        <input name="address" type="text" id="address" value="<?php echo $row["address"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr> 
      <td align="right" class="border">E-mail</td>
      <td class="border"><input name="email" type="text" id="email" value="<?php echo $row["email"]?>" size="45" maxlength="255" /></td>
    </tr>
    <tr>
      <td align="right" class="border">审核</td>
      <td class="border"><input name="passed" type="checkbox" id="passed" value="1"  <?php if ($row["passed"]==1) { echo "checked";}?>>
        （选中为通过审核） </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> 
        <input name="Submit" type="submit" class="buttons" value="修 改">
        <input name="action" type="hidden" id="action3" value="modify"></td>
    </tr>
  </table>
</form>
<?php
}
?>
</body>
</html>