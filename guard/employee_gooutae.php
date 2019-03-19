<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if(!$_SESSION['system_shell'][$system_dir]['isadmin']){
	die("访问被拒绝，请与管理员联系！");
}
$gooutid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee_goout`.`gooutid`,`db_employee_goout`.`goout_num`,`db_employee_goout`.`apply_date`,`db_employee_goout`.`destination`,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`,`db_employee_goout`.`goout_cause`,`db_employee_goout`.`approve_status`,`db_employee_goout`.`goout_status`,`db_employee_goout`.`dotime`,`db_employee`.`employee_name` ,ROUND(TIMESTAMPDIFF(MINUTE,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`)/60,1) AS `diffhour` FROM `db_employee_goout` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_goout`.`applyer` WHERE `db_employee_goout`.`gooutid` = '$gooutid' AND `db_employee_goout`.`approve_status` = 'B'";
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
		if(GetDateDiff(start_time,finish_time,'minute') <= 15){
			alert('出厂与回厂时间间隔最小为15分钟，请重新输入！');
			return false;
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
  <h4>出门证修改</h4>
  <form action="employee_gooutdo.php" name="employee_goout" method="post">
    <table>
      <tr>
        <th width="20%">出门证号：</th>
        <td width="80%"><?php echo $array['goout_num']; ?></td>
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
        <th>出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" />
          <span class="tag"> *请依据实际出厂时间填写</span></td>
      </tr>
      <tr>
        <th>回厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>小时(H)：</th>
        <td><?php echo $array['diffhour']; ?></td>
      </tr>
      <tr>
        <th>目的地：</th>
        <td><?php echo $array['destination']; ?></td>
      </tr>
      <tr>
        <th>出门事由：</th>
        <td><?php echo $array['goout_cause']; ?></td>
      </tr>
      <tr>
        <th>操作时间：</th>
        <td><?php echo $array['dotime']; ?></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="goout_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['goout_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="gooutid" value="<?php echo $gooutid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>