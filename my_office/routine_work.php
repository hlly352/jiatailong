<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m'); //查询月份
$month_days = date('t',strtotime($month."-01")); //查询月份天数
$month_firstday_week = date('w',strtotime($month."-01-01")); //查询月1号星期几
$pre_month = date('Y-m',strtotime($month." -1 month")); //上一个月
$pre_month_days = date('t',strtotime($pre_month."-01")); //上一个月天数
$month_endday = date('Y-m-d',strtotime($month."+1 month -1 day")); //查询月最后一天日期
$month_endday_week = date('w',strtotime($month_endday)); //查询月最后一天日期星期几
$next_month = date('Y-m',strtotime($month." +1 month")); //下一个月
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
          <input type="button" name="button" value="切换" class="button" onclick="location.href='routine_work_month.php#<?php echo date('Y-m-d'); ?>'" />
          <input type="button" name="button" value="例行工作" class="button" onclick="location.href='routine_work_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th style="background:#F00; color:#FFF; width:13%">星期日</th>
      <th width="14%">星期一</th>
      <th width="14%">星期二</th>
      <th width="15%">星期三</th>
      <th width="15%">星期四</th>
      <th width="15%">星期五</th>
      <th style="background:#F00; color:#FFF; width:14%">星期六</th>
    </tr>
    <tr>
      <?php
	  $num = $month_firstday_week+1; //上月天数格子数初始值
	  $pre_month_start_day = $pre_month_days - $month_firstday_week+1;
      for($a=$pre_month_start_day;$a<=$pre_month_days;$a++){ //上月天数格子输出，通过查询月第一天星期几判断数量
	      $day = date('Y-m-d',strtotime($pre_month.'-'.$a));
		  $week = date("w",strtotime($day));
		  $sql_work = "SELECT `db_routine_work`.`workid`,`db_routine_work`.`work_title`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND ((`db_routine_work`.`work_type` = 'A' AND FIND_IN_SET((DAYOFWEEK('$day')-1),`db_routine_work`.`work_week`) > 0) OR (`db_routine_work`.`work_type` = 'B' AND DATE_FORMAT('$day','%e') = `db_routine_work`.`work_month`) OR (`db_routine_work`.`work_type` = 'C' AND `db_routine_work`.`work_date` = '$day')) $sql_deptid";
		  $result_work = $db->query($sql_work);
		  if($result_work->num_rows){
			  $x = 1;
			  while($row_work = $result_work->fetch_assoc()){
				  $workid = $row_work['workid'];
				  $work_key = $workid.'-'.$day;
				  $count = array_key_exists($work_key,$array_update)?$array_update[$work_key]:0;
				  $href_style = $count?" style=\"color:#390\"":'';
				  $work_list .= "<br /><a href=\"routine_work_update.php?id=".$workid."&date=".$day ."\"".$href_style.">".$x.".".$row_work['work_title']."[".$row_work['dept_name']."]</a>";
				 $x++;
			  }
		  }
		  echo "<td style=\"text-align:left; vertical-align:top;\">".$day.$work_list."</td>";
		  $work_list = '';
	  }
	  ?>
      <?php
      for($i=1;$i<=$month_days;$i++){ //依据当月天数输出
	      $day = date('Y-m-d',strtotime($month.'-'.$i));
		  $week = date("w",strtotime($day));
		  $sql_work = "SELECT `db_routine_work`.`workid`,`db_routine_work`.`work_title`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND ((`db_routine_work`.`work_type` = 'A' AND FIND_IN_SET((DAYOFWEEK('$day')-1),`db_routine_work`.`work_week`) > 0) OR (`db_routine_work`.`work_type` = 'B' AND DATE_FORMAT('$day','%e') = `db_routine_work`.`work_month`) OR (`db_routine_work`.`work_type` = 'C' AND `db_routine_work`.`work_date` = '$day')) $sql_deptid";
		  $result_work = $db->query($sql_work);
		  if($result_work->num_rows){
			  $x = 1;
			  while($row_work = $result_work->fetch_assoc()){
				  $workid = $row_work['workid'];
				  $work_key = $workid.'-'.$day;
				  $count = array_key_exists($work_key,$array_update)?$array_update[$work_key]:0;
				  $href_style = $count?" style=\"color:#390\"":'';
				  $work_list .= "<br /><a href=\"routine_work_update.php?id=".$workid."&date=".$day ."\"".$href_style.">".$x.".".$row_work['work_title']."[".$row_work['dept_name']."]</a>";
				 $x++;
			  }
		  }
		  $td_bg = (date('Y-m-d') == $day)?" style=\"text-align:left; vertical-align:top; background:#FFC;\"":" style=\"text-align:left; vertical-align:top;\"";
		  echo "<td".$td_bg.">".$day.$work_list."</td>";
		  if($num%7 ==0){ //每7格子换行
			  echo "</tr></tr>";
		  }
		  $work_list = '';
		  $num++;
	  }
	  ?>
      <?php
      for($b=1;$b<=(6-$month_endday_week);$b++){ //下月天数格子输出，通过最后一天星期几判断数量
	      $day = date('Y-m-d',strtotime($next_month.'-'.$b));
		  $week = date("w",strtotime($day));
		  $sql_work = "SELECT `db_routine_work`.`workid`,`db_routine_work`.`work_title`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND ((`db_routine_work`.`work_type` = 'A' AND FIND_IN_SET((DAYOFWEEK('$day')-1),`db_routine_work`.`work_week`) > 0) OR (`db_routine_work`.`work_type` = 'B' AND DATE_FORMAT('$day','%e') = `db_routine_work`.`work_month`) OR (`db_routine_work`.`work_type` = 'C' AND `db_routine_work`.`work_date` = '$day')) $sql_deptid";
		  $result_work = $db->query($sql_work);
		  if($result_work->num_rows){
			  $x = 1;
			  while($row_work = $result_work->fetch_assoc()){
				  $workid = $row_work['workid'];
				  $work_key = $workid.'-'.$day;
				  $count = array_key_exists($work_key,$array_update)?$array_update[$work_key]:0;
				  $href_style = $count?" style=\"color:#390\"":'';
				  $work_list .= "<br /><a href=\"routine_work_update.php?id=".$workid."&date=".$day ."\"".$href_style.">".$x.".".$row_work['work_title']."[".$row_work['dept_name']."]</a>";
				 $x++;
			  }
		  }
		  echo "<td style=\"text-align:left; vertical-align:top;\">".$day.$work_list."</td>";
		  $work_list = '';
	  }
	  ?>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>