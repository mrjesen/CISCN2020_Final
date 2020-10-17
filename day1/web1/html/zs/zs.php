<?php
ob_start();//打开缓冲区，可以setcookie为向AJAX/zs.php中传b,s值
include("../inc/conn.php");
include("../inc/fy.php");
include("../inc/top.php");
include("../inc/bottom.php");
include("subzs.php");
include("../label.php");

$px = isset($_GET['px'])?$_GET['px']:"sendtime";
if ($px!='hit' && $px!='id' && $px!='sendtime'){
$px="sendtime";
}
if (isset($_GET["page_size"])){
$page_size=$_GET["page_size"];
checkid($page_size);
setcookie("page_size_zs",$page_size,time()+3600*24*360);
}else{
$page_size=isset($_COOKIE['page_size_zs'])?$_COOKIE['page_size_zs']:pagesize_qt;
}
if (isset($_GET["ys"])){
$ys=$_GET["ys"];
setcookie("yszs",$ys,time()+3600*24*360);
}else{
$ys=isset($_COOKIE['yszs'])?$_COOKIE['yszs']:'list';
}

$b = isset($_GET['b'])?$_GET['b']:"";
setcookie("zs_b",$b,time()+3600*24,"/");

$s = isset($_GET['s'])?$_GET['s']:"";
setcookie("zs_s",$s,time()+3600*24,"/");

if (isset($_GET['province'])){
$provinceNew=$_GET['province'];
setcookie("province",$provinceNew,time()+3600*24);
$province=$provinceNew;
}else{
$province=isset($_COOKIE['province'])?$_COOKIE['province']:'';
}

if (isset($_GET['p_id'])){
$p_idNew=$_GET['p_id'];
setcookie("p_id",$p_idNew,time()+3600*24);
$p_id=$p_idNew;
}else{
$p_id=isset($_COOKIE['p_id'])?$_COOKIE['p_id']:'';
}

if (isset($_GET['city'])){
$cityNew=$_GET['city'];
setcookie("city",$cityNew,time()+3600*24);
$city=$cityNew;
}else{
$city=isset($_COOKIE['city'])?$_COOKIE['city']:'';
}

if (isset($_GET['c_id'])){
$c_idNew=$_GET['c_id'];
setcookie("c_id",$c_idNew,time()+3600*24);
$c_id=$c_idNew;
}else{
$c_id=isset($_COOKIE['c_id'])?$_COOKIE['c_id']:'';
}

if (isset($_GET['xiancheng'])){
$xianchengNew=$_GET['xiancheng'];
setcookie("xiancheng",$xianchengNew,time()+3600*24);
$xiancheng=$xianchengNew;
}else{
$xiancheng=isset($_COOKIE['xiancheng'])?$_COOKIE['xiancheng']:'';
}

$descriptions="";
$keywords="";
$titles="";
$bigclassid='';
$bigclassname="";

$descriptionsx="";
$keywordsx="";
$titlesx="";
$smallclassname="";

if ($b<>""){
$sql="select * from zzcms_zsclass where classzm='".$b."'";
$rs=query($sql);
$row=fetch_array($rs);
$descriptions=$row["description"];
$keywords=$row["keyword"];
$titles=$row["title"];
$bigclassname=$row["classname"];
$bigclassid=$row["classid"];
$skin=explode("|",$row["skin"]);
$skin=@$skin[1];//列表页是第二个参数
}
if (!isset($skin)){$skin='zs_list.htm';}

if ($s<>"") {
$sql="select * from zzcms_zsclass where classzm='".$s."'";
$rs=query($sql);
$row=fetch_array($rs);
	$descriptionsx=$row["description"];
	$keywordsx=$row["keyword"];
	$titlesx=$row["title"];
	$smallclassname=$row["classname"];
	$smallclassid=$row["classid"];
}
if ($titlesx!=''){
$pagetitle=$titlesx;
}elseif($titles!=''){
$pagetitle=$titles;
}else{
$pagetitle=zslisttitle;
}
if ($keywordsx!=''){
$pagekeyword=$keywordsx;
}elseif($keywords!=''){
$pagekeyword=$keywords;
}else{
$pagekeyword=zslistkeyword;
}
if ($descriptionsx!=''){
$pagedescription=$descriptionsx;
}elseif($descriptions!=''){
$pagedescription=$descriptions;
}else{
$pagedescription=zslistdescription;
}

