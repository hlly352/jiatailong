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
		var try_causename = $("#try_causename").val();
		if(!$.trim(try_causename)){
			$("#try_causename").focus();
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
  <h4>模具试模原因添加</h4>
  <form action="mould_try_causedo.php" name="mould_try_cause" method="post">
    <table>
      <tr>
        <th width="20%">原因名称：</th>
        <td width="80%"><input type="text" name="try_causename" id="try_causename" class="input_txt" />
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
	  $try_causeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_mould_try_cause` WHERE `try_causeid` = '$try_causeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>模具试模原因修改</h4>
  <form action="mould_try_causedo.php" name="mould_try_cause" method="post">
    <table>
      <tr>
        <th width="20%">原因名称：</th>
        <td width="80%"><input type="text" name="try_causename" id="try_causename" value="<?php echo $array['try_causename']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="try_causestatus">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['try_causestatus']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="try_causeid" value="<?php echo $try_causeid; ?>" />
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