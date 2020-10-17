<?php
define('zzcmsroot2', str_replace("\\", '/', substr(dirname(__FILE__), 0, -3)));//-3截除当前目录inc
function WriteErrMsg($ErrMsg){
	$strErr="<!DOCTYPE html>";//有些文件不能设文件头
	$strErr=$strErr."<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
	$strErr=$strErr."<div style='text-align:center;font-size:14px;line-height:35px;margin:0 auto;width:50%;padding:20px;'>" ;
	$strErr=$strErr . "<div style='border:solid 1px #999999;background-color:#FFFFFF;'>";
	$strErr=$strErr . "<div style='background-color:#f1f1f1;border-bottom:solid 1px #ddd;font-weight:bold'>禁止操作</div>";
	$strErr=$strErr . "<div style='padding:10px;text-align:left'>" .$ErrMsg."</div>";
	$strErr=$strErr . "<div style='border-top:solid 1px #dddddd;margin:0 10px'><a href='javascript:history.go(-1)'>[返回上页]</a>&nbsp;&nbsp;<a href=# onClick='window.opener=null;window.close()'>[关闭窗口]</a></div>";
	$strErr=$strErr . "</div>"; 
	$strErr=$strErr . "</div>" ;
	echo $strErr;
}
//显示信息
function showmsg($msg,$zc_url = 'back'){
	$strErr="<!DOCTYPE html>";//有些文件不能设文件头
	$strErr=$strErr."<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
	if($zc_url && $zc_url!='back' && $zc_url!='null'){
	$strErr=$strErr.("<script>alert('$msg');parent.location=\"$zc_url\";</script>");
	}elseif( $zc_url=='null'){
	$strErr=$strErr.("<script>alert(\"$msg\")</script>");
	}else{
	$strErr=$strErr.("<script>alert(\"$msg\");history.back();</script>");
	}
	echo $strErr;
	exit;
}
 
function CutFenGeXian($str,$xian){
	for($i=0; $i<substr_count($str,$xian);$i++){
		if (substr($str,-1,1)==$xian){//去最后一个|
		$str=substr($str,0,strlen($str)-1);
		}
		if (substr($str,0,1)==$xian){//去第一个|
		$str=substr($str,1);
		}
	}
return $str;
}
		
function checkid($id,$classid=0,$msg='无'){
if ($id<>''){
	if (is_numeric($id)==false){showmsg('参数 '.$id.' 有误！');}
	if ($classid==0){//查大小类ID时这里设为1
		if ($id<1){showmsg('参数 '.$id.'有误！\r\r提示：'.$msg);}//翻页中有用,这个提示msg在其它地方有用
	}
}
}

function checkstr($str,$kind,$msg='',$back_url='back'){
if ($str<>''){
switch ($kind){
case "num";
	if (is_numeric($str)==false){showmsg($msg.'必须为数字');}
break;
case "tel";
	if(!preg_match("/1[34578]{1}\d{9}$/",$str) && !preg_match('/^400(\d{3,4}){2}$/',$str) && !preg_match('/^400(-\d{3,4}){2}$/',$str) && !preg_match('/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',$str)){
	//分别是手机，400电话(加-和不加两种情况都可以)，和普通电话
	showmsg('电话号码不正确！',$back_url);
	}
break;
case "email";
	if(! preg_match("/^[a-zA-Z0-9_.]+@([a-zA-Z0-9_]+.)+[a-zA-Z]{2,3}$/",$str)) {
	showmsg("Email格式不正确",$back_url);
	}
break;
case "hanzi";
	if (!preg_match("/([\x81-\xfe][\x40-\xfe])/",$str)) {//是否含有汉字
	showmsg($msg."必须有汉字",$back_url);
	}
break;
case "datetime";
	if (date('Y-m-d',strtotime($str))!=$str){
	showmsg($msg."必须为时间格式",$back_url);
	}
break;
case "quanhanzi";
	if(!preg_match("/^[\x7f-\xff]+$/",$str)){
	showmsg($msg."只能用中文",$back_url);
	}
break;	

case "upload";//用于引用网络图片地址和视频地址的上传页
	if (strpos("gif|jpg|peg|png|bmp|flv|swf",strtolower(substr($str,-3)))===false){
	showmsg($msg.$str."不支持的上传文件格式。支持的文件格式为（gif|jpg|png|bmp|flv|swf）",$back_url);
	}
	if (substr($str,0,4)<>"http" && $str<>"/image/nopic.gif"){
		if (strpos($str,'/uploadfiles')===false){
		showmsg($msg.$str."不支持的上传文件地址",$back_url);
		}
	}
break;	
}
}
}

function nohtml($str){
$str=trim($str);//清除字符串两边的空格
$str=strip_tags($str,"");//利用php自带的函数清除html格式
$str=str_replace("&nbsp;","",$str);//空白符
$str=str_replace("　","",$str);//table 所产生的空格
$str=preg_replace("/\t/","",$str);//使用正则表达式匹配需要替换的内容，如空格和换行，并将替换为空
$str=preg_replace("/\r\n/","",$str);
$str=preg_replace("/\r/","",$str);
$str=preg_replace("/\n/","",$str);
$str=preg_replace("/ /","",$str);//匹配html中的空格
return trim($str);//返回字符串
}

