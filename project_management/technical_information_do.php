<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	//删除原来的文件
	function del($db,$from,$informationid){
		$sql_url = "SELECT {$from} FROM `db_technical_information` WHERE `information_id` = '$informationid'"; 
		$result_url = $db->query($sql_url);
		if($result_url->num_rows){
			$url = $result_url->fetch_row()[0];
			if(file_exists($url)){
				@unlink($url);
			}
		}
	}

	//获取上传的文件类型
	$file_type = $_POST['file_type'];
	$mouldid = $_POST['mouldid'];
	$specification_id = $_POST['specification_id'];
	//查找是否有信息
	$is_exists_sql = "SELECT * FROM `db_technical_information` WHERE `specification_id` = '$specification_id'";
	$result_exists = $db->query($is_exists_sql);
	$is_exists = $result_exists->num_rows;
	if($is_exists == 0){
		$sql = "INSERT INTO `db_technical_information`(`mouldid`,`specification_id`) VALUES('$mouldid','$specification_id')";
		$db->query($sql);
		if($db->affected_rows){
			$informationid = $db->insert_id;
		}
	}else{
		$informationid = $result_exists->fetch_assoc()['information_id'];
	}
	//上传文件
	if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/technical_information/".$filedir."/";
			$upload = new upload();
		    $upload->upload_file($upload_path);
		    $array_upload_file = $upload ->array_upload_file;
		    $file_path = $upload_path.$array_upload_file['upload_final_name'];
		}
	
	//把地址填入到对应的表中
	switch($file_type)
		{
			case 'project_data':
				//查找当前项目的所有模具
				$project_sql = "SELECT `db_mould_specification`.`mould_specification_id`,`mould_id` FROM `db_mould_specification` WHERE `project_name` = (SELECT `db_mould_specification`.`project_name` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_technical_information`.`information_id` = '$informationid')";
				echo $informationid;
				$result_project = $db->query($project_sql);
				if($result_project->num_rows){
					$array_specification_id = array();
					while($row = $result_project->fetch_assoc()){
						$array_specification_id[] = $row['mould_specification_id'];
						//新建技术资料
						if($specification_id != $row['mould_specification_id']){
							$information_sql = "INSERT INTO `db_technical_information`(`specification_id`,`mouldid`) VALUES('".$row['mould_specification_id']."','".$row['mould_id']."')";
							$db->query($information_sql);
						}
					}
				}
				$specification_id = fun_convert_checkbox($array_specification_id);
				del($db,'`project_data`',$informationid);
				$sql = "UPDATE `db_technical_information` SET `project_data` = '$file_path' WHERE  FIND_IN_SET(specification_id,'$specification_id')";
			break;
			case 'mould_data':
				$sql = "UPDATE `db_technical_information` SET `mould_data` = '$file_path' WHERE `information_id` = '$informationid'";
			break;
			case 'flow':
				$sql = "UPDATE `db_technical_information` SET `flow` = '$file_path' WHERE `information_id` = '$informationid'";
			break;
			case 'report':
				$sql = "UPDATE `db_technical_information` SET `report` = '$file_path' WHERE `information_id` = '$informationid'";
			break;
			case 'standard':
				$sql = "UPDATE `db_technical_information` SET `standard` = '$file_path' WHERE `information_id` = '$informationid'";
			break;
			case 'drawing':
				$sql = "UPDATE `db_technical_information` SET `drawing` = '$file_path' WHERE `information_id` = '$informationid'";
			break;
		}
		
	$db->query($sql);
	if($db->affected_rows){
		header('location:technical_information.php');
	}
?>