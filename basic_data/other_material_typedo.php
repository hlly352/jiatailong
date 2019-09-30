<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$material_typecode = trim($_POST['material_typecode']);
	$material_typename = trim($_POST['material_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_other_material_type` (`material_typeid`,`material_typecode`,`material_typename`,`material_typestatus`) VALUES (NULL,'$material_typecode','$material_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:other_material_type.php");
		}
	}elseif($action == "edit"){
		$material_typeid = $_POST['material_typeid'];
		$material_typestatus = $_POST['material_typestatus'];
		$sql = "UPDATE `db_other_material_type` SET `material_typecode` = '$material_typecode',`material_typename` = '$material_typename',`material_typestatus` = '$material_typestatus' WHERE `material_typeid` = '$material_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:other_material_type.php");
		}
	}elseif($action == 'edit_specification'){
		$specificationid = $_POST['specificationid'];
		$standard_stock = $_POST['standard_stock'];
		$specification_name = $_POST['specification'];
		$sql = "UPDATE `db_other_material_specification` SET `specification_name` = '$specification_name',`standard_stock` = '$standard_stock' WHERE `specificationid` = '$specificationid'";
		
		$db->query($sql);
		
		header('location:'.$_SERVER['HTTP_REFERER']);
		

	}elseif($action == "del"){

		$array_id = $_POST['id'];
		$material_typeid = fun_convert_checkbox($array_id);
		//查找当前分类下是否有物料
		$data_sql = "SELECT COUNT(*) AS `count` FROM `db_other_mateiral_data` WHERE `material_typeid` IN($material_typeid)";
		$res_data = $db->query($data_sql);
		if($res_data->num_rows){
			$row_data = $res_data->fetch_row()[0];
			if($row_data >0){
				header('location:'.$_SERVER['HTTP_REFERER']);
				return false;
			}
			}
		}elseif($action == 'specification'){
			$materialid = $_POST['materialid'];
			$specification = $_POST['specification'];
			$standard_stock = $_POST['standard_stock'];
			//添加多种规格
			foreach($specification as $k=>$v){
				$sql_str .= "('$materialid','$v','$standard_stock[$k]'),";
			}
			$sql_str = rtrim($sql_str,',');
			$sql = "INSERT INTO `db_other_material_specification`(`materialid`,`specification_name`,`standard_stock`) VALUES{$sql_str}";
			
			$db->query($sql);
			if($db->affected_rows){
				header("location:other_material_data.php");
			}
		}elseif($action == 'delete'){
		$sql = "DELETE FROM `db_other_material_type` WHERE `material_typeid` IN ($material_typeid)";

		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
		}elseif($action == 'del_specification'){
			$array_id = $_POST['id'];
			
			$id = fun_convert_checkbox($array_id);
			$sql = "DELETE FROM `db_other_material_specification` WHERE `specificationid` IN($id)";

			$db->query($sql);
			if($db->affected_rows){
				header('location:'.$_SERVER['HTTP_REFERER']);
			}
		}
	}

?>