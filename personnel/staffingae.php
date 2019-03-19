<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$positionid = fun_check_int($_GET['id']);
$year = $_GET['year']?$_GET['year']:date('Y');
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
//职位
$sql_position = "SELECT `position_name` FROM `db_personnel_position` WHERE `positionid` = '$positionid' AND `position_status` = 1";
$result_position = $db->query($sql_position);
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
	$("#deptid").change(function(){
		var deptid = $(this).val();
		var positionid = $("#positionid").val();
		var year = $("#year").val();
		if(deptid){
			$.post("../ajax_function/staffing_dept.php",{
				   deptid:deptid,
				   positionid:positionid,
				   year:year
			},function(data,textStatus){
				if(data){
					var array_data = data.split('#');
					for (var i=0;i<array_data.length;i++){
						var array_month_quantity = array_data[i];
						var array_quantity = array_month_quantity.split('|');
						var month = array_quantity[0];
						var quantity = array_quantity[1];
						$("#"+month).val(quantity);
					}
				}else{
					$("input[name^=quantity]").val(0);
				}
			})
		}else{
			$("input[name^=quantity]").val(0);
		}
	})
	$("input[name^=quantity]").blur(function(){
		var quantity = $(this).val();
		if(!ri_a.test(quantity)){
			alert('请输入数字');
			$(this).val(0);
		}
	})
	$("#submit").click(function(){
		var deptid = $("#deptid").val();
		if(!deptid){
			$("#deptid").focus();
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
  <?php
  if($result_position->num_rows){
	  $array_position = $result_position->fetch_assoc();
  ?>
  <h4>人员编制更新</h4>
  <form action="staffingdo.php" name="staffing" method="post">
    <table>
      <tr>
        <th width="20%">年份：</th>
        <td width="80%"><?php echo $year; ?></td>
      </tr>
      <tr>
        <th>职位：</th>
        <td><?php echo $array_position['position_name']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
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
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i))."-01";
	  ?>
      <tr>
        <th><?php echo $i; ?>月：</th>
        <td><input type="text" name="quantity[]" id="<?php echo $month; ?>" value="0" class="input_txt" /></td>
      </tr>
      <?php } ?>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="positionid" id="positionid" value="<?php echo $positionid; ?>" />
          <input type="hidden" name="year" id="year" value="<?php echo $year; ?>" /></td>
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