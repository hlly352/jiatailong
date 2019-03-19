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
	$("#submit_reply").click(function(){
		var reply_content = $("#reply_content").val();
		if(!$.trim(reply_content)){
			$("#reply_content").focus();
			return false;
		}
	})
	//全选，反选，清除 
	$("#CheckedAll_other").click(function(){
		$('[name^=replyid]:checkbox').attr('checked',true);
		$('[id=submit_other]').attr('disabled',false);
	});
	$("#CheckedNo_other").click(function(){
		$('[name^=replyid]:checkbox').attr('checked',false);
		$('[id=submit_other]').attr('disabled',true);
	});
	$("#CheckedRev_other").click(function(){
		$('[name^=replyid]:checkbox').each(function(){
			this.checked=!this.checked;
		});
		flag=false;
		if(!$('[name^=replyid]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit_other]').attr('disabled',flag);
	});
	//checkbox id 选择
	$('[name^=replyid]:checkbox').click(function(){
		flag=false;
		if(!$('[name^=replyid]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit_other]').attr('disabled',flag);
	});
})
</script>
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $updateid = fun_check_int($_GET['id']);
	  $sql_update = "SELECT `db_work_update`.`update_content`,`db_work_update`.`update_type`,`db_work_update`.`delay_date`,`db_work_update`.`employee`,DATE_FORMAT(`db_work_update`.`dotime`,'%Y-%m-%d') AS `update_date` FROM `db_work_update` INNER JOIN `db_work` ON `db_work`.`workid` = `db_work_update`.`workid` WHERE `db_work_update`.`updateid` = '$updateid' AND `db_work`.`issuer` = '$employeeid' AND `db_work`.`work_status` = 1";
	  $result_update = $db->query($sql_update);
	  if($result_update->num_rows){
		  $array_update = $result_update->fetch_assoc();
		  $update_type = $array_update['update_type'];
		  $delay_date = ($update_type == 'B')?'['.$array_update['delay_date'].']':'';
		  //工作附件
		  $sql_update_file = "SELECT `fileid`,`upfilename` FROM `db_upload_file` WHERE `linkcode` = 'WKUP' AND `linkid` = '$updateid' ORDER BY `fileid` ASC";
		  $result_update_file = $db->query($sql_update_file);
		  if($result_update_file->num_rows){
			  $a = 1;
			  while($row_update_file = $result_update_file->fetch_assoc()){ 
				  $work_update_file .= $a.".<a href=\"../upload/download_file.php?id=".$row_update_file['fileid']."\">".$row_update_file['upfilename']."</a><br />";
				  $a++;
			  }
		  }else{
			  $work_update_file = '--';
		  }
  ?>
  <h4>工作批示添加</h4>
  <form action="work_replydo.php" name="work_reply" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">反馈时间：</th>
        <td width="80%"><?php echo $array_update['update_date']; ?></td>
      </tr>
      <tr>
        <th>反馈类型：</th>
        <td><?php echo $array_pdca_update_type[$update_type].$delay_date; ?></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><?php echo $array_update['update_content']; ?></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><?php echo $array_update['employee']; ?></td>
      </tr>
      <tr>
        <th>反馈附件：</th>
        <td><?php echo $work_update_file; ?></td>
      </tr>
      <tr>
        <th>批示内容：</th>
        <td><textarea name="reply_content" cols="80" rows="6" class="input_txt" id="reply_content"></textarea></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加文件" class="button_addfile" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit_reply" value="确定" class="button" />
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
  }elseif($action == "edit"){
	  $replyid = fun_check_int($_GET['id']);
	  $sql = "SELECT DATE_FORMAT(`db_work_update`.`dotime`,'%Y-%m-%d') AS `update_date`,`db_work_update`.`update_content`,`db_work_update`.`update_type`,`db_work_update`.`delay_date`,`db_work_update`.`employee`,`db_work_reply`.`updateid`,`db_work_reply`.`reply_content` FROM `db_work_reply` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_work_reply`.`updateid` INNER JOIN `db_work` ON `db_work`.`workid` = `db_work_update`.`workid` WHERE `db_work_reply`.`replyid` = '$replyid' AND `db_work_reply`.`employeeid` = '$employeeid' AND `db_work`.`work_status` = 1";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $updateid = $array['updateid'];
		  $update_type = $array['update_type'];
		  $delay_date = ($update_type == 'B')?'['.$array['delay_date'].']':'';
		  //工作附件
		  $sql_update_file = "SELECT `fileid`,`upfilename` FROM `db_upload_file` WHERE `linkcode` = 'WKUP' AND `linkid` = '$updateid'";
		  $result_update_file = $db->query($sql_update_file);
		  if($result_update_file->num_rows){
			  $a = 1;
			  while($row_update_file = $result_update_file->fetch_assoc()){ 
				  $work_update_file .= $a.".<a href=\"../upload/download_file.php?id=".$row_update_file['fileid']."\">".$row_update_file['upfilename']."</a><br />";
				  $a++;
			  }
		  }else{
			  $work_update_file = '--';
		  }
  ?>
  <h4>工作批示修改</h4>
  <form action="work_replydo.php" name="work_reply" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">反馈时间：</th>
        <td width="80%"><?php echo $array['update_date']; ?></td>
      </tr>
      <tr>
        <th>反馈类型：</th>
        <td><?php echo $array_pdca_update_type[$update_type].$delay_date; ?></td>
      </tr>
      <tr>
        <th>反馈内容：</th>
        <td><?php echo $array['update_content']; ?></td>
      </tr>
      <tr>
        <th>责任人：</th>
        <td><?php echo $array['employee']; ?></td>
      </tr>
      <tr>
        <th>反馈附件：</th>
        <td><?php echo $work_update_file; ?></td>
      </tr>
      <tr>
        <th>批示内容：</th>
        <td><textarea name="reply_content" cols="80" rows="6" class="input_txt" id="reply_content"><?php echo codetextarea($array['reply_content']); ?></textarea></td>
      </tr>
      <tr>
        <th>附件：</th>
        <td><input type="button" name="button" id="add_file" value="添加文件" class="button_addfile" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit_reply" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="replyid" value="<?php echo $replyid; ?>" />
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
	$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'WKRP' AND `db_upload_file`.`linkid` = '$replyid' ORDER BY `db_upload_file`.`fileid` ASC";
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
$sql_reply = "SELECT `db_work_reply`.`replyid`,`db_work_reply`.`reply_content`,`db_work_reply`.`dotime`,`db_work_reply`.`employeeid`,DATE_FORMAT(`db_work_reply`.`dotime`,'%Y-%m-%d') AS `reply_date`,`db_employee`.`employee_name` FROM `db_work_reply` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work_reply`.`employeeid` WHERE `db_work_reply`.`updateid` = '$updateid' ORDER BY `db_work_reply`.`dotime` DESC";
$result_reply = $db->query($sql_reply);
$result_replyid = $db->query($sql_reply);
?>
<div id="table_list">
  <?php
  if($result_reply->num_rows){
	  while($row_replyid = $result_replyid->fetch_assoc()){
		  $array_replyid .= $row_replyid['replyid'].',';
	  }
	  $array_replyid = rtrim($array_replyid,',');
	  //Group 工作附件
	  $sql_reply_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'WKRP' AND `linkid` IN ($array_replyid) GROUP BY `linkid`";
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
  <form action="work_replydo.php" name="reply_list" method="post">
    <table>
      <caption>
      工作批示
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">批示时间</th>
        <th>内容</th>
        <th width="10%">操作人</th>
        <th width="10%">时间</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
	  while($row_reply = $result_reply->fetch_assoc()){
		  $replyid = $row_reply['replyid'];
		  //工作附件
		  $array_reply_filelist = array_key_exists($replyid,$array_reply_file)?explode('#',$array_reply_file[$replyid]):'';
		  $reply_file_content = '';
		  if(is_array($array_reply_filelist)){
			  $a = 1;
			  foreach($array_reply_filelist as $reply_filelist){
				  $reply_file_content .= "<br />".$a.'.'.$reply_filelist;
				  $a++;
			  }
		  }
	  ?>
      <tr>
        <td><input type="checkbox" name="replyid[]" value="<?php echo $replyid; ?>"<?php if($employeeid != $row_reply['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_reply['reply_date']; ?></td>
        <td style="text-align:left;"><?php echo $row_reply['reply_content'].$reply_file_content; ?></td>
        <td><?php echo $row_reply['employee_name']; ?></td>
        <td><?php echo $row_reply['dotime']; ?></td>
        <td><?php if($employeeid == $row_reply['employeeid']){ ?><a href="work_replyae.php?id=<?php echo $replyid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll_other" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev_other" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo_other" value="清除" />
      <input type="submit" name="submit" id="submit_other" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="button" name="button" value="添加" class="select_button" onclick="location.href='work_replyae.php?id=<?php echo $updateid; ?>&action=add'" />
      <input type="hidden" name="updateid" value="<?php echo $updateid; ?>" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>