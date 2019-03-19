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
		var supplier_typecode = $("#supplier_typecode").val();
		if(!$.trim(supplier_typecode)){
			$("#supplier_typecode").focus();
			return false;
		}
		var supplier_typename = $("#supplier_typename").val();
		if(!$.trim(supplier_typename)){
			$("#supplier_typename").focus();
			return false;
		}
	})
	$("#supplier_typecode").blur(function(){
		var supplier_typecode = $(this).val();
		var supplier_typeid = $("#supplier_typeid").val();
		var action = $("#action").val();
		if($.trim(supplier_typecode)){
			$.post("../ajax_function/supplier_typecode_check.php",{
				   supplier_typecode:supplier_typecode,
				   supplier_typeid:supplier_typeid,
				   action:action
			},function(data,textStatus){
				if(data == 0){
					alert('类型代码重复，请重新输入！');
					$("#supplier_typecode").val('');
				}else{
					supplier_typecode = supplier_typecode.toUpperCase();
					$("#supplier_typecode").val(supplier_typecode);
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
  <h4>供应商类型添加</h4>
  <form action="supplier_typedo.php" name="supplier_type" method="post">
    <table>
      <tr>
        <th width="20%">类型代码：</th>
        <td width="80%"><input type="text" name="supplier_typecode" id="supplier_typecode" class="input_txt" />
          <span class="tag"> *必填,如A，B</span></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="supplier_typename" id="supplier_typename" class="input_txt" />
          <span class="tag"> *必填</span></td>
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
	  $supplier_typeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_supplier_type` WHERE `supplier_typeid` = '$supplier_typeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>供应商类型修改</h4>
  <form action="supplier_typedo.php" name="supplier_type" method="post">
    <table>
      <tr>
        <th width="20%">类型代码：</th>
        <td width="80%"><input type="text" name="supplier_typecode" id="supplier_typecode" value="<?php echo $array['supplier_typecode']; ?>" class="input_txt" />
          <span class="tag"> *必填,如A，B</span></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="supplier_typename" id="supplier_typename" value="<?php echo $array['supplier_typename']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="supplier_typestatus">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['supplier_typestatus']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="supplier_typeid" id="supplier_typeid" value="<?php echo $supplier_typeid; ?>" />
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