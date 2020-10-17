<?php
include ("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
 if (document.myform.title.value==""){
    alert("主题不能为空！");
	document.myform.title.focus();
	return false;
  }
  if (document.myform.content.value==""){
    alert("内容不能为空！");
	document.myform.content.focus();
	return false;
  }
} 
</script>
</head>
<body>
<?php
switch (@$do){
case "add";add();break;
case "modify";modify();break;
case "save";save();break;
default;show();
}

function save(){
checkadminisdo("sendmessage");
global $action,$id,$sendto,$title,$content;

if ($action=="add"){
query("INSERT INTO zzcms_message (sendto,title,content,sendtime)VALUES('$sendto','$title','$content','".date('Y-m-d H:i:s')."')");
}elseif ($action=="modify") {
checkid($id);
query("update zzcms_message set sendto='$sendto',title='$title',content='$content' where id='$id'");	
}
echo "<script>location.href='?'</script>";
}

function show(){
checkadminisdo("sendmessage");
global $action,$saver,$id;
checkid($id);
if (@$action=="del"){
query("delete from zzcms_message where id='$id'");
echo "<script>location.href='?page=".$page."'</script>";
}
?>
<div class="admintitle">短信息管理</div>
<div class="border2">
<input type="submit" class="buttons" onClick="javascript:location.href='?do=add'" value="发短信息">
</div>

<?php
$sql="select * from zzcms_message order by id desc"; 
$rs=query($sql);
$row=num_rows($rs);
if (!$row){
echo "暂无信息";
}else{
?>
<table width="100%" border="0" cellspacing="1" cellpadding="5">
  <tr class="trtitle"> 
    <td width="5%" align="center">ID</td>
    <td width="10%">标题</td>
    <td width="10%" align="center" >发布时间</td>
    <td width="10%" align="center">接收人</td>
    <td width="10%" align="center" >是否查看</td>
    <td width="5%" align="center" >操作</td>
  </tr>
<?php
while($row = fetch_array($rs)){
?>
   <tr class="trcontent">  
    <td align="center"><?php echo $row["id"]?></td>
    <td ><?php echo $row["title"]?></td>
    <td align="center"><?php echo $row["sendtime"]?></td>
    <td align="center"><?php echo $row["sendto"]?></td>
    <td align="center"><?php if ($row["looked"]==1) { echo"已查看";} else{ echo"<font color=red>未查看</font>";}?></td>
    <td align="center" class="docolor"><a href="?do=modify&id=<?php echo $row["id"]?>">修改</a> 
      | <a href="?action=del&id=<?php echo $row["id"]?>" onClick="return ConfirmDel();">删除</a></td>
  </tr>
<?php
}
?>
</table>
<?php
}
}



function add(){
checkadminisdo("sendmessage");
global $saver;
?>
<div class="admintitle">发送信息</div>
<form action="?do=add" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
          <tr> 
            
      <td width="16%" align="right" class="border">收件人(用户名)：</td>
            <td width="84%" class="border"><input name="sendto" type="text" value="<?php echo $saver?>"  size="50">
            (如果为空则发送给全部用户) </td>
          </tr>
          <tr> 
            <td align="right" class="border">标题：</td>
            <td class="border"> <input name="title"  type="text" size="50"></td>
          </tr>
          <tr> 
            <td align="right" class="border">内容：</td>
            <td class="border"> <textarea name="content" cols="50" rows="10" ></textarea> 
            
            </td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input type="submit" name="Submit" value="发送">
            <input name="action" type="hidden"  value="add"></td>
          </tr>
        </table>
</form>
	<?php
}



function modify(){
?>
<div class="admintitle">修改短信息</div>
<?php
checkadminisdo("sendmessage");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
$sql="select * from zzcms_message where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr> 
            <td width="16%" align="right" class="border">收信人：</td>
            <td width="84%" class="border"><input name="sendto" type="text"  value="<?php echo $row["sendto"]?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="border">标题：</td>
            <td class="border"> <input name="title" type="text"  value="<?php echo $row["title"]?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" class="border">内容：</td>
            <td class="border"> <textarea name="content" cols="50" rows="10" id="content"><?php echo $row["content"]?></textarea> 
            
              <input name="id" type="hidden" id="id" value="<?php echo $row["id"]?>">
              <input name="action" type="hidden"  value="modify"></td>
          </tr>
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"><input type="submit" name="Submit" value="提交"></td>
          </tr>
        </table>
</form>
<?php
}
?>	  	   
</body>
</html>