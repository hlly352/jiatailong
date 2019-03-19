<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate ." +1 month -1 day"));
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$plan_type = $_GET['plan_type'];
	if($plan_type){
		$sql_plan_type = " AND `db_job_plan`.`plan_type` = '$plan_type'";
	}
	$plan_result = $_GET['plan_result'];
	if($plan_result != NULL){
		$sql_plan_result = " AND `db_job_plan`.`plan_result` = '$plan_result'";
	}
	$plan_status = $_GET['plan_status'];
	if($plan_status != NULL){
		$sql_plan_status = " AND `db_job_plan`.`plan_status` = '$plan_status'";
	}
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%' $sql_plan_type $sql_plan_result $sql_plan_status";
}else{
	$plan_type = $_GET['type'];
	$plan_status = 1;
	$sqlwhere = " AND `db_job_plan`.`plan_status` = '$plan_status' AND `db_job_plan`.`plan_type` = '$plan_type'";
}
$sql = "SELECT GROUP_CONCAT(`db_job_plan`.`planid`) AS `planid`,`db_job_plan`.`start_date`,`db_job_plan`.`employeeid`,`db_employee`.`employee_name` FROM `db_job_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_job_plan`.`employeeid` WHERE ((`db_job_plan`.`start_date` BETWEEN '$sdate' AND '$edate') OR (`db_job_plan`.`finish_date` BETWEEN '$sdate' AND '$edate')) $sqlwhere GROUP BY `db_job_plan`.`start_date`,`db_job_plan`.`employeeid`";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_job_plan`.`start_date` DESC,`db_job_plan`.`employeeid` ASC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>日计划</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>计划人：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="plan_type">
            <option value="">所有</option>
            <?php foreach($array_job_plan_type as $plan_type_key=>$plan_type_value){ ?>
            <option value="<?php echo $plan_type_key; ?>"<?php if($plan_type_key == $plan_type) echo " selected=\"selected\""; ?>><?php echo $plan_type_value; ?></option>
            <?php } ?>
          </select></td>
        <th>完成：</th>
        <td><select name="plan_result">
            <option value="">所有</option>
            <?php
			foreach($array_is_status as $is_status_key=>$is_status_value){
				echo "<option value=\"".$is_status_key."\">".$is_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="plan_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $plan_status) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='job_planae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $result_planid .= $row_id['planid'].',';
	  }
	  $result_planid = rtrim($result_planid,',');
	  //统计planid
	  $sql_group_planid = "SELECT `start_date`,`employeeid`,GROUP_CONCAT(`planid` ORDER BY `planid` ASC) as `planid` FROM `db_job_plan` WHERE `planid` IN ($result_planid) GROUP BY `start_date`,`employeeid`";
	  $result_group_planid = $db->query($sql_group_planid);
	  if($result_group_planid->num_rows){
		  while($row_group_planid = $result_group_planid->fetch_assoc()){
			  $array_group_planid[$row_group_planid['start_date'].'-'.$row_group_planid['employeeid']] = $row_group_planid['planid'];
		  }
	  }else{
		  $array_group_planid = array();
	  }
	  //print_r($array_group_planid);
	  //统计内容
	  $sql_group_content = "SELECT `planid`,`start_date`,`finish_date`,`plan_content`,`plan_result`,`plan_status`,DATEDIFF(`finish_date`,CURDATE()) AS `diff_date` FROM `db_job_plan` WHERE `planid` IN ($result_planid)";
	  $result_group_content = $db->query($sql_group_content);
	  if($result_group_content->num_rows){
		  while($row_group_content = $result_group_content->fetch_assoc()){
			  $array_group_content[$row_group_content['planid']] = array('start_date'=>$row_group_content['start_date'],'finish_date'=>$row_group_content['finish_date'],'plan_content'=>$row_group_content['plan_content'],'plan_result'=>$row_group_content['plan_result'],'plan_status'=>$row_group_content['plan_status'],'diff_date'=>$row_group_content['diff_date']);
		  }
	  }else{
		  $array_group_content = array();
	  }
	  //print_r($array_group_content);
	  //Group 工作附件
	  $sql_plan_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'JP' AND `linkid` IN ($result_planid) GROUP BY `linkid`";
	  $result_plan_file = $db->query($sql_plan_file);
	  if($result_plan_file->num_rows){
		  while($row_plan_file = $result_plan_file->fetch_assoc()){
			  $array_plan_file[$row_plan_file['linkid']] = $row_plan_file['file_url'];
		  }
	  }else{
		  $array_plan_file = array();
	  }
	  //Group 最后更新内容
	  $sql_updateid = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(`updateid` ORDER BY `updateid` DESC),',',1) AS `updateid` FROM `db_job_plan_update` WHERE `planid` IN ($result_planid) GROUP BY `planid`";
	  $result_updateid = $db->query($sql_updateid);
	  if($result_updateid->num_rows){
		  while($row_updateid = $result_updateid->fetch_assoc()){
			  $array_updateid .= $row_updateid['updateid'].',';
		  }
	  }
	  $array_updateid = rtrim($array_updateid,',');
	  $sql_update_content = "SELECT `updateid`,`update_date`,`update_content`,`planid` FROM `db_job_plan_update` WHERE `updateid` IN ($array_updateid)";
	  $result_update_content = $db->query($sql_update_content);
	  if($result_update_content->num_rows){
		  while($row_update_content = $result_update_content->fetch_assoc()){
			  $update_content = "[".$row_update_content['update_date']."]<br />".$row_update_content['update_content'];
			  $array_update_content[$row_update_content['planid']] = array('update_content'=>$update_content,'updateid'=>$row_update_content['updateid']);
		  }
	  }else{
		  $array_update_content = array();
	  }
	  //print_r($array_update_content);
	  //Group 最后更新内容附件
	  $sql_update_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'JPUP' AND `linkid` IN (SELECT SUBSTRING_INDEX(GROUP_CONCAT(`updateid` ORDER BY `updateid` DESC),',',1) FROM `db_job_plan_update` WHERE `planid` IN ($result_planid) GROUP BY `planid`) GROUP BY `linkid`";
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
      <th width="8%">计划人</th>
      <th width="28%">内容</th>
      <th width="8%">开始日期</th>
      <th width="8%">完成日期</th>
      <th width="28%">最新反馈</th>
      <th width="4%">完成</th>
      <th width="4%">状态</th>
      <th width="4%">Update</th>
      <th width="4%">Edit</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$plan_key = $row['start_date'].'-'.$row['employeeid'];
		$group_planid = $array_group_planid[$plan_key];
		$array_planid = explode(',',$group_planid);
		$colspan = count($array_planid);
	?>
    <tr>
      <td rowspan="<?php echo $colspan; ?>"><?php echo $row['employee_name']; ?></td>
      <?php
      for($i=1;$i<=$colspan;$i++){
		  $planid = $array_planid[$i-1];
		  $start_date = $array_group_content[$planid]['start_date'];
		  $finish_date = $array_group_content[$planid]['finish_date'];
		  $plan_content = $array_group_content[$planid]['plan_content'];
		  $plan_result = $array_group_content[$planid]['plan_result'];
		  $plan_status = $array_group_content[$planid]['plan_status'];
		  $diff_date = $array_group_content[$planid]['diff_date'];
		  $plan_content = ($diff_date < 0 && $plan_result == 0)?"<font color=red>".$plan_content."</font>":$plan_content;
		  //工作附件
		  $array_plan_filelist = array_key_exists($planid,$array_plan_file)?explode('#',$array_plan_file[$planid]):'';
		  $plan_file_content = '';
		  if(is_array($array_plan_filelist)){
			  $a = 1;
			  foreach($array_plan_filelist as $plan_filelist){
				  $plan_file_content .= "<br />".$a.'.'.$plan_filelist;
				  $a++;
			  }
		  }
		  //最后更新内容
		  $update_content = array_key_exists($planid,$array_update_content)?$array_update_content[$planid]['update_content']:'--';
		  $updateid = array_key_exists($planid,$array_update_content)?$array_update_content[$planid]['updateid']:'';
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
      <td style="text-align:left;"><?php echo $plan_content.$plan_file_content; ?></td>
      <td><?php echo $start_date; ?></td>
      <td><?php echo $finish_date; ?></td>
      <td style="text-align:left;"><?php echo $update_content.$update_file_content; ?></td>
      <td><?php echo $array_is_status[$plan_result]; ?></td>
      <td><?php echo $array_status[$plan_status]; ?></td>
      <td><?php if($row['employeeid'] == $employeeid){ ?>
        <a href="job_plan_update.php?id=<?php echo $planid; ?>"><img src="../images/system_ico/update_10_10.png" width="10" height="10" /></a>
        <?php } ?></td>
      <td><?php if($row['employeeid'] == $employeeid){ ?>
        <a href="job_planae.php?id=<?php echo $planid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
        <?php } ?></td>
      <td><a href="job_plan_info.php?id=<?php echo $planid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      <?php if($i != $colspan) echo "</tr><tr>"; ?>
      <?php } ?>
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