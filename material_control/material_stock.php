<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`material_number` LIKE '%$material_number%'";
}

$sql = "SELECT * FROM `db_mould_material` INNER JOIN `db_material_order_list` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould_material`.`mouldid` = `db_mould`.`mouldid` INNER JOIN `db_material_order` ON `db_material_order_list`.`orderid` = `db_material_order`.`orderid` WHERE (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') AND `db_material_order_list`.`order_surplus`>0 $sqlwhere GROUP BY `db_mould_material`.`materialid`";

$result = $db->query($sql);
$_SESSION['cutter_inout_list_out'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_order_list`.`plan_date` DESC" . $pages->limitsql;

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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script> 
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具物料库存管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>物料编码：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>下单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_inout_out.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th widht="">模具编号</th>
      <th width="">物料名称</th>
      <th width="">物料编号</th>
      <th width="">规格</th>
      <th width="">硬度</th>
      <th width="">库存数量</th>
      <th width="">下单时间</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
     
    	?>
    <tr>
      <td class="cutterid"><?php echo $row['materialid'] ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_number']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['hardness'] ?></td>
      <td><?php echo $row['order_surplus'] ?></td>
      <td><?php echo $row['order_date']; ?></td>
    </tr>
    <?php } ?>
  </table>
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