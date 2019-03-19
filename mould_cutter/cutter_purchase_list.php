<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$purchaseid = fun_check_int($_GET['purchaseid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_purchase = "SELECT `db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_cutter_purchase`.`purchase_time`,`db_employee`.`employee_name` FROM `db_cutter_purchase` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_purchase`.`employeeid` WHERE `db_cutter_purchase`.`purchaseid` = '$purchaseid' AND `db_cutter_purchase`.`employeeid` = '$employeeid'";
$result_purchase = $db->query($sql_purchase);
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
if($result_purchase->num_rows){
	$array_purchase = $result_purchase->fetch_assoc();
	//类型
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
	$result_cutter_typeid = $db->query($sql_cutter_type);
	//品牌
	$sql_cutter_brand = "SELECT `brandid`,`brand` FROM `db_cutter_brand` ORDER BY `brandid` ASC";
	$result_cutter_brand = $db->query($sql_cutter_brand);
	//供应商
	$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) > 0 ORDER BY `supplier_code` ASC";
	$result_supplier = $db->query($sql_supplier);
?>
<div id="table_sheet">
  <h4>刀具申购明细添加</h4>
  <form action="cutter_purchase_listdo.php" name="cutter_purchase_list" method="post">
    <table>
      <tr>
        <th width="10%">申购单号：</th>
        <td width="15%"><?php echo $array_purchase['purchase_number']; ?></td>
        <th width="10%">申购人：</th>
        <td width="15%"><?php echo $array_purchase['employee_name']; ?></td>
        <th width="10%">申购日期：</th>
        <td width="15%"><?php echo $array_purchase['purchase_date']; ?></td>
        <th width="10%">操作时间：</th>
        <td width="15%"><?php echo $array_purchase['purchase_time']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="typeid" id="typeid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
					echo "<option value=\"".$row_cutter_type['typeid']."\">".$row_cutter_type['type']."</option>";
				}
			}
			?>
          </select></td>
        <th>规格：</th>
        <td><select name="specificationid" id="specificationid">
            <option value="">请选择</option>
          </select></td>
        <th>材质：</th>
        <td><select name="texture" id="texture">
            <option value="">请选择</option>
            <?php
            foreach($array_cutter_texture as $texture_key=>$texture_value){
				echo "<option value=\"".$texture_key."\">".$texture_value."</option>";
			}
			?>
          </select></td>
        <th>硬度：</th>
        <td><select name="hardnessid" id="hardnessid">
            <option value="">请选择</option>
          </select></td>
      </tr>
      <tr>
        <th>品牌：</th>
        <td><select name="brandid" id="brandid">
            <option value="">请选择</option>
            <?php
			if($result_cutter_brand->num_rows){
				while($row_cutter_brand = $result_cutter_brand->fetch_assoc()){
					echo "<option value=\"".$row_cutter_brand['brandid']."\">".$row_cutter_brand['brand']."</option>";
				}
			}
			?>
          </select></td>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" class="input_txt" size="10" />
          件</td>
        <th>库存：</th>
        <td><span id="surplus">0</span> 件</td>
        <th>计划回厂日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo date('Y-m-d',strtotime("+5 day")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
        <th>备注：</th>
        <td colspan="5"><input type="text" name="remark" class="input_txt" size="28" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit_do" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="purchaseid" value="<?php echo $purchaseid; ?>" />
          <input type="hidden" name="action" value="add" /></td>
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
	$sqlwhere = " WHERE `db_cutter_specification`.`specification` LIKE '%$specification%' AND `db_cutter_hardness`.`hardness` LIKE '%$hardness%' $sql_typeid $sql_texture";
}
$sql_cutter_list = "SELECT `db_mould_cutter`.`cutterid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness` FROM `db_mould_cutter` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` $sqlwhere";
$result_cutter_list = $db->query($sql_cutter_list);
$pages = new page($result_cutter_list->num_rows,10);
$sql_cutter_list = $sql_cutter_list . " ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_hardness`.`texture` ASC,`db_mould_cutter`.`cutterid` DESC" . $pages->limitsql;
$result_cutter_list = $db->query($sql_cutter_list);
$result_id = $db->query($sql_cutter_list);
?>
<div id="table_search">
  <h4>刀具数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_typeid->num_rows){
				while($row_cutter_typeid = $result_cutter_typeid->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_typeid['typeid']; ?>"<?php if($row_cutter_typeid['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_typeid['type']; ?></option>
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
          <input type="button" name="button" value="明细" class="button" onclick="location.href='cutter_purchase_list_info.php?purchaseid=<?php echo $purchaseid; ?>'" />
          <input type="hidden" name="purchaseid" value="<?php echo $purchaseid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result_cutter_list->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_cutterid .= $row_id['cutterid'].',';
	  }
	  $array_cutterid = rtrim($array_cutterid,',');
	  $sql_surplus = "SELECT `db_cutter_purchase_list`.`cutterid`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` IN ($array_cutterid) AND `db_cutter_order_list`.`surplus` > 0 GROUP BY `db_cutter_purchase_list`.`cutterid`";
	  $result_surplus = $db->query($sql_surplus);
	  if($result_surplus->num_rows){
		  while($row_surplus = $result_surplus->fetch_assoc()){
		  $array_surplus[$row_surplus['cutterid']] = $row_surplus['surplus'];
		  }
	  }else{
		  $array_surplus = array();
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="18%">类型</th>
      <th width="24%">规格</th>
      <th width="18%">材质</th>
      <th width="22%">硬度</th>
      <th width="6%">库存</th>
      <th width="4%">单位</th>
      <th width="4%">Add</th>
    </tr>
    <?php
    while($row_cutter_list = $result_cutter_list->fetch_assoc()){
		$cutterid = $row_cutter_list['cutterid'];
		$surplus = array_key_exists($cutterid,$array_surplus)?$array_surplus[$cutterid]:0;
	?>
    <tr>
      <td><?php echo $cutterid; ?></td>
      <td><?php echo $row_cutter_list['type']; ?></td>
      <td><?php echo $row_cutter_list['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row_cutter_list['texture']]; ?></td>
      <td><?php echo $row_cutter_list['hardness']; ?></td>
      <td><?php echo $surplus; ?></td>
      <td>件</td>
      <td><a href="cutter_purchase_list_add.php?purchaseid=<?php echo $purchaseid ?>&cutterid=<?php echo $cutterid ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无刀具数据！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>