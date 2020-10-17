<?php
ob_start();//打开缓冲区，这样输出内容后还可以setcookie
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发<?php echo channeldl?></title>
<script src="../js/area.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.cp.value==""){alert("请填写意向产品！");document.myform.cp.focus();return false;}
if (document.myform.classid.value==""){alert("请选择产品类别！");document.myform.classid.focus();return false;}  
if (document.myform.province.value=="请选择省份"){alert("请选择意向省份！");document.myform.province.focus();return false;}
if (document.myform.content.value==""){alert("请填写自我介绍！");document.myform.content.focus();return false;}
if (document.myform.truename.value==""){alert("请填写真实姓名！");document.myform.truename.focus();return false;}  
if (document.myform.tel.value==""){alert("请填写代联系电话！");document.myform.tel.focus();return false;}  

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

if ($cp=='' || $truename=='' || $tel==''){
WriteErrMsg('有必填项，未填！');
}else{
$page = isset($_POST['page'])?$_POST['page']:1;//返回列表页用
checkid($page);
$id = isset($_POST['dlid'])?$_POST['dlid']:'0';
checkid($id,1);//允许为0

$xiancheng=$_POST["cityforadd"];
$companyname = isset($_POST['companyname'])?$_POST['companyname']:'';
if ($dlsf=="个人" ){$companyname="";}
//checkyzm($_POST["yzm"]);

if ($_POST["action"]=="add"){
$isok=query("Insert into zzcms_dl(classid,cpid,cp,province,city,xiancheng,content,company,companyname,dlsname,tel,address,email,sendtime,editor) values('$classid',0,'$cp','$province','$city','$xiancheng','$content','$dlsf','$companyname','$truename','$tel','$address',
'$email','".date('Y-m-d H:i:s')."','$username')") ;  
$id=insert_id();	
	
}elseif ($_POST["action"]=="modify"){
$isok=query("update zzcms_dl set classid='$classid',cp='$cp',province='$province',city='$city',xiancheng='$xiancheng',
content='$content',company='$dlsf',companyname='$companyname',dlsname='$truename',tel='$tel',address='$address',email='$email',
sendtime='".date('Y-m-d H:i:s')."' where id='$id'");
}

setcookie("content",$content,time()+3600*24,"/user");//只在user目录内有效
setcookie("bigclassid",$classid,time()+3600*24,"/user");
setcookie("province",$province,time()+3600*24,"/user");
setcookie("city",$city,time()+3600*24,"/user");
setcookie("xiancheng",$xiancheng,time()+3600*24,"/user");

passed("zzcms_dl",$classid);
?>
<div class="boxsave"> 
    <div class="title">
	 <?php
	if ($_REQUEST["action"]=="add") {echo "添加"; }else{ echo "修改";}
	if ($isok) {echo "成功"; }else{ echo"失败";}
	 ?>
	</div>
	<div class="content_a">
	名称：<?php echo $cp?><br>
	意向区域：<?php echo $province.$city.$xiancheng?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="dlmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("dl",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}
}

function add(){
global $username;
$tablename="zzcms_dl";//checkaddinfo中用
include("checkaddinfo.php");
?>

<div class="admintitle">发<?php echo channeldl?></div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">      
  <table width="100%" border="0" cellpadding="3" cellspacing="1">
    <tr> 
      <td width="18%" align="right" class="border">意向产品<font color="#FF0000">（必填）</font>：</td>
      <td width="82%" class="border">
	  <input name="cp" type="text" id="cp" class="biaodan" size="60" maxlength="60" onFocus="javascript:if (this.value=='只能写产品名称，不要写联系方式等内容，否则会直接被删除') {this.value=''}" value="只能写产品名称，不要写联系方式等内容，否则会直接被删除">	        </td>
    </tr>
    <tr> 
      <td align="right" class="border2">产品类别<font color="#FF0000">（必填）</font>：</td>
      <td class="border2">
	   <select name="classid" class="biaodan">
          <option value='0' selected>请选择类别 </option>
          <?php
		$sql="select * from zzcms_zsclass where parentid=0";
		$rs=query($sql);
		while($row= fetch_array($rs)){
			?>
<option value="<?php echo $row["classid"]?>"<?php if (@$_COOKIE['bigclassid']==$row["classid"]){echo 'selected';}?>><?php echo $row["classname"]?></option>
          <?php
		  }
		  ?>
        </select> </td>
    </tr>
    <tr> 
      <td align="right" class="border">意向区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border"><table border="0" cellpadding="3" cellspacing="0">
              <tr>
                <td>
                   
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan" onChange="addSrcToDestList()"></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo @$_COOKIE['province']?>','<?php echo @$_COOKIE['city']?>','<?php echo @$_COOKIE['xiancheng']?>');
</script>                </td>
                <td align="center" valign="top">已选城市<br/>
                  <select name="destList" size="4" multiple="multiple" style='width:100px;height:60px' class="biaodan">
                  <?php 
			  if (isset($_COOKIE['xiancheng'])){
			  		if (strpos($_COOKIE["xiancheng"],",")==0) {?>
                     <option value="<?php echo $_COOKIE["xiancheng"]?>"><?php echo $_COOKIE["xiancheng"]?></option>
                     <?php 
					 }else{
			  		$selectedcity=explode(",",$_COOKIE["xiancheng"]);
						for ($i=0;$i<count($selectedcity);$i++){    ?>
                  		<option value="<?php echo $selectedcity[$i]?>"><?php echo $selectedcity[$i]?></option>
                      	<?php 
						}
					}
			}
			?>
				  </select>
                    <input name="cityforadd" type="hidden" id="cityforadd" /><br/>
                    <input name="button" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市" /></td>
              </tr>
            </table></td>
    </tr>
    <tr> 
      <td align="right" class="border2">自我介绍<font color="#FF0000">（必填）</font>：</td>
      <td class="border2">
	  <textarea name="content" cols="60" rows="4" id="content" class="biaodan" style="height:auto" onFocus="javascript:if (this.value=='最多200字') {this.value=''}"/><?php if( isset($_COOKIE["content"])){echo $_COOKIE["content"];}else{echo "最多200字";}?></textarea>      </td>
    </tr>
	<?php
	$sql="select * from zzcms_user where username='".$username."'";
	$rs=query($sql);
	$row= fetch_array($rs);
	?>
    <tr> 
      <td align="right" class="border">身份：</td>
      <td class="border"><label><input name="dlsf" type="radio" value="公司" onClick="showsubmenu(1)">
         公司</label>
         <label for="dlsf_person"><input name="dlsf" type="radio" onClick="hidesubmenu(1)" value="个人" checked>
         个人</label></td>
    </tr>
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

$sql="select * from zzcms_dl where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id<>0 && $row["editor"]<>$username) {
markit();showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<div class="admintitle">修改<?php echo channeldl?></div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border" >意向产品<font color="#FF0000">（必填）</font>：</td>
            <td width="82%" class="border" > <input name="cp" type="text" id="cp" class="biaodan" value="<?php echo $row["cp"]?>" size="60" maxlength="45" >
			</td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border2" >产品类别<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" ><select name="classid" class="biaodan">
                <option value="" selected="selected">请选择类别</option>
                <?php
		$sqln="select * from zzcms_zsclass where parentid=0";
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
            <td align="right" class="border" >意向区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border" ><table border="0" cellpadding="3" cellspacing="0">
              <tr>
                <td>
                  
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan" onChange="addSrcToDestList()"></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row['province']?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>                </td>
               
                <td align="center" valign="top">已选城市<br/>
                  <select style='width:100px;height:60px' size="4" name="destList" multiple="multiple" class="biaodan">
                      <?php 
		if ($row["xiancheng"]!="") {
			  if (strpos($row["city"],",")==0) {?>
                      <option value="<?php echo $row["xiancheng"]?>"><?php echo $row["xiancheng"]?></option>
                      <?php }else{
			  	$selectedcity=explode(",",$row["xiancheng"]);
				for ($i=0;$i<count($selectedcity);$i++){    
				?>
                      <option value="<?php echo $selectedcity[$i]?>"><?php echo $selectedcity[$i]?></option>
                      <?php }
				}
		}
			?>
                    </select>
                    <input name="cityforadd" type="hidden" id="cityforadd" /><br/>
                    <input name="button2" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市" /></td>
              </tr>
            </table></td>
          </tr>
          <tr> 
            <td align="right" class="border" >自我介绍<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > <textarea name="content" class="biaodan" style="height:auto" cols="60" rows="4" id="content"><?php echo $row["content"] ?></textarea></td>
          </tr>
          <tr> 
            <td align="right" class="border">身份：</td>
            <td class="border"><label><input name="dlsf" type="radio" value="公司" onClick="showsubmenu(1)" <?php if ($row["company"]=="公司") {echo "checked";}?>>公司 </label> 
<label><input type="radio" name="dlsf" value="个人" onClick="hidesubmenu(1)" <?php if ($row["company"]=="个人") {echo "checked";}?>> 
              个人</label>			  </td>
          </tr>
          <tr <?php if ($row["company"]=='个人') {echo " style='display:none'";}?> id='submenu1'> 
            <td align="right" class="border">公司名称：</td>
            <td class="border"><input name="company" type="text" id="company" class="biaodan" value="<?php echo $row["companyname"]?>" size="45" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border2">真实姓名<font color="#FF0000">（必填）</font>：</td>
            <td class="border2"> <input name="truename" type="text" id="truename" class="biaodan" value="<?php echo $row["dlsname"]?>" size="45" maxlength="255" /></td>
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
            <td class="border2" > <input name="dlid" type="hidden" id="ypid2" value="<?php echo $row["id"] ?>"> 
              <input name="action" type="hidden" value="modify"> 
              <input name="page" type="hidden" id="action" value="<?php echo $page ?>"> 
              <input name="Submit" type="submit" class="buttons" value="保存修改"></td>
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