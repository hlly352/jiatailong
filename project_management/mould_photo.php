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
<link rel="shortcut icon" type="text/css" href="../images/logo/jtl.ico" />
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
<title>基础数据-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql = "SELECT `db_mould`.`project_name`,`db_mould`.`mould_number`,`db_mould`.`image_filedir`,`db_mould`.`image_filename`,`db_client`.`client_code` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `db_mould`.`mouldid` = '$mouldid'";
  $result = $db->query($sql);
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $image_filedir = $array['image_filedir'];
	  $image_filename = $array['image_filename'];
	  $image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
	  $image_big_filepath = "../upload/mould_image/".$image_filedir.'/B'.$image_filename;
	  if(is_file($image_filepath)){
		  $image_file = "<a href=\"".$image_big_filepath."\" target=\"_blank\"><img src=\"".$image_filepath."\" /></a>";
	  }else{
		  $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
	  }
  ?>
  <h4>模具图片上传更新</h4>
  <form action="mould_photodo.php" name="mould_photo" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">客户代码：</th>
        <td width="80%"><?php echo $array['client_code']; ?></td>
      </tr>
      <tr>
        <th>项目名称：</th>
        <td><?php echo $array['project_name']; ?></td>
      </tr>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
      </tr>
      <tr>
        <th>模具照片：</th>
        <td><?php echo $image_file; ?></td>
      </tr>
      <tr>
        <th>更新照片：</th>
        <td><input type="file" name="file" id="file" class="input_file" />
        <span class="tag"> *支持JPG，GIF，PNG图片格式</span></td>
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