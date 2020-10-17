// JavaScript Document
function ConfirmDel(){
   if(confirm("确定要删除吗？一旦删除将不能恢复！"))
     return true;
   else
     return false;	 
}


function anyCheck(form){//form没有启用
	var checks = document.getElementsByName("id[]");
	n = 0;
	for(i=0;i<checks.length;i++){
		if(checks[i].checked)
		n++;
	}
	if (n<1){
	alert("至少要选中 1 条信息");
	return false;
	}
  return true; 	
}

function CheckAll(form){
  for (var i=0;i<form.elements.length;i++){
    var e = form.elements[i];
    if (e.Name != "chkAll")
       e.checked = form.chkAll.checked;
    }
}

  
function uncheckall()   { //切换大类时，清除小类的选中 
var code_Values = document.all['smallclassid[]'];   
if(code_Values.length){   
	for(var i=0;i<code_Values.length;i++) {   
	code_Values[i].checked = false;   
	}   
}else{   
code_Values.checked = false;   
}   
} 

function showfilter2(obj2) {
	if (obj2.style.display=="block") {
        obj2.style.display="none";
    }else {
        obj2.style.display="block";
    }   
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function openwindow(pageurl,iWidth,iHeight){
var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
window.open (pageurl,"","height="+iHeight+",width="+iWidth+",left="+iLeft+",top="+iTop+",toolbar =no,menubar=no,scrollbars=no,resizable=no, location=no,status=no");
}

function valueFormOpenwindow(value){ //子页面引用此函数传回value值,上传图片用
//alert(value);
document.getElementById("img").value=value;
document.getElementById("showimg").innerHTML="<img src='"+value+"' width=120>";
}

function valueFormOpenwindowForFlv(value){ //子页面引用此函数传回value值，上传flv用
//alert(value);
document.getElementById("flv").value=value;
if(value.substr(value.length-3).toLowerCase()=='flv'){//用这个播放器无法播放网络上的SWF格式的视频
        var s1 = new SWFObject("/image/player.swf","ply","200","200","9","#FFFFFF");
          s1.addParam("allowfullscreen","true");
          s1.addParam("allowscriptaccess","always");
          s1.addParam("flashvars","file="+value+"&autostart=true");
          s1.write("container");
		  
	}else if(value.substr(value.length-3).toLowerCase()=='swf'){
	var s1="<embed src='"+value+"' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width=200 height=200></embed>";
	document.getElementById("container").innerHTML=s1+"<br/>点击可修改";
	}
}