$station=getstation($b,$bigclassname,$s,$smallclassname,"","","zs");

if( isset($_GET["page"]) && $_GET["page"]!="") {$page=$_GET['page'];}else{$page=1;}
checkid($page);

	function formbigclass(){
		$str="";
        $sql = "select classzm,classname from zzcms_zsclass where parentid=0";
        $rs=query($sql);
			while($row=fetch_array($rs)){
			$str=$str. "<a href=?b=".$row["classzm"].">".$row["classname"]."</a>&nbsp;&nbsp;";
			}
		return $str;
		}
		
		function formsmallclass($b){
		if ($b!=0){
		$str="";
        $sql="select classzm,classname from zzcms_zsclass where parentid='" .$b. "' order by xuhao asc";
        $rs=query($sql);
			while($row=fetch_array($rs)){
			$str=$str. "<a href=?s=".$row["classzm"].">".$row["classname"]."</a>&nbsp;&nbsp;";
			}
		return $str;
		}
		}
		
		function formprovince(){
		$str="";
		global $citys;
		$city=explode("#",$citys);
		$c=count($city);//循环之前取值
	for ($i=0;$i<$c;$i++){ 
		$location_p=explode("*",$city[$i]);//取数组的第一个就是省份名，也就是*左边的
		$str=$str . "<a href=/zs/search.php?province=".$location_p[0]."&p_id=".$i.">".$location_p[0]."</a>&nbsp;&nbsp;";
	}
	return $str;
	}	
		
	function formcity(){
	global $citys,$p_id;
	$str="";
	if ($p_id<>"") {
	$city=explode("#",$citys);
	$location_cs=explode("*",$city[$p_id]);//取指定省份下的
	$location_cs2=explode("|",$location_cs[1]);//要*右边的市和县
	$c=count($location_cs2);//循环之前取值
		for ($i=0;$i<$c;$i++){ 
		$location_cs3=explode(",",$location_cs2[$i]);//取指定省份下的
		$str=$str . "<a href=/zs/search.php?city=".$location_cs3[0]."&c_id=".$i.">".$location_cs3[0]."</a>&nbsp;&nbsp;";
		}
	}else{
	$city="";
	}
	return $str;
}

function formxiancheng(){
	global $citys,$p_id,$c_id;
	$str="";
	if ($p_id<>"" && $c_id<>"") {
	$city=explode("#",$citys);
	$location_cs=explode("*",$city[$p_id]);//取指定省份下的
	$location_cs2=explode("|",$location_cs[1]);//要*右边的市和县
	$location_cs3=explode(",",$location_cs2[$c_id]);//取指定市和县下的
	$c=count($location_cs3);//循环之前取值
		for ($i=1;$i<$c;$i++){ //从1开始，0对应的是，前面的市名，市名不要，这里只显示县名。
		$str=$str . "<a href=/zs/search.php?xiancheng=".$location_cs3[$i].">".$location_cs3[$i]."</a>&nbsp;&nbsp;";
		}
	}else{
	$xiancheng="";
	}
	return $str;
}	

if ($b=="") {
$zsclass=bigclass($b,2);
}else{
$zsclass= showzssmallclass($b,$s);
}
	
$form_sj="&nbsp;<select name='sj'>";
$form_sj=$form_sj . "<option value=999>不限时间</option>";
$form_sj=$form_sj . "<option value=1 >当天</option>";
$form_sj=$form_sj . "<option value=3 >3天内</option>";
$form_sj=$form_sj . "<option value=7 >7天内</option>";
$form_sj=$form_sj . "<option value=30 >30天内</option>";
$form_sj=$form_sj . "<option value=90 >90天内</option>";
$form_sj=$form_sj . "</select>";

