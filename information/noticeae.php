<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../fckeditor/fckeditor.php';
require_once 'shell.php';
$sBasePath = '../fckeditor/';
$oFCKeditor = new FCKeditor('notice_content'); 
$oFCKeditor->BasePath = $sBasePath ;
$action = fun_check_action($_GET['action']);
//读取类型
$sql_notice_type = "SELECT `notice_typeid`,`notice_typename` FROM `db_notice_type`";
$result_notice_type = $db->query($sql_notice_type);
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
		var notice_title = $("#notice_title").val();
		if(!$.trim(notice_title)){
			$("#notice_title").focus();
			return false;
		}
		var notice_typeid = $("#notice_typeid").val();
		if(!notice_typeid){
			$("#notice_typeid").focus();
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
  <?php if($action == "add"){ ?>
  <h4>通知公告发布</h4>
  <form action="noticedo.php" name="notice" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">标题：</th>
        <td width="80%"><input type="text" name="notice_title" id="notice_title" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><?php 
		$oFCKeditor->Value = ''; 
		$oFCKeditor->Create(); 
		?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="notice_typeid" id="notice_typeid">
            <option value="">请选择</option>
            <?php
			if($result_notice_type->num_rows){
				while($row_notice_type = $result_notice_type->fetch_assoc()){
					echo "<option value=\"".$row_notice_type['notice_typeid']."\">".$row_notice_type['notice_typename']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加附件" class="button_addfile" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定"class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $noticeid = fun_check_int($_GET['id']);
	  $sql = "SELECT `notice_title`,`notice_content`,`notice_typeid`,`notice_status` FROM `db_notice` WHERE `noticeid` = '$noticeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>通知公告修改</h4>
  <form action="noticedo.php" name="notice" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">标题：</th>
        <td width="80%"><input type="text" name="notice_title" id="notice_title" value="<?php echo $array['notice_title']; ?>" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><?php 
		$oFCKeditor->Value = $array['notice_content']; 
		$oFCKeditor->Create(); 
		?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="notice_typeid" id="notice_typeid">
            <option value="">请选择</option>
            <?php
			if($result_notice_type->num_rows){
				while($row_notice_type = $result_notice_type->fetch_assoc()){
			?>
            <option value=<?php echo $row_notice_type['notice_typeid']; ?>""<?php if($row_notice_type['notice_typeid'] == $array['notice_typeid']) echo " selected=\"selected\""; ?>><?php echo $row_notice_type['notice_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="notice_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['notice_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加附件" class="button_addfile" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定"class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="noticeid" value="<?php echo $noticeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
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
<?php
if($action == "edit"){
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'NT' AND `db_upload_file`.`linkid` = '$noticeid' ORDER BY `db_upload_file`.`fileid` DESC";
	$result_file = $db->query($sql_file);
?>
<div id="table_list">
  <?php if($result_file->num_rows){ ?>
  <form action="../upload/upload_filedo.php" name="file_list" method="post">
    <table>
      <caption>
      附件列表
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th>文件名称</th>
        <th width="10%">文件大小</th>
        <th width="10%">上传人</th>
        <th width="10%">时间</th>
        <th width="4%">Down</th>
        <th width="4%">URL</th>
      </tr>
      <?php
      while($row_file = $result_file->fetch_assoc()){
		  $fileid = $row_file['fileid'];
		  $filedir = $row_file['filedir'];
		  $filename = $row_file['filename'];
		  $file_path = "../upload/file/".$filedir.'/'.$filename;
		  $file_path_url = "/upload/file/".$filedir.'/'.$filename;
		  $filesize = (is_file)?fun_sizeformat(filesize($file_path)):0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $fileid; ?>" /></td>
        <td><?php echo $row_file['upfilename']; ?></td>
        <td><?php echo $filesize; ?></td>
        <td><?php echo $row_file['employee_name']; ?></td>
        <td><?php echo $row_file['dotime']; ?></td>
        <td><a href="../upload/download_file.php?id=<?php echo $fileid; ?>"><img src="../images/system_ico/download_10_10.png" width="10" height="10" /></a></td>
        <td><img src="../images/system_ico/url_10_10.png" width="10" height="10" style="cursor:hand;" onclick="copyToClipboard('<?php echo $file_path_url; ?>')" /></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统：暂无附件！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>