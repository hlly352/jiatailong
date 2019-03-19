<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$inc_name = trim($_GET['inc_name']);
	$sqlwhere = " WHERE `inc_cname` LIKE '%$inc_name%' OR `inc_ename` LIKE '%$inc_name%'";
}
$sql = "SELECT * FROM `db_express_inc` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `incid` DESC" . $pages->limitsql;
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
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>快递公司</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递公司：</th>
        <td><input type="text" name="inc_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='express_incae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_incid .= $row_id['incid'].',';
	  }
	  $array_incid = rtrim($array_incid,',');
	  $sql_express = "SELECT `express_incid` FROM `db_employee_express` WHERE `express_incid` IN ($array_incid) GROUP BY `express_incid`";
	  $result_express = $db->query($sql_express);
	  if($result_express->num_rows){
		  while($row_express = $result_express->fetch_assoc()){
			  $array_express[] = $row_express['express_incid'];
		  }
	  }else{
		  $array_express = array();
	  }
	  //print_r($array_express);
	  $sql_express_receive = "SELECT `express_incid` FROM `db_employee_express_receive` WHERE `express_incid` IN ($array_incid) GROUP BY `express_incid`";
	  $result_express_receive = $db->query($sql_express_receive);
	  if($result_express_receive->num_rows){
		  while($row_express_receive = $result_express_receive->fetch_assoc()){
			  $array_express_receive[] = $row_express_receive['express_incid'];
		  }
	  }else{
		  $array_express_receive = array();
	  }
	  //print_r($array_express_receive);
	  $array_express_inc = array_unique(array_merge($array_express,$array_express_receive));
  ?>
  <form action="express_incdo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>快递公司</th>
        <th>英文名</th>
        <th>联系人</th>
        <th>联系电话</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $incid = $row['incid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $incid; ?>"<?php if(in_array($incid,$array_express_inc)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['inc_cname']; ?></td>
        <td><?php echo $row['inc_ename']; ?></td>
        <td><?php echo $row['inc_contact']; ?></td>
        <td><?php echo $row['inc_phone']; ?></td>
        <td><?php echo $array_status[$row['inc_status']]; ?></td>
        <td><a href="express_incae.php?id=<?php echo $incid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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