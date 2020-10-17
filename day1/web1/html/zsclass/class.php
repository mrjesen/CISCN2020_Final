<?php
include("../inc/conn.php");
include("../inc/top.php");
include("../inc/bottom.php");
include("../zs/subzs.php");
include("../label.php");
$b=isset($_GET["b"])?$_GET["b"]:'';

$descriptions="";
$keywords="";
$titles="";
$bigclassname="";

if ($b<>""){
$sql="select * from zzcms_zsclass where classzm='".$b."'";
$rs=query($sql);
$row=fetch_array($rs);
if ($row){
$descriptions=$row["description"];
$keywords=$row["keyword"];
$titles=$row["title"];
$bigclassname=$row["classname"];
$skin=explode("|",$row["skin"]);
$skin=$skin[0];
}
}
$pagetitle=$titles."-".sitename;
$pagekeyword=$keywords;
$pagedescription=$descriptions;
$bigclass="<a href='".getpageurl2("zs",$b,"")."'>".$bigclassname."</a>";
//bigclass="<span><a href='"&getpageurl2("zs",b,"")&"'>更多...</a></span>"&bigclassname&""

if ($skin==''){$skin='zs_class.htm';}
$fp="../template/".$siteskin."/".$skin;
if (file_exists($fp)==false){
WriteErrMsg($fp.'模板文件不存在');
exit;
}

$f = fopen($fp,'r');
$strout = fread($f,filesize($fp));
fclose($f);
$strout=str_replace("{#siteskin}",$siteskin,$strout) ;
$strout=str_replace("{#sitename}",sitename,$strout) ;
$strout=str_replace("{#siteurl}",siteurl,$strout) ;
$strout=str_replace("{#pagetitle}",$pagetitle,$strout);
$strout=str_replace("{#pagekeywords}",$pagekeyword,$strout);
$strout=str_replace("{#pagedescription}",$pagedescription,$strout);
$strout=str_replace("{#bigclass}",$bigclass,$strout);
$strout=str_replace("{#bigclassname}",$bigclassname,$strout);
//$strout=str_replace("{#more}",$more,$strout);
$zssmallclass_num=strbetween($strout,"{#zssmallclass:","}");
$strout=str_replace("{#zssmallclass:".$zssmallclass_num."}",showzssmallclass($b,"",$zssmallclass_num),$strout);
$strout=str_replace("{#sitebottom}",sitebottom(),$strout);
$strout=str_replace("{#sitetop}",sitetop(),$strout);
$strout=showlabel($strout);

echo  $strout;

?>