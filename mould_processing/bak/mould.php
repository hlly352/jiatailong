<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
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
$_SESSION['mould_processing'] = $sql;
$result_allid = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould`.`mould_number` ASC,`db_mould`.`mouldid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具数据</h4>
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
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_mould_processing.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $array_all_mouldid .= $row_allid['mouldid'].',';
	  }
	  $array_all_mouldid = rtrim($array_all_mouldid,',');
	  //统计烧焊费用
	  $sql_weld = "SELECT `mouldid`,SUM(`cost`) AS `total_cost` FROM `db_mould_weld` WHERE `mouldid` IN ($array_all_mouldid) AND `weld_status` = 1 GROUP BY `mouldid`";
	  $result_weld = $db->query($sql_weld);
	  if($result_weld->num_rows){
		  while($row_weld = $result_weld->fetch_assoc()){
			  $array_weld[$row_weld['mouldid']] = $row_weld['total_cost'];
		  }
	  }else{
		  $array_weld = array();
	  }
	  //统计外发费用
	  $sql_outward = "SELECT `mouldid`,SUM(`cost`) AS `total_cost` FROM `db_mould_outward` WHERE `mouldid` IN ($array_all_mouldid) AND `outward_status` = 1 GROUP BY `mouldid`";
	  $result_outward = $db->query($sql_outward);
	  if($result_outward->num_rows){
		  while($row_outward = $result_outward->fetch_assoc()){
			  $array_outward[$row_outward['mouldid']] = $row_outward['total_cost'];
		  }
	  }else{
		  $array_outward = array();
	  }
	  //统计试模费用
	  $sql_try = "SELECT `mouldid`,SUM(`cost`) AS `total_cost` FROM `db_mould_try` WHERE `mouldid` IN ($array_all_mouldid) AND `try_status` = 1 GROUP BY `mouldid`";
	  $result_try = $db->query($sql_try);
	  if($result_try->num_rows){
		  while($row_try = $result_try->fetch_assoc()){
			  $array_try[$row_try ['mouldid']] = $row_try ['total_cost'];
		  }
	  }else{
		  $array_try = array();
	  }  
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">代码</th>
      <th width="10%">项目名称</th>
      <th width="10%">模具编号</th>
      <th width="12%">目前状态</th>
      <th width="10%">外协费用</th>
      <th width="10%">烧焊费用</th>
      <th width="10%">试模费用</th>
      <th width="10%">费用总计</th>
      <th width="4%">外协</th>
      <th width="4%">烧焊</th>
      <th width="4%">试模</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	$total_cost = 0;
	while($row = $result->fetch_assoc()){
		$mouldid = $row['mouldid'];
		$outward_cost = array_key_exists($mouldid,$array_outward)?$array_outward[$mouldid]:0;
		$weld_cost = array_key_exists($mouldid,$array_weld)?$array_weld[$mouldid]:0;
		$try_cost = array_key_exists($mouldid,$array_try)?$array_try[$mouldid]:0;
		$total_cost = $weld_cost + $outward_cost + $try_cost;
	?>
    <tr>
      <td><?php echo $mouldid; ?></td>
      <td><?php echo $row['client_code']; ?></td>
      <td><?php echo $row['project_name']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['mould_statusname']; ?></td>
      <td><?php echo $outward_cost; ?></td>
      <td><?php echo $weld_cost; ?></td>
      <td><?php echo $try_cost; ?></td>
      <td><?php echo $total_cost; ?></td>
      <td><a href="mould_outwardae.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="mould_weldae.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="mould_try_applyae.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="mould_processing_info.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="5">Total</td>
      <td><?php echo array_sum($array_weld); ?></td>
      <td><?php echo array_sum($array_outward); ?></td>
      <td><?php echo array_sum($array_try); ?></td>
      <td><?php echo array_sum($array_weld) + array_sum($array_outward) + array_sum($array_try); ?></td>
      <td colspan="4">&nbsp;</td>
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