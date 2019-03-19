<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit_flow").click(function(){
		var flow_order = $("#flow_order").val();
		if(!ri_b.test(flow_order)){
			$("#flow_order").focus();
			return false;
		}
		var tag = $("#tag").html();
		if((tag)){
			$("#flow_order").focus();
			return false;
		}
		var approver = $("#approver").val();
		if(!approver){
			$("#approver").focus();
			return false;
		}
		var certigier = $("#certigier").val();
		if(!certigier){
			$("#certigier").focus();
			return false;
		}
	})
	$("input[name^=employee]").keyup(function(){
		var employee_name = $(this).val();
		var employee_type = $(this).attr('id');
		if($.trim(employee_name)){
			$.post('../ajax_function/employee_name.php',{
				employee_name:employee_name
			},function(data,textstatus){
				$("#employee_"+employee_type).show();
				$("#employee_"+employee_type).html(data);
			})
		}else{
			$("#employee_"+employee_type).hide();
		}
	})
	$("SELECT[id^=employee]").dblclick(function(){
		var id = $(this).attr('id');
		var employee_name = $("#"+id+" option:selected").text();
		var employeeid = $("#"+id+" option:selected").val();
		if(employeeid != ''){
			$("input[name="+id+"]").val(employee_name);
			$(this).hide();
		}
	})
	$("#flow_order").blur(function(){
		var flow_order = $(this).val();
		if(ri_b.test(flow_order)){
			var deptid = $("#deptid").val();
			var action = $("#action").val();
			var flowid = $("#flowid").val();
			$.post("../ajax_function/vehicle_flow_order_check.php",{
				flow_order:flow_order,
				deptid:deptid,
				action:action,
				flowid:flowid 
			},function(data,textStatus){
				$("#tag").html(data);
			})
		}
	})
})
</script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $deptid = fun_check_int($_GET['id']);
	  $sql_dept = "SELECT `dept_name` FROM `db_department` WHERE `deptid` = '$deptid' AND `dept_status` = 1";
	  $result_dept = $db->query($sql_dept);
	  if($result_dept->num_rows){
		  $array_dept = $result_dept->fetch_assoc();
  ?>
  <h4>审批流程添加</h4>
  <form action="approve_flowdo.php" name="approve_flow" method="post">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><?php echo $array_dept['dept_name']; ?></td>
      </tr>
      <tr>
        <th>序号：</th>
        <td><input type="text" name="flow_order" id="flow_order" class="input_txt" />
          <span class="tag" id="tag"></span></td>
      </tr>
      <tr>
        <th>审批人：</th>
        <td><input type="text" name="employee_approver" id="approver" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="approver" size="5" id="employee_approver" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
          </select></td>
      </tr>
      <tr>
        <th>授权人：</th>
        <td><input type="text" name="employee_certigier" id="certigier" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="certigier" size="5" id="employee_certigier" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
          </select></td>
      </tr>
      <tr>
        <th>派车员：</th>
        <td><input type="checkbox" name="iscontrol" value="1" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit_flow" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='approve_flow.php'" />
          <input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  }elseif($action == "edit"){
	  $flowid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_vehicle_flow`.`approver`,`db_vehicle_flow`.`certigier`,`db_vehicle_flow`.`flow_order`,`db_vehicle_flow`.`iscontrol`,`db_vehicle_flow`.`deptid`,`db_department`.`dept_name` ,`db_approver`.`employee_name` AS `approver_name`,`db_certigier`.`employee_name` AS `certigier_name`,`db_dept_approver`.`dept_name` AS `approver_deptname`,`db_dept_certigier`.`dept_name` AS `certigier_deptname` FROM `db_vehicle_flow` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_flow`.`deptid` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_vehicle_flow`.`approver` INNER JOIN `db_department` AS `db_dept_approver` ON `db_dept_approver`.`deptid` = `db_approver`.`deptid` INNER JOIN `db_employee` AS `db_certigier` ON `db_certigier`.`employeeid` = `db_vehicle_flow`.`certigier` INNER JOIN `db_department` AS `db_dept_certigier` ON `db_dept_certigier`.`deptid` = `db_certigier`.`deptid` WHERE `db_vehicle_flow`.`flowid` = '$flowid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $deptid = $array['deptid'];
  ?>
  <h4>审批流程修改</h4>
  <form action="approve_flowdo.php" name="approve_flow" method="post">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>级别：</th>
        <td><input type="text" name="flow_order" id="flow_order" value="<?php echo $array['flow_order']; ?>" class="input_txt" />
          <span class="tag" id="tag"></span></td>
      </tr>
      <tr>
        <th>审批人：</th>
        <td><input type="text" name="employee_approver" id="approver" value="<?php echo $array['approver_deptname'].'-'.$array['approver_name']; ?>" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="approver" size="5" id="employee_approver" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['approver']; ?>" selected="selected"><?php echo $array['approver_name']; ?></option>
          </select></td>
      </tr>
      <tr>
        <th>被授权人：</th>
        <td><input type="text" name="employee_certigier" id="certigier" value="<?php echo $array['certigier_deptname'].'-'.$array['certigier_name']; ?>" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span> <br />
          <select name="certigier" size="5" id="employee_certigier" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['certigier']; ?>" selected="selected"><?php echo $array['certigier_name']; ?></option>
          </select></td>
      </tr>
      <tr>
        <th>派车员：</th>
        <td><input type="checkbox" name="iscontrol" value="1"<?php if($array['iscontrol']) echo " checked=\"checked\""; ?> /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit_flow" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='approve_flow.php'" />
          <input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid; ?>" />
          <input type="hidden" name="flowid" id="flowid" value="<?php echo $flowid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
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
$sql_flow = "SELECT `db_approver`.`employee_name` AS `approver_name`,`db_certigier`.`employee_name` AS `certigier_name`,`db_vehicle_flow`.`flowid`,`db_vehicle_flow`.`flow_order`,`db_vehicle_flow`.`iscontrol` FROM `db_vehicle_flow` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_vehicle_flow`.`approver` INNER JOIN `db_employee` AS `db_certigier` ON `db_certigier`.`employeeid` = `db_vehicle_flow`.`certigier` WHERE `db_vehicle_flow`.`deptid` = '$deptid' ORDER BY `db_vehicle_flow`.`flow_order` ASC";
$result_flow = $db->query($sql_flow);
$result_id = $db->query($sql_flow);
?>
<div id="table_list">
  <?php
  if($result_flow->num_rows){
	  while($row_flowid = $result_id->fetch_assoc()){
		  $array_flowid .= $row_flowid['flowid'].',';
	  }
	  $array_flowid = rtrim($array_flowid,',');
	  $sql_vehicle_list = "SELECT `flowid` FROM `db_vehicle_list` WHERE `flowid` IN ($array_flowid) GROUP BY `flowid`";
	  $result_vehicle_list = $db->query($sql_vehicle_list);
	  if($result_vehicle_list->num_rows){
		  while($row_vehicle_list = $result_vehicle_list->fetch_assoc()){
			  $array_flow[] = $row_vehicle_list['flowid'];
		  }
	  }else{
		   $array_flow = array();
	  }
  ?>
  <form action="approve_flowdo.php" name="list" method="post">
    <table>
      <caption>
      审批流程
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="23%">序号</th>
        <th width="23%">审批人</th>
        <th width="23%">授权人</th>
        <th width="23%">派车员</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row_flow = $result_flow->fetch_assoc()){
		  $flowid = $row_flow['flowid'];
		  $iscontrol = $row_flow['iscontrol']?'是':'否';
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $flowid; ?>"<?php if(in_array($flowid,$array_flow)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_flow['flow_order']; ?></td>
        <td><?php echo $row_flow['approver_name']; ?></td>
        <td><?php echo $row_flow['certigier_name']; ?></td>
        <td><?php echo $iscontrol; ?></td>
        <td><a href="approve_flowae.php?id=<?php echo $flowid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="button" name="button" value="添加" class="select_button" onclick="location.href='approve_flowae.php?action=add&id=<?php echo $deptid; ?>'" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无审批流程！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>