<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$sql_level = "SELECT `employeeid`,`employee_name`,`superior` FROM `db_employee` WHERE `employee_status` = 1 ORDER BY `employeeid`";
$result_level = $db->query($sql_level);
if($result_level->num_rows){
	while($row_level = $result_level->fetch_assoc()){
	$array_level[] = array('employeeid'=>$row_level['employeeid'],'superior'=>$row_level['superior'],'employee_name'=>$row_level['employee_name']);
	}
}else{
	$array_level = array();
}
function merge($array,$superior=0){
    foreach($array as $k=>$v){
        if($v['superior']==$superior){	
			echo "<ul><li>".$v['employee_name'];
            merge($array,$v['employeeid']);
			echo "</li></ul>";
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
body, html {
	height:100%;
}
* {
	font-family:"微软雅黑", "宋体";
	font-size:13px;
}
#employee_level ul {
	list-style-image:url(../images/system_ico/li_tag_8_8.png);
}
</style>
<script language="javascript" src="../js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script language="javascript" src="js/jquery.cookie.js" type="text/javascript"></script>
<script language="javascript" src="js/main.js" type="text/javascript"></script>
<title>人事系统-希尔林</title>
</head>

<body>
<div id="employee_level">
  <?php merge($array_level); ?>
</div>
</body>
</html>