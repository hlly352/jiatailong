<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$accountid = $_GET['id'];
//查询计量单位
$sql_unit = "SELECT `unitid`,`unit_name` FROM `db_unit` ORDER BY `unitid` ASC";
$result_unit = $db->query($sql_unit);
if($result_unit->num_rows){
  while($row_unit = $result_unit->fetch_assoc()){
    $array_unit[$row_unit['unitid']] = $row_unit['unit_name'];
  }
}else{
  $array_unit = array();
}
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
<script language="javascript" type="text/javascript">
$(function(){
  /*
  $("input[name^=order_quantity]").blur(function(){
    var order_quantity = $(this).val();
    if(!rf_b.test(order_quantity)){
      alert('请输入大于零的数字');
      $(this).val(this.defaultValue);
    }else{
      $(this).val(parseFloat($(this).val()).toFixed(2));
      var array_id = $(this).attr('id').split('-');
      var materialid = array_id[1];
      $.post('../ajax_function/material_order_quantity.php',{
      materialid:materialid
      },function(data,textstatus){
        var unit_price = $("#unit_price-"+materialid).val();
        var actual_quantity = order_quantity*data;
        $("#actual_quantity-"+materialid).val(parseFloat(actual_quantity).toFixed(2));
        var amount = actual_quantity*unit_price;
        $("#amount-"+materialid).val(amount.toFixed(2));
      })
    }
  })
  */
  //失去焦点
  $("input[name^=actual_quantity]").blur(function(){
    var actual_quantity = $(this).val();
    if(!rf_a.test(actual_quantity) && $.trim(actual_quantity)){
      alert('请输入数字');
      $(this).val(this.defaultValue);
    }else{
      if($.trim(actual_quantity)){
        $(this).val(parseFloat($(this).val()).toFixed(2));
        var array_id = $(this).attr('id').split('-');
        var materialid = array_id[1];
        var unit_price = $("#unit_price-"+materialid).val();
        var amount = actual_quantity*unit_price;
        $("#amount-"+materialid).val(amount.toFixed(2));
      }
    }
  })
  //获取焦点
  $("input[name^=actual_quantity]").focus(function(){
    var array_id = $(this).attr('id').split('-');
    var materialid = array_id[1];
    $.post('../ajax_function/material_order_quantity.php',{
      materialid:materialid
    },function(data,textstatus){
      var array_data = data.split('#');
      var actual_quantity = array_data[0];  
      var unitid = array_data[1];
      $("#actual_quantity-"+materialid).val(actual_quantity);
      $("#actual_unitid-"+materialid).find("option[value="+unitid+"]").attr("selected",true);
      var unit_price = $("#unit_price-"+materialid).val();
      var amount = actual_quantity*unit_price;
      $("#amount-"+materialid).val(amount.toFixed(2));
    })
  })                 
  $("input[name^=unit_price]").blur(function(){
    var unit_price = $(this).val();
    if($.trim(unit_price) && !rf_b.test(unit_price)){
      alert('请输入大于零的数字')
      $(this).val(this.defaultValue);
    }else{
      if($.trim(unit_price)){
        $(this).val(parseFloat($(this).val()).toFixed(2));
        var array_id = $(this).attr('id').split('-');
        var materialid = array_id[1];
        var actual_quantity = $("#actual_quantity-"+materialid).val();
        var amount = actual_quantity*unit_price;
        $("#amount-"+materialid).val(amount.toFixed(2));
      }
    }
  })

  /*
  $("input[name^=amount]").blur(function(){
    var amount = $(this).val();
    if(!rf_a.test(amount)){
      alert('请输入数字')
      $(this).val(this.defaultValue);
    }else{
      $(this).val(parseFloat($(this).val()).toFixed(2))
    }
  })
  */
  $("#data_source").change(function(){
    $("#submit").click();
  })
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  //$sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` INNER JOIN `db_department` ON `db_mould_other_material`.`apply_team` = `db_department`.`deptid` INNER JOIN `db_other_supplier` ON `db_other_supplier`.`other_supplier_id` = `db_other_material_order`.`supplierid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` WHERE `db_material_account_list`.`accountid`='$accountid' AND `db_other_material_inout`.`dotype` ='I' AND `db_other_material_inout`.`account_status` = 'F' $sqlwhere";
  $sql_order = "SELECT * FROM `db_material_account_list` INNER JOIN `db_other_material_inout` ON `db_material_account_list`.`inoutid` = `db_other_material_inout`.`inoutid` INNER JOIN `db_other_material_orderlist` ON `db_other_material_inout`.`listid` = `db_other_material_orderlist`.`listid` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` INNER JOIN `db_other_supplier` ON `db_other_material_order`.`supplierid` = `db_other_supplier`.`other_supplier_id` INNER JOIN `db_material_account` ON `db_material_account_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_employee` ON `db_material_account`.`employeeid` = `db_employee`.`employeeid` WHERE `db_material_account_list`.`accountid` = '$accountid' AND `db_other_material_inout`.`dotype`='I' AND `db_other_material_inout`.`account_status` = 'F'";
  echo $sql_order;
$sql = $sql.'ORDER BY `db_other_material_order`.`orderid`';
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
    $array_order = $result_order->fetch_assoc();
    $plan_date = $array_order['plan_date'];
  ?>
  <h4>订单信息</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo date('Y-m-d',strtotime($array_order['order_date'])); ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
    die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
$data_source = $_GET['data_source']?trim($_GET['data_source']):'A';
if($_GET['submit']){
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['material_specification']);
  $sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_other_material`.`material_name` LIKE '%$material_name%' AND `db_mould_other_material`.`specification` LIKE '%$specification%'";
}
if($data_source == 'A'){
  //$sql = "SELECT `db_mould_other_material`.`materialid`,`db_mould_other_material`.`material_number`,`db_mould_other_material`.`material_name`,`db_mould_other_material`.`specification`,`db_mould_other_material`.`material_quantity`,`db_mould_other_material`.`texture`,`db_mould_other_material`.`complete_status`,`db_mould`.`mould_number` FROM `db_material` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`materialid` = `db_material_inquiry`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_other_material`.`mouldid` WHERE `db_mould_other_material`.`materialid` NOT IN (SELECT `materialid` FROM `db_other_material_orderlist` GROUP BY `materialid`) AND `db_material_inquiry`.`employeeid` = '$employeeid' $sqlwhere";
  $sql = "SELECT * FROM `db_mould_other_material` INNER JOIN `db_other_material_orderlist` ON `db_mould_other_material`.`mould_other_id`=`db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` WHERE `orderid`='$orderid' AND `inquiryid` = '$employeeid' AND `status` = 'F'";
}elseif($data_source == 'B'){
  $sql = "SELECT * FROM `db_mould_other_material` WHERE `status` = 'E'";
}elseif($data_source == 'C'){
  $sql = "SELECT * FROM `db_mould_other_material` WHERE `status` = 'E'";
}
$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_mould_other_material`.`mould_other_id` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>物料详情</h4>
  
</div>
<div id="table_list">
  <?php if($result_order->num_rows){ ?>
  <form action="other_order_material_listdo.php" name="material_list" method="post">
     <table>
    <tr>
      <th>ID</th>
      <th>合同号</th>
      <th>物料名称</th>
      <th>规格</th>
      <th>需求数量</th>
      <th>实际数量</th>
      <th>单位</th>
      <th>申请人</th>
      <th>单价<br />
        (含税)</th>
      <th>税率</th>
      <th>金额<br />
        (含税)</th>
      <th>现金</th>
      <th>供应商</th>
      <th>订单日期</th>
      <th>计划<br />回厂时间</th>
      <th>备注</th>
    </tr>
    <?php
  while($row = $result_order->fetch_assoc()){
    $listid = $row['listid'];
    $iscash = $row['iscash'] == '0'?'否':'是';
    $remark = $row['remark'];


  ?>
    <tr>
      <td><input type="checkbox" value="<?php echo $listid; ?>"></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_specification']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['unit']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $amount; ?></td>
      <td><?php echo $iscash ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo date('Y-m-d',strtotime($row['order_date'])); ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><?php echo $row['remark'] ?></td>
    </tr>
    <?php } ?>
 <!--    <tr>
      <td colspan="13">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_process_cost,2); ?></td>
      <td colspan="6">&nbsp;</td>
    </tr> -->

    <tr>
      <td colspan="12">总金额</td>
      <td><?php echo number_format($amount,2,'.',''); ?></td>
      <td colspan="7">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="18">
        <input type="button" class="button" name="" value="确定" onclick="window.location.assign('other_balance_account_do.php?accountid=<?php echo $accountid ?>&action=complete')">
        &nbsp;
        <input type="button" class="button" name="" value="退回" onclick="window.location.assign('other_balance_account_do.php?accountid=<?php echo $accountid ?>&action=back')">
        &nbsp;
        <input type="button" class="button" onclick="window.history.go(-1)" value="返回">
      </td>
    </tr>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>