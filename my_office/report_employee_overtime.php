<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$year = date('Y',strtotime($month));
//查询月份
$sql_month = "SELECT `applyer`,SUM(`overtime`) AS `total_time`,COUNT(*) AS `total_count` FROM `db_employee_overtime` WHERE  DATE_FORMAT(`start_time`,'%Y-%m') = '$month' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0 GROUP BY `applyer` ORDER BY SUM(`overtime`) ASC";
$result_month = $db->query($sql_month);
if($result_month->num_rows){
	while($row_month = $result_month->fetch_assoc()){
		$array_month[$row_month['applyer']] = array('time'=>$row_month['total_time'],'count'=>$row_month['total_count']);
	}
}else{
	$array_month = array();
}
//print_r($array_month);
$array_ordery = array_keys($array_month);
if($_GET['submit']){
	$applyer_name = trim($_GET['applyer_name']);
	$orderby = $_GET['orderby'];
	if($orderby == 'B'){
		$applyerid = fun_convert_checkbox($array_ordery);
		$sqlorderby = " ORDER BY field(`db_employee_overtime`.`applyer`,$applyerid) DESC";
	}elseif($orderby == 'A'){
		$sqlorderby = " ORDER BY SUM(`db_employee_overtime`.`overtime`) DESC";
	}
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$applyer_name%'";
}else{
	$sqlorderby = " ORDER BY SUM(`db_employee_overtime`.`overtime`) DESC";
}
$sql_year = "SELECT `db_employee_overtime`.`applyer`,`db_employee`.`employee_name`,SUM(`db_employee_overtime`.`overtime`) AS `total_time`,COUNT(*) AS `total_count` FROM `db_employee_overtime` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_overtime`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE DATE_FORMAT(`db_employee_overtime`.`start_time`,'%Y') = '$year' AND `db_employee_overtime`.`approve_status` = 'B' AND `db_employee_overtime`.`overtime_status` = 1 AND `db_employee_overtime`.`confirmer` != 0 $sqlwhere GROUP BY `db_employee_overtime`.`applyer`";
$result_year = $db->query($sql_year);
$pages = new page($result_year->num_rows,15);
$sqllist = $sql_year . $sqlorderby . $pages->limitsql;
$result_year = $db->query($sqllist);
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>加班报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>月份：</th>
        <td><input type="text" name="month" value="<?php echo $month; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>排序：</th>
        <td><select name="orderby">
            <option value="A">年度</option>
            <option value="B">月度</option>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="日报表" class="button" onclick="location.href='report_employee_overtime_day.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_year->num_rows){ ?>
  <table>
    <tr>
      <th width="5%" rowspan="2">ID</th>
      <th rowspan="2">员工</th>
      <th colspan="2">月度</th>
      <th colspan="2">年度</th>
    </tr>
    <tr>
      <th width="20%">小时(H)</th>
      <th width="20%">次数</th>
      <th width="20%">小时(H)</th>
      <th width="20%">次数</th>
    </tr>
    <?php
    while($row_year = $result_year->fetch_assoc()){
		$applyer = $row_year['applyer'];
		$month_totaltime = array_key_exists($applyer,$array_month)?$array_month[$applyer]['time']:0;
		$month_totalcount = array_key_exists($applyer,$array_month)?$array_month[$applyer]['count']:0;
	?>
    <tr>
      <td><?php echo $applyer; ?></td>
      <td><?php echo $row_year['employee_name']; ?></td>
      <td><?php echo $month_totaltime; ?></td>
      <td><?php echo $month_totalcount; ?></td>
      <td><?php echo $row_year['total_time']; ?></td>
      <td><?php echo $row_year['total_count']; ?></td>
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