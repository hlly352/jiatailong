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
		var brand = $("#brand").val();
		if(!$.trim(brand)){
			$("#brand").focus();
			return false;
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
  <h4>刀具品牌添加</h4>
  <form action="cutter_branddo.php" name="cutter_brand" method="post">
    <table>
      <tr>
        <th width="20%">品牌：</th>
        <td width="80%"><input type="text" name="brand" id="brand" class="input_txt" /></td>
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
	  $brandid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_cutter_brand` WHERE `brandid` = '$brandid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>刀具品牌修改</h4>
  <form action="cutter_branddo.php" name="cutter_brand" method="post">
    <table>
      <tr>
        <th width="20%">品牌：</th>
        <td width="80%"><input type="text" name="brand" id="brand" value="<?php echo $array['brand']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="brandid" value="<?php echo $brandid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
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