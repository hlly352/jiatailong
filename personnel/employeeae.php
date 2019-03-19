<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//上级领导
$sql_employee = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `employee_status` = 1 ORDER BY `employee_name` ASC";
$result_employee = $db->query($sql_employee);
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
<script language="javascript" type="text/javascript" src="../js/checkidcard.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var action = $("#action").val();
		var employee_name = $("#employee_name").val();
		if(!$.trim(employee_name)){
			$("#employee_name").focus();
			return false;
		}
		var employee_number = $("#employee_number").val();
		if(!$.trim(employee_number)){
			$("#employee_number").focus();
			return false;
		}
		var employee_number_tag = $("#employee_number_tag").html();
		if(employee_number_tag){
			$("#employee_number").focus();
			return false;
		}
		var deptid = $("#deptid").val();
		if(!deptid){
			$("#deptid").focus();
			return false;
		}
		var superior = $("#superior").val();
		if(!superior){
			$("#superior").focus();
			return false;
		}
		var position_type = $("#position_type").val();
		if(!position_type){
			$("#position_type").focus();
			return false;
		}
		var positionid = $("#positionid").val();
		if(!positionid){
			$("#positionid").focus();
			return false;
		}
		var education = $("#education").val();
		if(!education){
			$("#education").focus();
			return false;
		}
		var idcard = $("#idcard").val();
		if(!$.trim(idcard) || idcard_tag){
			$("#idcard").focus();
			return false;
		}
		var idcard_tag = $("#idcard_tag").html();
		if(idcard_tag){
			$("#idcard").focus();
			return false;
		}
		if(action == "edit"){
			var employee_status = $("#employee_status").val();
			var termdate = $("#termdate").val();
			if(employee_status == 0 && termdate == '0000-00-00'){
				alert('请选择离职日期');
				return false;
				var entrydate = $("#entrydate").val();
				if(GetDateDiff(entrydate,termdate,'day') < 0){
					alert('离职日期与小于入职日期，请重新选择！');
					return false;
				}
			}
		}
	})
	$("#employee_name").blur(function(){
		var employee_name = $(this).val();
		var employeeid = $("#employeeid").val();
		var action = $("#action").val();
		if($.trim(employee_name)){
			$.post("../ajax_function/employee_name_check.php",{
				   employee_name:employee_name,
				   employeeid:employeeid,
				   action:action
			},function(data,textStatus){
				$("#employee_name_tag").html(data);
			})
		}else{
			$("#employee_name_tag").html('');
		}
	})
	$("#employee_number").blur(function(){
		var employee_number = $(this).val();
		var employeeid = $("#employeeid").val();
		var action = $("#action").val();
		if($.trim(employee_number)){
			$.post("../ajax_function/employee_number_check.php",{
				   employee_number:employee_number,
				   employeeid:employeeid,
				   action:action
			},function(data,textStatus){
				$("#employee_number_tag").html(data);
			})
		}else{
			$("#employee_number_tag").html('');
		}
	})
	$("#idcard").blur(function(){
		var idcard = $(this).val();
		if($.trim(idcard)){
			$("#birthday").val('');
			$("#age").val('');
			$("#sex").val('');	
			var idcard_check_result = checkIdcard(idcard);
			$("#idcard_tag").html(idcard_check_result);
		}
	})
	$("#employee_status").change(function(){
		var employee_status = $("#employee_status").val();
		var employeeid = $("#employeeid").val();
		if(employee_status == 1){
			$("#termdate").attr('disabled',true);
		}else if(employee_status == 0){
			$.post("../ajax_function/employee_level_check.php",{
				   employeeid:employeeid
			},function(data,textStatus){
				if(data == 0){
					$("#termdate").attr('disabled',false);
				}else if(data > 0){
					alert('该员工有下属员工，请先转移！')
					$("#employee_status").val(1);
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
<div id="table_sheet">
  <?php
  if($action == "add"){
	  //部门
	  $sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
	  $result_dept = $db->query($sql_dept);
	  //职位
	  $sql_position = "SELECT `positionid`,CONCAT(`position_code`,'-',`position_name`) AS `position_name` FROM `db_personnel_position` WHERE `position_status` = 1 ORDER BY `position_code` ASC,`positionid` ASC";
	  $result_position = $db->query($sql_position);
  ?>
  <h4>员工添加</h4>
  <form action="employeedo.php" name="employee" method="post">
    <table>
      <tr>
        <th width="20%">员工姓名：</th>
        <td width="80%"><input type="text" name="employee_name" id="employee_name" class="input_txt" />
          <span id="employee_name_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>员工工号：</th>
        <td><input type="text" name="employee_number" id="employee_number" class="input_txt" />
          <span id="employee_number_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>所属部门：</th>
        <td><select name="deptid" id="deptid">
            <option value="">请选择</option>
            <?php
            if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
					echo "<option value=\"".$row_dept['deptid']."\">".$row_dept['dept_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>上级领导：</th>
        <td><select name="superior" id="superior">
            <option value="">请选择</option>
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
        <th>岗位级别：</th>
        <td><select name="position_type" id="position_type">
            <option value="">请选择</option>
            <?php
            foreach($array_position_type as $position_type_key=>$position_type_value){
				echo "<option value=\"".$position_type_key."\">".$position_type_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>工作职位：</th>
        <td><select name="positionid" id="positionid">
            <option value="">请选择</option>
            <?php
            if($result_position->num_rows){
				while($row_position = $result_position->fetch_assoc()){
					echo "<option value=\"".$row_position['positionid']."\">".$row_position['position_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>文化程度：</th>
        <td><select name="education" id="education">
            <option value="">请选择</option>
            <?php
            foreach($array_education_type as $education_type_key=>$education_type_value){
				echo "<option value=\"".$education_type_key."\">".$education_type_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>入职日期：</th>
        <td><input type="text" name="entrydate" id="entrydate" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>身份证号码：</th>
        <td><input type="text" name="idcard" id="idcard" class="input_txt" />
          <span id="idcard_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>出生年月：</th>
        <td><input type="text" name="birthday" id="birthday" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>年龄：</th>
        <td><input type="text" name="age" id="age" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>性别：</th>
        <td><input type="text" name="sex" id="sex" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>联系电话：</th>
        <td><input type="text" name="phone" class="input_txt" /></td>
      </tr>
      <tr>
        <th>家庭住址：</th>
        <td><input type="text" name="address" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td><input type="text" name="remark" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $employeeid = fun_check_int($_GET['id']);
	  //部门
	  $sql_dept = "SELECT `deptid`,`dept_name`,`dept_status` FROM `db_department` ORDER BY `dept_order` ASC,`deptid` ASC";
	  $result_dept = $db->query($sql_dept);
	  //职位
	  $sql_position = "SELECT `positionid`,CONCAT(`position_code`,'-',`position_name`) AS `position_name` FROM `db_personnel_position` ORDER BY `position_code` ASC,`positionid` ASC";
	  $result_position = $db->query($sql_position);
	  $sql = "SELECT `employee_name`,`employee_number`,`deptid`,`position_type`,`positionid`,`superior`,`education`,`entrydate`,`termdate`,`idcard`,`birthday`,`sex`,`phone`,`nativeplace`,`address`,`employee_status`,`remark`,(DATE_FORMAT(CURDATE(),'%Y')-DATE_FORMAT(`birthday`,'%Y')) AS `age` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>员工修改</h4>
  <form action="employeedo.php" name="employee" method="post">
    <table>
      <tr>
        <th width="20%">员工姓名：</th>
        <td width="80%"><input type="text" name="employee_name" id="employee_name" value="<?php echo $array['employee_name']; ?>" class="input_txt" />
          <span id="employee_name_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>员工工号：</th>
        <td><input type="text" name="employee_number" id="employee_number" value="<?php echo $array['employee_number']; ?>" class="input_txt" />
          <span id="employee_number_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>所属部门：</th>
        <td><select name="deptid" id="deptid">
            <?php
            if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
			?>
            <option value="<?php echo $row_dept['deptid']; ?>"<?php if($row_dept['deptid'] == $array['deptid']) echo " selected=\"selected\""; ?>><?php echo $row_dept['dept_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>上级领导：</th>
        <td><select name="superior" id="superior">
            <option value="">请选择</option>
            <?php
            if($result_employee->num_rows){
				while($row_employee = $result_employee->fetch_assoc()){
			?>
            <option value="<?php echo $row_employee['employeeid']; ?>"<?php if($row_employee['employeeid'] == $array['superior']) echo " selected=\"selected\""; ?>><?php echo $row_employee['employee_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>岗位级别：</th>
        <td><select name="position_type" id="position_type">
            <?php foreach($array_position_type as $position_type_key=>$position_type_value){ ?>
            <option value="<?php echo $position_type_key; ?>"<?php if($position_type_key == $array['position_type']) echo " selected=\"selected\""; ?>><?php echo $position_type_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>工作职位：</th>
        <td><select name="positionid" id="positionid">
            <?php
            if($result_position->num_rows){
				while($row_position = $result_position->fetch_assoc()){
			?>
            <option value="<?php echo $row_position['positionid']; ?>"<?php if($row_position['positionid'] == $array['positionid']) echo " selected=\"selected\""; ?>><?php echo $row_position['position_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>文化程度：</th>
        <td><select name="education" id="education">
            <option value="">请选择</option>
            <?php foreach($array_education_type as $education_type_key=>$education_type_value){ ?>
            <option value="<?php echo $education_type_key; ?>"<?php if($education_type_key == $array['education']) echo " selected=\"selected\""; ?>><?php echo $education_type_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>入职日期：</th>
        <td><input type="text" name="entrydate" id="entrydate" value="<?php echo $array['entrydate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>身份证号码：</th>
        <td><input type="text" name="idcard" id="idcard" value="<?php echo $array['idcard']; ?>" class="input_txt" />
          <span id="idcard_tag" class="tag"></span></td>
      </tr>
      <tr>
        <th>出生年月：</th>
        <td><input type="text" name="birthday" id="birthday" value="<?php echo $array['birthday']; ?>" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>年龄：</th>
        <td><input type="text" name="age" id="age" value="<?php echo $array['age']; ?>" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>性别：</th>
        <td><input type="text" name="sex" id="sex" value="<?php echo $array['sex']; ?>" class="input_txt" readonly="readonly" />
          <span class="tag"> *自动匹配</span></td>
      </tr>
      <tr>
        <th>籍贯：</th>
        <td><input type="text" name="nativeplace" value="<?php echo $array['nativeplace']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系电话：</th>
        <td><input type="text" name="phone" value="<?php echo $array['phone']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>家庭住址：</th>
        <td><input type="text" name="address" value="<?php echo $array['address']; ?>" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="employee_status" id="employee_status">
            <?php foreach($array_employee_status as $employee_status_key=>$employee_status_value){ ?>
            <option value="<?php echo $employee_status_key; ?>"<?php if($employee_status_key == $array['employee_status']) echo " selected=\"selected\""; ?>><?php echo $employee_status_value; ?></option>
            <?php } ?>
          </select>
          <span class="tag"> *选择离职，将关闭该账号所有程序访问权限。</span></td>
      </tr>
      <tr>
        <th>离职日期：</th>
        <td><input type="text" name="termdate" id="termdate" value="<?php echo $array['termdate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if($array['employee_status'] == 1){ echo " disabled=\"disabled\""; } ?> /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="employeeid" id="employeeid" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
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