function getip(){ 
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
$ip = getenv("HTTP_CLIENT_IP");
}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
$ip = getenv("HTTP_X_FORWARDED_FOR"); 
}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
$ip = getenv("REMOTE_ADDR"); 
}else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){ 
$ip = $_SERVER['REMOTE_ADDR']; 
}else{
$ip = "unknown";
}
if (check_isip($ip)==false){
$ip = "unknown";
}
return($ip); 
} 

//$_SERVER['HTTP_REFERER'];//上页来源
function markit(){
		  $userip=$_SERVER["REMOTE_ADDR"]; 
		  //$userip=getip(); 
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		  query("insert into zzcms_bad (username,ip,dose,sendtime)values('".$_COOKIE["UserName"]."','$userip','$url','".date('Y-m-d H:i:s')."')") ;     
}
function admindo(){
$adminip=getip();
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		  $f=zzcmsroot2."admindoes.txt";
$fp=fopen($f,"a+");//fopen()的其它开关请参看相关函数
$str=date('Y-m-d H:i:s')."  ".$_COOKIE["admin"]."  ".$adminip."  ".$url."\r\n";
fputs($fp,$str);
fclose($fp);    
}
function getpageurl($channel,$id){
	if (whtml=="Yes") {return "/". $channel . "/show-" . $id . ".htm" ;}else {return "/" . $channel . "/show.php?id=" . $id;}
}
function getpageurlzt($editor,$id){
	if(sdomain=="Yes" ){return "http://".$editor.".".substr(siteurl,strpos(siteurl,".")+1);}else{return siteurl.getpageurl("zt",$id);}
}

function getpageurl2($channel,$b,$s){
if (whtml=="Yes"){
	$str="/" . $channel;
	if ($b<>"") {$str=$str."/" . $b ."";}
	if ($s<>"") {$str=$str."/" . $s ."";}
	//$str=$str.".html";
}else{
	$str="/" .$channel."/" .$channel . ".php";
	if ($b<>""){$str=$str."?b=" . $b ."";}
	if ($s<>""){$str=$str. "&s=" . $s ."";}	
}
return $str;
}

function getpageurlzs($channel,$b){
if (whtml=="Yes"){
	$str="/" . $channel;
	if ($b<>"") {$str=$str."/" . $b ."";}
	$str=$str.".htm";
}else{
	$str="/" .$channel."/class.php";
	if ($b<>""){$str=$str."?b=" . $b ."";}
}
return $str;
}

function getpageurlzx($channel,$b){
if (whtml=="Yes"){
	$str="/" . $channel."/class";
	if ($b<>"") {$str=$str."/" . $b ."";}
}else{
	$str="/" .$channel."/class.php";
	if ($b<>""){$str=$str."?b=" . $b ."";}
}
return $str;
}

function getpageurl3($pagename){
	if (whtml=="Yes"){return $pagename . ".htm" ;}else {return $pagename . ".php";}
}

function addzero($str,$longs=2){
if (strlen($str)<$longs){
	$result=0;
	for ($i=1;$i<$longs-strlen($str);$i++){
	$result=$result."0";
	}
	$str= $result.$str;
}else{
$str= $str;
}
return $str;
}

function addhttp($url){
if ($url<>"" && substr($url,0,4)<>"http" && substr($url,0,3)=="www"){return "http://".$url;}else{return $url;}
}

function getstation($bid,$bname,$sid,$sname,$title,$keyword,$channel){
	$str="<li class='start'><a href='".siteurl."'>首页</a></li>";
	if (whtml=="Yes") {
		$str=$str. "<li><a href='/".$channel."/index.htm'>".getchannelname($channel)."</a></li>" ;
      	if ($bid<>""){$str=$str. "<li><a href='/".$channel."/".$bid."'>".$bname."</a></li>";}		
		if ($sid<>"") {$str=$str. "<li><a href='/".$channel."/".$bid."/".$sid."'>".$sname."</a></li>";}
		if ($title<>"") {$str=$str. "<li>".$title."</li>";}
		if ($keyword<>"") {$str=$str. "<li>关键字中含有“".$keyword."”</li>";}
	}else{
		$str=$str. "<li><a href='".$channel.".php'>".getchannelname($channel)."</a></li>" ;
      	if ($bid<>"") {$str=$str. "<li><a href='/".$channel."/".$channel.".php?b=".$bid."'>".$bname."</a></li>";}		
		if ($sid<>"") {$str=$str. "<li><a href='/".$channel."/".$channel.".php?b=".$bid."&s=".$sid."'>".$sname."</a></li>";}
		if ($title<>"") {$str=$str. "<li>".$title."</li>";}
		if ($keyword<>"") {$str=$str. "<li>关键字中含有“".$keyword."”</li>";}
	}
return $str;	
}

function getchannelname($channel){
switch ($channel){
case "zs";return channelzs;break;
case "zsclass";return channelzs;break;
case "pp";return "品牌";break;
case "job";return "招聘";break;
case "dl";return channeldl;break;
case "zh";return "展会";break;
case "zx";return "资讯";break;
case "wangkan";return "网刊";break;
case "baojia";return '报价';break;
case "ask";return '问答';break;
case "special";return "专题";break;
case "company";return "企业";break;
case "user";return "用户";break;
}
}

function checkyzm($yzm){
if($yzm!=$_SESSION["yzm_math"]){showmsg('验证问题答案错误！','back');}
}

function getimgincontent($content,$num=1){
preg_match_all("/<[img].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp]))[\'|\"].*?[\/]?>/i",$content,$match);
switch ($num){
case 1;return @$match[1][0];break;//只取第一个
case 2;return @$match[1];break;//取出所有，返回的是一个数组
}
}

