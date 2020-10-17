<?php
ob_start();//打开缓冲区，这样输出内容后还可以setcookie
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
if (str_is_inarr(usergr_power,'zs')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限','null');//不返回到上一页，防止由user/index.php?goto='zsadd.php'过来的造成死循环提示
}
?>
<title>发布<?php echo channelzs ?></title>
<script type="text/javascript" src="../js/gg.js"></script>
<script type="text/javascript" src="../js/swfobject.js"></script> 
<script type="text/javascript" src="../js/jquery.js"></script>  
<script type="text/javascript" src="../js/area.js"></script>
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script type="text/javascript" language="javascript">
$.ajaxSetup ({
cache: false //close AJAX cache
});
 
$(document).ready(function(){  
$("#proname").change(function() { //jquery 中change()函数  
$("#quote").load(encodeURI("/ajax/zstitlecheck_ajax.php?id="+$("#proname").val()));//jqueryajax中load()函数 加encodeURI，否则IE下无法识别中文参数 
});  
});   

function CheckForm(){
if (document.myform.proname.value==""){
	document.myform.proname.focus();
    document.myform.proname.value='此处不能为空';
    document.myform.proname.select();
	return false;
}

if (document.myform.prouse.value==""){
	document.myform.prouse.focus();
    document.myform.prouse.value='此处不能为空';
    document.myform.prouse.select();
	return false;
}  

/*
if (document.myform.sm.value==""){//IE支持不太好，有时加了内容也提示
    alert('说明不能为空');
	return false;
}
*/
  
	ischecked=false;
 	for(var i=0;i<document.myform.bigclassid.length;i++){ 
		if(document.myform.bigclassid[i].checked==true)  {
		 ischecked=true ;
   		} 
	}
   if(document.myform.bigclassid.checked==true)  {
		 ischecked=true ;
   	} 
 	if (ischecked==false){
	alert("请选择大类别！");	
    return false;
	}
	
  if (document.myform.province.value=="请选择省份"){
	document.myform.province.focus();
    alert("请选择省份！");
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

function showinfo(name, n){
	var chList=document.getElementsByName("ch"+name);
	var TextArea=document.getElementById(name);
	if(chList[n-1].checked) //数组从0开始
	{
		temp= TextArea.value; 
		TextArea.value = temp.replace(eval("document.getElementById(name+n).innerHTML"),"");
		TextArea.value+= eval("document.getElementById(name+n).innerHTML")
	}else{
		temp= TextArea.value; 
		TextArea.value = temp.replace(eval("document.getElementById(name+n).innerHTML"),"");
	}
}
function doClick_E(o){
	 var id,e;
	id=0
	 for(var i=1;i<=document.myform.bigclassid.length;i++){
	   id ="E"+i;
	   e = document.getElementById("E_con"+i);
	   if(id != o.id){
	   	 e.style.display = "none";		
	   }else{
		e.style.display = "block";
	   }
	 }
	   if(id==0){
		document.getElementById("E_con1").style.display = "block";
	   }
	 //document.write(classnum)
	 }
 
function ValidSelect(checkboxselect){
var ProductIdList = document.getElementsByName("smallclassid[]");
	var SelectCount=0;
	for(var i =0;i<ProductIdList.length;i++){
		if(ProductIdList[i].checked) SelectCount ++;
		if(SelectCount>3){
			checkboxselect.checked = false;
			alert("产品小类最多只能选择３项!");
			return false;
		}
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
</script>
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

$cpid = isset($_POST['cpid'])?$_POST['cpid']:'0';
checkid($cpid,1);//允许为0


$bigclassid=isset($_POST['bigclassid'])?$_POST["bigclassid"]:0;
checkid($bigclassid);

$smallclassid=isset($_POST['smallclassid'])?$_POST["smallclassid"][0]:0;//加[]可同多选共用同一个JS判断函数uncheckall
checkid($smallclassid);

$smallclassids="";
if(!empty($_POST['smallclassid'])){
    for($i=0; $i<count($_POST['smallclassid']);$i++){
    //$smallclassids=$smallclassids.('"'.$_POST['smallclassid'][$i].'"'.',');//为字符串时的写法
	$smallclassids=$smallclassids.($_POST['smallclassid'][$i].',');
    }
	$smallclassids=substr($smallclassids,0,strlen($smallclassids)-1);//去除最后面的","
}

$shuxing_value="";
	if(!empty($_POST['sx'])){
    for($i=0; $i<count($_POST['sx']);$i++){
	$shuxing_value=$shuxing_value.($_POST['sx'][$i].'|||');
    }
	$shuxing_value=substr($shuxing_value,0,strlen($shuxing_value)-3);//去除最后面的"|||"
	}
$szm =getfirstchar_all($proname); 
$flv=isset($_POST["flv"])?$_POST["flv"]:'';
checkstr($img,"upload");//入库前查上传文件地址是否合格
checkstr($flv,"upload");//入库前查上传文件地址是否合格

$city=$_POST["city"];
if ($city=='请选择城区'){$city='';}
$xiancheng=$_POST["cityforadd"];
if ($xiancheng=='请选择县城'){$xiancheng='';}

$title=isset($_POST["title"])?$_POST["title"]:$proname;
$keyword=isset($_POST["keyword"])?$_POST["keyword"]:$proname;
$description=isset($_POST["description"])?$_POST["description"]:$proname;
$skin=isset($_POST["skin"])?$_POST["skin"]:'';
$rs=query("select groupid,qq,comane,id,renzheng,province,city,xiancheng from zzcms_user where username='".$username."'");
$row=fetch_array($rs);
$groupid=$row["groupid"];
$qq=$row["qq"];
$comane=$row["comane"];
$renzheng=$row["renzheng"];
$userid=$row["id"];
$province_user=$row["province"];
$city_user=$row["city"];
$xiancheng_user=$row["xiancheng"];

//判断是不是重复信息
if ($_POST["action"]=="add" ){
$sql="select id from zzcms_main where proname='".$proname."' and editor='".$username."' ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过该信息，请不要发布重复的信息！');
}
}elseif($_POST["action"]=="modify"){
$sql="select id from zzcms_main where proname='".$proname."' and editor='".$username."' and id<>".$cpid." ";
$rs=query($sql);
$row=num_rows($rs);
if ($row){
showmsg('您已发布过该信息，请不要发布重复的信息！');
}
}

$ranNum=rand(100000,99999);
if ($groupid>1){
$TimeNum=date('Y')+1;
}else{
$TimeNum=date('Y');
}	
$TimeNum=$TimeNum.date("mdHis").$ranNum;
  
if ($_POST["action"]=="add" && $username<>''){//如果$_COOKIE消失不入库
$isok=query("Insert into zzcms_main(proname,bigclassid,smallclassid,smallclassids,szm,tz,prouse,sm,img,flv,province,city,xiancheng,
province_user,city_user,xiancheng_user,zc,yq,shuxing_value,title,keywords,description,sendtime,timefororder,editor,userid,groupid,qq,comane,renzheng,skin)
values
('$proname','$bigclassid','$smallclassid','$smallclassids','$szm','$tz','$prouse','$sm','$img','$flv','$province','$city','$xiancheng',
'$province_user','$city_user','$xiancheng_user','$zc','$yq','$shuxing_value','$title','$keyword','$description','".date('Y-m-d H:i:s')."','$TimeNum','$username','$userid','$groupid','$qq','$comane','$renzheng','$skin')") ;  
$cpid=insert_id();		

}elseif ($_POST["action"]=="modify" && $username<>''){//如果$_COOKIE消失不入库

$oldimg=trim($_POST["oldimg"]);
$oldflv=trim($_POST["oldflv"]);
checkstr($oldimg,"upload");
checkstr($oldflv,"upload");
$isok=query("update zzcms_main set proname='$proname',bigclassid='$bigclassid',smallclassid='$smallclassid',smallclassids='$smallclassids',szm='$szm',tz='$tz',prouse='$prouse',sm='$sm',
img='$img',flv='$flv',province='$province',city='$city',xiancheng='$xiancheng',province_user='$province_user',city_user='$city_user',xiancheng_user='$xiancheng_user',
zc='$zc',yq='$yq',shuxing_value='$shuxing_value',title='$title',keywords='$keyword',description='$description',
sendtime='".date('Y-m-d H:i:s')."',timefororder='$TimeNum',editor='$username',userid='$userid',groupid='$groupid',qq='$qq',comane='$comane',renzheng='$renzheng',
skin='$skin',passed=0 where id='$cpid'");

	if ($oldimg<>$img && $oldimg<>"/image/nopic.gif") {
	//deloldimg
		$f=$oldimg;
		if (file_exists($f)){
		unlink($f);		
		}
		$fs=str_replace(".","_small.",$oldimg);
		if (file_exists($fs)){
		unlink($fs);		
		}
	}
	if ($oldflv<>$flv && $oldflv<>""){
	//deloldflv
		$f="../".$oldflv;
		if (file_exists($f)){
		unlink($f);		
		}
	}			
}
setcookie("bigclassid",$bigclassid,time()+3600*24,"/user");//只在user目录内有效
setcookie("province",$province,time()+3600*24,"/user");
setcookie("city",$city,time()+3600*24,"/user");
setcookie("xiancheng",$xiancheng,time()+3600*24,"/user");
setcookie("zc",$zc,time()+3600*24,"/user");
setcookie("yq",$yq,time()+3600*24,"/user");
setcookie("skin",$skin,time()+3600*24,"/user");
passed("zzcms_main");
$fdir="../web/".$username."/".$cpid;
//创建文件目录
if (!file_exists($fdir)) {
   mkdir($fdir,0777,true);
}
$fp=$fdir."/index.htm";
$f=fopen($fp,"w+");
fwrite($f,stripfxg($sm,true));
fclose($f);	

?>	

<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	名称：<?php echo $proname?><br>
	区域：<?php echo $province.$city?>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $cpid?>">[修改]</a></li>
	<li><a href="zsmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("zs",$cpid)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>
		
	
<?php
}


function add(){
global $f_array;
$tablename="zzcms_main";
include("checkaddinfo.php");
?>

<div class="admintitle">发布<?php echo channelzs?></div>
<form  action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" class="border2" > 产品名称<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <input name="proname" type="text" id="proname" class="biaodan" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};" size="60" maxlength="45" /><br>
<span id="quote"></span>
             </td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border">选择类别<font color="#FF0000">（必填）</font>：</td>
            <td valign="middle" class="border" > 
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择所属大类</legend>
                    <?php
        $sql = "select * from zzcms_zsclass where parentid=0 order by xuhao asc";
		$rs = query($sql); 
		$n=1;
		while($row= fetch_array($rs)){
		if (@$_COOKIE['bigclassid']==$row['classid']){
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='$row[classid]' checked/><label for='E$n'>$row[classname]</label>";
		}else{
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='$row[classid]'/><label for='E$n'>$row[classname]</label>";
		}
		$n ++;
		if (($n-1) % 7==0) {echo "<br/>";}
		}
			?>
                    </fieldset></td>
                </tr>
                <tr> 
                  <td> 
                    <?php
$sql="select * from zzcms_zsclass where parentid=0 order by xuhao asc";
$rs = query($sql); 
$n=1;
while($row= fetch_array($rs)){
if (@$_COOKIE['bigclassid']==$row["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择所属小类</legend>";

$sqln="select * from zzcms_zsclass where parentid='$row[classid]' order by xuhao asc";
$rsn =query($sqln); 
$nn=1;
while($rown= fetch_array($rsn)){
if (zsclass_isradio=='Yes'){
echo "<input name='smallclassid[]' id='radio$nn$n' type='radio' value='$rown[classid]' />";
}else{
echo "<input name='smallclassid[]' id='radio$nn$n' type='checkbox' value='$rown[classid]' onclick='javascript:ValidSelect(this)'/>";
}
echo "<label for='radio$nn$n'>$rown[classname]</label>";
$nn ++;	
if (($nn-1) % 7==0) {echo "<br/>";}
}
echo "</fieldset>";
echo "</div>";
$n ++;
}
?>                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td align="right" class="border" >投资额度：</td>
            <td class="border" ><select name="tz" id="tz">
                <?php
			  $tz=explode("|",tz); 
			  		for ($i=0;$i<count($tz);$i++){ //count取得数组中的项目数 
			   ?>
                <option value="<?php echo $tz[$i]?>" ><?php echo $tz[$i]?></option>
                <?php
				}		
			   ?>
              </select>
            </td>
          </tr>
		  
          <tr>
            <td align="right" class="border2" >主要特点<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" ><textarea name="prouse" cols="60" rows="4" id="prouse" class="biaodan" style="height:auto" onClick="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';" onBlur="javascript:if (this.value=='此处不能为空') {this.value=''};this.style.backgroundColor='';"></textarea></td>
          </tr>
         
		    <?php
	if (shuxing_name!=''){
	$shuxing_name = explode("|",shuxing_name);
	for ($i=0; $i< count($shuxing_name);$i++){
	?>
	<tr>
      <td align="right" class="border" ><?php echo $shuxing_name[$i]?>：</td>
      <td class="border" ><input name="sx[]" type="text" value="" size="45" class="biaodan"></td>
    </tr>
	<?php
	}
	}
	?>
          <tr> 
            <td align="right" class="border" >产品说明<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > 
			<textarea name="sm" id="sm" class="biaodan"></textarea> 
            
			  <script type="text/javascript">CKEDITOR.replace('sm');</script>			</td>
          </tr>
          <tr> 
            <td align="right" class="border2">招商区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border2"> <table border="0" cellpadding="3" cellspacing="0">
                <tr> 
                  <td>
<select name="province" id="province" class="biaodan"></select>
<select name="city" id="city" class="biaodan"></select>
<select name="xiancheng" id="xiancheng" class="biaodan" onChange="addSrcToDestList()"></select>

<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo @$_COOKIE['province']?>', '<?php echo @$_COOKIE["city"]?>', '<?php echo @$_COOKIE["xiancheng"]?>');
</script>
                  <td width="100" align="center" valign="top">已选地区
                    <select name="destList" size="5" multiple="multiple" style="width:100px;height:50px" class="biaodan">
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
                    <input name="cityforadd" type="hidden" id="cityforadd" />                
				  <input name="button" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市" /></td>
                </tr>
                
              </table></td>
          </tr>
          <tr> 
            <td align="right" class="border" >上传图片（小于<?php echo maximgsize?>K）：<br /> 
			 <input name="img" type="hidden" id="img" value="/image/nopic.gif"/>                       </td>
            <td> 
			<table height="140" width="140"  border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> 
				  <input name="Submit2" type="button"  value="上传图片" />				  </td>  
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >上传视频：<br />
              （小于<?php echo maxflvsize?> M,flv格式）<input name="flv" type="hidden" id="flv" /></td>
            <td class="border2" >
			    <?php
if (check_user_power("uploadflv")=="yes"){
?>
			<table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#cccccc">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td id="container"  onClick="openwindow('/uploadflv_form.php',400,300)"> <input name="Submit24" type="button"  value="添加视频" /> </td>
                </tr>
              </table>
			    <?php
		   }else{
		  ?>
		  <table width="140" height="140" border="0" cellpadding="5" cellspacing="1" bgcolor="#ccc">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td id="container" onClick="javascript:window.location.href='vip_add.php'"> <p><img src="../image/jx.gif" width="48" height="48" /><br />
                      仅限收费会员</p>
                    <p><span class='buttons'>现在审请？</span><br />
                    </p></td>
                </tr>
              </table>
			  <?php
			  }
			  ?>			  </td>
          </tr>
        
          <tr> 
            <td align="right" valign="top" class="border2" >可提供的支持：</td>
            <td class="border2" > <textarea name="zc" class="biaodan"  style="height:60px" cols="60" rows="4" id="zc" onFocus="this.select()"><?php if (isset($_COOKIE["zc"]))echo $_COOKIE["zc"];?></textarea> 
              <div> <?php echo $f_array[0]?> </div></td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border" >对<?php echo channeldl ?>商的要求：</td>
            <td class="border" > <textarea name="yq" class="biaodan" style="height:60px" cols="60" rows="4" id="yq" onFocus="this.select()"><?php if (isset($_COOKIE["yq"]))echo $_COOKIE["yq"];?></textarea> 
              <div><?php echo $f_array[1]?> </div></td>
          </tr>
          <tr> 
            <td colspan="2" class="admintitle" >SEO优化设置（如与产品名称相同，以下可以留空不填）</td>
          </tr>
		  	    <?php
if (check_user_power("seo")=="yes"){
?>
          <tr> 
            <td align="right" class="border" >标题（title）：</td>
            <td class="border" ><input name="title" type="text" id="title" class="biaodan"  size="60" maxlength="255" /></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >关键词（keyword）：</td>
            <td class="border2" > <input name="keyword" type="text" id="keyword" class="biaodan"  size="60" maxlength="255" />
              (多个关键词以“,”隔开)</td>
          </tr>
          <tr> 
            <td align="right" class="border" >描述（description）：</td>
            <td class="border" ><input name="description" type="text" id="description" class="biaodan"  size="60" maxlength="255" />
              (适当出现关键词，最好是完整的句子)</td>
          </tr>
		  <?php 
		  }else{
		  ?>
  <tr> 
            <td align="right" class="border" >标题（title）：</td>
            <td class="border" ><input type="text" size="60" maxlength="255" disabled="disabled" value="您所在的用户组没有此权限"/></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >关键词（keyword）：</td>
            <td class="border2" > <input  type="text"  size="60" maxlength="255" value="您所在的用户组没有此权限" disabled="disabled"/>
              (多个关键词以“,”隔开)</td>
          </tr>
          <tr> 
            <td align="right" class="border" >描述（description）：</td>
            <td class="border" ><input type="text"  value="您所在的用户组没有此权限" size="60" maxlength="255" disabled="disabled"/>
             (适当出现关键词，最好是完整的句子)</td>
          </tr>
		   <?php 
		  }
		  ?>	
          <tr>
            <td colspan="2" class="admintitle" >产品展示页模板选择</td>
          </tr>
		     <?php
if (check_user_power("zsshow_template")=="yes"){
?>
          <tr>
            <td align="right" class="border" >选择应用模板：</td>
            <td class="border" >
              <label><input name="skin" type="radio" id="cp" value="cp" <?php if(@$_COOKIE['skin']=='cp'){echo 'checked';}?>/>
            普通模板</label>
              <label><input type="radio" name="skin" value="xm" id="xm" <?php if(@$_COOKIE['skin']=='xm'){echo 'checked';}?>/>
            广告页型模板</label></td>
          </tr>
 <?php 
		  }else{
		  ?>		 
		 <tr>
            <td align="right" class="border" >选择应用模板(您所在的用户组没有此权限)：</td>
            <td class="border" >
              <label><input name="skin" type="radio" id="cp" value="cp" checked="checked" disabled="disabled"/>
            普通模板</label>
              <label><input type="radio" name="skin" value="xm" id="xm" disabled="disabled"/>
            广告页型模板</label></td>
          </tr> 
		 <?php 
		  }
		  ?>		  
          <tr> 
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" > <input name="action" type="hidden" id="action2" value="add" /> 
              <input name="Submit" type="submit" class="buttons" value="填好了，发布信息" /></td>
          </tr>
        </table>
</form>

<?php
}

function modify(){
global $username,$f_array;
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id);

$sql="select * from zzcms_main where id='$id'";
$rs = query($sql); 
$row = fetch_array($rs);
if ($id!=0 && $row["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>
<div class="admintitle">修改<?php echo channelzs?></div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="20%" align="right" class="border" >产品名称<font color="#FF0000">（必填）</font>：</td>
            <td width="80%" class="border" > <input name="proname" type="text" id="proname" class="biaodan" value="<?php echo $row["proname"]?>" size="60" maxlength="45" >            </td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border2" ><br>
            选择类别<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > 
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr> 
                  <td> <fieldset class="fieldsetstyle">
                    <legend>请选择所属大类</legend>
                    <?php
        $sqlB = "select classid,classname from zzcms_zsclass where parentid=0 order by xuhao asc";
		$rsB =query($sqlB); 
		$n=1;
		while($rowB= fetch_array($rsB)){
		if ($row['bigclassid']==$rowB['classid']){
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='".$rowB['classid']."' checked/><label for='E$n'>".$rowB['classname']."</label>";
		}else{
		echo "<input name='bigclassid' type='radio' id='E$n'  onclick='javascript:doClick_E(this);uncheckall()' value='".$rowB['classid']."' /><label for='E$n'>".$rowB['classname']."</label>";
		}
		$n ++;
		if ($n % 7==0) {echo "<br/>";}
		}
			?>
                    </fieldset></td>
                </tr>
                <tr> 
                  <td> 
                    <?php
$sqlB="select classid,classname from zzcms_zsclass where parentid=0 order by xuhao asc";
$rsB =query($sqlB); 
$n=1;
while($rowB= fetch_array($rsB)){

if ($row["bigclassid"]==$rowB["classid"]) {  
echo "<div id='E_con$n' style='display:block;'>";
}else{
echo "<div id='E_con$n' style='display:none;'>";
}
echo "<fieldset class='fieldsetstyle'><legend>请选择所属小类</legend>";
$sqlS="select classid,classname from zzcms_zsclass where parentid='".$rowB['classid']."' order by xuhao asc";
$rsS =query($sqlS); 
$nn=1;
while($rowS= fetch_array($rsS)){
if (zsclass_isradio=='Yes'){
	if ($row['smallclassid']==$rowS['classid']){
	echo "<input name='smallclassid[]' id='radio$nn$n' type='radio' value='".$rowS[classid]."' checked/>";
	}else{
	echo "<input name='smallclassid[]' id='radio$nn$n' type='radio' value='".$rowS[classid]."' />";
	}
}else{
	if (strpos($row['smallclassids'],$rowS['classid'])!==false && $row['bigclassid']==$rowB['classid']){//与招商产品中大类名相同的大类下的小类才会被勾选
	echo "<input name='smallclassid[]' id='radio$nn$n' type='checkbox' value='".$rowS['classid']."' onclick='javascript:ValidSelect(this)' checked/>";
	}else{
	echo "<input name='smallclassid[]' id='radio$nn$n' type='checkbox' value='".$rowS['classid']."' onclick='javascript:ValidSelect(this)'/>";
	}
}
echo "<label for='radio$nn$n'>".$rowS['classname']."</label>";
$nn ++;
if ($nn % 6==0) {echo "<br/>";}            
}
echo "</fieldset>";
echo "</div>";
$n ++;
}
?>                  </td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td align="right" class="border" >投资额度：</td>
            <td class="border" ><select name="tz" id="tz">
                <?php
			  $tz=explode("|",tz); 
			  		for ($i=0;$i<count($tz);$i++){ //count取得数组中的项目数 
			   ?>
                <option value="<?php echo $tz[$i]?>" <?php if($row["tz"]==$tz[$i]){ echo 'selected';}?>><?php echo $tz[$i]?></option>
                <?php
				}		
			   ?>
              </select>
            </td>
          </tr>
		
          <tr> 
            <td align="right" class="border2" >主要特点<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <textarea name="prouse" cols="60" rows="4" class="biaodan" style="height:auto" id="prouse"><?php echo stripfxg($row["prouse"])?></textarea>            </td>
          </tr>
		   <?php
	if (shuxing_name!=''){
	$shuxing_name = explode("|",shuxing_name);
	$shuxing_value = explode("|||",$row["shuxing_value"]);
	for ($i=0; $i< count($shuxing_name);$i++){
	?>
	<tr>
      <td align="right" class="border" ><?php echo $shuxing_name[$i]?>：</td>
      <td class="border" ><input name="sx[]" type="text" value="<?php echo @$shuxing_value[$i]?>" size="45" class="biaodan"></td>
    </tr>
	<?php
	}
	}
	?>
          <tr> 
            <td align="right" class="border" >产品说明<font color="#FF0000">（必填）</font>：</td>
            <td class="border" > 
			<textarea name="sm" id="sm" class="biaodan" >
			<?php 
//$fp="../web/".$username."/".$id."/index.htm";
//if (file_exists($fp)) {			
//$f = fopen($fp,'r');
//$sm = trim(fread($f,filesize($fp)));
//fclose($f);
//echo $sm;//save.php 中已存为正常的HTML文件内容
//}else{
echo stripfxg($row["sm"],true);
//}
?>
			
			</textarea> 
			  <script type="text/javascript">CKEDITOR.replace('sm');</script>			</td>
          </tr>
          <tr> 
            <td align="right" class="border">招商区域<font color="#FF0000">（必填）</font>：</td>
            <td class="border"><table border="0" cellpadding="3" cellspacing="0">
                <tr>
                  <td>

<select name="province" id="province" class="biaodan" ></select>
<select name="city" id="city" class="biaodan" ></select>
<select name="xiancheng" id="xiancheng" onChange="addSrcToDestList()" class="biaodan" ></select>
<script type="text/javascript">
new PCAS('province', 'city', 'xiancheng', '<?php echo $row["province"]?>', '<?php echo $row["city"]?>', '<?php echo $row["xiancheng"]?>');
</script>                 </td>
                 
                  <td width="100" align="center" valign="top">已选地区
                    <select style='width:100px;height:50px' class="biaodan"  size="4" name="destList" multiple="multiple">
                      <?php 
		if ($row["xiancheng"]!="") {
			  if (strpos($row["xiancheng"],",")==0) {?>
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
                      <input name="cityforadd" type="hidden" id="cityforadd" />
                      <input name="button2" type="button" onClick="javascript:deleteFromDestList();" value="删除已选城市|" /></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td align="right" class="border" >上传图片（小于<?php echo maximgsize ?>K）：
 <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["img"] ?>"> 
              <input name="img"type="hidden" id="img" value="<?php echo $row["img"] ?>">                     </td>
            <td class="border" >
			 <table height="140" width="140" border="0" cellpadding="10" cellspacing="1" bgcolor="#999999">
                <tr> 
                  <td  align="center" bgcolor="#FFFFFF" id="showimg"> 
                    <?php
				if($row["img"]<>"/image/nopic.gif"){
				echo "<div style='padding:10px 0'><a href='".$row["img"]."' target='_blank'><img src='".$row["img"]."' border=0 width=120 /></a></div>";
				echo "<div onClick=\"openwindow('/uploadimg_form.php',400,300)\" class='buttons'>点击可更换</div>";
				}else{
				echo "<input name='Submit2' type='button'  value='上传图片' onClick=\"openwindow('/uploadimg_form.php',400,300)\"/>";
				}
				  ?>
				  </td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td align="right" class="border" >上传视频（小于<?php echo maxflvsize?>M,flv格式）：
              <input name="oldflv" type="hidden" id="oldflv2" value="<?php echo $row["flv"] ?>" /> 
              <input name="flv" type="hidden" id="flv" value="<?php echo $row["flv"] ?>" /></td>
            <td class="border" >
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
          var s1 = new SWFObject("../image/player.swf","ply","200","200","9","#FFFFFF");
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
			echo "<input name='Submit2' type='button'  value='上传视频'/>";
			}
				  ?>                  </td>
                </tr>
              </table>
			  <?php
		   }else{
		  ?>
		  <table width="140" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
                <tr align="center" bgcolor="#FFFFFF"> 
                  <td id="container" onClick="javascript:window.location.href='vip_add.php'"> <p><img src="../image/jx.gif" width="48" height="48" /><br />
                      仅限收费会员</p>
                    <p><span class='buttons'>现在审请？</span><br />
                    </p></td>
                </tr>
              </table>
			  <?php
			  }
			  ?>			  </td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border2" >可提供的支持：</td>
            <td class="border2" > <textarea name="zc" cols="60" rows="4" id="zc" class="biaodan" style="height:auto" ><?php echo stripfxg($row["zc"]) ?></textarea> 
              <div>  <?php echo $f_array[0]?> </div></td>
          </tr>
          <tr> 
            <td align="right" valign="top" class="border" >对<?php echo channeldl ?>商的要求：</td>
            <td class="border" > <textarea name="yq" cols="60" rows="4" id="yq" class="biaodan" style="height:auto"><?php echo stripfxg($row["yq"]) ?></textarea> 
              <div> <?php echo $f_array[1]?> </div></td>
          </tr>
          <tr> 
            <td colspan="2" class="admintitle" >SEO优化设置（如与产品名称相同，以下可以留空不填）</td>
          </tr>
		    <?php
		 if (check_user_power("seo")=="yes"){
		 ?>
          <tr> 
            <td align="right" class="border" >标题（title）：</td>
            <td class="border" ><input name="title" type="text" id="title" class="biaodan" value="<?php echo $row["title"] ?>" size="60" maxlength="255"></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >关键词（keyword）：</td>
            <td class="border2" > <input name="keyword" type="text" id="keyword" class="biaodan"  value="<?php echo $row["keywords"] ?>" size="60" maxlength="255">
              (多个关键词以“,”隔开)</td>
          </tr>
          <tr> 
            <td align="right" class="border" >描述（description）：</td>
            <td class="border" ><input name="description" type="text" id="description" class="biaodan"  value="<?php echo $row["description"] ?>" size="60" maxlength="255">
              (适当出现关键词，最好是完整的句子)</td>
          </tr>
 <?php 
		  }else{
		  ?>
  <tr> 
            <td align="right" class="border" >标题（title）：</td>
            <td class="border" ><input type="text" size="60" maxlength="255" disabled="disabled" value="您所在的用户组没有此权限"/></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >关键词（keyword）：</td>
            <td class="border2" > <input  type="text"  size="60" maxlength="255" value="您所在的用户组没有此权限" disabled="disabled"/>
             (多个关键词以“,”隔开)</td>
          </tr>
          <tr> 
            <td align="right" class="border" >描述（description）：</td>
            <td class="border" ><input type="text"  value="您所在的用户组没有此权限" size="60" maxlength="255" disabled="disabled"/>
             (适当出现关键词，最好是完整的句子)</td>
          </tr>
		  <?php 
		  }
		  ?>		  		  
          <tr> 
            <td colspan="2" class="admintitle" >产品展示页模板选择</td>
          </tr>
		  		     <?php
if (check_user_power("zsshow_template")=="yes"){
?>
          <tr>
            <td align="right" class="border" >选择应用模板：</td>
            <td class="border" >
              <label><input type="radio" name="skin" value="cp" id="cp" <?php if ($row["skin"]=='cp'){ echo "checked";}  ?>/>
              普通模板</label>
              <label> <input type="radio" name="skin" value="xm" id="xm" <?php if ($row["skin"]=='xm'){ echo "checked";}  ?>/>
             广告页型模板</label>            </td>
          </tr>
		  
		<?php 
		  }else{
		  ?>		 
		 <tr>
            <td align="right" class="border" >选择应用模板(您所在的用户组没有此权限)：</td>
            <td class="border" >
              <label><input name="skin" type="radio" id="cp" value="cp" checked="checked" disabled="disabled"/>
           普通模板</label>
              <label> <input type="radio" name="skin" value="xm" id="xm" disabled="disabled"/>
           广告页型模板</label></td>
          </tr> 
		 <?php 
		  }
		  ?>		    
		  
          <tr>
            <td align="center" class="border2" >&nbsp;</td>
            <td class="border2" ><input name="cpid" type="hidden"  value="<?php echo $row["id"] ?>" />
              <input name="action" type="hidden"  value="modify" />
              <input name="page" type="hidden" value="<?php echo $page ?>" />
              <input name="Submit" type="submit" class="buttons" value="保存修改结果" /></td>
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