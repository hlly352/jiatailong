<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET('4',`supplier_typeid`) ORDER BY `supplier_code` ASC";
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
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var supplierid = $("#supplierid").val();
		if(!supplierid){
			$("#supplierid").focus();
			return false;
		}
		var delivery_cycle = $("#delivery_cycle").val();
		if(!ri_b.test(delivery_cycle)){
			$("#delivery_cycle").focus();
			return false;
		}
	})
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <script type="text/javascript">
    $(function(){
      $('#submit').live('click',function(){
        //判断是否选择付款方式
        var pay_type = $('input[name = pay_type]:checked').val();
        if(!pay_type){
          alert('请选择付款方式');
          return false;
        }
      })
    })
  </script>
  <h4>物料订单添加</h4>
  <form action="other_material_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">合同号：</th>
        <td width="80%">系统生成</td>
      </tr>
      <tr>
        <th>订单日期：</th>
        <td><input type="text" name="order_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid" id="supplierid" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>交货周期：</th>
        <td><input type="text" name="delivery_cycle" id="delivery_cycle" value="5" class="input_txt" />
          天</td>
      </tr>
      <tr>
        <th>付款类型：</th>
        <td>
          <label><input type="radio" value="M" name="pay_type" />月结</label>
          &nbsp;
          <label><input type="radio" value="P" name="pay_type" />预付</label>
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
  <?php
  }elseif($action == "edit"){
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $orderid = fun_check_int($_GET['id']);
	  $sql = "SELECT `order_number`,`order_date`,`delivery_cycle`,`supplierid`,`order_status` FROM `db_other_material_order` WHERE `orderid` = '$orderid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
    
  ?>
  <h4>期间物料订单修改</h4>
  <form action="other_material_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">合同号：</th>
        <td width="80%"><?php echo $array['order_number']; ?></td>
      </tr>
      <tr>
        <th>订单日期：</th>
        <td><?php echo date('Y-m-d',strtotime($array['order_date'])); ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td>
          <select name="supplierid" id="supplierid" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
			?>    <?php $is_select = $array['supplierid'] == $row_supplier['supplierid'] ?'selected':'';?>
            <option <?php echo $is_select ?> value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $array['supplierid']) echo " selected=\"selected\""; ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>交货周期：</th>
        <td><input type="text" name="delivery_cycle" id="delivery_cycle" value="<?php echo $array['delivery_cycle']; ?>" class="input_txt" />
          天</td>
      </tr>
      <tr>
        <th>订单状态：</th>
        <td><select name="order_status">
            <?php foreach($array_order_status as $order_status_key=>$order_status_value){ ?>
            <option value="<?php echo $order_status_key; ?>"<?php if($order_status_key == $array['order_status']) echo " selected=\"selected\""; ?>><?php echo $order_status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
          <input type="hidden" name="action" value="edit" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>