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
		var item_type_sn = $("#item_type_sn").val();
		if(!$.trim(item_type_sn)){
			$("#item_type_sn").focus();
			return false;
		}
		var item_typename = $("#item_typename").val();
		if(!$.trim(item_typename)){
			$("#item_typename").focus();
			return false;
		}
	})
	$("#item_type_sn").blur(function(){
		var reg_sn = /[A-Z]+$/;
		var item_type_sn = $(this).val();
		var item_type_sn_defaultvalue = this.defaultValue;
		if($.trim(item_type_sn) && !reg_sn.test(item_type_sn)){
			alert('请输入英文大写字母');
			$(this).val(item_type_sn_defaultvalue);
		}
	})
})
</script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>项目类型添加</h4>
  <form action="item_typedo.php" name="item_type" method="post">
    <table>
      <tr>
        <th width="20%">序号：</th>
        <td width="80%"><input type="text" name="item_type_sn" id="item_type_sn" class="input_txt" /></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="item_typename" id="item_typename" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      <tr> </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $item_typeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_quote_item_type` WHERE `item_typeid` = '$item_typeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>项目类型修改</h4>
  <form action="item_typedo.php" name="item_type" method="post">
    <table>
      <tr>
        <th width="20%">序号：</th>
        <td width="80%"><input type="text" name="item_type_sn" id="item_type_sn" value="<?php echo $array['item_type_sn']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="item_typename" id="item_typename" value="<?php echo $array['item_typename']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="item_typeid" value="<?php echo $item_typeid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      <tr> </tr>
    </table>
  </form>
  <?php
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>