function getimg2($img) { //从getimgincontent正则后，做二次提取
if ($img<>''){
//$img=strtolower($img);//转小写后，导致与内容中的不对应，无法替换为本地路径的图片
if (strpos($img,'jpg')!==false){
$format='jpg';
}elseif (strpos($img,'gif')!==false){
$format='gif';
}elseif (strpos($img,'png')!==false){
$format='png';
}elseif (strpos($img,'bmp')!==false){
$format='bmp';
}
$end=strpos($img,$format)+3;
return substr($img,0,$end);
}
}

function cutstr($tempstr,$tempwid){
if (strlen($tempstr)/3>$tempwid){
return mb_substr($tempstr,0,$tempwid,'utf8').".";
}else{
return $tempstr;
}
}

function showannounce($cs){
$cs=explode(",",$cs); //传入的$cs是一个整体字符串,转成数组
$numbers=isset($cs[0])?$cs[0]:2;checkid($numbers,0,'{#showannounce}标签的第1个参数须为大于0的整数');
$titlelong=isset($cs[1])?$cs[1]:20;checkid($titlelong,0,'{#showannounce}标签的第2个参数须为大于0的整数');
if (isset($_COOKIE['closegg'])){
$str='';
}else{
	$n=1;$str='';
	$sql="select title,id,content,sendtime from zzcms_help where classid=2 and elite=1 order by id desc limit 0,$numbers";
	$rs=query($sql);
	//echo $sql;
	$row=num_rows($rs);
	if ($row){
	$str=$str ."<div id='gonggao'><span onclick=\"gonggao.style.display='none'\"><a href=javascript:delCookie('closegg')>×</a></span>";
		while ($row=fetch_array($rs)){
		$str=$str ."<li>公告【". $n ."】<a href=javascript:openwindow('/one/announce_show.php?id=".$row["id"]."',700,300)>".cutstr(strip_tags($row["title"]),$titlelong)." [".date("Y-m-d",strtotime($row['sendtime']))."] </a></li>";
		$n++;
		}
	$str=$str ."</div>";
	}
	}
return $str;
}

function showselectpage($pagename,$page_size,$b,$s,$page){
$str="<select name='menu1' onchange=MM_jumpMenu('parent',this,0)>";
$cs="/".$pagename."/".$pagename.".php?b=".$b."&s=".$s."&page=".$page;
if ($page_size=="20"){
$str=$str . "<option value='".$cs."&page_size=20' selected >20条/页</option>";
}else{
$str=$str . "<option value='".$cs."&page_size=20' >20条/页</option>";
}
if ($page_size=="50") {
$str=$str . "<option value='".$cs."&page_size=50' selected >50条/页</option>";
}else{
$str=$str . "<option value='".$cs."&page_size=50' >50条/页</option>";
}
if ($page_size=="100"){
$str=$str . "<option value='".$cs."&page=".$page."&page_size=100' selected >100条/页</option>";
}else{
$str=$str . "<option value='".$cs."&page=".$page."&page_size=100' >100条/页</option>";
}
$str=$str . "</select>";
return $str;
}

function getsmallimg($img){
if (substr($img,0,4) == "http"){
return $img;//复制的网上的图片，不生成小图片，直接显示大图
}elseif(strpos($img,'_small') !== false){
return $img;//已是小图的不再重加后缀_small
}else{
return siteurl.str_replace(".jpeg","_small.jpeg",str_replace(".png","_small.png",str_replace(".gif","_small.gif",str_replace(".jpg","_small.jpg",$img))));
}
}

function makesmallimg($img){
$img=substr($img,0);
$imgbig=zzcmsroot2.$img;
if(!file_exists($imgbig)){
echo "<script>alert('封面图片不存在，没有生成缩略图')</script>";
}else{
	$imgsmall=str_replace(siteurl,"",getsmallimg($imgbig));
	$sImgName =$imgsmall;
	$sImgSize=120;
	$data=GetImageSize($imgbig);//取得GIF、JPEG、PNG或SWF图片属性，返回数组，图形的宽度[0],图形的高度[1]，文件类型[2]
	if($data[2]!=4){//文件类型不为4，4为swf格式
    	switch ($data[2]) {
     	case 1 :$sImg = imagecreatefromgif($imgbig);break;
     	case 2 :$sImg = imagecreatefromjpeg($imgbig);break;
     	case 3 :$sImg = imagecreatefrompng($imgbig);break;
     	case 6 :$sImg = imagecreatefromwbmp($imgbig);break;
     	//default :echo "不支持的文件类型，无法生成缩略图";
    	}
		//生成小图
		if ($data[1]!=0 && $data[0]!=0){
		if ($data[1]>$data[0]){
		$newwidth=$sImgSize*($data[0]/$data[1]) ;
		$newheight= $sImgSize;
		}else{
		$newwidth=$sImgSize;
		$newheight=$sImgSize*($data[1]/$data[0]) ;
		}  
		$sImgDate = imagecreatetruecolor($newwidth,$newheight);   
		imagecopyresampled($sImgDate,$sImg, 0, 0, 0, 0, $newwidth, $newheight, $data[0],$data[1]);
    	switch ($data[2]) {
     	case 1 :imagegif($sImgDate, $sImgName);break;
     	case 2 :imagejpeg($sImgDate, $sImgName);break;
     	case 3 :imagepng($sImgDate, $sImgName);break;
     	case 6 :imagewbmp($sImgDate, $sImgName);break;
    	}
    	imagedestroy($sImgDate);
       	$isok=imagedestroy($sImg);
		//if ($isok){echo "生成小图片成功:".$sImgName;}	
		}
   	}
}
}

