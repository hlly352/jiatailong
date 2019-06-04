<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$x_arr = $_POST;
//获取所有计划和实际金额
$sql = "SELECT * FROM `db_order_pay`";
$result = $db->query($sql);
if($db->num_rows){
	while()
}
?>