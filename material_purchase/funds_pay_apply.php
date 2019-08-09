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
<div id="table_list">
<h4 style="height: 30px;line-height: 30px;margin-bottom: 5px;font-size: 14px;
    padding-left: 32px;background:#ddd">计划详情</h4>
<?php
  //查找当前计划单下面的所有计划内容
  $plan_list_sql = "SELECT * FROM `db_funds_plan_list` WHERE `planid` = '$planid'";
  $result_list = $db->query($plan_list_sql);
  if($result_list->num_rows){
    ?>
    <table>
      <tr>
        <th>ID</th>
        <th>对账时间</th>
        <th>发票时间</th>
        <th>供应商名称</th>
        <th>对账金额</th>
        <th>计划金额</th>
        <th>付款申请单</th>
      </tr>
  <?php
    while($row_list = $result_list->fetch_assoc()){
      //判断是对账款还是预付款
      if($row_list['accountid']){
        $info_sql = "SELECT * FROM `db_material_account` INNER JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_account`.`accountid` =".$row_list['accountid'];
      } else{
        $info_sql = "SELECT * FROM `db_funds_prepayment` INNER JOIN `db_supplier` ON `db_funds_prepayment`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_funds_prepayment`.`prepayid` =".$row_list['preid'];
      }
      $result_info = $db->query($info_sql);
      if($result_info->num_rows){
        $row_info = $result_info->fetch_assoc();
          
    ?>
    <tr>
          <td>
            <input type="checkbox" value="<?php echo $row_list['listid'] ?>">
          </td>
          <td><?php echo $row_info['account_time'] ?></td>
          <td><?php echo $row_info['date'] ?></td>
          <td><?php echo $row_info['supplier_cname']?></td>
          <td>
            <?php 
              $account_amount =  $row_info['tot_amount'] + $row_info['tot_process_cost'] - $row_info['tot_cancel_amount'] - $row_info['tot_cut_payment'] - $row_info['tot_prepayment'];
              echo number_format($account_amount,2,'.','');
            ?>    
          </td>
          <td>
            <?php 
              $plan_amount = $row_info['prepayment']?$row_info['prepayment']:$row_list['plan_amount'];
              echo number_format($plan_amount,2,'.','');
            ?>
          </td>
          <td>
            <a href="funds_plan_list_print.php?id=<?php echo $row_list['listid'] ?>">打印</a>
          </td>
      </tr> 
      
  
<?php
    }
  }
?>
  <tr>
        <td colspan="7">
        <?php 
          if($action == 'approval'){ ?>
          <input type="button" name="" class="button" value="通过" onclick="location.href='funds_plan_approval_do.php?action=approval&planid=<?php echo $planid ?>'" >
          <input type="button" name="" class="button" value="退回" onclick="location.href='funds_plan_approval_do.php?action=back&planid=<?php echo $planid ?>'" >
          <?php } ?>
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />

        </td>
      </tr>
  </table>

<?php
 }
?>
</div>


<?php include "../footer.php"; ?>
</body>
</html>