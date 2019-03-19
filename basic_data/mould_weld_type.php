<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$weld_typename = trim($_GET['weld_typename']);
	$sqlwhere = " WHERE `weld_typename` LIKE '%$weld_typename%'";
}
$sql = "SELECT * FROM `db_mould_weld_type` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `weld_typeid` DESC" . $pages->limitsql;
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
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具烧焊类型</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="weld_typename" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_weld_typeae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  $sql_mould_weld = "SELECT `weld_typeid` FROM `db_mould_weld` GROUP BY `weld_typeid`";
	  $result_mould_weld = $db->query($sql_mould_weld);
	  if($result_mould_weld->num_rows){
		  while($row_mould_weld = $result_mould_weld->fetch_assoc()){
			  $array_mould_weld[] = $row_mould_weld['weld_typeid'];
		  }
	  }else{
		  $array_mould_weld = array();
	  }
  ?>
  <form action="mould_weld_typedo.php" name="mould_weld_type_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>类型名称</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $weld_typeid = $row['weld_typeid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $weld_typeid; ?>"<?php if(in_array($weld_typeid,$array_mould_weld)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['weld_typename']; ?></td>
        <td><?php echo $array_status[$row['weld_typestatus']]; ?></td>
        <td width="4%"><a href="mould_weld_typeae.php?id=<?php echo $weld_typeid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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