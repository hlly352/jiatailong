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
$sql = "SELECT `db_material_order_list`.`listid`,(`db_material_order_list`.`order_quantity`-`db_material_order_list`.`in_quantity`) AS `quantity`,`db_material_order_list`.`plan_date`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,DATEDIFF(`db_material_order_list`.`plan_date`,CURDATE()) AS `diff_date` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` WHERE `db_material_order`.`order_status` = 1 AND (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') AND (`db_material_order_list`.`order_quantity`-`db_material_order_list`.`in_quantity`) > 0 $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_order_list`.`plan_date` ASC,`db_material_order_list`.`listid` ASC" . $pages->limitsql;
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
  <h4>待入库模具物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>合同号：</td>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <td>模具编号：</td>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <td>物料名称：</td>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <td>规格：</td>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <td>订单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <td>供应商：</td>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="material_batch_in.php" name="material_order_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</td>
        <th width="8%">合同号</td>
        <th width="8%">模具编号</td>
        <th width="12%">物料名称</td>
        <th width="20%">规格</td>
        <th width="8%">材质</td>
        <th width="6%">数量</td>
        <th width="6%">单位</td>
        <th width="8%">供应商</td>
        <th width="8%">订单日期</td>
        <th width="8%">计划回厂时间</td>
        <th width="4%">In</td>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $listid = $row['listid'];
		  $diff_date = $row['diff_date'];
		  if($diff_date <=1){
			  $tr_bg = " style=\"background:#F00;\"";
		  }elseif($diff_date == 2 || $diff_date == 3){
			  $tr_bg = " style=\"background:#FF0;\"";
		  }else{
			  $tr_bg = "";
		  }
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $listid; ?>" /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['unit_name']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td<?php echo $tr_bg; ?>><?php echo $row['plan_date']; ?></td>
        <td><a href="material_in_list_in.php?id=<?php echo $listid; ?>&amp;action=add"><img src="../images/system_ico/in_10_8.png" width="10" height="8" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="入库" class="select_button" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<!--模具入料入库单-->
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
  $entry_number = trim($_GET['entry_number']);
  $sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'M' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
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
<div id="table_search">
  <h4>模具物料入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>入库单号：</td>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <td>入库单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
    while($row_id = $result_id->Fetch_assoc()){
      $array_entryid .= $row_id['entryid'].','; 
    }
    $array_entryid = trim($array_entryid,',');
    $sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'M' GROUP BY `db_godown_entry_list`.`entryid`";
    $result_entry = $db->query($sql_entry);
    if($result_entry->num_rows){
      while($row_entry =$result_entry->fetch_assoc()){
        $array_entry[$row_entry['entryid']] = $row_entry['count'];
      }
    }else{
      $array_entry = array();
    }
    ?>
    <table>
      <tr>
        <th width="4%">ID</td>
        <th width="20%">入库单号</td>
        <th width="20%">入库单日期</td>
        <th width="20%">制单人</td>
        <th width="20%">时间</td>
        <th width="4%">项数</td>
        <th width="4%">Add</td>
        <th width="4%">List</td>
        <th width="4%">Print</td>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $entryid = $row['entryid'];
      $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="material_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<!--刀具入库-->
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $order_number = rtrim($_GET['order_number']);
  $specification = rtrim($_GET['specification']);
  $typeid = $_GET['typeid'];
  if($typeid){
    $sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
  }
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_cutter_order`.`supplierid` = '$supplierid'";
  }
  $sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid $sql_supplierid";
}
$sql = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`plan_date`,(`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) AS `quantity`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,DATEDIFF(`db_cutter_order_list`.`plan_date`,CURDATE()) AS `diff_date` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE `db_cutter_order`.`order_status` = 1 AND (`db_cutter_order`.`order_date` BETWEEN '$sdate' AND '$edate') AND (`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) > 0 $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_order`.`order_date` DESC,`db_cutter_order_list`.`listid` DESC" . $pages->limitsql;
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
<div id="table_search">
  <h4>待入库加工刀具</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>合同号：</td>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <td>规格：</td>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td>类型：</td>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
      if($result_cutter_type->num_rows){
        while($row_cutter_type = $result_cutter_type->fetch_assoc()){
      ?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
        }
      }
      ?>
          </select></td>
        <td>订单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <td>供应商：</td>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
      ?>
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $supplierid) echo " selected=\"selected\"" ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
        }
      }
      ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</td>
      <th width="10%">合同号</td>
      <th width="8%">类型</td>
      <th width="14%">规格</td>
      <th width="8%">材质</td>
      <th width="8%">硬度</td>
      <th width="8%">品牌</td>
      <th width="6">数量</td>
      <th width="4%">单位</td>
      <th width="10%">供应商</td>
      <th width="8%">订单日期</td>
      <th width="8%">回厂计划日期</td>
      <th width="4%">In</td>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
    $listid = $row['listid'];
    $diff_date = $row['diff_date'];
    if($diff_date <=1){
      $tr_bg = " style=\"background:#F00;\"";
    }elseif($diff_date == 2 || $diff_date == 3){
      $tr_bg = " style=\"background:#FF0;\"";
    }else{
      $tr_bg = "";
    }
  ?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['brand']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td<?php echo $tr_bg; ?>><?php echo $row['plan_date']; ?></td>
      <td><a href="cutter_in_list_in.php?id=<?php echo $listid; ?>&action=add"><img src="../images/system_ico/in_10_8.png" width="10" height="8" /></a></td>
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
</body>
</html>
<!--刀具入库单-->
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
  $entry_number = trim($_GET['entry_number']);
  $sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'C' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
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
<div id="table_search">
  <h4>加工刀具入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>入库单号：</td>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <td>入库单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
    while($row_id = $result_id->Fetch_assoc()){
      $array_entryid .= $row_id['entryid'].','; 
    }
    $array_entryid = trim($array_entryid,',');
    $sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'C' GROUP BY `db_godown_entry_list`.`entryid`";
    $result_entry = $db->query($sql_entry);
    if($result_entry->num_rows){
      while($row_entry =$result_entry->fetch_assoc()){
        $array_entry[$row_entry['entryid']] = $row_entry['count'];
      }
    }else{
      $array_entry = array();
    }
    //print_r($array_entry);
    ?>
    <table>
      <tr>
        <th width="4%">ID</td>
        <th width="20%">入库单号</td>
        <th width="20%">入库单日期</td>
        <th width="20%">制单人</td>
        <th width="20%">时间</td>
        <th width="4%">项数</td>
        <th width="4%">Add</td>
        <th width="4%">List</td>
        <th width="4%">Print</td>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $entryid = $row['entryid'];
      $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="cutter_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<!--期间入料入库-->
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
  $iscash = $_GET['iscash'];
  if($iscash != NULL){
    $sql_iscash = " AND `db_material_order_list`.`iscash` = '$iscash'";
  }
  $sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_iscash";
}
//$sql = "SELECT `db_other_material_orderlist`.`listid`,`db_other_material_orderlist`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,`db_material_order_list`.`remark`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` INNER JOIN `db_department` ON `db_mould_other_material`.`apply_team` = `db_department`.`deptid` INNER JOIN `db_other_supplier` ON `db_other_supplier`.`other_supplier_id` = `db_other_material_order`.`supplierid` WHERE `db_mould_other_material`.`status` = 'E'";
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_orderlist'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_order`.`order_date` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
</head>

