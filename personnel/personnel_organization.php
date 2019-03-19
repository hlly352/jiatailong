<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$sql = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkcode` = 'PEOR'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$filedir = $array['filedir'];
	$filename = $array['filename'];
	$file_path = '../upload/file/'.$filedir.'/'.$filename;
	if(is_file($file_path)){
?>
<div style="margin:0 auto; text-align:center;"><img src="<?php echo $file_path; ?>" /></div>
<?php
	}
}
?>
<?php include "../footer.php"; ?>
</body>
</html>