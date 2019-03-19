<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if(!$_SESSION['system_shell'][$system_dir]['isadmin']){
	die("<p style=\"font-size:14px; color:#F00;\">无权限访问 | Access Denied</p>");
}

$mouldid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould`.`project_name`,`db_mould`.`mould_number`,`db_mould`.`part_name`,`db_mould`.`plastic_material`,`db_mould`.`shrinkage_rate`,`db_mould`.`surface`,`db_mould`.`cavity_number`,`db_mould`.`gate_type`,`db_mould`.`core_material`,`db_mould`.`isexport`,`db_mould`.`quality_grade`,`db_mould`.`difficulty_degree`,`db_mould`.`first_time`,`db_mould`.`remark`,`db_mould`.`assembler`,`db_projecter`.`employee_name` AS `projecter_name`,`db_dept_projecter`.`dept_name` AS `dept_name_projecter`,`db_designer`.`employee_name` AS `designer_name`,`db_dept_designer`.`dept_name` AS `dept_name_designer`,`db_steeler`.`employee_name` AS `steeler_name`,`db_dept_steeler`.`dept_name` AS `dept_name_steeler`,`db_electroder`.`employee_name` AS `electroder_name`,`db_dept_electroder`.`dept_name` AS `dept_name_electroder`,`db_mould`.`image_filedir`,`image_filename`,`db_client`.`client_code`,`db_client`.`client_cname`,`db_mould_status`.`mould_statusname` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` LEFT JOIN `db_employee` AS `db_projecter` ON `db_projecter`.`employeeid` = `db_mould`.`projecter` LEFT JOIN `db_department` AS `db_dept_projecter` ON `db_dept_projecter`.`deptid` = `db_projecter`.`deptid` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould`.`designer` LEFT JOIN `db_department` AS `db_dept_designer` ON `db_dept_designer`.`deptid` = `db_designer`.`deptid` LEFT JOIN `db_employee` AS `db_steeler` ON `db_steeler`.`employeeid` = `db_mould`.`steeler` LEFT JOIN `db_department` AS `db_dept_steeler` ON `db_dept_steeler`.`deptid` = `db_steeler`.`deptid` LEFT JOIN `db_employee` AS `db_electroder` ON `db_electroder`.`employeeid` = `db_mould`.`electroder` LEFT JOIN `db_department` AS `db_dept_electroder` ON `db_dept_electroder`.`deptid` = `db_electroder`.`deptid` WHERE `db_mould`.`mouldid` = '$mouldid'";
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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$image_filepath = "../upload/mould_image/".$array['image_filedir'].'/B'.$array['image_filename'];
	if(is_file($image_filepath)){
		$image_file = "<img src=\"".$image_filepath."\" />";
	}else{
		$image_file = "<img src=\"../basic_data/images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
	}
?>
<div id="table_sheet">
  <h4>模具信息</h4>
  <table>
    <tr>
      <th width="12%" style="text-align:center;">零件图片</th>
      <th width="10%">客户代码：</th>
      <td width="12%"><?php echo $array['client_code'].'-'.$array['client_cname']; ?></td>
      <th width="10%">项目名称：</th>
      <td width="12%"><?php echo $array['project_name']; ?></td>
      <th width="10%">模具编号：</th>
      <td width="12%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">零件名称/编号：</th>
      <td width="12%"><?php echo $array['part_name']; ?></td>
    </tr>
    <tr>
      <th rowspan="5" style="text-align:center;"><?php echo $image_file; ?></th>
      <th>塑胶材料：</th>
      <td><?php echo $array['plastic_material']; ?></td>
      <th>缩水率：</th>
      <td><?php echo $array['shrinkage_rate']; ?></td>
      <th>表面要求：</th>
      <td><?php echo $array['surface']; ?></td>
      <th>模穴数：</th>
      <td><?php echo $array['cavity_number']; ?></td>
    </tr>
    <tr>
      <th>浇口类型：</th>
      <td><?php echo $array['gate_type']; ?></td>
      <th>型腔/型芯材质：</th>
      <td><?php echo $array['core_material']; ?></td>
      <th>是否出口：</th>
      <td><?php echo $array_is_status[$array['isexport']]; ?></td>
      <th>质量等级：</th>
      <td><?php echo $array['quality_grade']; ?></td>
    </tr>
    <tr>
      <th>难度系数：</th>
      <td><?php echo $array['difficulty_degree']; ?></td>
      <th>首板时间：</th>
      <td><?php echo $array['first_time']; ?></td>
      <th>重点提示：</th>
      <td><?php echo $array['remark']; ?></td>
      <th>目前状态：</th>
      <td><?php echo $array['mould_statusname']; ?></td>
    </tr>
    <tr>
      <th>项目：</th>
      <td><?php echo $array['dept_name_projecter'].'-'.$array['projecter_name']; ?></td>
      <th>设计：</th>
      <td><?php echo $array['dept_name_designer'].'-'.$array['designer_name']; ?></td>
      <th>钢料：</th>
      <td><?php echo $array['dept_name_steeler'].'-'.$array['steeler_name']; ?></td>
      <th>电极：</th>
      <td><?php echo $array['dept_name_electroder'].'-'.$array['electroder_name']; ?></td>
    </tr>
    <tr>
      <th>装配：</th>
      <td colspan="7"><?php echo $array_mould_assembler[$array['assembler']]; ?></td>
    </tr>
  </table>
</div>
<?php
//物料费用+加工费
$sql_material = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_mould_material`.`mouldid` = '$mouldid' AND `db_material_inout`.`dotype` = 'I' ORDER BY `db_material_order`.`orderid` DESC,`db_material_inout`.`inoutid` DESC";
$result_material = $db->query($sql_material);
?>
<div id="table_list" class="table_list">
  <?php if($result_material->num_rows){ ?>
  <table>
    <caption>
    模具物料
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">合同号</th>
      <th width="8%">物料名称</th>
      <th width="14%">规格</th>
      <th width="8%">材质</th>
      <th width="8%">表单号</th>
      <th width="6%">订单数量</th>
      <th width="4%">单位</th>
      <th width="6%">实际数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价(含税)</th>
      <th width="6%">金额(含税)</th>
      <th width="6%">加工费</th>
      <th width="6%">供应商</th>
      <th width="6%">入库日期</th>
    </tr>
    <?php while($row_material = $result_material->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_material['inoutid']; ?></td>
      <td><?php echo $row_material['order_number']; ?></td>
      <td><?php echo $row_material['material_name']; ?></td>
      <td><?php echo $row_material['specification']; ?></td>
      <td><?php echo $row_material['texture']; ?></td>
      <td><?php echo $row_material['form_number']; ?></td>
      <td><?php echo $row_material['quantity']; ?></td>
      <td><?php echo $row_material['unit_name_order']; ?></td>
      <td><?php echo $row_material['inout_quantity']; ?></td>
      <td><?php echo $row_material['unit_name_actual']; ?></td>
      <td><?php echo $row_material['unit_price']; ?></td>
      <td><?php echo $row_material['amount']; ?></td>
      <td><?php echo $row_material['process_cost']; ?></td>
      <td><?php echo $row_material['supplier_cname']; ?></td>
      <td><?php echo $row_material['dodate']; ?></td>
    </tr>
    <?php
    $total_material_amount += $row_material['amount'];
	$total_material_process_cost += $row_material['process_cost'];
	}
	?>
    <tr>
      <td colspan="11">Total</td>
      <td><?php echo number_format($total_material_amount,2); ?></td>
      <td><?php echo number_format($total_material_process_cost,2); ?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无模具物料记录！</p>";
  }
  ?>
