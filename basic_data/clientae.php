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
<script language="javascript">
$(function(){
	$("#submit").click(function(){
		var client_code = $("#client_code").val();
		if(!$.trim(client_code)){
			$("#client_code").focus();
			return false;
		}
		var client_cname = $("#client_cname").val();
		if(!$.trim(client_cname)){
			$("#client_cname").focus();
			return false;
		}
		var client_name = $("#client_name").val();
		if(!$.trim(client_name)){
			$("#client_name").focus();
			return false;
		}
	})
	$("#client_code").blur(function(){
		var client_code = $(this).val();
		var clientid = $("#clientid").val();
		var action = $("#action").val();
		if($.trim(client_code)){
			$.post("../ajax_function/client_code_check.php",{
				   client_code:client_code,
				   clientid:clientid,
				   action:action
			},function(data,textStatus){
				if(data == 0){
					alert('客户代码重复，请重新输入！');
					$("#client_code").val('');
				}else{
					client_code = client_code.toUpperCase();
					$("#client_code").val(client_code);
				}
			})
		}
	})
})
</script>
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>客户添加</h4>
  <form action="clientdo.php" name="cleint" method="post">
    <table>
      <tr>
        <th width="20%">客户代码：</th>
        <td width="80%"><input type="text" name="client_code" id="client_code" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>中文名：</th>
        <td><input type="text" name="client_cname" id="client_cname" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>英文名：</th>
        <td><input type="text" name="client_ename" class="input_txt" /></td>
      </tr>
      <tr>
        <th>全称：</th>
        <td><input type="text" name="client_name" id="client_name" class="input_txt" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>地址：</th>
        <td><input type="text" name="client_address" class="input_txt" size="35" /></td>
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
	  $clientid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_client` WHERE `clientid` = '$clientid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>客户修改</h4>
  <form action="clientdo.php" name="cleint" method="post">
    <table>
      <tr>
        <tr>
        <th width="20%">客户代码：</th>
        <td width="80%"><input type="text" name="client_code" id="client_code" value="<?php echo $array['client_code']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
        <th>中文名：</th>
        <td><input type="text" name="client_cname" id="client_cname" value="<?php echo $array['client_cname']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>英文名：</th>
        <td><input type="text" name="client_ename" value="<?php echo $array['client_ename']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>全称：</th>
        <td><input type="text" name="client_name" id="client_name" value="<?php echo $array['client_name']; ?>" class="input_txt" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>地址：</th>
        <td><input type="text" name="client_address" value="<?php echo $array['client_address']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="client_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['client_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="clientid" id="clientid" value="<?php echo $clientid; ?>" />
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