<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
session_start(); 
$_SESSION = array(); //unset($_session['xxx'])
session_destroy();
header("location:login.php");
?>