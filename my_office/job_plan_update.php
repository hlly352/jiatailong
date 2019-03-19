<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$planid = fun_check_int($_GET['id']);
$sql = "SELECT `plan_content`,`plan_status`,`plan_result`,`plan_type`,`start_date`,`finish_date` FROM `db_job_plan` WHERE `planid` = '$planid' AND `employeeid` = '$employeeid'";
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  //工作附件(workid+WK)
	  $sql_file = "SELECT `fileid`,`upfilename` FROM `db_upload_file` WHERE `linkcode` = 'JP' AND `linkid` = '$planid' ORDER BY `fileid` ASC";
	  $result_file = $db->query($sql_file);
	  if($result_file->num_rows){
		  $a = 1;
		  while($row_file = $result_file->fetch_assoc()){
			  $plan_file .= $a.".<a href=\"../upload/download_file.php?id=".$row_file['fileid']."\">".$row_file['upfilename']."</a><br />";
			  $a++;
		  }
	  }else{
		  $plan_file = '--';
	  }
  ?>
  <h4>计划内容</h4>
  <table>
    <tr>
      <th width="20%">内容：</th>
      <td width="80%"><?php echo $array['plan_content']; ?></td>
    </tr>
    <tr>
      <th>附件：</th>
      <td><?php echo $plan_file; ?></td>
    </tr>
    <tr>
      <th>类型：</th>
      <td><?php echo $array_job_plan_type[$array['plan_type']];?></td>
    </tr>
    <tr>
      <th>开始日期：</th>
      <td><?php echo $array['start_date']; ?></td>
    </tr>
    <tr>
      <th>结束日期：</th>
      <td><?php echo $array['finish_date']; ?></td>
    </tr>
    <tr>
      <th>完成：</th>
      <td><?php echo $array_is_status[$array['plan_result']]; ?></td>
    </tr>
    <tr>
      <th>状态：</th>
      <td><?php echo $array_status[$array['plan_status']]; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
$sql_update = "SELECT `updateid`,`update_date`,`update_content` FROM `db_job_plan_update` WHERE `planid` = '$planid'";
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
	  $sql_update_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'JPUP' AND `linkid` IN ($array_updateid) GROUP BY `linkid`";
	  $result_update_file = $db->query($sql_update_file);
	  if($result_update_file->num_rows){
		  while($row_update_file = $result_update_file->fetch_assoc()){
			  $array_update_file[$row_update_file['linkid']] = $row_update_file['file_url'];
		  }
	  }else{
		  $array_update_file = array();
	  }
  ?>
  <form action="job_plan_updatedo.php" name="job_plan_update_list" method="post">
    <table>
      <caption>
      计划进度反馈
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">反馈日期</th>
        <th width="41%">反馈内容</th>
        <th width="41%">附件</th>
        <th width="4%">Edit</th>
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
				  $update_file_content .= $a.'.'.$update_filelist."<br />";
				  $a++;
			  }
		  }
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $updateid; ?>" /></td>
        <td><?php echo $row_update['update_date']; ?></td>
        <td style="text-align:left"><?php echo $row_update['update_content']; ?></td>
        <td style="text-align:left"><?php echo $update_file_content; ?></td>
        <td><a href="job_plan_updateae.php?id=<?php echo $updateid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="button" name="button" value="添加" class="select_button" onclick="location.href='job_plan_updateae.php?id=<?php echo $planid; ?>&action=add'" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无进度反馈记录，请点击<a href='job_plan_updateae.php?id=".$planid."&action=add'>添加</a>！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>