<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_POST['action'];
if($_POST['submit']){
	if($action == 'del'){
		$array_inquiry_orderid = $_POST['id'];
		$inquiry_orderid = fun_convert_checkbox($array_inquiry_orderid);
		//删除询价单
		$sql_orderlist = "DELETE FROM `db_material_inquiry_orderlist` WHERE `inquiry_orderid` IN ($inquiry_orderid)";
		$db->query($sql_orderlist);
		$sql = "DELETE FROM `db_material_inquiry_order` WHERE `inquiry_orderid` IN ($inquiry_orderid)";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == 'del_list'){
		$array_listid = $_POST['id'];
		$listid = fun_convert_checkbox($array_listid);
		$sql = "DELETE FROM `db_material_inquiry_orderlist` WHERE `listid` IN ($listid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>