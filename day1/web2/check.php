<?php
if(isset($_POST["file"]))
{
	highlight_file(__FILE__);
}
if(isset($_POST["dir_test"])&&isset($_POST["file_path"]))
{	
	$file_path=$_POST["file_path"];
	var_dump(scandir($file_path));
}
if(isset($_POST["dir_test"])&&isset($_POST["dir"]))
{
	$dir=$_POST["dir"];
	echo "$dir"." is ".is_writable($dir);
}

