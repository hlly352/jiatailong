<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$deptid = $_POST['deptid'];
	$work_title = trim($_POST['work_title']);
	$work_content = htmlcode($_POST['work_content']);
	$work_type = $_POST['work_type'];
	$work_week = fun_convert_checkbox($_POST['work_week']);
	$work_month = $_POST['work_month'];
	$work_date = $_POST['work_date'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_routine_work` (`workid`,`deptid`,`work_title`,`work_content`,`work_type`,`work_week`,`work_month`,`work_date`,`work_status`,`employeeid`,`dotime`) VALUES (NULL,'$deptid','$work_title','$work_content','$work_type','$work_week','$work_month','$work_date',1,'$employeeid','$dotime')";
		$db->query($sql);
		if($workid = $db->insert_id){
			header("location:routine_work_list.php");
		}
	}elseif($action == "edit"){
		$workid = $_POST['workid'];
		$work_status = $_POST['work_status'];
		$sql = "UPDATE `db_routine_work` SET `deptid` = '$deptid',`work_title` = '$work_title',`work_content` = '$work_content',`work_type` = '$work_type',`work_week` = '$work_week',`work_month` = '$work_month',`work_date` = '$work_date',`work_status` = '$work_status' WHERE `workid` = '$workid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_workid = fun_convert_checkbox($_POST['id']);
		//删除更新文件
		$sql_update_file_list = "SELECT `db_upload_file`.`filedir`,`db_upload_file`.`filename` FROM `db_upload_file` INNER JOIN `db_routine_work_update` ON `db_routine_work_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_routine_work_update`.`workid` IN ($array_workid) AND `db_upload_file`.`linkcode` = 'RWUP'";
		$result_update_file_list = $db->query($sql_update_file_list);
		if($result_update_file_list->num_rows){
			while($row_update_file_list = $result_update_file_list->fetch_assoc()){
				$update_filepath = "../upload/file/".$row_update_file_list['filedir'].'/'.$row_update_file_list['filename'];
				fun_delfile($update_filepath);
			}
		}
		//删除工作文件
		$sql_work_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($array_workid) AND `linkcode` = 'RW'";
		$result_work_file_list = $db->query($sql_work_file_list);
		if($result_work_file_list->num_rows){
			while($row_work_file_list = $result_work_file_list->fetch_assoc()){
				$work_filepath = "../upload/file/".$row_work_file_list['filedir'].'/'.$row_work_file_list['filename'];
				fun_delfile($work_filepath);
			}
		}
		//删除更新文件
		$sql_update_file = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_routine_work_update` ON `db_routine_work_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_routine_work_update`.`workid` IN ($array_workid) AND `db_upload_file`.`linkcode` = 'RWUP'";
		$db->query($sql_update_file);
		//删除工作文件
		$sql_work_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($array_workid) AND `linkcode` = 'RW'";
		$db->query($sql_work_file);
		//删除更新
		$sql_update = "DELETE FROM `db_routine_work_update` WHERE `workid` IN ($array_workid)";
		$db->query($sql_update);
		//删除工作
		$sql_work = "DELETE FROM `db_routine_work` WHERE `workid` IN ($array_workid)";
		$db->query($sql_work);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($workid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/" . $filedir . "/";
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','RW','$workid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>