<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询父级类型
$sql_type = "SELECT * FROM `db_mould_check_type` ORDER BY `path` ASC,`id` ASC";
$result_type = $db->query($sql_type);
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
		var checkname = $("#checkname").val();
    if(!checkname){
      alert('请输入项目名称');
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
  <h4>物料类型添加</h4>
  <form action="mould_check_datado.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">类型名称</th>
        <td width="80%">
          <select name="typeid" class="input_txt txt">
            <?php
              if($result_type->num_rows){
                while($row_type = $result_type->fetch_assoc()){
                  $count = substr_count($row_type['path'],',') - 1;
                  $str = $count <= 0?'':str_repeat('--',$count);
                  echo '<option value="'.$row_type['id'].'">'.$str.$row_type['typename'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>项目名称：</th>
        <td>
          <input type="text" name="check_name" id="checkname" class="input_txt" style="width:70%" />
        </td>
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
	  $material_typeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_material_type` WHERE `material_typeid` = '$material_typeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>物料类型修改</h4>
  <form action="material_typedo.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">父级类型：</th>
        <td width="80%">
          <?php
          ?>
        </td>
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