<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$workid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//工作内容(workid+employeeid+有效+PD)
$sql = "SELECT `db_work`.`work_content`,`db_work`.`issue_date`,`db_work`.`deadline_date`,`db_work`.`pdca_status`,`db_employee`.`employee_name`,DATEDIFF(`db_work`.`deadline_date`,`db_work`.`issue_date`) AS `diff_deadline_date`,DATEDIFF(CURDATE(),`db_work`.`issue_date`) AS `diff_issue_date` FROM `db_work` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work`.`issuer` WHERE `db_work`.`workid` = '$workid' AND `db_work`.`worker` = '$employeeid' AND `db_work`.`work_status` = 1 AND `db_work`.`pdca_status` IN ('P','D')";
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	//全选，反选，清除 
	$("#CheckedAll_other").click(function(){
		$('[name^=updateid]:checkbox').attr('checked',true);
		$('[id=submit_other]').attr('disabled',false);
	});
	$("#CheckedNo_other").click(function(){
		$('[name^=updateid]:checkbox').attr('checked',false);
		$('[id=submit_other]').attr('disabled',true);
	});
	$("#CheckedRev_other").click(function(){
		$('[name^=updateid]:checkbox').each(function(){
			this.checked=!this.checked;
		});
		flag=false;
		if(!$('[name^=updateid]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit_other]').attr('disabled',flag);
	});
	//checkbox id 选择
	$('[name^=updateid]:checkbox').click(function(){
		flag=false;
		if(!$('[name^=updateid]:checkbox').filter(':checked').length){
			flag=true;
		}
		$('[id=submit_other]').attr('disabled',flag);
	});
	$("#submit_accept").click(function(){
		var deadline_date = $("#deadline_date").val();
		if(deadline_date == '0000-00-00'){
			alert('请选择期限时间');
			return false;
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
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $pdca_status = $array['pdca_status'];
	  $diff_deadline_date = $array['diff_deadline_date'];
	  $diff_issue_date = $array['diff_issue_date'];
	  //工作附件(workid+WK)
	  $sql_file = "SELECT `fileid`,`upfilename` FROM `db_upload_file` WHERE `linkcode` = 'WK' AND `linkid` = '$workid' ORDER BY `fileid` ASC";
	  $result_file = $db->query($sql_file);
	  if($result_file->num_rows){
		  $a = 1;
		  while($row_file = $result_file->fetch_assoc()){
			  $work_file .= $a.".<a href=\"../upload/download_file.php?id=".$row_file['fileid']."\">".$row_file['upfilename']."</a><br />";
			  $a++;
		  }
	  }else{
		  $work_file = '--';
	  }
  ?>
  <h4>工作更新</h4>
  <form action="work_acceptdo.php" name="work_accept" method="post">
    <table>
      <tr>
        <th width="20%">发布人：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
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
        <th>期限时间：</th>
        <td><input type="text" name="deadline_date" id="deadline_date" value="<?php echo $array['deadline_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>PDCA状态：</th>
        <td><?php echo $pdca_status.'-'.$array_pdca_status[$pdca_status]; ?></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit_accept" value="确定" class="button"<?php if($diff_issue_date > 2 || $array['pdca_status'] == 'C') echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="workid" value="<?php echo $workid; ?>" />
          <?php if($diff_issue_date > 2){ ?>
          <span class="tag">*因超出发布时间2天，没有确认期限时间，请与发起人联系确认</span>
          <?php } ?></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  die("<p>系统提示：暂无记录！</p></div>");
	  }
  ?>
</div>
<?php
if($pdca_status != 'P'){
//工作计划(workid)
$sql_plan = "SELECT `db_work_plan`.`planid`,`db_work_plan`.`plan_content`,`db_work_plan`.`end_date`,`db_work_plan`.`employee`,`db_work_plan`.`employeeid`,`db_work_plan`.`dotime`,`db_employee`.`employee_name` FROM `db_work_plan` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_work_plan`.`employeeid` WHERE `db_work_plan`.`workid` = '$workid' ORDER BY `db_work_plan`.`end_date` ASC,`db_work_plan`.`planid` ASC";
$result_plan = $db->query($sql_plan);
$result_plan_count = $db->query($sql_plan);
?>
<div id="table_list">
  <?php if($result_plan->num_rows){ ?>
  <form action="work_plando.php" name="plan_list" method="post">
    <table>
      <caption>
      工作计划
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th>内容</th>
        <th width="10%">截止时间</th>
        <th width="10%">责任人</th>
        <th width="10%">操作人</th>
        <th width="10%">时间</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row_plan = $result_plan->fetch_assoc()){
		  $planid = $row_plan['planid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $planid; ?>"<?php if($employeeid != $row_plan['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td style="text-align:left;"><?php echo $row_plan['plan_content']; ?></td>
        <td><?php echo $row_plan['end_date']; ?></td>
        <td><?php echo $row_plan['employee']; ?></td>
        <td><?php echo $row_plan['employee_name']; ?></td>
        <td><?php echo $row_plan['dotime']; ?></td>
        <td><?php if($employeeid == $row_plan['employeeid']){ ?>
          <a href="work_planae.php?id=<?php echo $planid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="button" name="button" value="添加" class="select_button" onclick="location.href='work_planae.php?id=<?php echo $workid; ?>&action=add'" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">暂无工作计划，请点击<a href=\"work_planae.php?id=".$workid."&action=add\">添加计划</a></p>";
  }
  ?>
</div>
<?php
if($diff_deadline_date >= 30 && $result_plan_count->num_rows <= 0){
	die("<div id=\"table_list\"><p class=\"tag\">该工作期限时间距发布时间间隔天数大于30天，请先填写<a href=\"work_planae.php?id=".$workid."&action=add\">工作计划</a></p></div>");
}
?>
<?php
//工作更新(workid)
$sql_update = "SELECT `updateid`,`update_type`,`update_content`,`employee`,`employeeid`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `update_date`,TIMESTAMPDIFF(HOUR,`dotime`,NOW()) AS `diff_hour` FROM `db_work_update` WHERE `workid` = '$workid' ORDER BY `dotime` DESC";
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
  <form action="work_updatedo.php" name="update_list" method="post">
    <table>
      <caption>
      工作反馈<br />
      <font color="#FF9900">(如超过操作时间48小时后无法修改删除)</font>
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">反馈时间</th>
        <th width="8%">类型</th>
        <th width="34%">内容</th>
        <th width="8%">负责人</th>
        <th width="34%">最新批示</th>
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
        <td><input type="checkbox" name="updateid[]" value="<?php echo $updateid; ?>"<?php if($row_update['diff_hour'] >= 48 || $employeeid != $row_update['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_update['update_date']; ?></td>
        <td><?php echo $array_pdca_update_type[$row_update['update_type']]; ?></td>
        <td style="text-align:left;"><?php echo $row_update['update_content'].$update_file_content; ?></td>
        <td><?php echo $row_update['employee']; ?></td>
        <td style="text-align:left;"><?php echo $reply_content.$reply_file_content; ?></td>
        <td><?php if($row_update['diff_hour'] < 48 && $employeeid == $row_update['employeeid']){ ?>
          <a href="work_updateae.php?id=<?php echo $updateid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll_other" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev_other" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo_other" value="清除" />
      <input type="submit" name="submit" id="submit_other" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="button" name="button" value="添加" class="select_button" onclick="location.href='work_updateae.php?id=<?php echo $workid; ?>&action=add'" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">暂无工作反馈，请点击<a href=\"work_updateae.php?id=".$workid."&action=add\">添加反馈</a></p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>