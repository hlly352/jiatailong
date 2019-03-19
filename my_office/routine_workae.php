<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$array_shell = $_SESSION['system_shell'];
if(!$array_shell[$system_dir]['isadmin']){
	die('您暂无权限！');
}
$action = fun_check_action($_GET['action']);
//部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` ASC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
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
		var deptid = $("#deptid").val();
		if(!deptid){
			$("#deptid").focus();
			return false;
		}
		var work_title = $("#work_title").val();
		if(!$.trim(work_title)){
			$("#work_title").focus();
			return false;
		}
		var work_type = $("#work_type").val();
		var work_month = $("#work_month").val();
		var work_date = $("#work_date").val();
		var work_week = $('input[name^=work_week]:checked').length;
		if(work_type == 'A' && !work_week){
			alert('请选择每周星期');
			return false;
		}else if(work_type == 'B' && !work_month){
			alert('请选择每月日期');
			return false;
		}else if(work_type == 'C' && !work_date){
			alert('请选择固定日期');
			return false;
		}
	})
	$("#work_type").change(function(){
		var work_type = $(this).val();
		if(work_type == 'A'){
			$("#work_date").val('');
			$("#work_month").attr('disabled',true);
			$("#work_date").attr('disabled',true);
			$('input[name^=work_week]').attr('disabled',false);
		}else if(work_type == 'B'){
			$("#work_date").val('');
			$('input[name^=work_week]').attr('checked',false);
			$('input[name^=work_week]').attr('disabled',true);
			$("#work_date").attr('disabled',true);
			$("#work_month").attr('disabled',false);
		}else if(work_type == 'C'){
			$("#work_date").attr('disabled',false);
			$('input[name^=work_week]').attr('disabled',true);
			$('input[name^=work_week]').attr('checked',false);
			$("#work_month").attr('disabled',true);
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
  <h4>例行工作添加</h4>
  <form action="routine_workdo.php" name="routine_work" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><select name="deptid" id="deptid">
            <option value="">请选择</option>
            <?php
            if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
					echo "<option value=\"".$row_dept['deptid']."\">".$row_dept['dept_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>工作标题：</th>
        <td><input type="text" name="work_title" id="work_title" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>工作内容：</th>
        <td><textarea name="work_content" cols="60" rows="4" class="input_txt"></textarea></td>
      </tr>
      <tr>
        <th>工作类型：</th>
        <td><select name="work_type" id="work_type">
            <?php
            foreach($array_routine_work_type as $work_type_key=>$work_type_value){
				echo "<option value=\"".$work_type_key."\">".$work_type_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>每周星期：</th>
        <td><?php
            foreach($array_week as $week_key=>$week_value){
				echo " <input type=\"checkbox\" name=\"work_week[]\" value=\"".$week_key."\" /> ".$week_value;
			}
			?></td>
      </tr>
      <tr>
        <th>每月日期：</th>
        <td><select name="work_month" id="work_month" disabled="disabled">
            <option value="">请选择</option>
            <?php
			for($i=1;$i<=31;$i++){
				echo "<option value=\"".$i."\">".$i."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>固定日期：</th>
        <td><input type="text" name="work_date" id="work_date" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" disabled="disabled" /></td>
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
	  $workid = fun_check_int($_GET['id']);
	  $sql = "SELECT `work_title`,`work_content`,`work_type`,`work_week`,`work_month`,`work_date`,`work_date`,`work_status`,`deptid` FROM `db_routine_work` WHERE `workid` = '$workid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $work_type = $array['work_type'];
		  $array_work_week = ($work_type == 'A')?explode(',',$array['work_week']):array();
		  $work_date = ($work_type == 'C')?$array['work_date']:'';
  ?>
  <h4>例行工作修改</h4>
  <form action="routine_workdo.php" name="routine_work" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">部门：</th>
        <td width="80%"><select name="deptid" id="deptid">
            <option value="">请选择</option>
            <?php
            if($result_dept->num_rows){
				while($row_dept = $result_dept->fetch_assoc()){
			?>
            <option value="<?php echo $row_dept['deptid']; ?>"<?php if($row_dept['deptid'] == $array['deptid']) echo " selected=\"selected\""; ?>><?php echo $row_dept['dept_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>工作标题：</th>
        <td><input type="text" name="work_title" id="work_title" value="<?php echo $array['work_title']; ?>" class="input_txt" size="50" /></td>
      </tr>
      <tr>
        <th>工作内容：</th>
        <td><textarea name="work_content" cols="60" rows="4" class="input_txt"><?php echo codetextarea($array['work_content']); ?></textarea></td>
      </tr>
      <tr>
        <th>工作类型：</th>
        <td><select name="work_type" id="work_type">
            <?php foreach($array_routine_work_type as $work_type_key=>$work_type_value){ ?>
            <option value="<?php echo $work_type_key; ?>"<?php if($work_type_key == $work_type) echo " selected=\"selected\""; ?>><?php echo $work_type_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>每周星期：</th>
        <td><?php foreach($array_week as $week_key=>$week_value){ ?>
          <input type="checkbox" name="work_week[]" value="<?php echo $week_key; ?>"<?php if($work_type != 'A') echo " disabled=\"disabled\""; ?><?php if(in_array($week_key,$array_work_week)) echo " checked=\"checked\""; ?> />
          <?php echo $week_value; ?>
          <?php }?></td>
      </tr>
      <tr>
        <th>每月日期：</th>
        <td><select name="work_month" id="work_month"<?php if($work_type != 'B') echo " disabled=\"disabled\""; ?>>
            <option value="">请选择</option>
            <?php for($i=1;$i<=31;$i++){ ?>
            <option value="<?php echo $i; ?>"<?php if($i==$array['work_month']) echo " selected=\"selected\""; ?>><?php echo $i; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>固定日期：</th>
        <td><input type="text" name="work_date" id="work_date" value="<?php echo $work_date; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if($work_type != 'C') echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="work_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['work_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?>
            </option>
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
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
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
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'RW' AND `db_upload_file`.`linkid` = '$workid' ORDER BY `db_upload_file`.`fileid`";
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