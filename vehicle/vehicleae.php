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
	$("#submit").click(function(){
		var plate_number = $("#plate_number").val();
		if(!$.trim(plate_number)){
			$("#plate_number").focus();
			return false;
		}
		var vehicle_type = $("#vehicle_type").val();
		if(!vehicle_type){
			$("#vehicle_type").focus();
			return false;
		}
		var owner = $("#owner").val();
		if(!$.trim(owner)){
			$("#owner").focus();
			return false;
		}
		var contact = $("#contact").val();
		if(!$.trim(contact)){
			$("#contact").focus();
			return false;
		}
		var charge_out = $("#charge_out").val();
		if(!rf_a.test(charge_out)){
			$("#charge_out").focus();
			return false;
		}
		var charge_in = $("#charge_in").val();
		if(!rf_a.test(charge_in)){
			$("#charge_in").focus();
			return false;
		}
		var charge_wait = $("#charge_wait").val();
		if(!rf_a.test(charge_wait)){
			$("#charge_wait").focus();
			return false;
		}
	})
})
</script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>车辆添加</h4>
  <form action="vehicledo.php" name="vehicle" method="post">
    <table>
      <tr>
        <th width="20%">车牌车牌：</th>
        <td width="80%"><input type="text" name="plate_number" id="plate_number" class="input_txt" /></td>
      </tr>
      <tr>
        <th>车辆类型：</th>
        <td><select name="vehicle_type" id="vehicle_type">
            <option value="">请选择</option>
            <?php
			foreach($array_vehicle_type as $vehicle_type_key=>$vehicle_type){
				echo "<option value=\"".$vehicle_type_key."\">".$vehicle_type."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>联系人：</th>
        <td><input type="text" name="owner" id="owner" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系方式：</th>
        <td><input type="text" name="contact" id="contact" class="input_txt" /></td>
      </tr>
      <tr>
        <th>长途费用(元/公里)：</th>
        <td><input type="text" name="charge_out" id="charge_out" class="input_txt" /></td
>
      </tr>
      <tr>
        <th>市内费用(元/公里)：</th>
        <td><input type="text" name="charge_in" id="charge_in" class="input_txt" /></td>
      </tr>
      <tr>
        <th>等候费用(元/小时)：</th>
        <td><input type="text" name="charge_wait" id="charge_wait" class="input_txt" /></td>
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
	  $vehicleid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_vehicle` WHERE `vehicleid` = '$vehicleid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>车辆修改</h4>
  <form action="vehicledo.php" name="vehicle" method="post">
    <table>
      <tr>
        <th width="20%">车牌车牌：</th>
        <td width="80%"><input type="text" name="plate_number" id="plate_number" value="<?php echo $array['plate_number']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>车辆类型：</th>
        <td><select name="vehicle_type" id="vehicle_type">
            <?php foreach($array_vehicle_type as $vehicle_type_key=>$vehicle_type){ ?>
            <option value="<?php echo $vehicle_type_key; ?>"<?php if($vehicle_type_key == $array['vehicle_type']) echo " selected=\"selected\""; ?>><?php echo $vehicle_type; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>联系人：</th>
        <td><input type="text" name="owner" id="owner" value="<?php echo $array['owner']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系方式：</th>
        <td><input type="text" name="contact" id="contact" value="<?php echo $array['contact']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>长途费用(元/公里)：</th>
        <td><input type="text" name="charge_out" id="charge_out" value="<?php echo $array['charge_out']; ?>" class="input_txt" /></td
>
      </tr>
      <tr>
        <th>市内费用(元/公里)：</th>
        <td><input type="text" name="charge_in" id="charge_in" value="<?php echo $array['charge_in']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>等候费用(元/小时)：</th>
        <td><input type="text" name="charge_wait" id="charge_wait" value="<?php echo $array['charge_wait']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>车辆状态：</th>
        <td><select name="vehicle_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['vehicle_status']) echo " selected=\"selected\"" ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无数据！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>