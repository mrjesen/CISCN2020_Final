

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title>正常的输入框</title>
	<link rel="stylesheet" href="css/bootstrap.css" crossorigin="anonymous">
	<script src="css/jquery-3.js" crossorigin="anonymous"></script>
	<script src="css/popper.js" crossorigin="anonymous"></script>
	<script src="css/bootstrap.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <h1>Get The Flag</h1>
    <!-- 
    		$query = "SELECT * FROM fake_flag WHERE id = $id limit 0,$limit";
    		//$query = "SELECT flag FROM real_flag WHERE id = $id limit 0,$limit";
     -->
    <form  method="get" class="form-group">
        <div class="row">
            <div class="col-md-1">
                yourt id
            </div>
            <div class="col-md-4">
                <input type="text" name="id" class="form-control">
            </div>
        </div>
        <input type="text" name="limit" hidden="" value="1">
        <div class="row">
            <input type="submit" value="get it" class="btn" btn-info="">
        </div>
    </form>
</div>
</body>
</html>

<?php
include 'conn.php';

function is_safe($id, $limit){
	
	$common_blacklist = ["!", "\"", "#", "%", "&", "'", ";", "<", "=", ">", "\\", "^", "`", "|" ,'between','not'," ","like",','];
	$hack_list = ['union','join','in',"/**/","substr","ascii","left","right"];
	$id_blacklist = ['left','right','like','(',')'];
	$limit_blacklist = ['-'];

	$blacklist = array_merge ($common_blacklist, $hack_list, $id_blacklist);
	foreach ($blacklist as $value) {
        if (stripos($id, $value) !== false)
            die("your param <b>id</b> look like dangerous!");
    }

	$blacklist = array_merge ($common_blacklist, $hack_list, $limit_blacklist);
	foreach ($blacklist as $value) {
        if (stripos($limit, $value) !== false)
        	die("your param <b>limit</b> look like dangerous!");
    }

    if( $limit > 10 or $limit <= 0)
    {
    	die("your param limit can't >10 or <=0");
    }

    return true;
}



if(isset($_GET["id"]) && isset($_GET['limit'])){
	$id = $_GET["id"];
	$limit = $_GET['limit'];
	if(!is_safe($id, $limit)){
		exit("stop , hack!");
	}

	$query = "SELECT * FROM fake_flag WHERE id = $id limit 0,$limit";
	$result = $mysqli->query($query);
	if ($result === false) {
		die("database error, please check your input");
	}
	//var_dump($result);
	$row = $result->fetch_assoc();
	if($row === NULL){
		echo "searched nothing";
	}else {
		var_dump($row);
	}
	while ($row = $result->fetch_assoc()) {
		var_dump($row);
	}
	$result->free();
	$mysqli->close();
}

?>