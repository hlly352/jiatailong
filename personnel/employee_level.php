<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$sql_employee = "SELECT `db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employeeid` = '$employeeid'";
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var employeeid = $("#employeeid").val();
		if(!employeeid){
			$("#employeeid").focus();
			return false;
		}
		var superior = $("#superior").val();
		if(!superior){
			$("#superior").focus();
			return false;
		}
	})
	$("#employeeid").change(function(){
		var employeeid = $(this).val();
		var employee_superior = $("#employee_superior").val();
		if($.trim(employeeid)){
			$.post("../ajax_function/employee_superior.php",{
				   employeeid:employeeid,
				   employee_superior:employee_superior
			},function(data,textStatus){
				$("#superior").html(data);
			})
		}else{
			$("#superior").html('<option value="">请选择</option>');
		}
	})
	$("#superior").change(function(){
		$("#employeeid").val('');
	})					 
})
</script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result_employee->num_rows){
      //读取下属员工(在职的)
      $array_employee = $result_employee->fetch_assoc();
      $sql_level = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `superior` = '$employeeid' AND `employee_status` = 1 ORDER BY CONVERT(`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
      $result_level = $db->query($sql_level);
      //读取员工
      $sql_superior = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employee_status` = 1 AND `db_employee`.`employeeid` != '$employeeid' ORDER BY `db_department`.`dept_order` ASC,`db_department`.`deptid` ASC,CONVERT(`db_employee`.`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
      $result_superior = $db->query($sql_superior);
  ?>
  <h4>员工下属转移</h4>
  <form action="employee_leveldo.php" name="employee_level" method="post">
    <table>
      <tr>
        <th width="20%">员工姓名：</th>
        <td width="80%"><?php echo $array_employee['employee_name']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array_employee['dept_name']; ?></td>
      </tr>
      <tr>
        <th>员工下属：</th>
        <td><select name="employeeid[]" id="employeeid" size="10" multiple="multiple" style="width:125px;">
           <option value="">请选择</option>
            <?php
			if($result_level->num_rows){
				while($row_level = $result_level->fetch_assoc()){
					echo "<option value=\"".$row_level['employeeid']."\">".$row_level['employee_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>转移到：</th>
        <td><select name="superior" id="superior">
            <option value="">请选择</option>
            <?php
			if($result_superior->num_rows){
				while($row_superior = $result_superior->fetch_assoc()){
					echo "<option value=\"".$row_superior['employeeid']."\">".$row_superior['dept_name'].'-'.$row_superior['employee_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='employee.php'" />
          <input type="hidden" name="employee_superior" id="employee_superior" value="<?php echo $employeeid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>