<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_SERVER['HTTP_REFERER']){
	$typeid = $_POST['typeid'];
	$sql = "SELECT `specificationid`,`specification` FROM `db_cutter_specification` WHERE `typeid` = '$typeid' ORDER BY `specification` ASC,`specificationid` DESC";
	$result = $db->query($sql);
	echo "<option value=\"\">请选择</option>";
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			echo "<option value=\"".$row['specificationid']."\">".$row['specification']."</option>";
		}
	}
}
?>