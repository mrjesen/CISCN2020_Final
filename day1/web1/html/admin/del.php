<?php 
//set_time_limit(1800);
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css"> 
<title></title>
</head>
<body>
<?php
$pagename=isset($_POST["pagename"])?$_POST["pagename"]:'';
$tablename=isset($_POST["tablename"])?$_POST["tablename"]:'';
$id="";
if(!empty($_POST['id'])){
    for($i=0; $i<count($_POST['id']);$i++){
    $id=$id.($_POST['id'][$i].',');
	checkid($_POST['id'][$i]);
    }
	$id=substr($id,0,strlen($id)-1);//去除最后面的","
}

if ($id==""){
echo "<script>alert('操作失败！至少要选中一条信息。');history.back(-1)</script>";
}

$tablenames='';
$rs = query("SHOW TABLES"); 
while($row = fetch_array($rs)) { 
$tablenames=$tablenames.$row[0]."#"; 
}
$tablenames=substr($tablenames,0,strlen($tablenames)-1);

if (str_is_inarr($tablenames,$tablename)=='no'){
showmsg('tablename 参数有误');
}

if ($tablename=="zzcms_main"){
checkadminisdo("zs_del");
if (strpos($id,",")>0){
$sql="select img,flv,id,sm from zzcms_main where id in (". $id .")";
}else{
$sql="select img,flv,id,sm from zzcms_main where id='$id'";
}
$rs=query($sql);
while($row=fetch_array($rs)){
		if ($row["img"]<>"" && substr($row["img"],0,4)<>"http" && strpos($row["img"],'/uploadfiles')!==false) {
		$f="../".substr($row["img"],1);//前面必须加../否则完法删
		$fs="../".substr(str_replace(".","_small.",$row["img"]),1);
			if (file_exists($f)){
			unlink($f);
			unlink($fs);		
			}
		}
		
		if ($row["flv"]<>"" && substr($row["flv"],0,4)<>"http" && strpos($row["flv"],'/uploadfiles')!==false) {
			$f="../".substr($row['flv'],1);
			if (file_exists($f)){
			unlink($f);
			}
		}
		
	$imgs=getimgincontent(stripfxg($row["sm"],true),2);//删内容中的图片
	if (is_array($imgs)){
	foreach ($imgs as $value) {
		if ($value<>"" && substr($value,0,4) !== "http"  && strpos($value,'/uploadfiles')!==false){
		$f="../".substr($value,1)."";
		if (file_exists($f)){unlink($f);}
		}
	}
	}
		query("delete from zzcms_main where id=".$row['id']."");
		query("update zzcms_dl set cpid=0 where cpid=".$row["id"]."");//把代理信息中的ID设为0
}
echo "<script>location.href='".$pagename."'</script>"; 

}elseif ($tablename=="zzcms_pp" || $tablename=="zzcms_licence"|| $tablename=="zzcms_ad"){

checkadminisdo("pp_del");checkadminisdo("licence");checkadminisdo("adv_del");
if (strpos($id,",")>0){
$sql="select * from `".$tablename."` where id in (". $id .")";
}else{
$sql="select * from `".$tablename."` where id='$id'";
}
$rs=query($sql);
while($row=fetch_array($rs)){
		if ($row["img"]<>"" && substr($row["img"],0,4)<>"http" && strpos($row["img"],'/uploadfiles')!==false) {
		$f="../".substr($row["img"],1)."";
		$fs="../".substr(str_replace(".","_small.",$row["img"]),1)."";
			if (file_exists($f)){
			unlink($f);
			unlink($fs);		
			}
		}
		query("delete from `".$tablename."` where id=".$row['id']."");
}
echo "<script>location.href='".$pagename."'</script>"; 

}elseif ($tablename=="zzcms_zx" || $tablename=="zzcms_special" || $tablename=="zzcms_ask"){

checkadminisdo("zx_del");checkadminisdo("special_del");checkadminisdo("ask_del");
if (strpos($id,",")>0){
$sql="select * from `".$tablename."` where id in (". $id .")";
}else{
$sql="select * from `".$tablename."` where id='$id'";
}
$rs=query($sql);
while($row=fetch_array($rs)){
		if ($row["img"]<>"" && substr($row["img"],0,4)<>"http" && strpos($row["img"],'/uploadfiles')!==false) {
		$f="../".substr($row["img"],1)."";
		$fs="../".substr(str_replace(".","_small.",$row["img"]),1)."";
			if (file_exists($f)){
			unlink($f);
			unlink($fs);		
			}
		}
		
	$imgs=getimgincontent(stripfxg($row["content"],true),2);//删内容中的图片
	if (is_array($imgs)){
	foreach ($imgs as $value) {
		if ($value<>"" && substr($value,0,4) !== "http"  && strpos($value,'/uploadfiles')!==false){
		$f="../".substr($value,1)."";
		if (file_exists($f)){unlink($f);}
		}
	}
	}
	query("delete from `".$tablename."` where id='".$row['id']."'");
}
echo "<script>location.href='".$pagename."'</script>"; 

}elseif ($tablename=="zzcms_dl"){checkadminisdo("dl_del");del();
}elseif ($tablename=="zzcms_zh"){checkadminisdo("zh_del");del();
}elseif ($tablename=="zzcms_job"){checkadminisdo("job_del");del();
}elseif ($tablename=="zzcms_baojia"){checkadminisdo("baojia_del");del();
}elseif ($tablename=="zzcms_link"){checkadminisdo("friendlink");del();
}else{del();}

function del(){
global $id,$tablename,$pagename;
if (strpos($id,",")>0){
$sql="delete from `".$tablename."` where id in (". $id .")";
}else{
$sql="delete from `".$tablename."` where id='$id'";
}
query($sql);
echo "<script>location.href=\"$pagename\"</script>"; 
}
?>
<a href="<?php echo $pagename?>">返回</a>
</body>
</html>