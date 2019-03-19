<?php
if(!$_SESSION['login_status']){
	header("location:../passport/login.php");
}else{
	$url = $_SERVER['REQUEST_URI'];
    $array_url = parse_url($url);
	$path = $array_url['path'];
	$array_dir = explode("/",$path);
	$system_dir = "/" . $array_dir[1] . "/";

}
?>