<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
	$("#submit").click(function(){
		var destination = $("#destination").val();
		if(!$.trim(destination)){
			$("#destination").focus();
			return false;
		}
		var start_time = $("#start_time").val();
		var finish_time = $("#finish_time").val();
		if(GetDateDiff(start_time,finish_time,'minute') < 15){
			alert('出厂与回厂时间间隔最小为15分钟，请重新输入！');
			return false;
		}
		var goout_cause = $("#goout_cause").val();
		if(!$.trim(goout_cause)){
			$("#goout_cause").focus();
			return false;
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
  ?>
  <h4>出门证申请</h4>
  <form action="employee_gooutdo.php" name="employee_goout" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><select name="applyer">
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
        <th>公事目的地：</th>
        <td><input type="text" name="destination" id="destination" class="input_txt" />
          <span class="tag"> *公事目的地填写样本：苏州佳能、上海SBTS</span></td>
      </tr>

      <tr>
        <th>出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo date('Y-m-d H:i:00'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>回厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo date('Y-m-d H:i:s',strtotime(date('H:i:00').'+1 hours')); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>仅限于公事出门申请</td>
      </tr>
      <tr>
        <th>出门事由：</th>
        <td><textarea name="goout_cause" id="goout_cause" cols="50" rows="3" class="input_txt"></textarea>
          <span class="tag"> *必填项</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="agenter" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $gooutid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_employee_goout`.`goout_num`,`db_employee_goout`.`apply_date`,`db_employee_goout`.`destination`,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`,`db_employee_goout`.`goout_cause`,`db_employee`.`employee_name` FROM `db_employee_goout` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_goout`.`applyer` WHERE `db_employee_goout`.`gooutid` = '$gooutid' AND `db_employee_goout`.`approve_status` = 'C' AND `db_employee_goout`.`goout_status` = 1 AND (`db_employee_goout`.`applyer` = '$employeeid' OR `db_employee_goout`.`agenter` = '$employeeid')";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>出门证修改</h4>
  <form action="employee_gooutdo.php" name="employee_goout" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>出门证号：</th>
        <td><?php echo $array['goout_num']; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>公事目的地：</th>
        <td><input type="text" name="destination" id="destination" value="<?php echo $array['destination']; ?>" class="input_txt" />
          <span class="tag"> *公事目的地填写样本：苏州佳能、上海SBTS</span></td>
      </tr>
      <tr>
        <th>出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo date('Y-m-d H:i:s',strtotime($array['start_time'])); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>回厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo date('Y-m-d H:i:s',strtotime($array['finish_time'])); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>外出事由：</th>
        <td><textarea name="goout_cause" id="goout_cause" cols="50" rows="3" class="input_txt"><?php echo $array['goout_cause']; ?></textarea>
          <span class="tag"> *必填项</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="gooutid" value="<?php echo $gooutid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>