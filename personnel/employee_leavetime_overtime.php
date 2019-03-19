<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$leaveid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee_leave`.`leave_num`,`db_employee_leave`.`applyer`,`db_employee_leave`.`apply_date`,`db_employee_leave`.`start_time`,`db_employee_leave`.`finish_time`,`db_employee_leave`.`leavetime`,`db_employee_leave`.`leavetime_valid`,`db_employee`.`employee_name` FROM `db_employee_leave` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_leave`.`applyer` WHERE `db_employee_leave`.`approve_status` = 'B' AND `db_employee_leave`.`confirmer` != 0 AND `db_employee_leave`.`leave_status` = 1 AND `db_employee_leave`.`leavetime_valid` > 0 AND `db_employee_leave`.`leaveid` = '$leaveid'";
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
		var leaveid = $("#leaveid").val();
		if(!leaveid){
			$("#leaveid").focus();
			return false;
		}
		var deduction_time = $("#deduction_time").val();
		if(!rf_b.test(deduction_time)){
			$("#deduction_time").focus();
			return false;
		}
	})
	$("#deduction_time").blur(function(){
		var deduction_time = $(this).val();
		var leaveid = $("#leaveid").val();
		var overtimeid = $("#overtimeid").val();
		if(rf_b.test(deduction_time) && leaveid){
			$.post("../ajax_function/deduction_time_check.php",{
				   deduction_time:deduction_time,
				   leaveid:leaveid,
				   overtimeid:overtimeid
			},function(data,textStatus){
				if(data == 1){	
					alert('抵扣时间异常,请重新输入!')
					$("#deduction_time").val('');
				}
			})
		}
	})
	$("#overtimeid").change(function(){
		var overtimeid = $(this).val();
		var deduction_time = $("#deduction_time").val();
		var leaveid = $("#leaveid").val();
		if(rf_b.test(deduction_time) && leaveid){
			$.post("../ajax_function/deductiontime_check.php",{
				   deduction_time:deduction_time,
				   leaveid:leaveid,
				   overtimeid:overtimeid
			},function(data,textStatus){
				if(data == 1){	
					alert('抵扣时间异常,请重新输入!')
					$("#deduction_time").val('');
				}
			})
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
	$sql_overtime = "SELECT `overtimeid`,`overtime_num`,`apply_date`,`overtime`,`overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$applyer' AND `approve_status` = 'B' AND `confirmer` != 0 AND `overtime_status` = 1 AND `overtime_valid` > 0";
	$result_overtime = $db->query($sql_overtime);
?>
<div id="table_sheet">
  <h4>请假时间抵扣加班时间</h4>
  <form action="over_leavetimedo.php" name="overtime" method="post">
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
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>请假时间：</th>
        <td><?php echo $array['start_time'].'->'.$array['finish_time']; ?></td>
      </tr>
      <tr>
        <th>请假小时(H)：</th>
        <td><?php echo $array['leavetime']; ?></td>
      </tr>
      <tr>
        <th>有效小时(H)：</th>
        <td><?php echo $array['leavetime_valid']; ?></td>
      </tr>
      <tr>
        <th>加班信息：</th>
        <td><select name="overtimeid" id="overtimeid">
        <option value="">请选择</option>
        <?php
        if($result_overtime->num_rows){
			while($row_overtime = $result_overtime->fetch_assoc()){
				echo "<option value=\"".$row_overtime['overtimeid']."\">".$row_overtime['overtime_num'].'-'.$row_overtime['apply_date'].'-'.$row_overtime['overtime'].'/'.$row_overtime['overtime_valid']."</option>";
			}
		}
		?>
        </select></td>
      </tr>
      <tr>
        <th>抵扣小时：</th>
        <td><input type="text" name="deduction_time" id="deduction_time" value="<?php echo $array['leavetime_valid']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="leaveid" id="leaveid" value="<?php echo $leaveid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>