function grabimg($url,$filename="") {
   if($url==""):return false;endif;
   if($filename=="") {
     $ext=strrchr($url,".");
     if($ext!=".gif" && $ext!=".jpg" && $ext!=".png"&& $ext!=".bmp"):return false;endif;
	 $filename_dir=zzcmsroot2.'uploadfiles/'.date("Y-m"); //上传文件地址 采用绝对地址方便upload.php文件放在站内的任何位置 
	 if (!file_exists($filename_dir)) {
	 @mkdir($filename_dir,0777,true);
	 }
	 $filename=$filename_dir."/".date("YmdHis").rand(100,999).$ext;
   }
   ob_start();
   readfile($url);
   $img = ob_get_contents();
   ob_end_clean();
   $size = strlen($img);

   $fp2=@fopen($filename, "a");
   fwrite($fp2,$img);
   fclose($fp2);
   return $filename;
}

function showprovince($cs){
global $province,$citys;
$cs=explode(",",$cs); //传入的$cs是一个整体字符串,转成数组
$channel=isset($cs[0])?$cs[0]:'';
$column=isset($cs[1])?$cs[1]:5;

	$str="<table width='100%' border='0' cellpadding='5' cellspacing='1' class='bgcolor3'><tr>";
	$city=explode("#",$citys);
		$c=count($city);//循环之前取值
	for ($i=1;$i<$c;$i++){ 
		$location_p=explode("*",$city[$i]);//取数组的第一个就是省份名，也就是*左边的
		//$str=$str . "<a href=?province=".$location_p[0]."&p_id=".$i.">".$location_p[0]."</a>&nbsp;&nbsp;";
		if ($location_p[0]==$province){
		$str=$str . "<td align='center' bgcolor='#FFFFFF' style='font-weight:bold'>" ;
		}else{
		$str=$str . "<td align='center' class='bgcolor1'>" ;
		}
		if ($channel=="area") {
		$str=$str ."<a href='/area/show.php?province=".$location_p[0]."&p_id=".$i."'>".$location_p[0]."</a>";
		}else{	
		$str=$str . "<a href='/".$channel."/search.php?province=".$location_p[0]."&p_id=".$i."'>".$location_p[0]."</a>";
		}
		$str=$str . "</td>" ;
		if ($i % $column==0) {$str=$str."</tr>";}
	}
	$str=$str. "</table>";
	return $str;
}

function showkeyword($cs){
global $keyword,$siteskin;
$cs=explode(",",$cs); //传入的$cs是一个整体字符串,转成数组
$channel=isset($cs[0])?$cs[0]:'zs';
$numbers=isset($cs[1])?$cs[1]:10;checkid($numbers);
$column=isset($cs[2])?$cs[2]:5;checkid($column);
	
if ($channel=='zs' || $channel=='dl'){
$fpath=zzcmsroot2."cache/zskeyword.txt";
}elseif ($channel=='zx'){
$fpath=zzcmsroot2."cache/zxkeyword.txt";
}

if (cache_update_time!=0 && file_exists($fpath)!==false && time()-filemtime($fpath)<3600*24*cache_update_time){
	return file_get_contents($fpath);
}else{
	if ($channel=='zs'||$channel=='dl'){
	$sql= "select keyword,url from zzcms_tagzs order by xuhao asc";
	}elseif($channel=='zx'){
	$sql= "select keyword,url from zzcms_tagzx order by xuhao asc";
	}
	$rs=query($sql);
	$row=num_rows($rs);
	if ($row){
	$str="";
	$liwidth=100/$column-3;
		while ($row=fetch_array($rs)){
		if ($row["keyword"]==$keyword) {
		$str=$str . "<li style='font-weight:bold;width:".$liwidth."%;display:inline-block'>";
		}else{
		$str=$str . "<li style='width:".$liwidth."%;display:inline-block'>";
		}
		$str=$str . "<a href='/".$channel."/search.php?keyword=".$row["keyword"]."'>".$row["keyword"]."</a></li>\r\n";			
		}
	}else{
	$str= "暂无信息";
	}
	return $str;
		
	if ($channel=='zs'||$channel=='dl'){
	$fpath=zzcmsroot2."cache/zskeyword.txt";
	}elseif ($channel=='zx'){
	$fpath=zzcmsroot2."cache/zxkeyword.txt";
	}
	if (!file_exists("../cache")) {mkdir("../cache",0777,true);}
	$fp=fopen($fpath,"w+");//fopen()的其它开关请参看相关函数
	fputs($fp,$str);//写入文件
	fclose($fp);
}	
}

function isaddsiteurl($str){
if (strpos($str,'http')!==false) {//有http时用的是网络图片前面就不加siteurl了
return $str;
}else{
return siteurl.$str;
}
}

