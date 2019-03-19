<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$express_incid = $_POST['express_incid'];
	$express_num = str_replace_null(trim($_POST['express_num']));
	$sender = trim($_POST['sender']);
	$receiver = $_POST['receiver'];
	$cost = $_POST['cost'];
	$express_item = trim($_POST['express_item']);
	$receipt_date = $_POST['receipt_date'];
	$registrant = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_employee_express_receive` (`expressid`,`express_incid`,`express_num`,`sender`,`receiver`,`cost`,`express_item`,`receipt_date`,`registrant`,`express_status`,`dotime`) VALUES (NULL,'$express_incid','$express_num','$sender','$receiver','$cost','$express_item','$receipt_date','$registrant',1,'$dotime')";
		$db->query($sql);
		if($expressid = $db->insert_id){
			$sql_express = "SELECT `db_receiver`.`employee_name`,`db_receiver`.`email` AS `receiver_email`,`db_superior`.`email` AS `superior_email`,`db_express_inc`.`inc_cname` FROM `db_employee_express_receive` INNER JOIN `db_employee` AS `db_receiver` ON `db_receiver`.`employeeid` = `db_employee_express_receive`.`receiver` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_receiver`.`superior` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express_receive`.`express_incid` WHERE `db_employee_express_receive`.`expressid` = '$expressid'";
			$result_express = $db->query($sql_express);
			if($result_express->num_rows){
				$array_express = $result_express->fetch_assoc();
				$employee_name = $array_express['employee_name'];
				$inc_cname = $array_express['inc_cname'];
				$email_name = $array_express['receiver_email']?$array_express['receiver_email']:$array_express['superior_email'];
				if($email_name){
					$email_subject = "快递申领".$express_num."到达";
					$email_content = $employee_name."，您的".$inc_cname."到达门卫，快递单号为".$express_num."，请及时申领处理.";
					$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
					$db->query($sql_email);
				}
			}
			header("location:employee_express_receive.php");
		}
	}elseif($action == "edit"){
		$expressid = $_POST['expressid'];
		$express_status = $_POST['express_status'];
		$sql = "UPDATE `db_employee_express_receive` SET `express_incid` = '$express_incid',`express_num` = '$express_num',`sender` = '$sender',`receiver` = '$receiver',`cost` = '$cost',`express_item` = '$express_item',`receipt_date` = '$receipt_date',`express_status` = '$express_status' WHERE `expressid` = '$expressid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$expressid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_employee_express_receive` WHERE `expressid` IN ($expressid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>