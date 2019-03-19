<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//查询供应商
$tryid = fun_check_int($_GET['id']);
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
$sql = "SELECT `db_mould_try`.`mould_size`,`db_mould_try`.`plan_date`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,`db_mould_try`.`try_causeid`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`approver`,`db_mould_try`.`remark`,`db_mould_try`.`approve_status`,`db_mould_try`.`try_status`,`db_mould`.`mould_number`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code`,`db_mould_try_cause`.`try_causename` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` WHERE `db_mould_try`.`tryid` = '$tryid' AND `db_mould_try`.`approve_status` = 'B' AND `db_mould_try`.`try_status` = 1 AND `db_mould_try`.`finish_status` = 0";
$result = $db->query($sql);
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
		var order_number = $("#order_number").val();
		if(!$.trim(order_number)){
			$("#order_number").focus();
			return false;
		}
		var unit_price = $("#unit_price").val();
		if(!ri_b.test($.trim(unit_price))){
			$("#unit_price").focus();
			return false;
		}
		var cost = $("#cost").val();
		if(!ri_b.test($.trim(cost))){
			$("#cost").focus();
			return false;
		}
	})
})
</script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $approve_status = $array['approve_status'];
  ?>
  <h4>试模确认</h4>
  <form action="mould_try_finishdo.php" name="mould_try_finish" method="post">
    <table>
      <tr>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">客户代码：</th>
        <td width="15%"><?php echo $array['client_code']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array['project_name']; ?></td>
        <th width="10%">模具尺寸：</th>
        <td width="15%"><?php echo $array['mould_size']; ?></td>
      </tr>
      <tr>
        <th>穴数：</th>
        <td><?php echo $array['cavity_number']; ?></td>
        <th>难度系数：</th>
        <td><?php echo $array['difficulty_degree']; ?></td>
        <th>试模次数：</th>
        <td><?php echo $array['try_times']; ?></td>
        <th>试模原因：</th>
        <td><?php echo $array['try_causename']; ?></td>
      </tr>
      <tr>
        <th>啤机吨位(T)：</th>
        <td><?php echo $array['tonnage']; ?></td>
        <th>成型周期(S)：</th>
        <td><?php echo $array['molding_cycle']; ?></td>
        <th>胶料品种：</th>
        <td><?php echo $array['plastic_material']; ?></td>
        <th>胶料颜色：</th>
        <td><?php echo $array['plastic_material_color']; ?></td>
      </tr>
      <tr>
        <th>胶料来源：</th>
        <td><?php echo $array['plastic_material_offer']; ?></td>
        <th>产品单重(g)：</th>
        <td><?php echo $array['product_weight']; ?></td>
        <th>样板啤数(啤)：</th>
        <td><?php echo $array['product_quantity']; ?></td>
        <th>需要用料(kg)：</th>
        <td><?php echo $array['material_weight']; ?></td>
      </tr>
      <tr>
        <th>要求时间：</th>
        <td><?php echo $array['plan_date']; ?></td>
        <th>试模日期：</th>
        <td><input type="text" name="try_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>钳工组别：</th>
        <td><?php echo $array_mould_assembler[$array['assembler']]; ?></td>
        <th>备注：</th>
        <td><?php echo $array['remark']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid" id="supplierid">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <th>送货单号：</th>
        <td><input type="text" name="order_number" id="order_number" class="input_txt" /></td>
        <th>含税单价(元)：</th>
        <td><input type="text" name="unit_price" id="unit_price" class="input_txt" /></td>
        <th>金额(元)：</th>
        <td><input type="text" name="cost" id="cost" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="tryid" value="<?php echo $tryid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>