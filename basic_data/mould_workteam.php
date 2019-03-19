<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$workteam_name = trim($_GET['workteam_name']);
	$sqlwhere = " WHERE `workteam_name` LIKE '%$workteam_name%'";
}
$sql = "SELECT * FROM `db_mould_workteam` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `workteamid` DESC" . $pages->limitsql;
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
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具申请组别</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>组别名称：</th>
        <td><input type="text" name="workteam_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_workteamae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  $sql_mould_weld = "SELECT `workteamid` FROM `db_mould_weld` GROUP BY `workteamid`";
	  $result_mould_weld = $db->query($sql_mould_weld);
	  if($result_mould_weld->num_rows){
		  while($row_mould_weld = $result_mould_weld->fetch_assoc()){
			  $array_mould_weld[] = $row_mould_weld['workteamid'];
		  }
	  }else{
		  $array_mould_weld = array();
	  }
	  $sql_mould_outward = "SELECT `workteamid` FROM `db_mould_outward` GROUP BY `workteamid`";
	  $result_mould_outward = $db->query($sql_mould_outward);
	  if($result_mould_outward->num_rows){
		  while($row_mould_outward = $result_mould_outward->fetch_assoc()){
			  $array_mould_outward[] = $row_mould_outward['work_teamid'];
		  }
	  }else{
		  $array_mould_outward = array();
	  }
	  $array_workteam = array_unique(array_merge($array_mould_weld,$array_mould_outward));
  ?>
  <form action="mould_workteamdo.php" name="mould_workteam_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>组别名称</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $workteamid = $row['workteamid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $workteamid; ?>"<?php if(in_array($workteamid,$array_workteam)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['workteam_name']; ?></td>
        <td><?php echo $array_status[$row['workteam_status']]; ?></td>
        <td width="4%"><a href="mould_workteamae.php?id=<?php echo $workteamid; ?>&amp;action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>