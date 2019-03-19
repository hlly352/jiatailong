<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$apply_listid = fun_check_int($_GET['id']);
$sql_apply = "SELECT `db_cutter_apply_list`.`cutterid`,`db_cutter_apply_list`.`quantity`,`db_cutter_apply_list`.`out_quantity`,`db_cutter_apply_list`.`plan_date`,`db_cutter_apply_list`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_apply`.`apply_time`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_employee`.`employee_name`,`db_mould`.`mould_number` FROM `db_cutter_apply_list` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` WHERE `db_cutter_apply_list`.`apply_listid` = '$apply_listid'";
$result_apply = $db->query($sql_apply);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result_apply->num_rows){
	$array_apply = $result_apply->fetch_assoc();
	$cutterid = $array_apply['cutterid'];
	$sql_surplus = "SELECT `db_cutter_order_list`.`surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` = '$cutterid' AND `db_cutter_order_list`.`surplus` > 0";
	$result_surplus = $db->query($sql_surplus);
	if($result_surplus->num_rows){
		while($row_surplus = $result_surplus->fetch_assoc()){
			$surplus += $row_surplus['surplus'];
		}
	}else{
		$surplus = 0;
	}
?>
<div id="table_sheet">
  <h4>刀具申领单</h4>
  <table>
    <tr>
      <th width="10%">申领单号：</th>
      <td width="15%"><?php echo $array_apply['apply_number']; ?></td>
      <th width="10%">申领人：</th>
      <td width="15%"><?php echo $array_apply['employee_name']; ?></td>
      <th width="10%">申请日期：</th>
      <td width="15%"><?php echo $array_apply['apply_date']; ?></td>
      <th width="10%">操作时间：</th>
      <td width="15%"><?php echo $array_apply['apply_time']; ?></td>
    </tr>
    <tr>
      <th>类型：</th>
      <td><?php echo $array_apply['type']; ?></td>
      <th>规格：</th>
      <td><?php echo $array_apply['specification']; ?></td>
      <th>材质：</th>
      <td><?php echo $array_cutter_texture[$array_apply['texture']]; ?></td>
      <th>硬度：</th>
      <td><?php echo $array_apply['hardness']; ?></td>
    </tr>
    <th>库存：</th>
      <td><?php echo $surplus; ?> 件</td>
      <th>申领数量：</th>
      <td><?php echo $array_apply['quantity']; ?> 件</td>
      <th>已领数量：</th>
      <td><?php echo $array_apply['out_quantity']; ?> 件</td>
      <th>模具：</th>
      <td><?php echo $array_apply['mould_number']; ?></td>
      
    </tr>
    <tr>
      <th>计划领用日期：</th>
      <td><?php echo $array_apply['plan_date']; ?></td>
      <th>备注：</th>
      <td colspan="5"><?php echo $array_apply['remark']; ?></td>
    </tr>
  </table>
</div>
<?php
$sql_inout = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`form_number`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_inout`.`dotime`,`db_cutter_order`.`order_number`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_inout`.`employeeid` WHERE `db_cutter_inout`.`apply_listid` = '$apply_listid' AND `db_cutter_inout`.`dotype` = 'O' ORDER BY `db_cutter_inout`.`inoutid` DESC";
$result_inout = $db->query($sql_inout);
?>
<div id="table_list">
  <?php if($result_inout->num_rows){ ?>
  <table>
    <caption>
    出库记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">合同号</th>
      <th width="10%">表单号</th>
      <th width="6%">出库数量</th>
      <th width="6%">更换数量</th>
      <th width="6%">单位</th>
      <th width="13%">出库日期</th>
      <th width="13%">操作人</th>
      <th width="14%">操作时间</th>
      <th width="16%">备注</th>
    </tr>
    <?php while($row_inout = $result_inout->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_inout['inoutid']; ?></td>
      <td><?php echo $row_inout['order_number']; ?></td>
      <td><?php echo $row_inout['form_number']; ?></td>
      <td><?php echo $row_inout['quantity']; ?></td>
      <td><?php echo $row_inout['old_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row_inout['dodate']; ?></td>
      <td><?php echo $row_inout['employee_name']; ?></td>
      <td><?php echo $row_inout['dotime']; ?></td>
      <td><?php echo $row_inout['remark']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无出库记录</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>