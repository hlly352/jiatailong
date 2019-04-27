<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$systemid = fun_check_int($_GET['id']);
//读取系统程序
$sql_system = "SELECT `system_name`,`system_type` FROM `db_system` WHERE `systemid` = '$systemid'";
$result_system = $db->query($sql_system);
//读取系统管理员
$sql_system_employee = "SELECT `db_system_employee`.`employeeid` FROM `db_system_employee` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_system_employee`.`employeeid` WHERE `db_system_employee`.`systemid` = '$systemid' AND `db_system_employee`.`isadmin` = 1 AND `db_employee`.`employee_status` = 1 AND `db_employee`.`account_status` = 1";
$result_system_employee = $db->query($sql_system_employee);
if($result_system_employee->num_rows){
	while($row_system_employee = $result_system_employee->fetch_assoc()){
		$array_system_employee[] = $row_system_employee['employeeid'];
	}
}else{
	$array_system_employee = array();
}
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
	$("input[name]='employeeid'").click(function(){
		var employeeid = $(this).val();
		var systemid = $("#systemid").val();
		//$(this).is(':checked');
		$.post("system_admindo.php",{
			   employeeid:employeeid,
			   systemid:systemid
		},function(data,textStatus){
			if(data == 'add'){
				$("#"+employeeid).addClass('checkbox_checked');
			}else if(data == 'del'){
				$("#"+employeeid).removeClass('checkbox_checked');
			}
		})
	})
})
</script>
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_list">
  <?php
  if($result_system->num_rows){
      $array_system = $result_system->fetch_assoc();
      //读取部门
      $sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 AND `deptid` IN (SELECT `db_employee`.`deptid` FROM `db_system_employee` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_system_employee`.`employeeid` WHERE `db_system_employee`.`systemid` = '$systemid' AND `db_employee`.`account_status` = 1 GROUP BY `db_employee`.`deptid` HAVING COUNT(*) > 0) ORDER BY `dept_order` ASC,`deptid` ASC";

      $result_dept = $db->query($sql_dept);
  ?>
  <table>
    <caption>
    <?php echo '【'.$array_system_type[$array_system['system_type']].'】'.'-'.$array_system['system_name']; ?>-管理员设置
    <input type="hidden" name="systemid" id="systemid" value="<?php echo $systemid; ?>" />
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">部门</th>
      <th style="text-align:left;">员工</th>
    </tr>
    <?php
    if($result_dept->num_rows){
		//读取员工按部门Group BY employeeid,employee_name
		$sql_employee = "SELECT GROUP_CONCAT(CONCAT(`db_employee`.`employeeid`,'#',`db_employee`.`employee_name`) ORDER BY `db_employee`.`employeeid` ASC SEPARATOR '/') AS `employee_info`,`db_employee`.`deptid` FROM `db_system_employee` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_system_employee`.`employeeid` WHERE `db_system_employee`.`systemid` = '$systemid' AND `db_employee`.`employee_status` = 1 AND `db_employee`.`account_status` = 1 GROUP BY `db_employee`.`deptid`";

		$result_employee = $db->query($sql_employee);
		if($result_employee->num_rows){
			while($row_employee = $result_employee->fetch_assoc()){
				$array_employee[$row_employee['deptid']] = $row_employee['employee_info'];
			}
		}else{
			$array_employee = array();
		}
		//循环读取部门
		while($row_dept = $result_dept->fetch_assoc()){
			$deptid= $row_dept['deptid'];
			$employee_checkbox = '';
			//处理部门员工
			if(array_key_exists($deptid,$array_employee)){
				$employee_date = $array_employee[$deptid];
				$array_employee_date = explode('/',$employee_date);
				foreach($array_employee_date as $employee_info){
					$array_employee_info = explode('#',$employee_info);
					$employeeid = $array_employee_info[0];
					$employee_name = $array_employee_info[1];
					//判断是否已选中,处理勾选状态与员工样式
					if(in_array($employeeid,$array_system_employee)){
						$checked = " checked=\"checked\"";
						$span_style = " class=\"checkbox_checked\"";
					}else{
						$checked = "";
						$span_style = "";
					}
					$employee_checkbox .= " <input type=\"checkbox\" name=\"employee\" value=\"".$employeeid."\" ".$checked."/> "."<span id=\"".$employeeid."\"".$span_style.">".$employee_name."</span>";
				}
			}

	?>
    <tr>
      <td><?php echo $deptid; ?></td>
      <td><?php echo $row_dept['dept_name']; ?></td>
      <td style="text-align:left;"><?php echo $employee_checkbox; ?></td>
    </tr>
    <?php
		}
	}
	?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无数据！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>