<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier`  ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
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
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>付款计划添加</h4>
  <form action="funds_plando.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">付款单号：</th>
        <td width="80%">系统生成</td>
      </tr>
      <tr>
        <th>付款日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="add_plan" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "add_prepayment"){ ?>
  <script language="javascript" type="text/javascript">
  //通过供应商和下单日期来查找合同号
  function getval(val){
    var order_date = $(val).val();
    var supplierid = $('#supplierid').val();
    var account_type = $('#account_type').val()
    if(supplierid && order_date){
      //获取当前月份供应商的订单号
      $.post('../ajax_function/get_order_number.php',{order_date:order_date,supplierid:supplierid,account_type:account_type},function(data){
        console.log(data);
        $('#order_number').empty();
        if(data != null){
        var sel = $('#order_number');
        for(var i=0;i<data.length;i++){
          var opt = '<option>'+data[i].order_number+'</option>';
            sel.append(opt);
        }
      } else {
        var sel = $('#order_number');
        var opt = '<option value="">当前月份无订单</option>';
        sel.append(opt);
      }
      },'json')
    }
  }
  $(function(){
    $('#supplierid,#account_type').live('change',function(){
     getval($('#order_date'));
    })
    //提交时对数据进行判断
    $('#submit').live('click',function(){
      var supplierid = $('#supplierid').val();
      var order_number = $('#order_number').val();
      if(!supplierid){
        alert('请选择供应商');
        $('#supplierid').focus();
        return false;
      }
      if(!order_number){
        alert('没有订单');
        return false;
      }
      var prepayment = $.trim($('input[name=prepayment]').val());
      if(!(prepayment && rf_a.test(prepayment))){
        alert('请输入数字');
        $('input[name=prepayment]').focus();
        return false;
      }
    })
  })
</script>
      <h4>添加预付款</h4>
  <form action="funds_plando.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">类型：</th>
        <td width="80%">
          <select name="account_type" id="account_type" class="input_txt txt">
            <option value="M">模具物料</option>
            <option value="C">加工刀具</option>
            <option value="O">期间物料</option>
          </select>
        </td>
      </tr>
      <tr>
        <th width="20%">供应商名称：</th>
        <td width="80%">
          <select class="input_txt txt" id="supplierid" name="supplierid">
            <option value="">请选择</option>
            <?php 
              while($row = $result_supplier->fetch_assoc()){
                echo '<option value="'.$row['supplierid'].'">'.$row['supplier_code'].'-'.$row['supplier_cname'].'</option>';
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>下单月份：</th>
        <td><input type="text" id="order_date" name="order_date" value="<?php echo date('Y-m'); ?>" onchange="getval(this);" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>合同号：</th>
        <td>
          <select class="input_txt txt" name="order_number" id="order_number"></select>
        </td>
      </tr>
      <tr>
        <th>预付金额：</th>
        <td>
          <input type="text" class="input_txt" placeholder="填入数字" name="prepayment">
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
 <?php }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>