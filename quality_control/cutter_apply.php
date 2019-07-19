<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$apply_number = trim($_GET['apply_number']);
	$sqlwhere = " WHERE `db_cutter_apply`.`apply_number` LIKE '%$apply_number%'";
}
$sql = "SELECT `db_cutter_apply`.`applyid`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_apply`.`apply_time`,`db_cutter_apply`.`employeeid`,`db_employee`.`employee_name` FROM `db_cutter_apply` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_apply`.`applyid` DESC" . $pages->limitsql;
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
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>刀具申领</h4>
  <form action="" name="mould_cutter_apply" method="get">
    <table>
      <tr>
        <th>申领单号：</th>
        <td><input type="text" name="apply_number" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="申领" class="button" onclick="location.href='cutter_apply_create.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_applyid .= $row_id['applyid'].',';
 	  }
	  $array_applyid = rtrim($array_applyid,',');
	  //统计项数
	  $sql_list_count = "SELECT `applyid`,COUNT(*) AS `count` FROM `db_cutter_apply_list` WHERE `applyid` IN ($array_applyid) GROUP BY `applyid`";
	  $result_list_count = $db->query($sql_list_count);
	  if($result_list_count->num_rows){
		  while($row_list_count = $result_list_count->fetch_assoc()){
			  $array_list_count[$row_list_count['applyid']] = $row_list_count['count'];
		  }
	  }else{
		  $array_list_count = array();
	  }
	  //统计申购单明细是否被下订单
	  $sql_inout = "SELECT `db_cutter_apply_list`.`applyid` FROM `db_cutter_inout` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` GROUP BY `db_cutter_apply_list`.`applyid`";
	  $result_inout = $db->query($sql_inout);
	  if($result_inout->num_rows){
		  while($row_inout = $result_inout->fetch_assoc()){
			  $array_inout[] = $row_inout['applyid'];
		  }
	  }else{
		  $array_inout = array();
	  }
	  //print_r($array_inout);
  ?>
  <form action="cutter_applydo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="15%">申购单号</th>
        <th width="15%">申购人</th>
        <th width="15%">申购日期</th>
        <th width="25%">操作时间</th>
        <th width="10%">项数</th>
        <th width="4%">Add</th>
        <th width="4%">list</th>
        <th width="4%">Excel</th>
        <th width="4%">Print</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $applyid = $row['applyid'];
		  $list_count = array_key_exists($applyid,$array_list_count)?$array_list_count[$applyid]:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $applyid; ?>"<?php if(in_array($applyid,$array_inout) || $row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['apply_number']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['apply_date']; ?></td>
        <td><?php echo $row['apply_time']; ?></td>
        <td><?php echo $list_count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_apply_list_add.php?applyid=<?php echo $applyid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /><?php } ?></a></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_apply_list.php?applyid=<?php echo $applyid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><a href="excel_cutter_apply.php?id=<?php echo $applyid; ?>"><img src="../images/system_ico/excel_10_10.png" width="10" height="10" /></a></td>
        <td><a href="cutter_apply_print.php?id=<?php echo $applyid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
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