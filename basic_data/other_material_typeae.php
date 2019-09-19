<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_GET['action'];

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
		var material_typecode = $("#material_typecode").val();
		if(!ri_a.test($.trim(material_typecode))){
			$("#material_typecode").focus();
			return false;
		}
		var material_typename = $("#material_typename").val();
		if(!$.trim(material_typename)){
			$("#material_typename").focus();
			return false;
		}
	})
	$("#material_typecode").blur(function(){
		var material_typecode = $(this).val();
		var material_typeid = $("#material_typeid").val();
		var action = $("#action").val();
		if($.trim(material_typecode)){
			$.post("../ajax_function/other_material_typecode_check.php",{
				   material_typecode:material_typecode,
				   material_typeid:material_typeid,
				   action:action
			},function(data,textStatus){
				if(data == 0){
					alert('类型代码重复，请重新输入！');
					$("#material_typecode").val('');
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
  <h4>物料类型添加</h4>
  <form action="other_material_typedo.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">类型代码：</th>
        <td width="80%"><input type="text" name="material_typecode" id="material_typecode" class="input_txt" />
          <span class="tag"> *必填,如0，1</span></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="material_typename" id="material_typename" class="input_txt" />
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
    }elseif($action == "specification"){
      $id = $_GET['id'];
      //查找物料名称
      $sql = "SELECT * FROM `db_other_material_data` WHERE `dataid` = '$id'";
      $result = $db->query($sql);
      if($result->num_rows){
        $row = $result->fetch_assoc();
      }
  ?>
      <h4>物料规格添加</h4>
    <form action="other_material_typedo.php" name="" method="post">
      <table>
        <tr>
          <th width="20%">物料名称：</th>
          <td width="80%">
            <?php echo $row['material_name'] ?>
            <!-- <span class="tag"> *必填,如0，1</span></td> -->
        </tr>
        <tr>
          <th>物料规格：</th>
          <td>
              <textarea name="specification" rows="5" cols="20">
                
              </textarea>
            <br>
            <span class="tag">多种规格中间用“|”隔开</span></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><input type="submit" name="submit" id="submits" value="确定" class="button" />
            <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
            <input type="hidden" name="materialid" value="<?php echo $_GET['id'] ?>"/>
            <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
        </tr>
      </table>
    </form>
  <?php
    }elseif($action == "edit"){
	  $material_typeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_other_material_type` WHERE `material_typeid` = '$material_typeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>物料类型修改</h4>
  <form action="other_material_typedo.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">类型代码：</th>
        <td width="80%"><input type="text" name="material_typecode" id="material_typecode" value="<?php echo $array['material_typecode']; ?>" class="input_txt" />
          <span class="tag"> *必填,如A，B</span></td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="material_typename" id="material_typename" value="<?php echo $array['material_typename']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="material_typestatus">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['material_typestatus']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="material_typeid" id="material_typeid" value="<?php echo $material_typeid; ?>" />
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