<body>
<div id="table_search">
  <h4>待入库期间物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>合同号：</td>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <td>物料名称：</td>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <td>规格：</td>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <td>订单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
        <td>供应商：</td>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
        <td>现金：</td>
        <td><select name="iscash">
            <option value="">所有</option>
            <?php foreach($array_is_status as $is_status_key=>$is_statua_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $iscash && $iscash != NULL) echo " selected=\"selected\""; ?>><?php echo $is_statua_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_orderlist.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_id = $result_id->fetch_assoc()){
      $array_listid .= $row_id['listid'].',';
    }
    $array_listid = rtrim($array_listid,',');
    //支付金额
    $sql_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_listid) AND `data_type` = 'M' GROUP BY `linkid`";
    $result_pay_amount = $db->query($sql_pay_amount);
    if($result_pay_amount->num_rows){
      while($row_pay_amount = $result_pay_amount->fetch_assoc()){
        $array_pay_amount[$row_pay_amount['linkid']] = $row_pay_amount['total_pay_amount'];
      }
    }else{
      $array_pay_amount = array();
    }
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];
      $total_process_cost += $row_total['process_cost'];
    }
    //入库数量
    $sql_material_in = "SELECT SUM(`inout_quantity`) AS `in_quantity`,SUM(`quantity`) AS `quantity`,`listid` FROM `db_material_inout` WHERE `db_material_inout`.`dotype` = 'I' AND `listid` IN ($array_listid) GROUP BY `listid`";
    $result_material_in = $db->query($sql_material_in);
    if($result_material_in->num_rows){
      while($row_material_in = $result_material_in->fetch_assoc()){
        $array_material_in[$row_material_in['listid']] = array('in_quantity'=>$row_material_in['in_quantity'],'quantity'=>$row_material_in['quantity']);
      }
    }else{
      $array_material_in = array();
    }
  ?>
  <table>
    <tr>
      <td>ID</td>
      <td>合同号</td>
      <td>模具编号</td>
      <td>物料名称</td>
      <td>规格</td>
      <td>需求数量</td>
      <td>实际数量</td>
      <td>单位</td>
      <td>申请人</td>
      <td>申请部门</td>
      <td>单价<br />
        (含税)</td>
      <td>税率</td>
      <td>金额<br />
        (含税)</td>
      <td>现金</td>
      <td>供应商</td>
      <td>订单日期</td>
      <td>计划<br />回厂时间</td>
      <td>In</td>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $listid = $row['listid'];
    $iscash = $row['iscash'] == '0'?'否':'是';
    $remark = $row['remark'];
    //计算金额
    $amount = number_format((floatval($row['unit_price']) * floatval($row['actual_quantity'])),2,'.','');
    if($remark){
      $specification = "<span title=\"".$remark."\" style=\"text-decoration:underline;\">".$row['specification']."</span>";
    }else{
      $specification = $row['specification'];
    }
    $in_quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['in_quantity']:0;
    $quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['quantity']:0;
    $pay_amount = ($iscash)?array_key_exists($listid,$array_pay_amount)?$array_pay_amount[$listid]:0:'--';
  ?>
    <tr>
      <td><input type="checkbox" value="<?php echo $listid; ?>"></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_no']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_specification']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['unit']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $amount; ?></td>
      <td><?php echo $iscash ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo date('Y-m-d',strtotime($row['order_date'])); ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td>
          <a href="other_material_in_listin.php?id=<?php echo $listid; ?>&amp;action=add"><img src="../images/system_ico/in_10_8.png" width="10" height="8" /></a>
      </td>
    </tr>
    <?php } ?>
 <!--    <tr>
      <td colspan="13">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_process_cost,2); ?></td>
      <td colspan="6">&nbsp;</td>
    </tr> -->
  </table>
  <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="入库" class="select_button" />
    </div>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<!--期间物料入库单-->


<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
  $entry_number = trim($_GET['entry_number']);
  $sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'M' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
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
<div id="table_search">
  <h4>期间物料入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <td>入库单号：</td>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <td>入库单日期：</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
    while($row_id = $result_id->Fetch_assoc()){
      $array_entryid .= $row_id['entryid'].','; 
    }
    $array_entryid = trim($array_entryid,',');
    $sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'M' GROUP BY `db_godown_entry_list`.`entryid`";
    $result_entry = $db->query($sql_entry);
    if($result_entry->num_rows){
      while($row_entry =$result_entry->fetch_assoc()){
        $array_entry[$row_entry['entryid']] = $row_entry['count'];
      }
    }else{
      $array_entry = array();
    }
    ?>
    <table>
      <tr>
        <th width="4%">ID</td>
        <th width="20%">入库单号</td>
        <th width="20%">入库单日期</td>
        <th width="20%">制单人</td>
        <th width="20%">时间</td>
        <th width="4%">项数</td>
        <th width="4%">Add</td>
        <th width="4%">List</td>
        <th width="4%">Print</td>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $entryid = $row['entryid'];
      $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="material_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
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