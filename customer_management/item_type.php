<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$item_typename = trim($_GET['item_typename']);
	$sqlwhere = " WHERE `item_typename` LIKE '%$item_typename%'";
}
$sql = "SELECT * FROM `db_quote_item_type` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `item_type_sn` ASC,`item_typeid` ASC" . $pages->limitsql;
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
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>项目类型</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><input type="text" name="item_typename" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='item_typeae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		 $array_item_typeid .= $row_id['item_typeid'].',';
	  }
	  $array_item_typeid = rtrim($array_item_typeid,',');
	  $sql_item = "SELECT `item_typeid` FROM `db_quote_item` WHERE `item_typeid` IN ($array_item_typeid) GROUP BY `item_typeid`";
	  $result_item = $db->query($sql_item);
	  if($result_item->num_rows){
		  while($row_item = $result_item->fetch_assoc()){
			  $array_item[] = $row_item['item_typeid'];
		  }
	  }else{
		  $array_item = array();
	  }
  ?>
  <form action="item_typedo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">序号</th>
        <th width="86%">类型</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $item_typeid = $row['item_typeid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $item_typeid; ?>"<?php if(in_array($item_typeid,$array_item)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['item_type_sn']; ?></td>
        <td><?php echo $row['item_typename']; ?></td>
        <td><a href="item_typeae.php?id=<?php echo $item_typeid ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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