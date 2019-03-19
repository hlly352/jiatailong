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
		$sql_supplierid = " AND `db_cutter_order`.`supplierid` = '$supplierid'";
	}
	$order_status = $_GET['order_status'];
	if($order_status != NULL){
		$sql_order_status = " AND `db_cutter_order`.`order_status` = '$order_status'";
	}
	$sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_order_status";
}
$sql = "SELECT `db_cutter_order`.`orderid`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_order`.`delivery_cycle`,`db_cutter_order`.`dotime`,`db_cutter_order`.`order_status`,`db_cutter_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_cutter_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE (`db_cutter_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_order`.`order_number` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sql);
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
  <h4>刀具订单</h4>
  <form action="" name="search" method="get">
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
			?>
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $supplierid) echo " selected=\"selected\""; ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>订单状态：</th>
        <td><select name="order_status">
            <option value="">所有</option>
            <?php foreach($array_order_status as $order_status_key=>$order_status_value){ ?>
            <option value="<?php echo $order_status_key; ?>"<?php if($order_status_key == $order_status && $order_status != NULL) echo " selected=\"selected\""; ?>><?php echo $order_status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_orderae.php?action=add'" /></td>
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
	  $sql_order_list = "SELECT `orderid`,COUNT(*) AS `count` FROM `db_cutter_order_list` WHERE `orderid` IN ($array_orderid) GROUP BY `orderid`";
	  $result_order_list = $db->query($sql_order_list);
	  if($result_order_list->num_rows){
		  while($row_order_list = $result_order_list->fetch_assoc()){
			  $array_order_list[$row_order_list['orderid']] = $row_order_list['count'];
		  }
	  }else{
		  $array_order_list = array();
	  }
	  //订单是否有出入库记录
	  $sql_cutter_inout = "SELECT `db_cutter_order_list`.`orderid` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` GROUP BY `db_cutter_order_list`.`orderid`";
	  $result_cutter_inout = $db->query($sql_cutter_inout);
	  if($result_cutter_inout->num_rows){
		  while($row_cutter_inout = $result_cutter_inout->fetch_assoc()){
			  $array_cutter_inout[] = $row_cutter_inout['orderid'];
		  }
	  }else{
		  $array_cutter_inout = array();
	  }
  ?>
  <form action="cutter_orderdo.php" name="cutter_order" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="16%">合同号</th>
        <th width="10%">订单日期</th>
        <th width="10%">供应商</th>
        <th width="10%">交货周期(天)</th>
        <th width="10%">操作人</th>
        <th width="12%">操作时间</th>
        <th width="6%">项数</th>
        <th width="6%">订单状态</th>
        <th width="4%">Add</th>
        <th width="4%">Edit</th>
        <th width="4%">Excel</th>
        <th width="4%">List</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $orderid = $row['orderid'];
		  $list_count = array_key_exists($orderid,$array_order_list)?$array_order_list[$orderid]:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $orderid; ?>"<?php if(in_array($orderid,$array_cutter_inout) || $employeeid != $row['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['delivery_cycle']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $list_count; ?></td>
        <td><?php echo $array_order_status[$row['order_status']]; ?></td>
        <td><?php if($employeeid == $row['employeeid']){ ?>
          <a href="cutter_order_list_add.php?orderid=<?php echo $orderid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><?php if(!in_array($orderid,$array_cutter_inout) && $employeeid == $row['employeeid']){ ?>
          <a href="cutter_orderae.php?orderid=<?php echo $orderid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><?php if($employeeid == $row['employeeid'] && $list_count){ ?>
          <a href="excel_cutter_order.php?orderid=<?php echo $orderid; ?>"><img src="../images/system_ico/excel_10_10.png" width="10" height="10" />
          <?php } ?>
          </a></td>
        <td><?php if($employeeid == $row['employeeid'] && $list_count){ ?>
          <a href="cutter_order_list.php?orderid=<?php echo $orderid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a>
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