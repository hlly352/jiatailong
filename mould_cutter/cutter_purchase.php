<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$purchase_number = trim($_GET['purchase_number']);
	$sqlwhere = "WHERE `db_cutter_purchase`.`purchase_number` LIKE '%$purchase_number%'";
}
$sql = "SELECT `db_cutter_purchase`.`purchaseid`,`db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_cutter_purchase`.`purchase_time`,`db_cutter_purchase`.`employeeid`,`db_employee`.`employee_name` FROM `db_cutter_purchase` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_purchase`.`employeeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_purchase`.`purchaseid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
<div id="table_search">
  <h4>刀具申购</h4>
  <form action="" name="mould_cutter_purchase" method="get">
    <table>
      <tr>
        <th>申购单号：</th>
        <td><input type="text" name="purchase_number" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="申购" class="button" onclick="location.href='cutter_purchase_create.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_purchaseid .= $row_id['purchaseid'].',';
 	  }
	  $array_purchaseid = rtrim($array_purchaseid,',');
	  //统计项数
	  $sql_list = "SELECT `purchaseid`,COUNT(*) AS `count` FROM `db_cutter_purchase_list` WHERE `purchaseid` IN ($array_purchaseid) GROUP BY `purchaseid`";
	  $result_list = $db->query($sql_list);
	  if($result_list->num_rows){
		  while($row_list = $result_list->fetch_assoc()){
			  $array_list[$row_list['purchaseid']] = $row_list['count'];
		  }
	  }else{
		  $array_list = array();
	  }
	  //统计申购单明细是否被下订单
	  $sql_order = "SELECT `db_cutter_purchase_list`.`purchaseid` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` GROUP BY `db_cutter_purchase_list`.`purchaseid`";
	  $result_order = $db->query($sql_order);
	  if($result_order->num_rows){
		  while($row_order = $result_order->fetch_assoc()){
			  $array_order[] = $row_order['purchaseid'];
		  }
	  }else{
		  $array_order = array();
	  }
  ?>
  <form action="cutter_purchasedo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="15%">申购单号</th>
        <th width="15%">申购人</th>
        <th width="15%">申购日期</th>
        <th width="25%">操作时间</th>
        <th width="10%">项数</th>
        <th width="4%">Add</th>
        <th width="4%">list</th>
        <th width="4%">Excel</th>
        <th width="4%">Print</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $purchaseid = $row['purchaseid'];
		  $list_count = array_key_exists($purchaseid,$array_list)?$array_list[$purchaseid]:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $purchaseid; ?>"<?php if(in_array($purchaseid,$array_order) || $row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['purchase_number']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['purchase_date']; ?></td>
        <td><?php echo $row['purchase_time']; ?></td>
        <td><?php echo $list_count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?>
          <a href="cutter_purchase_list.php?purchaseid=<?php echo $purchaseid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" />
          <?php } ?>
          </a></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_purchase_list_info.php?purchaseid=<?php echo $purchaseid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><a href="excel_cutter_purchase.php?id=<?php echo $purchaseid; ?>"><img src="../images/system_ico/excel_10_10.png" width="10" height="10" /></a></td>
        <td><a href="cutter_purchase_print.php?id=<?php echo $purchaseid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>