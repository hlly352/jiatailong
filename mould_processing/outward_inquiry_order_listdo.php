<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_POST['submit']){
	$array_inquiryid = $_POST['inquiryid'];
	$inquiry_orderid = $_POST['inquiry_orderid'];
	$sql_str = '';
	foreach($array_inquiryid as $inquiryid){
		//获取对应的数据
		$plan_date = $_POST["plan_date_{$inquiryid}"];
		$outward_remark = $_POST["outward_remark_{$inquiryid}"];
		//更改备注信息
		$sql_remark = "UPDATE `db_outward_inquiry` SET `outward_remark` = '$outward_remark' WHERE `inquiryid` = $inquiryid ";
		$db->query($sql_remark);
		$sql_str .= '(\''.$inquiry_orderid.'\',\''.$inquiryid.'\',\''.$employeeid.'\',\''.$plan_date.'\'),';
	}
	$sql_str = rtrim($sql_str,',');
	//添加到询价单中
	$sql_inquiry_orderlist = "INSERT INTO `db_outward_inquiry_orderlist`(`inquiry_orderid`,`inquiryid`,`employeeid`,`plan_date`) VALUES $sql_str";
	
	$db->query($sql_inquiry_orderlist);
	if($db->insert_id){
		header("location:outward_inquiry_orderlist.php?id=".$inquiry_orderid);
	}
}
?>