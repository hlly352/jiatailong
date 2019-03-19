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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var dotype = $("#dotype").val();
		if(!dotype){
			$("#dotype").focus();
			return false;
		}
		var vehicle_category = $("#vehicle_category").val();
		if(!vehicle_category){
			$("#vehicle_category").focus();
			return false;
		}
		var departure = $("#departure").val();
		if(!$.trim(departure)){
			$("#departure").focus();
			return false;
		}
		var destination = $("#destination").val();
		if(!$.trim(destination) || destination.length >6 ){
			$("#destination").focus();
			return false;
		}
		var start_time = $("#start_time").val();
		var finish_time = $("#finish_time").val();
		if(GetDateDiff(start_time,finish_time,'minute') < 30){
			alert('出厂与回厂时间间隔最小为30分钟，请重新输入！');
			return false;
		}
		var roundtype = $("#roundtype").val();
		if(!roundtype){
			$("#roundtype").focus();
			return false;
		}
		var other = $("#other").val();
		if(!$.trim(other)){
			$("#other").focus();
			return false;
		}
		var cause = $("#cause").val();
		if(!$.trim(cause)){
			$("#cause").focus();
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
	  $result_employee = $db->query($sql_employee);;
  ?>
  <h4>用车申请</h4>
  <form action="employee_vehicledo.php" name="employee_vehicle" method="post">
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
          </select>
          <span class="tag"> *如需代理申请请下拉选择</span></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><select name="dotype" id="dotype">
            <option value="">请选择</option>
            <?php
            foreach($array_vehicle_dotype as $dotype_key=>$dotype){
				echo "<option value=\"".$dotype_key."\">".$dotype."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>车辆类型：</th>
        <td><select name="vehicle_category" id="vehicle_category">
            <option value="">请选择</option>
            <?php
            foreach($array_vehicle_category as $category_key=>$category_value){
				echo "<option value=\"".$category_key."\">".$category_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><input type="text" name="departure" id="departure" class="input_txt" size="35" />
          <span class="tag"> *填写样本：苏州双林</span></td>
      </tr>
      <tr>
        <th>目的地：</th>
        <td><input type="text" name="destination" id="destination" class="input_txt" size="35" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>路程方式：</th>
        <td><select name="roundtype" id="roundtype">
            <option value="">请选择</option>
            <?php
            foreach($array_vehicle_roundtype as $roundtype_key=>$roundtype){
				echo "<option value=\"".$roundtype_key."\">".$roundtype."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>预计出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo date('Y-m-d H:i:00'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>预计返厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo date('Y-m-d H:i:s',strtotime(date('H:i:00').'+1 hours')); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>随车人员：</th>
        <td><input type="text" name="other" id="other" value="无" class="input_txt" />
          <span class="tag"> *必填或无</span></td>
      </tr>
      <tr>
        <th>用车事由：</th>
        <td><textarea name="cause" cols="50" rows="3" class="input_txt" id="cause"></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='employee_vehicle_apply.php'" />
          <input type="hidden" name="agenter" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $listid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`applyer`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`vehicle_category`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_employee`.`employee_name` FROM `db_vehicle_list` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` WHERE `db_vehicle_list`.`listid` = '$listid' AND `db_vehicle_list`.`applyer` = '$employeeid' AND `db_vehicle_list`.`vehicle_status` = 1 AND `db_vehicle_list`.`approve_status` = 'C'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>用车申请修改</h4>
  <form action="employee_vehicledo.php" name="employee_vehicle" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>派车单号：</th>
        <td><?php echo $array['vehicle_num']; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><select name="dotype" id="dotype">
            <?php foreach($array_vehicle_dotype as $dotype_key=>$dotype){ ?>
            <option value="<?php echo $dotype_key; ?>"<?php if($dotype_key == $array['dotype']) echo " selected=\"selected\""; ?>><?php echo $dotype; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>车辆类型：</th>
        <td><select name="vehicle_category" id="vehicle_category">
            <option value="">请选择</option>
            <?php foreach($array_vehicle_category as $category_key=>$category_value){ ?>
            <option value="<?php echo $category_key; ?>"<?php if($category_key == $array['vehicle_category']) echo " selected=\"selected\""; ?>><?php echo $category_value; ?></option>
			<?php } ?>	
          </select></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><input type="text" name="departure" id="departure" value="<?php echo $array['departure']; ?>" class="input_txt" size="35" />
          <span class="tag"> *填写样本：苏州双林</span></td>
      </tr>
      <tr>
        <th>目的地：</th>
        <td><input type="text" name="destination" id="destination" value="<?php echo $array['destination']; ?>" class="input_txt" size="35" />
          <span class="tag"> *填写公司简称，最多6个字符</span></td>
      </tr>
      <tr>
        <th>路程方式：</th>
        <td><select name="roundtype" id="roundtype">
            <?php foreach($array_vehicle_roundtype as $roundtype_key=>$roundtype){ ?>
            <option value="<?php echo $roundtype_key; ?>"<?php if($roundtype_key == $array['roundtype']) echo " selected=\"selected\""; ?>><?php echo $roundtype; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>预计出厂时间：</th>
        <td><input type="text" name="start_time" id="start_time" value="<?php echo $array['start_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>预计返厂时间：</th>
        <td><input type="text" name="finish_time" id="finish_time" value="<?php echo $array['finish_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:00',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>随车人员：</th>
        <td><input type="text" name="other" id="other" value="<?php echo $array['other']; ?>" class="input_txt" />
          <span class="tag"> *必填或无</span></td>
      </tr>
      <tr>
        <th>用车事由：</th>
        <td><textarea name="cause" cols="50" rows="3" class="input_txt" id="cause"><?php echo $array['cause']; ?></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="applyer" value="<?php echo $array['applyer']; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" /></td>
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