<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
#goout, #express, #vehicle  {
	width:100%;
	border:1px solid #CCC;
	margin-bottom:10px;
}
#goout h4, #express h4, #vehicle h4 {
	font-size:13px;
	background:#EEE;
	padding:4px 10px;
	border-bottom:1px solid #999;
}
#goout p, #express p, #vehicle P {
	font-size:13px;
	padding:4px 10px;
}
#goout dl {
	float:left;
	width:140px;
	height:245px;
	margin:10px;
	text-align:center;
}
#goout dl dt, #express dl dt, #vehicle dl dt {
	font-size:13px;
	padding:2px 0;
}
#goout dl dd{
	width:98px;
	height:140px;
	margin:0 auto;
}
#express dl {
	float:left;
	width:140px;
	height:188px;
	margin:10px;
	text-align:center;
}
#express dl a {
	color:#039;
}
#vehicle dl {
	float:left;
	width:240px;
	height:265px;
	margin:10px;
	text-align:center;
	background:#EEE;
}
</style>
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
//查询今日出门人员
$sql_goout = "SELECT `db_employee_goout`.`gooutid`,`db_employee_goout`.`confirmer_out`,`db_employee_goout`.`confirmer_in`,`db_employee`.`employee_name`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_department`.`dept_name`,DATE_FORMAT(`db_employee_goout`.`start_time`,'%H:%i:%s') AS `start_time`,DATE_FORMAT(`db_employee_goout`.`finish_time`,'%H:%i:%s') AS `finish_time` FROM `db_employee_goout` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_goout`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE DATE_FORMAT(`db_employee_goout`.`start_time`,'%Y-%m-%d') = CURDATE() AND `db_employee_goout`.`approve_status` = 'B' AND `db_employee_goout`.`goout_status` = 1 ORDER BY DATE_FORMAT(`db_employee_goout`.`start_time`,'%H:%i:%s') ASC";
$result_goout = $db->query($sql_goout);
?>
<div id="goout">
  <h4>今日出门信息</h4>
  <?php
  if($result_goout->num_rows){
	  while($row_goout = $result_goout->fetch_assoc()){
		  $gooutid = $row_goout['gooutid'];
		  $confirmer_out = $row_goout['confirmer_out'];
		  $confirmer_in = $row_goout['confirmer_in'];
		  $employee_photo_path = "../upload/personnel/".$row_goout['photo_filedir'].'/'.$row_goout['photo_filename'];
		  $employee_photo = is_file($employee_photo_path)?"<img src=\"".$employee_photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
		  if(!$confirmer_out){
			  $goout_time = $row_goout['start_time'];
			  $goout_dotype = '待出厂';
		  }elseif(!$confirmer_in){
			  $goout_time = $row_goout['finish_time'];
			  $goout_dotype = '待回厂';
		  }elseif($confirmer_in && $confirmer_out){
			  $goout_time = $row_goout['finish_time'];
			   $goout_dotype = '完成';
		  }
  ?>
  <dl>
    <dd><?php echo $employee_photo; ?></dd>
    <dt><?php echo $row_goout['dept_name']; ?></dt>
    <dt><?php echo $row_goout['employee_name']; ?></dt>
    <dt><?php echo $goout_time; ?></dt>
    <dt><?php echo $goout_dotype; ?></dt>
  </dl>
  <?php } ?>
  <div class="clear"></div>
  <?php
  }else{
	  echo "<p>暂无记录</p>";
  }
  ?>
