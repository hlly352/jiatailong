<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$planid = $_GET['planid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));

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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_plan = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `planid`= $planid";
  $result_plan = $db->query($sql_plan);
  if($result_plan->num_rows){
    $array_plan = $result_plan->fetch_assoc();
    $plan_date = $array_plan['plan_date'];
  ?>
  <h4>付款计划</h4>
  <table>
    <tr>
      <th width="10%">付款单号：</th>
      <td width="15%"><?php echo $array_plan['plan_number']; ?></td>
      <th width="10%">计划日期：</th>
      <td width="15%"><?php echo $array_plan['plan_date']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_plan['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }
  ?>
</div>
<?php

  $sql = "SELECT `db_funds_plan_list`.`plan_amount`,`db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_funds_plan_list` ON `db_material_account`.`accountid` = `db_funds_plan_list`.`accountid` WHERE `db_material_inout`.`account_status` = 'M' AND `db_funds_plan_list`.`planid` = '$planid' AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_material_account`.`account_time` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>计划付款</h4>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="funds_plando.php" name="material_list" method="post">
    <table>
      <tr>
        <th>ID</th>
        <th>对账时间</th>
        <th>发票时间</th>
        <th>供应商名称</th>
        <th>对账金额</th>
        <th width="17%">计划金额</th>
        <th width="20%">发票号</th>
      </tr>

      <?php
      while($row = $result->fetch_assoc()){
        //查询对应的发票号
        $invoice_sql = "SELECT `invoice_no` FROM `db_material_invoice_list` WHERE `accountid`=".$row['accountid'];
        $result_invoice = $db->query($invoice_sql);

    ?>
      <tr>
        <td>
          <input type="checkbox" class="accountid" value="<?php echo $row['accountid'] ?>">
          <input type="hidden" name="accountid[]" value="<?php echo $row['accountid'] ?>">
        </td>
        <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
        <td><?php echo $row['account_time'] ?></td>
        <td><?php echo $row['date'] ?></td>
        <td><?php echo $row['supplier_cname'] ?></td>
        <td class="amount" id="amount-<?php echo $row['accountid'] ?>"><?php echo number_format($row['amount'],2,'.','') ?></td>
        <td>
          <?php echo number_format($row['plan_amount'],2,'.','') ?>
        </td>
        <td>
          <?php
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo ' PO:'.$row_invoice['invoice_no'];
              }
            }
          ?>
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="15">
          <input type="button" name="submit" class="button" value="通过" onclick="location.assign('funds_plan_approval_do.php?action=complete&planid=<?php echo $_GET['planid'] ?>')">
          <input type="button" class="button" onclick="location.assign('funds_plan_approval_do.php?action=back&planid=<?php echo $_GET['planid'] ?>')" value="退回">
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />

        </td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>