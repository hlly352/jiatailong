<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-01-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate ." +1 year -1 day"));
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$plan_type = $_GET['plan_type'];
	if($plan_type){
		$sql_plan_type = " AND `db_job_plan`.`plan_type` = '$plan_type'";
	}
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%' $sql_plan_type";
}
$sql = "SELECT `db_employee`.`employee_name`,`db_job_plan`.`employeeid`,COUNT(*) AS `total_count` FROM `db_job_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE (`db_job_plan`.`start_date` BETWEEN '$sdate' AND '$edate') AND `db_job_plan`.`plan_status` = 1 $sqlwhere GROUP BY `db_job_plan`.`employeeid`";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY COUNT(*) DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>计划工作报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>计划人：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="plan_type">
            <option value="">所有</option>
            <?php foreach($array_job_plan_type as $plan_type_key=>$plan_type_value){ ?>
            <option value="<?php echo $plan_type_key; ?>"<?php if($plan_type_key == $plan_type) echo " selected=\"selected\""; ?>><?php echo $plan_type_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="明细" class="button" onclick="location.href='job_plan_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  $sql_plan = "SELECT `db_job_plan`.`employeeid`,COUNT(*) AS `count` FROM `db_job_plan_list` INNER JOIN `db_job_plan` ON `db_job_plan`.`planid` = `db_job_plan_list`.`planid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE ((`db_job_plan`.`start_date` BETWEEN '$sdate' AND '$edate') OR (`db_job_plan`.`finish_date` BETWEEN '$sdate' AND '$edate')) AND `db_job_plan`.`plan_status` = 1 $sqlwhere GROUP BY `db_job_plan`.`employeeid`";
	  $result_plan = $db->query($sql_plan); 
	  if($result_plan->num_rows){
		  while($row_plan = $result_plan->fetch_assoc()){
			  $array_plan[$row_plan['employeeid']] = $row_plan['count'];
		  }
	  }else{
		  $array_plan = array();
	  }
  ?>
  <table>
    <tr>
      <th width="25%">计划人</th>
      <th width="25%">计划数量</th>
      <th width="25%">未完成数量</th>
      <th width="25%">按时完成率</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$employeeid = $row['employeeid'];
		$total_count = $row['total_count'];
		$count = array_key_exists($employeeid,$array_plan)?$array_plan[$employeeid]:0;
		$rate = number_format((($total_count-$count)/$total_count)*100,2);
    ?>
    <tr>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $total_count; ?></td>
      <td><?php echo $count; ?></td>
      <td><?php echo $rate; ?>%</td>
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