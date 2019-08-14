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
  $plan_list_sql = "SELECT `db_material_order`.`supplierid`,`db_funds_plan_list`.`listid`,`db_funds_plan_list`.`order_amount`,`db_funds_plan_list`.`process_cost`,`db_funds_plan_list`.`accountid`,`db_funds_plan_list`.`cancel_amount`,`db_funds_plan_list`.`cut_payment`,`db_funds_plan_list`.`plan_amount`,`db_supplier`.`supplier_cname`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`supplierid` FROM `db_funds_plan_list` INNER JOIN `db_material_order` ON `db_funds_plan_list`.`orderid` = `db_material_order`.`orderid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `planid` = '$planid'  ORDER BY `db_material_order`.`supplierid`";
  $result_list = $db->query($plan_list_sql);
  if($result_list->num_rows){
    ?>
    <table>
        <tr>
          <th>ID</th>
          <th>对账时间</th>
          <th>合同号</th>
          <th>供应商</th>
          <th>物料金额</th>
          <th>加工费</th>
          <th>核销金额</th>
          <th>品质扣款</th>
          <th>对账金额</th>
          <th>计划金额</th>
          <th>小计</th>
        </tr>
  <?php
    while($row_list = $result_list->fetch_assoc()){
      $total_order_amount += $row_list['order_amount'];
      $total_process_cost += $row_list['process_cost'];
      $total_cancel_amount += $row_list['cancel_amount'];
      $total_cut_payment += $row_list['cut_payment'];
      $total_account_amount += $row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'];
      $total_plan_amount += $row_list['plan_amount'];
      $total_sql = "SELECT SUM(`db_funds_plan_list`.`plan_amount`) AS `total`,COUNT(`db_funds_plan_list`.`listid`) AS `count` FROM `db_funds_plan_list` INNER JOIN `db_material_order` ON `db_funds_plan_list`.`orderid` = `db_material_order`.`orderid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_material_order`.`supplierid` = ".$row_list['supplierid']." GROUP BY `db_material_order`.`supplierid`"; 
      $result_total = $db->query($total_sql);
      if($result_total->num_rows){
        $total = array();
        while($row_total = $result_total->fetch_assoc()){
          $total[$row_list['supplierid']] = $row_total;
        }
        
      }
    
      // //判断是对账款还是预付款
      // if($row_list['accountid']){
      //  $info_sql = "SELECT * FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_account`.`accountid` =".$row_list['accountid'];
      // echo $info_sql.'<br>';
      // $result_info = $db->query($info_sql);
      // if($result_info->num_rows){
      //  $row_info = $result_info->fetch_assoc();
      // } else{
      //  //$info_sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_material_order`.`order_amount` AS `sum`,`db_material_order`.`prepayment` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`orderid` =".$row_list['orderid'];
        
      //}
          
    ?>
    <tr>
          <td>
            <input type="checkbox" value="<?php echo $row_list['listid'] ?>">
          </td>
          <td><?php echo $row_list['order_date'] ?></td>
          <td><?php echo $row_list['order_number'] ?></td>
          <td><?php echo $row_list['supplier_cname']?></td>
          <td><?php echo $row_list['order_amount']?></td>
          <td><?php echo $row_list['process_cost'] ?></td>
          <td><?php echo $row_list['cancel_amount'] ?></td>
          <td><?php echo $row_list['cut_payment'] ?></td>
          <td>
            <?php 
              $account_amount = $row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'];
              echo number_format($account_amount,2,'.','');
            ?>    
          </td>
          <td><?php echo $row_list['plan_amount'] ?></td>
          <?php
          
           ?>

          <td class="count count_<?php echo $row_list['supplierid'] ?>"><?php echo $total[$row_list['supplierid']]['total'] ?></td>
          <?php 
          ?>
      </tr> 
      
  
<?php
    }
  
?>  
  <tr>
    <td colspan="4">总计</td>
    <td><?php echo number_format($total_order_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_process_cost,2,'.','') ?></td>
    <td><?php echo number_format($total_cancel_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_cut_payment,2,'.','') ?></td>
    <td><?php echo number_format($total_account_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
  </tr>
  <tr>
        <td colspan="11">
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