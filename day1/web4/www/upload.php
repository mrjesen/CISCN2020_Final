<?php
error_reporting(0);
header("Content-type:text/html;charset=utf-8");

class  hint{
    public function __destruct() {
        echo '<!-- hint:./blog/ciscn_notes.php -->';
    }
}


if( isset( $_POST[ 'Upload' ] ) ) {
    $target_path  = "uploads/";
    $target_path .= basename( $_FILES[ 'upload_file' ][ 'name' ] );
    $uploaded_filename = $_FILES[ 'upload_file' ][ 'name' ];
    $uploaded_ext  = substr( $uploaded_filename, strrpos( $uploaded_filename, '.' ) + 1);
    $uploaded_file_size = $_FILES[ 'upload_file' ][ 'size' ];
    $uploaded_tmp_file  = $_FILES[ 'upload_file' ][ 'tmp_name' ];
    @extract($_POST);
    if( ( strtolower( $uploaded_ext ) == "jpg" || strtolower( $uploaded_ext ) == "jpeg" || strtolower( $uploaded_ext ) == "png" ) && ( $uploaded_size < 100000 ) && getimagesize( $uploaded_tmp_file ) ) {
        if(file_exists($target_path)) {
            echo "<pre>图片已经存在!<pre>";
        }
        else{
            if( !move_uploaded_file( $uploaded_tmp_file, $target_path."tmp.tmp" ) ) {
                echo "<pre>无法保存图片!</pre>";
            }
            else {
                echo "<pre>图片上传成功!</pre>";
            }
        }
    }
    else {
        echo "<pre>只能上传格式为jpg,jpeg和png的图片.</pre>";
        }
}

?>