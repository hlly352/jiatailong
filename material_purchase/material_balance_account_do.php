<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//获取对账成功的记录id
if($_POST['submit']){
	$id = $_POST['id'];
	$inoutid = fun_convert_checkbox($id);
} else{
	$inoutid = $_GET['id'];
}
//更改入库记录中的对账状态
$sql = "UPDATE `db_material_inout` SET `account_status` = 'F' WHERE `inoutid` IN($inoutid)";
$db->query($sql);
if($db->affected_rows){
	header("location:".$_SERVER['HTTP_REFERER']);
}


?>