<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$purchase_listid = fun_check_int($_GET['purchase_listid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "SELECT `db_cutter_purchase_list`.`purchaseid`,`db_cutter_purchase_list`.`cutterid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`brandid`,`db_cutter_purchase_list`.`supplierid`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_specification`.`typeid`,`db_mould_cutter`.`specificationid`,`db_cutter_hardness`.`texture`,`db_mould_cutter`.`hardnessid`,`db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_cutter_purchase`.`purchase_time`,`db_employee`.`employee_name` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_purchase`.`employeeid` LEFT JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`purchase_listid` = `db_cutter_purchase_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`purchase_listid` = '$purchase_listid' AND `db_cutter_purchase`.`employeeid` = '$employeeid' AND `db_cutter_order_list`.`listid` IS NULL";
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#typeid").change(function(){
		var typeid = $(this).val();
		$("#surplus").html(0);
		if(typeid){
			$.post("select_cutter_specification.php",{
				   typeid:typeid
			},function(data,textStatus){
				$("#specificationid").html(data);
			})
		}else{
			$("#specificationid").html("<option vlaue=\"\">请选择</option>");
		}
	})
	$("#specificationid").change(function(){
		var specificationid = $(this).val();
		var hardnessid = $("#hardnessid").val();
		if(specificationid && hardnessid){
			$.post("mould_cutter_surplus.php",{
				   specificationid:specificationid,
				   hardnessid:hardnessid
			},function(data,textStatus){
				$("#surplus").html(data);
			})
		}else{
			$("#surplus").html(0);
		}
	})
	$("#texture").change(function(){
		var texture = $(this).val();
		$("#surplus").html(0);
		if(texture){
			$.post("select_cutter_hardness.php",{
				   texture:texture
			},function(data,textStatus){
				$("#hardnessid").html(data);
			})
		}else{
			$("#hardnessid").html("<option vlaue=\"\">请选择</option>");
			
		}
	})
	$("#hardnessid").change(function(){
		var hardnessid = $(this).val();
		var specificationid = $("#specificationid").val();
		if(specificationid && hardnessid){
			$.post("mould_cutter_surplus.php",{
				   specificationid:specificationid,
				   hardnessid:hardnessid
			},function(data,textStatus){
				$("#surplus").html(data);
			})
		}else{
			$("#surplus").html(0);
		}
	})
	$("#submit_do").click(function(){
		var typeid = $("#typeid").val();
		if(!typeid){
			$("#typeid").focus();
			return false;
		}
		var specificationid = $("#specificationid").val();
		if(!specificationid){
			$("#specificationid").focus();
			return false;
		}
		var texture = $("#texture").val();
		if(!texture){
			$("#texture").focus();
			return false;
		}
		var hardnessid = $("#hardnessid").val();
		if(!hardnessid){
			$("#hardnessid").focus();
			return false;
		}
		var brandid = $("#brandid").val();
		if(!brandid){
			$("#brandid").focus();
			return false;
		}
		var quantity = $("#quantity").val();
		if(!ri_b.test(quantity)){
			$("#quantity").focus();
			return false;
		}
		var supplierid = $("#supplierid").val();
		if(!supplierid){
			$("#supplierid").focus();
			return false;
		}
	})
})
</script>
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$purchaseid = $array['purchaseid'];
	$cutterid = $array['cutterid'];
	$cutter_typeid = $array['typeid'];
	$cutter_texture = $array['texture'];
	//类型
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
	$result_cutter_type_id = $db->query($sql_cutter_type);
	//规格
	$sql_cutter_specification = "SELECT `specificationid`,`specification` FROM `db_cutter_specification` WHERE `typeid` = '$cutter_typeid' ORDER BY `specificationid` ASC";
	$result_cutter_specification = $db->query($sql_cutter_specification);
	//硬度
	$sql_cutter_hardness = "SELECT `hardnessid`,`hardness` FROM `db_cutter_hardness` WHERE `texture` = '$cutter_texture' ORDER BY `hardnessid` ASC";
	$result_cutter_hardness = $db->query($sql_cutter_hardness);
	//品牌
	$sql_cutter_brand = "SELECT `brandid`,`brand` FROM `db_cutter_brand` ORDER BY `brandid` ASC";
	$result_cutter_brand = $db->query($sql_cutter_brand);
	//供应商
	$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) > 0 ORDER BY `supplier_code` ASC";
	$result_supplier = $db->query($sql_supplier);
	//库存
	$sql_surplus = "SELECT `db_cutter_order_list`.`surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchae_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` WHERE `db_mould_cutter`.`cutterid` = '$cutterid' AND `db_cutter_order_list`.`surplus` > 0";
	$result_surplus = $db->query($sql_surplus);
	if($result_surplus->num_rows){
		while($array_surplus = $result_surplus->fetch_assoc()){
		$surplus += $array_surplus['surplus'];
		}
	}else{
		$surplus = 0;
	}
