<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $order_number = trim($_GET['order_number']);
  $mould_number = trim($_GET['mould_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_material_order`.`supplierid` = '$supplierid'";
  }
  $sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
}
$sql = "SELECT `db_other_material_inout`.`inout_quantity`,`db_other_material_specification`.`specificationid`,`db_other_material_specification`.`material_name`,`db_other_material_inout`.`inoutid` ,`db_other_material_specification`.`type`,`db_other_material_specification`.`materialid`,`db_other_material_inout`.`taker`,`db_other_material_inout`.`dodate`,`db_other_material_inout`.`remark`,`db_other_material_inout`.`form_number`,`db_other_material_specification`.`material_name`,`db_other_material_specification`.`specification_name` FROM `db_other_material_inout` INNER JOIN `db_other_material_specification` ON `db_other_material_inout`.`listid` = `db_other_material_specification`.`specificationid` WHERE `db_other_material_inout`.`dotype` = 'O'";

$result = $db->query($sql);
$_SESSION['material_inout_list_out'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_inout`.`inoutid` DESC" . $pages->limitsql;
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
  <h4>物料出库记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>表单号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <th>出库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
   
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_inout_out.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">物料名称</th>
      <th width="14%">规格</th>
      <th width="6%">数量</th>
      <th width="4%">单位</th>
      <th width="8%">领料人</th>
      <th width="8%">表单号</th>
      <th width="8%">出库日期</th>
      <th width="8%">备注</th>
      <!-- <th width="4%">Edit</th>
      <th width="4%">Info</th> -->
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $inoutid = $row['inoutid'];
    if($row['type'] == 'A'){
            $sql_info = "SELECT `material_name`,`unit` FROM `db_other_material_data` WHERE `dataid` = ".$row['materialid'];
          }elseif($row['type'] == 'B'){
            $sql_info = "SELECT `unit` FROM `db_mould_other_material` WHERE `mould_other_id` = ".$row['materialid'];
          }
          $result_info = $db->query($sql_info);
          if($result_info->num_rows){
            $info = $result_info->fetch_assoc();
          }
    ?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row['material_name']?$row['material_name']:$info['material_name']; ?></td>
      <td><?php echo $row['specification_name']; ?></td>
      <td><?php echo $row['inout_quantity']; ?></td>
      <td><?php echo $info['unit']; ?></td>
      <td><?php echo $row['taker']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><?php echo $row['remark']; ?></td>
     <!--  <td><a href="material_out_list_out.php?id=<?php echo $inoutid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="material_inout_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td> -->
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>