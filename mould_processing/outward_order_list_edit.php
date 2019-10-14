<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$listid = $_GET['listid'];
$orderid = trim($_GET['orderid']);
$sql = "SELECT `db_outward_order_list`.`listid`,`db_outward_inquiry`.`outward_remark`,`db_outward_order_list`.`unit_price`,(`db_outward_inquiry`.`outward_quantity` * `db_outward_order_list`.`unit_price`) AS `amount`,`db_mould_specification`.`mould_no`,`db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code`,`db_outward_inquiry`.`outward_quantity`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_order_list` INNER JOIN `db_outward_inquiry` ON `db_outward_order_list`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` INNER JOIN `db_mould_outward_type` ON `db_outward_inquiry`.`outward_typeid` = `db_mould_outward_type`.`outward_typeid` INNER JOIN `db_mould_specification` ON `db_mould_material`.`mouldid` = `db_mould_specification`.`mould_specification_id` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry`.`employeeid` WHERE `db_outward_order_list`.`listid` = '$listid'";
$result = $db->query($sql);

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
  <?php if($action == "edit"){ ?>
  <script type="text/javascript">
    $(function(){
      $('#submit').live('click',function(){
          var unit_price = $("#unit_price").val();
          if(!unit_price){
            $("#unit_price").focus();
            return false;
          }
      })
    })
  </script>
  <h4>更改外协合同详情</h4>
  <?php if($result->num_rows){ 
    $row = $result->fetch_assoc();
  ?>

  <form action="outward_inquiry_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">物料名称：</th>
        <td width="80%"><?php echo $row['material_name'] ?></td>
      </tr>
      <tr>
        <th>规格：</th>
        <td><?php echo $row['specification'] ?></td>
      </tr>
      <tr>
        <th>加工类型：</th>
        <td><?php echo $row['outward_typename'] ?></td>
      </tr>
      <tr>
        <th>加工数量：</th>
        <td><?php echo $row['outward_quantity'] ?></td>
      </tr>
      <tr>
        <th>单价：</th>
        <td>
          <input type="text" name="unit_price" id="unit_price" value="<?php echo $row['unit_price'] ?>" class="input_txt" />
          <input type="hidden" name="listid" value="<?php echo $row['listid'] ?>" />
          <input type="hidden" name="orderid" value="<?php echo $orderid ?>" />
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="approval"/>
        </td>
      </tr>
    </table>
  </form>
  <?php
  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>