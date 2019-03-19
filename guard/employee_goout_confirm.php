<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$gooutid = fun_check_int($_GET['id']);
$employee_name = $_SESSION['employee_info']['employee_name'];
$sql = "SELECT `db_employee_goout`.`gooutid`,`db_employee_goout`.`goout_num`,`db_employee_goout`.`applyer`,`db_employee_goout`.`agenter`,`db_employee_goout`.`apply_date`,`db_employee_goout`.`destination`,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`,`db_employee_goout`.`goout_cause`,`db_employee_goout`.`confirmer_out`,`db_employee_goout`.`confirmer_in`,`db_employee_goout`.`dotime`,`db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`employee_name` AS `agenter_name`,ROUND(TIMESTAMPDIFF(MINUTE,`db_employee_goout`.`start_time`,`db_employee_goout`.`finish_time`)/60,1) AS `diffhour` FROM `db_employee_goout` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_goout`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_goout`.`agenter` WHERE `db_employee_goout`.`gooutid` = '$gooutid' AND `db_employee_goout`.`approve_status` = 'B' AND `db_employee_goout`.`goout_status` = 1 AND (`db_employee_goout`.`confirmer_out` = 0 OR `db_employee_goout`.`confirmer_in` = 0)";
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
	$agenter_name = ($array['applyer'] == $array['agenter'])?'--':$array['agenter_name'];
	$confirmer_out = $array['confirmer_out'];
	$confirmer_in = $array['confirmer_in'];
?>
<div id="table_sheet">
  <?php if(!$confirmer_out){ ?>
  <h4>出厂确认</h4>
  <form action="employee_goout_confirmdo.php" name="employee_goout_confirm" method="post">
    <table>
      <tr>
        <th width="20%">出门证号：</th>
        <td width="80%"><?php echo $array['goout_num']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
      </tr>
      <tr>
        <th>代理人：</th>
        <td><?php echo $agenter_name; ?></td>
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
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" disabled="disabled" /></td>
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
        <th>确认人：</th>
        <td><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="gooutid" value="<?php echo $gooutid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="dotype" value="O" /></td>
      </tr>
    </table>
  </form>
  <?php }elseif(!$confirmer_in){ ?>
  <h4>回厂确认</h4>
  <form action="employee_goout_confirmdo.php" name="employee_goout_confirm" method="post">
    <table>
      <tr>
        <th width="20%">出门证号：</th>
        <td width="80%"><?php echo $array['goout_num']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
      </tr>
      <tr>
        <th>代理人：</th>
        <td><?php echo $agenter_name; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" disabled="disabled" /></td>
      </tr>
      <tr>
        <th>回厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" />
          <span class="tag"> *请依据实际出厂时间填写;如不回厂,请按下班时间填写.</span></td>
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
      <th>确认人：</th>
        <td><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="gooutid" value="<?php echo $gooutid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="dotype" value="I" /></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$gooutid' AND `db_office_approve`.`approve_type` = 'G' ORDER BY `db_office_approve`.`approveid` DESC";
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