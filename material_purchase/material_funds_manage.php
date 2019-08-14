<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = $_GET['id'];
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
        <td><select name="data_source" id="data_source" class="input_txt txt">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付帐款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
//$data_source = $_GET['data_source']?trim($_GET['data_source']):'B';


  $order_list_sql = "SELECT `db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time` FROM `db_material_account` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` WHERE `db_material_account`.`status` = 'P' GROUP BY `db_material_account`.`accountid`";

  $result = $db->query($order_list_sql);
  $pages = new page($result->num_rows,5);
  $sqllist = $order_list_sql . " ORDER BY `db_material_account`.`accountid` DESC" . $pages->limitsql;
  $result_order_list = $db->query($sqllist);
  if($result_order_list->num_rows){ ?>
    <div id="table_list">
        <table>
          <tr>
            <th>ID</th>
            <th>对账时间</th>
            <th>合同号</th>
            <th>供应商</th>
            <th>物料金额</th>
            <th>核销金额</th>
            <th>品质扣款</th>
            <th>加工费</th>
            <th>对账金额</th>
            <th>操作人</th>
          </tr>
  <?php
    while($row_order_list = $result_order_list->fetch_assoc()){
      $orderidlist = $row_order_list['orderidlist'];
      $orderid = explode(',',$orderidlist);

      $count = count($orderid);
      $accountid = $row_order_list['accountid'];
  ?>
      <tr>
        <td rowspan="<?php echo $count+1 ?>"><?php echo $row_order_list['account_number'] ?></td>
      </tr>
  <?php

      //通过对账单号在对账详情表中查找订单信息
       $sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_supplier`.`supplier_cname`,SUM(`db_material_inout`.`amount`) AS `sum`,SUM(`db_material_inout`.`cancel_amount`) AS `cancel_amount`,SUM(`db_material_inout`.`cut_payment`) AS `cut_payment`,SUM(`db_material_order_list`.`process_cost`) AS `process_cost` FROM `db_material_order` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_material_inout` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` WHERE `db_material_account_list`.`accountid` = '$accountid' AND `db_material_order`.`orderid` NOT IN(SELECT `orderid` FROM `db_funds_plan_list` GROUP BY `orderid`) GROUP BY `db_material_order`.`orderid`";
  
        $result = $db->query($sql);
        if($result->num_rows){
          while($row_list = $result->fetch_assoc()){

      ?>
        <tr>
          <td>
            <?php echo $row_list['order_number'] ?></td>
        </tr>
    <?php  
          }
        }
   }?>
    </table>
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




<?php include "../footer.php"; ?>
</body>
</html>