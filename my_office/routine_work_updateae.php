<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
		var update_content = $("#update_content").val();
		if(!$.trim(update_content)){
			$("#update_content").focus();
			return false;
		}
	})
})
</script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $workid = fun_check_int($_GET['id']);
	  $date = $_GET['date']?$_GET['date']:date('Y-m-d');
	  $sql = "SELECT `db_routine_work`.`work_title`,`db_routine_work`.`work_title`,`db_routine_work`.`work_content`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND ((`db_routine_work`.`work_type` = 'A' AND FIND_IN_SET((DAYOFWEEK('$date')-1),`db_routine_work`.`work_week`) > 0) OR (`db_routine_work`.`work_type` = 'B' AND DATE_FORMAT('$date','%e') = `db_routine_work`.`work_month`) OR (`db_routine_work`.`work_type` = 'C' AND `db_routine_work`.`work_date` = '$date')) AND `db_routine_work`.`workid` = '$workid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>例行工作反馈添加</h4>
  <form action="routine_work_updatedo.php" name="routine_work_update" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">日期：</th>
        <td width="80%"><?php echo $date; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>标题：</th>
        <td><?php echo $array['work_title']; ?></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><?php echo $array['work_content']; ?></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><textarea name="update_content" id="update_content" cols="60" rows="6" class="input_txt"></textarea></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
          <input type="hidden" name="update_date" value="<?php echo $date; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }elseif($action == "edit"){
	  $updateid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_routine_work_update`.`update_date`,`db_routine_work_update`.`update_content`,`db_routine_work`.`work_title`,`db_routine_work`.`work_content`,`db_department`.`dept_name` FROM `db_routine_work_update` INNER JOIN `db_routine_work` ON `db_routine_work`.`workid` = `db_routine_work_update`.`workid` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`work_status` = 1 AND `db_routine_work_update`.`updateid` = '$updateid' AND `db_routine_work_update`.`employeeid` = '$employeeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>例行工作反馈修改</h4>
  <form action="routine_work_updatedo.php" name="routine_work_update" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">日期：</th>
        <td width="80%"><?php echo $array['update_date']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>标题：</th>
        <td><?php echo $array['work_title']; ?></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><?php echo $array['work_content']; ?></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><textarea name="update_content" id="update_content" cols="60" rows="6" class="input_txt"><?php echo codetextarea($array['update_content']); ?></textarea></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="updateid" value="<?php echo $updateid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  }
  ?>
</div>
<?php
if($action == "edit"){
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'RWUP' AND `db_upload_file`.`linkid` = '$updateid' ORDER BY `db_upload_file`.`fileid` ASC";
	$result_file = $db->query($sql_file);
?>
<div id="table_list">
  <?php if($result_file->num_rows){ ?>
  <form action="../upload/upload_filedo.php" name="list" method="post">
    <table>
      <caption>
      附件列表
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th>文件名称</th>
        <th width="10%">文件大小</th>
        <th width="10%">上传人</th>
        <th width="10%">上传时间</th>
        <th width="4%">Down</th>
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
  <?php } ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>