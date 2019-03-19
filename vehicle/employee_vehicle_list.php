<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01',strtotime("-1 month"));
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime(date('Y-m-01')."+1 month -1 day"));
if($_GET['submit']){
	$vehicle_num = trim($_GET['vehicle_num']);
	$apply_name = trim($_GET['apply_name']);
	$approve_status = $_GET['approve_status'];
	if($approve_status){
		$sql_approve_status = " AND `db_vehicle_list`.`approve_status` = '$approve_status'";
	}
	$vehicle_status = $_GET['vehicle_status'];
	if($vehicle_status != NULL){
		$sql_vehicle_status = " AND `db_vehicle_list`.`vehicle_status` = '$vehicle_status'";
	}
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_num` LIKE '%$vehicle_num%' AND `db_applyer`.`employee_name` LIKE '%$apply_name%' $sql_approve_status $sql_vehicle_status";
}else{
	$vehicle_status = 1;
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_status` = $vehicle_status";
}
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`vehicle_category`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,DATE_FORMAT(`db_vehicle_list`.`start_time`,'%m-%d %h:%i') AS `start_time`,DATE_FORMAT(`db_vehicle_list`.`finish_time`,'%m-%d %H:%i') AS `finish_time`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`pathtype`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,`db_vehicle_list`.`charge_toll`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`wait_time`,((`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`)*`db_vehicle_list`.`charge`+ROUND(`db_vehicle_list`.`wait_time`)*`db_vehicle_list`.`charge_wait`+`db_vehicle_list`.`charge_toll`+`db_vehicle_list`.`charge_parking`) AS `total_cost`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`vehicle_status`,`db_vehicle_list`.`vehicleid`,`db_vehicle`.`plate_number`,`db_vehicle_list`.`confirmer_out`,`db_vehicle_list`.`confirmer_in`,`db_vehicle_list`.`reckoner`,`db_vehicle_list`.`cause`,`db_applyer`.`employee_name` AS `applyer_name`,`db_department`.`dept_name`,`db_reckoner`.`employee_name` AS `reckoner_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outname`,`db_confirmer_in`.`employee_name` AS `confirmer_inname` FROM `db_vehicle_list` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` LEFT JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` LEFT JOIN `db_employee` AS `db_reckoner` ON `db_reckoner`.`employeeid`  = `db_vehicle_list`.`reckoner` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid`  = `db_vehicle_list`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid`  = `db_vehicle_list`.`confirmer_in` WHERE (`db_vehicle_list`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_allid = $db->query($sql);
$_SESSION['employee_vehicle_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_vehicle_list`.`listid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>用车数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><input type="text" name="vehicle_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applye_name" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>--</th>
        <td><input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>审批状态：</th>
        <td><select name="approve_status">
            <option value="">所有</option>
            <?php
			foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
				echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="vehicle_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $vehicle_status && $vehicle_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee_vehicle_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list" style="width:150%">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $array_listid .= $row_allid['listid'].',';
	  }
	  $array_listid = rtrim($array_listid,',');
	  $sql_cost = "SELECT SUM((`odometer_finish`-`odometer_start`)*`charge`+ROUND(`wait_time`)*`charge_wait`+`db_vehicle_list`.`charge_toll`+`db_vehicle_list`.`charge_parking`) AS `total_cost` FROM `db_vehicle_list` WHERE `listid` IN ($array_listid)";
	  $result_cost = $db->query($sql_cost);
	  $array_cost = $result_cost->fetch_assoc();
	  $all_total_cost = $array_cost['total_cost'];
  ?>
  <form action="employee_vehicledo.php" name="employee_vehicle_list" method="post">
    <table>
      <tr>
        <th width="2%">ID</th>
        <th width="4%">派车单号</th>
        <th width="4%">部门</th>
        <th width="3%">申请人</th>
        <th width="4%">申请日期</th>
        <th width="4%">用车类型</th>
        <th width="4%">车辆类型</th>
        <th width="3%">路程方式</th>
        <th width="5%">出发地</th>
        <th width="6%">目的地</th>
        <th width="14%">事由</th>
        <th width="4%">预计<br />
          出厂时间</th>
        <th width="4%">预计<br />
          返厂时间</th>
        <th width="4%">车辆车牌</th>
        <th width="3%">里程<br />
          类型</th>
        <th width="3%">总费用<br />
          (元)</th>
        <th width="3%">里程数<br />
          (Km)</th>
        <th width="3%">过路费<br />
          (元)</th>
        <th width="3%">停车费<br />
          (元)</th>
        <th width="3%">等候时间<br />
          (H)</th>
        <th width="3%">进厂<br />
          经办人</th>
        <th width="3%">出厂<br />
          经办人</th>
        <th width="3%">结算人</th>
        <th width="2%">审批</th>
        <th width="2%">状态</th>
        <th width="2%">Edit</th>
        <th width="2%">Info</th>
      </tr>
      <?php
	  while($row = $result->fetch_assoc()){
		  $listid = $row['listid'];
		  $plate_number = $row['vehicleid']?$row['plate_number']:'未派车';
		  $pathtype = array_key_exists($row['pathtype'],$array_vehicle_pathtype)?$array_vehicle_pathtype[$row['pathtype']]:'--';
		  $confirmer_outname = $row['confirmer_out']?$row['confirmer_outname']:'--';
		  $confirmer_inname = $row['confirmer_in']?$row['confirmer_inname']:'--';
		  $reckoner = $row['reckoner'];
		  $reckoner_name = $reckoner?$row['reckoner_name']:'--';
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php if($reckoner) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['vehicle_num']; ?></a></td>
        <td><?php echo $row['dept_name']; ?></td>
        <td><?php echo $row['applyer_name']; ?></td>
        <td><?php echo $row['apply_date']; ?></td>
        <td><?php echo $array_vehicle_dotype[$row['dotype']]; ?></td>
        <td><?php echo $array_vehicle_category[$row['vehicle_category']]; ?></td>
        <td><?php echo $array_vehicle_roundtype[$row['roundtype']]; ?></td>
        <td><?php echo $row['departure']; ?></td>
        <td><?php echo $row['destination']; ?></td>
        <td><?php echo $row['cause']; ?></td>
        <td><?php echo $row['start_time']; ?></td>
        <td><?php echo $row['finish_time']; ?></td>
        <td><?php echo $plate_number; ?></td>
        <td><?php echo $pathtype; ?></td>
        <td><?php echo $row['total_cost']; ?></td>
        <td><?php echo $row['kilometres']; ?></td>
        <td><?php echo $row['charge_toll']; ?></td>
        <td><?php echo $row['charge_parking']; ?></td>
        <td><?php echo $row['wait_time']; ?></td>
        <td><?php echo $confirmer_outname; ?></td>
        <td><?php echo $confirmer_inname; ?></td>
        <td><?php echo $reckoner_name; ?></td>
        <td><?php echo $array_office_approve_status[$row['approve_status']]; ?></td>
        <td><?php echo $array_status[$row['vehicle_status']]; ?></td>
        <td><?php if($row['reckoner'] != 0 && $row['approve_status'] == 'B' && $_SESSION['system_shell'][$system_dir]['isadmin']){ ?><a href="employee_vehicleae.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><a href="employee_vehicle_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="15">Total</td>
        <td><?php echo $all_total_cost; ?></td>
        <td colspan="12">&nbsp;</td>
      </tr>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
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