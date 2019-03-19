<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if(!$_SESSION['system_shell'][$system_dir]['isadmin']){
	die("访问被拒绝，请与管理员联系！");
}
$listid = fun_check_int($_GET['id']);
//查询车辆
$sql_vehicle = "SELECT `vehicleid`,`plate_number`,`owner` FROM `db_vehicle` WHERE `vehicle_status` = 1 ORDER BY `vehicleid` ASC";
$result_vehicle = $db->query($sql_vehicle);
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`passby`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`charge`,`db_vehicle_list`.`charge_wait`,`db_vehicle_list`.`charge_wait`,`db_vehicle_list`.`odometer_start`,`db_vehicle_list`.`odometer_finish`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,`db_vehicle_list`.`charge_toll`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`wait_time`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`vehicle_status`,`db_vehicle_list`.`vehicleid`,`db_vehicle`.`plate_number`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_vehicle_list` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` LEFT JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`listid` = '$listid' AND `db_vehicle_list`.`approve_status` = 'B' AND `db_vehicle_list`.`reckoner` != 0";
$result = $db->query($sql);
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
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var charge = $("#charge").val();
		if(!rf_a.test(charge)){
			$("#charge").focus();
			return false;
		}
		var charge_wait = $("#charge_wait").val();
		if(!rf_a.test(charge_wait)){
			$("#charge_wait").focus();
			return false;
		}
		var departure = $("#departure").val();
		if(!$.trim(departure)){
			$("#departure").focus();
			return false;
		}
		var destination = $("#destination").val();
		if(!$.trim(destination)){
			$("#destination").focus();
			return false;
		}
		var start_time = $("#start_time").val();
		var finish_time = $("#finish_time").val();
		if(GetDateDiff(start_time,finish_time,'minute') < 30){
			alert('出厂与回厂时间间隔最小为30分钟，请重新输入！');
			return false;
		}
		var odometer_start = $("#odometer_start").val();
		if(!ri_a.test(odometer_start)){
			$("#odometer_start").focus();
			return false;
		}
		var odometer_finish = $("#odometer_finish").val();
		if(!ri_a.test(odometer_finish)){
			$("#odometer_finish").focus();
			return false;
		}
		var kilometres = $("#kilometres").val();
		if(!ri_a.test(kilometres)){
			$("#odometer_finish").focus();
			return false;
		}
		var charge_parking = $("#charge_parking").val();
		if(!ri_a.test(charge_parking)){
			$("#charge_parking").focus();
			return false;
		}
		var charge_toll = $("#charge_toll").val();
		if(!ri_a.test(charge_toll)){
			$("#charge_toll").focus();
			return false;
		}
		var wait_time = $("#wait_time").val();
		if(!rf_a.test(wait_time)){
			$("#wait_time").focus();
			return false;
		}
		var other = $("#other").val();
		if(!$.trim(other)){
			$("#other").focus();
			return false;
		}
	})
	$("#vehicleid").change(function(){
		var vehicleid = $(this).val();
		var pathtype = $("#pathtype").val();
		if(vehicleid && pathtype){
			$.post("../ajax_function/vehicle_charge.php",{
				vehicleid:vehicleid,
				pathtype:pathtype
			},function(data,textStatus){
				$array_data = data.split('#');
				charge = $array_data[0];
				charge_wait = $array_data[1];
				$("#charge").val(charge);
				$("#charge_wait").val(charge_wait);
			})
		}else{
			$("#charge").val(0);
			$("#charge_wait").val(0);
		}
	})
	$("#pathtype").change(function(){
		var pathtype = $(this).val();
		var vehicleid = $("#vehicleid").val();
		if(vehicleid && pathtype){
			$.post("../ajax_function/vehicle_charge.php",{
				vehicleid:vehicleid,
				pathtype:pathtype
			},function(data,textStatus){
				$array_data = data.split('#');
				charge = $array_data[0];
				charge_wait = $array_data[1];
				$("#charge").val(charge);
				$("#charge_wait").val(charge_wait);
			})
		}else{
			$("#charge").val(0);
			$("#charge_wait").val(0);
		}
	})
	$("#odometer_start").blur(function(){
		var odometer_start = $(this).val();
		var odometer_finish = $("#odometer_finish").val();
		if(ri_a.test(odometer_start) && ri_a.test(odometer_finish)){
			var kilometres = parseInt(odometer_finish) - parseInt(odometer_start);
			$("#kilometres").val(kilometres);
		}
	})
	$("#odometer_finish").blur(function(){
		var odometer_start = $("#odometer_start").val();
		var odometer_finish = $(this).val();
		if(ri_a.test(odometer_start) && ri_a.test(odometer_finish)){
			var kilometres = parseInt(odometer_finish) - parseInt(odometer_start);
			$("#kilometres").val(kilometres);
		}
	})
})
</script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php 
  if($result->num_rows){
	  $array = $result->fetch_assoc();
  ?>
  <h4>用车数据修改</h4>
  <form action="employee_vehicledo.php" name="employee_vehicle" method="post">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>派车单号：</th>
        <td><?php echo $array['vehicle_num']; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><select name="dotype" id="dotype">
            <?php foreach($array_vehicle_dotype as $dotype_key=>$dotype){ ?>
            <option value="<?php echo $dotype_key; ?>"<?php if($dotype_key == $array['dotype']) echo " selected=\"selected\""; ?>><?php echo $dotype; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>车辆车牌：</th>
        <td><select name="vehicleid" id="vehicleid">
            <?php
            if($result_vehicle->num_rows){
				while($row_vehicle = $result_vehicle->fetch_assoc()){
			?>
            <option value="<?php echo $row_vehicle['vehicleid']; ?>"<?php if($row_vehicle['vehicleid'] == $array['vehicleid']) echo " selected=\"selected\""; ?>><?php echo $row_vehicle['plate_number'].'-'.$row_vehicle['owner']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>里程类型：</th>
        <td><select name="pathtype" id="pathtype">
            <?php foreach($array_vehicle_pathtype as $pathtype_key=>$pathtype){ ?>
            <option value="<?php echo $pathtype_key; ?>"<?php if($pathtype_key == $array['pathtype']) echo " selected=\"selected\""; ?>><?php echo $pathtype; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>计费单价(元/公里)：</th>
        <td><input type="text" name="charge" id="charge" class="input_txt" value="<?php echo $array['charge']; ?>" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>等候单价(元/小时)：</th>
        <td><input type="text" name="charge_wait" id="charge_wait" class="input_txt" value="<?php echo $array['charge_wait']; ?>" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><input type="text" name="departure" id="departure" value="<?php echo $array['departure']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>目的地：</th>
        <td><input type="text" name="destination" id="destination" value="<?php echo $array['destination']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>途径地：</th>
        <td><input type="text" name="passby" value="<?php echo $array['passby']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>路程方式：</th>
        <td><select name="roundtype" id="roundtype">
            <?php foreach($array_vehicle_roundtype as $roundtype_key=>$roundtype){ ?>
            <option value="<?php echo $roundtype_key; ?>"<?php if($roundtype_key == $array['roundtype']) echo " selected=\"selected\""; ?>><?php echo $roundtype; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>预计出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>预计返厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>出厂码表里程(Km)</th>
        <td><input type="text" name="odometer_start" id="odometer_start" value="<?php echo $array['odometer_start']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>进厂码表里程(Km)</th>
        <td><input type="text" name="odometer_finish" id="odometer_finish" value="<?php echo $array['odometer_finish']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>里程数(Km)</th>
        <td><input type="text" name="kilometres" id="kilometres" value="<?php echo $array['kilometres']; ?>" class="input_txt" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>过路费(元)</th>
        <td><input type="text" name="charge_toll" id="charge_toll" value="<?php echo $array['charge_toll']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>停车费(元)</th>
        <td><input type="text" name="charge_parking" id="charge_parking" value="<?php echo $array['charge_parking']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>等候时间(H)</th>
        <td><input type="text" name="wait_time" id="wait_time" value="<?php echo $array['wait_time']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>随车人员：</th>
        <td><input type="text" name="other" id="other" value="<?php echo $array['other']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>用车事由：</th>
        <td><input type="text" name="cause" value="<?php echo $array['cause']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>申请状态：</th>
        <td><select name="vehicle_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['vehicle_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>审核状态：</th>
        <td><?php echo $array_office_approve_status[$array['approve_status']]; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" />
          <input type="hidden" name="action" value="edit" /></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>