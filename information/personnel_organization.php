<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
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
		var filepath = $("#file").val();	
		var extStart = filepath.lastIndexOf(".")+1;
		var ext = filepath.substring(extStart, filepath.length).toUpperCase();
		var allowtype = ["JPG","GIF","PNG"];
		if($.inArray(ext,allowtype) == -1)
		{
			alert("请选择正确文件类型");
			return false;
		}
	})
})
</script>
<title>信息发布-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <h4>组织架构上传更新</h4>
  <form action="personnel_organizationdo.php" name="personnel_organization" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th>文件：</th>
        <td><input type="file" name="file" id="file" class="input_file" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="更新" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" /></td>
      </tr>
    </table>
  </form>
</div>
<?php include "../footer.php"; ?>
</body>
</html>