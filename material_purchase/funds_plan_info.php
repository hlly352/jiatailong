<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = intval($_GET['id']);
$planid = fun_check_int($planid);
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_GET['action'];

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
  <?php
  $sql_plan = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `planid`= $planid";
  $result_plan = $db->query($sql_plan);
  if($result_plan->num_rows){
    $array_plan = $result_plan->fetch_assoc();
    $plan_date = $array_plan['plan_date'];
  ?>
  <h4>付款计划</h4>
  <table>
    <tr style="font-size:14px">
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
<div id="table_list">
<?php
  //查找当前计划单下面的所有计划内容
  $plan_list_sql = "SELECT SUM(`db_funds_plan_list`.`plan_amount`) AS `plan_amount`,`db_account_order_list`.`accountid`,`db_material_account`.`account_number`,`db_supplier`.`supplier_cname`,`db_material_account`.`account_time`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) AS `total_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` INNER JOIN `db_material_account` ON `db_account_order_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_account`.`supplierid` WHERE `db_funds_plan_list`.`planid` = '$planid' GROUP BY `db_material_account`.`accountid`";
  $result_list = $db->query($plan_list_sql);
  if($result_list->num_rows){
    ?>
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
           <th>操作</th>
        </tr>
    <?php
      while($row_list = $result_list->fetch_assoc()){
        //计算总计
        $total_tot_amount += $row_list['total_amount'];
        $total_plan_amount += $row_list['plan_amount'];
        $accountid = $row_list['accountid'];
        //查找发票信息
        $invoice_sql = "SELECT `invoice_no`,`date` FROM `db_material_invoice_list` WHERE `accountid` =".$row_list['accountid'];
        $result_invoice = $db->query($invoice_sql);
    ?>
    <tr>
      <td><input type="checkbox" value="<?php echo $row_list['accountid'] ?>" /></td>
      <td><?php echo $row_list['supplier_cname'] ?></td>
      <td><?php echo $row_list['account_number'] ?></td>
      <td><?php echo $row_list['account_time'] ?></td>
      <td>
        <?php if($result_invoice->num_rows){
          while($row_invoice = $result_invoice->fetch_assoc()){
            echo $row_invoice['invoice_no'].'<br>';
          }
         } ?>
      </td>
      <td>
        <?php
          $result_invoice = $db->query($invoice_sql);
          if($result_invoice->num_rows){
          while($row_invoice = $result_invoice->fetch_assoc()){
            echo $row_invoice['date'].'<br>';
          }
         } ?>
      </td>
      <td><?php echo $row_list['total_amount'] ?></td>
      <td><?php echo $row_list['plan_amount'] ?></td>
          <td>
            <a href="funds_plan_order_info.php?action=show&method=approval&planid=<?php echo $planid ?>&accountid=<?php echo $accountid ?>">详情</a>
          </td>
      </tr> 
      
  
<?php
    }
  
?>  
  <tr>
    <td colspan="6">总计</td>
    <td><?php echo number_format($total_tot_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
    
    <td></td>
  </tr>
  <tr>
    <td colspan="15">
      <?php if($action == 'approval'){ ?>
        <input type="button" name="" value="审核" class="button" onclick="window.location.href='funds_plando.php?action=approval&method=complete&planid=<?php echo $planid ?>'" />
        <input type="button" name="" value="撤回" class="button"  onclick="window.location.href='funds_plando.php?action=approval&method=back&planid=<?php echo $planid ?>'" />
      <?php } ?>
        <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
        </td>
    </tr>
</table>
<?php
 }else{
    echo "<p class=\"tag\">系统提示：暂无付款计划！</p></div>";
  }

?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>