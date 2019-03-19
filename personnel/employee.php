<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
//职位
$sql_position = "SELECT `positionid`,CONCAT(`position_code`,'-',`position_name`) AS `position_name` FROM `db_personnel_position` WHERE `position_status` = 1 ORDER BY `position_code` ASC,`positionid` ASC";
$result_position = $db->query($sql_position);
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$employee_number = trim($_GET['employee_number']);
	$deptid = $_GET['deptid'];
	if($deptid){
		$sql_deptid = " AND `db_employee`.`deptid` = '$deptid'";
	}
	$positionid = $_GET['positionid'];
	if($positionid){
		$sql_positionid = " AND `db_employee`.`positionid` = '$positionid'";
	}
	$work_year = $_GET['work_year'];
	if($work_year){
		$sql_work_year = " AND IF((`db_employee`.`employee_status` = 1),ROUND(DATEDIFF(CURDATE(),`db_employee`.`entrydate`)/365,1),ROUND(DATEDIFF(`db_employee`.`termdate`,`db_employee`.`entrydate`)/365,1)) >= '$work_year'";
	}
	$employee_status = $_GET['employee_status'];
	if($employee_status != NULL){
		$sql_employee_status = " AND `db_employee`.`employee_status` = '$employee_status'";
	}
	$sqlwhere = " WHERE `db_employee`.`employee_name` LIKE '%$employee_name%' AND `db_employee`.`employee_number` LIKE '%$employee_number%' $sql_deptid $sql_positionid $sql_work_year $sql_employee_status";
}
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_employee`.`employee_number`,`db_employee`.`employee_number`,`db_employee`.`education`,`db_employee`.`idcard`,`db_employee`.`birthday`,`db_employee`.`sex`,`db_employee`.`phone`,`db_employee`.`nativeplace`,`db_employee`.`entrydate`,`db_employee`.`employee_status`,`db_department`.`dept_name`,`db_personnel_position`.`position_name`,(DATE_FORMAT(CURDATE(),'%Y')-DATE_FORMAT(`db_employee`.`birthday`,'%Y')) AS `age`,IF((`db_employee`.`employee_status` = 1),ROUND(DATEDIFF(CURDATE(),`db_employee`.`entrydate`)/365,1),ROUND(DATEDIFF(`db_employee`.`termdate`,`db_employee`.`entrydate`)/365,1)) AS `work_year` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` $sqlwhere";
$result = $db->query($sql);
$_SESSION['employee'] = $sql;
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_department`.`dept_order` ASC,`db_employee`.`employee_number` ASC,`db_employee`.`employeeid` ASC" . $pages->limitsql;
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>员工</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>工号：</th>
        <td><input type="text" name="employee_number" class="input_txt" /></td>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
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
        <th>职位：</th>
        <td><select name="positionid">
            <option value="">所有</option>
            <?php
            if($result_position->num_rows){
				while($row_position = $result_position->fetch_assoc()){
					echo "<option value=\"".$row_position['positionid']."\">".$row_position['position_name']."</option>";
				}
			}
			?>
          </select></td>
        <th>工龄：</th>
        <td><select name="work_year">
            <option value="">所有</option>
            <option value="1">1年</option>
            <option value="3">3年</option>
            <option value="5">5年</option>
            <option value="7">7年</option>
            <option value="10">10年</option>
          </select></td>
        <th>状态：</th>
        <td><select name="employee_status">
            <option value="">所有</option>
            <?php foreach($array_employee_status as $employee_status_key=>$employee_status_value){ ?>
            <option value="<?php echo $employee_status_key; ?>"><?php echo $employee_status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='employeeae.php?action=add'" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="employeedo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="5%">工号</th>
        <th width="5%">员工</th>
        <th width="5%">部门</th>
        <th width="6%">职务</th>
        <th width="4%">文化</th>
        <th width="10%">身份证号码</th>
        <th width="6%">出生年月</th>
        <th width="4%">年龄</th>
        <th width="4%">性别</th>
        <th width="10%">籍贯</th>
        <th width="6%">入职日期</th>
        <th width="4%">工龄</th>
        <th width="7%">联系电话</th>
        <th width="4%">状态</th>
        <th width="4%">Level</th>
        <th width="4%">Photo</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $employeeid = $row['employeeid'];
		  $nativeplace = strlen_sub($row['nativeplace'],10,10);
		  $nativeplace_title = (mb_strlen($row['nativeplace'],'utf8')>10)?" title=\"".$row['nativeplace']."\"":'';
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $employeeid; ?>" disabled="disabled" /></td>
        <td><?php echo $row['employee_number']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dept_name']; ?></td>
        <td><?php echo $row['position_name']; ?></td>
        <td><?php echo $array_education_type[$row['education']]; ?></td>
        <td><?php echo $row['idcard']; ?></td>
        <td><?php echo $row['birthday']; ?></td>
        <td><?php echo $row['age']; ?></td>
        <td><?php echo $row['sex']; ?></td>
        <td<?php echo $nativeplace_title; ?>><?php echo $nativeplace; ?></td>
        <td><?php echo $row['entrydate']; ?></td>
        <td><?php echo $row['work_year']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $array_employee_status[$row['employee_status']]; ?></td>
        <td><a href="employee_level.php?id=<?php echo $employeeid; ?>"><img src="../images/system_ico/employee_level_10_10.png" width="10" height="10" /></a></td>
        <td><a href="photo.php?id=<?php echo $employeeid; ?>"><img src="../images/system_ico/photo_10_10.png" width="10" height="10" /></a></td>
        <td><a href="employeeae.php?id=<?php echo $employeeid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
        <td><a href="employee_info.php?id=<?php echo $employeeid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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