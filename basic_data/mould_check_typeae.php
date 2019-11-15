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
		var typename = $("#typename").val();
    if(!typename){
      alert('请输入类型名称');
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
  <h4>项目类型添加</h4>
  <form action="mould_check_typedo.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">父级类型</th>
        <td width="80%">
          <select name="pid" class="input_txt txt">
            <option value="0">顶级类型</option>
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
        <th>类型名称：</th>
        <td>
          <input type="text" name="typename" id="typename" class="input_txt" />
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
	  $typeid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_mould_check_type` WHERE `id` = '$typeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $row = $result->fetch_assoc();
  ?>
 <h4>项目类型修改</h4>
  <form action="mould_check_typedo.php" name="material_type" method="post">
    <table>
      <tr>
        <th width="20%">父级类型</th>
        <td width="80%">
          <select name="pid" class="input_txt txt">
            <option value="0">顶级类型</option>
            <?php
              if($result_type->num_rows){
                while($row_type = $result_type->fetch_assoc()){
                  $count = substr_count($row_type['path'],',') - 1;
                  $str = $count <= 0?'':str_repeat('--',$count);
                  $is_select = $row['pid'] == $row_type['id']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_type['id'].'">'.$str.$row_type['typename'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>类型名称：</th>
        <td>
          <input type="text" name="typename" value="<?php echo $row['typename'] ?>" id="typename" class="input_txt" />
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="typeid" value="<?php echo $typeid; ?>" />
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