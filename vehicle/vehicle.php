<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$plate_number = trim($_GET['plate_number']);
	$sqlwhere = " WHERE `plate_number` LIKE '%$plate_number%'";
}
$sql = "SELECT * FROM `db_vehicle` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `vehicleid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_vehicleid = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>车辆数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>车辆车牌：</th>
        <td><input type="text" name="plate_number" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='vehicleae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_vehicleid = $result_vehicleid->fetch_assoc()){
		  $array_vehicleid .= $row_vehicleid['vehicleid'].',';
	  }
	  $array_vehicleid = rtrim($array_vehicleid,',');
	  $sql_vehicle_list = "SELECT `vehicleid` FROM `db_vehicle_list` WHERE `vehicleid` IN ($array_vehicleid) GROUP BY `vehicleid`";
	  $result_vehicle_list = $db->query($sql_vehicle_list);
	  if($result_vehicle_list->num_rows){
		  while($row_vehicle_list = $result_vehicle_list->fetch_assoc()){
			  $array_vehicle[] = $row_vehicle_list['vehicleid'];
		  }
	  }else{
		  $array_vehicle = array();
	  }
  ?>
  <form action="vehicledo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">车辆车牌</th>
        <th width="8%">类型</th>
        <th width="10%">联系人</th>
        <th width="15%">联系方式</th>
        <th width="15%">长途费用(元/公里)</th>
        <th width="15%">市内费用(元/公里)</th>
        <th width="15%">等候费用(元/小时)</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $vehicleid = $row['vehicleid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $vehicleid; ?>"<?php if(in_array($vehicleid,$array_vehicle)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['plate_number']; ?></td>
        <td><?php echo $array_vehicle_type[$row['vehicle_type']]; ?></td>
        <td><?php echo $row['owner']; ?></td>
        <td><?php echo $row['contact']; ?></td>
        <td><?php echo $row['charge_out']; ?></td>
        <td><?php echo $row['charge_in']; ?></td>
        <td><?php echo $row['charge_wait']; ?></td>
        <td><?php echo $array_status[$row['vehicle_status']]; ?></td>
        <td><a href="vehicleae.php?id=<?php echo $vehicleid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无车辆数据！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>