if (isset($_GET["tp"])) {
$form_img="&nbsp;<input name='tp' type='checkbox' id='tp' value='yes' checked/>";
}else{
$form_img="&nbsp;<input name='tp' type='checkbox' id='tp' value='yes'/>";
}
$form_img=$form_img . "有图";

if (isset($_GET["vip"])) {
$form_vip= "&nbsp;<input name=vip type=checkbox id=vip value=yes checked/>";
}else{
$form_vip= "&nbsp;<input name=vip type=checkbox id=vip value=yes />";
}
$form_vip=$form_vip . "VIP&nbsp;";


$form_px= "<select name='menu2' onChange=MM_jumpMenu('parent',this,0)>";
if ($px=="id") {
$form_px=$form_px . "<option value=/zs/zs_list.php?b=".$b."&s=".$s."&px=id selected>最近发布</option>";
}else{
$form_px=$form_px . "<option value=/zs/zs_list.php?b=".$b."&s=".$s."&px=id >最近发布</option>";
}
if( $px=="sendtime") {
$form_px=$form_px . "<option value='/zs/zs_list.php?b=".$b."&s=".$s."&px=sendtime' selected>最近更新</option>";
}else{
$form_px=$form_px . "<option value='/zs/zs_list.php?b=".$b."&s=".$s."&px=sendtime'>最近更新</option>";
}
if ($px=="hit") { 
$form_px=$form_px . "<option value='/zs/zs_list.php?b=".$b."&s=".$s."&px=hit' selected>最热点击</option>";
}else{
$form_px=$form_px . "<option value='/zs/zs_list.php?b=".$b."&s=".$s."&px=hit'>最热点击</option>";
}
$form_px=$form_px . "</select>&nbsp;";

$showselectpage=showselectpage("zs",$page_size,$b,$s,$page);

$form_xs="&nbsp;<a href='/zs/zs.php?b=".$b."&s=".$s."&page=".$page."&ys=list'>";
if ($ys=="list") {
$form_xs=$form_xs . "<img src='/image/showlist.gif' border='0' title='图文显示' style='filter:gray'/>";
}else{
$form_xs=$form_xs . "<img src='/image/showlist.gif' border='0' title='图文显示' />";
}
$form_xs=$form_xs . "</a> ";

$form_xs=$form_xs . "<a href='/zs/zs.php?b=".$b."&s=".$s."&page=".$page."&ys=window'>";
if ($ys=="window") {
$form_xs=$form_xs . "<img src='/image/showwindow.gif' border='0' title='橱窗显示' style='filter:gray'/>";
}else{
$form_xs=$form_xs . "<img src='/image/showwindow.gif' border='0' title='橱窗显示' />";
}
$form_xs=$form_xs . "</a> ";


$fp="../template/".$siteskin."/".$skin;
if (file_exists($fp)==false){
WriteErrMsg($fp.'模板文件不存在');
exit;
}
$f = fopen($fp,'r');
$strout = fread($f,filesize($fp));
fclose($f);

$sql="select count(*) as total from zzcms_main where passed<>0 ";

$sql2='';
if ($b<>""){
$sql2=$sql2. "and bigclassid='".$bigclassid."' ";
}

if ($s<>"") {
	if (zsclass_isradio=='Yes'){
	$sql2=$sql2." and smallclassid ='".$smallclassid."'  ";
	}else{
	$sql2=$sql2." and smallclassids like '%".$smallclassid."%' ";
	}
}
$rs =query($sql.$sql2); 
$row = fetch_array($rs);
$totlenum = $row['total'];
$offset=($page-1)*$page_size;//$page_size在上面被设为COOKIESS
$totlepage=ceil($totlenum/$page_size);

