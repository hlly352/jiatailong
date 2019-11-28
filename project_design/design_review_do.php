<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/uploads.php';
	require_once 'shell.php';
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$reviewid = $_POST['reviewid'];
	$array_pic_remark = $_POST['pic_remark'];
	if($_POST['submit']){
	$data = $_POST;
	$array_dataid = $data['dataid'];
	$images = $_FILES;
	foreach($array_dataid as $k=>$v){
		if($v){
			//判断是否有图片上传
			if($_FILES['image_'.$v]['name'][0]){
				$filedir = date("Ymd");
				$upload_path = "../upload/technical_other/".$filedir."/";
				$upload = new upload($images['image_'.$v]['name'],$images['image_'.$v]['tmp_name'],$images['image_'.$v]['size'],$images['image_'.$v]['error']);
				$upload->upload_files($upload_path);
				$array_upload_files = $upload ->array_upload_files;
				if($array_upload_files){
					foreach($array_upload_files as $ks=>$vs){
						$data['image_path_'.$v] .= $upload_path.$vs['upload_final_name'].'**'.$array_pic_remark[$ks].'&&';
					}
				}
				//$file_path = $upload_path.$array_upload_file['upload_final_name'];
				//$file_name = $array_upload_file['upload_name'];
				//$data['image_path_'.$v] = $file_path;
			}
			$data['image_path_'.$v] = rtrim($data['image_path_'.$v],'&&');
			$sql_exists = "SELECT * FROM `db_design_review_list` WHERE `reviewid` = '$reviewid' AND `dataid` = '$v'";
			$result_exists = $db->query($sql_exists);
			//判断评审详情表是否存在项目
			$approval = $data['approval_'.$v];
			$remark   = $data['remark_'.$v];
			$image_path = $data['image_path_'.$v];
			//是否有当前评审记录
			if($result_exists->num_rows){
				if($_FILES['image_'.$v]['name'][0]){
					$sql_list = "UPDATE `db_design_review_list` SET `approval` = '$approval',`remark` = '$remark',`image_path` = '$image_path' WHERE `reviewid` = '$reviewid' AND `dataid` = '$v'";
				}else{
					$sql_list = "UPDATE `db_design_review_list` SET `approval` = '$approval',`remark` = '$remark' WHERE `reviewid` = '$reviewid' AND `dataid` = '$v'";
				}
			}else{
				$sql_list = "INSERT INTO `db_design_review_list`(`reviewid`,`dataid`,`approval`,`remark`,`image_path`) VALUES('$reviewid','$v','$approval','$remark','$image_path')";
			}
			//echo $sql_list.'<br>';
			$db->query($sql_list);
		}	
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
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