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
$sql = "SELECT `db_job_plan`.`planid`,`db_job_plan`.`plan_content`,`db_job_plan`.`plan_type`,`db_job_plan`.`start_date`,`db_job_plan`.`finish_date`,`db_employee`.`employee_name`,DATE_FORMAT(`db_job_plan_list`.`dotime`,'%Y-%m-%d') AS `dodate` FROM `db_job_plan_list` INNER JOIN `db_job_plan` ON `db_job_plan`.`planid` = `db_job_plan_list`.`planid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE ((`db_job_plan`.`start_date` BETWEEN '$sdate' AND '$edate') OR (`db_job_plan`.`finish_date` BETWEEN '$sdate' AND '$edate')) AND `db_job_plan`.`plan_status` = 1 $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_job_plan_list`.`listid` DESC" . $pages->limitsql;
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
  <h4>未按时完成计划工作</h4>
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
        <td><input type="submit" name="submit" value="查询" class="button" onclick="javascript:form.action=''" />
        <input type="submit" name="submit" value="报表" class="button" onclick="javascript:form.action='report_job_plan.php'" />
        <input type="button" name="button" value="月报表" class="button" onclick="location.href='report_job_plan_month.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">计划人</th>
      <th>内容</th>
      <th width="6%">类型</th>
      <th width="10%">开始日期</th>
      <th width="10%">完成日期</th>
      <th width="10%">采集日期</th>
    </tr>
    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row['planid']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td style="text-align:left;"><?php echo $row['plan_content']; ?></td>
      <td><?php echo $array_job_plan_type[$row['plan_type']] ?></td>
      <td><?php echo $row['start_date']; ?></td>
      <td><?php echo $row['finish_date']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
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