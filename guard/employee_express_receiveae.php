<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//读取快递公司
$sql_inc = "SELECT `incid`,`inc_cname`,`inc_ename` FROM `db_express_inc` ORDER BY `incid` ASC";
$result_inc = $db->query($sql_inc);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" src="../js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script language="javascript" src="../js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
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
		var sender = $("#sender").val();
		if(!$.trim(sender) || sender.length > 6){
			$("#sender").focus();
			return false;
		}
		var receiver = $("#receiver").val();
		if(!receiver){
			$("#receiver_name").focus();
			return false;
		}
		var cost = $("#cost").val();
		if(!ri_a.test(cost)){
			$("#cost").focus();
			return false;
		}
		var express_item = $("#express_item").val();
		if(!$.trim(express_item)){
			$("#express_item").focus();
			return false;
		}
	})
	$("#receiver_name").keyup(function(){
		var receiver_name = $(this).val();
		if($.trim(receiver_name)){
			$.post('../ajax_function/employee_name_all.php',{
				employee_name:receiver_name
			},function(data,textstatus){
				$("#receiver").show();
				$("#receiver").html(data);
			})
		}else{
			$("#receiver").hide();
		}
	})
	$("#receiver").dblclick(function(){
		var receiver_name = $("#receiver option:selected").text();
		var receiver = $("#receiver option:selected").val();
		if(receiver != ''){
			$("#receiver_name").val(receiver_name);
			$("#receiver").hide();
		}
	})
})
</script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>快递收件登记</h4>
  <form action="employee_express_receivedo.php" name="employee_express_receive" method="post">
    <table>
      <tr>
        <th width="20%">快递公司：</th>
        <td width="80%"><select name="express_incid" id="express_incid">
            <option value="">请选择</option>
            <?php
			if($result_inc->num_rows){
				while($row_inc = $result_inc->fetch_assoc()){
					echo "<option value=\"".$row_inc['incid']."\">".$row_inc['inc_cname'].'('.$row_inc['inc_ename'].")</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" id="express_num" class="input_txt" size="25" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>寄件方：</th>
        <td><input type="text" name="sender" id="sender" class="input_txt" size="25" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>收件人：</th>
        <td><input type="text" name="receiver_name" id="receiver_name" class="input_txt" size="25" autocomplete="off" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="receiver" size="5" id="receiver" style="width:170px; border:1px solid #DDD; position:absolute; display:none;">
          </select></td>
      </tr>
      <tr>
        <th>费用：</th>
        <td><input type="text" name="cost" id="cost" value="0" class="input_txt" />
          <span class="tag"> *如到付件请填写费用</span></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><textarea name="express_item" id="express_item" cols="50" rows="3" class="input_txt"></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>收件日期：</th>
        <td><input type="text" name="receipt_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);"/>
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $expressid = fun_check_int($_GET['id']);
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $sql = "SELECT `db_employee_express_receive`.`express_incid`,`db_employee_express_receive`.`express_num`,`db_employee_express_receive`.`sender`,`db_employee_express_receive`.`receiver`,`db_employee_express_receive`.`cost`,`db_employee_express_receive`.`express_item`,`db_employee_express_receive`.`receipt_date`,`db_employee_express_receive`.`express_status`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee_express_receive` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express_receive`.`receiver` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `expressid` = '$expressid' AND `registrant` = '$employeeid' AND `get_status` = 0";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>快递收件修改</h4>
  <form action="employee_express_receivedo.php" name="employee_express_receive" method="post">
    <table>
      <tr>
        <th width="20%">快递公司：</th>
        <td width="80%"><select name="express_incid" id="express_incid">
            <option value="">请选择</option>
            <?php
			if($result_inc->num_rows){
				while($row_inc = $result_inc->fetch_assoc()){
			?>
            <option value="<?php echo $row_inc['incid']; ?>"<?php if($row_inc['incid'] == $array['express_incid']) echo " selected=\"selected\""; ?>><?php echo $row_inc['inc_cname'].'('.$row_inc['inc_ename'].')'; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" id="express_num" value="<?php echo $array['express_num']; ?>" class="input_txt" size="25" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>寄件方：</th>
        <td><input type="text" name="sender" id="sender" value="<?php echo $array['sender']; ?>" class="input_txt" size="25" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>收件人：</th>
        <td><input type="text" name="receiver_name" id="receiver_name" value="<?php echo $array['dept_name'].'-'.$array['employee_name']; ?>" class="input_txt" size="30" autocomplete="off" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="receiver" size="5" id="receiver" style="width:170px; border:1px solid #DDD; position:absolute; display:none;">
            <?php 
			echo "<option value=\"".$array['receiver']."\" selected=\"selected\">".$array['dept_name'].'-'.$array['employee_name']."</option>";
			?>
          </select></td>
      </tr>
      <tr>
        <th>费用：</th>
        <td><input type="text" name="cost" id="cost" value="<?php echo $array['cost']; ?>" class="input_txt" />
          <span class="tag"> *如到付件请填写费用</span></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><textarea name="express_item" id="express_item" cols="50" rows="3" class="input_txt"><?php echo $array['express_item']; ?></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>收件日期：</th>
        <td><input type="text" name="receipt_date" value="<?php echo $array['receipt_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="express_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['express_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);"/>
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