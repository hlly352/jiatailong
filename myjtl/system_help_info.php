<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$helpid = fun_check_int($_GET['id']);
$sql = "SELECT `db_system_help`.`help_title`,`db_system_help`.`help_content`,DATE_FORMAT(`db_system_help`.`dotime`,'%Y-%m-%d') AS `dodate`,`db_system`.`system_name` FROM `db_system_help` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_help`.`systemid` WHERE `db_system_help`.`help_status` = 1 AND `db_system_help`.`helpid` = '$helpid'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
#help_info {
	width:980px;
	margin:10px auto;
}
#help_info dl dt {
	font-size:24px;
	text-align:center;
	padding:5px 0;
}
#help_info dl dd.tag {
	margin:10px 0;
	padding:4px 0 4px 18px;
	border-bottom:1px solid #999;
	font-size:13px;
	color:#666;
	background:url(../images/system_ico/article_12_16.png) no-repeat  left center;
}
#help_info dl dd.content {
	font-size:15px;
	color:#333;
	margin-top:10px;
	line-height:24px;
}
</style>
<title>系统帮助-苏州希尔林</title>
</head>

<body>
<?php
if($result->num_rows){
	$row = $result->fetch_assoc();
?>
<div id="help_info">
  <dl>
    <dt><?php echo $row ['help_title']; ?></dt>
    <dd class="tag"> >> <?php echo $row['system_name'].'--'.$row['dodate']; ?></dd>
    <dd class="content"><?php echo $row['help_content']; ?></dd>
  </dl>
</div>
<?php } ?>
</body>
</html>