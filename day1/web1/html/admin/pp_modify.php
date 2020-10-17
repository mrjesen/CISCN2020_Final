<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){
if (document.myform.bigclassid.value==""){
    alert("请选择类别！");
	document.myform.bigclassid.focus();
	return false;
  }
  if (document.myform.cpname.value==""){
    alert("名称不能为空！");
	document.myform.cpname.focus();
	return false;
}
}
</script>
</head>
<body>  
<div class="admintitle">修改品牌信息</div>
<?php
//checkadminisdo("pp_modify");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
$sqlzs="select * from zzcms_pp where id='$id'";
$rszs=query($sqlzs);
$rowzs=fetch_array($rszs);
?>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="15%" align="right" class="border">名称 <font color="#FF0000">*</font></td>
      <td width="85%" class="border"> <input name="cpname" type="text" id="cpname" value="<?php echo $rowzs["ppname"]?>" size="45"></td>
    </tr>
    <tr> 
      <td align="right" class="border"> 类别 <font color="#FF0000">*</font></td>
      <td class="border"> 
        <?php
$sql = "select classid,parentid,classname from zzcms_zsclass where parentid<>0 order by xuhao asc";
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
    }</script> <select name="bigclassid" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
          <option value="0">请选择大类别</option>
          <?php
	$sql = "select classid,classname from zzcms_zsclass where  parentid=0 order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
	?>
    <option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzs["bigclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
          <?php
				}
				?>
        </select> <select name="smallclassid">
          <option value="0">不指定小类</option>
          <?php
$sql="select classid,classname from zzcms_zsclass where parentid='" .$rowzs["bigclassid"]."' order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
?>
<option value="<?php echo $row["classid"]?>" <?php if ($row["classid"]==$rowzs["smallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
<?php 
}
?>
        </select> </td>
    </tr>
	 
    <tr> 
      <td align="right" class="border">说明：</td>
      <td class="border"> <textarea name="sm" cols="60" rows="5" id="sm"><?php echo $rowzs["sm"]?></textarea></td>
    </tr>
    <tr> 
      <td align="right" class="border">图片： 
 <input name="img" type="hidden" id="img" value="<?php echo $rowzs["img"]?>" size="45"></td>
      <td class="border"> <table height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
          <tr> 
            <td width="120" align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php',400,300)"> 
              <?php
				  if($rowzs["img"]<>""){
				  echo "<img src='".$rowzs["img"]."' border=0 width=120 /><br>点击可更换图片";
				  }else{
				  echo "<input name='Submit2' type='button'  value='上传图片'/>";
				  }
				  
				  ?>            </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td align="right" class="border">发布人：</td>
      <td class="border"><input name="editor" type="text" id="editor" value="<?php echo $rowzs["editor"]?>" size="45"> 
        <input name="oldeditor" type="hidden" id="oldeditor" value="<?php echo $rowzs["editor"]?>"></td>
    </tr>
    <tr> 
      <td align="right" class="border">审核：</td>
      <td class="border"><input name="passed" type="checkbox" id="passed" value="1"  <?php if ($rowzs["passed"]==1) { echo "checked";}?>>
        （选中为通过审核） </td>
    </tr>
    
    <tr> 
      <td align="center" class="border">&nbsp;</td>
      <td class="border"><input name="cpid" type="hidden" id="cpid" value="<?php echo $rowzs["id"]?>"> 
        <input name="sendtime" type="hidden" id="sendtime" value="<?php echo $rowzs["sendtime"]?>"> 
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"> 
        <input type="submit" name="Submit" value="修 改"></td>
    </tr>
  </table>
</form>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
if ($do=="save"){
checkadminisdo("pp_modify");

$page = isset($_POST['page'])?$_POST['page']:1;//只从修改页传来的值
checkid($page);
$cpid = isset($_POST['cpid'])?$_POST['cpid']:0;
checkid($cpid,1);
$passed = isset($_POST['passed'])?$_POST['passed']:0;
checkid($passed,1);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:0;
checkid($bigclassid,1);checkid($smallclassid,1);
checkstr($img,"upload");//入库前查上传文件地址是否合格
query("update zzcms_pp set bigclassid='$bigclassid',smallclassid='$smallclassid',ppname='$cpname',sm='$sm',img='$img',sendtime='$sendtime',passed='$passed' where id='$cpid'");

if ($editor<>$oldeditor) {
$rs=query("select comane,id from zzcms_user where username='".$editor."'");
$row = num_rows($rs);
	if ($row){
	$row = fetch_array($rs);
	$userid=$row["id"];
	$comane=$row["comane"];
	}else{
	$userid=0;
	$comane="";
	}
query("update zzcms_pp set editor='$editor',userid='$userid',comane='$comane',passed='$passed' where id='$cpid'");
}
echo "<script>location.href='pp_manage.php?page=".$page."'</script>";
}
?>
</body>
</html>