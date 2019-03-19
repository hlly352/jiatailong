<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$goout_num = trim($_GET['goout_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$sqlwhere = " AND `db_employee_goout`.`goout_num` LIKE '%$goout_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_employee_goout`.`gooutid`,`db_employee_goout`.`goout_num`,`db_employee_goout`.`applyer`,`db_employee_goout`.`apply_date`,`db_employee_goout`.`destination`,`db_employee_goout`.`goout_cause`,DATE_FORMAT(`db_employee_goout`.`start_time`,'%m-%d %H:%i') AS `start_time`,DATE_FORMAT(`db_employee_goout`.`finish_time`,'%m-%d %H:%i') AS `finish_time`,`db_employee_goout`.`approve_status`,`db_employee_goout`.`goout_status`,`db_employee_goout`.`confirmer_out`,`db_employee_goout`.`confirmer_in`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outname`,`db_confirmer_in`.`employee_name` AS `confirmer_inname`,`db_department`.`dept_name`,ROUND(TIMESTAMPDIFF(MINUTE,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`)/60,1) AS `diffhour` FROM `db_employee_goout` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_goout`.`applyer` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid` = `db_employee_goout`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid` = `db_employee_goout`.`confirmer_in` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_applyer`.`deptid` WHERE (`db_employee_goout`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_goout`.`gooutid` DESC" . $pages->limitsql;
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
  <h4>出门证</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>出门证号：</th>
        <td><input type="text" name="goout_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
      <th width="6%">出门证号</th>
      <th width="6%">部门</th>
      <th width="6%">申请人</th>
      <th width="6%">申请日期</th>
      <th width="8%">出厂时间</th>
      <th width="8%">回厂时间</th>
      <th width="6%">小时(H)</th>
      <th width="10%">目的地</th>
      <th width="16%">事由</th>
      <th width="6%">出厂<br />
        确认人</th>
      <th width="6%">回厂<br />
        确认人</th>
      <th width="4%">审批</th>
      <th width="4%">状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$gooutid = $row['gooutid'];
		$confirmer_out = $row['confirmer_out'];
		$confirmer_in = $row['confirmer_in'];
		$confirmer_outname = $confirmer_out?$row['confirmer_outname']:'--';
		$confirmer_inname = $confirmer_in?$row['confirmer_inname']:'--';
		$goout_cause = $row['goout_cause'];
		$goout_cause_content = strlen_sub($goout_cause,15,15);
		$goout_cause_title = (mb_strlen($goout_cause,'utf8')>15)?" title=\"" . $goout_cause . "\"":'';
	?>
    <tr>
      <td><?php echo $gooutid; ?></td>
      <td><?php echo $row['goout_num']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['start_time']; ?></td>
      <td><?php echo $row['finish_time']; ?></td>
      <td><?php echo $row['diffhour']; ?></td>
      <td><?php echo $row['destination']; ?></td>
      <td<?php echo $goout_cause_title ?>><?php echo $goout_cause_content; ?></td>
      <td><?php echo $confirmer_outname; ?></td>
      <td><?php echo $confirmer_inname; ?></td>
      <td><?php echo $array_office_approve_status[$row['approve_status']]; ?></td>
      <td><?php echo $array_status[$row['goout_status']];; ?></td>
      <td><a href="employee_goout_info.php?id=<?php echo $gooutid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示:暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>