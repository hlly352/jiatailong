<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
//require_once '../class/page.php';
require_once 'shell.php';
$sql_type = "SELECT * FROM `db_quote_item_type` ORDER BY `item_typeid` ASC";
$result_type = $db->query($sql_type);
if($_GET['submit']){
	$item_name = trim($_GET['item_name']);
	$item_typeid = $_GET['item_typeid'];
	if($item_typeid){
		$sql_item_type = " AND `db_quote_item`.`item_typeid` = '$item_typeid'";
	}
	$sqlwhere = " WHERE `db_quote_item`.`item_name` LIKE '%$item_name%' $sql_item_type";
}
$sql = "SELECT `db_quote_item`.`itemid`,`db_quote_item`.`item_sn`,`db_quote_item`.`item_name`,`db_quote_item`.`specification`,`db_quote_item`.`unit_price`,`db_quote_item`.`descripition`,`db_quote_item_type`.`item_type_sn`,`db_quote_item_type`.`item_typename` FROM `db_quote_item` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` $sqlwhere ORDER BY `db_quote_item_type`.`item_typeid` ASC,`db_quote_item`.`itemid` ASC";
$result = $db->query($sql);
$result_id = $db->query($sql);
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
  <h4>项目名称</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>名称：</th>
        <td><input type="text" name="item_name" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="item_typeid">
            <option value="">所有</option>
            <?php
			if($result_type->num_rows){
				while($row_type = $result_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_type['item_typeid']; ?>"<?php if($row_type['item_typeid'] == $item_typeid) echo " selected=\"selected\""; ?>><?php echo $row_type['item_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='quote_itemae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		 $array_itemid .= $row_id['itemid'].',';
	  }
	  $array_itemid = rtrim($array_itemid,',');
	  $sql_item = "SELECT `itemid` FROM `db_mould_quote_list` WHERE `itemid` IN ($array_itemid) GROUP BY `itemid`";
	  $result_item = $db->query($sql_item);
	  if($result_item->num_rows){
		  while($row_item = $result_item->fetch_assoc()){
			  $array_item[] = $row_item['itemid'];
		  }
	  }else{
		  $array_item = array();
	  }
  ?>
  <form action="quote_itemdo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">序号</th>
        <th width="20%">类型</th>
        <th width="20%">名称</th>
        <th width="14%">规格型号/牌号</th>
        <th width="8%">单价</th>
        <th width="22%">备注/说明</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $itemid = $row['itemid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $itemid; ?>"<?php if(in_array($itemid,$array_item)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['item_type_sn'].'-'.$row['item_sn']; ?></td>
        <td><?php echo $row['item_typename'] ?></td>
        <td><?php echo $row['item_name'] ?></td>
        <td><?php echo $row['specification'] ?></td>
        <td><?php echo $row['unit_price'] ?></td>
        <td><?php echo $row['descripition'] ?></td>
        <td><a href="quote_itemae.php?id=<?php echo $itemid ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>