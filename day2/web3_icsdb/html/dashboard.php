<?php
session_start();
include_once "lib.php";

if (isset($_POST["username"])
    && isset($_POST["password"])
    && isset($_POST["age"])
    && isset($_POST["email"])
    && is_string($_POST["username"])
    && is_string($_POST["password"])
    && is_string($_POST["age"])
    && is_string($_POST["email"])
)
{
    $user = new User($_POST["username"], $_POST["password"], $_POST["age"], $_POST["email"] );
    $user->register();
} else {
    $user = new User($_SESSION['username'], $_SESSION['password'], $_SESSION['age'], $_SESSION['email']);
    $user->register();
}


if (!isset($_SESSION['username']))
{
    $user->alertMes("please register and login first", "./index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>工业控制系统Dashboard</title>
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/plugins/chartist-js/dist/chartist.min.css" rel="stylesheet">
    <link href="assets/plugins/chartist-js/dist/chartist-init.css" rel="stylesheet">
    <link href="assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
    <link href="assets/plugins/c3-master/c3.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/colors/blue.css" id="theme" rel="stylesheet">
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
</head>

<body class="fix-header fix-sidebar card-no-border">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <b>
                            <img src="assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <span>

                         <img src="assets/images/logo-light-text.png" class="light-logo" alt="homepage" /></span> </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item hidden-sm-down search-box"> <a class="nav-link hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <form class="app-search">
                                <input type="text" class="form-control" placeholder="Search & enter"> <a class="srh-btn"><i class="ti-close"></i></a> </form>
                        </li>
                    </ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?=$user->username;?>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <script src="js/post.js"></script>
                        <li> <a class="waves-effect waves-dark" href='#' aria-expanded="false"><i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</span></a></li>
                    </ul>
                    <div class="text-center m-t-30">
                        <a href="#" class="btn waves-effect waves-light btn-warning hidden-md-down">开发中</a>
                    </div>
                </nav>
            </div>
            <div class="sidebar-footer">
                <!-- item--><a href="" class="link" data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
                <!-- item--><a href="" class="link" data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
                <!-- item--><a href="logout.php" class="link" data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a> </div>
        </aside>
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <div class="col-md-7 col-4 align-self-center">
                        <a href="#" class="btn waves-effect waves-light btn-danger pull-right hidden-sm-down"> Upgrade to Pro</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap">
                                            <div>
                                                <h3 class="card-title">Sales Overview</h3>
                                                <h6 class="card-subtitle">This month's Products Vs The last month's Products</h6> </div>
                                            <div class="ml-auto">
                                                <ul class="list-inline">
                                                    <li>
                                                        <h6 class="text-muted text-success"><i class="fa fa-circle font-10 m-r-10 "></i>This month</h6> </li>
                                                    <li>
                                                        <h6 class="text-muted  text-info"><i class="fa fa-circle font-10 m-r-10"></i>The last month</h6> </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="amp-pxl" style="height: 360px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="card">
                            <div class="card-block">
                                <h3 class="card-title">Our Machines </h3>
                                <h6 class="card-subtitle">Different Machines Used to Control</h6>
                                <div id="visitor" style="height:290px; width:100%;"></div>
                            </div>
                            <div>
                                <hr class="m-t-0 m-b-0">
                            </div>
                            <div class="card-block text-center ">
                                <ul class="list-inline m-b-0">
                                    <li>
                                        <h6 class="text-muted text-info"><i class="fa fa-circle font-10 m-r-10 "></i>PLC</h6> </li>
                                    <li>
                                        <h6 class="text-muted  text-primary"><i class="fa fa-circle font-10 m-r-10"></i>SCADA</h6> </li>
                                    <li>
                                        <h6 class="text-muted  text-success"><i class="fa fa-circle font-10 m-r-10"></i>DCS</h6> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-xlg-3 col-md-5">
                        <div class="card">
                            <img class="card-img-top" src="assets/images/background/profile-bg.jpg" alt="Card image cap">
                            <div class="card-block little-profile text-center">
                                <div class="pro-img" id="pro-avatar">
                                </div>
                                <h3 class="m-b-0">admin</h3>
                                <p>Administrator of Industrial Control System</p>
                                <a href="#" class="m-t-10 waves-effect waves-dark btn btn-primary btn-md btn-rounded">Follow</a>
                                <div class="row text-center m-t-20">
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">1099</h3><small>Articles</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">23,469</h3><small>Followers</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h3 class="m-b-0 font-light">6035</h3><small>Following</small></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-block bg-info">
                                <h4 class="text-white card-title">My Contacts</h4>
                                <h6 class="card-subtitle text-white m-b-0 op-5">Checkout my contacts here</h6>
                            </div>
                            <div class="card-block">
                                <div class="message-box contact-box">
                                    <h2 class="add-ct-btn"><button type="button" class="btn btn-circle btn-lg btn-success waves-effect waves-dark">+</button></h2>
                                    <div class="message-widget contact-widget">
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/7.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">info@wrappixel.com</span></div>
                                        </a>
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Sonu Nigam</h5> <span class="mail-desc">pamela1987@gmail.com</span></div>
                                        </a>
                                        <a href="#">
                                            <div class="user-img"> <span class="round">A</span> <span class="profile-status away pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>Arijit Sinh</h5> <span class="mail-desc">cruise1298.fiplip@gmail.com</span></div>
                                        </a>
                                        <a href="#">
                                            <div class="user-img"> <img src="assets/images/users/back-4.jpg" alt="user" class="img-circle"> <span class="profile-status offline pull-right"></span> </div>
                                            <div class="mail-contnet">
                                                <h5>David Sams</h5> <span class="mail-desc">root@davidsams.com</span></div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-xlg-9 col-md-7">
                        <div class="card">
                            <ul class="nav nav-tabs profile-tab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Events</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#avatar" role="tab">Avatar</a> </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="home" role="tabpanel">
                                    <div class="card-block">
                                        <div class="profiletimeline">
                                            <div class="sl-item">
                                                <div class="sl-left"> <img src="assets/images/users/7.jpg" alt="user" class="img-circle"> </div>
                                                <div class="sl-right">
                                                    <div><a href="#" class="link">Pavan kumar</a> <span class="sl-date">5 minutes ago</span>
                                                        <p>All the equipment is working normally</p>
                                                        <div class="row">
                                                            <div class="col-lg-3 col-md-6 m-b-20"><img src="assets/images/big/2.jpeg" alt="user" class="img-responsive radius"></div>
                                                            <div class="col-lg-3 col-md-6 m-b-20"><img src="assets/images/big/4.jpeg" alt="user" class="img-responsive radius"></div>
                                                            <div class="col-lg-3 col-md-6 m-b-20"><img src="assets/images/big/1.jpg" alt="user" class="img-responsive radius"></div>
                                                            <div class="col-lg-3 col-md-6 m-b-20"><img src="assets/images/big/3.jpg" alt="user" class="img-responsive radius"></div>
                                                        </div>
                                                        <div class="like-comm"> <a href="javascript:void(0)" class="link m-r-10">2 comment</a> <a href="javascript:void(0)" class="link m-r-10"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="sl-item">
                                                <div class="sl-left"> <img src="assets/images/users/2.jpg" alt="user" class="img-circle"> </div>
                                                <div class="sl-right">
                                                    <div> <a href="#" class="link">Sonu Nigam</a> <span class="sl-date">5 minutes ago</span>
                                                        <div class="m-t-20 row">
                                                            <div class="col-md-3 col-xs-12"><img src="assets/images/big/5.jpg" alt="user" class="img-responsive radius"></div>
                                                            <div class="col-md-9 col-xs-12">
                                                                <p> The industrial Internet was initiated by General Electric Company of the United States and promoted by at &amp; T, Cisco, general electric, IBM and Intel. </p> <a href="#" class="btn btn-success">Control System</a></div>
                                                        </div>
                                                        <div class="like-comm m-t-20"> <a href="javascript:void(0)" class="link m-r-10">2 comment</a> <a href="javascript:void(0)" class="link m-r-10"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="sl-item">
                                                <div class="sl-left"> <img src="assets/images/users/3.jpg" alt="user" class="img-circle"> </div>
                                                <div class="sl-right">
                                                    <div><a href="#" class="link">Arijit Sinh</a> <span class="sl-date">5 minutes ago</span>
                                                        <p class="m-t-10"> Digitalization, networking and intellectualization have become the important characteristics of the development of manufacturing industry and the main direction of the future development of manufacturing enterprises. Both the industrial Internet and industry 4.0 strategy propose to use information and intelligent technology to transform the current production and service mode, improve the production efficiency of enterprises and enhance the market competitiveness of products and services. </p>
                                                    </div>
                                                    <div class="like-comm m-t-20"> <a href="javascript:void(0)" class="link m-r-10">2 comment</a> <a href="javascript:void(0)" class="link m-r-10"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="sl-item">
                                                <div class="sl-left"> <img src="assets/images/users/back-4.jpg" alt="user" class="img-circle"> </div>
                                                <div class="sl-right">
                                                    <div><a href="#" class="link">David Sams</a> <span class="sl-date">5 minutes ago</span>
                                                        <blockquote class="m-t-10">
                                                            Industrial control system (ICS) is a general term that encompasses several types of control systems and associated instrumentation used for industrial process control.
                                                        </blockquote>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if (isset($_POST["old_password"])
                                    && isset($_POST["update_password"])
                                    && isset($_POST["update_age"])
                                    && isset($_POST["update_email"])
                                    && is_string($_POST["old_password"])
                                    && is_string($_POST["update_password"])
                                    && is_string($_POST["update_age"])
                                    && is_string($_POST["update_email"])
                                )
                                {
                                    if ( preg_match('/[^\d]/', $_POST["update_age"]) || !filter_var($_POST["update_email"], FILTER_VALIDATE_EMAIL) || strlen($_POST["update_password"]) > 16 || preg_match('/\W/', $_POST["update_password"]) )
                                        $user->alertMes("invalid information", "./dashboard.php");
                                    $update_profile = array (
                                        "old_password" => $_POST["old_password"],
                                        "old_real_password" => $user->password,
                                        "password" => $_POST["update_password"],
                                        "age" => $_POST["update_age"],
                                        "email" => $_POST["update_email"]
                                    );
                                    $user->update(serialize($update_profile));
                                }
                                ?>
                                <!--second tab-->
                                <div class="tab-pane" id="profile" role="tabpanel">
                                    <div class="card-block">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong>
                                                <br>
                                                <p class="text-muted"><?=$user->username;?></p>
                                            </div>
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>password</strong>
                                                <br>
                                                <p class="text-muted"><?=str_repeat("*", strlen($user->password)); ?></p>
                                            </div>
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Email</strong>
                                                <br>
                                                <p class="text-muted"><?=$user->email;?></p>
                                            </div>
                                            <div class="col-md-3 col-xs-6"> <strong>Age</strong>
                                                <br>
                                                <p class="text-muted"><?=$user->age;?></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="m-t-30">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries </p>
                                        <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                                        <h4 class="font-medium m-t-30">Mineral Resources</h4>
                                        <hr>
                                        <h5 class="m-t-30">Gold <span class="pull-right">80%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">Steel <span class="pull-right">90%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">Silver <span class="pull-right">50%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">Copper <span class="pull-right">70%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="settings" role="tabpanel">
                                    <div class="card-block">
                                        <form class="form-horizontal form-material" action="dashboard.php" method="post">
                                            <div class="form-group">
                                                <label class="col-md-12">username</label>
                                                <div class="col-md-12">
                                                    <input name="update_username" type="text" placeholder="<?=$user->username; ?>" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12">Old Password</label>
                                                <div class="col-md-12">
                                                    <input name= "old_password" type="password" value="password" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12">New Password</label>
                                                <div class="col-md-12">
                                                    <input name= "update_password" type="password" value="password" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">Email</label>
                                                <div class="col-md-12">
                                                    <input name="update_email" type="email" placeholder="<?=$user->email; ?>" class="form-control form-control-line" id="example-email">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-12">Age</label>
                                                <div class="col-md-12">
                                                    <input name="update_age" type="text" placeholder="<?=$user->age; ?>" class="form-control form-control-line">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success">Upgrade Profile</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="tab-pane" id="avatar" role="tabpanel">
                                    <div class="card-block">
                                        You can update your Avatar here<!-- 头像 -->
                                    </div>
                                    <div class="form-group">
                                        <form class="form-horizontal form-material" action="dashboard.php" method="post" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <input type="file" name="file" class="btn btn-flickr">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <button class="btn btn-success" type="submit" value="submit" name="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <footer class="footer">Copyright &copy; 2019-2020. <a target="_blank" href="#">ICS Dashboard</a> All rights reserved.</footer>
        </div>
    </div>
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/tether.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/waves.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="assets/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="assets/plugins/chartist-js/dist/chartist.min.js"></script>
    <script src="assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="assets/plugins/d3/d3.min.js"></script>
    <script src="assets/plugins/c3-master/c3.min.js"></script>
    <script src="js/dashboard1.js"></script>
</body>
</html>
<?php

if (isset($_FILES["file"])) {
    $allowed_extension = "png";
    $temp = explode(".", $_FILES["file"]["name"]);
    $extension = end($temp);
    if ( (($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png"))
        && ($_FILES["file"]["size"] < 204800)
        && $extension === $allowed_extension
    )
    {
        if ($_FILES["file"]["error"] > 0) {
            $user->alertMes("错误：: " . $_FILES["file"]["error"], "./dashboard.php");
        } else if (file_exists("upload/" . MD5($_FILES["file"]["name"]) . ".png")){
            $user->alertMes("file already exists, please change your filename", "./dashboard.php");
        }  else if (preg_match("/php|HALT\_COMPILER/i", file_get_contents($_FILES["file"]["tmp_name"]) )){
            $user->alertMes("dangerous file content", "./dashboard.php");
        }else {
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . MD5($_FILES["file"]["name"]) . ".png");
            $user->set_avatar("upload/" . MD5($_FILES["file"]["name"]) . ".png");
            echo "upload/" . MD5($_FILES["file"]["name"]) . ".png";
        }
    } else {
        $user->alertMes("dangerous", "./dashboard.php");
    }
}

$user_avatar = $user->get_avatar();
if ( is_string($user_avatar) && !empty($user_avatar)) {
    $content = file_get_contents(__DIR__ . "/" . $user_avatar);
    $png_header = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A\x00\x00\x00\x0D\x49\x48\x44\x52";
    if (strpos($content, $png_header) === false )
    {
        throw new Exception("png content got an unexpected Exception");
    }
}
?>