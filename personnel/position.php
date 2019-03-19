<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$position_name = trim($_GET['position_name']);
	$sqlwhere = " WHERE `position_name` LIKE '%$position_name%'";
}
$sql = "SELECT * FROM `db_personnel_position` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `position_code` ASC,`positionid` ASC" . $pages->limitsql;
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>职位</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>职位：</th>
        <td><input type="text" name="position_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='positionae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_positionid = $result_id->fetch_assoc()){
		  $array_positionid .= $row_positionid['positionid'].',';
	  }
	  $array_positionid = rtrim($array_positionid,',');
	  $sql_employee = "SELECT `positionid` FROM `db_employee` WHERE `positionid` IN ($array_positionid) GROUP BY `positionid`";
	  $result_employee = $db->query($sql_employee);
	  if($result_employee->num_rows){
		  while($row_employee = $result_employee->fetch_assoc()){
			  $array_employee[] = $row_employee['positionid'];
		  }
	  }else{
		  $array_employee = array();
	  }
  ?>
  <form action="positiondo.php" name="position_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>职位</th>
        <th width="4%">代码</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $positionid = $row['positionid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $positionid; ?>"<?php if(in_array($positionid,$array_employee)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['position_name']; ?></td>
        <td><?php echo $row['position_code']; ?></td>
        <td><?php echo $array_status[$row['position_status']]; ?></td>
        <td><a href="positionae.php?id=<?php echo $positionid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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