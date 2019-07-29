<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_material_account`.`supplierid` = '$supplierid'";
  }
  $sqlwhere = "$sql_supplierid";
}
// $sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'I' AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$sql = "SELECT `db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_inout`.`account_status` = 'M' AND `db_material_account`.`status` ='F' AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_inout_list_in'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_account`.`account_time` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>应付账款管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
      <!--   <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        -->
        <th>对账日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>

      
      
      <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
        <td>
          <input type="submit" name="submit" value="查询" class="button" /> 
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];
      $total_process_cost += $row_total['process_cost'];  
    }                                                                       
  ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th width="">对账时间</th>
      <th width="">发票时间</th>
      <th width="">供应商</th>
      <th width="">总金额</th>
      <th width="">未付金额</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $accountid = $row['accountid'];
    $listid = $row['listid'];
  ?>
  <form action="material_balance_account_do.php" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $accountid?>">
      </td>
      <td><?php echo $row['account_time']; ?></td>
      <td><?php echo $row['date']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['amount'] - $row['apply_amount']; ?></td>
    </tr>
    <?php 
      $amount += $row['amount'];
      $no_amount += $row['amount'] - $row['apply_amount'];
    } ?>
    <tr>
      <td colspan="3">Total</td>
      <td></td>
      <td><?php echo number_format($amount,2,'.',''); ?></td>
      <td><?php echo number_format($no_amount,2,'.',''); ?></td>
    </tr>
  </table>
  <!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="审核" class="select_button" />
    </div> -->
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<!--預付款管理-->
<?php 
  //查询预付款项
  $prepayment_sql = "SELECT * FROM `db_funds_prepayment` INNER JOIN `db_supplier` ON `db_funds_prepayment`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_funds_prepayment`.`employeeid` = `db_employee`.`employeeid` WHERE `db_funds_prepayment`.`status` = '0'";
  $results = $db->query($prepayment_sql);
  if($results->num_rows){
    while($rows = $results->fetch_assoc()){
      $amounts += $rows['prepayment'];
    }
  }
  $pages = new page($results->num_rows,2);
  $prepayment_sql .= "ORDER BY `db_funds_prepayment`.`dotime` DESC".$pages->limitsql;
  $result_prepayment = $db->query($prepayment_sql);

?>
<div id="table_search">
  <h4>预付款管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
      <!--   <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        -->  
      
      <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            $result_supplier = $db->query($sql_supplier);
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
           <input type="button" name="button" value="添加预付款" class="button" onclick="location.href='funds_plan.php?action=add_prepayment'" />
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result_prepayment->num_rows){                                                                    
  ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th width="">添加时间</th>
      <th width="">供应商</th>
      <th width="">合同号</th>
      <th width="">预付金额</th>
      <th width="">操作人</th>
    </tr>
    <?php
  while($row = $result_prepayment->fetch_assoc()){
  ?>
  <form action="material_balance_account_do.php" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $row['prepayid']?>">
      </td>
      <td><?php echo $row['dotime']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['prepayment']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
    </tr>
    <?php }?>
    <tr>
      <td colspan="3">Total</td>
      <td></td>
      <td><?php echo number_format($amounts,2,'.',''); ?></td>
      <td></td>
    </tr>
  </table>
  <!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="审核" class="select_button" />
    </div> -->
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>