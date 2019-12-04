<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/uploads.php';
	require_once 'shell.php';
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$specification_id = trim($_POST['specification_id']);
	if($_POST['submit']){
	$data = $_POST;
	$array_dataid = $data['dataid'];
	foreach($array_dataid as $k=>$v){
		if($v){
			$sql_exists = "SELECT * FROM `db_project_review_list` WHERE `specification_id` = '$specification_id' AND `dataid` = '$v'";
			$result_exists = $db->query($sql_exists);
			//判断评审详情表是否存在项目
			$approval = $data['approval_'.$v];
			$remark   = htmlspecialchars(trim($data['remark_'.$v]));
			//是否有当前评审记录
			if($result_exists->num_rows){
					$sql_list = "UPDATE `db_project_review_list` SET `approval` = '$approval',`remark` = '$remark' WHERE `specification_id` = '$specification_id' AND `dataid` = '$v'";
			}else{
				$sql_list = "INSERT INTO `db_project_review_list`(`specification_id`,`dataid`,`approval`,`remark`) VALUES('$specification_id','$v','$approval','$remark')";
			};
			//echo $sql_list.'<br>';
			$db->query($sql_list);
		}	
	}
	header('location:project_start.php');
	exit;
		//查找发件人
		$sql_send = "SELECT `email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$send = '';
		$result_send = $db->query($sql_send);
		if($result_send->num_rows){
			$send = $result_send->fetch_assoc()['email'];
		}
		$array_dept = $_POST['data_dept'];
		$depts = fun_convert_checkbox($array_dept);
		//查询收件人信息
		$sql_email = "SELECT distinct(`db_employee`.`email`) AS `mail` FROM `db_employee` INNER JOIN `db_department` ON `db_employee`.`deptid` = `db_department`.`deptid` WHERE `db_department`.`deptid` IN($depts)";
		$result_email = $db->query($sql_email);
		$address = array();
		if($result_email->num_rows){
			while($row_email = $result_email->fetch_assoc()){
				$address[] = $row_email['mail'];
			}
		}
		$address[] = 'hr.04@hl.com';
		$subject = $mould_no.'的模具';
		$body = '点击链接查看：<a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/project_design/design_review_edit.php?action=edit&specification_id='.$specification_id.'&changeid='.$changeid.'">http://localhost/project_design/mould_change_edit.php?action=edit&specification_id=24&changeid=1</a>';
		send('hr.04@hl.com',$address,$subject,$body);
	}
?>