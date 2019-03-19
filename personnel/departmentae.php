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
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var dept_name = $("#dept_name").val();
		if(!$.trim(dept_name)){
			$("#dept_name").focus();
			return false;
		}
		var dept_order = $("#dept_order").val();
		if(!ri_a.test(dept_order)){
			$("#dept_order").focus();
			return false;
		}
	})
})
</script>
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>部门添加</h4>
  <form action="departmentdo.php" name="department" method="post">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><input type="text" name="dept_name" id="dept_name" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>排序：</th>
        <td><input type="text" name="dept_order" id="dept_order" class="input_txt" />
          <span class="tag"> *必填，数字</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $deptid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_department` WHERE `deptid` = '$deptid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  //查该部门下的在职人员数
		  $sql_employee = "SELECT * FROM `db_employee` WHERE `deptid` = '$deptid' AND `employee_status` = 1";
		  $result_employee = $db->query($sql_employee);
  ?>
  <h4>部门修改</h4>
  <form action="departmentdo.php" name="department" method="post">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><input type="text" name="dept_name" id="dept_name" value="<?php echo $array['dept_name']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>排序：</th>
        <td><input type="text" name="dept_order" id="dept_order" value="<?php echo $array['dept_order']; ?>" class="input_txt" />
          <span class="tag"> *必填，数字</span></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="dept_status" id="dept_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['dept_status']) echo " selected=\"selected\""; ?><?php if($status_key == 0 && $result_employee->num_rows) echo " disabled=\"disabled\"" ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="deptid" value="<?php echo $deptid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
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