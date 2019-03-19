<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$expressid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee_express_receive`.`expressid`,`db_employee_express_receive`.`express_num`,`db_employee_express_receive`.`sender`,`db_employee_express_receive`.`cost`,`db_employee_express_receive`.`express_item`,`db_employee_express_receive`.`applyer`,`db_employee_express_receive`.`confirmor`,`db_employee_express_receive`.`receipt_date`,`db_employee_express_receive`.`apply_status`,`db_employee_express_receive`.`get_status`,`db_employee_express_receive`.`apply_time`,`db_employee_express_receive`.`confirm_time`,`db_employee_express_receive`.`dotime`,`db_employee_express_receive`.`express_status`,`db_receiver`.`employee_name` AS `receiver_name`,`db_registrant`.`employee_name` AS `registrant_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmor`.`employee_name` AS `confirmor_name`,`db_department`.`dept_name`,`db_express_inc`.`inc_cname`,`db_express_inc`.`inc_ename` FROM `db_employee_express_receive` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express_receive`.`express_incid` INNER JOIN `db_employee` AS `db_receiver` ON `db_receiver`.`employeeid` = `db_employee_express_receive`.`receiver` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_receiver`.`deptid` INNER JOIN `db_employee` AS `db_registrant` ON `db_registrant`.`employeeid` = `db_employee_express_receive`.`registrant` LEFT JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express_receive`.`applyer` LEFT JOIN `db_employee` AS `db_confirmor` ON `db_confirmor`.`employeeid` = `db_employee_express_receive`.`confirmor` WHERE `db_employee_express_receive`.`expressid` = '$expressid'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" src="../js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script language="javascript" src="js/main.js" type="text/javascript"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $applyer = $array['applyer'];
	  $confirmor = $array['confirmor'];
	  $applyer_name = $applyer?$array['applyer_name']:'--';
	  $confirmor_name = $confirmor?$array['confirmor_name']:'--';
	  $apply_time = $applyer?$array['apply_time']:'--';
	  $confirm_time = $confirmor?$array['confirm_time']:'--';
	  $apply_status = $array_express_apply_status[$array['apply_status']];
	  $get_status = $array_express_get_status[$array['get_status']];
	  $express_status = $array_status[$array['express_status']];
  ?>
  <h4>快递收件登记信息</h4>
  <table>
    <tr>
      <th width="20%">快递公司：</th>
      <td width="80%"><?php echo $array['inc_cname'].'('.$array['inc_ename'].')'; ?></td>
    </tr>
    <tr>
      <th>快递单号：</th>
      <td><?php echo $array['express_num']; ?></td>
    </tr>
    <tr>
      <th>寄件方：</th>
      <td><?php echo $array['sender']; ?></td>
    </tr>
    <tr>
      <th>收件人：</th>
      <td><?php echo $array['dept_name'].'-'.$array['receiver_name']; ?></td>
    </tr>
    <tr>
      <th>费用：</th>
      <td><?php echo $array['cost']; ?></td>
    </tr>
    <tr>
      <th>快递物品：</th>
      <td><?php echo $array['express_item']; ?></td>
    </tr>
    <tr>
      <th>收件日期：</th>
      <td><?php echo $array['receipt_date']; ?></td>
    </tr>
    <tr>
      <th>状态：</th>
      <td><?php echo $express_status; ?></td>
    </tr>
    <tr>
      <th>申领状态：</th>
      <td><?php echo $apply_status; ?></td>
    </tr>
    <tr>
      <th>申领人：</th>
      <td><?php echo $applyer_name; ?></td>
    </tr>
    <tr>
      <th>申领时间：</th>
      <td><?php echo $apply_time; ?></td>
    </tr>
    <tr>
      <th>提件状态：</th>
      <td><?php echo $get_status; ?></td>
    </tr>
    <tr>
      <th>提件确认人：</th>
      <td><?php echo $confirmor_name; ?></td>
    </tr>
    <tr>
      <th>确认时间：</th>
      <td><?php echo $confirm_time; ?></td>
    </tr>
    <tr>
      <th>登记人：</th>
      <td><?php echo $array['registrant_name']; ?></td>
    </tr>
    <tr>
      <th>登记时间：</th>
      <td><?php echo $array['dotime']; ?></td>
    </tr>
  </table>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>