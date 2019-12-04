<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$page = $_POST['page'];
if($_POST['submit']){
	$action = $_POST['action'];
	$name = htmlspecialchars(trim($_POST['name']));
	if($action == "add"){
		$sql_sort = "SELECT (MAX(`sort`) + 0) AS `max_sort` FROM `db_project_review_data`";
		$result_sort = $db->query($sql_sort);
		if($result_sort->num_rows){
			$max_sort = $result_sort->fetch_assoc()['max_sort'];
			$next_sort = $max_sort + 1;
		}else{
			$next_sort = 1;
		}

		//添加新的项目名称
		$sql = "INSERT INTO `db_project_review_data`(`name`,`sort`) VALUES('$name','$next_sort')";
		$db->query($sql);
		header("location:project_review.php");
	}elseif($action == "edit"){
		$id = $_POST['id'];
		$sql = "UPDATE `db_project_review_data` SET `name` = '$name' WHERE `id` = '$id'";
		$db->query($sql);
		header("location:project_review.php?page=".$page);
		
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$id = fun_convert_checkbox($array_id);
		///////////////
		//检测是否有已测评项目//
		///////////////
		$sql = "DELETE FROM `db_project_review_data` WHERE `id` IN ($id)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>