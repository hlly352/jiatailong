<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = $_GET['id'];
$employeeid = $_SESSION['employee_info']['employeeid'];
//查找供应商
$supplier_sql = "SELECT `supplierid`,`supplier_cname`,`supplier_code` FROM `db_supplier` ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($supplier_sql);
if($_GET['submit']){
  $account_number = trim($_GET['account_number']);
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_material_account`.`supplierid` = '$supplierid'";
  }
  $sqlwhere = " AND `db_material_account`.`account_number` LIKE '%$account_number%' $sql_supplierid";
}

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
<title>采购管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
  <div id="table_search">
  <h4>应付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td>
          <select name="supplierid" class="input_txt txt">
            <option value="">所有</option>
            <?php

              while($row_supplier = $result_supplier->fetch_assoc()){
                echo '<option value="'.$row_supplier['supplierid'].'">'.$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname'].'</option>';
              }
            ?>  
          </select>
        <th>对账单号</th>
        <td>
          <input type="text" name="account_number" class="input_txt" />
        </td>
        <th>对账时间</th>
        <td>
          <input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
        </td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
//$data_source = $_GET['data_source']?trim($_GET['data_source']):'B';


  $order_list_sql = "SELECT `db_material_account`.`tot_plan_amount`,`db_material_account`.`account_type`,`db_material_account`.`supplierid`,`db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `total_amount`  FROM `db_material_account`INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_account`.`employeeid` WHERE `db_material_account`.`status` = 'P' AND `db_material_account`.`tot_plan_amount` < (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate') $sqlwhere GROUP BY `db_material_account`.`accountid`";

  $result = $db->query($order_list_sql);
  $pages = new page($result->num_rows,10);
  $sqllist = $order_list_sql . " ORDER BY `db_material_account`.`accountid` DESC" . $pages->limitsql;
  $result_order_list = $db->query($sqllist);
  ?>
    <div id="table_list">
      <form action="funds_plando.php" method="post">
   <?php 
  if($result_order_list->num_rows){ ?>
        <table>
          <tr>
            <th>ID</th>
            <th>供应商</th>
            <th>对账单号</th>
            <th>对账时间</th>
            <th>发票号</th>
            <th>发票时间</th>
            <th>总金额</th>
            <th>计划金额</th>
            <th>应付余额</th>
            <th>操作</th>
          </tr>
  <?php
    while($row_order_list = $result_order_list->fetch_assoc()){
      $accountid = $row_order_list['accountid'];
      $total_amount += $row_order_list['total_amount'];
      $total_plan_amount += $row_order_list['tot_plan_amount'];
      //查找发票号
      $invoice_sql = "SELECT `invoice_no`,`date` FROM `db_material_invoice_list` WHERE `accountid` = '$accountid'";
      $result_invoice = $db->query($invoice_sql);
  ?>
      <tr>
        <td>
          <input type="checkbox" value="<?php echo $row_order_list['accountid'] ?>" name="accountid[]" />
        </td>
        <td>
          <?php echo $row_order_list['supplier_cname'] ?>
        </td>
        <td>
          <?php echo $row_order_list['account_number'] ?>
            
        </td>
        <td>
          <?php echo $row_order_list['account_time'] ?>
            
        </td>
        <td>
         <?php 
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['invoice_no'].'<br>';
            }
          }
          ?>
        </td>
        <td>
          <?php
            $result_invoice = $db->query($invoice_sql);
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['date'].'<br>';
              }
            }
          ?>
        </td>
        <td>
          <?php echo $row_order_list['total_amount'] ?>
        </td>
        <td>
          <?php echo $row_order_list['tot_plan_amount'] ?>
        </td>
        <td>
          <?php echo number_format(($row_order_list['total_amount'] - $row_order_list['tot_plan_amount']),2,'.','') ?>
        </td>
        <td>
          <a href="funds_plan_order_info.php?action=funds&planid=<?php echo $planid ?>&accountid=<?php echo $row_order_list['accountid'] ?>">详情</a>
        </td>
      </tr>
      <?php } ?>
        <tr>
        <td colspan="6">合 计</td>
        <td><?php echo $total_amount ?></td>
        <td><?php echo $total_plan_amount ?></td>
        <td><?php echo $total_amount - $total_plan_amount ?></td>
        <td></td>
      </tr>
       <!--  <tr>
        <td colspan="15">
          <input type="submit" name="submit" value="添加" class="button" />
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="data_source" value="B">
          <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href = 'material_funds_plan.php'" />

        </td>
      </tr> -->
    </table>
    </form>
    </div>
   <div id="page">
    <?php $pages->getPage();?>
   </div>
    <?php
    }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
 


</table>
</div>
<?php
   if($_GET['submits']){
  $supplierid = $_GET['supplierids'];
  if($supplierid){
    $sql_supplierid = " AND `db_material_account`.`supplierid` = '$supplierid'";
  }
}
     $sql = "SELECT `db_material_account`.`tot_plan_amount`,`db_material_account`.`account_type`,`db_material_account`.`supplierid`,`db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `total_amount`  FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_account`.`employeeid` WHERE  `db_material_account`.`status` = 'Y' AND `db_material_account`.`tot_plan_amount` < (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate') $sql_supplierid GROUP BY `db_material_account`.`accountid`";

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql."ORDER BY `db_material_account`.`account_time` DESC".$pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>预付账款</h4>
   <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td>
          <select name="supplierids" class="input_txt txt">
            <option value="">所有</option>
            <?php
              $result_suppliers = $db->query($supplier_sql);
              while($row_supplier = $result_suppliers->fetch_assoc()){
                echo '<option value="'.$row_supplier['supplierid'].'">'.$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname'].'</option>';
              }
            ?>  
          </select>
        <th>对账时间</th>
        <td>
          <input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
        </td>
        <td><input type="submit" name="submits" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="funds_plando.php" name="material_list" id="account" method="post">
    <table>
     <tr>
      <th>ID</th>
        <th>供应商</th>
        <th>对账单号</th>
        <th>对账时间</th>
        <th>发票号</th>
        <th>发票时间</th>
        <th>总金额</th>
        <th>计划金额</th>
        <th>未排余额</th>
        <th>操作</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
      
  ?>
  <form action="funds_plando.php" id="account" method="post">
  <tr>
        <td>
          <input type="checkbox" value="<?php echo $row_order_list['accountid'] ?>" name="accountid[]" />
        </td>
        <td>
          <?php echo $row['supplier_cname'] ?>
        </td>
        <td></td>
        <td>
          <?php echo $row['account_time'] ?>   
        </td>
        <td></td>
        <td></td>
        <td>
          <?php echo $row['total_amount'] ?>
        </td>
        <td>
          <?php echo $row['tot_plan_amount'] ?>
        </td>
        <td>
          <?php echo number_format(($row['total_amount'] - $row['tot_plan_amount']),2,'.','') ?>
        </td>
        <td>
          <a href="funds_plan_order_info.php?action=funds&planid=<?php echo $planid ?>&accountid=<?php echo $row['accountid'] ?>">详情</a>
        </td>
      </tr>
      <?php } ?>

    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>