</div>
<?php
//查询今日请假人员
$sql_leave = "SELECT `db_employee_leave`.`leaveid`,`db_employee_leave`.`confirmer_out`,`db_employee_leave`.`confirmer_in`,`db_employee`.`employee_name`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_department`.`dept_name`,DATE_FORMAT(`db_employee_leave`.`start_time`,'%m-%d %H:%i:%s') AS `start_time`,DATE_FORMAT(`db_employee_leave`.`finish_time`,'%m-%d %H:%i:%s') AS `finish_time` FROM `db_employee_leave` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_leave`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE ((`db_employee_leave`.`confirmer_out` = 0 AND DATE_FORMAT(`db_employee_leave`.`start_time`,'%Y-%m-%d') = CURDATE()) OR (`db_employee_leave`.`confirmer_out` != 0 AND DATE_FORMAT(`db_employee_leave`.`finish_time`,'%Y-%m-%d') = CURDATE())) AND `db_employee_leave`.`approve_status` = 'B' AND `db_employee_leave`.`leave_status` = 1 ORDER BY DATE_FORMAT(`db_employee_leave`.`start_time`,'%H:%i:%s') ASC";
$result_leave = $db->query($sql_leave);
?>
<div id="goout">
  <h4>今日请假信息</h4>
  <?php
  if($result_leave->num_rows){
	  while($row_leave = $result_leave->fetch_assoc()){
		  $leaveid = $row_leave['leaveid'];
		  $confirmer_out = $row_leave['confirmer_out'];
		  $confirmer_in = $row_leave['confirmer_in'];
		  $employee_photo_path = "../upload/personnel/".$row_leave['photo_filedir'].'/'.$row_leave['photo_filename'];
		  $employee_photo = is_file($employee_photo_path)?"<img src=\"".$employee_photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
		  if(!$confirmer_out){
			  $leave_time = $row_leave['start_time'];
			  $leave_dotype = '待出厂';
		  }elseif(!$confirmer_in){
			  $leave_time = $row_leave['finish_time'];
			  $leave_dotype = '待回厂';
		  }elseif($confirmer_in && $confirmer_out){
			  $leave_time = $row_leave['finish_time'];
			   $leave_dotype = '完成';
		  }
  ?>
  <dl>
    <dd><?php echo $employee_photo; ?></dd>
    <dt><?php echo $row_leave['dept_name']; ?></dt>
    <dt><?php echo $row_leave['employee_name']; ?></dt>
    <dt><?php echo $leave_time; ?></dt>
    <dt><?php echo $leave_dotype; ?></dt>
  </dl>
  <?php } ?>
  <div class="clear"></div>
  <?php
  }else{
	  echo "<p>暂无记录</p>";
  }
  ?>
</div>
<?php
//查询今日寄件快递
$sql_express = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`express_num`,`db_employee_express`.`reckoner`,`db_employee`.`employee_name`,`db_express_inc`.`inc_cname` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE DATE_FORMAT(`db_employee_express`.`apply_date`,'%Y-%m-%d') = CURDATE() AND `db_employee_express`.`approve_status` = 'B' AND `db_employee_express`.`express_status` = 1";
$result_express = $db->query($sql_express);
?>
<div id="express">
  <h4>今日快递(寄件)</h4>
  <?php
  if($result_express->num_rows){
	  while($row_express = $result_express->fetch_assoc()){
		  $issettle = $row_express['reckoner']?'已结算':'未结算';
  ?>
  <dl>
    <dd><img src="../images/express.png" width="100" height="88" /></dd>
    <dt><?php echo $row_express['inc_cname']; ?></dt>
    <dt><?php echo $row_express['express_num']; ?></dt>
    <dt><?php echo $row_express['employee_name']; ?></dt>
    <dt><?php echo $issettle; ?></dt>
  </dl>
  <?php } ?>
  <div class="clear"></div>
  <?php
  }else{
	  echo "<p>暂无记录</p>";
  }
  ?>
