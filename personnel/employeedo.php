<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$employee_name = trim($_POST['employee_name']);
	$employee_number = trim($_POST['employee_number']);
	$deptid = $_POST['deptid'];
	$superior = $_POST['superior'];
	$position_type = $_POST['position_type'];
	$positionid = $_POST['positionid'];
	$education = $_POST['education'];
	$entrydate = $_POST['entrydate'];
	$idcard = trim($_POST['idcard']);
	$birthday = $_POST['birthday'];
	$sex = $_POST['sex'];
	$phone = trim($_POST['phone']);
	$address = trim($_POST['address']);
	$remark = trim($_POST['remark']);
	if($action == "add"){
		$sql = "INSERT INTO `db_employee` (`employeeid`,`employee_name`,`employee_number`,`deptid`,`superior`,`positionid`,`position_type`,`education`,`entrydate`,`idcard`,`birthday`,`sex`,`phone`,`address`,`employee_status`,`remark`) VALUES (NULL,'$employee_name','$employee_number','$deptid','$superior','$positionid','$position_type','$education','$entrydate','$idcard','$birthday','$sex','$phone','$address',1,'$remark')";
		$db->query($sql);
		if($employeeid = $db->insert_id){
			//籍贯
			$cardno = substr($idcard,0,6);
			$sql_card = "SELECT `place` FROM `db_card_place` WHERE `cardno` = '$cardno'";
			$result_card = $db->query($sql_card);
			if($result_card->num_rows){
				$array_card = $result_card->fetch_assoc();
				$place = $array_card['place'];
			}else{
				$cardno = substr($idcard,0,4).'00';
				$sql_card = "SELECT `place` FROM `db_card_place` WHERE `cardno` = '$cardno'";
				$result_card = $db->query($sql_card);
				if($result_card->num_rows){
					$array_card = $result_card->fetch_assoc();
					$place = $array_card['place'];
				}else{
					$cardno = substr($idcard,0,2).'0000';
					$sql_card = "SELECT `place` FROM `db_card_place` WHERE `cardno` = '$cardno'";
					$result_card = $db->query($sql_card);
					if($result_card->num_rows){
						$array_card = $result_card->fetch_assoc();
						$place = $array_card['place'];
					}
				}
			}
			$sql_update = "UPDATE `db_employee` SET `nativeplace` = '$place' WHERE `employeeid` = '$employeeid'";
			$db->query($sql_update);
			header("location:employee.php");
		}
	}elseif($action == "edit"){
		$employeeid = $_POST['employeeid'];
		$nativeplace = trim($_POST['nativeplace']);
		$employee_status = $_POST['employee_status'];
		$termdate = $_POST['termdate'];
		//离职办理，关闭账号,同时删除该账号的成员权限
		if($employee_status == 0){
			$sql_account_status = ",`account_status` = 0";
		}
		$sql = "UPDATE `db_employee` SET `employee_name` = '$employee_name',`employee_number` = '$employee_number',`deptid` = '$deptid',`superior` = '$superior',`positionid` = '$positionid',`position_type` = '$position_type',`education` = '$education',`entrydate` = '$entrydate',`termdate` = '$termdate',`idcard` = '$idcard',`birthday` = '$birthday',`sex` = '$sex',`nativeplace` = '$nativeplace',`phone` = '$phone',`address` = '$address',`employee_status` = '$employee_status',`remark` = '$remark' $sql_account_status WHERE `employeeid` = '$employeeid'";
		$db->query($sql);
		if($db->affected_rows){
			if($employee_status == 0){
				$sql_system_employee = "DELETE FROM `db_system_employee` WHERE `employeeid` = '$employeeid'";
				$db->query($sql_system_employee);
			}
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_employeeid = $_POST['id'];
		$employeeid = fun_convert_checkbox($array_employeeid);
		$sql_photo = "SELECT `photo_filedir`,`photo_filename` FROM `db_employee` WHERE `employeeid` IN ($employeeid)";
		$result_photo = $db->query($sql_photo);
		if($result_photo->num_rows){
			while($row_photo = $result_photo->fetch_assoc()){
				$photo_filedir = $row_photo['photo_filedir'];
				$photo_filename = $row_photo['photo_filename'];
				$photo_filepath = "../upload/personnel/".$photo_filedir.'/'.$photo_filename;
				$photo_big_filepath = "../upload/personnel/".$photo_filedir.'/B'.$photo_filename;
				fun_delfile($photo_filepath);
				fun_delfile($photo_big_filepath);
			}
		}
		$sql = "DELETE FROM `db_employee` WHERE `employeeid` IN ($employeeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>