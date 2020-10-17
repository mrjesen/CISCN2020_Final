<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发布专题信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<?php
if (str_is_inarr(usergr_power,'special')=="no" && $usersf=='个人'){
showmsg('个人用户没有此权限','null');//设为null提示后，不返回到上一页，防止由user/index.php?goto='zsadd.php'过来的造成死循环提示
}
?>
<script type="text/javascript" src="../js/jquery.js"></script> 
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script> 
<script language="javascript">  
$(document).ready(function(){  
  $("#title").change(function() { //jquery 中change()函数  
	$("#quote").load(encodeURI("../ajax/SpecialTitleCheck_ajax.php?id="+$("#title").val()));//jqueryajax中load()函数 加encodeURI，否则IE下无法识别中文参数 
  });  
});  

function CheckForm(){
if (document.myform.bigclassid.value==""){
    alert("请选择大类名称！");
	document.myform.bigclassid.focus();
	return false;
  }
if (document.myform.title.value==""){
    alert("标题不能为空！");
	document.myform.title.focus();
	return false;
  } 
}  

</script>
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
$id = isset($_POST['id'])?$_POST['id']:0;
checkid($id);
$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
checkid($bigclassid,1);
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:0;

$bigclassname="";
$smallclassname="";
if ($bigclassid!=0){
$rs = query("select classname from zzcms_specialclass where classid='$bigclassid'"); 
$row= fetch_array($rs);
$bigclassname=$row["classname"];
}

if ($smallclassid!=0){
$rs = query("select classname from zzcms_specialclass where classid='$smallclassid'"); 
$row= fetch_array($rs);
$smallclassname=$row["classname"];
}

$img=getimgincontent(stripfxg($content,true));
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($keywords=="" ){$keywords=$title;}

if ($_POST["action"]=="add"){
//判断是不是重复信息,为了修改信息时不提示这段代码要放到添加信息的地方
$sql="select title,editor from zzcms_special where title='".$title."'";
$rs = query($sql); 
$row= num_rows($rs); 
if ($row){
showmsg('此信息已存在，请不要发布重复的信息！');
}

$isok=query("insert into zzcms_special(bigclassid,bigclassname,smallclassid,smallclassname,title,laiyuan,keywords,description,groupid,jifen,content,img,editor,sendtime)
values('$bigclassid','$bigclassname','$smallclassid','$smallclassname','$title','$laiyuan','$keywords','$description',
'$groupid','$jifen','$content','$img','$username','".date('Y-m-d H:i:s')."')");  
$id=insert_id();		
}elseif ($_POST["action"]=="modify"){
$isok=query("update zzcms_special set bigclassid='$bigclassid',bigclassname='$bigclassname',smallclassid='$smallclassid',smallclassname='$smallclassname',title='$title',laiyuan='$laiyuan',
keywords='$keywords',description='$description',groupid='$groupid',jifen='$jifen',content='$content',img='$img',editor='$username',
sendtime='".date('Y-m-d H:i:s')."',passed=0 where id='$id'");	
}
passed("zzcms_special");
?>
<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	标题：<?php echo $title?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="specialmanage.php?bigclassid=<?php echo $bigclassid?>&page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("special",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php
}

function add(){
$tablename="zzcms_special";
include("checkaddinfo.php");
?>
<div class="admintitle">发布专题信息</div>  
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border2">类别<font color="#FF0000">（必填）</font>：</td>
            <td width="82%" class="border2"> 
              <?php
$sql = "select * from zzcms_specialclass where parentid<>0 order by xuhao asc";
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
}
</script> 
<select name="bigclassid" class="biaodan" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
                <option value="" selected="selected">请选择大类别</option>
                <?php
	$sql = "select * from zzcms_specialclass where parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
		?>
		<option value="<?php echo $row["classid"]?>"><?php echo $row["classname"]?></option>
		<?php 
	}	
		?>		
              </select> 
			  <select name="smallclassid" class="biaodan">
                <option value="0">不指定小类</option>	  
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">标题<font color="#FF0000">（必填）</font>：</td>
            <td class="border">			 <input name="title" type="text" id="title" class="biaodan" size="50" maxlength="255"> 
			  <span id="quote"></span></td>
          </tr>
       
          <tr id="trlaiyuan"> 
            <td align="right" class="border2" >信息来源：</td>
            <td class="border2" > <input name="laiyuan" type="text" class="biaodan" value="<?php echo sitename?>" size="50" maxlength="50" /></td>
          </tr>
          <tr id="trcontent"> 
            <td align="right" class="border2" >内容<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <textarea name="content" id="content"></textarea> 
			  <script type="text/javascript">CKEDITOR.replace('content');</script>            </td>
          </tr>
          <tr id="trseo">
            <td colspan="2" class="admintitle" >SEO优化设置</td>
          </tr>
          <tr id="trkeywords">
            <td align="right" class="border" >关键词（keywords）</td>
            <td class="border" ><input name="keywords" type="text" id="keywords" class="biaodan" size="50" maxlength="50" /></td>
          </tr>
          <tr id="trdescription">
            <td align="right" class="border2" >描述（description）:</td>
            <td class="border2" ><input name="description" type="text" id="description" class="biaodan" size="50" maxlength="50" /></td>
          </tr>
          <tr id="trquanxian">
            <td colspan="2" class="admintitle" >浏览权限设置</td>
          </tr>
          <tr id="trquanxian2">
            <td align="right" class="border" >&nbsp;</td>
            <td class="border" ><select name="groupid" class="biaodan">
                <option value="0">全部用户</option>
                <?php
		  $rs=query("select * from zzcms_usergroup ");
		  while($row = fetch_array($rs)){
		  	echo "<option value='".$row["groupid"]."'>".$row["groupname"]."</option>";
		  }
	 ?>
              </select>
                <select name="jifen" id="jifen" class="biaodan">
                <option value="0">请选择无权限用户是否可用积分查看</option>
                  <option value="0" >无权限用户不可用积分查看</option>
                  <option value="10" >付我10积分可查看</option>
                  <option value="20" >付我20积分可查看</option>
                  <option value="30" >付我30积分可查看</option>
                  <option value="50" >付我50积分可查看</option>
                  <option value="100" >付我100积分可查看</option>
                  <option value="200" >付我200积分可查看</option>
                  <option value="500" >付我500积分可查看</option>
                  <option value="1000">付我1000积分可查看</option>
                </select>
            </td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发布">
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

