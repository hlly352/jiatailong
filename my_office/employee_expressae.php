<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//读取快递公司
$sql_express_inc = "SELECT `incid`,`inc_cname` FROM `db_express_inc` ORDER BY `incid` ASC";
$result_express_inc = $db->query($sql_express_inc);
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
		var express_incid = $("#express_incid").val();
		if(!express_incid){
			$("#express_incid").focus();
			return false;
		}
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
		if(!paytype){
			$("#paytype").focus();
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
  <h4>快递申请</h4>
  <form action="employee_expressdo.php" name="employee_express" method="post">
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
          </select></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>快递公司：</th>
        <td><select name="express_incid" id="express_incid">
            <option value="">请选择</option>
            <?php
			if($result_express_inc->num_rows){
				while($row_express_inc = $result_express_inc->fetch_assoc()){
					echo "<option value=\"".$row_express_inc['incid']."\">".$row_express_inc['inc_cname']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>快递单号：</th>
        <td><input name="express_num" type="text" class="input_txt" id="express_num" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th style="text-decoration:line-through">收件人姓名：</th>
        <td><input name="consignee" type="text" class="input_txt" id="consignee" size="35" style="background:#F6F6F6;" disabled="disabled" />
          <span class="tag"> *填写样本：张三</span></td>
      </tr>
      <tr>
        <th>收件人公司：</th>
        <td><input name="consignee_inc" type="text" class="input_txt" id="consignee_inc" size="35" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><textarea name="express_item" cols="50" rows="3" class="input_txt" id="express_item"></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>付款方式：</th>
        <td><select name="paytype" id="paytype">
            <option value="">请选择</option>
            <?php
            foreach($array_express_paytype as $paytype_key=>$paytype_value){
				echo "<option value=\"".$paytype_key."\">".$paytype_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location:javascript:history.go(-1);" />
          <input type="hidden" name="agenter" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){  
	  $expressid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_employee_express`.`apply_date`,`db_employee_express`.`express_incid`,`db_employee_express`.`express_num`,`db_employee_express`.`consignee`,`db_employee_express`.`consignee_inc`,`db_employee_express`.`express_item`,`db_employee_express`.`paytype`,`db_employee`.`employee_name` FROM `db_employee_express` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE `db_employee_express`.`expressid` = '$expressid' AND `db_employee_express`.`express_status` = 1 AND (`db_employee_express`.`applyer` = '$employeeid' OR `db_employee_express`.`agenter` = '$employeeid') AND `db_employee_express`.`approve_status` = 'C'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>快递申请修改</h4>
  <form action="employee_expressdo.php" name="employee_express" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
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
        <th>快递单号：</th>
        <td><input name="express_num" type="text" class="input_txt" id="express_num" value="<?php echo $array['express_num']; ?>" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th style="text-decoration:line-through">收件人姓名：</th>
        <td><input name="consignee" type="text" class="input_txt" id="consignee" value="<?php echo $array['consignee']; ?>" size="35" style="background:#F6F6F6;" disabled="disabled" />
          <span class="tag"> *填写样本：张三</span></td>
      </tr>
      <tr>
        <th>收件人公司：</th>
        <td><input name="consignee_inc" type="text" class="input_txt" id="consignee_inc" value="<?php echo $array['consignee_inc']; ?>" size="35" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><textarea name="express_item" cols="50" rows="3" class="input_txt" id="express_item"><?php echo $array['express_item']; ?></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>付款方式：</th>
        <td><select name="paytype" id="paytype">
            <?php foreach($array_express_paytype as $paytype_key=>$paytype_value){ ?>
            <option value="<?php echo $paytype_key; ?>"<?php if($paytype_key == $array['paytype']) echo " selected=\"selected\""; ?>><?php echo $paytype_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location:javascript:history.go(-1);" />
          <input type="hidden" name="expressid" value="<?php echo $expressid; ?>" />
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