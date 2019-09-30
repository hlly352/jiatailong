<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
  $sql_order = "SELECT `db_other_material_order`.`order_number`,`db_other_material_order`.`order_date`,`db_other_material_order`.`dotime`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_other_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_other_material_order`.`employeeid` WHERE `db_other_material_order`.`orderid` = '$orderid' AND `db_other_material_order`.`employeeid` = '$employeeid'";
//判断是否是查看详情
$info = $_GET['action'];
$str = empty($info)?"AND `status` = 'E'":"";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
    $array_order = $result_order->fetch_assoc();
    $plan_date = $array_order['plan_date'];
  ?>
  <h4>物料订单</h4>
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
if($_GET['submit']){
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['material_specification']);
  $sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}

 $sql = "SELECT `db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_other_material_data`.`unit`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_other_material_orderlist`.`actual_quantity`,`db_other_material_orderlist`.`unit_price`,`db_other_material_orderlist`.`tax_rate`,(`db_other_material_orderlist`.`actual_quantity` * `db_other_material_orderlist`.`unit_price`) AS `amount`,`db_other_material_orderlist`.`iscash`,`db_other_material_orderlist`.`plan_date` FROM `db_other_material_orderlist`  INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid`  WHERE `db_other_material_orderlist`.`orderid` = '$orderid'";

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_mould_other_material`.`mould_other_id` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>订单物料</h4>
  
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="other_order_material_listdo.php" name="material_list" method="post">
    <table>
      <tr>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th >需求数量</th>
        <th >实际数量</th>
        <th>单位</th>
        <th width="">单价(含税)</th>
        <th width="">税率</th>
        <th width="">金额(含税)</th>
        <th width="">现金</th>
        <th width="">计划回厂日期</th>
        <th width="">备注</th>
      </tr>
      
      <?php
        while($row = $result->fetch_assoc()){
      ?>
      <tr>
        <td><?php echo $row['material_unit']?$row['material_name']:$row['data_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>
         <?php echo $row['actual_quantity'] ?>
        </td>
        <td><?php echo $row['material_unit']?$row['material_unit']:$row['unit'] ?></td>
        <td>
          <?php echo $row['unit_price'] ?>
        </td>
        <td>
          <?php
            echo ($row['tax_rate']*100).'%';
          ?>
        </td>
        <td>
          <?php
            $amount = floatval($row['unit_price'])*floatval($row['actual_quantity']);
            echo number_format($amount,2,'.','');
          ?>
        </td>
    
        <td>
          <?php echo $row['iscash']=='0'?'否':'是' ?>
        </td>
        <td>
          <?php echo $row['plan_date'] ?>
        </td>
        <td>
          <?php echo $row['remark'] ?>
        </td>
      </tr>
      <?php } ?>
      <tr>
      <td colspan="14">
          <input type="button" name="button" value="返回" class="button" onclick="window.location.assign('other_material_order.php')" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
      <input type="hidden" name="mould_other_id" value="<?php echo $row['mould_other_id'] ?>"/>
        </td>
      </tr>
    </table>
  </form>
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