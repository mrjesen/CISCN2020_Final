<?php
include ("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>  
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script language="javascript">  
$(document).ready(function(){  
  $("#title").change(function() { //jquery 中change()函数  
	$("#quote").load(encodeURI("../ajax/asktitlecheck_ajax.php?id="+$("#title").val()));//jqueryajax中load()函数 加encodeURI，否则IE下无法识别中文参数 
  });  
});  

function CheckForm(){
/*if (document.myform.bigclassid.value==""){
    alert("请选择大类别！");
	document.myform.bigclassid.focus();
	return false;
} */
if (document.myform.title.value==""){
    alert("标题不能为空！");
	document.myform.title.focus();
	return false;
}
  
//创建正则表达式
var re=/^[0-9]*$/;		
	if(document.myform.elite.value==""){
		alert("请输入数值！");
		document.myform.elite.focus();
		return false;
	}
	if(document.myform.elite.value.search(re)==-1)  {
    alert("必须为正整数！");
	document.myform.elite.value="";
	document.myform.elite.focus();
	return false;
  	}
	
	if(document.myform.elite.value>127)  {
    alert("不得大于127");
	document.myform.elite.focus();
	return false;
  	} 
	document.getElementById('loading').style.display='block'; 	    
} 
</script>
</head>

<body>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}


if ($do=="save"){
$page = isset($_POST['page'])?$_POST['page']:1;//只从修改页传来的值
checkid($page);
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id,1);
$passed=isset($_POST["passed"])?$_POST["passed"]:0;
checkid($passed,1);

if (isset($_POST["elite"])){
$elite=$_POST["elite"];
	if ($elite>127){
	$elite=127;
	}elseif ($elite<0){
	$elite=0;
	}
}else{
$elite=0;
}
checkid($elite,1);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:0;
checkid($bigclassid,1);checkid($smallclassid,1);
//---保存内容中的远程图片，并替换内容中的图片地址
$msg='';
$imgs=getimgincontent(stripfxg($content,true),2);
if (is_array($imgs)){
foreach ($imgs as $value) {
	checkstr($value,"upload");//入库前查上传文件地址是否合格
	if (substr($value,0,4) == "http"){
	$value=getimg2($value);//做二次提取，过滤后面的图片样式
	$img_bendi=grabimg($value,"");//如果是远程图片保存到本地
	if($img_bendi):$msg=$msg.  "远程图片：".$value."已保存为本地图片：".$img_bendi."<br>";else:$msg=$msg. "false";endif;
	$img_bendi=substr($img_bendi,strpos($img_bendi,"/uploadfiles"));//在grabimg函数中$img被加了zzcmsroo。这里要去掉
	$content=str_replace($value,$img_bendi,$content);//替换内容中的远程图片为本地图片
	}
}
}
//---end
$img=$_POST["img"];
if ($img==''){//放到内容下面，避免多保存一张远程图片
$img=getimgincontent(stripfxg($content,true));
$img=getimg2($img);
}

if ($img<>''){
checkstr($img,"upload");//入库前查上传文件地址是否合格
	if (substr($img,0,4) == "http"){//$img=trim($_POST["img"])的情况下，这里有可能是远程图片地址
		$img=grabimg($img,"");//如果是远程图片保存到本地
		if($img):$msg=$msg. "远程图片已保存到本地：".$img."<br>";else:$msg=$msg. "false";endif; 
		$img=substr($img,strpos($img,"/uploadfiles"));//在grabimg函数中$img被加了zzcmsroo。这里要去掉 
	}
		
	$imgsmall=str_replace(siteurl,"",getsmallimg($img));
	if (file_exists(zzcmsroot.$imgsmall)===false && file_exists(zzcmsroot.$img)!==false){//小图不存在，且大图存在的情况下，生成缩略图
	makesmallimg($img);//同grabimg一样，函数里加了zzcmsroot
	}	
}

