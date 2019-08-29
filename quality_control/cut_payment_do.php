<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_POST['action'];
$inoutid = $_POST['inoutid'];
$cut_payment_type = $_POST['cut_payment_type'];
$cut_payment = trim($_POST['cut_payment']);
$cut_cause = trim($_POST['cut_cause']);
$supplierid = $_POST['supplierid'];
$material_name = $_POST['material_name'];
$order_number = $_POST['order_number'];
$specification = $_POST['specification'];
$image = $_FILES['image'];
if($action == 'add'){
	if($image != null){
		//拼接图片路径
		$filedir = date('Ymd');
		$upfiledir = "../upload/cut_payment/".$filedir."/";
		echo $upfiledir;
	}
	if(!is_dir($upfiledir)){
		mkdir($upfiledir,0777,true);
	}
	$upload_name = $upfiledir.$image['name'];
	//得到传输的数据
	if($image['tmp_name'][0] != null){
		if($image['name']){
			//上传图片
			if(is_uploaded_file($image['tmp_name'])){
				move_uploaded_file($image['tmp_name'],$upload_name);
			}
		}
	}
if($cut_payment_type == 'M'){
	$inout_sql = "UPDATE `db_material_inout` SET `cut_payment` = '$cut_payment' WHERE `inoutid` = '$inoutid'";
	$db->query($inout_sql);
}
	
//新增扣款信息
$sql = "INSERT INTO `db_cut_payment`(`inoutid`,`supplierid`,`material_name`,`order_number`,`specification`,`cut_payment_type`,`cut_payment`,`cut_cause`,`image`,`employeeid`,`add_time`) VALUES('$inoutid','$supplierid','$material_name','$order_number','$specification','$cut_payment_type','$cut_payment','$cut_cause','$upload_name','$employeeid',DATE_FORMAT(NOW(),'%Y-%m-%d'))";
$db->query($sql);
if($db->affected_rows){
	header('location:cut_payment.php');
}
}

?>