<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
  <style type="text/css" rel="stylesheet">
   #movediv{
  position:absolute;border:1px solid #dddddd;background:#ffffff;
  padding:10px;
    cursor:move;
    left:200px;
    top:100px;
   }
  </style>

<script language="JavaScript" src="../js/gg.js"></script>
  <script language="javascript" type="text/javascript">
   var _IsMousedown = 0;
   var _ClickLeft = 0;
   var _ClickTop = 0;
   function moveInit(divID,evt){
    _IsMousedown = 1;
    if(getBrowserType() == "NSupport"){
     return;
    }
    var obj = getObjById(divID);
    if(getBrowserType() == "fox"){
     _ClickLeft = evt.pageX - parseInt(obj.style.left);
     _ClickTop = evt.pageY - parseInt(obj.style.top);
    }else{
     _ClickLeft = evt.x - parseInt(obj.style.left);
     _ClickTop = evt.y - parseInt(obj.style.top);
    }
   }
   function Move(divID,evt){
    if(_IsMousedown == 0){
     return;
    }
    var objDiv = getObjById(divID);
    if(getBrowserType() == "fox"){
     objDiv.style.left = evt.pageX - _ClickLeft;
     objDiv.style.top = evt.pageY - _ClickTop;
    }else{
     objDiv.style.left = evt.x - _ClickLeft;
     objDiv.style.top = evt.y - _ClickTop;
    }
    
   }
   function stopMove(){
    _IsMousedown = 0;
   }
   function getObjById(id){
    return document.getElementById(id);
   }
   function getBrowserType(){
    var browser=navigator.appName
    var b_version=navigator.appVersion
    var version=parseFloat(b_version)
    //alert(browser);
    if ((browser=="Netscape")){
     return "fox";
    }
    else if(browser=="Microsoft Internet Explorer"){
     if(version>=4){
      return "ie4+";
     }else{
      return "ie4-";
     }
    }else{
     return "NSupport";
    }
   }
  </script>
</head>
<body>
<div class="admintitle">来自用户的短信息</div>
<?php 
if (@$_GET["step"]==2){
?>

<div id="movediv" style="left:200px;top:200px;" onMouseDown="moveInit('movediv',event);" onMouseMove="Move('movediv',event)" onMouseUp="stopMove()" onMouseOut="stopMove()">
 反馈内容：<?php echo $_GET["content"]?>
  <form name="form1" method="post" action="?action=reply">
      <textarea name="reply" cols="50" rows="5" id="reply"></textarea>
      <input name="message_id" type="hidden" value="<?php echo $_GET["id"]?>">
      <br>
      <input type="submit" name="Submit" value="回复">
      <input type="button" name="Submit2"  onClick="javascript:location.href='?'" value="取消回复">
  </form>
</div>
<?php
}

$action=isset($_REQUEST["action"])?$_REQUEST["action"]:'';
if ($action=="reply"){
$id=$_REQUEST["message_id"];
$reply=$_POST["reply"];
query("update zzcms_usermessage set reply='$reply',replytime='".date('Y-m-d H:i:s')."' where id='$id'");
//echo "<script>location.href='?'<//script>" ;
}

checkadminisdo("sendmessage");
$page=isset($page)?$page:1;
checkid($page);

$reply=isset($_GET["reply"])?$_GET["reply"]:'';

$page_size=pagesize_ht;  //每页多少条数据
$offset=($page-1)*$page_size;
$sql="select * from zzcms_usermessage ";
if ($reply=='no'){
$sql=$sql."where reply is null ";
}
$rs = query($sql); 
$totlenum= num_rows($rs);  
$totlepage=ceil($totlenum/$page_size);		
$sql=$sql . " order by id desc limit $offset,$page_size";
$rs = query($sql); 
if(!$totlenum){
echo "暂无信息";
}else{
?>
<form name="myform" method="post" action="del.php">
<table width="100%" border="0" cellpadding="5" cellspacing="1">
    <tr class="trtitle"> 
      <td width="70%">内容/回复</td>
      <td width="5%" align="center">删除</td>
    </tr>
<?php
while($row = fetch_array($rs)){
?>
    <tr class="trcontent"> 
      <td>
	  <div style="border-bottom:solid 1px #dddddd;padding:5px;margin-bottom:5px">
	  <span style="float:right"><?php echo $row["sendtime"]?></span>内容：<?php echo stripfxg($row["content"],false,true)?>
	  </div>
	  <div style="padding:5px">
	  <?php 
	  if ($row["reply"]<>''){
	  ?>
	  <span style="float:right"><?php echo $row["replytime"]?></span><a href='?step=2&id=<?php echo $row["id"]?>&content=<?php echo $row["content"]?>' style="color:green">回复：<?php echo stripfxg($row["reply"],false,true)?></a>
	  
	  </div>
	  <?php 
	  }else{
	  echo "<a href='?step=2&id=".$row["id"]."&content=".$row["content"]."'>我来回复</a>";
	  }
	  ?>
	 
	  </td>
      <td align="center" class="docolor"><input name="id[]" type="checkbox" id="id" value="<?php echo $row["id"]?>" /></td>
    </tr>
<?php
}
?>
  </table>

<div class="border" style="text-align:right">
页次：<strong><font color="#CC0033"><?php echo $page?></font>/<?php echo $totlepage?>　</strong> 
      <strong><?php echo $page_size?></strong>条/页　共<strong><?php echo $totlenum ?></strong>条		 
          <?php  
if ($page!=1){
echo "<a href=?page=1&reply=$reply>【首页】</a> ";
echo "<a href=?page=".($page-1)."&reply=$reply>【上一页】</a> ";
}else{
echo "【首页】【上一页】";
}
if ($page!=$totlepage){
echo "<a href=?page=".($page+1)."&reply=$reply>【下一页】</a> ";
echo "<a href=?page=".$totlepage."&reply=$reply>【尾页】</a>";
}else{
echo "【下一页】【尾页】";
}
?>
         
          <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox" />
          <label for="chkAll">全选</label>
          <input name="submit"  type="submit" class="buttons"  value="删除" onClick="return ConfirmDel()" />
          <input name="pagename" type="hidden" id="pagename" value="usermessage.php?page=<?php echo $page ?>" />
          <input name="tablename" type="hidden" id="tablename" value="zzcms_usermessage" />
  </div>
</form>

<?php
}
?>
</body>
</html>