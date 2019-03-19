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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var update_type = $("#update_type").val();
		if(!update_type){
			$("#update_type").focus();
			return false;
		}
		var delay_date = $("#delay_date").val();
		if(update_type == 'B' && delay_date == '0000-00-00'){
			alert('请选择延期时间');
			return false;
		}
		var update_content = $("#update_content").val();
		if(!$.trim(update_content)){
			$("#update_content").focus();
			return false;
		}
		var employee = $("#employee").val();
		if(!$.trim(employee)){
			$("#employee").focus();
			return false;
		}
	})
	$("#update_type").change(function(){
		var update_type = $(this).val();
		if(update_type == 'B'){
			$("#delay_date").attr('disabled',false);
		}else{
			$("#delay_date").attr('disabled',true);
		}
	})
})
</script>
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $workid = fun_check_int($_GET['id']);
	  $sql_work = "SELECT `work_content`,`deadline_date` FROM `db_work` WHERE `workid` = '$workid' AND `worker` = '$employeeid' AND `work_status` = 1 AND `pdca_status` IN ('P','D')";
	  $result_work = $db->query($sql_work);
	  if($result_work->num_rows){
		  $array_work = $result_work->fetch_assoc();
  ?>
  <h4>工作反馈添加</h4>
  <form action="work_updatedo.php" name="work_update" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">工作内容：</th>
        <td width="80%"><?php echo $array_work['work_content']; ?></td>
      </tr>
      <tr>
        <th>期限时间：</th>
        <td><?php echo $array_work['deadline_date']; ?></td>
      </tr>
      <tr>
        <th>反馈类型：</th>
        <td><select name="update_type" id="update_type">
            <option value="">请选择</option>
            <?php
			foreach($array_pdca_update_type as $update_type_key=>$update_type_value){
				echo "<option value=\"".$update_type_key."\">".$update_type_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>延期时间：</th>
        <td><input type="text" name="delay_date" id="delay_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" disabled="disabled" /></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><textarea name="update_content" cols="80" rows="6" class="input_txt" id="update_content"></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee" id="employee" class="input_txt" />
          <span class="tag"> *必填</span></td>
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
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  }elseif($action == "edit"){
	  $updateid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_work_update`.`update_content`,`db_work_update`.`update_type`,`db_work_update`.`delay_date`,`db_work_update`.`employee`,`db_work_update`.`workid`,`db_work`.`work_content`,`db_work`.`deadline_date` FROM `db_work_update` INNER JOIN `db_work` ON `db_work`.`workid` = `db_work_update`.`workid` WHERE `db_work_update`.`updateid` = '$updateid' AND `db_work_update`.`employeeid` = '$employeeid' AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` IN ('P','D')";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $workid = $array['workid'];
  ?>
  <h4>工作反馈修改</h4>
  <form action="work_updatedo.php" name="work_update" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">工作内容：</th>
        <td width="80%"><?php echo $array['work_content']; ?></td>
      </tr>
      <tr>
        <th>期限时间：</th>
        <td><?php echo $array['deadline_date']; ?></td>
      </tr>
      <tr>
        <th>反馈类型：</th>
        <td><select name="update_type" id="update_type">
            <?php foreach($array_pdca_update_type as $update_type_key=>$update_type_value){ ?>
            <option value="<?php echo $update_type_key; ?>"<?php if($update_type_key == $array['update_type']) echo " selected=\"selected\""; ?>><?php echo $update_type_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>延期时间：</th>
        <td><input type="text" name="delay_date" id="delay_date" value="<?php echo $array['delay_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if($array['update_type'] != 'B') echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><textarea name="update_content" cols="80" rows="6" class="input_txt" id="update_content"><?php echo codetextarea($array['update_content']); ?></textarea>
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><input type="text" name="employee" id="employee" value="<?php echo $array['employee']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
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
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'WKUP' AND `db_upload_file`.`linkid` = '$updateid' ORDER BY `db_upload_file`.`fileid` ASC";
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
<?php
$sql_update = "SELECT `updateid`,`update_type`,`update_content`,`employee`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `update_date` FROM `db_work_update` WHERE `workid` = '$workid' ORDER BY `dotime` DESC";
$result_update = $db->query($sql_update);
$result_id = $db->query($sql_update);
?>
<div id="table_list">
  <?php
  if($result_update->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_updated .= $row_id['updateid'].',';
	  }
	  $array_updateid = rtrim($array_updated,',');
	  //Group 工作更新附件
	  $sql_update_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'WKUP' AND `linkid` IN ($array_updateid) GROUP BY `linkid`";
	  $result_update_file = $db->query($sql_update_file);
	  if($result_update_file->num_rows){
		  while($row_update_file = $result_update_file->fetch_assoc()){
			  $array_update_file[$row_update_file['linkid']] = $row_update_file['file_url'];
		  }
	  }else{
		  $array_update_file = array();
	  }
	  //print_r($array_update_file);
	  //Group 最后指示内容
	  $sql_reply_content = "SELECT `updateid`,`replyid`,`reply_content`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `reply_date` FROM `db_work_reply` WHERE `replyid` IN (SELECT SUBSTRING_INDEX(GROUP_CONCAT(`replyid` ORDER BY `replyid` DESC),',',1) FROM `db_work_reply` WHERE `updateid` IN ($array_updateid) GROUP BY `updateid`)";
	  $result_reply_content = $db->query($sql_reply_content);
	  if($result_reply_content->num_rows){
		  while($row_reply_content = $result_reply_content->fetch_assoc()){
			  $reply_content = $row_reply_content['reply_content']."<br />".$row_reply_content['reply_date'];
			  $array_reply_content[$row_reply_content['updateid']] = array('reply_content'=>$reply_content,'replyid'=>$row_reply_content['replyid']);
		  }
	  }else{
		  $array_reply_content = array();
	  }
	  //print_r($array_reply_content);
	  //Group 最后指示内容附件
	  $sql_reply_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'WKRP' AND `linkid` IN (SELECT SUBSTRING_INDEX(GROUP_CONCAT(`replyid` ORDER BY `replyid` DESC),',',1) FROM `db_work_reply` WHERE `updateid` IN ($array_updateid) GROUP BY `updateid`) GROUP BY `linkid`";
	  $result_reply_file = $db->query($sql_reply_file);
	  if($result_reply_file->num_rows){
		  while($row_reply_file = $result_reply_file->fetch_assoc()){
			  $array_reply_file[$row_reply_file['linkid']] = $row_reply_file['file_url'];
		  }
	  }else{
		  $array_reply_file = array();
	  }
	  //print_r($array_reply_file);
  ?>
  <table>
    <caption>
    工作反馈
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">反馈时间</th>
      <th width="8%">类型</th>
      <th width="36%">内容</th>
      <th width="8%">负责人</th>
      <th width="36%">最新批示</th>
    </tr>
    <?php
      while($row_update = $result_update->fetch_assoc()){
		  $updateid = $row_update['updateid'];
		  //工作更新附件
		  $array_update_filelist = array_key_exists($updateid,$array_update_file)?explode('#',$array_update_file[$updateid]):'';
		  $update_file_content = '';
		  if(is_array($array_update_filelist)){
			  $a = 1;
			  foreach($array_update_filelist as $update_filelist){
				  $update_file_content .= "<br />".$a.'.'.$update_filelist;
				  $a++;
			  }
		  }
		  $reply_content = array_key_exists($updateid,$array_reply_content)?$array_reply_content[$updateid]['reply_content']:'--';
		  $replyid = array_key_exists($updateid,$array_reply_content)?$array_reply_content[$updateid]['replyid']:'';
		  //最后指示内容附件
		  $array_reply_filelist = array_key_exists($replyid,$array_reply_file)?explode('#',$array_reply_file[$replyid]):'';
		  $reply_file_content = '';
		  if(is_array($array_reply_filelist)){
			  $b = 1;
			  foreach($array_reply_filelist as $reply_filelist){
				  $reply_file_content .= "<br />".$b.'.'.$reply_filelist;
				  $b++;
			  }
		  }  
	  ?>
    <tr>
      <td><?php echo $updateid; ?></td>
      <td><?php echo $row_update['update_date']; ?></td>
      <td><?php echo $array_pdca_update_type[$row_update['update_type']]; ?></td>
      <td style="text-align:left;"><?php echo $row_update['update_content'].$update_file_content; ?></td>
      <td><?php echo $row_update['employee']; ?></td>
      <td><?php echo $reply_content.$reply_file_content; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>