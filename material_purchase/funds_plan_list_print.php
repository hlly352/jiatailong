<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/toSuperCase.php';
require_once 'shell.php';
$accountid = fun_check_int($_GET['accountid']);
$planid = $_GET['planid'];
$toSuperCase = new Num2Cny();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
/*Base_css*/
body, html {
  height:100%;
}
* {
  margin:0;
  padding:0;
  font-family:"微软雅黑", "宋体";
}
#main {
  width:1040px;
  height:662px;
  margin:0 auto;
}
#sheet {
  border-collapse:collapse;
  width:100%;
  margin-top:20px;
}
#sheet th, #sheet td {
  border:1px solid #000;
  font-size:13px;
  text-align:center;
  padding:8px 0;
  word-break:break-all;
  word-wrap:break-all;
}
</style>
<title>付款申请单-嘉泰隆</title>
</head>

<body>
<?php

     $sql = "SELECT `db_funds_plan_list`.`listid` AS `plan_listid`,`db_material_invoice_list`.`invoice_no`,`db_material_account`.`account_number`,`db_material_account`.`account_type`,`db_account_order_list`.`listid`,`db_account_order_list`.`order_number`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) AS `account_amount`,`db_funds_plan_list`.`plan_amount`,`db_supplier`.`supplier_name`,`db_supplier`.`supplier_blank`,`db_supplier`.`supplier_account` FROM `db_account_order_list` INNER JOIN `db_material_account` ON `db_material_account`.`accountid` = `db_account_order_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` LEFT JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_funds_plan_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_material_account`.`accountid` = '$accountid' AND `db_funds_plan_list`.`planid` = '$planid' AND `db_funds_plan_list`.`plan_status` = 'A' GROUP BY `db_funds_plan_list`.`listid`";

   // $sql = "SELECT `db_funds_plan_list`.`planid`,`db_material_account`.`accountid`,`db_funds_plan_list`.`plan_amount`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `account_amount`,`db_material_funds_plan`.`plan_number`,`db_supplier`.`supplier_name`,`db_material_account`.`account_type`,`db_material_account`.`account_number` FROM `db_material_funds_plan` INNER JOIN `db_funds_plan_list` ON `db_material_funds_plan`.`planid` = `db_funds_plan_list`.`planid` INNER JOIN `db_account_order_list` ON `db_account_order_list`.`listid` = `db_funds_plan_list`.`order_listid` INNER JOIN `db_material_account` ON `db_account_order_list`.`accountid` = `db_account_order_list`.`accountid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_account`.`supplierid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_material_account`.`accountid` = '$accountid'";
    //$sql = "SELECT  `db_funds_plan_list`.`planid`,`db_material_account`.`accountid` FROM `db_material_funds_plan` INNER JOIN `db_funds_plan_list` ON `db_material_funds_plan`.`planid` = `db_funds_plan_list`.`planid` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid`  INNER JOIN `db_material_account` ON `db_account_order_list`.`accountid` = `db_account_order_list`.`accountid` WHERE `db_material_account`.`accountid` = '$accountid' AND `db_funds_plan_list`.`planid` = '$planid' ";

      $plan_sql = "SELECT `plan_number` FROM `db_material_funds_plan` WHERE `planid` = '$planid'";
      $result_plan = $db->query($plan_sql);
  $result = $db->query($sql);
  $result_amount = $db->query($sql);
  if($count = $result->num_rows){
    $toal_page = ceil($count/10);
    $page = 1;
    $total_amount = 0;
    while($row_amount = $result_amount->fetch_assoc()){
      $order_number[] = $row_amount['order_number'];
      $plan[] = $row_amount['plan_amount'];
      $invoice_no[] = $row_amount['invoice_no'];
      $plan_amount += $row_amount['plan_amount'];
      $total_amount = $row_amount['account_amount'];
      $plan_listid[] = $row_amount['plan_listid'];
    }
    $plan_listid = fun_convert_checkbox($plan_listid);
    $total_amount = number_format($total_amount,2);
    //更改计划状态
     $plan_sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'B' WHERE `listid` IN($plan_listid)";
     $db->query($plan_sql);
?> 
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style=" font-size:18px; line-height:25px; margin-bottom:-15px;">
        苏州嘉泰隆实业有限公司<br />
        付款申请单
        </caption>
        <tr>
          <td colspan="2" style="border:none;box-sizing:content-box;padding-right:93px">
              申请编号：<?php 
              if($result_plan->num_rows){
                echo $result_plan->fetch_row()[0];
              }
           ?>
          </td>
          <td colspan="2" style="border:none"></td>
          <td colspan="2"  style="border:none;box-sizing:content-box;padding-right:75px">
              申请日期：<?php echo date('Y-m-d') ?>
              
          </td>
          <td style="border:none"></td>
        </tr>

        <?php
    $i = 1;
        while($row = $result->fetch_assoc()){
         
         if($i == 1){
    ?>
        <tr>
          <th>收款单位</th>
          <td colspan="3"><?php echo $row['supplier_name'] ?></td>
          <th width="17%">对账名称</th>
          <td width="16%">
            <?php if($row['account_type'] == 'M' ){
              echo '模具物料';
            }elseif($row['account_type'] == 'C'){
              echo '加工刀具';
            }elseif($row['account_type'] == 'O'){
              echo '期间物料';
            }

            ?>
          </td>
        </tr>
        <tr>
          <th>开户银行</th>
          <td colspan="3">
            <?php echo $row['supplier_blank'] ?>
          </td>
          <th>对账单号</th>
          <td><?php echo $row['account_number'] ?></td>
        </tr>
        <tr>
          <th>开户账号</th>
          <td colspan="3">
            <?php echo $row['supplier_account'] ?>
          </td>
          <th>对账金额</th>
          <td><?php echo $total_amount ?></td>
        </tr>
        <tr>
          <th>税&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号</th>
          <td colspan="3"></td>
          <th>计划金额</th>
          <td><?php echo number_format($plan_amount,2,'.','') ?></td>
        </tr>
        <tr>
          <th colspan="2">付款明细摘要</th>
          <th width="17%">付款金额</th>
          <th width="17.5%">发票号</th>
          <th>付款方式</th>
          <th>备注</th>
        </tr>
        <?php
          foreach($order_number as $k=>$v){
        ?>
          <tr>
            <td colspan="2" class="bor"><?php echo 'PO:'.$v ?></td>
            <td class="bor"><?php echo $plan[$k] ?></td>
            <?php if($k==0){ ?>
            <td rowspan="<?php echo $count ?>">
                <?php
                  $invoice_no = array_unique($invoice_no);
                  foreach($invoice_no as $v){
                    echo $v.'<br>';
                  }
                ?>
            </td>
            <td rowspan="<?php echo $count ?>">
              电汇&nbsp;<input checked type="checkbox"><br />
              现金&nbsp;<input checked type="checkbox"><br />
              支票&nbsp;<input checked type="checkbox"><br />
              承兑&nbsp;<input checked type="checkbox"><br />
              其他&nbsp;<input checked type="checkbox"><br />     
            </td>
            <td rowspan="<?php echo $count ?>">
              
            </td>
            <?php } ?>
          </tr>
        <?php } ?>
          <tr> 
            <td>合计金额</td>
            <td colspan="3" style="border-right:none;text-align:left">&yen;<?php echo number_format($plan_amount,2,'.','').'&nbsp;&nbsp;&nbsp;人民币大写：'.$toSuperCase::ParseNumber($plan_amount); ?></td>
            <td colspan="2">附件单据（&nbsp;&nbsp; ）张</td>
          </tr>
          <tr>
            <td style="border:none;text-align:left">经办人</td>
            <td style="border:none;text-align:left">部门经理<br>采购经理</td>
            <td style="border:none;text-align:left">财务经理</td>
            <td style="border:none;text-align:left">总经理</td>
            <td style="border:none;text-align:left">出纳</td>
            <td style="border:none;text-align:left">会计</td>
          </tr>
        <?php $i++;} }?>
<?php } ?>
<?php
  //判断是否全部申请付款
     $plan_count_sql = "SELECT COUNT(*) FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` INNER JOIN `db_material_account` ON `db_account_order_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_account`.`supplierid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_funds_plan_list`.`plan_status` = 'A' GROUP BY `db_material_account`.`accountid`";

    $result_count = $db->query($plan_count_sql);
    $count =  $result_count->num_rows;
    if($count == 0){
      $status_sql = "UPDATE `db_material_funds_plan` SET `plan_status` = 7 WHERE `planid` = '$planid'";
      $db->query($status_sql);
    }
?>
</body>
</html>