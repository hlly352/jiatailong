<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mouldid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould`.`mouldid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_client`.`client_code`,`db_mould_status`.`mould_statusname` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` WHERE `db_mould`.`mouldid` = '$mouldid'";
$result = $db->query($sql);
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
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
  ?>
  <h4>模具信息</h4>
  <table>
    <tr>
      <th width="10%">代码：</th>
      <td width="15%"><?php echo $array['client_code']; ?></td>
      <th width="10%">项目名称：</th>
      <td width="15%"><?php echo $array['project_name']; ?></td>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">目前状态：</th>
      <td width="15%"><?php echo $array['mould_statusname']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_order`.`order_number`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`employeeid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_inout`.`dotype` = 'O' AND `db_cutter_apply_list`.`mouldid` = '$mouldid'";
$result = $db->query($sql);
?>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <caption>
    刀具出库记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">申领编号</th>
      <th width="10%">合同号</th>
      <th width="8%">类型</th>
      <th width="12%">规格</th>
      <th width="8%">材质</th>
      <th width="10%">硬度</th>
      <th width="5%">出库数量</th>
      <th width="5%">更换数量</th>
      <th width="4%">单位</th>
      <th width="8%">申领人</th>
      <th width="8%">出库日期</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$inoutid = $row['inoutid'];
	?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row['apply_number']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['old_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：刀具出库记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>