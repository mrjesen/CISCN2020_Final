<?php
ob_start();//打开缓冲区，可以setcookie
include("admin.php");
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/timer.js"></script>	
<script language="javascript" src="../js/gg.js"></script>
<script language = "JavaScript">
function CheckForm(){	  
if (document.myform.bigclassid.value==""){
    alert("请选择大类别！");
	document.myform.bigclassid.focus();
	return false;
  }
if (document.myform.smallclassid.value==""){
    alert("请选择小类别！");
	document.myform.smallclassid.focus();
	return false;
  }    
if (document.myform.title.value==""){
    alert("标题不能为空！");
	document.myform.title.focus();
	return false;
}
if (document.myform.link.value==""){
    alert("链接地址不能为空！");
	document.myform.link.focus();
	return false;
}
} 
</script>
</head>
<body>
<?php
$do=isset($_GET['do'])?$_GET['do']:'';
switch ($do){
case "add";add();break;
case "modify";modify();break;
}


if ($do=="save"){
$page=isset($_POST["page"])?$_POST["page"]:1;//只从修改页传来的值,返回列表页用
checkid($page);
$id=isset($_POST["id"])?$_POST["id"]:0;
checkid($id,1);

$title=str_replace("{","",$title);//过滤{ 如果这里填写调用标签如{#showad:4,0,yes,yes,首页第一行}就会在label中反复替换出现布局上的错乱
$link=str_replace("{","",$link);

if (isset($_POST["noimg"])){$img='';}

$bigclassname=$_POST["bigclassid"];
$smallclassname=$_POST["smallclassid"];

if ($starttime=="") {$starttime=date('Y-m-d');}
if ($endtime==""){$endtime=date('Y-m-d',time()+60*60*24*365);}
$elite=$_POST["elite"];
checkid($elite,1);

$msg='';
if ($img<>''){
	if (substr($img,0,4) == "http"){
		$img_bendi=grabimg($img,"");//如果是远程图片保存到本地
		if($img_bendi):$msg="远程图片".$img."已保存到本地：".$img_bendi."<br>";else:$msg="远程图片".$img."保存到本地 失败";endif; 
		$img=substr($img_bendi,strpos($img_bendi,"/uploadfiles"));//注：这里变量要换成$img以供入库用。在grabimg函数中$img被加了zzcmsroot这里要去掉 
	}
		
	$imgsmall=str_replace(siteurl,"",getsmallimg($img));
	if (file_exists(zzcmsroot.$imgsmall)===false && file_exists(zzcmsroot.$img)!==false){//小图不存在，且大图存在的情况下，生成缩略图
	makesmallimg($img);//同grabimg一样，函数里加了zzcmsroot
	}	
}


checkstr($img,"upload");//入库前查上传文件地址是否合格

if ($_REQUEST["action"]=="add"){
checkadminisdo("adv_add");
$isok=query("INSERT INTO zzcms_ad (bigclassname,smallclassname,title,titlecolor,link,img,username,starttime,endtime,elite,sendtime)
VALUES
('$bigclassname','$smallclassname','$title','$titlecolor','$link','$img','$username','$starttime','$endtime','$elite','".date('Y-m-d H:i:s',time()-(showadvdate+1)*60*60*24)."')");
$id=insert_id();
}elseif ($_REQUEST["action"]=="modify") {
checkadminisdo("adv_modify");
$isok=query("update zzcms_ad set bigclassname='$bigclassname',smallclassname='$smallclassname',title='$title',titlecolor='$titlecolor',link='$link',
img='$img',username='$username',nextuser='$nextuser',
starttime='$starttime',endtime='$endtime',elite='$elite' where id='$id'");		
}
setcookie("title",$title,time()+3600*24,"/admin");
setcookie("bigclassid",$bigclassname,time()+3600*24,"/admin");
setcookie("smallclassid",$smallclassname,time()+3600*24,"/admin");
setcookie("link",$link,time()+3600*24,"/admin");
?>
  
<div class="boxsave"> 
    <div class="title">
	<?php
	if ($_REQUEST["action"]=="add") {echo "添加 ";}else{echo"修改";}
	if ($isok){echo"成功";}else{echo "失败";}
     ?>
	</div>
	<div class="content_a">
	标题：<span style="color:<?php echo $titlecolor?>"><?php echo $title?></span><br/>
	链接：<?php echo $link?>
	<div class="editor">
	<li><a href="?do=add">[继续添加]</a></li>
	<li><a href="?do=modify&id=<?php echo $id?>">[修改]</a></li>
	<li><a href="ad_manage.php?b=<?php echo $bigclassname?>&s=<?php echo $smallclassname?>&page=<?php echo $page?>">[返回]</a></li>
	
	</div>
	</div>
	</div>

<?php
if ($msg<>'' ){echo "<div class='border'>" .$msg."</div>";}
}


function add(){
//checkadminisdo("adv_add");
$stitle=isset($_COOKIE["title"])?$_COOKIE["title"]:'';
$slink=isset($_COOKIE["link"])?$_COOKIE["link"]:'javascript:void(0)';
?>
<div class="admintitle">添加广告</div>
<form name="myform" method="post" action="?do=save" onSubmit="return CheckForm();">
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="20%" align="right" class="border">所属类别：</td>
      <td width="80%" class="border"> 
<?php
$sql = "select classname,parentid from zzcms_adclass where parentid<>'A' order by xuhao asc";
$rs=query($sql);
?>
        <script language = "JavaScript" type="text/JavaScript">
var onecount;
subcat = new Array();
        <?php 
        $count = 0;
        while($row = fetch_array($rs)){
        ?>
subcat[<?php echo $count?>] = new Array("<?php echo trim($row["classname"])?>","<?php echo trim($row["parentid"])?>","<?php echo trim($row["classname"])?>");
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
    }</script> 
	<select name="bigclassid" onChange="changelocation(document.myform.bigclassid.options[document.myform.bigclassid.selectedIndex].value)" size="1">
          <option value="" selected="selected">请选择大类别</option>
          <?php
	$sql = "select classname from zzcms_adclass where parentid='A' order by xuhao asc";
    $rs=query($sql);
	while($row = fetch_array($rs)){
	?>
          <option value="<?php echo trim($row["classname"])?>" <?php if ($row["classname"]==@$_COOKIE["bigclassid"]) { echo "selected";}?>><?php echo trim($row["classname"])?></option>
          <?php
				}
				?>
        </select> <select name="smallclassid">
          <option value="">不指定小类</option>
          <?php
if ($_COOKIE["bigclassid"]!=""){
$sql="select classname from zzcms_adclass where parentid='" .$_COOKIE["bigclassid"]."' order by xuhao asc";
$rs=query($sql);
while($row = fetch_array($rs)){
	?>
<option value="<?php echo $row["classname"]?>" <?php if ($row["classname"]==@$_COOKIE["smallclassid"]) { echo "selected";}?>><?php echo $row["classname"]?></option>
          <?php
			    }
				}
				?>
        </select>		</td>
    </tr>
    <tr> 
      <td align="right" class="border">标题：</td>
      <td class="border"> <input name="title" type="text" id="title" value="<?php echo $stitle?>" size="50">
        标题颜色： 
        <select name="titlecolor" id="titlecolor">
          <option value=""  style="background-color:FFFFFF;color:#000000" >默认</option>
          <option value="red"  style="background-color:red;color:#FFFFFF" >红色</option>
          <option value="green" style="background-color:green;color:#FFFFFF">绿色</option>
          <option value="blue" style="background-color:blue;color:#FFFFFF">蓝色</option>
        </select> </td>
    </tr>
    <tr> 
      <td align="right" class="border">链接地址：</td>
      <td class="border"> <input name="link" type="text" id="link2" value="<?php echo $slink?>" size="50">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input type="submit" name="Submit2" value="提交"></td>
    </tr>
    <tr> 
      <td colspan="2" class="admintitle2">以下内容为图片广告所填写</td>
    </tr>
    <tr> 
      <td align="right" class="border">图片： <input name="img" type="hidden" id="img" ></td>
      <td class="border"> 
 <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
          <tr align="center" bgcolor="#FFFFFF"> 
            <td id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"> <input name="Submit2" type="button"  value="上传图片" /></td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> <input type="submit" name="Submit" value="提交"></td>
    </tr>
    <tr> 
      <td colspan="2" align="right" class="admintitle2">以下内容为收费广告所填写</td>
    </tr>
    <tr> 
      <td align="right" class="border">是否可抢占：</td>
      <td class="border"> <input type="radio" name="elite" value="1">
        不可抢占(收费广告选此项) 
        <input name="elite" type="radio" value="0" checked>
        可抢占</td>
    </tr>
    <tr> 
      <td align="right" class="border">广告主：</td>
      <td class="border"> <input name="username" type="text" id="username" size="25">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">广告期限：</td>
      <td class="border"> <input name="starttime" type="text" id="starttime" value="<?php echo date('Y-m-d')?>" size="10" onFocus="JTC.setday(this)">
        至 
        <input name="endtime" type="text" id="endtime" value="<?php echo date('Y-m-d',time()+60*60*24*365)?>" size="10" onFocus="JTC.setday(this)">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input type="submit"  value="提交">
      <input name="action" type="hidden" id="action" value="add"></td>
    </tr>
  </table>
</form>
<?php
}


function modify(){
//checkadminisdo("adv_modify");
$page = isset($_GET['page'])?$_GET['page']:1;
checkid($page);
$id = isset($_GET['id'])?$_GET['id']:0;
checkid($id,1);

$sql="select * from zzcms_ad where id='$id'";
$rs=query($sql);
$row=fetch_array($rs);
?>
<div class="admintitle">修改广告</div>
<form action="?do=save" method="post" name="myform" >
  <table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr> 
      <td width="19%" align="right" class="border">所属类别：</td>
      <td width="81%" class="border"> 
       <?php
$sqln = "select classname,parentid from zzcms_adclass where parentid<>'A' order by xuhao asc";
$rsn=query($sqln);
?>
        <script language = "JavaScript" type="text/JavaScript">
var onecount;
subcat = new Array();
        <?php 
        $count = 0;
        while($rown = fetch_array($rsn)){
        ?>
subcat[<?php echo $count?>] = new Array("<?php echo trim($rown["classname"])?>","<?php echo trim($rown["parentid"])?>","<?php echo trim($rown["classname"])?>");
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
	$sqln = "select classname from zzcms_adclass where  parentid='A' order by xuhao asc";
    $rsn=query($sqln);
	while($rown = fetch_array($rsn)){
	?>
<option value="<?php echo $rown["classname"]?>" <?php if ($rown["classname"]==$row["bigclassname"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
          <?php
				}
				?>
        </select> <select name="smallclassid">
          <option value="">不指定小类</option>
          <?php
$sqln="select classname from zzcms_adclass where parentid='" .$row["bigclassname"]."' order by xuhao asc";
$rsn=query($sqln);
while($rown = fetch_array($rsn)){
	?>
<option value="<?php echo $rown["classname"]?>" <?php if ($rown["classname"]==$row["smallclassname"]) { echo "selected";}?>><?php echo $rown["classname"]?></option>
          <?php
			    }
				?>
        </select>		</td>
    </tr>
    <tr> 
      <td align="right" class="border">标题：</td>
      <td class="border"> <input name="title" type="text" id="title" value="<?php echo $row["title"]?>" size="50">
        标题颜色： 
        <select name="titlecolor" id="titlecolor">
          <option value=""  style="background-color:FFFFFF;color:#000000" >默认</option>
          <option value="red"  style="background-color:red;color:#FFFFFF" <?php if ($row["titlecolor"]=='red') {echo "selected";}?>>红色</option>
          <option value="green" style="background-color:green;color:#FFFFFF" <?php if ($row["titlecolor"]=="green") {echo "selected";}?>>绿色</option>
          <option value="blue" style="background-color:blue;color:#FFFFFF" <?php if ($row["titlecolor"]=="blue") {echo "selected";}?>>蓝色</option>
        </select> </td>
    </tr>
    <tr> 
      <td align="right" class="border">链接地址：</td>
      <td class="border"> <input name="link" type="text" id="link" value="<?php echo $row["link"]?>" size="50">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input name="id" type="hidden" value="<?php echo $row["id"]?>"> 
        <input name="action" type="hidden"  value="modify"> <input name="page" type="hidden"  value="<?php echo $page?>"> 
        <input type="submit" value="提交"></td>
    </tr>
    <tr> 
      <td colspan="2" class="admintitle2"><strong>以下内容为图片广告所填写</strong></td>
    </tr>
    <tr> 
      <td align="right" class="border">图片： 
        <input name="oldimg" type="hidden" id="oldimg" value="<?php echo $row["img"]?>"> 
        <input name="img" type="hidden" id="img" value="<?php echo $row["img"]?>"></td>
      <td class="border"> 
	  <table width="120" height="120" border="0" cellpadding="5" cellspacing="1" bgcolor="#999999">
          <tr> 
            <td align="center" bgcolor="#FFFFFF" id="showimg" onClick="openwindow('/uploadimg_form.php?noshuiyin=1',400,300)"> 
              <?php
			if ($row["img"]<>""){
			echo "<img src='".$row["img"]."' width='120'  border='0'> 点击可更换图片";
			}else{
			echo "<input name='Submit2' type='button'  value='上传图片'/>";
            }	
			?>            </td>
          </tr>
        </table>
        <input name='noimg' type='checkbox' id="noimg" value='1' />
        选中可改为文字广告</td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"> <input type="submit" name="Submit" value="提交"> </td>
    </tr>
    <tr> 
      <td colspan="2" class="admintitle2"><strong>以下内容为收费广告所填写</strong></td>
    </tr>
    <tr> 
      <td align="right" class="border">广告主：</td>
      <td class="border"> <input name="username" type="text" id="username" value="<?php echo $row["username"]?>" size="25">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">下一个占位的广告主：</td>
      <td class="border"> <input name="nextuser" type="text" id="nextuser" value="<?php echo $row["nextuser"]?>" size="25"></td>
    </tr>
    <tr> 
      <td align="right" class="border">是否可抢占：</td>
      <td class="border"> <input type="radio" name="elite" value="1" <?php if ($row["elite"]==1){ echo "checked";}?>>
        不可抢占(收费广告选此) 
        <input type="radio" name="elite" value="0" <?php if ($row["elite"]==0) { echo "checked";}?>>
        可抢占</td>
    </tr>
    <tr> 
      <td align="right" class="border">广告期限：</td>
      <td class="border"> <input name="starttime" type="text" id="starttime" value="<?php echo $row["starttime"]?>" size="10" onFocus="JTC.setday(this)">
        至 
        <input name="endtime" type="text" id="endtime" value="<?php echo $row["endtime"]?>" size="10"  onFocus="JTC.setday(this)">      </td>
    </tr>
    <tr> 
      <td align="right" class="border">&nbsp;</td>
      <td class="border"><input type="submit"  value="提交">
      <input name="action" type="hidden" id="action" value="modify"></td>
    </tr>
  </table>
</form>
<?php
}
?>
</body>
</html>