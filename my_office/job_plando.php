<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$plan_content = htmlcode($_POST['plan_content']);
	$plan_type = $_POST['plan_type'];
	$start_date = $_POST['start_date'];
	$finish_date = $_POST['finish_date'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_job_plan` (`planid`,`plan_content`,`start_date`,`finish_date`,`plan_status`,`plan_type`,`plan_result`,`employeeid`,`dotime`) VALUES (NULL,'$plan_content','$start_date','$finish_date',1,'$plan_type',0,'$employeeid','$dotime')";
		$db->query($sql);
		if($planid = $db->insert_id){
			header("location:job_plan.php?type=".$plan_type);
		}
	}elseif($action == "edit"){
		$planid = $_POST['planid'];
		$plan_result = $_POST['plan_result'];
		$plan_status = $_POST['plan_status'];
		$sql = "UPDATE `db_job_plan` SET `plan_content` = '$plan_content',`start_date` = '$start_date',`finish_date` = '$finish_date',`plan_status` = '$plan_status',`plan_type` = '$plan_type',`plan_result` = '$plan_result' WHERE `planid` = '$planid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		
	}
	if($planid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/" . $filedir . "/";
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','JP','$planid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>