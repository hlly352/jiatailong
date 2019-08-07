<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
	$action = $_POST['action'];
	$data = $_POST;
	$employeeid = $data['applyer'];
	unset($data['action']);
	if($action == 'add'){
	unset($data['submit']);
		//拼接添加数据库的sql语句
		$key_sql = '';
		$val_sql = '';
		foreach($data as $k=>$v){
			$key_sql .= '`'.$k.'`,';
			$val_sql .= '"'.$v.'",';
		}
		
		//查询审批人
		$sql_employee = "SELECT `position_type` FROM `db_employee` WHERE `employeeid` = '$employeeid'";

		$result_employee = $db->query($sql_employee);
		
		if($result_employee->num_rows){
			$array_employee = $result_employee->fetch_assoc();
			$position_type = $array_employee['position_type'];
			
			if($position_type != 'A'){
				if($position_type == 'D'){
					$sql_super ="SELECT `db_superior`.`position_type`,`db_employee`.`superior` FROM `db_employee` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` =".$employeeid;
					$result_super = $db->query($sql_super);
					if($result_super->num_rows){
						$array_superior = $result_super->fetch_assoc();
						$position_types = $array_superior['position_type'];
						$employeeid = $array_superior['superior'];
						
							}
						if($position_types == 'D'){

							$sql ="SELECT `db_superior`.`position_type`,`db_employee`.`superior` FROM `db_employee` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` =".$employeeid;
							$result = $db->query($sql);
							if($result->num_rows){
									$array_superior = $result->fetch_assoc();
									$position_type = $array_superior['position_type'];
									$employeeid = $array_superior['superior'];
									
								}
							}
				} else{
					$sql_superior ="SELECT `db_superior`.`position_type`,`db_employee`.`superior` FROM `db_employee` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` =".$employeeid;
					$result_superior = $db->query($sql_superior);
					if($result_superior->num_rows){
						$array_superior = $result_superior->fetch_assoc();
						$position_type = $array_superior['position_type'];

					}
					$employeeid = $array_superior['superior'];
				}
			}
			$approver = $array_superior['superior']?$array_superior['superior']:$employeeid;
		}
		//添加时间戳和审批人
		$key_sql .='`add_time`,`approver`,`status`';
		$val_sql .='"'.time().'","'.$approver.'","A"';
		$add_sql = "INSERT INTO `db_mould_other_material`($key_sql) VALUES($val_sql)";

		$db->query($add_sql);
		if($db->affected_rows){
			header('location:mould_other_fee.php');
		}
	} elseif($action == 'edit') {
		$id = $data['mould_other_id'];
		unset($data['mould_other_id']);
		//判断是否通过审评
		
		if($data['submit'] == '退回'){
			$no_approval_sql = "UPDATE `db_mould_other_material` SET `status`='C',`do_time`=".time()." WHERE `mould_other_id`=".$id;
			
			$db->query($no_approval_sql);
			if($db->affected_rows){
				header('location:mould_other_fee.php');
			}
		}elseif($data['submit'] == '通过'){
			unset($data['submit']);
			foreach($data as $k=>$v){
				$sql_value .= '`'.$k.'`="'.$v.'",';
			}
			$sql_value .= '`status`=\'B\',`do_time`='.time();
			$approval_sql = "UPDATE `db_mould_other_material` SET {$sql_value} WHERE `mould_other_id`=".$id;
	
			$db->query($approval_sql);
			if($db->affected_rows){
				header('location:mould_other_fee.php');
			}

		}
	}elseif($action == 'del'){
		//接收数据
		$id = $_POST['id'];
		$other_material_id = fun_convert_checkbox($id);
		$other_material_sql = "DELETE FROM `db_mould_other_material` WHERE `mould_other_id` IN($other_material_id)";
		
		$db->query($other_material_sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}

?>