function showad($cs){
global $siteskin;
$cs=explode(",",$cs); //传入的$cs是一个整体字符串,转成数组
$b=isset($cs[0])?$cs[0]:'';
$s=isset($cs[1])?$cs[1]:'';
$num=isset($cs[2])?$cs[2]:'';
$titlelong=isset($cs[3])?$cs[3]:0;
$bianhao=isset($cs[4])?$cs[4]:'';
$fp=zzcmsroot2."cache/".$siteskin."/adv_".pinyin($b)."_".pinyin($s).".htm";//广告中文类别名转换成拼音字母来给缓存文件命名
if (cache_update_time!=0 && file_exists($fp) && filesize($fp)>10 && time()-filemtime($fp)<3600*24*cache_update_time ) {
//按管理员设定的时间更新,//utf-8有文件头，空文件大小为3字节
	$fso = fopen($fp,'r');
	$fcontent = fread($fso,filesize($fp));
	fclose($fso);
	return $fcontent;
}else{
$n=1;$str='';
//sql= "select * from zzcms_ad where endtime>= '"&date()&"' "
$sql= "select * from zzcms_ad where bigclassname='".$b."' and smallclassname='".$s."' order by xuhao asc,id asc ";
if ($num<>0){$sql= $sql. " limit 0,$num";}
$rs=query($sql);
$row=num_rows($rs);
if ($row){   
$str="<ul>";
while ($row=fetch_array($rs)){
$str=$str."<li> ";
if (isshowad_when_timeend=="No" && $row["endtime"]<=date('Y-m-d H:i:s')){ //到期的
$str=$str. showadtext;
}else{		
$str=$str. "<a href='".addhttp($row["link"])."' target='_blank' style='color:".$row["titlecolor"]."'>";
	if ($row["img"]<>"" ) {//有图片的
	$str=$str. "<img data-original='".isaddsiteurl($row["img"])."'  alt='".$row["title"]."' />";
		if ($titlelong!=0){
		if ($bianhao=='yes'){$str=$str.addzero($n,2)."-";}
		$str=$str.cutstr($row["title"],$titlelong);
		}
	}else{//无图的
		if ($bianhao=='yes'){$str=$str.addzero($n,2)."-";}
		if ($titlelong!=0){$str=$str.cutstr($row["title"],$titlelong);}else{$str=$str.$row["title"];}
	}		
$str=$str."</a>";
}
$str=$str."</li>\n";
$n=$n+1;
}
$str=$str."</ul>";
}
	if (cache_update_time!=0){
	$fp=zzcmsroot2."cache/".$siteskin."/adv_".pinyin($b)."_".pinyin($s).".htm";
	if (!file_exists(zzcmsroot2."cache/".$siteskin)) {mkdir(zzcmsroot2."cache/".$siteskin,0777,true);}
	$f=fopen($fp,"w+");//fopen()的其它开关请参看相关函数
	fputs($f,$str);
	fclose($f);
	}
return $str;
}
}

function myip2long($ip){ //在PHP 4.x，PHP 5.x中 ip2long 有前导零的ip转换的结果都不正确。这里自义定了此函数
   $ip_arr = explode('.',$ip); 
   $iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]); 
   return $iplong; 
}

function stopip(){
if (stopip<>''){
$isok=false;
$stopip=myip2long(getip()); 
$ip=explode(PHP_EOL,trim(stopip));//循环出内容项内的项目//去除首尾的换行符
	for ($i=0,$size=count($ip);$i<$size;$i++){
	$ips=explode("|",$ip[$i]); //$ips[0]为起始IP $ips[1]为结束IP
	if ($stopip==myip2long($ip[$i]) || ($stopip>=myip2long($ips[0]) && $stopip<=myip2long($ips[1]))){$isok=true;break;}
	}
if ($isok==true){WriteErrMsg("此IP被封，不能访问本站!");exit();}
} 
}

function stripfxg($string,$htmlspecialchars_decode=false,$nl2br=false) {//去反斜杠 
$string=stripslashes($string);//去反斜杠,不开get_magic_quotes_gpc 的情况下，在stopsqlin中都加上了，这里要去了
if ($htmlspecialchars_decode==true){
$string=htmlspecialchars_decode($string);//转html实体符号
}
if ($nl2br==true){
$string=nl2br($string);
}
return $string; 
}

function strbetween($str,$start,$end,$startadd=0) { 
$a= strpos($str,$start)+strlen($start)+$startadd;//在起始标识$start所在位后追加数字，如取src="后的字符时，双引号无法直接表示，所以加这个startadd可以解决这种问题
if (strpos($str,$start)!==false){ 
$b= strpos($str,$end,$a);//必须定起始位置
return substr($str,$a,$b-$a); 
}
}

function getfirstchar($s0){ //获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = @iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "a";
    if($asc >= -20283 and $asc <= -19776) return "b";
    if($asc >= -19775 and $asc <= -19219) return "c";
    if($asc >= -19218 and $asc <= -18711) return "d";
    if($asc >= -18710 and $asc <= -18527) return "e";
    if($asc >= -18526 and $asc <= -18240) return "f";
    if($asc >= -18239 and $asc <= -17923) return "g";
    if($asc >= -17922 and $asc <= -17418) return "h";
    if($asc >= -17922 and $asc <= -17418) return "i";
    if($asc >= -17417 and $asc <= -16475) return "j";
    if($asc >= -16474 and $asc <= -16213) return "k";
    if($asc >= -16212 and $asc <= -15641) return "l";
    if($asc >= -15640 and $asc <= -15166) return "m";
    if($asc >= -15165 and $asc <= -14923) return "n";
    if($asc >= -14922 and $asc <= -14915) return "o";
    if($asc >= -14914 and $asc <= -14631) return "p";
    if($asc >= -14630 and $asc <= -14150) return "q";
    if($asc >= -14149 and $asc <= -14091) return "r";
    if($asc >= -14090 and $asc <= -13319) return "s";
    if($asc >= -13318 and $asc <= -12839) return "t";
    if($asc >= -12838 and $asc <= -12557) return "w";
    if($asc >= -12556 and $asc <= -11848) return "x";
    if($asc >= -11847 and $asc <= -11056) return "y";
    if($asc >= -11055 and $asc <= -10247) return "z";
    return NULL;
    //return $s0;
}	
	