if ($_REQUEST["action"]=="add"){
checkadminisdo("ask_add");
//判断是不是重复信息,为了修改信息时不提示这段代码要放到添加信息的地方
//$sql="select title,editor from zzcms_zx where title='".$title."'";
//$rs = query($sql); 
//$row= fetch_array($rs);
//if ($row){
//showmsg('此信息已存在，请不要发布重复的信息！','zx_add.php');
//}
$isok=query("insert into zzcms_ask(bigclassid,smallclassid,title,content,img,editor,elite,passed,sendtime) values('$bigclassid','$smallclassid','$title','$content','$img','管理员','$elite','$passed','".date('Y-m-d H:i:s')."')");  
$id=insert_id();	

}elseif ($_REQUEST["action"]=="modify"){
checkadminisdo("ask_modify");
$isok=query("update zzcms_ask set bigclassid='$bigclassid',smallclassid='$smallclassid',title='$title',content='$content',img='$img',
sendtime='".date('Y-m-d H:i:s')."',elite='$elite',passed='$passed' where id='$id'");	
}

setcookie("askbigclassid",$bigclassid);
setcookie("asksmallclassid",$smallclassid);
?>
<div class="boxsave"> 
    <div class="title">
	<?php
	if ($_REQUEST["action"]=="add") {echo "添加 ";}else{echo"修改";}
	if ($isok){echo"成功";}else{echo "失败";}
     ?>
	</div>
	<div class="content_a">
	名称：<?php echo $title?><br/>
	推荐： <?php if ($elite<>0){echo "是" ;}else{ echo "否" ;}?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="ask_manage.php?b=<?php echo $bigclassid?>&page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("ask",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php 
if ($msg<>'' ){echo "<div class='border'>" .$msg."</div>";}
}


function add(){
//checkadminisdo("ask_add");
?>
<div class="admintitle">发布问答</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="12%" align="right" class="border" >所属类别</td>
      <td width="88%" class="border" > 
        <?php
$sql = "select * from zzcms_askclass where parentid<>0 order by xuhao asc";
$rs=query($sql);
?>
<script language = "JavaScript" type="text/JavaScript">
var onecount;
subcat = new Array();
        <?php 
        $count = 0;
        while($row = fetch_array($rs)){
        ?>
subcat[<?php echo $count?>] = new Array("<?php echo $row["classname"]?>","<?php echo $row["parentid"]?>","<?php echo $row["classid"]?>");
        <?php
        $count = $count + 1;
       }
        ?>
onecount=<?php echo $count ?>;
function changelocation(locationid){
    document.myform.smallclassid.length = 1; 
    for (i=0;i < onecount; i++){
    if (subcat[i][1] == locationid){ document.myform.smallclassid.options[document.myform.smallclassid.length] = new Option(subcat[i][0], subcat[i][2]);}
    }
    }
	</script> 
<select name="bigclassid" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
<option value="">请选择大类别</option>
    <?php
	$sql = "select * from zzcms_askclass where parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
	?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==@$_COOKIE["askbigclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
  <?php
	}
	?>
</select> 
<select name="smallclassid">
<option value="">不指定小类</option>
<?php
if ($_COOKIE["askbigclassid"]!=""){
$sql="select * from zzcms_askclass where parentid=" .$_COOKIE["askbigclassid"]." order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==@$_COOKIE["asksmallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
<?php
}
}
?>
      </select></td>
    </tr>
    <tr> 
      <td align="right" class="border" >标题 </td>
      <td class="border" > 
        <input name="title" type="text" id="title" size="50" maxlength="255">
		<span id="quote"></span>	
		</td>
    </tr>
    <tr> 
      <td align="right" class="border" >内容</td>
      <td class="border" ><textarea name="content" type="hidden" id="content"></textarea> 
        <script type="text/javascript">CKEDITOR.replace('content');	</script>      </td>
    </tr>
    <tr>
      <td align="right" class="border" >封面图片</td>
      <td class="border" ><input name="img" type="text" id="title2" size="50" maxlength="255" />
        （如果内容中有图片，这里可以留空，会自动获取内容中的第一张图片）</td>
    </tr>
    <tr>
      <td colspan="2" class="admintitle2" >属性设置</td>
    </tr>
    <tr> 
      <td align="right" class="border" >审核</td>
      <td class="border" ><input name="passed" type="checkbox" id="passed" value="1">
      （选中为通过审核）</td>
    </tr>
    <tr>
      <td align="right" class="border" >推荐值</td>
      <td class="border" ><input name="elite" type="text" id="elite" value="0" size="4" maxlength="4">
