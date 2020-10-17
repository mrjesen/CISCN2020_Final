<?php
$url = $_GET['url'];
$parts = parse_url($url);
if(empty($parts['host']) || $parts['host'] != 'localhost') {
    exit('error');
}
#readfile($url);
?>