function getfirstchar_all($str){  //获取整条字符串所有汉字拼音首字母
    $ret = "";
    @$s1 = iconv("UTF-8","gb2312", $str);//有出错的比如 囍
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $str){$str = $s1;}
    for($i = 0; $i < strlen($str); $i++){
        $s1 = substr($str,$i,1);
        $p = ord($s1);
        if($p > 160){
            $s2 = substr($str,$i++);
            $ret .= getfirstchar($s2);
        }else{
            $ret .= $s1;
        }
    }
    return $ret;
}	
	
//取得拼音
function pinyin($_String, $_Code='UTF8'){ //GBK页面可改为gb2312，其他随意填写为UTF8
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha". 
                        "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|". 
                        "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er". 
                        "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui". 
                        "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang". 
                        "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang". 
                        "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue". 
                        "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne". 
                        "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen". 
                        "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang". 
                        "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|". 
                        "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|". 
                        "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu". 
                        "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you". 
                        "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|". 
                        "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo"; 
        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990". 
                        "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725". 
                        "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263". 
                        "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003". 
                        "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697". 
                        "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211". 
                        "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922". 
                        "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468". 
                        "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664". 
                        "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407". 
                        "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959". 
                        "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652". 
                        "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369". 
                        "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128". 
                        "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914". 
                        "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645". 
                        "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149". 
                        "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087". 
                        "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658". 
                        "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340". 
                        "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888". 
                        "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585". 
                        "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847". 
                        "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055". 
                        "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780". 
                        "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274". 
                        "|-10270|-10262|-10260|-10256|-10254"; 
        $_TDataKey   = explode('|', $_DataKey); 
        $_TDataValue = explode('|', $_DataValue);
        $_Data = array_combine($_TDataKey, $_TDataValue);
        arsort($_Data); 
        reset($_Data);
        if($_Code!= 'gb2312') $_String = _U2_Utf8_Gb($_String); 
        $_Res = ''; 
        for($i=0; $i<strlen($_String); $i++) { 
                $_P = ord(substr($_String, $i, 1)); 
                if($_P>160) { 
                        $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536;
                } 
                $_Res .= _pinyin($_P, $_Data); 
        } 
        return preg_replace("/[^a-z0-9A-Z]*/", '', $_Res); 
} 
function _pinyin($_Num, $_Data){ 
        if($_Num>0 && $_Num<160 ){
                return chr($_Num);
        }elseif($_Num<-20319 || $_Num>-10247){
                return '';
        }else{ 
                foreach($_Data as $k=>$v){ if($v<=$_Num) break; } 
                return $k; 
        } 
}
function _U2_Utf8_Gb($_C){ 
        $_String = ''; 
        if($_C < 0x80){
                $_String .= $_C;
        }elseif($_C < 0x800) { 
                $_String .= chr(0xC0 | $_C>>6); 
                $_String .= chr(0x80 | $_C & 0x3F); 
        }elseif($_C < 0x10000){ 
                $_String .= chr(0xE0 | $_C>>12); 
                $_String .= chr(0x80 | $_C>>6 & 0x3F); 
                $_String .= chr(0x80 | $_C & 0x3F); 
        }elseif($_C < 0x200000) { 
                $_String .= chr(0xF0 | $_C>>18); 
                $_String .= chr(0x80 | $_C>>12 & 0x3F); 
                $_String .= chr(0x80 | $_C>>6 & 0x3F); 
                $_String .= chr(0x80 | $_C & 0x3F); 
        } 
        return iconv('UTF-8', 'GB2312', $_String); 
}
function passed($table,$classid=0){
global $username;
	if(check_user_power('passed')=='yes'){
	query("update `$table` set passed=1 where editor='".$username."'");
	}
}
function show2url($editor){
if (strpos(siteurl,"www.")!==false){
return "http://".$editor.".".substr(siteurl,strpos(siteurl,".")+1);
}else{
	$n=count(explode(".",siteurl));
	if ($n==2){//http://zzcms.net的情况
	return "http://".$editor.".".str_replace("http://",'',siteurl);
	}
	if ($n==3){//分两种情况：1 http://demo.zzcms.net的情况2 http://zzcms.net.cn
		if (strpos(siteurl,".com.cn")!==false or strpos(siteurl,".net.cn")!==false or strpos(siteurl,".org.cn")!==false){
		return "http://".$editor.".".str_replace("http://",'',siteurl);
		}else{
		return "http://".$editor.".".substr(siteurl,strpos(siteurl,".")+1);
		}
	}
}
}	
function province_zm2hz($zm){
$province='';
$zm=strtolower($zm);
switch ($zm){
case'beijing':$province="北京";break;
case'shanghai':$province="上海";break;
case'tianjin':$province="天津";break;
case'chongqing':$province="重庆";break;
case'hebei':$province="河北";break;
case'shanxi':$province="山西";break;
case'liaoning':$province="辽宁";break;
case'jilin':$province="吉林";break;
case'heilongjiang':$province="黑龙江";break;
case'jiangshu':$province="江苏";break;
case'zejinag':$province="浙江";break;
case'anhui':$province="安徽";break;
case'fujian':$province="福建";break;
case'jiangxi':$province="江西";break;
case'shandong':$province="山东";break;
case 'henan':$province="河南";break;
case'hubei':$province="湖北";break;
case'hunan':$province="湖南";break;
case'guangdong':$province="广东";break;
case'guangxi':$province="广西";break;
case'neimenggu':$province="内蒙古";break;
case'hainan':$province="海南";break;
case'shichuan':$province="四川";break;
case'guizhou':$province="贵州";break;
case'yunnan':$province="云南";break;
case'xizhang':$province="西藏";break;
case'shanxisheng':$province="陕西";break;
case'ganshu':$province="甘肃";break;
case'ningxia':$province="宁夏";break;
case'qinghai':$province="青海";break;
case'xinjiang':$province="新疆";break;
case'hongkong':$province="香港";break;
case'aomen':$province="澳门";break;
default:$province=$zm;
}
return $province;	
}

