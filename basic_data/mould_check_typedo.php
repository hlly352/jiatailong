<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
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
		$typeid = $_POST['typeid'];
		$pid = $_POST['pid'];
		$typename = htmlspecialchars(trim($_POST['typename']));
		//查询父级类型的path
		$sql_path = "SELECT `path` FROM `db_mould_check_type` WHERE `id` = '$pid'";
		$result_path = $db->query($sql_path);
		if($result_path->num_rows){
			$array_path = $result_path->fetch_assoc();
			$pid_path = $array_path['path'];
		}
		$path = $pid_path.$typeid.',';
		$sql = "UPDATE `db_mould_check_type` SET `pid` = '$pid',`typename` = '$typename',`path` = '$path' WHERE `id` = '$typeid'";
		$db->query($sql);
		header("location:mould_check_type.php");
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$typeid = fun_convert_checkbox($array_id);
		//查询当前类型下是否有子类
		$sql_child = "SELECT `id` FROM `db_mould_check_type` WHERE `pid` IN($typeid)";
		$result_child = $db->query($sql_child);
		$array_child_typeid = array();
		if($result_child->num_rows){
			echo '此类型下有子项目';
			header('Refresh:3;url='.$_SERVER['HTTP_REFERER']);
			exit;
		}
		$sql_data = "SELECT * FROM `db_mould_check_data` WHERE `cotegoryid` IN($typeid)";
		$result_data = $db->query($sql_data);
		if($result_data->num_rows){
			echo '此类型下有项目';
			header('Refresh:3;url='.$_SERVER['HTTP_REFERER']);
			exit;
		}
		$sql = "DELETE FROM `db_mould_check_type` WHERE `id` IN ($typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>