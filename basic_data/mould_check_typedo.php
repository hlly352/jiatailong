<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
var_dump($_POST);
if($_POST['submit']){
	$action = $_POST['action'];
	$pid = trim($_POST['pid']);
	$typename = trim($_POST['typename']);
	if($action == "add"){
		//查找父级类型的path
		$sql_path = "SELECT `path` FROM `db_mould_check_type` WHERE `id` = '$pid'";
		$result_path = $db->query($sql_path);
		if($result_path->num_rows){
			$p_path = $result_path->fetch_assoc()['path'];
		}else{
			$p_path = '';
		}
		//添加新的类型
		$sql_add = "INSERT INTO `db_mould_check_type`(`pid`,`typename`) VALUES('$pid','$typename')";;
		$db->query($sql_add);
		$id = $db->insert_id;
		$path = $p_path.$id.',';
		//更新path
		$sql_update = "UPDATE `db_mould_check_type` SET `path` = '$path' WHERE `id` = '$id'";
		$db->query($sql_update);
		header("location:mould_check_type.php");
	}elseif($action == "edit"){
		$material_typeid = $_POST['material_typeid'];
		$material_typestatus = $_POST['material_typestatus'];
		$sql = "UPDATE `db_material_type` SET `material_typecode` = '$material_typecode',`material_typename` = '$material_typename',`material_typestatus` = '$material_typestatus' WHERE `material_typeid` = '$material_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$material_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_material_type` WHERE `material_typeid` IN ($material_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>