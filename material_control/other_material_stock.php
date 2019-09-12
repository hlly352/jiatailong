<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_type = "SELECT `material_typeid`,`material_typename` FROM `db_other_material_type` ORDER BY `material_typeid` ASC";
$result_type = $db->query($sql_type);
if($_GET['submit']){
	$material_name = rtrim($_GET['material_name']);
	$specification = rtrim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_other_material_data`.`material_typeid` = '$typeid'";
	}
	$sqlwhere = " AND `db_other_material_data`.`material_name` LIKE '%$material_name%' $sql_typeid";
}
// $sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`listid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`employeeid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_mould`.`mould_number`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE (`db_cutter_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND `db_cutter_inout`.`dotype` = 'O' $sqlwhere";
$sql = "SELECT * FROM `db_other_material_data` INNER JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid`  WHERE `db_other_material_data`.`stock` > 0 $sqlwhere";

$result = $db->query($sql);
$_SESSION['cutter_inout_list_out'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_data`.`dataid` DESC" . $pages->limitsql;

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
  <h4>期间物料库存管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid" class="input_txt txt">
            <option value="">所有</option>
            <?php
			if($result_type->num_rows){
				while($row_type = $result_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_type['material_typeid']; ?>"<?php if($row_type['material_typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_type['material_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
<!--         <th>出库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td> -->
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
      <th widht="">物料类型</th>
      <th width="">物料名称</th>
      <th width="">规格</th>
      <th width="">标准库存</th>
      <th width="">库存数量</th>
      <th width="">下单时间</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
      //查询最后下单时间
      $date_sql = "SELECT `db_other_material_order`.`order_date` FROM `db_other_material_data` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` INNER JOIN `db_other_material_orderlist` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` WHERE `db_other_material_data`.`dataid` =".$row['dataid'];

      $result_date = $db->query($date_sql);
      if($result_date->num_rows){
        $date = $result_date->fetch_row()[0];
      }
     ?>
    <tr>
      <td class="cutterid"><?php echo $row['dataid'] ?></td>
      <td><?php echo $row['material_typename']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['']; ?></td>
      <td><?php echo $row['standard_stock']?></td>
      <td><?php echo $row['stock'] ?></td>
      <td><?php echo date('Y-m-d',strtotime($date)); ?></td>

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