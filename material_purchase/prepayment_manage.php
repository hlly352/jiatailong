<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$order_number = trim($_GET['order_number']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_material_order`.`supplierid` = '$supplierid'";
	}
	$order_status = $_GET['order_status'];
	if($order_status != NULL){
		$sql_order_status = " AND `db_material_order`.`order_status` = '$order_status'";
	}
	$sqlwhere = " AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_order_status";
}
$sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`delivery_cycle`,`db_material_order`.`dotime`,`db_material_order`.`order_status`,`db_material_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`pay_type` = 'P' AND `db_material_order`.`order_status` = '1' AND (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere GROUP BY `db_material_order`.`orderid`";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_order`.`order_number` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>预付款项</h4>
  <form action="" name="material_order" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>订单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <th>订单状态：</th>
        <td><select name="order_status">
            <option value="">所有</option>
            <?php
            foreach($array_order_status as $order_status_key=>$order_status_value){
				echo "<option value=\"".$order_status_key."\">".$order_status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_orderae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_orderid .= $row_id['orderid'].',';
	  }
	  $array_orderid = rtrim($array_orderid,',');
	  //订单明细数量
	  $sql_order_list = "SELECT `orderid`,COUNT(*) AS `count`,SUM(`actual_quantity` * `unit_price`) AS `sum` FROM `db_material_order_list` WHERE `orderid` IN ($array_orderid) GROUP BY `orderid`";
  
	  $result_order_list = $db->query($sql_order_list);
	  if($result_order_list->num_rows){
		  while($row_order_list = $result_order_list->fetch_assoc()){
        $array_order_list[$row_order_list['orderid']]['count'] = $row_order_list['count'];
			  $array_order_list[$row_order_list['orderid']]['sum'] = $row_order_list['sum'];
		  }
	  }else{
		  $array_order_list = array();
	  }

  ?>
  <form action="material_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="16%">合同号</th>
        <th width="10%">订单日期</th>
        <th width="10%">供应商</th>
        <th width="10%">总金额</th>
        <th width="10%">操作人</th>
        <th width="12%">操作时间</th>
        <th width="6%">项数</th>
        <th width="6%">订单状态</th>
        <th width="4%">List</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $orderid = $row['orderid'];
		  $list_count = array_key_exists($orderid,$array_order_list)?$array_order_list[$orderid]['count']:0;
      $sum = array_key_exists($orderid,$array_order_list)?$array_order_list[$orderid]['sum']:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $orderid; ?>" /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo number_format($sum,2,'.','') ?></td>
        <input type="hidden" value="<?php echo $sum ?>" name="plan_amount" />
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $list_count; ?></td>
        <td><?php echo $array_order_status[$row['order_status']]; ?></td>
       
        <td><?php if( $list_count){ ?>
          <a href="prepayment_list.php?from=material&id=<?php echo $orderid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
    </table>
   <!--  <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div> -->
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无订单记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>