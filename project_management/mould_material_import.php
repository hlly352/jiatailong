<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mouldid = fun_check_int($_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var filepath = $("#file").val();	
		var extStart = filepath.lastIndexOf(".")+1;
		var ext = filepath.substring(extStart, filepath.length).toUpperCase();
		var allowtype = ["XLS"];
		if($.inArray(ext,allowtype) == -1)
		{
			alert("请选择正确文件类型");
			return false;
		}
	})
})
</script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_mould = "SELECT `mould_number` FROM `db_mould` WHERE `mouldid` = '$mouldid'";
  $result_mould = $db->query($sql_mould);
  if($result_mould->num_rows){
	  $array_mould = $result_mould->fetch_assoc();
  ?>
  <h4>物料导入</h4>
  <form action="mould_material_importdo.php" name="mould_material_import" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><?php echo $array_mould['mould_number']; ?></td>
      </tr>
      <tr>
        <th>下单日期：</th>
        <td><?php echo date('Y-m-d'); ?></td>
      </tr>
      <tr>
        <th>导入文件(<a href="../template_file/mould_material.xls" target="_blank">模板</a>)：</th>
        <td><input type="file" name="file" id="file" class="input_file" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mouldid" value="<?php echo $mouldid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>