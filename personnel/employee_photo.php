<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
#employee_photolist {
	margin-bottom:10px;
}
#employee_photolist table {
	width:100%;
	border-collapse:collapse;
	background:#FFF;
}
#employee_photolist table th, #employee_photolist table td {
	border:1px solid #DDD;
}
#employee_photolist table th {
	font-size:14px;
}
#employee_photolist table td {
	padding:10px 0 10px 10px;
}
#employee_photolist table td dl {
	float:left;
	width:105px;
	height:228px;
	text-align:center;
	margin:5px 15px;
}
#employee_photolist table td dd {
	width:98px;
	height:140px;
}
#employee_photolist table td dt {
	font-size:13px;
	padding:2px 0;
}
</style>
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="employee_photolist">
  <?php if($result_dept->num_rows){ ?>
  <table>
    <?php
    while($row_dept = $result_dept->fetch_assoc()){
		$deptid = $row_dept['deptid'];
		$sql_employee = "SELECT `db_employee`.`employee_name` ,`db_employee`.`nativeplace`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_personnel_position`.`position_name` FROM `db_employee` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE `db_employee`.`deptid` = '$deptid' AND `db_employee`.`employee_status` = 1 ORDER BY `db_employee`.`position_type` ASC,`db_employee`.`employeeid` ASC";
		$result_employee = $db->query($sql_employee);
	?>
    <tr>
      <th width="10%"><?php echo $row_dept['dept_name']; ?></th>
      <td width="90%">
	  <?php
      if($result_employee->num_rows){
		  while($row_employee = $result_employee->fetch_assoc()){
			  $photo_path = "../upload/personnel/".$row_employee['photo_filedir'].'/'.$row_employee['photo_filename'];
			  $photo = is_file($photo_path)?"<img src=\"".$photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
	  ?>
        <dl>
          <dd><?php echo $photo; ?></dd>
          <dt><?php echo $row_employee['employee_name']; ?></dt>
          <dt><?php echo $row_employee['position_name']; ?></dt>
          <dt><?php echo $row_employee['nativeplace']; ?></dt>
        </dl>
	  <?php
		  }
	  }
	  ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>