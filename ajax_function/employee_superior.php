<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$array_employeeid = $_POST['employeeid'];
$employeeid = fun_convert_checkbox($array_employeeid);
$employee_superior = $_POST['employee_superior'];
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employee_status` = 1 AND `db_employee`.`employeeid` != '$employee_superior' AND `db_employee`.`employeeid` NOT IN ($employeeid) ORDER BY `db_department`.`dept_order` ASC,`db_department`.`deptid` ASC,CONVERT(`db_employee`.`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
$result = $db->query($sql);
echo "<option value=\"\">请选择</option>";
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		echo "<option value=\"".$row['employeeid']."\">".$row['dept_name'].'-'.$row['employee_name']."</option>";
	}
}
?>