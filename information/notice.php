<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//读取类型
$sql_notice_type = "SELECT `notice_typeid`,`notice_typename` FROM `db_notice_type`";
$result_notice_type = $db->query($sql_notice_type);
if($_GET['submit']){
	$notice_title = trim($_GET['notice_title']);
	$notice_typeid = $_GET['notice_typeid'];
	if($notice_typeid){
		$sql_notice_typeid = " AND `db_notice`.`notice_typeid` = '$notice_typeid'";
	}
	$notice_status = $_GET['notice_status'];
	if($notice_status != NULL){
		$sql_notice_status = " AND `db_notice`.`notice_status` = '$notice_status'";
	}
	$sqlwhere = " WHERE `db_notice`.`notice_title` LIKE '%$notice_title%' $sql_notice_typeid $sql_notice_status";
}
$sql = "SELECT `db_notice`.`noticeid`,`db_notice`.`notice_title`,`db_notice`.`notice_status`,`db_notice`.`dotime`,`db_employee`.`employee_name`,`db_notice_type`.`notice_typename` FROM `db_notice` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_notice`.`employeeid` INNER JOIN `db_notice_type` ON `db_notice_type`.`notice_typeid` = `db_notice`.`notice_typeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_notice`.`noticeid` DESC" . $pages->limitsql;
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
<title>信息发布-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>通知公告</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>标题：</th>
        <td><input type="text" name="notice_title" class="input_txt" />
        <th>类型：</th>
        <td><select name="notice_typeid">
            <option value="">所有</option>
            <?php
			if($result_notice_type->num_rows){
				while($row_notice_type = $result_notice_type->fetch_assoc()){
					echo "<option value=\"".$row_notice_type['notice_typeid']."\">".$row_notice_type['notice_typename']."</option>";
				}
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="notice_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="发布" class="button" onclick="location.href='noticeae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="noticedo.php" name="notice_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>标题</th>
        <th width="8%">类型</th>
        <th width="10%">发布人</th>
        <th width="12%">发布时间</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
	  while($row = $result->fetch_assoc()){
		  $noticeid = $row['noticeid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $noticeid; ?>" /></td>
        <td><?php echo $row['notice_title']; ?></td>
        <td><?php echo $row['notice_typename']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $array_status[$row['notice_status']]; ?></td>
        <td><a href="noticeae.php?id=<?php echo $noticeid; ?>&amp;action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
        <td><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></td>
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