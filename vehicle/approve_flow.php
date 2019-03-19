<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$dept_name = trim($_GET['dept_name']);
	$sqlwhere = " AND `dept_name` LIKE '%$dept_name%'";
}
$sql = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `dept_order` ASC,`deptid` ASC" . $pages->limitsql;
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
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>审批流程</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>部门：</th>
        <td><input type="text" name="dept_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th>部门</th>
      <th width="4%">Edit</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$deptid = $row['deptid'];
	?>
    <tr>
      <td><?php echo $deptid; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><a href="approve_flowae.php?id=<?php echo $deptid; ?>&action=add"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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