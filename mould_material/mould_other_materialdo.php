<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
	$action = $_POST['action'];
	$data = $_POST;
	if($action == 'add'){
		$material_name = $data['material_name'];
		unset($data['material_name']);
		unset($data['submit']);
		unset($data['action']);
		$applyerid = $data['applyer'];
		$approver = approver($db,$applyerid);
		//拼接添加数据库的sql语句
		$key_sql = '';
		$val_sql = '';
		foreach($data as $k=>$v){
			$key_sql .= '`'.$k.'`,';
			$val_sql .= '"'.$v.'",';
		}
		//添加时间戳和审批人
		$key_sql .='`add_time`,`approver`,`status`';
		$val_sql .='"'.time().'","'.$approver.'","A"';
		$add_sql = "INSERT INTO `db_mould_other_material`($key_sql) VALUES($val_sql)";
		$db->query($add_sql);
		$mould_other_id = $db->insert_id;
		//期间物料规格表中添加一项
		$sql_specification = "INSERT INTO `db_other_material_specification`(`materialid`,`material_name`,`type`,`last_date`) VALUES('$mould_other_id','$material_name','B',DATE_FORMAT(NOW(),'%Y-%m-%d'))";
	
		$db->query($sql_specification);
		$specificationid = $db->insert_id;
		//规格id插入到期间物料表中
		$sql_mould_other = "UPDATE `db_mould_other_material` SET `material_name` = '$specificationid' WHERE `mould_other_id` = '$mould_other_id'";
		
		$db->query($sql_mould_other);
		if($db->affected_rows){
			header('location:mould_other_fee.php');
		}
	}elseif($action == 'material_control'){
		$array_specificationid = $data['specificationid'];
		$array_applyer = $data['applyer'];
		$array_quantity = $data['quantity'];
		$array_apply_team = $data['apply_team'];
		$array_apply_date = $data['apply_date'];
		$array_requirement_date = $data['requirement_date'];
		$array_remark = $data['remark'];

		//添加数据
		$sql_str = '';
		foreach($array_quantity as $k=>$v){
			if(!empty($v)){
				//查询上级领导
				$approver = approver($db,$array_applyer[$k]);

				$sql_str .= '(\''.$array_specificationid[$k].'\',\''.$array_apply_team[$k].'\',\''.$array_apply_date[$k].'\',\''.$v.'\',\''.$array_applyer[$k].'\',\''.$approver.'\',\''.$array_remark[$k].'\',\''.time().'\',\'A\',\''.$array_requirement_date[$k].'\'),';
				$specificationids .= $array_specificationid[$k].',';
			}
		}
		$sql_str = rtrim($sql_str,',');
		
		$specificationids = rtrim($specificationids,',');
		//把批量申购的物料信息插入到物料信息表中
		$sql = "INSERT INTO `db_mould_other_material`(`material_name`,`apply_team`,`apply_date`,`quantity`,`applyer`,`approver`,`remark`,`add_time`,`status`,`requirement_date`) VALUES$sql_str";
		 $db->query($sql);
		//物料规格表中插入下单时间
		$sql_specification = "UPDATE `db_other_material_specification` SET `last_date` = DATE_FORMAT(NOW(),'%Y-%m_%d') WHERE FIND_IN_SET(`specificationid`,'$specificationids')";

		$db->query($sql_specification);
		if($db->affected_rows){
			header('location:mould_other_material_apply.php?action=add');
		}
	}elseif($action == 'edit') {
		$id = $data['mould_other_id'];
		$to = $data['to'];
		$remark = $data['remark'];
		//判断是否通过审评
		if($data['submit'] == '退回'){
			$no_approval_sql = "UPDATE `db_mould_other_material` SET `remark`='$remark',`status`='D',`do_time`=".time()." WHERE `mould_other_id`=".$id;
			$db->query($no_approval_sql);
			if($db->affected_rows){
				header('location:mould_other_fee.php');
			}
		}elseif($data['submit'] == '通过'){
			if($to == 'B'){
				$sql_value = '`status`=\'B\',`remark`=\''.$remark.'\',`do_time`='.time();
			}elseif($to == 'C'){
				$sql_value = '`status`=\'C\',`remark`=\''.$remark.'\',`do_time`='.time();
			}
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