<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$expressid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$employee_name = $_SESSION['employee_info']['employee_name'];
//读取快递公司
$sql_express_inc = "SELECT `incid`,`inc_cname` FROM `db_express_inc` ORDER BY `incid` ASC";
$result_express_inc = $db->query($sql_express_inc);
$sql = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`apply_date`,`db_employee_express`.`express_num`,`db_employee_express`.`express_incid`,`db_employee_express`.`consignee`,`db_employee_express`.`consignee_inc`,`db_employee_express`.`express_item`,`db_employee_express`.`paytype`,`db_employee_express`.`cost`,`db_employee_express`.`express_status`,`db_employee_express`.`dotime`,`db_employee`.`employee_name` FROM `db_employee_express` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE `db_employee_express`.`expressid` = '$expressid' AND `db_employee_express`.`approve_status` = 'B' AND (`db_employee_express`.`reckoner` = '$employeeid' OR `db_employee_express`.`reckoner` = 0)";
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
		var express_num = $("#express_num").val();
		if(!$.trim(express_num)){
			$("#express_num").focus();
			return false;
		}
		/*
		var consignee = $("#consignee").val();
		if(!$.trim(consignee)){
			$("#consignee").focus();
			return false;
		}
		*/
		var consignee_inc = $("#consignee_inc").val();
		if(!$.trim(consignee_inc) || consignee_inc.length > 6){
			$("#consignee_inc").focus();
			return false;
		}
		var express_item = $("#express_item").val();
		if(!$.trim(express_item)){
			$("#express_item").focus();
			return false;
		}
		var paytype = $("#paytype").val();
		var express_status = $("#express_status").val();
		var cost = $("#cost").val();
		if(paytype == 'A' && !ri_b.test(cost) && express_status == 1){
			$("#cost").focus();
			return false;
		}
	})
	$("#paytype").change(function(){
		var paytype = $(this).val();
		if(paytype == 'A'){
			$("#cost").attr('disabled',false);
		}else if(paytype == 'B'){
			$("#cost").val(0);
			$("#cost").attr('disabled',true);
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
  <h4>寄快递结算</h4>
  <form action="employee_express_settledo.php" name="employee_express_settle" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" id="express_num" value="<?php echo $array['express_num']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>快递公司：</th>
        <td><select name="express_incid" id="express_incid">
            <?php
			if($result_express_inc->num_rows){
				while($row_express_inc = $result_express_inc->fetch_assoc()){
			?>
            <option value="<?php echo $row_express_inc['incid']; ?>"<?php if($row_express_inc['incid'] == $array['express_incid']) echo " selected=\"selected\""; ?>><?php echo $row_express_inc['inc_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th style="text-decoration:line-through">收件人姓名：</th>
        <td><input type="text" name="consignee" id="consignee" value="<?php echo $array['consignee']; ?>" class="input_txt" style="background:#F6F6F6;" disabled="disabled" />
          <span class="tag"> *填写样本：张三</span></td>
      </tr>
      <tr>
        <th>收件人公司：</th>
        <td><input type="text" name="consignee_inc" id="consignee_inc" value="<?php echo $array['consignee_inc']; ?>" class="input_txt" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><input type="text" name="express_item" id="express_item" value="<?php echo $array['express_item']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>付款方式：</th>
        <td><select name="paytype" id="paytype">
            <?php foreach($array_express_paytype as $paytype_inc_key=>$paytype_inc_value){ ?>
            <option value="<?php echo $paytype_inc_key; ?>"<?php if($paytype_inc_key == $array['paytype']) echo " selected=\"selected\""; ?>><?php echo $paytype_inc_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>操作时间：</th>
        <td><?php echo $array['dotime']; ?></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="express_status" id="express_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['express_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>费用：</th>
        <td><input type="text" name="cost" id="cost" value="<?php echo $array['cost']; ?>" class="input_txt"<?php if($array['paytype'] == 'B') echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>结算人：</th>
        <td><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="expressid" value="<?php echo $expressid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$expressid' AND `db_office_approve`.`approve_type` = 'E' ORDER BY `db_office_approve`.`approveid` DESC";
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