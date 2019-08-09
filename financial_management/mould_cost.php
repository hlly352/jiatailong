<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//非该项管理员无法查看本页面
if(!$_SESSION['system_shell'][$system_dir]['isadmin']){
	die("<p style=\"font-size:14px; color:#F00;\">无权限访问 | Access Denied</p>");
}
//查询模具状态
$sql_mould_status = "SELECT `mould_statusid`,`mould_statusname` FROM `db_mould_status` ORDER BY `mould_statusid` ASC";
$result_mould_status = $db->query($sql_mould_status);
if($_GET['submit']){
	$client_code = trim($_GET['client_code']);
	$mould_number = trim($_GET['mould_number']);
	$mould_statusid = $_GET['mould_statusid'];
	if($mould_statusid){
		$sql_mould_statusid = " AND `db_mould`.`mould_statusid` = '$mould_statusid'";
	}
	$sqlwhere = " WHERE `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_client`.`client_code` LIKE '%$client_code%' $sql_mould_statusid";
}
$sql = "SELECT `db_mould`.`mouldid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_client`.`client_code`,`db_mould_status`.`mould_statusname` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould`.`mould_number` DESC,`db_mould`.`mouldid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
<div id="table_search">
  <h4>模具成本</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>代码：</th>
        <td><input type="text" name="client_code" class="input_txt" /></td>
        <th>目前状态：</th>
        <td><select name="mould_statusid">
            <option value="">所有</option>
            <?php
			if($result_mould_status->num_rows){
				while($row_mould_status = $result_mould_status->fetch_assoc()){
					echo "<option value=\"".$row_mould_status['mould_statusid']."\">".$row_mould_status['mould_statusname']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_mouldid .= $row_id['mouldid'].',';
	  }
	  $array_mouldid = rtrim($array_mouldid,',');
	  //统计物料成本(订单入库明细)
	  $sql_material = "SELECT `db_mould_material`.`mouldid`,SUM(`db_material_inout`.`amount`) AS `amount`,SUM(`db_material_inout`.`process_cost`) AS `process_cost` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` WHERE `db_mould_material`.`mouldid` IN ($array_mouldid) AND `db_material_inout`.`dotype` = 'I' GROUP BY `db_mould_material`.`mouldid`";
	  $result_material = $db->query($sql_material);
	  if($result_material->num_rows){
		  while($row_material = $result_material->fetch_assoc()){
			  $array_material[$row_material['mouldid']] = array('amount'=>$row_material['amount'],'process_cost'=>$row_material['process_cost']);
		  }
	  }else{
		  $array_material = array();
	  }
	  //统计刀具费用(申领出库明细)
	 $sql_mould_cutter = "SELECT `db_cutter_apply_list`.`mouldid`,SUM(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` WHERE `db_cutter_inout`.`dotype` = 'O' AND `db_cutter_apply_list`.`mouldid` IN ($array_mouldid) GROUP BY `db_cutter_apply_list`.`mouldid`";
	  $result_mould_cutter = $db->query($sql_mould_cutter);
	  if($result_mould_cutter->num_rows){
		  while($row_mould_ocuter = $result_mould_cutter->fetch_assoc()){
			  $array_mould_cutter[$row_mould_ocuter['mouldid']] = $row_mould_ocuter['amount'];
		  }
	  }else{
		  $array_mould_cutter = array();
	  }
	  //统计模具外发加工
	  $sql_mould_outward = "SELECT `mouldid`,SUM(`cost`) AS `cost` FROM `db_mould_outward` WHERE `mouldid` IN ($array_mouldid) AND `outward_status` = 1 GROUP BY `mouldid`";
	  $result_mould_outward = $db->query($sql_mould_outward);
	  if($result_mould_outward->num_rows){
		  while($row_mould_outward = $result_mould_outward->fetch_assoc()){
			  $array_mould_outward[$row_mould_outward['mouldid']] = $row_mould_outward['cost'];
		  }
	  }else{
		  $array_mould_outward = array();
	  }
	  //统计烧焊费用
	  $sql_mould_weld = "SELECT `mouldid`,SUM(`cost`) AS `cost` FROM `db_mould_weld` WHERE `mouldid` IN ($array_mouldid) AND `weld_status` = 1 GROUP BY `mouldid`";
	  $result_mould_weld = $db->query($sql_mould_weld);
	  if($result_mould_weld->num_rows){
		  while($row_mould_weld = $result_mould_weld->fetch_assoc()){
			  $array_mould_weld[$row_mould_weld['mouldid']] = $row_mould_weld['cost'];
		  }
	  }else{
		  $array_mould_weld = array();
	  }
	  //统计试模费用
	  $sql_mould_try = "SELECT `mouldid`,SUM(`cost`) AS `cost` FROM `db_mould_try` WHERE `mouldid` IN ($array_mouldid) AND `try_status` = 1 GROUP BY `mouldid`";
	  $result_mould_try = $db->query($sql_mould_try);
	  if($result_mould_try->num_rows){
		  while($row_mould_try = $result_mould_try->fetch_assoc()){
			  $array_mould_try[$row_mould_try ['mouldid']] = $row_mould_try ['cost'];
		  }
	  }else{
		  $array_mould_try = array();
	  }  
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">模具编号</th>
      <th width="8%">代码</th>
      <th width="10%">项目名称</th>
      <th width="8%">目前状态</th>
      <th width="8%">物料费用</th>
      <th width="8%">物料加工费用</th>
      <th width="8%">外协加工费用</th>
      <th width="8%">烧焊费用</th>
      <th width="8%">试模费用</th>
      <th width="8%">刀具费用</th>
      <th width="10%">费用总计</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$mouldid = $row['mouldid'];
		$material_amount = array_key_exists($mouldid,$array_material)?$array_material[$mouldid]['amount']:0;
		$material_process_cost = array_key_exists($mouldid,$array_material)?$array_material[$mouldid]['process_cost']:0;
		$mould_outward_cost = array_key_exists($mouldid,$array_mould_outward)?$array_mould_outward[$mouldid]:0;
		$mould_weld_cost = array_key_exists($mouldid,$array_mould_weld)?$array_mould_weld[$mouldid]:0;
		$mould_try_cost = array_key_exists($mouldid,$array_mould_try)?$array_mould_try[$mouldid]:0;
		$mould_cutter_cost = array_key_exists($mouldid,$array_mould_cutter)?$array_mould_cutter[$mouldid]:0;
		$total_all_cost = $material_amount+$material_process_cost+$mould_outward_cost+$mould_weld_cost+$mould_try_cost+$mould_cutter_cost;
	?>
    <tr>
      <td><?php echo $mouldid; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['client_code']; ?></td>
      <td><?php echo $row['project_name']; ?></td>
      <td><?php echo $row['mould_statusname']; ?></td>
      <td><?php echo $material_amount; ?></td>
      <td><?php echo $material_process_cost; ?></td>
      <td><?php echo $mould_outward_cost; ?></td>
      <td><?php echo $mould_weld_cost; ?></td>
      <td><?php echo $mould_try_cost; ?></td>
      <td><?php echo $mould_cutter_cost; ?></td>
      <td><?php echo number_format($total_all_cost,2,'.',''); ?></td>
      <td><a href="mould_cost_info.php?id=<?php echo $mouldid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>