<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$accountid = $_GET['id'];
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
<title>财务管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
//通过对账汇总表id查询详情
$sql = "SELECT `db_outward_order_list`.`listid`,`db_outward_order_list`.`order_quantity`,`db_outward_order_list`.`unit_price`,`db_outward_order_list`.`amount`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_outward_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_account_order_list` ON `db_outward_order_list`.`orderid` = `db_account_order_list`.`orderid` WHERE `db_account_order_list`.`accountid` = '$accountid' $sqlwhere ORDER BY `db_mould`.`mould_number` DESC";

$result = $db->query($sql);
?>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <caption>
    对账详情
    </caption>
      <tr>
        <th width="">ID</th>
        <th width="">模具编号</th>
        <th width="">料单编号</th>
        <th width="">料单序号</th>
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">材质</th>
        <th width="">数量</th>
        <th width="">加工数量</th>
        <th width="">单价</th>
        <th width="">金额</th>
    </tr>
    <?php
	$surplus = 0;
	while($row = $result->fetch_assoc()){
	?>
  <tr>
      <td>
          <input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $row['specification'] ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['order_quantity'] ?></td>
        <td><?php echo $row['unit_price'] ?></td>
        <td><?php echo $row['amount'] ?></td>
    </tr>
    <?php
  $tot_amount +=$row['amount'];

  }
  ?>
    <tr>
      <td colspan="11">合计</td>
      <td><?php echo number_format($tot_amount,2,'.',''); ?></td>
    </tr>
    <tr>
      <td colspan="19">
        <input type="button" class="button" name="" value="确定" onclick="window.location.assign('material_balance_account_do.php?accountid=<?php echo $accountid; ?>&action=complete')">
        &nbsp;
        <input type="button" class="button" name="" value="退回" onclick="window.location.assign('material_balance_account_do.php?accountid=<?php echo $accountid; ?>&action=back')">
        &nbsp;
        <input type="button" class="button" onclick="window.history.go(-1)" value="返回">
      </td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无对账信息</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>