<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "SELECT `db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`vehicle_category`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_vehicle_list`.`apply_date`,`db_vehicle_flow`.`approver`,`db_vehicle_flow`.`certigier`,`db_vehicle_flow`.`iscontrol`,`db_department`.`dept_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`employee_name` AS `agenter_name`,`db_vehicle_list`.`approve_status` FROM `db_vehicle_list` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_vehicle_list`.`agenter` INNER JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` WHERE `db_vehicle_list`.`listid` = '$listid' AND (`db_vehicle_flow`.`approver` = '$employeeid' OR `db_vehicle_flow`.`certigier` = '$employeeid') AND `db_vehicle_list`.`vehicle_status` = 1 AND `db_vehicle_list`.`approve_status` = 'A'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var iscontrol = $("#iscontrol").val();
		var approve_status = $("#approve_status").val();
		if(iscontrol == 1 && approve_status == 'B'){
			var pathtype = $("#pathtype").val();
			if(!pathtype){
				$("#pathtype").focus();
				return false;
			}
			var vehicleid = $("#vehicleid").val();
			if(!vehicleid){
				$("#vehicleid").focus();
				return false;
			}
		}
	})
})
</script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$certigier = $array['certigier'];
	$iscontrol = $array['iscontrol'];
	$approve_status = $array['approve_status'];
?>
<div id="table_sheet">
  <h4>用车审批</h4>
  <form action="employee_vehicle_approvedo.php" name="employee_vehicle_approve" method="post">
    <table>
      <tr>
        <th width="20%">派车单号：</th>
        <td width="80%"><?php echo $array['vehicle_num']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><?php echo $array['departure']; ?></td>
      </tr>
      <tr>
        <th>目的地：</th>
        <td><?php echo $array['destination']; ?></td>
      </tr>
      <tr>
        <th>预计出厂时间：</th>
        <td><?php echo $array['start_time']; ?></td>
      </tr>
      <tr>
        <th>预计返厂时间：</th>
        <td><?php echo $array['finish_time']; ?></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><?php echo $array_vehicle_dotype[$array['dotype']]; ?></td>
      </tr>
      <tr>
        <th>车辆类型：</th>
        <td><?php echo $array_vehicle_category[$array['vehicle_category']]; ?></td>
      </tr>
      <tr>
        <th>路程方式：</th>
        <td><?php echo $array_vehicle_roundtype[$array['roundtype']]; ?></td>
      </tr>
      <tr>
        <th>随车人员：</th>
        <td><?php echo $array['other']; ?></td>
      </tr>
      <tr>
        <th>用车事由：</th>
        <td><?php echo $array['cause']; ?></td>
      </tr>
      <?php
      if($iscontrol){
		  $sql_vehicle = "SELECT `vehicleid`,`plate_number`,`owner` FROM `db_vehicle` WHERE `vehicle_status` = 1 ORDER BY `vehicleid` ASC";
		  $result_vehicle = $db->query($sql_vehicle);
	  ?>
      <tr>
        <th>里程类型：</th>
        <td><select name="pathtype" id="pathtype">
            <option value="">请选择</option>
            <?php
            foreach($array_vehicle_pathtype as $pathtype_key=>$pathtype){
				echo "<option value=\"".$pathtype_key."\">".$pathtype."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>车辆车牌：</th>
        <td><select name="vehicleid" id="vehicleid">
            <option value="">请选择</option>
            <?php
            if($result_vehicle->num_rows){
				while($row_vehicle = $result_vehicle->fetch_assoc()){
					echo "<option value=\"".$row_vehicle['vehicleid']."\">".$row_vehicle['plate_number'].'-'.$row_vehicle['owner']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <?php } ?>
      <tr>
        <th>审批意见：</th>
        <td><input type="text" name="approve_content" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>审批状态：</th>
        <td><select name="approve_status" id="approve_status">
            <?php
			foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
				if($approve_status_key != $approve_status){
					echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="iscontrol" id="iscontrol" value="<?php echo $iscontrol; ?>" />
          <input type="hidden" name="certigier" value="<?php echo $certigier; ?>" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$listid' AND `db_office_approve`.`approve_type` = 'V' ORDER BY `db_office_approve`.`approveid` DESC";
$result_approve = $db->query($sql_approve);
?>
<div id="table_list">
  <?php if($result_approve->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">审批人</th>
      <th>审批意见</th>
      <th width="10%">审批状态</th>
      <th width="10%">审批时间</th>
    </tr>
    <?php while($row_approve = $result_approve->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_approve['approveid']; ?></td>
      <td><?php echo $row_approve['employee_name']; ?></td>
      <td><?php echo $row_approve['approve_content']; ?></td>
      <td><?php echo $array_office_approve_status[$row_approve['approve_status']]; ?></td>
      <td><?php echo $row_approve['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无审批记录！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>