<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$type = trim($_GET['type']);
	$sqlwhere = " WHERE `type` LIKE '%$type%'";
}
$sql = "SELECT * FROM `db_cutter_type` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `typeid` DESC" . $pages->limitsql;
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
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>刀具类型</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><input type="text" name="type" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_typeae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_typeid .= $row_id['typeid'].',';
	  }
	  $array_typeid = rtrim($array_typeid,',');
	  $sql_group = "SELECT `typeid` FROM `db_cutter_specification` WHERE `typeid` IN ($array_typeid) GROUP BY `typeid`";
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
			  $array_group[] = $row_group['typeid'];
		  }
	  }else{
		  $array_group = array();
	  }
	  //print_r($array_group);
  ?>
  <form action="cutter_typedo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>类型</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $typeid = $row['typeid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $typeid; ?>"<?php if(in_array($typeid,$array_group)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['type']; ?></td>
        <td><a href="cutter_typeae.php?id=<?php echo $typeid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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