$sqlzx="select * from zzcms_special where id='$id'";
$rszx =query($sqlzx); 
$rowzx = fetch_array($rszx);
if ($id!=0 && $rowzx["editor"]<>$username) {
markit();
showmsg('非法操作！警告：你的操作已被记录！小心封你的用户及IP！');
}
?>	  
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border2">类别<font color="#FF0000">（必填）</font>：</td>
            <td width="82%" class="border2"> 
              <?php
$sql = "select parentid,classid,classname from zzcms_specialclass where parentid<>0 order by xuhao asc";
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
    }
	</script> 
<select name="bigclassid" class="biaodan" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
<option value="" selected="selected">请选择大类别</option>
    <?php
	$sql = "select * from zzcms_specialclass where parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
	?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzx["bigclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
    <?php
	}
	?>
              </select> <select name="smallclassid" class="biaodan">
                <option value="0">不指定小类</option>
                <?php
$sql="select * from zzcms_specialclass where parentid='" .$rowzx["bigclassid"]."' order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzx["smallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
<?php
}
?>
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">标题<font color="#FF0000">（必填）</font>：</td>
			
            <td class="border">
			 <input name="title" type="text" class="biaodan" size="50" maxlength="255" value="<?php echo $rowzx["title"]?>" >
			 <span id="quote"></span> </td>
          </tr>
		
          <tr> 
            <td align="right" class="border2" >信息来源：</td>
            <td class="border2" > <input name="laiyuan" type="text" id="laiyuan" class="biaodan" value="<?php echo sitename?>" size="50" maxlength="50" /></td>
          </tr>
          <tr> 
            <td align="right" class="border2" >内容<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <textarea name="content" type="hidden" id="content"><?php echo stripfxg($rowzx["content"])?></textarea> 
              <script type="text/javascript">CKEDITOR.replace('content');	</script>            </td>
          </tr>
          <tr>
            <td colspan="2" class="admintitle" >SEO优化设置</td>
          </tr>
          <tr>
            <td align="right" class="border2" >关键词（keywords）：</td>
            <td class="border2" ><input name="keywords" type="text" id="keywords" class="biaodan" size="50" maxlength="50" value="<?php echo $rowzx["keywords"]?>" /></td>
          </tr>
          <tr>
            <td align="right" class="border" >描述（description）：</td>
            <td class="border" ><input name="description" type="text" id="description" class="biaodan" size="50" maxlength="500" value="<?php echo $rowzx["description"]?>" /></td>
          </tr><tr id="trquanxian">
      <td colspan="2" class="admintitle" >浏览权限设置</td>
    </tr>
    <tr id="trquanxian2"> 
      <td align="right" class="border" >&nbsp;</td>
      <td class="border" > <select name="groupid" class="biaodan">
          <option value="0">全部用户</option>
          <?php
		$rs=query("select * from zzcms_usergroup ");
		while($row = fetch_array($rs)){
			if ($rowzx["groupid"]== $row["groupid"]) {
		  	echo "<option value='".$row["groupid"]."' selected>".$row["groupname"]."</option>";
			}else{
			echo "<option value='".$row["groupid"]."'>".$row["groupname"]."</option>";
			}
		}
	 ?>
        </select> <select name="jifen" id="jifen" class="biaodan">
          <option value="0">请选择无权限用户是否可用积分查看</option>
           <option value="0" <?php if ($rowzx["jifen"]==0) { echo "selected";}?>>无权限用户不可用积分查看</option>
          <option value="10" <?php if ($rowzx["jifen"]==10) { echo "selected";}?>>付我10积分可查看</option>
          <option value="20" <?php if ($rowzx["jifen"]==20) { echo "selected";}?>>付我20积分可查看</option>
          <option value="30" <?php if ($rowzx["jifen"]==30) { echo "selected";}?>>付我30积分可查看</option>
          <option value="50" <?php if ($rowzx["jifen"]==50) { echo "selected";}?>>付我50积分可查看</option>
          <option value="100" <?php if ($rowzx["jifen"]==100) { echo "selected";}?>>付我100积分可查看</option>
          <option value="200" <?php if ($rowzx["jifen"]==200) { echo "selected";}?>>付我200积分可查看</option>
          <option value="500" <?php if ($rowzx["jifen"]==500) { echo "selected";}?>>付我500积分可查看</option>
          <option value="1000" <?php if ($rowzx["jifen"]==1000) { echo "selected";}?>>付我1000积分可查看</option>
        </select> </td>
    </tr>
            <td align="right" class="border2">&nbsp;</td>
            <td class="border2"> <input name="Submit" type="submit" class="buttons" value="发 布">
              <input name="id" type="hidden" id="id" value="<?php echo $rowzx["id"] ?>" /> 
              <input name="page" type="hidden" id="page" value="<?php echo $page?>" />
              <input name="action" type="hidden" id="action" value="modify" /></td>
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