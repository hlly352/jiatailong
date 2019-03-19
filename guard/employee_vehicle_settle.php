<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`passby`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`odometer_start`,`db_vehicle_list`.`odometer_finish`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,`db_vehicle_list`.`wait_time`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`charge_toll`,`db_vehicle_list`.`confirmer_out`,`db_vehicle_list`.`confirmer_in`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`vehicle_status`,`db_vehicle`.`plate_number`,`db_vehicle`.`owner`,`db_vehicle`.`contact`,`db_department`.`dept_name`,`db_employee`.`employee_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outer`,`db_confirmer_in`.`employee_name` AS `confirmer_iner` FROM `db_vehicle_list` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid` = `db_vehicle_list`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid` = `db_vehicle_list`.`confirmer_in` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`listid` = '$listid' AND `db_vehicle_list`.`approve_status` = 'B' AND (`db_vehicle_list`.`reckoner` = 0 ||`db_vehicle_list`.`reckoner` = '$employeeid')";
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var vehicle_status = $("#vehicle_status").val();
		if(vehicle_status == 1){
			var start_time = $("#start_time").val();
			var finish_time = $("#finish_time").val();
			if(GetDateDiff(start_time,finish_time,'minute') < 30){
				alert('时间间隔太短');
				return false;
			}		
			var odometer_start = $("#odometer_start").val();
			if(!ri_b.test(odometer_start)){
				$("#odometer_start").focus();
				return false;
			}
			var odometer_finish = $("#odometer_finish").val();
			if(!ri_b.test(odometer_finish)){
				$("#odometer_finish").focus();
				return false;
			}
			var kilometres = $("#kilometres").val();
			if(!ri_a.test(kilometres)){
				$("#odometer_finish").focus();
				return false;
			}
			var wait_time = $("#wait_time").val();
			if(!rf_a.test(wait_time)){
				$("#waittime").focus();
				return false;
			}
			var confirmer_out = $("#confirmer_out").val();
			if(!confirmer_out){
				$("#confirmer_out").focus();
				return false;
			}
			var confirmer_in = $("#confirmer_in").val();
			if(!confirmer_in){
				$("#confirmer_out").focus();
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
		}
	})
	
	$("input[name^=employee]").keyup(function(){
		var employee_name = $(this).val();
		var employee_type = $(this).attr('id');
		if($.trim(employee_name)){
			$.post('../ajax_function/employee_name_all.php',{
				employee_name:employee_name
			},function(data,textstatus){
				$("#employee_"+employee_type).show();
				$("#employee_"+employee_type).html(data);
			})
		}else{
			$("#employee_"+employee_type).hide();
		}
	})
	$("SELECT[id^=employee]").dblclick(function(){
		var id = $(this).attr('id');
		var employee_name = $("#"+id+" option:selected").text();
		var employeeid = $("#"+id+" option:selected").val();
		if(employeeid != ''){
			$("input[name="+id+"]").val(employee_name);
			$(this).hide();
		}
	})
	
	$("#odometer_start").blur(function(){
		var odometer_start = $(this).val();
		var odometer_finish = $("#odometer_finish").val();
		if(ri_b.test(odometer_start) && ri_b.test(odometer_finish)){
			var kilometres = parseInt(odometer_finish) - parseInt(odometer_start);
			$("#kilometres").val(kilometres);
		}
	})
	$("#odometer_finish").blur(function(){
		var odometer_start = $("#odometer_start").val();
		var odometer_finish = $(this).val();
		if(ri_b.test(odometer_start) && ri_b.test(odometer_finish)){
			var kilometres = parseInt(odometer_finish) - parseInt(odometer_start);
			$("#kilometres").val(kilometres);
		}
	})
})
</script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
?>
<div id="table_sheet">
  <h4>用车结算</h4>
  <form action="employee_vehicle_settledo.php" name="employee_vehicle_settl" method="post">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><?php echo $array['vehicle_num']; ?></td>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
        <th>申请人：</th>
        <td><?php echo $array['employee_name']; ?></td>
        <th>申请日期：</th>
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><?php echo $array['departure']; ?></td>
        <th>目的地：</th>
        <td><?php echo $array['destination']; ?></td>
        <th>途径地：</th>
        <td><input type="text" name="passby" value="<?php echo $array['passby']; ?>" class="input_txt" /></td>
        <th>随车人员：</th>
        <td><?php echo $array['other']; ?></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><?php echo $array_vehicle_dotype[$array['dotype']]; ?></td>
        <th>路程方式：</th>
        <td><?php echo $array_vehicle_roundtype[$array['roundtype']]; ?></td>
        <th>预计出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>预计返厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>里程类型：</th>
        <td><?php echo $array_vehicle_pathtype[$array['pathtype']]; ?></td>
        <th>车辆车牌：</th>
        <td><?php echo $array['plate_number']; ?></td>
        <th>联系人：</th>
        <td><?php echo $array['owner']; ?></td>
        <th>联系电话：</th>
        <td><?php echo $array['contact']; ?></td>
      </tr>
      <tr>
        <th>出厂码表里程(Km)：</th>
        <td><input type="text" name="odometer_start" id="odometer_start" value="<?php echo $array['odometer_start']; ?>" class="input_txt" /></td>
        <th>进厂码表里程(Km)：</th>
        <td><input type="text" name="odometer_finish" id="odometer_finish" value="<?php echo $array['odometer_finish']; ?>" class="input_txt" /></td>
        <th>里程数(Km)：</th>
        <td><input type="text" name="kilometres" id="kilometres" value="<?php echo $array['kilometres']; ?>" class="input_txt" readonly="readonly" /></td>
        <th>等候时间(H)：</th>
        <td><input type="text" name="wait_time" id="wait_time" value="<?php echo $array['wait_time']; ?>" value="0" class="input_txt" /></td>
      </tr>
      <tr>
        <th>出厂经办人：</th>
        <td><input type="text" name="employee_confirmer_out" id="confirmer_out" value="<?php echo $array['confirmer_outer']; ?>" class="input_txt" />
          <br />
          <select name="confirmer_out" size="5" id="employee_confirmer_out" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
          <option value="<?php echo $array['confirmer_out']; ?>" selected="selected"></option>
          </select></td>
        <th>进厂经办人：</th>
        <td><input type="text" name="employee_confirmer_in" id="confirmer_in" value="<?php echo $array['confirmer_iner']; ?>" class="input_txt" />
          <br />
          <select name="confirmer_in" size="5" id="employee_confirmer_in" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
          <option value="<?php echo $array['confirmer_in']; ?>" selected="selected"></option>
          </select></td>
        <th>停车费(元)：</th>
        <td><input type="text" name="charge_parking" id="charge_parking" value="<?php echo $array['charge_parking'] ?>" class="input_txt" /></td>
        <th>过路费(元)：</th>
        <td><input type="text" name="charge_toll" id="charge_toll" value="<?php echo $array['charge_toll'] ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="vehicle_status" id="vehicle_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['vehicle_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td>用车事由：</td>
        <td colspan="5"><?php echo $array['cause']; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='employee_vehicle.php'" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="reckoner" value="<?php echo $accountid; ?>" /></td>
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