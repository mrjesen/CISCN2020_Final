<?php
include("../inc/conn.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>发布问答信息</title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../3/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>  
<script language="javascript">  
$(document).ready(function(){  
  $("#title").change(function() { //jquery 中change()函数  
	$("#quote").load(encodeURI("/ajax/asktitlecheck_ajax.php?id="+$("#title").val()));//jqueryajax中load()函数 加encodeURI，否则IE下无法识别中文参数 
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
$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:0;
$img=getimgincontent(stripfxg($content,true));
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($_POST["action"]=="add"){
//判断是不是重复信息,为了修改信息时不提示这段代码要放到添加信息的地方
$sql="select title,editor from zzcms_ask where title='".$title."'";
$rs = query($sql); 
$row= num_rows($rs); 
if ($row){
showmsg('此信息已存在，请不要发布重复的信息！');
}

$isok=query("Insert into zzcms_ask(bigclassid,smallclassid,title,content,img,jifen,editor,passed,sendtime) values('$bigclassid','$smallclassid','$title','$content','$img','$jifen','$username',1,'".date('Y-m-d H:i:s')."')");  
query("update zzcms_user set totleRMB=totleRMB-".$jifen." where username='$username'");//减去发布者积分
query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('$username','提问花积分','-".jifen."','','".date('Y-m-d H:i:s')."')");//记录积分
$id=insert_id();		

}elseif ($_POST["action"]=="modify"){

$id=$_POST["id"];
$isok=query("update zzcms_ask set bigclassid='$bigclassid',smallclassid='$smallclassid',title='$title',
content='$content',img='$img',jifen='$jifen',editor='$username',
sendtime='".date('Y-m-d H:i:s')."',passed=1 where id='$id'");	

if ($jifen>$old_jifen){
$jifen_cha=$jifen-$old_jifen;
query("update zzcms_user set totleRMB=totleRMB-".$jifen_cha." where username='$username'");//减去发布者积分
query("insert into zzcms_pay (username,dowhat,RMB,mark,sendtime) values('$username','提问提高悬赏花积分','-".$jifen_cha."','','".date('Y-m-d H:i:s')."')");//记录积分
}

}
passed("zzcms_ask");
?>

<div class="boxsave"> 
    <div class="title"> <?php if ($isok) {echo "发布成功";}else{echo "发布失败";}?></div>
	<div class="content_a">
	标题：<?php echo $title?><br>
	
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="askmanage.php?page=<?php echo $page?>">[返回]</a></li>
	<li><a href="<?php echo getpageurl("ask",$id)?>" target="_blank">[预览]</a></li>
	</div>
	</div>
	</div>

<?php	
}


function add(){
$tablename="zzcms_ask";
include("checkaddinfo.php");
?>
<div class="admintitle">发布问答信息</div>  
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr> 
            <td width="18%" align="right" class="border2">类别<font color="#FF0000">（必填）</font>：</td>
            <td width="82%" class="border2"> 
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
subcat[<?php echo $count?>] = new Array("<?php echo trim($row["classname"])?>","<?php echo trim($row["parentid"])?>","<?php echo trim($row["classid"])?>");
        <?php
        $count = $count + 1;
       }
        ?>
onecount=<?php echo $count ?>;

function changelocation(locationid){
 document.myform.smallclassid.length = 1; 
    for (i=0;i < onecount; i++){
            if (subcat[i][1] == locationid){ 
                document.myform.smallclassid.options[document.myform.smallclassid.length] = new Option(subcat[i][0], subcat[i][2]);
            }        
        }
    }</script>
 <select name="bigclassid" class="biaodan" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
                <option value="" selected="selected">请选择大类别 </option>
                <?php
	$sql = "select * from zzcms_askclass where  parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
		?>
		<option value="<?php echo $row["classid"]?>"><?php echo $row["classname"]?></option>
	<?php 
	}	
	?>		
              </select> 
			  <select name="smallclassid"  class="biaodan">
                <option value="0">不指定小类</option>
              </select></td>
          </tr>
          <tr> 
            <td align="right" class="border">标题<font color="#FF0000">（必填）</font>：</td>
            <td class="border">
			 <input name="title" type="text" id="title" size="50" maxlength="255"  class="biaodan">
			 <span id="quote"></span>
			 </td>
          </tr>
          <tr id="trcontent"> 
            <td align="right" class="border2" >内容<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <textarea name="content" id="content"></textarea> 
			  <script type="text/javascript">CKEDITOR.replace('content');</script></td>
          </tr>
         
          <tr id="trkeywords">
            <td align="right" class="border" >悬赏积分：</td>
            <td class="border" ><select name="jifen" id="jifen">
              <option value="0" selected="selected">0</option>
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="30">30</option>
            </select>
			<?php	   
        $rs=query("select totleRMB from zzcms_user where username='" .$_COOKIE["UserName"]. "'");
		$row=fetch_array($rs);
		echo "您的积分：".$row['totleRMB'];
			?>            </td>
          </tr>
         
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发布">
              <input name="action" type="hidden"  value="add"></td>
          </tr>
        </table>
</form>
<?php
}


function modify(){
global $username;
?>

<div class="admintitle">修改问答信息</div>
<?php
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sqlzx="select * from zzcms_ask where id='$id'";
$rszx =query($sqlzx); 
$rowzx = fetch_array($rszx);
if ($id<>0 && $rowzx["editor"]<>$username) {
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
subcat[<?php echo $count?>] = new Array("<?php echo trim($row["classname"])?>","<?php echo trim($row["parentid"])?>","<?php echo trim($row["classid"])?>");
        <?php
        $count = $count + 1;
       }
        ?>
onecount=<?php echo $count ?>;

function changelocation(locationid){
 document.myform.smallclassid.length = 1; 
    for (i=0;i < onecount; i++){
            if (subcat[i][1] == locationid){ 
                document.myform.smallclassid.options[document.myform.smallclassid.length] = new Option(subcat[i][0], subcat[i][2]);
            }        
        }
    }</script> 
	<select name="bigclassid" class="biaodan" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
                <option value="" selected="selected">请选择大类别</option>
                <?php
	$sql = "select * from zzcms_askclass where  parentid=0 order by xuhao asc";
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
$sql="select classid,classname from zzcms_askclass where parentid='" .$rowzx["bigclassid"]."' order by xuhao asc";
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
			 <input name="title" type="text" class="biaodan" size="50" maxlength="255" value="<?php echo $rowzx["title"]?>" >			 </td>
          </tr>
		 
          <tr id="trcontent"> 
            <td align="right" class="border2" >内容<font color="#FF0000">（必填）</font>：</td>
            <td class="border2" > <textarea name="content" type="hidden" id="content"><?php echo $rowzx["content"]?></textarea> 
              <script type="text/javascript">CKEDITOR.replace('content');	</script>            </td>
          </tr>
          <tr>
            <td align="right" class="border" >悬赏积分：
              <input name="old_jifen" type="hidden" id="old_jifen"  value="<?php echo $rowzx["jifen"] ?>" />
            </td>
            <td class="border" ><select name="jifen" id="jifen">
                <option value="0" <?php if ($rowzx["jifen"]==0){ echo 'selected';}?>>0</option>
                <option value="5" <?php if ($rowzx["jifen"]==5){ echo 'selected';}?>>5</option>
                <option value="10" <?php if ($rowzx["jifen"]==10){ echo 'selected';}?>>10</option>
                <option value="20" <?php if ($rowzx["jifen"]==20){ echo 'selected';}?>>20</option>
                <option value="30" <?php if ($rowzx["jifen"]==30){ echo 'selected';}?>>30</option>
              </select>
                <?php	   
        $rs=query("select totleRMB from zzcms_user where username='" .$_COOKIE["UserName"]. "'");
		$row=fetch_array($rs);
		echo "您的积分：".$row['totleRMB'];
			?>
            </td>
          </tr>
            <td align="right" class="border2">&nbsp;</td>
            <td class="border2"> <input name="Submit" type="submit" class="buttons" value="发布">
              <input name="id" type="hidden"  value="<?php echo $rowzx["id"] ?>" />
              <input name="page" type="hidden"  value="<?php echo $page?>" />
              <input name="action" type="hidden" value="modify" /></td>
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