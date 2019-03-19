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
		var plan_content = $("#plan_content").val();
		if(!$.trim(plan_content)){
			$("#plan_content").focus();
			return false;
		}
		var employee = $("#employee").val();
		if(!$.trim(employee)){
			$("#employee").focus();
			return false;
		}
	})
})
</script>
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $workid = fun_check_int($_GET['id']);
	  $sql_work = "SELECT `work_content`,`deadline_date` FROM `db_work` WHERE `workid` = '$workid' AND `worker` = '$employeeid' AND `work_status` = 1 AND `pdca_status` IN ('P','D')";
	  $result_work = $db->query($sql_work);
	  if($result_work->num_rows){
		  $array_work = $result_work->fetch_assoc();
  ?>
  <h4>工作计划添加</h4>
  <form action="work_plando.php" name="work_plan" method="post">
    <table>
      <tr>
        <th width="20%">工作内容：</th>
        <td width="80%"><?php echo $array_work['work_content']; ?></td>
      </tr>
      <tr>
        <th>期限时间：</th>
        <td><?php echo $array_work['deadline_date']; ?></td>
      </tr>
      <tr>
        <th>计划内容：</th>
        <td><textarea name="plan_content" cols="80" rows="6" class="input_txt" id="plan_content"></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>截止时间：</th>
        <td><input type="text" name="end_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee" id="employee" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  }elseif($action == "edit"){
	  $planid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_work_plan`.`plan_content`,`db_work_plan`.`end_date`,`db_work_plan`.`employee`,`db_work_plan`.`workid`,`db_work`.`work_content`,`db_work`.`deadline_date` FROM `db_work_plan` INNER JOIN `db_work` ON `db_work`.`workid` = `db_work_plan`.`workid` WHERE `db_work_plan`.`planid` = '$planid' AND `db_work_plan`.`employeeid` = '$employeeid' AND `db_work`.`worker` = '$employeeid' AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` IN ('P','D')";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $workid = $array['workid'];
  ?>
  <h4>工作计划修改</h4>
  <form action="work_plando.php" name="work_plan" method="post">
    <table>
      <tr>
        <th width="20%">工作内容：</th>
        <td width="80%"><?php echo $array['work_content']; ?></td>
      </tr>
      <tr>
        <th>期限时间：</th>
        <td><?php echo $array['deadline_date']; ?></td>
      </tr>
      <tr>
        <th>计划内容：</th>
        <td><textarea name="plan_content" cols="80" rows="6" class="input_txt" id="plan_content"><?php echo codetextarea($array['plan_content']); ?></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>截止时间：</th>
        <td><input type="text" name="end_date" value="<?php echo $array['end_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee" id="employee" value="<?php echo $array['employee']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
          <input type="hidden" name="planid" value="<?php echo $planid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  }
  ?>
</div>
<?php
$sql_plan = "SELECT `db_work_plan`.`planid`,`db_work_plan`.`plan_content`,`db_work_plan`.`end_date`,`db_work_plan`.`employee`,`db_work_plan`.`dotime`,`db_employee`.`employee_name` FROM `db_work_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work_plan`.`employeeid` WHERE `db_work_plan`.`workid` = '$workid' ORDER BY `db_work_plan`.`end_date` ASC,`db_work_plan`.`planid` ASC";
$result_plan = $db->query($sql_plan);
?>
<div id="table_list">
  <?php if($result_plan->num_rows){ ?>
  <table>
    <caption>
    工作计划
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th>内容</th>
      <th width="10%">截止时间</th>
      <th width="10%">责任人</th>
      <th width="10%">操作人</th>
      <th width="10%">时间</th>
    </tr>
    <?php while($row_plan = $result_plan->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_plan['planid']; ?></td>
      <td style="text-align:left;"><?php echo $row_plan['plan_content']; ?></td>
      <td><?php echo $row_plan['end_date']; ?></td>
      <td><?php echo $row_plan['employee']; ?></td>
      <td><?php echo $row_plan['employee_name']; ?></td>
      <td><?php echo $row_plan['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>