(0-127之间的数字，数值大的排在前面) </td>
    </tr>
    <tr> 
      <td align="right" class="border" >&nbsp;</td>
      <td class="border" > <input type="submit" name="Submit" value="发 布" >
      <input name="action" type="hidden" id="action" value="add" /></td>
    </tr>
  </table>
</form>	
<?php
}

function modify(){
checkadminisdo("ask_modify");
?>
<div class="admintitle">修改问答信息</div>
<?php
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$rszx = query("select * from zzcms_ask where id='$id'"); 
$rowzx= fetch_array($rszx);
?>
<form action="ask.php?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr> 
      <td width="12%" align="right" class="border">所属类别</td>
      <td width="88%" class="border"> 
        <?php
$sql = "select parentid,classid,classname from zzcms_askclass where parentid<>0 order by xuhao asc";
$rs=query($sql);
?>
        <script language = "JavaScript" type="text/JavaScript">
var onecount;
subcat = new Array();
        <?php 
        $count = 0;
        while($row = fetch_array($rs)){
        ?>
subcat[<?php echo $count?>] = new Array("<?php echo trim($row["classname"])?>","<?php echo trim($row["parentid"])?>","<?php echo trim($row["classid"])?>");
        <?php
        $count = $count + 1;
       }
        ?>
onecount=<?php echo $count ?>;

function changelocation(locationid){
    document.myform.smallclassid.length = 1; 
    for (i=0;i < onecount; i++){
    if (subcat[i][1] == locationid){ document.myform.smallclassid.options[document.myform.smallclassid.length] = new Option(subcat[i][0], subcat[i][2]);}        
    }
    }</script> <select name="bigclassid" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
          <option value="" selected="selected">请选择大类别</option>
          <?php
	$sql = "select classid,classname from zzcms_askclass where  parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
	?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzx["bigclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
          <?php
				}
				?>
        </select> <select name="smallclassid">
          <option value="0">不指定小类</option>
          <?php
$sql="select classid,classname from zzcms_askclass where parentid='" .$rowzx["bigclassid"]."' order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
	?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzx["smallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
        <?php
		 }
		?>
        </select> </td>
    </tr>
    <tr> 
      <td align="right" class="border">标题</td>
      <td class="border"> 
        <input name="title" type="text" id="title2" value="<?php echo $rowzx["title"]?>" size="50" maxlength="255">      </td>
    </tr>
    <tr id="trcontent"> 
      <td width="12%" align="right" class="border">内容</td>
      <td class="border"> <textarea name="content" id="content" ><?php echo stripfxg($rowzx["content"])?></textarea> 
        <script type="text/javascript">CKEDITOR.replace('content');	</script> 
        <input name="id" type="hidden" id="id" value="<?php echo $rowzx["id"]?>"> 
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"> </td>
    </tr>
    <tr id="trkeywords">
      <td align="right" class="border" >封面图片</td>
      <td class="border" ><input name="img" type="text" id="title2" value="<?php echo $rowzx["img"]?>" size="50" maxlength="255">
      （如果内容中有图片，这里可以留空，会自动获取内容中的第一张图片）</td>
    </tr>
    <tr id="trkeywords">
      <td colspan="2" class="admintitle2" >属性设置</td>
    </tr>
    <tr>
      <td align="right" class="border">审核</td>
      <td class="border"><input name="passed[]" type="checkbox" id="passed[]" value="1"  <?php if ($rowzx["passed"]==1) { echo "checked";}?>>
        （选中为通过审核） </td>
    </tr>
    <tr> 
      <td align="right" class="border">推荐值</td>
      <td class="border"> <input name="elite" type="text" id="elite" value="<?php echo $rowzx["elite"]?>" size="4" maxlength="3">
        (0-127之间的数字，数值大的排在前面) </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> <input type="submit" name="Submit" value="提交">
      <input name="action" type="hidden" id="action" value="modify"></td>
    </tr>
  </table>
</form> 
<?php
}
?> 
<div id='loading' style="display:none">正在保存，请稍候...</div>
</body>
</html>