</div>
<?php
//查询今日收件快递
$sql_express_receive = "SELECT `db_employee_express_receive`.`expressid`,`db_employee_express_receive`.`express_num`,`db_employee_express_receive`.`apply_status`,`db_employee_express_receive`.`get_status`,`db_employee`.`employee_name`,`db_express_inc`.`inc_cname` FROM `db_employee_express_receive` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express_receive`.`express_incid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express_receive`.`receiver` WHERE DATE_FORMAT(`db_employee_express_receive`.`receipt_date`,'%Y-%m-%d') = CURDATE() AND `db_employee_express_receive`.`express_status` = 1";
$result_express_receive = $db->query($sql_express_receive);
?>
<div id="express">
  <h4>今日快递(待申领)</h4>
  <?php
  if($result_express_receive->num_rows){
	  while($row_express_receive= $result_express_receive->fetch_assoc()){
		  $express_receiveid = $row_express_receive['expressid'];
		  $apply_status = $row_express_receive['apply_status'];
		  $get_status = $row_express_receive['get_status'];
		  if($get_status){
			  $status = '已提件';
		  }elseif($apply_status && !$get_status){
			  $status = '已申领,未提件';
		  }elseif(!$apply_status){
			  $status = "<a href=\"employee_express_receive_apply.php?id=".$express_receiveid."\">未申领</a>";
		  }
		  
  ?>
  <dl>
    <dd><img src="../images/express.png" width="100" height="88" /></dd>
    <dt><?php echo $row_express_receive['inc_cname']; ?></dt>
    <dt><?php echo $row_express_receive['express_num']; ?></dt>
    <dt><?php echo $row_express_receive['employee_name']; ?></dt>
    <dt><?php echo $status; ?></dt>
  </dl>
  <?php } ?>
  <div class="clear"></div>
  <?php
  }else{
	  echo "<p>暂无记录</p>";
  }
  ?>
</div>
<?php
//查询今日用车记录
$sql_vehicle = "SELECT `db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`other`,`db_employee`.`employee_name`,`db_department`.`dept_name`,`db_vehicle`.`plate_number`,`db_vehicle`.`owner`,`db_vehicle`.`contact`,`db_vehicle`.`vehicle_type` FROM `db_vehicle_list` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`vehicle_status` = 1 AND `approve_status` = 'B' AND DATE_FORMAT(`db_vehicle_list`.`start_time`,'%Y-%m-%d') >= CURDATE() ORDER BY `db_vehicle_list`.`vehicleid` ASC,`db_vehicle_list`.`start_time` ASC ";
$result_vehicle = $db->query($sql_vehicle);
?>
<div id="vehicle">
  <h4>今日用车信息</h4>
  <?php
  if($result_vehicle->num_rows){
	  while($row_vehicle = $result_vehicle->fetch_assoc()){
		  $vehicle_type = $array_vehicle_type[$row_vehicle['vehicle_type']];
		  $roundtype = $array_vehicle_roundtype[$row_vehicle['roundtype']];
		  $nowtime = fun_gettime();
		  $start_time = $row_vehicle['start_time'];
		  $finish_time = $row_vehicle['finish_time'];
		  if($nowtime < $start_time){
			  $runstatus = "<font color=\"#FF6600\">预运行</font>";
		  }elseif($nowtime > $start_time && $nowtime < $finish_time){
			  $runstatus = "<font color=\"#3366CC\">运行中</font>";
		  }elseif($nowtime > $finish_time){
			  $runstatus = "<font color=\"#FF0000\">已运行</font>";
		  }
  ?>
  <dl>
    <dd><img src="../images/vehicle.png" width="50" height="50" /></dd>
    <dt><?php echo $row_vehicle['plate_number'].'('.$vehicle_type.')' ?></dt>
    <dt><?php echo $row_vehicle['owner'].'('.$row_vehicle['contact'].')' ?></dt>
    <dt>申请人：<?php echo $row_vehicle['dept_name'].'-'.$row_vehicle['employee_name']; ?></dt>
    <dt>路程：<?php echo $row_vehicle['departure'].'->'.$row_vehicle['destination'].'('.$roundtype.')'; ?></dt>
    <dt>出发时间：<?php echo $start_time; ?></dt>
    <dt>返程时间：<?php echo $finish_time; ?></dt>
    <dt>随车人员：<?php echo $row_vehicle['other']; ?></dt>
    <dt>运行状态：<?php echo $runstatus; ?></dt>
  </dl>
  <?php } ?>
  <div class="clear"></div>
  <?php
  }else{
	  echo "<p>暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>