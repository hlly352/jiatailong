<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $order_number = trim($_GET['order_number']);
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    if($cut_payment_type == 'M'){
        $sql_supplierid = " AND `db_material_order`.`supplierid` = '$supplierid'";
      }elseif($cut_payment_type == 'C'){
        $sql_supplierid = " AND `db_cutter_order`.`supplierid` = '$supplierid'";
      }elseif($cut_payment_type == 'O'){
        $sql_supplierid = " AND `db_other_material_order`.`supplierid` = '$suplierid'";
      }
  }
  if($cut_payment_type == 'M'){
      $sqlwhere = " AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
    }elseif($cut_payment_type == 'C'){
      $sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
    }elseif($cut_payment_type == 'O'){
      $sqlwhere = " AND `db_other_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
    }
}
$cut_payment_type = isset($_GET['cut_payment_type'])?$_GET['cut_payment_type']:'M';
if($cut_payment_type == 'M'){
$sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'I' AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') $sqlwhere";
  }elseif($cut_payment_type == 'C'){
    $sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`listid`,`db_cutter_inout`.`form_number`,`db_cutter_inout`.`quantity` AS `in_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_order_list`.`unit_price`,`db_cutter_order`.`order_number`,`db_cutter_purchase_list`.`quantity` AS `order_quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE (`db_cutter_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND `db_cutter_inout`.`dotype` = 'I' $sqlwhere";
  }elseif($cut_payment_type == 'O'){
    $sql = "SELECT * FROM `db_other_material_inout` INNER JOIN `db_other_material_orderlist` ON `db_other_material_orderlist`.`listid` = `db_other_material_inout`.`listid` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid`  WHERE `db_other_material_inout`.`dotype` = 'I' AND (`db_other_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') $sqlwhere";
  }

$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_inout_list_in'] = $sql;
$pages = new page($result->num_rows,15);
if($cut_payment_type == 'M'){
    $sqllist = $sql . " ORDER BY `db_material_inout`.`inoutid` DESC" . $pages->limitsql;
  }elseif($cut_payment_type == 'C'){
    $sqllist = $sql." ORDER BY `db_cutter_inout`.`inoutid` DESC".$pages->limitsql;
  }elseif($cut_payment_type == 'O'){
    $sqllist = $sql." ORDER BY `db_other_material_inout`.`inoutid` DESC".$pages->limitsql;
  }
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
<script type="text/javascript">
  //每次只能扣款一项
$(function(){
  $('#submit').live('click',function(){
    var ids = $('input[name ^= id]').val();
    console.log(ids);
  
  })
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>物料入库记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>入库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          $is_select = $_GET['supplierid'] == $row_supplier['supplierid']?'selected':'';
          echo "<option ".$is_select." value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select>
        </td>
        <th>扣款类型：</th>
        <td>
          <select name="cut_payment_type" class="input_txt txt">
            <option value="M">模具物料</option>
            <option value="C">加工刀具</option>
            <option value="O">期间物料</option>
          </select>
        </td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_inout_in.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<form action='insert_cut_payment.php?action=add' method="post">
<?php if($cut_payment_type == 'M'){ ?>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];
      $total_process_cost += $row_total['process_cost'];  
    }                                                                       
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="6%">模具编号</th>
      <th width="8%">物料名称</th>
      <th width="12%">规格</th>
      <th width="7%">材质</th>
      <th width="8%">表单号</th>
      <th width="5%">订单<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="5%">实际<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="5%">单价<br />
        (含税)</th>
      <th width="5%">金额<br />
        (含税)</th>
      <th width="5%">加工费</th>
      <th width="6%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="4">操作</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $inoutid = $row['inoutid'];
    $listid = $row['listid'];
  ?>
    <tr>
      <td>
        <input type="checkbox" value="<?php echo $inoutid; ?>" name="id[]" >
      </td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['texture']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['unit_name_order']; ?></td>
      <td><?php echo $row['inout_quantity']; ?></td>
      <td><?php echo $row['unit_name_actual']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><a href="insert_cut_payment.php?action=add&inoutid=<?php echo $inoutid ?>&cut_payment_type=<?php echo $cut_payment_type; ?>">扣款</a></td>
      <!-- <td><a href="material_inout_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td> -->
    </tr>
    <?php } ?>
    <tr>
      <td colspan="12">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_process_cost,2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
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
<?php } elseif($cut_payment_type=='C'){ ?>
    <div id="table_list">
  <?php
  if($result->num_rows){
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];  
    }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">合同号</th>
      <th width="6%">类型</th>
      <th width="10%">规格</th>
      <th width="6%">材质</th>
      <th width="8%">硬度</th>
      <th width="6%">品牌</th>
      <th width="8%">表单号</th>
      <th width="5%">订单<br />
        数量</th>
      <th width="5%">入库<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价<br />
        (含税)</th>
      <th width="6%">金额<br />
        (含税)</th>
      <th width="8%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="4">操作</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
    $inoutid = $row['inoutid'];
    $listid = $row['listid'];
  ?>
    <tr>
      <td><input type="checkbox" value="<?php echo $inoutid; ?>" name="id[]"></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['brand']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['order_quantity']; ?></td>
      <td><?php echo $row['in_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><a href="insert_cut_payment.php?action=add&inoutid=<?php echo $inoutid; ?>&cut_payment_type=<?php echo $cut_payment_type; ?>">扣款</a></td>
<!--       <td><a href="cutter_inout_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td> -->
    </tr>
    <?php } ?>
    <tr>
      <td colspan="12">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
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
<?php } elseif($cut_payment_type=='O'){ ?>
    <div id="table_list">
  <?php
  if($result->num_rows){
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];
      $total_process_cost += $row_total['process_cost'];  
    }                                                                       
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="8%">物料名称</th>
      <th width="12%">规格</th>
      <th width="6%">表单号</th>
      <th width="6%">订单<br />
        数量</th>
      <th width="6%">实际<br />
        数量</th>
      <th width="5%">单位</th>
      <th width="6%">单价<br />
        (含税)</th>
      <th width="5%">金额<br />
        (含税)</th>
      <th width="5%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="4%">操作</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $inoutid = $row['inoutid'];
    $listid = $row['listid'];
  ?>
    <tr>
      <td>
        <input type="checkbox" value="<?php echo $inoutid; ?>" name="id[]">
      </td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_specification']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['inout_quantity']; ?></td>
      <td><?php echo $row['unit']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amounts'] ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><a href="insert_cut_payment.php?action=add&inoutid=<?php echo $inoutid ?>&cut_payment_type=<?php echo $cut_payment_type; ?>">扣款</a></td>
    <?php } ?>
    <tr>
      <td colspan="9">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td></td>
      <td colspan="4">&nbsp;</td>
    </tr>
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
<?php } ?>
<!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button"  disabled="disabled" />
  </div> -->
      <input type="hidden" value="<?php echo $cut_payment_type ?>" name="cut_payment_type">
</form>
<?php include "../footer.php"; ?>
</body>
</html>