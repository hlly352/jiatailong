<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
	$apply_number = rtrim($_GET['apply_number']);
	$specification = rtrim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " WHERE `db_cutter_apply`.`apply_number` LIKE '%$apply_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid";
}
$sql = "SELECT `db_cutter_apply_list`.`apply_listid`,`db_cutter_apply_list`.`quantity`,`db_cutter_apply_list`.`out_quantity`,`db_cutter_apply_list`.`plan_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_employee`.`employee_name`,`db_mould`.`mould_number` FROM `db_cutter_apply_list` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid`  INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid`$sqlwhere";
$result = $db->query($sql);
$result_all = $db->query($sql);
$_SESSION['apply_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_apply`.`applyid` DESC,`db_cutter_apply_list`.`apply_listid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>刀具管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>刀具申领明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申领单号：</th>
        <td><input type="text" name="apply_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th></th>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_apply_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_all = $result_all->fetch_assoc()){
		  $total_quantity += $row_all['quantity']; 
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">申领单号</th>
      <th width="6%">类型</th>
      <th width="14%">规格</th>
      <th width="6%">材质</th>
      <th width="10%">硬度</th>
      <th width="6%">申领数量</th>
      <th width="6%">已领数量</th>
      <th width="4%">单位</th>
      <th width="8%">模具编号</th>
      <th width="8%">申领人</th>
      <th width="8%">申请日期</th>
      <th width="8%">计划申领日期</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$apply_listid = $row['apply_listid'];
	?>
    <tr>
      <td><?php echo $apply_listid; ?></td>
      <td><?php echo $row['apply_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['out_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><a href="cutter_apply_list_info.php?id=<?php echo $apply_listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="6">&nbsp;</td>
      <td><?php echo $total_quantity; ?></td>
      <td colspan="7">&nbsp;</td>
    </tr>
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