<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$array_system = $_SESSION['system_dir'];
foreach($array_system as $k=>$v){
	if($v == $system_dir){
		$systemid = $k;
	}
}
$sql_admin = "SELECT `employeeid` FROM `db_system_employee` WHERE `systemid` = '$systemid' AND `isadmin` = '1' AND `isconfirm` = '1'";
$result_admin = $db->query($sql_admin);
if($result_admin->num_rows){
	$approver = $result_admin->fetch_assoc()['employeeid'];
}
//查询当前模块的管理者
$action = $_POST['action'];
if($_POST['submit']){
	if($action == 'add'){
		$outward_typeid = $_POST['outward_typeid'];
		$array_materialid = $_POST['id'];
		$query = $_POST['query'];
		$page = $_POST['page'];

		//获取要加工的数量
		foreach($array_materialid as $materialid){
			$outward_quantity = $_POST["outward_quantity_{$materialid}"];
			$outward_remark = $_POST["remark_{$materialid}"];
			$sqladd .= "(NULl,'$materialid','$employeeid','$approver','$outward_typeid','$outward_quantity','$outward_remark'),";
		}
		$sqladd = rtrim($sqladd,',');
		$sql = "INSERT INTO `db_outward_inquiry` (`inquiryid`,`materialid`,`employeeid`,`approver`,`outward_typeid`,`outward_quantity`,`outward_remark`) values ".$sqladd;
		$db->query($sql);
		if($db->insert_id){
			if($page || $query){
				header('location:'.$_SERVER['HTTP_REFERER'].'&outward_typeid='.$outward_typeid);
			}else{
				header('location:'.$_SERVER['HTTP_REFERER'].'?outward_typeid='.$outward_typeid);
			}
		}
	}elseif($action == 'del'){
		$array_inquiryid = $_POST['id'];
		$inquiryid = fun_convert_checkbox($array_inquiryid);
		
		$sql = "DELETE FROM `db_outward_inquiry` WHERE `inquiryid` IN($inquiryid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>