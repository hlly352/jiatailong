<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//假期
$sql_vacation = "SELECT `vacationid`,`vacation_name` FROM `db_personnel_vacation` WHERE `vacation_status` = 1 ORDER BY `vacationid` ASC";
$result_vacation = $db->query($sql_vacation);
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
<script language="javascript" type="text/javascript">
$(function(){
	var action = $("#action").val();
	if(action == "edit"){
		$("#leavetime").focus();
	}
	$("#submit").click(function(){
		var vacationid = $("#vacationid").val();
		if(!vacationid){
			$("#vacationid").focus();
			return false;
		}
		var work_shift = $("#work_shift").val();
		if(!work_shift){
			$("#work_shift").focus();
			return false;
		}
		var start_time = $("#start_time").val();
		var finish_time = $("#finish_time").val();
		if(GetDateDiff(start_time,finish_time,'minute') < 30){
			alert('开始与结束时间间隔最小为30分钟，请重新输入！');
			return false;
		}
		var leavetime = $("#leavetime").val();
		if(!rf_b.test(leavetime)){
			$("#leavetime").focus();
			return false;
		}
	})
	$("#applyer").change(function(){
		var applyer = $(this).val();
		if(applyer){
			$.post("../ajax_function/employee_overtime_valid.php",{
				   applyer:applyer
			},function(data,textStatus){
				$("#vacationid").html(data);
			})
		}
	})
	$("#leavetime").blur(function(){
		var leavetime = $(this).val();
		var applyer = $("#applyer").val();
		var vacationid = $("#vacationid").val();
		if(vacationid == 2){
			$.post("../ajax_function/employee_overtime_check.php",{
				   leavetime:leavetime,
				   applyer:applyer
			},function(data,textStatus){
				if(data == 0){
					alert('请假时间超出可调休时间');
					$("#leavetime").val(0);
				}
			})
		}
	})
	$("#vacationid").change(function(){
		var vacationid = $(this).val();
		var applyer = $("#applyer").val();
		var leavetime = $("#leavetime").val();
		if(vacationid == 2 && ri_b.test(leavetime)){
			$.post("../ajax_function/employee_overtime_check.php",{
				   leavetime:leavetime,
				   applyer:applyer
			},function(data,textStatus){
				if(data == 0){
					alert('请假时间超出可调休时间');
					$("#leavetime").val(0);
				}
			})
		}
	})
})
</script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $employee_name = $_SESSION['employee_info']['employee_name'];
	  //员工的下属
	  $sql_employee = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `superior` = '$employeeid' AND `employee_status`= 1 AND `account_status` = 0 ORDER BY CONVERT(`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
	  $result_employee = $db->query($sql_employee);
	  //员工的加班时间
	  $sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$employeeid' AND `overtime_status` = 1 AND `approve_status` = 'B' AND `confirmer` != 0";
	  $result_overtime = $db->query($sql_overtime);
	  if($result_overtime->num_rows){
		  while($row_overtime = $result_overtime->fetch_assoc()){
			  $overtime_valid += $row_overtime['overtime_valid'];
		  }
	  }else{
		  $overtime_valid = 0;
	  }
  ?>
  <h4>请假单申请</h4>
  <form action="employee_leavedo.php" name="employee_leave" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><select name="applyer" id="applyer">
            <option value="<?php echo $employeeid; ?>"><?php echo $employee_name; ?></option>
            <?php
			if($result_employee->num_rows){
				while($row_employee = $result_employee->fetch_assoc()){
					echo "<option value=\"".$row_employee['employeeid']."\">".$row_employee['employee_name']."</option>";
				}
			}
			?>
          </select>
          <span class="tag"> *如需代理申请请下拉选择</span></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>请假类型：</th>
        <td><select name="vacationid" id="vacationid">
            <option value="">请选择</option>
            <?php
            if($result_vacation->num_rows){
				while($row_vacation = $result_vacation->fetch_assoc()){
			?>
            <option value="<?php echo $row_vacation['vacationid']; ?>"<?php if($row_vacation['vacationid'] == 2 && $overtime_valid == 0)echo " disabled=\"disabled\""; ?>><?php echo $row_vacation['vacation_name']; ?>
            <?php if($row_vacation['vacationid'] == 2) echo "(".$overtime_valid."小时)"; ?>
            </option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>班次：</th>
        <td><select name="work_shift" id="work_shift">
            <option value="">请选择</option>
            <?php
            foreach($array_work_shift as $work_shift_key=>$work_shift_value){
				echo "<option value=\"".$work_shift_key."\">".$work_shift_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>开始时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo date('Y-m-d H:i:00'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>结束时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo date('Y-m-d H:i:s',strtotime(date('H:i:00').'+1 hours')); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>请假时间(H)：</th>
        <td><input type="text" name="leavetime" id="leavetime" class="input_txt" />
          <span class="tag"> *请准确计算请假小时数后输入(只计算工作时间)</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>私事请假后不用再填写出门申请</td>
      </tr>
      <tr>
        <th>请假事由：</th>
        <td><textarea name="leave_cause" id="leave_cause" cols="50" rows="3" class="input_txt"></textarea></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="agenter" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $leaveid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_employee_leave`.`leave_num`,`db_employee_leave`.`applyer`,`db_employee_leave`.`apply_date`,`db_employee_leave`.`work_shift`,`db_employee_leave`.`start_time`,`db_employee_leave`.`finish_time`,`db_employee_leave`.`leavetime`,`db_employee_leave`.`leave_cause`,`db_employee_leave`.`vacationid`,`db_employee`.`employee_name` FROM `db_employee_leave` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_leave`.`applyer` WHERE `db_employee_leave`.`leaveid` = '$leaveid' AND (`db_employee_leave`.`applyer` = '$employeeid' OR `db_employee_leave`.`agenter` = '$employeeid') AND `db_employee_leave`.`approve_status` = 'C'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $applyer = $array['applyer'];
		  //员工的加班时间
		  $sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$applyer' AND `overtime_status` = 1 AND `confirmer` != 0";
		  $result_overtime = $db->query($sql_overtime);
		  if($result_overtime->num_rows){
			  while($row_overtime = $result_overtime->fetch_assoc()){
				  $over_validtime += $row_overtime['overtime_valid'];
			  }
		  }else{
			  $over_validtime = 0;
		  }
  ?>
  <h4>请假单修改</h4>
  <form action="employee_leavedo.php" name="employee_leave" method="post">
    <table>
      <tr>
        <th width="20%">请假单号：</th>
        <td width="80%"><?php echo $array['leave_num']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>请假类型：</th>
        <td><select name="vacationid" id="vacationid">
            <?php
            if($result_vacation->num_rows){
				while($row_vacation = $result_vacation->fetch_assoc()){
			?>
            <option value="<?php echo $row_vacation['vacationid']; ?>"<?php if($row_vacation['vacationid'] == $array['vacationid']) echo " selected=\"selected\""; ?><?php if($row_vacation['vacationid'] == 2 && $over_validtime == 0)echo " disabled=\"disabled\""; ?>><?php echo $row_vacation['vacation_name']; ?>
            <?php if($row_vacation['vacationid'] == 2) echo "(".$over_validtime."小时)"; ?>
            </option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>班次：</th>
        <td><select name="work_shift" id="work_shift">
            <?php foreach($array_work_shift as $work_shift_key=>$work_shift_value){ ?>
            <option value="<?php echo $work_shift_key; ?>"<?php if($work_shift_key == $array['work_shift']) echo " selected=\"selected\""; ?>><?php echo $work_shift_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>开始时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>结束时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>请假时间(H)：</th>
        <td><input type="text" name="leavetime" id="leavetime" value="<?php echo $array['leavetime']; ?>" class="input_txt" />
          <span class="tag"> *请准确计算请假小时数后输入(只计算工作时间)</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>私事请假后不用再填写出门申请</td>
      </tr>
      <tr>
        <th>请假事由：</th>
        <td><textarea name="leave_ause" id="leave_cause" cols="50" rows="3" class="input_txt"><?php echo $array['leave_cause']; ?></textarea></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="applyer" value="<?php echo $applyer; ?>" />
          <input type="hidden" name="leaveid" value="<?php echo $leaveid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>