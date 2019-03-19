<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
if($_GET['submit']){
	$position_name = trim($_GET['position_name']);
	$deptid = $_GET['deptid'];
	if($deptid){
		$sql_deptid = " AND `positionid` IN (SELECT `positionid` FROM `db_personnel_staffing` WHERE `deptid` = '$deptid' GROUP BY `positionid`)";
	}
	$isstaff = $_GET['isstaff'];
	if($isstaff != NULL){
		if($isstaff == 1){
			$sql_isstaff = " AND `positionid` IN (SELECT `positionid` FROM `db_personnel_staffing` GROUP BY `positionid`)";
		}elseif($isstaff == 0){
			$sql_isstaff = " AND `positionid` NOT IN (SELECT `positionid` FROM `db_personnel_staffing` GROUP BY `positionid`)";
		}
	}
	$sqlwhere = " AND `position_name` LIKE '%$position_name%' $sql_deptid $sql_isstaff";
}
$sql = "SELECT `positionid`,`position_name` FROM `db_personnel_position` WHERE `position_status` = 1 $sqlwhere";
$result = $db->query($sql);
$result_id_all = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `position_code` ASC,`positionid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
$resul_id = $db->query($sqllist);
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
  <h4>人员编制</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>职位：</th>
        <td><input type="text" name="position_name" class="input_txt" /></td>
        <th>年份：</th>
        <td><input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>部门：</th>
        <td><select name="deptid">
            <option value="">所有</option>
            <?php
            if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
					echo "<option value=\"".$row_dept['deptid']."\">".$row_dept['dept_name']."</option>";
				}
			}
		    ?>
          </select></td>
        <th>编制：</th>
        <td><select name="isstaff">
            <option value="">所有</option>
            <option value="1">有</option>
            <option value="0">无</option>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  //所有ID
	  while($row_positionid_all = $result_id_all->fetch_assoc()){
		  $array_positionid_all .= $row_positionid_all['positionid'].",";
	  }
	  $array_positionid_all = rtrim($array_positionid_all,',');
	  //统计按月份编制总数
	  $sql_total_month = "SELECT DATE_FORMAT(`month`,'%Y-%m') AS `month`,SUM(`quantity`) AS `quantity` FROM `db_personnel_staffing` WHERE DATE_FORMAT(`month`,'%Y') = '$year' AND `positionid` IN ($array_positionid_all) GROUP BY DATE_FORMAT(`month`,'%Y-%m')";
	  $result_total_month = $db->query($sql_total_month);
	  if($result_total_month->num_rows){
		  while($row_total_month = $result_total_month->fetch_assoc()){
			  $array_total_month[$row_total_month['month']] = $row_total_month['quantity'];
		  }
	  }else{
		  $array_total_month = array();
	  }
	  //每页ID
	  while($row_positionid = $resul_id->fetch_assoc()){
		  $array_positionid .= $row_positionid['positionid'].",";
	  }
	  $array_positionid = rtrim($array_positionid,',');
	  //统计按月份职位编制人数
	  $sql_position_month = "SELECT `positionid`,DATE_FORMAT(`month`,'%Y-%m') AS `month`,SUM(`quantity`) AS `quantity` FROM `db_personnel_staffing` WHERE DATE_FORMAT(`month`,'%Y') = '$year' AND `positionid` IN ($array_positionid) GROUP BY `positionid`,DATE_FORMAT(`month`,'%Y-%m')";
	  $result_position_month = $db->query($sql_position_month);
	  if($result_position_month->num_rows){
		  while($row_position_month = $result_position_month->fetch_assoc()){
			  $array_position_month[$row_position_month['positionid'].'-'.$row_position_month['month']] = $row_position_month['quantity'];
		  }
	  }else{
		  $array_position_month = array();
	  }
  ?>
  <table>
    <tr>
      <th id="4%">ID</th>
      <th width="12%">职位</th>
      <?php
      for($i=1;$i<=12;$i++){
		  echo "<th>".$i."月</th>";
	  }
	  ?>
      <th width="4%">Edit</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		 $positionid = $row['positionid'];
	?>
    <tr>
      <td><?php echo $positionid; ?></td>
      <td><?php echo $row['position_name']; ?></td>
      <?php
      for($i=1;$i<=12;$i++){
		  $position_month_key = $positionid."-".date('Y-m',strtotime($year.'-'.$i));
		  $position_month_quantity = array_key_exists($position_month_key,$array_position_month)?$array_position_month[$position_month_key]:0;
		  echo "<td>".$position_month_quantity."</td>";
	  }
	  ?>
      <td><a href="staffingae.php?id=<?php echo $positionid; ?>&year=<?php echo $year; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="staffing_info.php?id=<?php echo $positionid; ?>&year=<?php echo $year; ?>" target="_blank"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="2">Total</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $total_month = date('Y-m',strtotime($year.'-'.$i));
		  $total_month_quantity = array_key_exists($total_month,$array_total_month)?$array_total_month[$total_month]:0;
		  echo "<td>".$total_month_quantity."</td>";
	  }
	  ?>
      <td colspan="2">&nbsp;</td>
    </tr>
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