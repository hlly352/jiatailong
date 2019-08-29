<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$inoutid = $_GET['inoutid'];
$cut_payment_type = $_GET['cut_payment_type'];
//查询选择的扣款项信息
if($cut_payment_type == 'M'){
	$sql = "SELECT `db_supplier`.`supplierid`,`db_supplier`.`supplier_cname`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order_list`.`orderid` = `db_material_order`.`orderid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_inout`.`inoutid` = '$inoutid'";
}
$result = $db->query($sql);
if($result->num_rows){
	$row = $result->fetch_assoc();
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
	$("#submit").click(function(){
		var cut_payment = $("#cut_payment").val();
	    var cut_cause = $("#cut_cause").val();
	    if(!(cut_payment && ri_b.test(cut_payment))){
	      $("#cut_payment").focus();
	      return false;
	    }
	    if(!cut_cause){
	      $("#cut_cause").focus();
	      return false;
	    }
	   })
	$('#cut_cause').live('blur',function(){
		var cut_cause = $.trim($(this).val());
		$(this).val(cut_cause);
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
        
      })
    })
  </script>
  <h4>扣款信息添加</h4>
  <form action="cut_payment_do.php" name="material_order" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">供应商：</th>
        <td width="80%"><?php echo $row['supplier_cname'] ?>
        	<input type="hidden" name="supplierid" value="<?php echo $row['supplierid'] ?>" />
        </td>
      </tr>
      <tr>
        <th>合同号：</th>
        <td><?php echo $row['order_number']; ?>
        	<input type="hidden" value="<?php echo $row['order_number'] ?>" name="order_number" />
        </td>
      </tr>
      <tr>
        <th>物料名称：</th>
        <td><?php echo $row['material_name']; ?>
        	<input type="hidden" value="<?php echo $row['material_name'] ?>" name="material_name" />
        </td>
      </tr>
      <tr>
        <th>规格：</th>
        <td><?php echo $row['specification']; ?>
        	<input type="hidden" value="<?php echo $row['specification'] ?>" name="specification" />
        </td>
      </tr>
      <tr>
        <th>扣款金额：</th>
        <td>
        	<input tepe="text" id="cut_payment" class="input_txt" name="cut_payment" />
        </td>
      </tr>
      <tr>
        <th>扣款原因：</th>
        <td>
          <textarea id="cut_cause" name="cut_cause" >
          	
          </textarea>
        </td>
      </tr>
      <tr>
        <th>图片：</th>
        <td>
        <span>
          <?php echo $img_file ?>  
          </span>
          <input type="file" name="image" onchange="view_data(this)">
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
          <input type="hidden" name="cut_payment_type" value="<?php echo $cut_payment_type; ?>" />
          <input type="hidden" name="inoutid" value="<?php echo $inoutid ?>" />
        </td>
      </tr>
    </table>
  </form>
  <?php } ?>
<?php include "../footer.php"; ?>
</body>
<script type="text/javascript" src="../js/view_img.js"></script>
</html>