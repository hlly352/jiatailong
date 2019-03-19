<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$leaveid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$employee_name = $_SESSION['employee_info']['employee_name'];
//读取该假期
$sql_vacation = "SELECT `vacationid`,`vacation_name` FROM `db_personnel_vacation` WHERE `vacation_status` = 1 ORDER BY `vacationid` ASC";
$result_vacation = $db->query($sql_vacation);
$sql = "SELECT `db_employee_leave`.`leaveid`,`db_employee_leave`.`applyer`,`db_employee_leave`.`leave_num`,`db_employee_leave`.`apply_date`,`db_employee_leave`.`work_shift`,`db_employee_leave`.`vacationid`,`db_employee_leave`.`start_time`,`db_employee_leave`.`finish_time`,`db_employee_leave`.`leavetime`,`db_employee_leave`.`leave_cause`,`db_employee_leave`.`leave_status`,`db_employee_leave`.`dotime`,`db_applyer`.`employee_name` AS `applyer_name` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` WHERE `db_employee_leave`.`leaveid` = '$leaveid' AND `db_employee_leave`.`approve_status` = 'B' AND (`db_employee_leave`.`confirmer` = '$employeeid' OR `db_employee_leave`.`confirmer` = 0)";
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
		var start_time = $("#start_time").val();
		var finish_time = $("#finish_time").val();
		if(GetDateDiff(start_time,finish_time,'minute') < 30){
			alert('开始与结束时间间隔异常,请重新输入');
			return false;
		}
		var leavetime = $("#leavetime").val();
		if(!rf_b.test(leavetime)){
			$("#leavetime").focus();
			return false;
		}else{
			if(leavetime < 0.5){
				$("#leavetime").focus();
				return false;
			}
		}
		var vacationid = $("#vacationid").val();
		if(!vacationid){
			$("#vacationid").focus();
			return false;
		}
	})
})
</script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$applyer = $array['applyer'];
	$sql_lv = "SELECT * FROM `db_leave_overtime` WHERE `leaveid` = '$leaveid'";
	$result_lv = $db->query($sql_lv);
	//查询该员工的加班时间
	$sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$applyer' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0";
	$result_overtime = $db->query($sql_overtime);
	if($result_overtime->num_rows){
		while($row_overtime = $result_overtime->fetch_assoc()){
			$overtime_valid += $row_overtime['overtime_valid'];
		}
	}else{
		$overtime_valid = 0;
	}

?>
<div id="table_sheet">
  <h4>请假单确认</h4>
  <form action="employee_leave_confirmdo.php" name="employee_leave_confirm" method="post">
    <table>
      <tr>
        <th width="20%">请假单号：</th>
        <td width="80%"><?php echo $array['leave_num']; ?></td>
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
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" />
          <span class="tag"> *加班时间间隔最小为30分钟</span></td>
      </tr>
      <tr>
        <th>小时(H)：</th>
        <td><input type="text" name="leavetime" id="leavetime" value="<?php echo $array['leavetime']; ?>" class="input_txt"<?php if($result_lv->num_rows){ echo " readonly=\"readonly\""; } ?> />
          <span class="tag"> *请输入请假小时数(最小单位为0.5小时),有抵扣记录后无法修改</span></td>
      </tr>
      <tr>
        <th>请假类型：</th>
        <td><select name="vacationid" id="vacationid">
            <option value="">请选择</option>
            <?php
            if($result_vacation->num_rows){
				while($row_vacation = $result_vacation->fetch_assoc()){
					
			?>
            <option value="<?php echo $row_vacation['vacationid']; ?>"<?php if($row_vacation['vacationid'] == $array['vacationid']) echo " selected=\"selected\""; ?>><?php echo $row_vacation['vacation_name']; ?>
            <?php if($row_vacation['vacationid'] == 2) echo "<font color='red'>(".$overtime_valid."小时)</font>"; ?>
            </option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>请假事由：</th>
        <td><?php echo $array['leave_cause']; ?></td>
      </tr>
      <tr>
        <th>操作时间：</th>
        <td><?php echo $array['dotime']; ?></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="leave_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['leave_status']) echo " selected=\"selected\""; ?><?php if($status_key == 0 && $result_lv->num_rows) echo " disabled=\"disabled\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select>
          <span class="tag"> *有抵扣记录无法修改成无效状态</span></td>
      </tr>
      <tr>
        <th>确认人：</th>
        <td><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="leaveid" value="<?php echo $leaveid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>