?>
<div id="table_sheet">
  <h4>刀具申购明细修改</h4>
  <form action="cutter_purchase_listdo.php" name="cutter_purchase_list" method="post">
    <table>
      <tr>
        <th width="10%">申购单号：</th>
        <td width="15%"><?php echo $array['purchase_number']; ?></td>
        <th width="10%">申购人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
        <th width="10%">申购日期：</th>
        <td width="15%"><?php echo $array['purchase_date']; ?></td>
        <th width="10%">操作时间：</th>
        <td width="15%"><?php echo $array['purchase_time']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="typeid" id="typeid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $cutter_typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>规格：</th>
        <td><select name="specificationid" id="specificationid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_specification->num_rows){
				while($row_cutter_specification = $result_cutter_specification->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_specification['specificationid']; ?>"<?php if($row_cutter_specification['specificationid'] == $array['specificationid']) echo " selected=\"selected\""; ?>><?php echo $row_cutter_specification['specification']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>材质：</th>
        <td><select name="texture" id="texture">
            <option value="">请选择</option>
            <?php foreach($array_cutter_texture as $texture_key=>$texture_value){ ?>
            <option value="<?php echo $texture_key; ?>"<?php if($texture_key == $cutter_texture) echo " selected=\"selected\""; ?>><?php echo $texture_value; ?></option>
            <?php } ?>
          </select></td>
        <th>硬度：</th>
        <td><select name="hardnessid" id="hardnessid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_hardness->num_rows){
				while($row_cutter_hardness = $result_cutter_hardness->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_hardness['hardnessid']; ?>"<?php if($row_cutter_hardness['hardnessid'] == $array['hardnessid']) echo " selected=\"selected\""; ?>><?php echo $row_cutter_hardness['hardness']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>品牌：</th>
        <td><select name="brandid" id="brandid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_brand->num_rows){
				while($row_cutter_brand = $result_cutter_brand->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_brand['brandid']; ?>"<?php if($row_cutter_brand['brandid'] == $array['brandid']) echo " selected=\"selected\""; ?>><?php echo $row_cutter_brand['brand']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array['quantity']; ?>" class="input_txt" size="10" />
          件</td>
        <th>库存：</th>
        <td><span id="surplus"><?php echo $surplus; ?></span> 件</td>
        <th>计划回厂日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid" id="supplierid">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
			?>
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $array['supplierid']) echo " selected=\"selected\""; ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>备注：</th>
        <td colspan="5"><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" size="28" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit_do" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="purchase_listid" value="<?php echo $purchase_listid; ?>" />
          <input type="hidden" name="action" value="edit" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
if($_GET['submit']){
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$specification = trim($_GET['specification']);
	$texture = $_GET['texture'];
	if($texture){
		$sql_texture = " AND `db_cutter_hardness`.`texture` = '$texture'";
	}
	$hardness = trim($_GET['hardness']);
	$sqlwhere = " AND `db_cutter_specification`.`specification` LIKE '%$specification%' AND `db_cutter_hardness`.`hardness` LIKE '%$hardness%' $sql_typeid $sql_texture";
}
$sql_purchase_list = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_order_list`.`listid`,`db_cutter_order_list`.`in_quantity` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` LEFT JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`purchase_listid` = `db_cutter_purchase_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`purchaseid` = '$purchaseid' AND `db_cutter_purchase`.`employeeid` = '$employeeid' $sqlwhere ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_hardness`.`texture` ASC,`db_mould_cutter`.`cutterid` DESC";
$result_paurchase_list = $db->query($sql_purchase_list);
?>
<div id="table_search">
  <h4>申购明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type_id->num_rows){
				while($row_cutter_type_id = $result_cutter_type_id->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type_id['typeid']; ?>"<?php if($row_cutter_type_id['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type_id['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>材质：</th>
        <td><select name="texture">
            <option value="">所有</option>
            <?php foreach($array_cutter_texture as $texture_key=>$texture_value){ ?>
            <option value="<?php echo $texture_key; ?>"<?php if($texture_key == $texture) echo " selected=\"selected\"" ?>><?php echo $texture_value; ?></option>
            <?php } ?>
          </select></td>
        <th>硬度：</th>
        <td><input type="text" name="hardness" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_purchase_list.php?purchaseid=<?php echo $purchaseid; ?>'" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_purchase.php?id=<?php echo $purchaseid; ?>'" />
          <input type="hidden" name="purchase_listid" value="<?php echo $purchase_listid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_paurchase_list->num_rows){ ?>
  <form action="cutter_purchase_listdo.php" name="cutter_purchase_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">类型</th>
        <th width="12%">规格</th>
        <th width="8%">材质</th>
        <th width="12%">硬度</th>
        <th width="8%">品牌</th>
        <th width="6%">申购数量</th>
        <th width="6%">入库数量</th>
        <th width="4%">单位</th>
        <th width="8%">计划回厂日期</th>
        <th width="6%">状态</th>
        <th width="14%">备注</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row_purchase_list = $result_paurchase_list->fetch_assoc()){
		  $purchase_listid = $row_purchase_list['purchase_listid'];
		  $listid = $row_purchase_list['listid'];
		  $cutter_order_status = ($listid)?'已下单':'未下单';
		  $in_quantity = ($listid)?$row_purchase_list['in_quantity']:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $purchase_listid; ?>"<?php if($listid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_purchase_list['type']; ?></td>
        <td><?php echo $row_purchase_list['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row_purchase_list['texture']]; ?></td>
        <td><?php echo $row_purchase_list['hardness']; ?></td>
        <td><?php echo $row_purchase_list['brand']; ?></td>
        <td><?php echo $row_purchase_list['quantity']; ?></td>
        <td><?php echo $in_quantity; ?></td>
        <td>件</td>
        <td><?php echo $row_purchase_list['plan_date']; ?></td>
        <td><?php echo $cutter_order_status; ?></td>
        <td><?php echo $row_purchase_list['remark']; ?></td>
        <td><?php if($listid == NULL){ ?>
          <a href="cutter_purchase_list_edit.php?purchase_listid=<?php echo $purchase_listid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无刀具明细！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>