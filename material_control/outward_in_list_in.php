<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查找当前操作人姓名
$sql_employee = "SELECT `employee_name` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
$result_employee = $db->query($sql_employee);
if($result_employee->num_rows){
  $employee_name = $result_employee->fetch_row()[0];
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
<script type="text/javascript">
  $('#submit').live('click',function(){
    var form_number = $.trim($('#form_number').val());
    if(!form_number){
      alert('请填写送货单号');
      return false;
    }
  })
</script>
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
$sql_order = "SELECT `db_outward_order`.`orderid`,`db_outward_order`.`order_number`,`db_outward_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_outward_order`.`dotime`,`db_outward_order`.`order_status` FROM `db_outward_order` INNER JOIN `db_employee` ON `db_outward_order`.`employeeid` = `db_employee`.`employeeid` LEFT JOIN `db_outward_inquiry_order` ON `db_outward_order`.`inquiry_orderid` = `db_outward_inquiry_order`.`inquiry_orderid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid` WHERE `db_outward_order`.`order_status` = '1' AND `db_outward_order`.`orderid` = '$orderid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
    $array_order = $result_order->fetch_assoc();
  ?>
  <h4>外协加工订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
    die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $sqlwhere = " AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT `db_outward_inquiry`.`outward_remark`,`db_outward_order_list`.`unit_price`,(`db_outward_inquiry`.`outward_quantity` * `db_outward_order_list`.`unit_price`) AS `amount`,`db_mould_specification`.`mould_no`,`db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code`,`db_outward_inquiry`.`outward_quantity`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_order_list` INNER JOIN `db_outward_order` ON `db_outward_order`.`orderid` = `db_outward_order_list`.`orderid` INNER JOIN `db_outward_inquiry` ON `db_outward_order_list`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` INNER JOIN `db_mould_outward_type` ON `db_outward_inquiry`.`outward_typeid` = `db_mould_outward_type`.`outward_typeid` INNER JOIN `db_mould_specification` ON `db_mould_material`.`mouldid` = `db_mould_specification`.`mould_specification_id` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry`.`employeeid` WHERE `db_outward_order_list`.`orderid` = '$orderid' $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
?>
<div id="table_list">
  <?php
  if($result->num_rows){
 ?>
  <form action="outward_in_list_indo.php" name="material_order_list" method="post">
    <table>
      <tr>
       <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="10%">物料名称</th>
        <th width="8%">物料编码</th>
        <th width="10%">规格</th>
        <th width="6%">材质</th>
        <th width="6%">硬度</th>
        <th width="6%">品牌</th>
        <th width="6%">加工类型</th>
        <th width="6%">数量</th>
        <th width="4%">单价</th>
        <th witth="">金额</th>
        <th width="12%">备注</th>
      </tr>
      <?php
    $amount = 0;
    $total_order_quantity = 0;
    $total_amount = 0;
      while($row = $result->fetch_assoc()){
        $listid = $row['listid'];
    ?>
           <tr>
        <td>
          <input type="checkbox" name="id[]" value="<?php echo $inquiryid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_no']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td><?php echo $row['specification'] ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['outward_typename']; ?></td>
        <td><?php echo $row['outward_quantity']; ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['amount'] ?></td>
        <td><?php echo $row['outward_remark'] ?></td>
      </tr>
      <?php
      $total_outward_quantity += $row['outward_quantity'];
      $total_amount += $row['amount'];
    }
    ?>
      <tr>
        <td colspan="9">Total</td>
        <td><?php echo $total_outward_quantity; ?></td>
        <td></td>
        <td><?php echo number_format($total_amount,2); ?></td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3"></td>
        <td>送货单号</td>
        <td>
          <input type="text" id="form_number" class="input_txt" name="form_number" />
        </td>
        <td>日期</td>
        <td>
          <input type="text" name="dodate" value="<?php echo date('Y-m-d') ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
        </td>
        <td>操作人</td>
        <td>
          <input type="text" value="<?php echo $employee_name ?>" name="taker" />
        </td>
      </tr>
      <tr>
        <td colspan="14">
          <input type="submit" id="submit"  value="确定" class="button" />
          <input type="button" id="save" value="退回" class="button"/>
          <input type="hidden"  value="query" name="action" />
          <input type="hidden"  value="<?php echo $orderid ?>" name="orderid" />
          <input type="button" onclick="window.location.href='outward_in_list.php'" value="返回" class="button" />
        </td>
      </tr>
    </table>
  </form>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>