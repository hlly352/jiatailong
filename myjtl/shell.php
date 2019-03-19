<?php
if($_SESSION['login_status'] == false){
	header("location:/passport/login.php");
}
?>