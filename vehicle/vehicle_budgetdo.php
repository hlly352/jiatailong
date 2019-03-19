<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$budget_month = $_POST['budget_month'].'-01';
	$budget_cost = $_POST['budget_cost'];
	$sql_budget = "SELECT `budgetid` FROM `db_vehicle_budget` WHERE `budget_month` = '$budget_month'";
	$result_budget = $db->query($sql_budget);
	if($result_budget->num_rows){
		$array_budget = $result_budget->fetch_assoc();
		$budgetid = $array_budget['budgetid'];
		$sql_update = "UPDATE `db_vehicle_budget` SET `budget_cost` = '$budget_cost' WHERE `budgetid` = '$budgetid'";
		$db->query($sql_update);
		if($db->affected_rows){
			header("location:vehicle_budget.php");
		}
	}else{
		$sql_add = "INSERT INTO `db_vehicle_budget` (`budgetid`,`budget_month`,`budget_cost`) VALUES (NULL,'$budget_month','$budget_cost')";
		$db->query($sql_add);
		if($db->insert_id){
			header("location:vehicle_budget.php");
		}
	}
}
?>