function getIPLoc_sina($queryIP){     
$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;     
$ch = curl_init($url);     
//curl_setopt($ch,CURLOPT_ENCODING ,'utf8');     
curl_setopt($ch, CURLOPT_TIMEOUT, 10);     
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回     
$location = curl_exec($ch);     
$location = json_decode($location);     
curl_close($ch);         
 $loc = "";     
 if($location===FALSE) return "";     
 if (empty($location->desc)) {        
  @$loc = $location->province.$location->city.$location->district.$location->isp;     
 }else{         
 $loc = $location->desc;     
 }     
 return $loc; 
}

function sitecount($channel){
if ($channel=='zs'){
$sql="select count(*) as total from zzcms_main";
}else{
$sql="select count(*) as total from zzcms_".$channel;
}
$rs=query($sql);
$row = fetch_array($rs);
$totlenum = $row['total'];
return "<li>".getchannelname($channel)."<span>".$totlenum."</span></li>";
}

function showsitecount($cs){ 
global $siteskin;
$fpath=zzcmsroot2."cache/".$siteskin."/sitecount.txt";
if (cache_update_time!=0 && file_exists($fpath)!==false && time()-filemtime($fpath)<3600*24*cache_update_time){
	return file_get_contents($fpath);
}else{	
$str='';
if (strpos($cs,'users')!==false){$str=$str.sitecount('user');}
if (strpos($cs,'zs')!==false){$str=$str.sitecount('zs');}
if (strpos($cs,'dl')!==false){$str=$str.sitecount('dl');}
if (strpos($cs,'pp')!==false){$str=$str.sitecount('pp');}
if (strpos($cs,'zh')!==false){$str=$str.sitecount('zh');}
if (strpos($cs,'job')!==false){$str=$str.sitecount('job');}
if (strpos($cs,'zx')!==false){$str=$str.sitecount('zx');}
if (strpos($cs,'special')!==false){$str=$str.sitecount('special');}
if (strpos($cs,'wangkan')!==false){$str=$str.sitecount('wangkan');}
if (strpos($cs,'baojia')!==false){$str=$str.sitecount('baojia');}
if (cache_update_time!=0){
	$fpath=zzcmsroot2."cache/".$siteskin."/sitecount.txt";
	$fp=fopen($fpath,"w+");//fopen()的其它开关请参看相关函数
	fputs($fp,stripfxg($str));//写入文件
	fclose($fp);
}	
return $str;
}
}

//显示联系方式在job/show.php,zs/show.php,pp/show.php
function showcontact($channel,$cpid,$startdate,$comane,$kind,$editor,$userid,$groupid,$somane,$sex,$phone,$qq,$email,$mobile,$fox){
checkid($groupid,1);
checkid($kind,1);//类别有为0的情况，所以第二个参数要设一下，设为1
$contact="<div id='zscontact'>" ;
$contact=$contact . "<ul>";
$contact=$contact . "<li>";
//$sqln="select groupname,grouppic,groupid,config from zzcms_usergroup where groupid=(select groupid from zzcms_user where username=(Select editor From zzcms_main where id='$cpid'))";
$sqln="select groupname,grouppic,groupid,config from zzcms_usergroup where groupid=$groupid";
$rsn=query($sqln);
$rown=fetch_array($rsn);
	if ($rown["groupid"]>1) {
	$contact=$contact . "<img src='/image/cxqy.png'/>";
	$contact=$contact . "<img src='/image/viptime/".(date('Y')-date('Y',strtotime($startdate))+1).".png'/>";
	}else{
	$contact=$contact . "<img src='".$rown["grouppic"]."'/>";
	$contact=$contact . "&nbsp;".$rown["groupname"];
	}
$showcontact=str_is_inarr($rown["config"],'showcontact');
$contact=$contact . "</li>";
$contact=$contact . "<li style='font-weight:bold'>".$comane."</li>";
$contact=$contact . "<li>";
	if ($kind<>"" && $kind<>0 ) {
	$rsn=query("select classname from zzcms_userclass where classid=".$kind."");
	$rown=fetch_array($rsn);
	$contact=$contact ."经营模式：".$rown["classname"];
	}else{
	$contact=$contact ."经营模式：未选择";
	}
$contact=$contact ."</li>";
$contact=$contact ."<li style=height:36px>";
	if (sdomain=="Yes") {
	$contact=$contact . "<a href='".show2url($editor)."'>";
	}else{
	$contact=$contact . "<a href='".getpageurl("zt",$userid)."'>";
	}
$contact=$contact . "<img src='/image/button_site.gif'  border='0' /></a></li>";
if ($showcontact=='yes'  || @$_COOKIE["dlliuyan"]==$editor) {
//if ($showcontact=='yes' ) {
	$contact=$contact . "<li>联系人：<b>".$somane."</b>&nbsp;";
	if ($sex==1){ 
	$contact=$contact . "先生";
	}elseif($sex==0){
	$contact=$contact . "女士";
	}
	$contact=$contact . "</li>";
	$contact=$contact . "<li>电　话：".$phone."</li>";
	$contact=$contact . "<li>传　真：".$fox."</li>";
	$contact=$contact . "<li>手　机：".$mobile."</li>";
	$contact=$contact . "<li>E-mail：".$email."</li>";
	if ($qq<>"") {
	$contact=$contact . "<li><a target=blank href=http://wpa.qq.com/msgrd?v=1.uin=".$qq.".Site=".sitename.".Menu=yes><img border=0 src=http://wpa.qq.com/pa?p=1:".$qq.":10 alt='QQ交流'></a> ";
	$contact=$contact . "</li>";
	}	
}else{
	if ($channel=="job"){
	$contact=$contact . "<li style='height:50px'>普通会员联系方式不显示，应聘者请直接投简历到该公司EMAIL</li>";
	}else{
	$contact=$contact . "<li>联系方式<a href='#dl_liuyan'><b>留言</b></a>后在此显示。</li>";
	}
}
$contact=$contact . "</ul>";
$contact=$contact . " </div>";
return $contact;
}

