<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
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
		var plan_content = $("#plan_content").val();
		if(!$.trim(plan_content)){
			$("#plan_content").focus();
			return false;
		}
		var start_date = $("#start_date").val();
		var finish_date = $("#finish_date").val();
		if(GetDateDiff(start_date,finish_date,'day') < 0){
			alert('开始与结束时间间隔异常，请重新输入！');
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
  <?php if($action == "add"){ ?>
  <h4>计划添加</h4>
  <form action="job_plando.php" name="job_plan" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">类型：</th>
        <td width="80%"><select name="plan_type">
            <?php
            foreach($array_job_plan_type as $plan_type_key=>$paln_type_value){
				echo "<option value=\"".$plan_type_key."\">".$paln_type_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><textarea name="plan_content" id="plan_content" cols="80" rows="6" class="input_txt"></textarea></td>
      </tr>
      <tr>
        <th>开始日期：</th>
        <td><input type="text" name="start_date" id="start_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>结束日期：</th>
        <td><input type="text" name="finish_date" id="finish_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $planid = fun_check_int($_GET['id']);
	  $sql = "SELECT `plan_content`,`plan_content`,`plan_status`,`plan_result`,`plan_type`,`start_date`,`finish_date` FROM `db_job_plan` WHERE `planid` = '$planid' AND `employeeid` = '$employeeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>计划修改</h4>
  <form action="job_plando.php" name="job_plan" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">类型：</th>
        <td width="80%"><select name="plan_type">
            <?php foreach($array_job_plan_type as $plan_type_key=>$paln_type_value){ ?>
            <option value="<?php echo $plan_type_key; ?>"<?php if($plan_type_key == $array['plan_type']) echo " selected=\"selected\""; ?>><?php echo $paln_type_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>内容：</th>
        <td><textarea name="plan_content" id="plan_content" cols="80" rows="6" class="input_txt"><?php echo codetextarea($array['plan_content']); ?></textarea></td>
      </tr>
      <tr>
        <th>开始日期：</th>
        <td><input type="text" name="start_date" id="start_date" value="<?php echo $array['start_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>结束日期：</th>
        <td><input type="text" name="finish_date" id="finish_date" value="<?php echo $array['finish_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>完成：</th>
        <td><select name="plan_result">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $array['plan_result']) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="plan_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['plan_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
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
          <input type="button" name="button" value="删除" class="button" onclick="JavaScript:location.href='job_plan_del.php?id=<?php echo $planid; ?>';return confirm('系统提示:确定删除吗?')" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="planid" value="<?php echo $planid; ?>" />
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
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'JP' AND `db_upload_file`.`linkid` = '$planid' ORDER BY `db_upload_file`.`fileid`";
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