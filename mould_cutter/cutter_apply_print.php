<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$applyid = fun_check_int($_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>刀具申领单打印-希尔林</title>
<style>
* {
	margin:0;
	padding:0;
	list-style:none;
	text-decoration:none;
	font-family:"微软雅黑", "宋体";
}
#main {
	width:1120px;
	border-collapse:collapse;
	margin:0 auto;
}
#main td {
	border:1px solid #000;
	text-align:center;
	font-size:13px;
	height:28px;
}
</style>
</head>

<body>
<?php
$sql = "SELECT `db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_employee`.`employee_name` FROM `db_cutter_apply` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_apply`.`applyid` = '$applyid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$sql_list = "SELECT `db_cutter_apply_list`.`cutterid`,`db_cutter_apply_list`.`quantity`,`db_cutter_apply_list`.`plan_date`,`db_cutter_apply_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_mould`.`mould_number` FROM `db_cutter_apply_list` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` WHERE `db_cutter_apply_list`.`applyid` = '$applyid' ORDER BY `db_cutter_apply_list`.`apply_listid` DESC";
	$result_list = $db->query($sql_list);
	$result_id = $db->query($sql_list);
	if($result_list->num_rows){
		while($row_id = $result_id->fetch_assoc()){
			$array_cutterid .= $row_id['cutterid'].',';
		}
		$array_cutterid = rtrim($array_cutterid,',');
		$sql_surplus = "SELECT `db_cutter_purchase_list`.`cutterid`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` IN ($array_cutterid) AND `db_cutter_order_list`.`surplus` > 0 GROUP BY `db_cutter_purchase_list`.`cutterid`";
		$result_surplus = $db->query($sql_surplus);
		if($result_surplus->num_rows){
			while($row_surplus = $result_surplus->fetch_assoc()){
			$array_surplus[$row_surplus['cutterid']] = $row_surplus['surplus'];
			}
		}else{
			$array_surplus = array();
		}
		//print_r($array_surplus);
?>
<table id="main" style="background:url(../images/logo/logo_print.png) no-repeat">
  <tr>
    <td colspan="11" style="border:none; font-size:28px; font-weight:bold;">苏州希尔林机械科技有限公司</td>
  </tr>
  <tr>
    <td colspan="11" style="border:none; font-size:28px; font-weight:bold;">刀具申领单</td>
  </tr>
  <tr>
    <td colspan="6" style="border:none; font-weight:bold; text-align:left;">申领单号：<?php echo $array['apply_number']; ?></td>
    <td colspan="5" style="border:none; font-weight:bold; text-align:right;">申请日期：<?php echo $array['apply_date']; ?></td>
  </tr>
  <tr>
    <td width="5%" bgcolor="#CCCCCC"><strong>序号</strong></td>
    <td width="8%" bgcolor="#CCCCCC"><strong>类型</strong></td>
    <td width="14%" bgcolor="#CCCCCC"><strong>规格</strong></td>
    <td width="8%" bgcolor="#CCCCCC"><strong>材质</strong></td>
    <td width="11%" bgcolor="#CCCCCC"><strong>硬度</strong></td>
    <td width="6%" bgcolor="#CCCCCC"><strong>数量</strong></td>
    <td width="6%" bgcolor="#CCCCCC"><strong>库存</strong></td>
    <td width="6%" bgcolor="#CCCCCC"><strong>单位</strong></td>
    <td width="8%" bgcolor="#CCCCCC"><strong>模具编号</strong></td>
    <td width="10%" bgcolor="#CCCCCC"><strong>计划领用日期</strong></td>
    <td width="18%" bgcolor="#CCCCCC"><strong>备注</strong></td>
  </tr>
  <?php
  $a = 1;
  while($row_list = $result_list->fetch_assoc()){
	  $cutterid = $row_list['cutterid'];
	  $surplus = array_key_exists($cutterid,$array_surplus)?$array_surplus[$cutterid]:0;
  ?>
  <tr>
    <td><?php echo $a; ?></td>
    <td><?php echo $row_list['type']; ?></td>
    <td><?php echo $row_list['specification']; ?></td>
    <td><?php echo $array_cutter_texture[$row_list['texture']]; ?></td>
    <td><?php echo $row_list['hardness']; ?></td>
    <td><?php echo $row_list['quantity']; ?></td>
    <td><?php echo $surplus; ?></td>
    <td>件</td>
    <td><?php echo $row_list['mould_number']; ?></td>
    <td><?php echo $row_list['plan_date']; ?></td>
    <td><?php echo $row_list['remark']; ?></td>
  </tr>
  <?php
  $a++;
  }
  ?>
  <tr>
    <td colspan="6" style="border:none; font-weight:bold; text-align:left;">申请人：<?php echo $array['employee_name']; ?></td>
    <td colspan="5" style="border:none; font-weight:bold; text-align:left;">审核人：</td>
  </tr>
</table>
<?php
	}
}
?>
</body>
</html>