<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$workid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//工作信息
$sql = "SELECT `db_work`.`issuer`,`db_work`.`work_content`,`db_work`.`issue_date`,`db_work`.`deadline_date`,`db_work`.`finish_date`,`db_work`.`pdca_status`,`db_work`.`work_status`,`db_work`.`dotime`,DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) AS `diff_finishdate`,DATEDIFF(CURDATE(),`db_work`.`deadline_date`) AS `diff_curdate`,`db_issuer`.`employee_name` AS `issuer_name`,`db_worker`.`employee_name` AS `worker_name` FROM `db_work` INNER JOIN `db_employee` AS `db_issuer` ON `db_issuer`.`employeeid` = `db_work`.`issuer` INNER JOIN `db_employee` AS `db_worker` ON `db_worker`.`employeeid` = `db_work`.`worker` WHERE `db_work`.`workid` = '$workid'";
$result = $db->query($sql);
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
<title>PDCA-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $issuer = $array['issuer'];
	  $deadline_date = ($array['deadline_date'] == '0000-00-00')?'--':$array['deadline_date'];
	  $finish_date = ($array['finish_date'] == '0000-00-00')?'--':$array['finish_date'];
	  //判断执行结果
	  $pdca_status = $array['pdca_status'];
	  $work_status = $array['work_status'];
	  $diff_finishdate = $array['diff_finishdate'];
	  $diff_curdate = $array['diff_curdate'];
	  if($pdca_status == 'P'){
		  $pdca_result = "<font color=orange>未接受</font>";
	  }elseif($pdca_status == 'D'){
		  if($diff_curdate > 0){
			  $pdca_result = "<font color=red>超期未完成</font>";
		  }else{
			  $pdca_result = "<font color=green>执行中</font>";
		  }
		  
	  }elseif($pdca_status == 'C'){
		  if($diff_finishdate > 0){
			  $pdca_result = "<font color=red>超期完成</font>";
		  }else{
			  $pdca_result = "<font color=green>按时完成</font>";
		  }
	  }
	  //查工作附件
	  $sql_work_file = "SELECT `fileid`,`upfilename` FROM `db_upload_file` WHERE `linkcode` = 'WK' AND `linkid` = '$workid' ORDER BY `fileid` ASC";
	  $result_work_file = $db->query($sql_work_file);
	  if($result_work_file->num_rows){
		  $a = 1;
		  while($row_work_file = $result_work_file->fetch_assoc()){
			  $work_file .= $a.".<a href=\"../upload/download_file.php?id=".$row_work_file['fileid']."\">".$row_work_file['upfilename']."</a><br />";
			  $a++;
		  }
	  }else{
		  $work_file = '--';
	  }
  ?>
  <h4>工作信息</h4>
  <table>
    <tr>
      <th width="20%">发布人：</th>
      <td width="80%"><?php echo $array['issuer_name']; ?></td>
    </tr>
    <tr>
      <th>发布时间：</th>
      <td><?php echo $array['issue_date']; ?></td>
    </tr>
    <tr>
      <th>工作内容：</th>
      <td><?php echo $array['work_content']; ?></td>
    </tr>
    <tr>
      <th>附件：</th>
      <td><?php echo $work_file; ?></td>
    </tr>
    <tr>
      <th>责任人：</th>
      <td><?php echo $array['worker_name']; ?></td>
    </tr>
    <tr>
      <th>期限时间：</th>
      <td><?php echo $deadline_date; ?></td>
    </tr>
    <tr>
      <th>完成时间：</th>
      <td><?php echo $finish_date; ?></td>
    </tr>
    <tr>
      <th>PDCA状态：</th>
      <td><?php echo $pdca_status.'-'.$array_pdca_status[$pdca_status]; ?></td>
    </tr>
    <tr>
      <th>执行状态：</th>
      <td><?php echo $pdca_result; ?></td>
    </tr>
    <tr>
      <th>任务状态：</th>
      <td><?php echo $array_status[$work_status]; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
    </tr>
  </table>
  <?php
	  }else{
		  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
	  }
  ?>
</div>
<?php
//工作计划
$sql_plan = "SELECT `db_work_plan`.`planid`,`db_work_plan`.`plan_content`,`db_work_plan`.`end_date`,`db_work_plan`.`employee`,`db_work_plan`.`dotime`,`db_employee`.`employee_name` FROM `db_work_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work_plan`.`employeeid` WHERE `db_work_plan`.`workid` = '$workid' ORDER BY `db_work_plan`.`end_date` ASC,`db_work_plan`.`planid` ASC";
$result_plan = $db->query($sql_plan);
?>
<div id="table_list">
  <?php if($result_plan->num_rows){ ?>
  <table>
    <caption>
    工作计划
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th>内容</th>
      <th width="10%">截止时间</th>
      <th width="10%">负责人</th>
      <th width="10%">操作人</th>
      <th width="10%">时间</th>
    </tr>
    <?php while($row_plan = $result_plan->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_plan['planid']; ?></td>
      <td style="text-align:left;"><?php echo $row_plan['plan_content']; ?></td>
      <td><?php echo $row_plan['end_date']; ?></td>
      <td><?php echo $row_plan['employee']; ?></td>
      <td><?php echo $row_plan['employee_name']; ?></td>
      <td><?php echo $row_plan['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php
$sql_update = "SELECT `updateid`,`update_type`,`delay_date`,`update_content`,`employee`,`employeeid`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `update_date` FROM `db_work_update` WHERE `workid` = '$workid' ORDER BY `dotime` DESC";
$result_update = $db->query($sql_update);
$result_updateid = $db->query($sql_update);
?>
<div id="table_list">
  <?php
  if($result_update->num_rows){
	  while($row_updateid = $result_updateid->fetch_assoc()){
		  $array_updated .= $row_updateid['updateid'].',';
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
			  $reply_content = $row_reply_content['reply_content']."<br />".'['.$row_reply_content['reply_date'].']';
			  $array_reply_content[$row_reply_content['updateid']] = array('reply_content'=>$reply_content,'replyid'=>$row_reply_content['replyid']);
		  }
	  }else{
		  $array_reply_content = array();
	  }
	  //print_r($array_reply_content);
	  //Group 最后批示内容附件
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
      <th width="34%">内容</th>
      <th width="8%">责任人</th>
      <th width="34%">最新批示</th>
      <th width="4%">Reply</th>
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
		//最后批示内容附件
		$array_reply_filelist = array_key_exists($replyid,$array_reply_file)?explode('#',$array_reply_file[$replyid]):'';
		$reply_file_content = '';
		if(is_array($array_reply_filelist)){
			$b = 1;
			foreach($array_reply_filelist as $reply_filelist){
				$reply_file_content .= "<br />".$b.'.'.$reply_filelist;
				$b++;
			}
		}
		$delay_date = ($row_update['update_type'] == 'B')?"<br />".$row_update['delay_date']:'';
	?>
    <tr>
      <td><?php echo $updateid; ?></td>
      <td><?php echo $row_update['update_date']; ?></td>
      <td><?php echo $array_pdca_update_type[$row_update['update_type']].$delay_date; ?></td>
      <td style="text-align:left;"><?php echo $row_update['update_content'].$update_file_content; ?></td>
      <td><?php echo $row_update['employee']; ?></td>
      <td style="text-align:left;"><?php echo $reply_content.$reply_file_content; ?></td>
      <td><?php if($employeeid == $issuer && $work_status == 1){ ?><a href="work_replyae.php?id=<?php echo $updateid; ?>&action=add"><img src="../images/system_ico/reply_10_10.png" width="10" height="10" /></a><?php } ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>