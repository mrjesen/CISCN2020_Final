<?php
include("../inc/conn.php");
include("../inc/fy.php");
include("check.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/<?php echo siteskin_usercenter?>/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="/js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
  if (document.myform2.content.value==""){
    alert("内容不能为空！");
	//document.myform.content.focus();
	return false;
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
<div class="admintitle">已返馈的信息</div>
<?php	  
if( isset($_GET["page"]) && $_GET["page"]!="") {$page=$_GET['page'];}else{$page=1;}
checkid($page,0);
$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select * from zzcms_usermessage where editor='".$username."' ";
$rs = query($sql); 
$totlenum= num_rows($rs);  
$totlepage=ceil($totlenum/$page_size);		
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo '暂无信息<br/><br/>';
}else{
?>
<form name="myform" method="post" action="del.php">
<table width="100%" border="0" cellpadding="5" cellspacing="1" class="bgcolor">
    <tr class="trtitle"> 
      <td width="70%" class="border">内容/回复</td>
      <td width="5%" align="center" class="border">删除</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td>
	  <div style="border-bottom:dotted 1px #b4cced;"><span style="float:right"><?php echo $row["sendtime"]?></span><?php echo stripfxg($row["content"],false,true)?></div>
	  <div style="color:green">
	  <?php 
	  if ($row["reply"]<>''){
	  ?>
	  <span style="float:right"><?php echo $row["replytime"]?></span><?php echo $row["reply"]?></div>
	  <?php 
	  }else{
	  echo '暂无回复';
	  }
	  ?>
	  </td>
      <td align="center" class="docolor"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
    </tr>
<?php
}
?>
  </table>

<div class="fenyei">
<?php echo showpage()?> 
<input name="chkAll" type="checkbox" id="chkAll" onclick="CheckAll(this.form)" value="checkbox" />
          <label for="chkAll">全选</label>
          <input name="submit"  type="submit" class="buttons"  value="删除" onclick="return ConfirmDel()" />
          <input name="pagename" type="hidden" id="pagename" value="message.php?page=<?php echo $page ?>" />
          <input name="tablename" type="hidden" id="tablename" value="zzcms_usermessage" />
        </div>
</form>
<?php
}
?>
  <div class="admintitle">我要返馈信息</div>
  <form action="?" method="post" name="myform2" id="myform2" onsubmit="return CheckForm()" >
        <table width="100%" border="0" cellpadding="3" cellspacing="1">
          <tr id="trcontent"> 
            <td width="15%" align="right" class="border" >内容 <font color="#FF0000">（必填）</font>：</td>
            <td width="85%" class="border" > 
			<textarea name="content" cols="100" rows="5" id="content" onpropertychange="if(value.length>200) value=value.substr(0,200)"></textarea> 
              </td>
          </tr>
         
          <tr> 
            <td align="right" class="border">&nbsp;</td>
            <td class="border"> <input name="Submit" type="submit" class="buttons" value="发布">
              <input name="editor" type="hidden" id="editor2" value="<?php echo $username?>" />
              <input name="action" type="hidden" id="action3" value="add"></td>
          </tr>
        </table>
</form>
<?php 
if (isset($_POST["action"])){
$content=trim($_POST["content"]);
$editor=trim($_POST["editor"]);
//判断是不是重复信息,为了修改信息时不提示这段代码要放到添加信息的地方
$sql="select content,editor from zzcms_usermessage where content='".$content."'";
$rs = query($sql); 
$row= num_rows($rs); 
if ($row){
echo "<script lanage='javascript'>alert('此信息已存在，请不要发布重复的信息！');</script>";
}else{
	$sql="select sendtime from zzcms_usermessage where editor='".$editor."' order by id desc limit 1";
	$rs = query($sql); 
	$row= fetch_array($rs); 
	$second=time()-strtotime($row['sendtime']);
	if ($second<60){//
	echo "<script lanage='javascript'>alert('留言时间，间隔太短');</script>";
	}else{
	query("Insert into zzcms_usermessage(content,editor,sendtime) values('$content','$editor','".date('Y-m-d H:i:s')."')"); 
	echo "<script lanage='javascript'>location.replace('message.php')</script>"; 
	}
}
}

?>
</div>
</div>
</div>
</div>
</body>
</html>