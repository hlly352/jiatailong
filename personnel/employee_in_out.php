<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if(isset($_GET['submit'])){
	$employee_name = trim($_GET['employee_name']);
	$employee_status = $_GET['employee_status'];
	if($employee_status == 1){
		$sql_employee_status = " AND `db_employee`.`employee_status` = '$employee_status' AND (`db_employee`.`entrydate` BETWEEN '$sdate' AND '$edate')";
	}elseif($employee_status == 0 && $employee_status != NULL){
		$sql_employee_status = " AND `db_employee`.`employee_status` = '$employee_status' AND (`db_employee`.`termdate` BETWEEN '$sdate' AND '$edate')";
	}
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%' $sql_employee_status";
}
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_employee`.`entrydate`,`db_employee`.`termdate`,`db_employee`.`sex`,`db_employee`.`employee_status`,`db_department`.`dept_name`,`db_personnel_position`.`position_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE (`db_employee`.`entrydate` BETWEEN '$sdate' AND '$edate') OR (`db_employee`.`termdate` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_department`.`dept_order` ASC,`db_employee`.`employeeid` ASC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>入/离职记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          -
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="employee_status">
            <option value="">所有</option>
            <?php
            foreach($array_employee_status as $employee_status_key=>$employee_status_value){
				echo "<option value=\"".$employee_status_key."\">".$employee_status_value."</option>";
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
      <th width="12%">员工</th>
      <th width="15%">部门</th>
      <th width="15%">职位</th>
      <th width="25%">入职日期</th>
      <th width="25%">离职日期</th>
      <th width="4%">状态</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$employee_status = $row['employee_status'];
		$termdate = $employee_status?'--':$row['termdate'];
	?>
    <tr>
      <td><?php echo $row['employeeid']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['position_name']; ?></td>
      <td><?php echo $row['entrydate']; ?></td>
      <td><?php echo $termdate; ?></td>
      <td><?php echo $array_employee_status[$employee_status]; ?></td>
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