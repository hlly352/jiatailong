<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	if($_POST['submit']){
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$data = $_POST;
	$action  = $data['action'];
	$specificaiton_id = $data['specification_id'];
	$title = $data['title'];
	$designid = $_POST['designid'];
	unset($data['action']);
	unset($data['submit']);
	unset($data['title']);
	unset($data['designid']);
	//限制上传文件的大小
	if($_FILES['file']['size'] > 106132773){
		echo '文件超大，请重新上传<a href="design_plan_edit.php?action=add&specification_id='.$specification_id.'">返回</a>';
		return false;
	}
	//上传文件
	if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/technical_other/".$filedir."/";
			$upload = new upload();
		    $upload->upload_file($upload_path);
		    $array_upload_file = $upload ->array_upload_file;
		    $file_path = $upload_path.$array_upload_file['upload_final_name'];
		    $file_name = $array_upload_file['upload_name'];
		}
		$date = date('Y-m-d');
		$data_info = $title.'#'.$file_name.'#'.$date.
		$sql_str = '';
	if($action == 'add'){	
		foreach($data as $word=>$value){
			$sql_word .= '`'.$word.'`,';
			$sql_value .= '"'.$value.'",';
		}
		$sql_word .= '`employeeid`,`design_plan_info`,`design_plan_path`,`time`';
		$sql_value .= '"'.$employeeid.'","'.$data_info.'","'.$file_path.'","'.time().'"';
		//把设计计划信息填入表中
		$sql_design_plan = "INSERT INTO `db_design_plan`($sql_word) VALUES($sql_value)";
		$result_design_plan = $db->query($sql_design_plan);
	}elseif($action == 'edit'){
		foreach($data as $word=>$value){
			$sql_str .= '`'.$word.'`="'.$value.'",';
		}
		//判断是否有文件上传
		if(empty($file_path)){
			$sql_str = rtrim($sql_str,',');
		}else{
			$sql_str .= "`design_plan_path` = CONCAT_WS('&',`design_plan_path`,'$file_path'),`design_plan_info` = CONCAT_WS('&',`design_plan_info`,'$data_info')";
		}
		$sql = "UPDATE `db_design_plan` SET $sql_str WHERE `designid` = '$designid'";
		$db->query($sql);

	}

	/*-----发送邮件----*/
		//查询操作人的邮箱地址
		$sql_do_mail = "SELECT `email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$result_do_mail = $db->query($sql_do_mail);
		$send = '';
		if($result_do_mail->num_rows){
			$send = $result_do_mail->fetch_assoc()['email'];
		}
		//查询需要发送邮件的人员
		$sql_employee = "SELECT `db_mould_specification`.`mould_no`,`db_mould_specification`.`projecter`,`db_mould_specification`.`designer`,`db_design_plan`.`drawer_2d`,`db_design_plan`.`design_group` FROM `db_design_plan` INNER JOIN `db_mould_specification` ON `db_design_plan`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_design_plan`.`designid` = '$designid'";
		$result_employee = $db->query($sql_employee);
		$array_employee = array();
		if($result_employee->num_rows){
			$array_employee = $result_employee->fetch_row();
		}
		$mould_no = $array_employee[0];
		unset($array_employee[0]);
		$employeeids = fun_convert_checkbox($array_employee);
		//查询邮箱地址
		$sql_email = "SELECT `email` FROM `db_employee` WHERE `employeeid` IN($employeeids)";
		$result_email = $db->query($sql_email);
		if($result_email->num_rows){
			$address = array();
			while($row_email = $result_email->fetch_row()){
				$address[] = $row_email[0];
			}
		}
		$subject = $mould_no.'的设计计划更新';
		$body = $mould_no.'的设计计划发生更新，请查看';
		//发送邮件
		send($send,$address,$subject,$body);
		header('location:design_plan.php');
	}else{
		$designid = $_GET['designid'];
		$key = $_GET['key'];
	}
	
?>