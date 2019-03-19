<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	if($mould_number){
		$sql_mould_number = " AND `db_mould`.`mould_number` LIKE '%$mould_number%'";
	}
	$order_number = trim($_GET['order_number']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_mould_outward`.`supplierid` = '$supplierid'";
	}
	$sqlwhere = " AND `db_mould_outward`.`order_number` LIKE '%$order_number%' $sql_mould_number $sql_supplierid";
}
$sql = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,ROUND(IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0),2) AS `total_pay_amount`,((`db_mould_outward`.`cost`)-IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0)) AS `wait_pay_amount` FROM `db_mould_outward` LEFT JOIN `db_cash_pay` ON (`db_cash_pay`.`linkid` = `db_mould_outward`.`outwardid` AND `db_cash_pay`.`data_type` = 'MO') LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` WHERE `db_mould_outward`.`iscash` = 1 AND `db_mould_outward`.`outward_status` = 1 $sqlwhere GROUP BY `db_mould_outward`.`outwardid` HAVING `db_mould_outward`.`cost` > `total_pay_amount`";
$result = $db->query($sql);
$result_allid =$db->query($sql);
$_SESSION['wait_mould_outward_payment_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_outward`.`order_date` DESC,`db_mould_outward`.`outwardid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>财务管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>待付加工</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>外协单号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
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
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_wait_mould_outward_payment_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		   $all_total_amount += $row_allid['cost'];
		  $array_allid .= $row_allid['outwardid'].',';
	  }
	  $array_allid = rtrim($array_allid,',');
	  //支付金额
	  $sql_all_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_allid) AND `data_type` = 'MO' GROUP BY `linkid`";
	  $result_all_pay_amount = $db->query($sql_all_pay_amount);
	  if($result_all_pay_amount->num_rows){
		  while($row_all_pay_amount = $result_all_pay_amount->fetch_assoc()){
			  $array_all_pay_amount[$row_all_pay_amount['linkid']] = $row_all_pay_amount['total_pay_amount'];
		  }
	  }else{
		  $array_all_pay_amount = array();
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">模具编号</th>
      <th width="18%">零件编号</th>
      <th width="8%">外协时间</th>
      <th width="6%">申请组别</th>
      <th width="8%">外协单号</th>
      <th width="6%">数量</th>
      <th width="8%">供应商</th>
      <th width="6%">类型</th>
      <th width="6%">金额</th>
      <th width="6%">已付</th>
      <th width="6%">待付</th>
      <th width="6%">申请人</th>
      <th width="4%">支付</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$outwardid = $row['outwardid'];
		$mould_number = $row['mouldid']?$row['mould_number']:'--';
		$part_number = strlen_sub($row['part_number'],38,38);
		$part_number_title = (mb_strlen($row['part_number'],'utf8')>38)?" title=\"".$row['part_number']."\"":'';
	?>
    <tr>
      <td><?php echo $outwardid; ?></td>
      <td><?php echo $mould_number; ?></td>
      <td<?php echo $part_number_title; ?>><?php echo $part_number; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['workteam_name']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['outward_typename']; ?></td>
      <td><?php echo $row['cost']; ?></td>
      <td><?php echo $row['total_pay_amount']; ?></td>
      <td><?php echo $row['wait_pay_amount']; ?></td>
      <td><?php echo $row['applyer']; ?></td>
      <td><a href="pay_mould_outward_list.php?id=<?php echo $outwardid; ?>&action=add"><img src="../images/system_ico/pay_12_12.png" width="12" height="12" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="9">Total</td>
      <td><?php echo number_format($all_total_amount,2); ?></td>
      <td><?php echo number_format(array_sum($array_all_pay_amount),2); ?></td>
      <td><?php echo number_format(($all_total_amount-array_sum($array_all_pay_amount)),2); ?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
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