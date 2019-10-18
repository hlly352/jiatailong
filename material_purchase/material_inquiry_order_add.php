<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$orderid = $_GET['id'];
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT * FROM `db_material_inquiry_order` INNER JOIN `db_employee` ON `db_material_inquiry_order`.`employeeid` = `db_employee`.`employeeid`";
$result = $db->query($sql);
$_SESSION['material_inquiry_list'] = $sql;
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
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>物料询价单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料编号：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_inquiry.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ 
    //查找项数
    $sql_inquiry_count = "SELECT `db_material_inquiry_order`.`inquiry_orderid`,count(*) AS `count` FROM `db_material_inquiry_orderlist` INNER JOIN `db_material_inquiry_order` ON `db_material_inquiry_order`.`inquiry_orderid` = `db_material_inquiry_orderlist`.`inquiry_orderid` WHERE `db_material_inquiry_orderlist`.`materialid` NOT IN(SELECT `materialid` FROM `db_material_order_list` GROUP BY `materialid`) GROUP BY `db_material_inquiry_orderlist`.`inquiry_orderid`";
    $result_count = $db->query($sql_inquiry_count);
    if($result_count->num_rows){
      $array_count = array();
      while($row_count = $result_count->fetch_assoc()){
        $array_count[$row_count['inquiry_orderid']] = $row_count['count'];
      }
    }else{
      $array_count = array();
    }
  ?>
  <form action="material_order_list_add.php" name="material_inquiry_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">询价单号</th>
        <th width="6%">询价日期</th>
        <th width="6%">操作人</th>
        <th width="4%">项数</th>
        <th width="10%">详情</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $inquiry_orderid = $row['inquiry_orderid'];
      if($array_count[$inquiry_orderid]){
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $inquiry_orderid; ?>"/></td>
        <td><?php echo $row['inquiry_number']; ?></td>  
        <td><?php echo $row['inquiry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $array_count[$row['inquiry_orderid']]?$array_count[$row['inquiry_orderid']]:0; ?></td>
        <td>
          <a href="material_inquiry_list.php?id=<?php echo $inquiry_orderid; ?>">
            <img src="../images/system_ico/info_8_10.png">
          </a>
        </td>
      </tr>
      <?php }} ?>
    </table>

    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="下单" class="select_button" />
      <input type="hidden" value="<?php echo $orderid; ?>" name="orderid" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>