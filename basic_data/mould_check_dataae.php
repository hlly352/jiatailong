<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$page = $_GET['page'];
$type = $_GET['type'];
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
<title>基础数据-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>模具图纸检查表项目添加</h4>
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
        <th>等级：</th>
        <td>
          <select name="degree" class="input_txt txt">
            <?php
              foreach($array_mould_check_degree as $k=>$degree):
                echo '<option value="'.$k.'">'.$degree.'</option>';
              endforeach;
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
	  $id = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_mould_check_data` WHERE `id` = '$id'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $row = $result->fetch_assoc();
  ?>
  <h4>模具图纸检查表项目修改</h4>
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
                  $is_select = $row['categoryid'] == $row_type['id']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_type['id'].'">'.$str.$row_type['typename'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>等级：</th>
        <td>
          <select name="degree" class="input_txt txt">
            <?php
              foreach($array_mould_check_degree as $k=>$degree):
                $is_select = $k == $row['degree']?'selected':'';
                echo '<option '.$is_select.' value="'.$k.'">'.$degree.'</option>';
              endforeach;
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>项目名称：</th>
        <td>
          <input type="text" name="check_name" value="<?php echo $row['checkname'] ?>" id="checkname" class="input_txt" style="width:70%" />
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>
          <input type="hidden" value="<?php echo $type ?>" name="type" />
          <input type="hidden" value="<?php echo $page ?>" name="page" />
          <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="id" value="<?php echo $id ?>"/>
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