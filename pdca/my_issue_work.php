<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	//查询执行结果
	$pdca_status = $_GET['pdca_status'];
	if($pdca_status){
		$sql_pdcastatus = " AND `db_work`.`pdca_status` = '$pdca_status'";
	}
	$pdca_result = $_GET['pdca_result'];
	if($pdca_result == 'A'){
		$sql_pdca_result = " AND `db_work`.`pdca_status` = 'P'";
	}elseif($pdca_result == 'B'){
		$sql_pdca_result = " AND `db_work`.`pdca_status` = 'D' AND (DATEDIFF(CURDATE(),`db_work`.`deadline_date`) <= 0)";
	}elseif($pdca_result == 'E'){
		$sql_pdca_result = " AND `db_work`.`pdca_status` = 'D' AND (DATEDIFF(CURDATE(),`db_work`.`deadline_date`) > 0)";
	}elseif($pdca_result == 'C'){
		$sql_pdca_result = " AND `db_work`.`pdca_status` = 'C' AND (DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) <= 0)";
	}elseif($pdca_result == 'D'){
		$sql_pdca_result = " AND `db_work`.`pdca_status` = 'C' AND (DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) > 0)";
	}
	$work_status = $_GET['work_status'];
	if($work_status != NULL){
		$sql_workstatus = " AND `db_work`.`work_status` = '$work_status'";
	}
	//查询最后更新类型
	$update_type = $_GET['update_type'];
	if($update_type){
		$sql_updatetype = "SELECT `workid` FROM `db_work_update` AS `db_work_update_A`,(SELECT MAX(`updateid`) as `updateid` FROM `db_work_update` GROUP BY `workid`) AS `db_work_update_B` WHERE `db_work_update_B`.`updateid` = `db_work_update_A`.`updateid` AND `db_work_update_A`.`update_type` = '$update_type'";
		$result_updatetype = $db->query($sql_updatetype);
		if($result_updatetype->num_rows){
			while($row_updatetyp = $result_updatetype->fetch_assoc()){
				$array_update_type_workid .= $row_updatetyp['workid'].',';
			}
		}
		$array_update_type_workid = rtrim($array_update_type_workid,',');
		$sql_update_type = " AND `db_work`.`workid` IN ($array_update_type_workid)";
	}
	$sqlwher = " AND `db_employee`.`employee_name` LIKE '%$employee_name%' $sql_pdcastatus $sql_pdca_result $sql_workstatus $sql_update_type";
}else{
	$work_status = 1;
	$sqlwhere = " AND `db_work`.`work_status` = '$work_status' AND `db_work`.`work_status` IN ('P','D')";
}
$sql = "SELECT `db_work`.`workid`,`db_work`.`work_content`,`db_work`.`issue_date`,`db_work`.`deadline_date`,`db_work`.`finish_date`,`db_work`.`pdca_status`,`db_work`.`work_status`,`db_employee`.`employee_name`,DATEDIFF(`db_work`.`finish_date`,`db_work`.`deadline_date`) AS `diff_finishdate`,DATEDIFF(CURDATE(),`db_work`.`deadline_date`) AS `diff_curdate` FROM `db_work` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work`.`worker` WHERE `db_work`.`issuer` = '$employeeid' $sqlwher";
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
  <h4>我的发布</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>责任人：</th>
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
        <th>执行状态：</th>
        <td><select name="pdca_result">
            <option value="">所有</option>
            <?php
			foreach($array_pdca_result as $pdca_result_key=>$pdca_result_value){
				echo "<option value=\"".$pdca_result_key."\">".$pdca_result_value."</option>";
			}
			?>
          </select></td>
        <th>任务状态：</th>
        <td><select name="work_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $work_status && $work_status != NULL) echo " selected=\"selected\"";?>><?php echo $status_value; ?></option>
			<?php } ?>
          </select></td>
        <th>反馈类型：</th>
        <td><select name="update_type">
            <option value="">所有</option>
            <?php
			foreach($array_pdca_update_type as $update_type_key=>$update_type_value){
				echo "<option value=\"".$update_type_key."\">".$update_type_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='workae.php?action=add'" /></td>
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
  <form action="workdo.php" name="work_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>发布日期</th>
        <th width="25%">Plan/Action工作计划与行动计划</th>
        <th>责任人</th>
        <th>期限时间</th>
        <th>完成日期</th>
        <th width="25%">Do阶段成果</th>
        <th>PDCA</th>
        <th>Result</th>
        <th>Status</th>
        <th width="4%">Edit</th>
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
        <td><input type="checkbox" name="id[]" value="<?php echo $workid; ?>" /></td>
        <td><?php echo $row['issue_date']; ?></td>
        <td style="text-align:left;"><?php echo $row['work_content'].$work_file_content; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $deadline_date; ?></td>
        <td><?php echo $finish_date; ?></td>
        <td style="text-align:left;"><?php echo $update_content.$update_file_content; ?></td>
        <td><?php echo $pdca_status.'-'.$array_pdca_status[$pdca_status]; ?></td>
        <td><?php echo $pdca_result; ?></td>
        <td><?php echo $array_status[$row['work_status']]; ?></td>
        <td><a href="workae.php?id=<?php echo $workid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
        <td><a href="work_info.php?id=<?php echo $workid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
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