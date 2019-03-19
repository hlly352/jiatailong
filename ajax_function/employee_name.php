<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$employee_name = trim($_POST['employee_name']);
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employee_name` LIKE '%$employee_name%' AND `db_employee`.`employee_status` = 1 AND `db_employee`.`account_status` = 1 ORDER BY `db_department`.`dept_order` ASC,`db_employee`.`employee_name` ASC";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		echo "<option value=\"".$row['employeeid']."\">".$row['dept_name'].'-'.$row['employee_name']."</option>";
	}
}else{
	echo "<option value=\"\">暂无</option>";
}
?>