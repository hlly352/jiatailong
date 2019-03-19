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
		var texture = $("#texture").val();
		if(!texture){
			$("#texture").focus();
			return false;
		}
		var hardness = $("#hardness").val();
		if(!$.trim(hardness)){
			$("#hardness").focus();
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
  <h4>刀具硬度添加</h4>
  <form action="cutter_hardnessdo.php" name="cutter_hardness" method="post">
    <table>
      <tr>
        <th width="20%">材质：</th>
        <td width="80%"><select name="texture" id="texture">
            <option value="">请选择</option>
            <?php
			foreach($array_cutter_texture as $texture_key=>$texture_value){
				echo "<option value=\"".$texture_key."\">".$texture_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>硬度：</th>
        <td><input type="text" name="hardness" id="hardness" class="input_txt" /></td>
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
	  $hardnessid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_cutter_hardness` WHERE `hardnessid` = '$hardnessid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>刀具硬度修改</h4>
  <form action="cutter_hardnessdo.php" name="cutter_hardness" method="post">
    <table>
      <tr>
        <th width="20%">材质：</th>
        <td width="80%"><select name="texture" id="texture">
            <option value="">请选择</option>
            <?php foreach($array_cutter_texture as $texture_key=>$texture_value){ ?>
            <option value="<?php echo $texture_key; ?>"<?php if($texture_key == $array['texture']) echo " selected=\"selected\""; ?>><?php echo $texture_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>规格：</th>
        <td><input type="text" name="hardness" id="hardness" value="<?php echo $array['hardness']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="hardnessid" value="<?php echo $hardnessid; ?>" />
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