$sql="select id,proname,prouse,img,tz,shuxing_value,link,province,city,xiancheng,province_user,city_user,xiancheng_user,sendtime,editor,elite,
userid,comane,qq,groupid,renzheng from zzcms_main where passed=1 ";
$sql=$sql.$sql2;
$sql=$sql." order by groupid desc,elite desc,".$px." desc limit $offset,$page_size";
//echo $sql;
$rs = query($sql); 
$zs=strbetween($strout,"{zs}","{/zs}");
$loop_list=strbetween($strout,"{loop_list}","{/loop_list}");
$loop_window=strbetween($strout,"{loop_window}","{/loop_window}");

if ($ys=="window"){
$proname_num=strbetween($loop_window,"{#proname:","}");
$prouse_num=strbetween($loop_window,"{#prouse:","}");
}else{
$proname_num=strbetween($loop_list,"{#proname:","}");
$prouse_num=strbetween($loop_list,"{#prouse:","}");
}

if(!$totlenum){
$strout=str_replace("{zs}".$zs."{/zs}","暂无信息",$strout) ;
}else{

$i=0;
$keyword="";
$province="";
$list2='';

	while($row= fetch_array($rs)){
	if ($ys=="window"){
	$list2 = $list2. str_replace("{#id}",$row["id"],$loop_window) ;
	}else{
	$list2 = $list2. str_replace("{#id}",$row["id"],$loop_list) ;
	}
	if ($row["link"]<>""){
	$link=$row["link"];
	}else{
	$link=getpageurl("zs",$row["id"]);	
	}
	$list2 =str_replace("{#i}",$i,$list2) ;
	$list2 =str_replace("{#url}",$link,$list2) ;
	$list2 =str_replace("{#proname:".$proname_num."}",cutstr($row["proname"],$proname_num),$list2) ;
	$list2 =str_replace("{#prouse:".$prouse_num."}",cutstr($row["prouse"],$prouse_num),$list2) ;
	
	$list2 =str_replace("{#img}",getsmallimg($row["img"]),$list2) ;
	$list2 =str_replace("{#imgbig}",$row["img"],$list2) ;
	$list2 =str_replace("{#comane}",$row["comane"],$list2) ;
	$list2 =str_replace("{#province}",$row["province"],$list2) ;
	$list2 =str_replace("{#city}",$row["city"],$list2) ;
	$list2 =str_replace("{#tz}",$row["tz"],$list2);
	$list2 =str_replace("{#province_company}",$row["province_user"],$list2) ;
	$list2 =str_replace("{#city_company}",$row["city_user"],$list2) ;
	$list2 =str_replace("{#xiancheng_company}",$row["xiancheng_user"],$list2) ;
	$list2 =str_replace("{#groupid}",$row["groupid"],$list2) ;
	$list2 =str_replace("{#userid}",$row["userid"],$list2) ;
	$list2 =str_replace("{#zturl}",getpageurlzt($row["editor"],$row["userid"]),$list2) ;//展厅地址
	
	if ($row["renzheng"]==1) {
	$list2 =str_replace("{#renzheng}" ,"<img src='/image/ico_renzheng.png' alt='认证会员'>",$list2) ;
	}else{
	$list2 =str_replace("{#renzheng}" ,"",$list2) ;
	}
	
	if ($row["elite"]==1) { 
	$list2 =str_replace("{#elite}" ,"<img src='/image/ico_jian.png' alt='tag:".$row["elite"]."' >",$list2) ;
	}else{
	$list2 =str_replace("{#elite}" ,"",$list2) ;
	}
	
	if ($row["qq"]!=''){
	$showqq="<a target=blank href=http://wpa.qq.com/msgrd?v=1&uin=".$row["qq"]."&Site=".sitename."&MMenu=yes><img border='0' src='http://wpa.qq.com/pa?p=1:".$row["qq"].":10' alt='QQ交流'></a> ";
	$list2 =str_replace("{#qq}",$showqq,$list2) ;
	}else{
	$list2 =str_replace("{#qq}","",$list2) ;
	}
	if ($row["shuxing_value"]==''){
	for ($a=0; $a< 6;$a++){
	$list2=str_replace("{#shuxing".$a."}",'',$list2);
	}
	}else{
	$shuxing_value = explode("|||",$row["shuxing_value"]);
	for ($n=0; $n< count($shuxing_value);$n++){
	$list2=str_replace("{#shuxing".$n."}",$shuxing_value[$n],$list2);
	}
	}

	$list2 =str_replace("{#sendtime}",$row["sendtime"],$list2) ;

	$rsn=query("select grouppic,groupname from zzcms_usergroup where groupid=".$row["groupid"]."");
	$rown=fetch_array($rsn);
	if ($rown){
	$list2 =str_replace("{#grouppic}" ,"<img src=".$rown["grouppic"]." alt=".$rown["groupname"].">",$list2) ;
	}
	
	if (showdlinzs=="Yes") {//数据量大时，建议关闭，否则查寻很慢
	$rsn=query("select id from zzcms_dl where cpid=".$row["id"]." and passed=1");
	$list2 =str_replace("{#dl_num}","(".channeldl."留言<font color='#FF6600'><b>".num_rows($rsn)."</b></font>条)",$list2) ;
	}else{
	$list2 =str_replace("{#dl_num}","",$list2) ;
	}

	$i=$i+1;
	}
if ($ys=="window"){	
$strout=str_replace("{loop_window}".$loop_window."{/loop_window}",$list2,$strout) ;
$strout=str_replace("{loop_list}".$loop_list."{/loop_list}","",$strout) ;
}else{
$strout=str_replace("{loop_list}".$loop_list."{/loop_list}",$list2,$strout) ;
$strout=str_replace("{loop_window}".$loop_window."{/loop_window}","",$strout) ;
}
$strout=str_replace("{#fenyei}",showpage2("zs"),$strout) ;
$strout=str_replace("{zs}","",$strout) ;
$strout=str_replace("{/zs}","",$strout) ;
}

