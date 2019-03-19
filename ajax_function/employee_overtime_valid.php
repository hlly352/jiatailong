<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$applyer = $_POST['applyer'];
//假期
$sql_vacation = "SELECT `vacationid`,`vacation_name` FROM `db_personnel_vacation` WHERE `vacation_status` = 1 ORDER BY `vacationid` ASC";
$result_vacation = $db->query($sql_vacation);
//员工的加班时间
$sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$applyer' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0";
$result_overtime = $db->query($sql_overtime);
if($result_overtime->num_rows){
	while($row_overtime = $result_overtime->fetch_assoc()){
		$overtime_valid += $row_overtime['overtime_valid'];
	}
}else{
	$overtime_valid = 0;
}
echo "<option value=\"\">请选择</option>";
if($result_vacation->num_rows){
	while($row_vacation = $result_vacation->fetch_assoc()){
?>
<option value="<?php echo $row_vacation['vacationid']; ?>"<?php if($row_vacation['vacationid'] == 2 && $overtime_valid == 0)echo " disabled=\"disabled\""; ?>><?php echo $row_vacation['vacation_name']; ?>
<?php if($row_vacation['vacationid'] == 2) echo "(".$overtime_valid."小时)"; ?>
</option>
<?php
	}
}
?>
