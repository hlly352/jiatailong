<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$client_name = trim($_GET['client_name']);
	$sqlwhere = " WHERE `client_cname` LIKE '%$client_name%' OR `client_ename` LIKE '%$client_name%'";
}
$sql = "SELECT * FROM `db_client` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `client_ename` ASC,`clientid` ASC" . $pages->limitsql;
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
  <h4>客户</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>客户：</th>
        <td><input type="text" name="client_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='clientae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_clientid .= $row_id['clientid'].',';
	  }
	  $array_clientid = rtrim($array_clientid,',');
	  $sql_mould = "SELECT `clientid` FROM `db_mould` WHERE `clientid` IN ($array_clientid) GROUP BY `clientid`";
	  $result_mould = $db->query($sql_mould);
	  if($result_mould->num_rows){
		  while($row_mould = $result_mould->fetch_assoc()){
			  $array_mould[] = $row_mould['clientid'];
		  }
	  }else{
		  $array_mould = array();
	  }
  ?>
  <form action="clientdo.php" name="client_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">代码</th>
        <th width="15%">中文名</th>
        <th width="15%">英文名</th>
        <th width="20%">全称</th>
        <th>地址</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $clientid = $row['clientid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $clientid; ?>"<?php if(in_array($clientid,$array_mould)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['client_code']; ?></td>
        <td><?php echo $row['client_cname']; ?></td>
        <td><?php echo $row['client_ename']; ?></td>
        <td><?php echo $row['client_name']; ?></td>
        <td><?php echo $row['client_address']; ?></td>
        <td><?php echo $array_status[$row['client_status']]; ?></td>
        <td width="4%"><a href="clientae.php?id=<?php echo $clientid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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