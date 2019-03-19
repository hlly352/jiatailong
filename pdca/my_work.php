<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$pdca_status = $_GET['pdca_status'];
	if($pdca_status){
		$sql_pdcastatus = " AND `db_work`.`pdca_status` = '$pdca_status'";
	}
	$sqlwher = " AND `db_employee`.`employee_name` LIKE '%$employee_name%' $sql_pdcastatus";
}
$sql = "SELECT `db_work`.`workid`,`db_work`.`work_content`,`db_work`.`issue_date`,`db_work`.`deadline_date`,`db_work`.`finish_date`,`db_work`.`pdca_status`,`db_work`.`work_status`,`db_employee`.`employee_name` FROM `db_work` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work`.`issuer` WHERE `db_work`.`worker` = '$employeeid' AND `db_work`.`work_status` = 1 $sqlwher";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_work`.`issue_date` DESC,`db_work`.`workid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
<div id="table_search">
  <h4>我的任务</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>发布人：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>PDCA状态：</th>
        <td><select name="pdca_status">
            <option value="">所有</option>
            <?php
			foreach($array_pdca_status as $pdca_status_key=>$pdca_status_value){
				echo "<option value=\"".$pdca_status_key."\">".$pdca_status_key.'-'.$pdca_status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_workid .= $row_id['workid'].',';
	  }
	  $array_workid = rtrim($array_workid,',');
	  //Group 工作附件
	  $sql_work_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'WK' AND `linkid` IN ($array_workid) GROUP BY `linkid`";
	  $result_work_file = $db->query($sql_work_file);
	  if($result_work_file->num_rows){
		  while($row_work_file = $result_work_file->fetch_assoc()){
			  $array_work_file[$row_work_file['linkid']] = $row_work_file['file_url'];
		  }
	  }else{
		  $array_work_file = array();
	  }
	  //Group 最后更新内容
	  $sql_update_content = "SELECT `workid`,`updateid`,`update_type`,`delay_date`,`update_content`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `update_date` FROM `db_work_update` WHERE `updateid` IN (SELECT SUBSTRING_INDEX(GROUP_CONCAT(`updateid` ORDER BY `updateid` DESC),',',1) FROM `db_work_update` WHERE `workid` IN ($array_workid) GROUP BY `workid`)";
	  $result_update_content = $db->query($sql_update_content);
	  if($result_update_content->num_rows){
		  while($row_update_content = $result_update_content->fetch_assoc()){
			  $update_type = $row_update_content['update_type'];
			  if($update_type == 'B'){
				  $update_content = "<font color=red>".$array_pdca_update_type[$update_type]."</font>"."[".$row_update_content['update_date']."]<br />".$row_update_content['update_content']."<br />".$array_pdca_update_type[$update_type].":".$row_update_content['delay_date'];
			  }elseif($update_type == 'C'){
				  $update_content = "<font color=green>".$array_pdca_update_type[$update_type]."</font>"."[".$row_update_content['update_date']."]<br />".$row_update_content['update_content'];
			  }elseif($update_type == 'A'){
				  $update_content = $array_pdca_update_type[$update_type]."[".$row_update_content['update_date']."]<br />".$row_update_content['update_content'];
			  }
			  $array_update_content[$row_update_content['workid']] = array('update_content'=>$update_content,'updateid'=>$row_update_content['updateid']);
		  }
	  }else{
		  $array_update_content = array();
	  }
	  //print_r($array_update_content);
	  //Group 最后更新内容附件
	  $sql_update_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'WKUP' AND `linkid` IN (SELECT SUBSTRING_INDEX(GROUP_CONCAT(`updateid` ORDER BY `updateid` DESC),',',1) FROM `db_work_update` WHERE `workid` IN ($array_workid) GROUP BY `workid`) GROUP BY `linkid`";
	  $result_update_file = $db->query($sql_update_file);
	  if($result_update_file->num_rows){
		  while($row_update_file = $result_update_file->fetch_assoc()){
			  $array_update_file[$row_update_file['linkid']] = $row_update_file['file_url'];
		  }
	  }else{
		  $array_update_file = array();
	  }
	  //print_r($array_update_file);
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th>发布人</th>
      <th>发布日期</th>
      <th width="25%">Plan/Action工作计划与行动计划</th>
      <th>期限时间</th>
      <th>完成日期</th>
      <th width="25%">Do阶段成果</th>
      <th>PDCA</th>
      <th>Result</th>
      <th width="4%">Update</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$workid = $row['workid'];
		//判断执行结果
		$pdca_status = $row['pdca_status'];
		$diff_finishdate = $row['diff_finishdate'];
		$diff_curdate = $row['diff_curdate'];
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
		$deadline_date = ($row['deadline_date'] == '0000-00-00')?'--':$row['deadline_date'];
		$finish_date = ($row['finish_date'] == '0000-00-00')?'--':$row['finish_date'];
		//工作附件
		$array_work_filelist = array_key_exists($workid,$array_work_file)?explode('#',$array_work_file[$workid]):'';
		$work_file_content = '';
		if(is_array($array_work_filelist)){
			$a = 1;
			foreach($array_work_filelist as $work_filelist){
				$work_file_content .= "<br />".$a.'.'.$work_filelist;
				$a++;
			}
		}
		//最后更新内容
		$update_content = array_key_exists($workid,$array_update_content)?$array_update_content[$workid]['update_content']:'--';
		$updateid = array_key_exists($workid,$array_update_content)?$array_update_content[$workid]['updateid']:'';
		//最后更新内容附件
		$array_update_filelist = array_key_exists($updateid,$array_update_file)?explode('#',$array_update_file[$updateid]):'';
		$update_file_content = '';
		if(is_array($array_update_filelist)){
			$b = 1;
			foreach($array_update_filelist as $update_filelist){
				$update_file_content .= "<br />".$b.'.'.$update_filelist;
				$b++;
			}
		}	
	?>
    <tr>
      <td><?php echo $workid; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['issue_date']; ?></td>
      <td style="text-align:left;"><?php echo $row['work_content'].$work_file_content; ?></td>
      <td><?php echo $deadline_date; ?></td>
      <td><?php echo $finish_date; ?></td>
      <td style="text-align:left;"><?php echo $update_content.$update_file_content; ?></td>
      <td><?php echo $pdca_status.'-'.$array_pdca_status[$pdca_status]; ?></td>
      <td><?php echo $pdca_result; ?></td>
      <td><?php if(in_array($pdca_status,array('P','D'))){ ?><a href="work_update.php?id=<?php echo $workid; ?>"><img src="../images/system_ico/update_10_10.png" width="10" height="10" /></a><?php } ?></td>
      <td><a href="work_info.php?id=<?php echo $workid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>