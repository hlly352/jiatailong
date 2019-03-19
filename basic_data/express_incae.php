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
		var inc_cname = $("#inc_cname").val();
		if(!$.trim(inc_cname)){
			$("#inc_cname").focus();
			return false;
		}
		var inc_ename = $("#inc_ename").val();
		if(!$.trim(inc_ename)){
			$("#inc_ename").focus();
			return false;
		}
	})
})
</script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>快递公司添加</h4>
  <form action="express_incdo.php" name="express_inc" method="post">
    <table>
      <tr>
        <th width="20%">快递公司(中文名)：</th>
        <td width="80%"><input type="text" name="inc_cname" id="inc_cname" class="input_txt" /></td>
      </tr>
      <tr>
        <th>快递公司(英文名)：</th>
        <td><input type="text" name="inc_ename" id="inc_ename" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系人：</th>
        <td><input type="text" name="inc_contact" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系电话：</th>
        <td><input type="text" name="inc_phone" class="input_txt" /></td>
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
	  $incid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_express_inc` WHERE `incid` = '$incid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>快递公司修改</h4>
  <form action="express_incdo.php" name="express_inc" method="post">
    <table>
      <tr>
        <th width="20%">快递公司(中文名)：</th>
        <td width="80%"><input type="text" name="inc_cname" id="inc_cname" value="<?php echo $array['inc_cname']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>快递公司(英文名)：</th>
        <td><input type="text" name="inc_ename" id="inc_ename" value="<?php echo $array['inc_ename']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系人：</th>
        <td><input type="text" name="inc_contact" value="<?php echo $array['inc_contact']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>联系电话：</th>
        <td><input type="text" name="inc_phone" value="<?php echo $array['inc_phone']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="inc_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['inc_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="incid" value="<?php echo $incid; ?>" />
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