</div>
<?php
//外协加工
$sql_outward = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename` FROM `db_mould_outward` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` WHERE `db_mould_outward`.`mouldid` = '$mouldid' AND `db_mould_outward`.`outward_status` = 1 ORDER BY `db_mould_outward`.`order_date` ASC,`db_mould_outward`.`outwardid` ASC";
$result_outward = $db->query($sql_outward);
?>
<div id="table_list" class="table_list">
  <?php if($result_outward->num_rows){ ?>
  <table>
    <caption>
    外协加工
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="16%">零件编号</th>
      <th width="8%">外协时间</th>
      <th width="8%">申请组别</th>
      <th width="8%">外协单号</th>
      <th width="6%">数量</th>
      <th width="8%">供应商</th>
      <th width="8%">类型</th>
      <th width="6%">金额</th>
      <th width="6%">申请人</th>
      <th width="8%">计划回厂</th>
      <th width="8%">实际回厂</th>
      <th width="6%">进度状态</th>
    </tr>
    <?php
	while($row_outward = $result_outward->fetch_assoc()){
		$inout_status = $row_outward['inout_status'];
		$actual_date = $inout_status?$row_outward['actual_date']:'--';
	?>
    <tr>
      <td><?php echo $row_outward['outwardid']; ?></td>
      <td><?php echo $row_outward['part_number']; ?></td>
      <td><?php echo $row_outward['order_date']; ?></td>
      <td><?php echo $row_outward['workteam_name']; ?></td>
      <td><?php echo $row_outward['order_number']; ?></td>
      <td><?php echo $row_outward['quantity']; ?></td>
      <td><?php echo $row_outward['supplier_cname']; ?></td>
      <td><?php echo $row_outward['outward_typename']; ?></td>
      <td><?php echo $row_outward['cost']; ?></td>
      <td><?php echo $row_outward['applyer']; ?></td>
      <td><?php echo $row_outward['plan_date']; ?></td>
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
    </tr>
    <?php
    $total_outward_cost += $row_outward['cost'];
    }
	?>
    <tr>
      <td colspan="8">Total</td>
      <td><?php echo number_format($total_outward_cost,2); ?></td>
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无外协加工记录！</p>";
  }
  ?>
