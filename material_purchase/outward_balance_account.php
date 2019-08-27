<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
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
<title>采购管理-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT `db_outward_order`.`order_number`,`db_outward_order_list`.`listid`,`db_outward_order_list`.`order_quantity`,`db_outward_order_list`.`unit_price`,`db_outward_order_list`.`amount`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code`,`db_supplier`.`supplier_cname` FROM `db_outward_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_outward_order` ON `db_outward_order`.`orderid` = `db_outward_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_outward_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_outward_order`.`material_control` = 'Y' AND `db_outward_order_list`.`listid` NOT IN(SELECT `inoutid` FROM `db_material_account_list`) $sqlwhere ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC";

$result = $db->query($sql);
$result_id = $db->query($sql);
?>
<div id="table_search">
  <h4>外协加工对账</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td>
          <input type="hidden" name="id" value="<?php echo $orderid; ?>" />
          <input type="submit" name="submit" value="查询" class="button" />
        </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
 ?>
  <form action="outward_balance_account_do.php" name="material_order_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">合同号</th>
        <th width="">模具编号</th>
        <th width="">料单编号</th>
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">材质</th>
        <th width="">供应商</th>
        <th width="">数量</th>
        <th width="">加工数量</th>
        <th width="">单价</th>
        <th width="">金额</th>
        <th width="">操作</th>
      </tr>
      <?php
	  $amount = 0;
	  $process_cost = 0;
	  $total_amount = 0;
      while($row = $result->fetch_assoc()){
        $listid = $row['listid'];
	  ?>
      <tr>
        <td>
          <input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $row['specification'] ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['order_quantity'] ?></td>
        <td><?php echo $row['unit_price'] ?></td>
        <td><?php echo $row['amount'] ?></td>
        <td><a href="outward_balance_account_do.php?id=<?php echo $listid ?>">对账</a></td>
      </tr>
      <?php
      $total_order_quantity += $row['order_quantity'];
      $total_amount += $row['amount'];
	  }
	  ?>
      <tr>
        <td colspan="10">Total</td>
        <td><?php echo number_format($total_order_quantity,2); ?></td>
        <td></td>
        <td><?php echo number_format($total_amount,2); ?></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="14">
          <input type="button" onclick="window.history.go(-1)" value="返回" class="button" />
        </td>
      </tr>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="对账" class="select_button" />
      <input type="hidden" value="del_list" name="action" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>