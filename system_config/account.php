<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$account = trim($_GET['account']);
	$deptid = $_GET['deptid'];
	if($deptid){
		$sqldept = " AND `db_employee`.`deptid` = '$deptid'";
	}
	$employee_status = $_GET['employee_status'];
	if($employee_status != NULL){
		$sqlemployee_status = " AND `db_employee`.`employee_status` = '$employee_status'";
	}
	$account_status = $_GET['account_status'];
	if($account_status != NULL){
		$sqlaccount_status = " AND `db_employee`.`account_status` = '$account_status'";
	}
	$sqlwhere = " WHERE `db_employee`.`employee_name` LIKE '%$employee_name%' AND `db_employee`.`account` LIKE '%$account%' $sqldept $sqlemployee_status $sqlaccount_status";
}
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`email`,`db_employee`.`employee_status`,`db_employee`.`account_status`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_employee`.`employeeid` ASC" . $pages->limitsql;
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
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>员工账号</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>账号：</th>
        <td><input type="text" name="account" class="input_txt" /></td>
        <th>部门：</th>
        <td><select name="deptid">
            <option value="">所有</option>
            <?php
			if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
					echo "<option value=\"".$row_dept['deptid']."\">".$row_dept['dept_name']."</option>";
				}
			}
			?>
          </select></td>
        <th>员工状态：</th>
        <td><select name="employee_status">
            <option value="">所有</option>
            <?php
			foreach($array_employee_status as $employee_status_key=>$employee_status_value){
				echo "<option value=\"".$employee_status_key."\">".$employee_status_value."</option>";
			}
			?>
          </select></td>
        <th>账号状态：</th>
        <td><select name="account_status">
            <option value="">所有</option>
            <?php
			foreach($array_status as $status_key=>$status_value){
				echo "<option value=\"".$status_key."\">".$status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="15%">员工</th>
      <th width="15%">账号</th>
      <th width="20%">部门</th>
      <th width="30%">邮箱</th>
      <th width="4%">状态</th>
      <th width="4%">账号</th>
      <th width="4%">密码</th>
      <th width="4%">Edit</th>
    </tr>
    <?php
      while($row = $result->fetch_assoc()){
		  $employeeid = $row['employeeid'];
	  ?>
    <tr>
      <td><?php echo $employeeid; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['account']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['email']; ?></td>
      <td><?php echo $array_employee_status[$row['employee_status']]; ?></td>
      <td><?php echo $array_status[$row['account_status']]; ?></td>
      <td><a href="password.php?id=<?php echo $employeeid; ?>"><img src="../images/system_ico/password_10_10.png" width="10" height="10" /></a></td>
      <td><a href="accountae.php?id=<?php echo $employeeid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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