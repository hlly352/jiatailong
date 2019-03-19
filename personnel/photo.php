<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee`.`employee_name`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employeeid` = '$employeeid'";
$result = $db->query($sql);
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
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $photo_filedir = $array['photo_filedir'];
	  $photo_filename = $array['photo_filename'];
	  $photo_path = "../upload/personnel/".$photo_filedir.'/'.$photo_filename;
	  $photo = is_file($photo_path)?"<img src=\"".$photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
  ?>
  <h4>员工照片更新</h4>
  <form action="photodo.php" name="photo" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">员工：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>员工照片：</th>
        <td><?php echo $photo; ?></td>
      </tr>
      <tr>
        <th>更新照片：</th>
        <td><input type="file" name="file" class="input_file" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="employeeid" value="<?php echo $employeeid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>