<?php
include("admin.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script>
function ConfirmClear(){
   if(confirm("初始化数据库后将不能恢复！确定初始化么？"))
     return true;
   else
     return false;	 
}
function CheckAll(form){
  for (var i=0;i<form.elements.length;i++){
    var e = form.elements[i];
    if (e.Name != "chkAll")
       e.checked = form.chkAll.checked;
    }
}
</script>
</head>
<body>
<div class="admintitle">初始化数据库</div>
<?php
if (!isset($_POST["action"])) {
?>

      <form name="form1" method="post" action="" onSubmit="return ConfirmClear();">
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr> 
            <td class="border">
				<?php 
			$rs = query("SHOW TABLES"); 
while($row = fetch_array($rs)) { 
	if ($row[0]=='zzcms_admin' || $row[0]=='zzcms_admingroup'){
	echo "<label><input name='table[]' type='checkbox'  value='".$row[0]."'>".$row[0]."</label>(用户/权限表，初始化后不能登录)<br/>"; 
	}else{
	echo "<label><input name='table[]' type='checkbox'  value='".$row[0]."'>".$row[0]."</label><br/>"; 
	}
}
			?>		
			
			 <input name="chkAll" type="checkbox" id="chkAll" onClick="CheckAll(this.form)" value="checkbox">
              <label for="chkAll">全选</label>
              <input name="Submit24" type="submit" class="buttons" value="初始化"> 
              <input name="action" type="hidden" id="action" value="clear"> </td>
          </tr>
        </table>
      </form>
<?php
}else{
checkadminisdo("siteconfig");
?>
<div class="border">
<?php
if(!empty($_POST['table'])){
    for($i=0; $i<count($_POST['table']);$i++){
	query("truncate ".trim($_POST['table'][$i])."");
	echo $table[$i]."表已被初始化<br>"; 
    }	
}
?>
</div>
<?php
}
?>
</body>
</html>