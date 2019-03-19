<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$purchaseid = fun_check_int($_GET['purchaseid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_purchase = "SELECT `db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_cutter_purchase`.`purchase_time`,`db_employee`.`employee_name` FROM `db_cutter_purchase` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_purchase`.`employeeid` WHERE `db_cutter_purchase`.`purchaseid` = '$purchaseid' AND `db_cutter_purchase`.`employeeid` = '$employeeid'";
$result_purchase = $db->query($sql_purchase);
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
if($result_purchase->num_rows){
	$array_purchase = $result_purchase->fetch_assoc();
	//类型
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
?>
<div id="table_sheet">
  <h4>刀具申购单</h4>
  <table>
    <tr>
      <th width="10%">申购单号：</th>
      <td width="15%"><?php echo $array_purchase['purchase_number']; ?></td>
      <th width="10%">申购人：</th>
      <td width="15%"><?php echo $array_purchase['employee_name']; ?></td>
      <th width="10%">申购日期：</th>
      <td width="15%"><?php echo $array_purchase['purchase_date']; ?></td>
      <th width="10%">操作时间：</th>
      <td width="15%"><?php echo $array_purchase['purchase_time']; ?></td>
    </tr>
  </table>
</div>
<?php
if($_GET['submit']){
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$specification = trim($_GET['specification']);
	$texture = $_GET['texture'];
	if($texture){
		$sql_texture = " AND `db_cutter_hardness`.`texture` = '$texture'";
	}
	$hardness = trim($_GET['hardness']);
	$sqlwhere = " AND `db_cutter_specification`.`specification` LIKE '%$specification%' AND `db_cutter_hardness`.`hardness` LIKE '%$hardness%' $sql_typeid $sql_texture";
}
$sql_purchase_list = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,`db_cutter_order_list`.`listid`,`db_cutter_order_list`.`in_quantity` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_purchase_list`.`supplierid` LEFT JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`purchase_listid` = `db_cutter_purchase_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`purchaseid` = '$purchaseid' AND `db_cutter_purchase`.`employeeid` = '$employeeid' $sqlwhere ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_hardness`.`texture` ASC,`db_mould_cutter`.`cutterid` DESC";
$result_paurchase_list = $db->query($sql_purchase_list);
?>
<div id="table_search">
  <h4>申购明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>材质：</th>
        <td><select name="texture">
            <option value="">所有</option>
            <?php foreach($array_cutter_texture as $texture_key=>$texture_value){ ?>
            <option value="<?php echo $texture_key; ?>"<?php if($texture_key == $texture) echo " selected=\"selected\"" ?>><?php echo $texture_value; ?></option>
            <?php } ?>
          </select></td>
        <th>硬度：</th>
        <td><input type="text" name="hardness" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_purchase_list.php?purchaseid=<?php echo $purchaseid; ?>'" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_purchase.php?id=<?php echo $purchaseid; ?>'" />
          <input type="hidden" name="purchaseid" value="<?php echo $purchaseid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_paurchase_list->num_rows){ ?>
  <form action="cutter_purchase_listdo.php" name="cutter_purchase_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="7%">类型</th>
        <th width="11%">规格</th>
        <th width="7%">材质</th>
        <th width="11%">硬度</th>
        <th width="7%">品牌</th>
        <th width="7%">供应商</th>
        <th width="5%">申购数量</th>
        <th width="5%">入库数量</th>
        <th width="4%">单位</th>
        <th width="8%">计划回厂日期</th>
        <th width="6%">状态</th>
        <th width="14%">备注</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row_purchase_list = $result_paurchase_list->fetch_assoc()){
		  $purchase_listid = $row_purchase_list['purchase_listid'];
		  $listid = $row_purchase_list['listid'];
		  $cutter_order_status = ($listid)?'已下单':'未下单';
		  $in_quantity = ($listid)?$row_purchase_list['in_quantity']:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $purchase_listid; ?>"<?php if($listid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_purchase_list['type']; ?></td>
        <td><?php echo $row_purchase_list['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row_purchase_list['texture']]; ?></td>
        <td><?php echo $row_purchase_list['hardness']; ?></td>
        <td><?php echo $row_purchase_list['brand']; ?></td>
        <td><?php echo $row_purchase_list['supplier_cname']; ?></td>
        <td><?php echo $row_purchase_list['quantity']; ?></td>
        <td><?php echo $in_quantity; ?></td>
        <td>件</td>
        <td><?php echo $row_purchase_list['plan_date']; ?></td>
        <td><?php echo $cutter_order_status; ?></td>
        <td><?php echo $row_purchase_list['remark']; ?></td>
        <td><?php if($listid == NULL){ ?>
          <a href="cutter_purchase_list_edit.php?purchase_listid=<?php echo $purchase_listid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无刀具明细！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>