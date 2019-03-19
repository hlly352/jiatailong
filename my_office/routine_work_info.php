<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$workid = fun_check_int($_GET['id']);
$sql = "SELECT `db_routine_work`.`work_title`,`db_routine_work`.`work_title`,`db_routine_work`.`work_content`,`db_routine_work`.`work_type`,`db_routine_work`.`work_week`,`db_routine_work`.`work_month`,`db_routine_work`.`work_date`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` WHERE `db_routine_work`.`workid` = '$workid'";
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
	  $work_type = $array['work_type'];
	  if($work_type == 'A'){
		  $array_work_week = explode(',',$array['work_week']);
		  foreach($array_work_week as $work_week_value){
			  $work_week .= "星期".$array_week[$work_week_value].';';
		  }
	  }
  ?>
  <h4>例行工作反馈</h4>
  <table>
    <tr>
      <th width="20%"h>部门：</th>
      <td width="80%"><?php echo $array['dept_name']; ?></td>
    </tr>
    <tr>
      <th>标题：</th>
      <td><?php echo $array['work_title']; ?></td>
    </tr>
    <tr>
      <th>内容：</th>
      <td><?php echo $array['work_content']; ?></td>
    </tr>
    <tr>
      <th>类型：</th>
      <td><?php echo $array_routine_work_type[$work_type]; ?></td>
    </tr>
    <?php if($work_type == 'A'){ ?>
    <tr>
      <th>每周星期：</th>
      <td><?php echo $work_week; ?></td>
    </tr>
    <?php }elseif($work_type == "B"){ ?>
    <tr>
      <th>每月日期：</th>
      <td><?php echo $array['work_month']; ?></td>
    </tr>
    <?php }elseif($work_type == "C"){ ?>
    <tr>
      <th>固定日期：</th>
      <td><?php echo $array['work_date']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
$sql_update = "SELECT `db_routine_work_update`.`updateid`,`db_routine_work_update`.`update_content`,`db_routine_work_update`.`dotime`,`db_routine_work_update`.`employeeid`,`db_employee`.`employee_name` FROM `db_routine_work_update` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_routine_work_update`.`employeeid` WHERE`db_routine_work_update`.`workid` = '$workid' ORDER BY `db_routine_work_update`.`updateid` DESC LIMIT 0,20";
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
	  $sql_update_file = "SELECT `linkid`,GROUP_CONCAT(CONCAT('<a href=\"../upload/download_file.php?id=',`fileid`,'\">',`upfilename`,'</a>') ORDER BY `fileid` ASC SEPARATOR '#') AS `file_url` FROM `db_upload_file` WHERE `linkcode` = 'RWUP' AND `linkid` IN ($array_updateid) GROUP BY `linkid`";
	  $result_update_file = $db->query($sql_update_file);
	  if($result_update_file->num_rows){
		  while($row_update_file = $result_update_file->fetch_assoc()){
			  $array_update_file[$row_update_file['linkid']] = $row_update_file['file_url'];
		  }
	  }else{
		  $array_update_file = array();
	  }
  ?>
  <form action="routine_work_updatedo.php" name="routine_work_update_list" method="post">
    <table>
      <caption>
      例行工作反馈(Top20)
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="36">内容</th>
        <th width="36">附件</th>
        <th width="10%">反馈人</th>
        <th width="10%">操作时间</th>
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
        <td><input type="checkbox" name="id[]" value="<?php echo $updateid; ?>"<?php if($row_update['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td style="text-align:left"><?php echo $row_update['update_content']; ?></td>
        <td style="text-align:left"><?php echo $update_file_content; ?></td>
        <td><?php echo $row_update['employee_name']; ?></td>
        <td><?php echo $row_update['dotime']; ?></td>
        <td><?php if($row_update['employeeid'] == $employeeid){ ?><a href="routine_work_updateae.php?id=<?php echo $updateid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
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
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无例行工作反馈记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>