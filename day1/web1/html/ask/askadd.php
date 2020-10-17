<?php
include("../inc/conn.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="/template/<?php echo siteskin?>/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include("../inc/top2.php");
echo sitetop();
?>
<div class="main">
<div class="pagebody">
<div class="titles">提问</div>
<div class="content">
<form action="?" method="post" name="myform" id="myform" onSubmit="return CheckForm();">      
  <table width="100%" border="0" cellpadding="8" cellspacing="1">
    <tr> 
      <td width="130" align="right" class="border2">类别 <font color="#FF0000">*</font></td>
      <td class="border2"><?php

$sql = "select * from zzcms_askclass where parentid<>0 order by xuhao asc";
$rs=query($sql);
?>
        <script language = "JavaScript" type="text/javascript">
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
    if (subcat[i][1] == locationid){ document.myform.smallclassid.options[document.myform.smallclassid.length] = new Option(subcat[i][0], subcat[i][2]); }        
    }
    }</script>
        <select name="bigclassid"  onchange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
          <option value="" selected="selected">请选择大类 </option>
          <?php
	$sql = "select * from zzcms_askclass where  parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
		if ($row["classid"]==@$b){
	?>
          <option value="<?php echo $row["classid"]?>" selected="selected"><?php echo $row["classname"]?></option>
          <?php
		}elseif($row["classid"]==@$_SESSION["bigclassid"] && @$b==''){	
				?>
          <option value="<?php echo $row["classid"]?>" selected="selected"><?php echo $row["classname"]?></option>
          <?php 
		}else{
		?>
          <option value="<?php echo $row["classid"]?>"><?php echo $row["classname"]?></option>
          <?php 
		}
	}	
		?>
        </select>
        <select name="smallclassid">
          <option value="0">请选择小类 </option>
          <?php
if ($b!=''){//从index.php获取的大类值优先
$sql="select * from zzcms_askclass where parentid='".$b."' order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
				?>
          <option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$s) { echo "selected";}?>><?php echo $row["classname"]?></option>
          <?php
	}
}elseif($_SESSION["bigclassid"]!=''){
$sql="select * from zzcms_askclass where parentid=" .@$_SESSION["bigclassid"]." order by xuhao asc";
$rs=query($sql);
	while($row = fetch_array($rs)){
	?>
  <option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$_SESSION["smallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
    <?php 
	}
	}
	?>
        </select></td>
    </tr>
    <tr>
      <td align="right" class="border">问题 <font color="#FF0000">*</font></td>
      <td class="border"><input name="title" type="text" id="title" size="45" maxlength="45" value="<?php echo @$_POST['keyword']?>"/>      </td>
    </tr>
	
    <tr> 
    <td align="right" class="border2">内容：</td>      
    <td class="border2">
	<textarea name="content" id="content"></textarea> 
    <script type="text/javascript" src="/3/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">CKEDITOR.replace('content');</script>	</td>
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
if (isset($_POST["action"])){


$bigclassid=isset($_POST["bigclassid"])?trim($_POST["bigclassid"]):0;
$smallclassid=isset($_POST["smallclassid"])?trim($_POST["smallclassid"]):0;

$img=getimgincontent(stripfxg($content,true));
checkstr($img,"upload");//入库前查上传文件地址是否合格
if ($title<>''){
$isok=query("Insert into zzcms_ask(bigclassid,smallclassid,title,content,img,jifen,editor,passed,sendtime) values('$bigclassid','$smallclassid','$title','$content','$img','0','未登陆用户',0,'".date('Y-m-d H:i:s')."')");  
}  
if ($isok){
echo showmsg('发布成功，审核后显示。');
}else{
echo showmsg('发布失败！');
}

}	
?>
</div>
</div>
</div>
<?php
include("../inc/bottom.php");
echo sitebottom();
?>
</body>
</html>