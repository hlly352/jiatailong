<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$tryid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould_try`.`mould_size`,`db_mould_try`.`plan_date`,`db_mould_try`.`try_date`,`db_mould_try`.`order_number`,`db_mould_try`.`supplierid`,`db_mould_try`.`unit_price`,`db_mould_try`.`cost`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`try_status`,`db_mould_try`.`approve_status`,`db_mould_try`.`remark`,`db_mould_try`.`finish_status`,`db_mould_try`.`finish_confirmor`,`db_mould_try`.`finish_confirm_time`,`db_mould_try`.`dotime`,`db_mould`.`mould_number`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_mould_try_cause`.`try_causename`,`db_supplier`.`supplier_cname`,`db_client`.`client_code`,`db_applyer`.`employee_name` AS `applyer_name`,`db_finish_confirmor`.`employee_name` AS `finish_confirmor_name` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_mould_try`.`employeeid` LEFT JOIN `db_employee` AS `db_finish_confirmor` ON `db_finish_confirmor`.`employeeid` = `db_mould_try`.`finish_confirmor` WHERE `db_mould_try`.`tryid` = '$tryid'";
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php 
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $supplier_cname = $array['supplierid']?$array['supplier_cname']:'--';
	  $order_number = $array['order_number']?$array['order_number']:'--';
	  $finish_confirmor_name = $array['finish_confirmor']?$array['finish_confirmor_name']:'--';
	  $finish_confirm_time = $array['finish_status']?$array['finish_confirm_time']:'--';
  ?>
  <h4>模具试模信息</h4>
    <table>
      <tr>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">客户代码：</th>
        <td width="15%"><?php echo $array['client_code']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array['project_name']; ?></td>
        <th width="10%">模具尺寸：</th>
        <td width="15%"><?php echo $array['mould_size']; ?></td>
      </tr>
      <tr>
        <th>穴数：</th>
        <td><?php echo $array['cavity_number']; ?></td>
        <th>难度系数：</th>
        <td><?php echo $array['difficulty_degree']; ?></td>
        <th>试模次数：</th>
        <td><?php echo $array['try_times']; ?></td>
        <th>试模原因：</th>
        <td><?php echo $array['try_causename']; ?></td>
      </tr>
      <tr>
        <th>啤机吨位(T)：</th>
        <td><?php echo $array['tonnage']; ?></td>
        <th>成型周期(S)：</th>
        <td><?php echo $array['molding_cycle']; ?></td>
        <th>胶料品种：</th>
        <td><?php echo $array['plastic_material']; ?></td>
        <th>胶料颜色：</th>
        <td><?php echo $array['plastic_material_color']; ?></td>
      </tr>
      <tr>
        <th>胶料来源：</th>
        <td><?php echo $array['plastic_material_offer']; ?></td>
        <th>产品单重(g)：</th>
        <td><?php echo $array['product_weight']; ?></td>
        <th>样板啤数(啤)：</th>
        <td><?php echo $array['product_quantity']; ?></td>
        <th>需要用料(kg)：</th>
        <td><?php echo $array['material_weight']; ?></td>
      </tr>
      <tr>
        <th>要求日期：</th>
        <td><?php echo $array['plan_date']; ?></td>
        <th>试模日期：</th>
        <td><?php echo $array['try_date']; ?></td>
        <th>钳工组别：</th>
        <td><?php echo $array_mould_assembler[$array['assembler']]; ?></td>
        <th>备注：</th>
        <td><?php echo $array['remark']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><?php echo $supplier_cname; ?></td>
        <th>送货单号：</th>
        <td><?php echo $order_number; ?></td>
        <th>含税单价(元)：</th>
        <td><?php echo $array['unit_price']; ?></td>
        <th>金额(元)：</th>
        <td><?php echo $array['cost']; ?></td>
      </tr>
      <tr>
        <th>试模状态：</th>
        <td><?php echo $array_status[$array['try_status']]; ?></td>
        <th>完成状态：</th>
        <td><?php echo $array_finish_status[$array['finish_status']]; ?></td>
        <th>完成确认人：</th>
        <td><?php echo $finish_confirmor_name; ?></td>
        <th>完成确认时间：</th>
        <td><?php echo $finish_confirm_time; ?></td>
      </tr>
      <tr>
        <th>审批状态：</th>
        <td><?php echo $array_office_approve_status[$array['approve_status']]; ?></td>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
        <th>申请时间：</th>
        <td colspan="3"><?php echo $array['dotime']; ?></td>
      </tr>
    </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$tryid' AND `db_office_approve`.`approve_type` = 'MT' ORDER BY `db_office_approve`.`approveid` DESC";
$result_approve = $db->query($sql_approve);
?>
<div id="table_list">
  <?php if($result_approve->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">审批人</th>
      <th>审批意见</th>
      <th width="10%">审批状态</th>
      <th width="10%">审批时间</th>
    </tr>
    <?php while($row_approve = $result_approve->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_approve['approveid']; ?></td>
      <td><?php echo $row_approve['employee_name']; ?></td>
      <td><?php echo $row_approve['approve_content']; ?></td>
      <td><?php echo $array_office_approve_status[$row_approve['approve_status']]; ?></td>
      <td><?php echo $row_approve['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无审批记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>