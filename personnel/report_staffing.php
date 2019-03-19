<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$year = date('Y',strtotime($month));
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
//职位
$sql_position = "SELECT `positionid`,`position_name` FROM `db_personnel_position` WHERE `position_status` = 1 ORDER BY `position_code` ASC,`positionid` ASC";
$result_position = $db->query($sql_position);
$result_position_all = $db->query($sql_position);
if($result_position->num_rows){
	while($row_position = $result_position->fetch_assoc()){
		$array_position_name[$row_position['positionid']] = $row_position['position_name'];
	}
}else{
	$array_position_name = array();
}
//统计预算部门有多少职位
$sql_position_a = "SELECT `db_personnel_staffing`.`deptid`,GROUP_CONCAT(DISTINCT(`db_personnel_staffing`.`positionid`) ORDER BY `db_personnel_position`.`position_code` ASC) AS `positionid` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND DATE_FORMAT(`month`,'%Y') = '$year' GROUP BY `db_personnel_staffing`.`deptid`";
$result_position_a = $db->query($sql_position_a);
if($result_position_a->num_rows){
	while($row_position_a = $result_position_a->fetch_assoc()){
		$array_position_a[$row_position_a['deptid']] = $row_position_a['positionid'];
	}
}else{
	$array_position_a = array();
}
//统计实际部门有多少职位
$sql_position_b = "SELECT `db_employee`.`deptid`,GROUP_CONCAT(DISTINCT(`db_employee`.`positionid`) ORDER BY `db_personnel_position`.`position_code` ASC) AS `positionid` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND `db_employee`.`employee_status` = 1 GROUP BY `db_employee`.`deptid`";
$result_position_b = $db->query($sql_position_b);
if($result_position_b->num_rows){
	while($row_position_b = $result_position_b->fetch_assoc()){
		$array_position_b[$row_position_b['deptid']] = $row_position_b['positionid'];
	}
}else{
	$array_position_b = array();
}
//预算按部门+职位+月份
$sql_dpms = "SELECT `db_personnel_staffing`.`deptid`,`db_personnel_staffing`.`positionid`,`db_personnel_staffing`.`month`,`db_personnel_staffing`.`quantity` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y') = '$year' AND `db_personnel_staffing`.`quantity` > 0";
$result_dpms = $db->query($sql_dpms);
if($result_dpms->num_rows){
	while($row_dpms = $result_dpms->fetch_assoc()){
		$array_dpms[$row_dpms['deptid'].'-'.$row_dpms['positionid'].'-'.$row_dpms['month']] = $row_dpms['quantity'];
	}
}else{
	$array_dpms = array();
}
//查询月预算部门+职位
$sql_dps = "SELECT `db_personnel_staffing`.`deptid`,`db_personnel_staffing`.`positionid`,`db_personnel_staffing`.`month`,`db_personnel_staffing`.`quantity` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y-%m') = '$month' AND `db_personnel_staffing`.`quantity` > 0";
$result_dps = $db->query($sql_dps);
if($result_dps->num_rows){
	while($row_dps = $result_dps->fetch_assoc()){
		$array_dps[$row_dps['deptid'].'-'.$row_dps['positionid']] = $row_dps['quantity'];
	}
}else{
	$array_dps = array();
}
//实际部门+职位
$sql_dpn = "SELECT `db_employee`.`deptid`,`db_employee`.`positionid`,COUNT(*) AS `count` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND `db_employee`.`employee_status` = 1 GROUP BY `db_employee`.`deptid`,`db_employee`.`positionid`";
$result_dpn = $db->query($sql_dpn);
if($result_dpn->num_rows){
	while($row_bpn= $result_dpn->fetch_assoc()){
		$array_dpn[$row_bpn['deptid'].'-'.$row_bpn['positionid']] = $row_bpn['count'];
	}
}else{
	$array_dpn = array();
}
//预算职位+月份
$sql_pms = "SELECT `db_personnel_staffing`.`positionid`,`db_personnel_staffing`.`month`,SUM(`db_personnel_staffing`.`quantity`) AS `quantity` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y') = '$year' AND `db_personnel_staffing`.`quantity` > 0 GROUP BY `db_personnel_staffing`.`positionid`,`db_personnel_staffing`.`month`";
$result_pms = $db->query($sql_pms);
if($result_pms->num_rows){
	while($row_pms = $result_pms->fetch_assoc()){
		$array_pms[$row_pms['positionid'].'-'.$row_pms['month']] = $row_pms['quantity'];
	}
}else{
	$array_pms = array();
}
//查询月预算职位+月份
$sql_ps = "SELECT `db_personnel_staffing`.`positionid`,SUM(`db_personnel_staffing`.`quantity`) AS `quantity` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y-%m') = '$month' AND `db_personnel_staffing`.`quantity` > 0 GROUP BY `db_personnel_staffing`.`positionid`";
$result_ps = $db->query($sql_ps);
if($result_ps->num_rows){
	while($row_ps = $result_ps->fetch_assoc()){
		$array_ps[$row_ps['positionid']] = $row_ps['quantity'];
	}
}else{
	$array_ps = array();
}
$sql_pn = "SELECT `db_employee`.`positionid`,COUNT(*) AS `count` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND `db_employee`.`employee_status` = 1 GROUP BY `db_employee`.`positionid`";
$result_pn = $db->query($sql_pn);
if($result_pn->num_rows){
	while($row_pn= $result_pn->fetch_assoc()){
		$array_pn[$row_pn['positionid']] = $row_pn['count'];
	}
}else{
	$array_pn = array();
}
$sql_pmts = "SELECT `db_personnel_staffing`.`month`,SUM(`db_personnel_staffing`.`quantity`) AS `quantity` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_personnel_staffing`.`positionid` WHERE `db_department`.`dept_status` = 1 AND `db_personnel_position`.`position_status` = 1 AND DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y') = '$year' AND `db_personnel_staffing`.`quantity` > 0 GROUP BY `db_personnel_staffing`.`month`";
$result_pmts = $db->query($sql_pmts);
if($result_pmts->num_rows){
	while($row_pmts = $result_pmts->fetch_assoc()){
		$array_pmts[$row_pmts['month']] = $row_pmts['quantity'];
	}
}else{
	$array_pmts = array();
}
//查询员工总数(实际)
$sql_total = "SELECT * FROM `db_employee` WHERE `employee_status` = 1";
$result_total= $db->query($sql_total);
$total_quantity =($result_total->num_rows)?$result_total->num_rows:0; 
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
  <h4>编制报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>月份：</th>
        <td><input type="text" name="month" value="<?php echo $month; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result_dept->num_rows){
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">部门</th>
      <th width="10%">职位</th>
      <?php
      for($i=1;$i<=12;$i++){
		  $month_key = date('Y-m',strtotime($year.'-'.$i));
		  if($month_key == $month){
			  echo "<th style=\"background:#06F; color:#FFF\">".$i."月</th>";
		  }else{
			   echo "<th>".$i."月</th>";
		  }
	  }
	  ?>
      <th>编制/实际</th>
      <th>占比(%)</th>
      <th>差异</th>
      <th width="4%">备注</th>
    </tr>
    <?php
    while($row_dept = $result_dept->fetch_assoc()){
		$deptid = $row_dept['deptid'];
		$position_a = array_key_exists($deptid,$array_position_a)?$array_position_a[$deptid]:'';
		$position_b = array_key_exists($deptid,$array_position_b)?$array_position_b[$deptid]:'';
		$position = $array_position_a[$deptid].','.$array_position_b[$deptid];
		$position = trim($position,',');
		$array_position = explode(',',$position);
		$array_position = array_values(array_unique($array_position));
		$rowspan = count($array_position)?count($array_position):1;
	?>
    <tr>
      <td rowspan="<?php echo $rowspan; ?>"><?php echo $deptid; ?></td>
      <td rowspan="<?php echo $rowspan; ?>"><?php echo $row_dept['dept_name']; ?></td>
      <?php
      for($i=0;$i<$rowspan;$i++){
		  if($i != 0) echo "<tr>";
		  $positionid = $array_position[$i];
		  $position_name = array_key_exists($positionid,$array_position_name)?$array_position_name[$positionid]:'--';
		  //月份与实际
		  $dp = $deptid.'-'.$positionid;
		  $dps_quantity = array_key_exists($dp,$array_dps)?$array_dps[$dp]:'0';
		  $dpn_quantity = array_key_exists($dp,$array_dpn)?$array_dpn[$dp]:'0';
		  $diff_quantity = $dpn_quantity - $dps_quantity;
		  if($diff_quantity > 0){
			  $diff_quantity_bg = " style=\"background:#F00\"";
		  }elseif($diff_quantity < 0){
			  $diff_quantity_bg = " style=\"background:#39F\"";
		  }else{
			  $diff_quantity_bg = '';
		  }
      ?>
      <td><?php echo $position_name; ?></td>
      <?php
      for($a=1;$a<=12;$a++){
		  $dpm = $deptid.'-'.$positionid.'-'.date('Y-m',strtotime($year.'-'.$a)).'-01';
		  $dpms_qunatity = array_key_exists($dpm,$array_dpms)?$array_dpms[$dpm]:'0';
		  echo "<td>".$dpms_qunatity."</td>";
	  }
	  ?>
      <td><?php echo $dps_quantity.'/'.$dpn_quantity; ?></td>
      <td><?php echo round(@(($dps_quantity)/array_sum($array_dps)*100),1).'%/'.round(@(($dpn_quantity)/array_sum($array_dpn)*100),1).'%'; ?></td>
      <td<?php echo $diff_quantity_bg; ?>><?php echo $diff_quantity; ?></td>
      <td><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></td>
    </tr>
    <?php } ?>
    <?php } ?>
    <?php
    if($result_position_all->num_rows){
		while($row_position_all = $result_position_all->fetch_assoc()){
			$postionidid_ps = $row_position_all['positionid'];
			$ps_quantity = array_key_exists($postionidid_ps,$array_ps)?$array_ps[$postionidid_ps]:'0';
			$pn_quantity = array_key_exists($postionidid_ps,$array_pn)?$array_pn[$postionidid_ps]:'0';
			$diff_quantity_p = $pn_quantity - $ps_quantity;
			if($diff_quantity_p > 0){
				$diff_quantity_bg_p = " style=\"background:#F00\"";
			}elseif($diff_quantity_p < 0){
				$diff_quantity_bg_p = " style=\"background:#39F\"";
			}else{
				$diff_quantity_bg_p = '';
			}
	?>
    <tr>
      <td colspan="3"><?php echo $row_position_all['position_name']; ?></td>
      <?php
      for($b=1;$b<=12;$b++){
		  $pm = $postionidid_ps.'-'.date('Y-m',strtotime($year.'-'.$b)).'-01';
		  $pms_quantity = array_key_exists($pm,$array_pms)?$array_pms[$pm]:'0';
		  echo "<td>".$pms_quantity."</td>";
	  }
	  ?>
      <td><?php echo $ps_quantity.'/'.$pn_quantity; ?></td>
      <td><?php echo round(@(($ps_quantity)/array_sum($array_ps)*100),1).'%/'.round(@(($pn_quantity)/array_sum($array_pn)*100),1).'%'; ?></td>
      <td<?php echo $diff_quantity_bg_p; ?>><?php echo $diff_quantity_p; ?></td>
      <td>--</td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="3">Total</td>
      <?php
	  $search_month = $month.'-01';
	  $month_pmts_quantity = array_key_exists($search_month,$array_pmts)?$array_pmts[$search_month]:'0';
	  $diff_quantity_t = $total_quantity - $month_pmts_quantity;
	  if($diff_quantity_t > 0){
		  $diff_quantity_bg_t = " style=\"background:#F00\"";
	  }elseif($diff_quantity_t < 0){
		  $diff_quantity_bg_t = " style=\"background:#39F\"";
	  }else{
		  $diff_quantity_bg_t = '';
	  }
      for($c=1;$c<=12;$c++){
		  $pmts_key = date('Y-m',strtotime($year.'-'.$c)).'-01';
		  $pmts_quantity = array_key_exists($pmts_key,$array_pmts)?$array_pmts[$pmts_key]:'0';
		  echo "<td>".$pmts_quantity."</td>";
	  }
	  ?>
      <td><?php echo $month_pmts_quantity.'/'.$total_quantity; ?></td>
      <td>--</td>
      <td<?php echo $diff_quantity_bg_t; ?>><?php echo $diff_quantity_t; ?></td>
      <td>--</td>
    </tr>
    <?php } ?>
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