function removeBOM($str = ''){
if (substr($str,0,3) == pack("CCC",0xef,0xbb,0xbf)) {
$str = substr($str, 3);
}
return $str;
}

function del_dirandfile( $dir ){
if (file_exists($dir)){
if ( $handle = opendir( "$dir" ) ) {
	while ( false !== ( $item = readdir( $handle ) ) ) {
	if ( $item != "." && $item != ".." ) {
		if ( is_dir( "$dir/$item" ) ) {
		del_dirandfile( "$dir/$item" );
		} else {
		if( unlink( "$dir/$item" ) )echo "成功删除文件：".$dir."/".$item."<br /> ";
		}
	}
	}
   closedir( $handle );
   if( rmdir( $dir ) )echo "成功删除目录：". $dir."<br /> ";
}
}else{
echo "目录已不存在，已完成清理<br />"; 
}
//echo "缓存已被清理<br />";
}

function formatnumber($number){
if($number >= 10000){return sprintf("%.2f", $number/10000)."万";}else{return $number;}
}

function checkver($str){
if(strpos($str,base64_decode('aHR0cDovL3d3dy56emNtcy5uZXQ='))===false){
WriteErrMsg(base64_decode('PGRpdiBzdHlsZT0nZm9udC1zaXplOjIwcHgnPuWFjei0ueeJiCzli7/liKDmlLlaWkNNU+agh+ivhu+8gei/mOWOn+WQjizliJnkuI3lho3mj5DnpLo8L2Rpdj4='));
}
}

function checkadminisdo($str){
$rs=query("select config from zzcms_admingroup where id=(select groupid from zzcms_admin where pass='".@$_COOKIE["pass"]."' and admin='".@$_COOKIE["admin"]."')");//只验证密码会出现，两个管理员密码相同的情况，导致出错,前加@防止SESSION失效后出错提示
	$row=fetch_array($rs);
	$config=$row["config"];
	if(str_is_inarr($config,$str)=='no'){showmsg('没有操作权限!');}
}

function checkadminisdo2($str){
$rs=query("select config from zzcms_admingroup where id=(select groupid from zzcms_admin where pass='".@$_COOKIE["pass"]."' and admin='".@$_COOKIE["admin"]."')");
	$row=fetch_array($rs);
	$config=$row["config"];
	if(str_is_inarr($config,$str)=='no'){
	return 'no';
	}elseif(str_is_inarr($config,$str)=='yes'){
	return 'yes';
	}
}

function check_user_power($str){
global $username;
if (!isset($username)){
$username=$_COOKIE["UserName"];
}
$rs=query("select config from zzcms_usergroup where groupid=(select groupid from zzcms_user where username='".$username."')");
	$row=fetch_array($rs);
	$config=$row["config"];
	if (str_is_inarr($config,$str)=='yes'){return 'yes';}else{return 'no';}
}

function str_is_inarr($arrs,$str){
if(strpos($arrs,'#')!==false){//多个,循环值后对比,内容较多，要转换成数组，如果只用strpos字符判断，有重复的字符
$arr=explode("#",$arrs); //转换成数组
	if(in_array($str,$arr)){return 'yes';}else{return 'no';}
}else{//单个,直接对比
	if($arrs==$str){ return 'yes';}else{return 'no';}
}	
}

function get_zhuyuming($str){
$houzhui_array = array(".com",".net",".org",".gov",".edu","com.cn",".cn",".tv",".cc",".top",".xyz");
for($i=0; $i<count($houzhui_array);$i++){
	$str=trim(str_replace($houzhui_array[$i],'',$str));
    }	
 return $str;
}

function check_isip($str){
  if(preg_match("/^[\d]{2,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}$/", $str))
  return true;
  return false;
}
?>