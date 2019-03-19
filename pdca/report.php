<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1";
$result_dept = $db->query($sql_dept);
$deptid = $_GET['deptid']?$_GET['deptid']:'';
if($_GET['submit']){
	$deptid = $_GET['deptid'];
	if($deptid){
		$sqldept = " AND `db_worker`.`deptid` = '$deptid'";
	}
}else{
	$sqldept = '';
}
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-01-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d');
//所有任务
$sql_work = "SELECT `db_work`.`worker`,COUNT(*) AS `count`,`db_worker`.`employee_name` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND `db_work`.`work_status` = 1 $sqldept GROUP BY `db_work`.`worker` ORDER BY `count` DESC";
$result_work = $db->query($sql_work);
if($result_work->num_rows){
	while($row_work = $result_work->fetch_assoc()){
		$array_employee[$row_work['worker']] = array('count'=>$row_work['count'],'employee_name'=>$row_work['employee_name']);
	}
}else{
	$array_employee = array();
}
//print_r($array_employee);
//超期完成
$sql_a = "SELECT `db_work`.`worker`,COUNT(*) AS `count` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) >0 AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` = 'C' $sqldept GROUP BY `db_work`.`worker`";
$result_a = $db->query($sql_a);
if($result_a->num_rows){
	while($row_a = $result_a->fetch_assoc()){
		$array_count_a[$row_a['worker']] = $row_a['count'];
	}
}else{
	$array_count_a = array();
}
//print_r($array_count_a);
//超期未完成
$sql_b = "SELECT `db_work`.`worker`,COUNT(*) AS `count` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(CURDATE(),`db_work`.`deadline_date`) > 0 AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` = 'D' $sqldept GROUP BY `db_work`.`worker`";
$result_b = $db->query($sql_b);
if($result_b->num_rows){
	while($row_b = $result_b->fetch_assoc()){
		$array_count_b[$row_b['worker']] = $row_b['count'];
	}
}else{
	$array_count_b = array();
}
//未接受任务
$sql_c = "SELECT `db_work`.`worker`,COUNT(*) AS `count` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` = 'P' $sqldept GROUP BY `db_work`.`worker`";
$result_c = $db->query($sql_c);
if($result_c->num_rows){
	while($row_c = $result_c->fetch_assoc()){
		$array_count_c[$row_c['worker']] = $row_c['count'];
	}
}else{
	$array_count_c = array();
}
//print_r($array_count_c);
//按时完成任务
$sql_d = "SELECT `db_work`.`worker`,COUNT(*) AS `count` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) <= 0 AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` = 'C' $sqldept GROUP BY `db_work`.`worker`";
$result_d = $db->query($sql_d);
if($result_d->num_rows){
	while($row_d = $result_d->fetch_assoc()){
		$array_count_d[$row_d['worker']] = $row_d['count'];
	}
}else{
	$array_count_d = array();
}
//正在执行的任务
$sql_e = "SELECT `db_work`.`worker`,COUNT(*) AS `count` FROM `db_work` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE (`db_work`.`issue_date` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(CURDATE(),`db_work`.`deadline_date`) <= 0 AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` = 'D' $sqldept GROUP BY `db_work`.`worker`";
$result_e = $db->query($sql_e);
if($result_e->num_rows){
	while($row_e = $result_e->fetch_assoc()){
		$array_count_e[$row_e['worker']] = $row_e['count'];
	}
}else{
	$array_count_e = array();
}
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
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>部门：</th>
        <td><select name="deptid">
            <option value="">所有</option>
            <?php
			if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
			?>
            <option value="<?php echo $row_dept['deptid']; ?>"<?php if($row_dept['deptid'] == $deptid) echo " selected=\"selected\""; ?>><?php echo $row_dept['dept_name']; ?></option>
            <?php	
				}
			}
			?>
          </select></td>
        <th>日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if(count($array_employee)){ ?>
  <table>
    <tr>
      <th>姓名</th>
      <th width="15%">总任务数</th>
      <th width="15%" style="background:#F00">超期完成</th>
      <th width="15%" style="background:#F00">超期未完成</th>
      <th width="15%">按时完成</th>
      <th width="15%" style="background:#F90">未接受任务</th>
      <th width="15%">执行中任务</th>
    </tr>
    <?php
	$toatl_count = 0;
    foreach($array_employee as $employeeid=>$array_employee_info){
		$employee_name = $array_employee_info['employee_name'];
		$count = $array_employee_info['count'];
		$count_a = array_key_exists($employeeid,$array_count_a)?$array_count_a[$employeeid]:0;
		$count_b = array_key_exists($employeeid,$array_count_b)?$array_count_b[$employeeid]:0;
		$count_d = array_key_exists($employeeid,$array_count_d)?$array_count_d[$employeeid]:0;
		$count_c = array_key_exists($employeeid,$array_count_c)?$array_count_c[$employeeid]:0;
		$count_e = array_key_exists($employeeid,$array_count_e)?$array_count_e[$employeeid]:0;
	?>
    <tr>
      <td><?php echo $employee_name; ?></td>
      <td><?php echo $count; ?></td>
      <td><?php echo ($count_a)?"<a href=\"work_list.php?worker_name=".$employee_name."&pdca_result=D&submit=查询\">".$count_a."</a>":$count_a; ?></td>
      <td><?php echo ($count_b)?"<a href=\"work_list.php?worker_name=".$employee_name."&pdca_result=E&submit=查询\">".$count_b."</a>":$count_b; ?></td>
      <td><?php echo ($count_d)?"<a href=\"work_list.php?worker_name=".$employee_name."&pdca_result=C&submit=查询\">".$count_d."</a>":$count_d; ?></td>
      <td><?php echo ($count_c)?"<a href=\"work_list.php?worker_name=".$employee_name."&pdca_result=A&submit=查询\">".$count_c."</a>":$count_c; ?></td>
      <td><?php echo ($count_e)?"<a href=\"work_list.php?worker_name=".$employee_name."&pdca_result=B&submit=查询\">".$count_e."</a>":$count_e; ?></td>
    </tr>
    <?php
	$toatl_count += $count;
	}
	?>
    <tr>
      <td>Total</td>
      <td><?php echo $toatl_count; ?></td>
      <td><?php echo array_sum($array_count_a); ?></td>
      <td><?php echo array_sum($array_count_b); ?></td>
      <td><?php echo array_sum($array_count_d); ?></td>
      <td><?php echo array_sum($array_count_c); ?></td>
      <td><?php echo array_sum($array_count_e); ?></td>
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