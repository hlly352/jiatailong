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
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould`.`mould_number` DESC,`db_mould`.`mouldid` ASC" . $pages->limitsql;
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
<title>模具物料-希尔林</title>
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
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_moulid .= $row_id['mouldid'].',';
	  }
	  $array_moulid = rtrim($array_moulid,',');
	  $sql_material = "SELECT `mouldid`,COUNT(*) AS `count` FROM `db_mould_material` WHERE  `mouldid` IN ($array_moulid) AND `type` != 'D' GROUP BY `mouldid`";
	  $result_material = $db->query($sql_material);
	  if($result_material->num_rows){
		  while($row_material = $result_material->fetch_assoc()){
			  $array_material[$row_material['mouldid']] = $row_material['count'];
		  }
	  }else{
		  $array_material = array();
	  }
   
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="20%">模具编号</th>
      <th width="14%">代码</th>
      <th width="20%">项目名称</th>
      <th width="20%">目前状态</th>
      <th width="6%">物料项数</th>
      <th width="4%">Add</th>
      <th width="4%">Import</th>
      <th width="4%">Excel</th>
      <th width="4%">List</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$mouldid = $row['mouldid'];
		$count = array_key_exists($mouldid,$array_material)?$array_material[$mouldid]:0;
	?>
    <tr>
      <td><?php echo $mouldid; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['client_code']; ?></td>
      <td><?php echo $row['project_name']; ?></td>
      <td><?php echo $row['mould_statusname']; ?></td>
      <td><?php echo $count; ?></td>
      <td><a href="mould_materialae.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="mould_material_import.php?id=<?php echo $mouldid; ?>&action=add"><img src="../images/system_ico/upload_10_10.png" width="10" height="10" /></a></td>
      <td><a href="excel_mould_material.php?id=<?php echo $mouldid; ?>"><img src="../images/system_ico/excel_10_10.png" width="10" height="10" /></a></td>
      <td><a href="mould_material_info.php?id=<?php echo $mouldid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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