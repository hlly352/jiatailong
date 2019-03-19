<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
require_once '../class/page.php';
$array_shell = $_SESSION['system_shell'];
if($_GET['submit']){
	$work_title = trim($_GET['work_title']);
	$work_type = $_GET['work_type'];
	if($work_type){
		$sql_work_type = " AND `db_routine_work`.`work_type` = '$work_type'";
	}
	$sqlwhere = " WHERE `db_routine_work`.`work_title` LIKE '%$work_title%' $sql_work_type";
}
$sql = "SELECT `db_routine_work`.`workid`,`db_routine_work`.`work_title`,`db_routine_work`.`work_type`,`db_routine_work`.`work_week`,`db_routine_work`.`work_month`,`db_routine_work`.`work_date`,`db_routine_work`.`work_status`,`db_department`.`dept_name` FROM `db_routine_work` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_routine_work`.`deptid` $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_routine_work`.`workid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<div id="table_search">
  <h4>例行工作</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>标题：</th>
        <td><input type="text" name="work_title" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="work_type">
            <option value="">所有</option>
            <?php
            foreach($array_routine_work_type as $work_type_key=>$work_type_value){
				echo "<option value=\"".$work_type_key."\">".$work_type_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='routine_workae.php?action=add'"<?php if(!$array_shell[$system_dir]['isadmin']) echo " disabled=\"disabled\""; ?> /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="routine_workdo.php" name="routine_work_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">部门</th>
        <th width="32%">标题</th>
        <th width="10%">类型</th>
        <th width="15%">每周星期</th>
        <th width="7%">每月日期</th>
        <th width="10%">固定日期</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $workid = $row['workid'];
		  $work_type = $row['work_type'];
		  if($work_type == 'A'){
			  $work_week = '';
			  $array_work_week = explode(',',$row['work_week']);
			  foreach($array_work_week as $work_week_value){
				  $work_week .= "星期".$array_week[$work_week_value].';';
			  }
		  }else{
			  $work_week = '--';
		  }
		  $work_month = ($work_type == 'B')?$row['work_month']:'--';
		  $work_date = ($work_type == 'C')?$row['work_date']:'--';
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $workid; ?>"<?php if(!$array_shell[$system_dir]['isadmin']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['dept_name']; ?></td>
        <td><?php echo $row['work_title']; ?></td>
        <td><?php echo $array_routine_work_type[$work_type]; ?></td>
        <td><?php echo $work_week; ?></td>
        <td><?php echo $work_month; ?></td>
        <td><?php echo $work_date; ?></td>
        <td><?php echo $array_status[$row['work_status']]; ?></td>
        <td><?php if($array_shell[$system_dir]['isadmin']){ ?><a href="routine_workae.php?id=<?php echo $workid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><a href="routine_work_info.php?id=<?php echo $workid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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
	  echo "<p class=\"tag\">系统提示：暂无数据！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>