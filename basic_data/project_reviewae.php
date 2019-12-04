<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$page = $_GET['page'];
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
		var name = $("#name").val();
    if(!name){
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
  <h4>项目评审项目添加</h4>
  <form action="project_review_do.php" name="material_type" method="post">
    <table>
      <tr>
        <th>项目名称：</th>
        <td>
          <input type="text" name="name" id="name" class="input_txt" style="width:70%" />
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
	  $sql = "SELECT * FROM `db_project_review_data` WHERE `id` = '$id'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $row = $result->fetch_assoc();
  ?>
  <h4>模具图纸检查表项目修改</h4>
  <form action="project_review_do.php" name="material_type" method="post">
    <table>
      <tr>
        <th>项目名称：</th>
        <td>
          <input type="text" name="name" value="<?php echo $row['name'] ?>" id="name" class="input_txt" style="width:70%" />
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