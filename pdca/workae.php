<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employee_name = $_SESSION['employee_info']['employee_name'];
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
		var work_content = $("#work_content").val();
		if(!$.trim(work_content)){
			$("#work_content").focus();
			return false;
		}
		var employeeid = $("#employeeid").val();
		if(!employeeid){
			$("#employee_name").focus();
			return false;
		}
		var pdca_status = $("#pdca_status").val();
		var deadline_date = $("#deadline_date").val();
		var finish_date = $("#finish_date").val();
		if(pdca_status != 'P' && deadline_date == '0000-00-00'){
			alert('请输入期限时间');
			return false;
		}
		if((pdca_status == 'C' || pdca_status == 'A') && finish_date == '0000-00-00'){
			alert('请输入或完成时间');
			return false;
		}
	})
	$("input[name=employee_name]").keyup(function(){
		var employee_name = $(this).val();
		if($.trim(employee_name)){
			$.post('../ajax_function/employee_name.php',{
				employee_name:employee_name
			},function(data,textstatus){
				$("#employeeid").show();
				$("#employeeid").html(data);
			})
		}else{
			$("#employeeid").hide();
			$("#employeeid").val('');
		}
	})
	$("select[id=employeeid]").dblclick(function(){							   
		var employee_name = $("#employeeid option:selected").text();
		var employeeid = $("#employeeid option:selected").val();
		if(employeeid != ''){
			$("#employee_name").val(employee_name);
			$("#employeeid").hide();
		}
	})
	$("#pdca_status").change(function(){
		var pdca_status = $(this).val();
		var array_pdce = ['P','D'];
		if(jQuery.inArray(pdca_status, array_pdce) == -1){
			$("#finish_date").attr('disabled',false);
		}else{
			$("#finish_date").attr('disabled',true);
		}
		if(pdca_status == 'P'){
			$("#deadline_date").attr('disabled',true);
		}else{
			$("#deadline_date").attr('disabled',false);
		}
	})
})
</script>
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>工作发布</h4>
  <form action="workdo.php" name="work" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">发布人：</th>
        <td width="80%"><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>发布时间：</th>
        <td><input type="text" name="issue_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>工作内容：</th>
        <td><textarea name="work_content" cols="80" rows="8" class="input_txt" id="work_content"></textarea></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee_name" id="employee_name" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span><br />
          <select name="employeeid" id="employeeid" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value=""></option>
          </select></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加文件" class="button_addfile" /></td>
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
	  $workid = fun_check_int($_GET['id']);
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $sql = "SELECT `db_work`.`work_content`,`db_work`.`worker`,`db_work`.`issue_date`,`db_work`.`deadline_date`,`db_work`.`finish_date`,`db_work`.`pdca_status`,`db_work`.`work_status`,`db_department`.`dept_name`,`db_employee`.`employee_name` FROM `db_work` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work`.`worker` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_work`.`workid` = '$workid' AND `db_work`.`issuer` = '$employeeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $pdca_status = $array['pdca_status'];
		  $work_status = $array['work_status'];
  ?>
  <h4>工作修改</h4>
  <form action="workdo.php" name="work" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">发布人：</th>
        <td width="80%"><?php echo $employee_name; ?></td>
      </tr>
      <tr>
        <th>发布时间：</th>
        <td><input type="text" name="issue_date" value="<?php echo $array['issue_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>工作内容：</th>
        <td><textarea name="work_content" cols="80" rows="8" class="input_txt" id="work_content"><?php echo codetextarea($array['work_content']); ?></textarea></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee_name" id="employee_name" value="<?php echo $array['dept_name'].'-'.$array['employee_name'] ?>" class="input_txt" />
          <span class="tag"> *请输入员工姓名后选择</span><br />
          <select name="employeeid" id="employeeid" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['worker']; ?>" selected="selected"><?php echo $array['dept_name'].'-'.$array['employee_name'] ?></option>
          </select></td>
      </tr>
      <tr>
        <th>期限时间：</th>
        <td><input type="text" name="deadline_date" id="deadline_date" value="<?php echo $array['deadline_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if($pdca_status == 'P') echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>PDCA状态：</th>
        <td><select name="pdca_status" id="pdca_status">
            <?php foreach($array_pdca_status as $pdca_status_key=>$pdca_status_value){ ?>
            <option value="<?php echo $pdca_status_key; ?>"<?php if($pdca_status_key == $pdca_status) echo " selected=\"selected\""; ?>><?php echo $pdca_status_key.'-'.$pdca_status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>完成时间：</th>
        <td><input type="text" name="finish_date" id="finish_date" value="<?php echo $array['finish_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if(in_array($pdca_status,array('P','D'))) echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>任务状态：</th>
        <td><select name="work_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $work_status) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加文件" class="button_addfile" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p>系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php
if($action == "edit"){
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'WK' AND `db_upload_file`.`linkid` = '$workid' ORDER BY `db_upload_file`.`fileid`";
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