<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$array_planid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "SELECT * FROM `db_job_plan` WHERE `planid` = '$array_planid' AND `employeeid` = '$employeeid'";
$result = $db->query($sql);
if($result->num_rows){
	//删除更新文件
	$sql_update_file_list = "SELECT `db_upload_file`.`filedir`,`db_upload_file`.`filename` FROM `db_upload_file` INNER JOIN `db_job_plan_update` ON `db_job_plan_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_job_plan_update`.`planid` IN ($array_planid) AND `db_upload_file`.`linkcode` = 'JPUP'";
	$result_update_file_list = $db->query($sql_update_file_list);
	if($result_update_file_list->num_rows){
		while($row_update_file_list = $result_update_file_list->fetch_assoc()){
			$update_filepath = "../upload/file/".$row_update_file_list['filedir'].'/'.$row_update_file_list['filename'];
			fun_delfile($update_filepath);
		}
	}
	//删除工作文件
	$sql_plan_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($array_planid) AND `linkcode` = 'JP'";
	$result_plan_file_list = $db->query($sql_plan_file_list);
	if($result_plan_file_list->num_rows){
		while($row_plan_file_list = $result_plan_file_list->fetch_assoc()){
			$plan_filepath = "../upload/file/".$row_plan_file_list['filedir'].'/'.$row_plan_file_list['filename'];
			fun_delfile($plan_filepath);
		}
	}
	//删除更新文件
	$sql_update_file = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_job_plan_update` ON `db_job_plan_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_job_plan_update`.`planid` IN ($array_planid) AND `db_upload_file`.`linkcode` = 'JPUP'";
	$db->query($sql_update_file);
	//删除更新
	$sql_update = "DELETE FROM `db_job_plan_update` WHERE `planid` IN ($array_planid)";
	$db->query($sql_update);
	//删除工作文件
	$sql_plan_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($array_planid) AND `linkcode` = 'JP'";
	$db->query($sql_plan_file);
	//删除采集
	$sql_plan_list = "DELETE FROM `db_job_plan_list` WHERE `planid` IN ($array_planid)";
	$db->query($sql_plan_list);
	//删除工作
	$sql_plan = "DELETE FROM `db_job_plan` WHERE `planid` IN ($array_planid)";
	$db->query($sql_plan);
	if($db->affected_rows){
		header("location:job_plan.php?type=A");
	}
}
?>