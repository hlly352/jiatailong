<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`employee_number`,`db_employee`.`superior`,`db_employee`.`position_type`,`db_employee`.`education`,`db_employee`.`idcard`,`db_employee`.`birthday`,`db_employee`.`sex`,`db_employee`.`nativeplace`,`db_employee`.`entrydate`,`db_employee`.`termdate`,`db_employee`.`address`,`db_employee`.`phone`,`db_employee`.`extnum`,`db_employee`.`email`,`db_employee`.`employee_status`,`db_employee`.`account_status`,`db_employee`.`remark`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_superior`.`employee_name` AS `superior_name`,`db_department`.`dept_name`,`db_personnel_position`.`position_name`,(DATE_FORMAT(CURDATE(),'%Y')-DATE_FORMAT(`db_employee`.`birthday`,'%Y')) AS `age`,IF((`db_employee`.`employee_status` = 1),ROUND(DATEDIFF(CURDATE(),`db_employee`.`entrydate`)/365,1),ROUND(DATEDIFF(`db_employee`.`termdate`,`db_employee`.`entrydate`)/365,1)) AS `work_year` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` = '$employeeid'";
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
	  $account = $array['account']?$array['account']:'--';
	  $superior_name = ($array['superior'] != 0)?$array['superior_name']:'--';
	  $termdate = $array['employee_status']?'--':$array['termdate'];
	  $photo_path = "../upload/personnel/".$array['photo_filedir'].'/'.$array['photo_filename'];
	  $photo = is_file($photo_path)?"<img src=\"".$photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
  ?>
  <h4>员工信息</h4>
  <table>
    <tr>
      <th rowspan="8" width="13%" style="text-align:center;"><?php echo $photo; ?></th>
      <th width="11%">工号：</th>
      <td width="18%"><?php echo $array['employee_number']; ?></td>
      <th width="11%">姓名：</th>
      <td width="18%"><?php echo $array['employee_name']; ?></td>
      <th width="11%">账号：</th>
      <td width="18%"><?php echo $account; ?></td>
    </tr>
    <tr>
      <th>部门：</th>
      <td><?php echo $array['dept_name']; ?></td>
      <th>上级领导：</th>
      <td><?php echo $superior_name; ?></td>
      <th>岗位级别：</th>
      <td><?php echo $array_position_type[$array['position_type']]; ?></td>
    </tr>
    <tr>
      <th>工作职位：</th>
      <td><?php echo $array['position_name']; ?></td>
      <th>文化程度：</th>
      <td><?php echo $array_education_type[$array['education']]; ?></td>
      <th>身份证号码：</th>
      <td><?php echo $array['idcard']; ?></td>
    </tr>
    <tr>
      <th>入职日期：</th>
      <td><?php echo $array['entrydate']; ?></td>
      <th>离职日期：</th>
      <td><?php echo $termdate; ?></td>
      <th>工龄：</th>
      <td><?php echo $array['work_year']; ?></td>
    </tr>
    <tr>
      <th>出生年月：</th>
      <td><?php echo $array['birthday']; ?></td>
      <th>年龄：</th>
      <td><?php echo $array['age']; ?></td>
      <th>性别：</th>
      <td><?php echo $array['sex']; ?></td>
    </tr>
    <tr>
      <th>联系电话：</th>
      <td><?php echo $array['phone']; ?></td>
      <th>分机：</th>
      <td><?php echo $array['extnum']; ?></td>
      <th>Email：</th>
      <td><?php echo $array['email']; ?></td>
    </tr>
    <tr>
      <th>籍贯：</th>
      <td><?php echo $array['nativeplace']; ?></td>
      <th>家庭住址：</th>
      <td><?php echo $array['address']; ?></td>
      <th>员工状态：</th>
      <td><?php echo $array_employee_status[$array['employee_status']]; ?></td>
    </tr>
    <tr>
      <th>账号状态：</th>
      <td><?php echo $array_status[$array['account_status']]; ?></td>
      <th>备注：</th>
      <td colspan="3"><?php echo $array['remark']; ?></td>
    </tr>
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