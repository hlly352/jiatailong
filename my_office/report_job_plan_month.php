<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%'";
}
$sql = "SELECT `db_job_plan`.`employeeid`,`db_employee`.`employee_name` FROM `db_job_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE DATE_FORMAT(`db_job_plan`.`start_date`,'%Y') = '$year' AND `db_employee`.`employee_status` = 1 $sqlwhere GROUP BY `db_job_plan`.`employeeid`";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_employee`.`employeeid` ASC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>计划月报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>计划人：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>年份：</th>
        <td><input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_employeeid .= $row_id['employeeid'].',';
	  }
	  $array_employeeid = rtrim($array_employeeid,',');
	  //统计每月未完成数量
	  $sql_job_no = "SELECT `db_job_plan`.`employeeid`,DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_job_plan_list` INNER JOIN `db_job_plan` ON `db_job_plan`.`planid` = `db_job_plan_list`.`planid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE `db_job_plan`.`employeeid` IN ($array_employeeid) AND DATE_FORMAT(`db_job_plan`.`start_date`,'%Y') = '$year' GROUP BY `db_job_plan`.`employeeid`,DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m')";
	  $result_job_no = $db->query($sql_job_no);
	  if($result_job_no->num_rows){
		  while($row_job_no = $result_job_no->fetch_assoc()){
			  $array_job_no[$row_job_no['employeeid'].'-'.$row_job_no['month']] = $row_job_no['count'];
		  }
	  }else{
		  $array_job_no = array();
	  }
	  //统计每月总数
	  $sql_job = "SELECT `db_job_plan`.`employeeid`,DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_job_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE `db_job_plan`.`employeeid` IN ($array_employeeid) AND DATE_FORMAT(`db_job_plan`.`start_date`,'%Y') = '$year' GROUP BY `db_job_plan`.`employeeid`,DATE_FORMAT(`db_job_plan`.`start_date`,'%Y-%m')";
	  $result_job = $db->query($sql_job);
	  if($result_job->num_rows){
		  while($row_job = $result_job->fetch_assoc()){
			  $array_job[$row_job['employeeid'].'-'.$row_job['month']] = $row_job['count'];
		  }
	  }else{
		  $array_job = array();
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">计划人</th>
      <th width="6%">类型</th>
      <?php
      for($i=1;$i<=12;$i++){
		  echo "<th width=\"6%\">".$i."月</th>";
	  }
	  ?>
      <th width="8%">Total</th>
      <th width="4%">Chart</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$employeeid = $row['employeeid'];
	?>
    <tr>
      <td rowspan="3"><?php echo $employeeid; ?></td>
      <td rowspan="3"><?php echo $row['employee_name']; ?></td>
      <td>计划数量</td>
      <?php
	  $total_job = 0;
      for($i=1;$i<=12;$i++){
		  $job_key = $employeeid.'-'.date('Y-m',strtotime($year.'-'.$i));
		  $job = array_key_exists($job_key,$array_job)?$array_job[$job_key]:0;
		  echo "<td>".$job."</td>";
		  $total_job += $job;
	  }
	  ?>
      <td><?php echo $total_job; ?></td>
      <td rowspan="3"><a href="jpgraph_report_job_plan_month.php?id=<?php echo $employeeid; ?>&year=<?php echo $year ?>" target="_blank"><img src="../images/system_ico/chart_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <tr>
      <td>未完成数量</td>
      <?php
	  $total_job_no = 0;
      for($i=1;$i<=12;$i++){
		  $job_no_key = $employeeid.'-'.date('Y-m',strtotime($year.'-'.$i));
		  $job_no = array_key_exists($job_no_key,$array_job_no)?$array_job_no[$job_no_key]:0;
		  echo "<td>".$job_no."</td>";
		  $total_job_no += $job_no;
	  }
	  ?>
      <td><?php echo $total_job_no; ?></td>
    </tr>
    <tr>
      <td>按时完成率</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $job_key = $employeeid.'-'.date('Y-m',strtotime($year.'-'.$i));
		  $job_no_key = $employeeid.'-'.date('Y-m',strtotime($year.'-'.$i));
		  $job = array_key_exists($job_key,$array_job)?$array_job[$job_key]:0;
		  $job_no = array_key_exists($job_no_key,$array_job_no)?$array_job_no[$job_no_key]:0;
		  $job_yes = $job-$job_no;
		  $ratio = round(@($job_yes/$job)*100,2).'%';
		  echo "<td>".$ratio."</td>";
	  }
	  ?>
      <td><?php
      $total_job_yes = $total_job-$total_job_no;
	  echo $total_ratio = round(@($total_job_yes/$total_job)*100,2).'%';
	  ?></td>
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