$strout=str_replace("{#siteskin}",$siteskin,$strout) ;
$strout=str_replace("{#sitename}",sitename,$strout) ;
$strout=str_replace("{#siteurl}",siteurl,$strout) ;
$strout=str_replace("{#station}",$station,$strout) ;
$strout=str_replace("{#zsclass}",$zsclass,$strout) ;
$strout=str_replace("{#pagetitle}",$pagetitle,$strout);
$strout=str_replace("{#pagekeywords}",$pagekeyword,$strout);
$strout=str_replace("{#pagedescription}",$pagedescription,$strout);

if ($b=="") {//当小类为空显示大类，否则只显小类
$strout=str_replace("{#formbigclass}",formbigclass(),$strout);
}else{
$strout=str_replace("{#formbigclass}","",$strout);
}
$strout=str_replace("{#formsmallclass}",formsmallclass($bigclassid),$strout);
if ($province=="") {
$strout=str_replace("{#formprovince}",formprovince(),$strout);
}else{
$strout=str_replace("{#formprovince}","",$strout);
}
if ($city=="") {
$strout=str_replace("{#formcity}",formcity(),$strout);
}else{
$strout=str_replace("{#formcity}","",$strout);
}
$strout=str_replace("{#formxiancheng}",formxiancheng(),$strout);

$strout=str_replace("{#form_sj}",$form_sj,$strout);
$strout=str_replace("{#form_img}",$form_img,$strout);
$strout=str_replace("{#form_vip}",$form_vip,$strout);
$strout=str_replace("{#b}",$b,$strout);
$strout=str_replace("{#s}",$s,$strout);
$strout=str_replace("{#form_px}",$form_px,$strout);
$strout=str_replace("{#showselectpage}",$showselectpage,$strout);
$strout=str_replace("{#form_xs}",$form_xs,$strout);

$strout =str_replace("{#province}",$province,$strout) ;
$strout =str_replace("{#city}",$city,$strout) ;
$strout =str_replace("{#xiancheng}",$xiancheng,$strout) ;

$strout=str_replace("{#sitebottom}",sitebottom(),$strout);
$strout=str_replace("{#sitetop}",sitetop(),$strout);
$strout=showlabel($strout);

echo  $strout;
?>