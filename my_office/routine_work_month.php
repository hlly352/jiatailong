<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$month_days = date('t',strtotime($month."-01"));
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
if($_GET['submit']){
	$deptid = $_GET['deptid'];
	if($deptid){
		$sql_deptid = " AND `db_routine_work`.`deptid` = '$deptid'";
	}
}
//统计月份
$sql_group = "SELECT `workid`,`update_date`,COUNT(*) AS `count` FROM `db_routine_work_update` WHERE DATE_FORMAT(`update_date`,'%Y-%m') = '$month' GROUP BY `update_date`,`workid`";
$result_group = $db->query($sql_group);
if($result_group->num_rows){
	while($row_group = $result_group->fetch_assoc()){
		$array_update[$row_group['workid'].'-'.$row_group['update_date']] = $row_group['count'];
	}
}else{
	$array_update = array();
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>例行工作</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>月份：</th>
        <td><input type="text" name="month" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" value="<?php echo $month; ?>" class="input_txt" /></td>
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
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="切换" class="button" onclick="location.href='routine_work.php'" />
          <input type="button" name="button" value="例行工作" class="button" onclick="location.href='routine_work_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="15%">日期</th>
      <th width="85%">例行工作</th>
    </tr>
    <?php
    for($i=1;$i<=$month_days;$i++){
		$day = date('Y-m-d',strtotime($month.'-'.$i));
		$week = date("w",strtotime($day));
		$day_week = ($week ==6 || $week ==0)?"<font color=red>星期".$array_week[$week]."</font>":"星期".$array_week[$week];
		$sql_work = "SELECT `db_routine_work`.`workid`,`db_routine_work`.`work_title`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND ((`db_routine_work`.`work_type` = 'A' AND FIND_IN_SET((DAYOFWEEK('$day')-1),`db_routine_work`.`work_week`) > 0) OR (`db_routine_work`.`work_type` = 'B' AND DATE_FORMAT('$day','%e') = `db_routine_work`.`work_month`) OR (`db_routine_work`.`work_type` = 'C' AND `db_routine_work`.`work_date` = '$day')) $sql_deptid";
		$result_work = $db->query($sql_work);
	?>
    <tr>
      <td<?php if(date('Y-m-d') == $day) echo " style=\"background:#06C; color:#FFF\""; ?>><a name="<?php echo $day; ?>" id="<?php echo $day; ?>"></a><?php echo $day."<br />".$day_week; ?></td>
      <td style="text-align:left;"><?php
      if($result_work->num_rows){
		  $a = 1;
		  while($row_work = $result_work->fetch_assoc()){
			  $workid = $row_work['workid'];
			  $work_key = $workid.'-'.$day;
			  $count = array_key_exists($work_key,$array_update)?$array_update[$work_key]:0;
			  $href_style = $count?" style=\"color:#390\"":'';
			  echo "<a href=\"routine_work_update.php?id=".$workid."&date=".$day ."\"".$href_style.">".$a.".".$row_work['work_title']."[".$row_work['dept_name']."]</a><br />";
			 $a++;
		  }
	  }
	  ?></td>
    </tr>
    <?php } ?>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>