</div>
<?php
//烧焊
$sql_weld = "SELECT `db_mould_weld`.`weldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`inout_status`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_responsibility_team`.`team_name`,`db_mould_weld_type`.`weld_typename` FROM `db_mould_weld` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_weld`.`workteamid` INNER JOIN `db_responsibility_team` ON `db_responsibility_team`.`teamid` = `db_mould_weld`.`teamid` INNER JOIN `db_mould_weld_type` ON `db_mould_weld_type`.`weld_typeid` = `db_mould_weld`.`weld_typeid` WHERE `db_mould_weld`.`mouldid` = '$mouldid' AND `db_mould_weld`.`weld_status` = 1 ORDER BY `db_mould_weld`.`order_date` ASC,`db_mould_weld`.`weldid` ASC";
$result_weld = $db->query($sql_weld);
?>
<div id="table_list">
  <?php if($result_weld->num_rows){ ?>
  <table>
    <caption>
    零件烧焊
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="14%">零件编号</th>
      <th width="6%">外发时间</th>
      <th width="8%">申请组别</th>
      <th width="6%">外协单号</th>
      <th width="6%">数量</th>
      <th width="12%">烧焊原因</th>
      <th width="6%">责任组别</th>
      <th width="8%">供应商</th>
      <th width="6%">加工类型</th>
      <th width="6%">金额</th>
      <th width="6%">申请人</th>
      <th width="6%">计划回厂</th>
      <th width="6%">回厂状态</th>
    </tr>
    <?php
	while($row_weld = $result_weld->fetch_assoc()){
	?>
    <tr>
      <td><?php echo $row_weld['weldid']; ?></td>
      <td><?php echo $row_weld['part_number']; ?></td>
      <td><?php echo $row_weld['order_date']; ?></td>
      <td><?php echo $row_weld['workteam_name']; ?></td>
      <td><?php echo $row_weld['order_number']; ?></td>
      <td><?php echo $row_weld['quantity']; ?></td>
      <td><?php echo $row_weld['weld_cause']; ?></td>
      <td><?php echo $row_weld['team_name']; ?></td>
      <td><?php echo $row_weld['supplier_cname']; ?></td>
      <td><?php echo $row_weld['weld_typename']; ?></td>
      <td><?php echo $row_weld['cost']; ?></td>
      <td><?php echo $row_weld['applyer']; ?></td>
      <td><?php echo $row_weld['plan_date']; ?></td>
      <td><?php echo $array_mould_inout_status[$row_weld['inout_status']]; ?></td>
    </tr>
    <?php
	$total_weld_cost += $row_weld['cost'];
    }
	?>
    <tr>
      <td colspan="10">Total</td>
      <td><?php echo number_format($total_weld_cost,2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无零件烧焊记录！</p>";
  }
  ?>
</div>
<?php
$sql_try = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`order_number`,`db_mould_try`.`try_date`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,CONCAT('T',`db_mould_try`.`tonnage`) AS `tonnage`,`db_mould_try`.`unit_price`,`db_mould_try`.`cost`,`db_mould_try`.`remark`,`db_mould_try`.`try_status`,`db_mould_try_cause`.`try_causename`,`db_supplier`.`supplier_cname` FROM `db_mould_try` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` WHERE `db_mould_try`.`mouldid` = '$mouldid' AND `db_mould_try`.`try_status` = 1 ORDER BY `db_mould_try`.`try_date` ASC,`db_mould_try`.`tryid` ASC";
$result_try = $db->query($sql_try);
?>
<div id="table_list">
  <?php if($result_try->num_rows){ ?>
  <table>
    <caption>
    模具试模
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">供应商</th>
      <th width="8%">送货单号</th>
      <th width="12%">试模日期</th>
      <th width="8%">试模次数</th>
      <th width="16%">试模原因</th>
      <th width="8%">啤机吨位</th>
      <th width="8%">含税单价</th>
      <th width="8%">金额</th>
      <th width="20%">备注</th>
    </tr>
    <?php while($row_try = $result_try->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_try['tryid']; ?></td>
      <td><?php echo $row_try['supplier_cname']; ?></td>
      <td><?php echo $row_try['order_number']; ?></td>
      <td><?php echo $row_try['try_date']; ?></td>
      <td><?php echo $row_try['try_times']; ?></td>
      <td><?php echo $row_try['try_causename']; ?></td>
      <td><?php echo $row_try['tonnage']; ?></td>
      <td><?php echo $row_try['unit_price']; ?></td>
      <td><?php echo $row_try['cost']; ?></td>
      <td><?php echo $row_try['remark']; ?></td>
    </tr>
    <?php
	$total_try_cost += $row_try['cost'];
    }
	?>
    <tr>
      <td colspan="8">Total</td>
      <td><?php echo number_format($total_try_cost,2); ?></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无模具试模记录！</p>";
  }
  ?>
</div>
<?php
//刀具
$sql_cutter = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_order`.`order_number`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`employeeid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_employee`.`employee_name`,`db_cutter_order_list`.`unit_price`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_inout`.`dotype` = 'O' AND `db_cutter_apply_list`.`mouldid` = '$mouldid'";
$result_cutter = $db->query($sql_cutter);
?>
<div id="table_list">
  <?php if($result_cutter->num_rows){ ?>
  <table>
    <caption>
    模具刀具
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">申领编号</th>
      <th width="8%">合同号</th>
      <th width="8%">类型</th>
      <th width="14%">规格</th>
      <th width="8%">材质</th>
      <th width="12%">硬度</th>
      <th width="5%">出库数量</th>
      <th width="5%">更换数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价(含税)</th>
      <th width="6%">金额(含税)</th>
      <th width="6%">申领人</th>
      <th width="6%">出库日期</th>
    </tr>
    <?php while($row_cutter = $result_cutter->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_cutter['inoutid']; ?></td>
      <td><?php echo $row_cutter['apply_number']; ?></td>
      <td><?php echo $row_cutter['order_number']; ?></td>
      <td><?php echo $row_cutter['type']; ?></td>
      <td><?php echo $row_cutter['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row_cutter['texture']]; ?></td>
      <td><?php echo $row_cutter['hardness']; ?></td>
      <td><?php echo $row_cutter['quantity']; ?></td>
      <td><?php echo $row_cutter['old_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row_cutter['unit_price']; ?></td>
      <td><?php echo $row_cutter['amount']; ?></td>
      <td><?php echo $row_cutter['employee_name']; ?></td>
      <td><?php echo $row_cutter['dodate']; ?></td>
    </tr>
    <?php
	$total_cutter_quantity += $row_cutter['quantity'];
	$total_cutter_amount += $row_cutter['amount'];
	}
	?>
    <tr>
      <td colspan="7">Total</td>
      <td><?php echo $total_cutter_quantity; ?></td>
      <td colspan="3">&nbsp;</td>
      <td><?php echo number_format($total_cutter_amount,2); ?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无模具刀具记录！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>