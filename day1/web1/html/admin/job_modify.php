<?php
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language = "JavaScript">
function CheckForm(){
if (document.myform.bigclassid.value==""){
    alert("请选择类别！");
	document.myform.bigclassid.focus();
	return false;
}
if (document.myform.jobname.value==""){
    alert("名称不能为空！");
	document.myform.jobname.focus();
	return false;
}
}
</script>
</head>
<body>
<?php
//checkadminisdo("job_modify");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);
$sqlzs="select * from zzcms_job where id='$id'";
$rszs=query($sqlzs);
$rowzs=fetch_array($rszs);
?>   
<div class="admintitle">修改招聘信息</div>
<form action="?do=save" method="post" name="myform" id="myform" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="18%" align="right" class="border"> 类别 <font color="#FF0000">*</font></td>
      <td width="82%" class="border"> 
        <?php
$sql = "select classid,parentid,classname from zzcms_jobclass where parentid<>0 order by xuhao asc";
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
          <option value="" selected="selected">请选择大类别</option>
          <?php
	$sql = "select classid,classname from zzcms_jobclass where  parentid='0' order by xuhao asc";
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
$sql="select classid,classname from zzcms_jobclass where parentid='" .$rowzs["bigclassid"]."' order by xuhao asc";
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
      <td align="right" class="border">职位<font color="#FF0000">*</font></td>
      <td class="border"><input name="jobname" type="text" id="cpname" value="<?php echo $rowzs["jobname"]?>" size="45"></td>
    </tr>
	 
    <tr> 
      <td align="right" class="border">说明：</td>
      <td class="border"> <textarea name="sm" cols="40" rows="5" id="sm"><?php echo $rowzs["sm"]?></textarea></td>
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
      <td class="border"><input name="id" type="hidden" id="id" value="<?php echo $rowzs["id"]?>"> 
        <input name="sendtime" type="hidden" id="sendtime" value="<?php echo $rowzs["sendtime"]?>"> 
        <input name="page" type="hidden" id="page" value="<?php echo $page?>"> 
        <input type="submit" name="Submit" value="修 改"></td>
    </tr>
  </table>
</form>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
if ($do=="save"){
checkadminisdo("job_modify");

$page = isset($_POST['page'])?$_POST['page']:1;//只从修改页传来的值
checkid($page);
$cpid = isset($_POST['id'])?$_POST['id']:0;
checkid($cpid,1);
$passed = isset($_POST['passed'])?$_POST['passed']:0;
checkid($passed,1);

$bigclassid = isset($_POST['bigclassid'])?$_POST['bigclassid']:0;
$smallclassid = isset($_POST['smallclassid'])?$_POST['smallclassid']:0;
checkid($bigclassid,1);checkid($smallclassid,1);

$smallclassname="未指定小类";
if ($bigclassid!=0){
$rs = query("select classname from zzcms_jobclass where classid='$bigclassid'"); 
$row= fetch_array($rs);
$bigclassname=$row["classname"];
}

if ($smallclassid !=0){
$rs = query("select classname from zzcms_jobclass where classid='$smallclassid'"); 
$row= fetch_array($rs);
$smallclassname=$row["classname"];
}

query("update zzcms_job set bigclassid='$bigclassid',bigclassname='$bigclassname',smallclassid='$smallclassid',smallclassname='$smallclassname',jobname='$jobname',sm='$sm',sendtime='$sendtime',passed='$passed' where id='$cpid'");
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
query("update zzcms_job set editor='$editor',userid='$userid',comane='$comane' where id='$cpid'");
}

echo "<script>location.href='job_manage.php?page=".$page."'</script>";
}
?>
</body>
</html>