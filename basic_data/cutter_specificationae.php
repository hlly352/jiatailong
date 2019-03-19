<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
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
		var typeid = $("#typeid").val();
		if(!typeid){
			$("#typeid").focus();
			return false;
		}
		var specification = $("#specification").val();
		if(!$.trim(specification)){
			$("#specification").focus();
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
  <h4>刀具规格添加</h4>
  <form action="cutter_specificationdo.php" name="cutter_specification" method="post">
    <table>
      <tr>
        <th width="20%">类型：</th>
        <td width="80%"><select name="typeid" id="typeid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
					echo "<option value=\"".$row_cutter_type['typeid']."\">".$row_cutter_type['type']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>规格：</th>
        <td><input type="text" name="specification" id="specification" class="input_txt" /></td>
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
	  $specificationid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_cutter_specification` WHERE `specificationid` = '$specificationid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>刀具规格修改</h4>
  <form action="cutter_specificationdo.php" name="cutter_specification" method="post">
    <table>
      <tr>
        <th width="20%">类型：</th>
        <td width="80%"><select name="typeid" id="typeid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $array['typeid']) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>规格：</th>
        <td><input type="text" name="specification" id="specification" value="<?php echo $array['specification']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="specificationid" value="<?php echo $specificationid; ?>" />
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