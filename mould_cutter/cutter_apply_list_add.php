<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$applyid = fun_check_int($_GET['applyid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_apply = "SELECT `db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_apply`.`apply_time`,`db_employee`.`employee_name` FROM `db_cutter_apply` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_apply`.`applyid` = '$applyid' AND `db_cutter_apply`.`employeeid` = '$employeeid'";
$result_apply = $db->query($sql_apply);
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
	$("input[name^=quantity]").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		if(!ri_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(default_quantity);
		}else{
			var array_id = $(this).attr('id').split('-');
			var cutterid = array_id[1];
			$.post('../ajax_function/cutter_apply_quantity_check.php',{
				quantity:quantity,
				cutterid:cutterid
			},function(data,textstatus){
				if(data == 0){
					alert('申领数量异常！');
					$("#quantity-"+cutterid).val(default_quantity);
				}
			})
		}
	})
	$("input[name^=mould_number]").keyup(function(){
		var mould_number = $(this).val();
		if($.trim(mould_number)){
			var array_id = $(this).attr('id').split('-');
			var cutterid = array_id[1];
			$.post('../ajax_function/mould_try.php',{
				mould_number:mould_number
			},function(data,textstatus){
				$("#mouldid-"+cutterid).show();
				$("#mouldid-"+cutterid).html(data);
			})
		}else{
			$("#mouldid-"+cutterid).hide();
			$("#mouldid-"+cutterid).val('');
		}
	})
	$("select[id^=mouldid]").dblclick(function(){
		var array_id = $(this).attr('id').split('-');
		var cutterid = array_id[1];
		var mould_number = $("#mouldid-"+cutterid+" option:selected").text();
		var mouldid = $("#mouldid-"+cutterid+" option:selected").val();
		if(mouldid){
			$("#mould_number-"+cutterid).val(mould_number);
			$(this).hide();			
		}
	})
})
</script>
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result_apply->num_rows){
	$array_apply = $result_apply->fetch_assoc();
	//类型
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
	//品牌
	$sql_cutter_brand = "SELECT `brandid`,`brand` FROM `db_cutter_brand` ORDER BY `brandid` ASC";
	$result_cutter_brand = $db->query($sql_cutter_brand);
?>
<div id="table_sheet">
  <h4>刀具申领单</h4>
  <table>
    <tr>
      <th width="10%">申领单号：</th>
      <td width="15%"><?php echo $array_apply['apply_number']; ?></td>
      <th width="10%">申领人：</th>
      <td width="15%"><?php echo $array_apply['employee_name']; ?></td>
      <th width="10%">申请日期：</th>
      <td width="15%"><?php echo $array_apply['apply_date']; ?></td>
      <th width="10%">操作时间：</th>
      <td width="15%"><?php echo $array_apply['apply_time']; ?></td>
    </tr>
  </table>
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
$sql_cutter_list = "SELECT `db_cutter_purchase_list`.`cutterid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` WHERE `db_cutter_order_list`.`surplus` > 0 $sqlwhere GROUP BY `db_cutter_purchase_list`.`cutterid`";
$result_cutter_list = $db->query($sql_cutter_list);
$pages = new page($result_cutter_list->num_rows,15);
$sql_cutter_list = $sql_cutter_list . " ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_hardness`.`texture` ASC,`db_mould_cutter`.`cutterid` DESC" . $pages->limitsql;
$result_cutter_list = $db->query($sql_cutter_list);
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
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
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
          <input type="button" name="button" value="明细" class="button" onclick="location.href='cutter_apply_list.php?applyid=<?php echo $applyid; ?>'" />
          <input type="hidden" name="applyid" value="<?php echo $applyid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_cutter_list->num_rows){ ?>
  <form action="cutter_apply_list_adddo.php" name="cutter_apply_list_add" method="post">
    <table>
      <tr>
        <th width="10%">类型</th>
        <th width="14%">规格</th>
        <th width="14%">材质</th>
        <th width="12%">硬度</th>
        <th width="6%">库存</th>
        <th width="6%">申领数量</th>
        <th width="4%">单位</th>
        <th width="10%">模具编号(必填)</th>
        <th width="8%">计划申领日期</th>
        <th width="16%">备注</th>
      </tr>
      <?php
	  while($row_cutter_list = $result_cutter_list->fetch_assoc()){
		  $cutterid = $row_cutter_list['cutterid'];
	  ?>
      <tr>
        <td><?php echo $row_cutter_list['type']; ?></td>
        <td><?php echo $row_cutter_list['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row_cutter_list['texture']]; ?></td>
        <td><?php echo $row_cutter_list['hardness']; ?></td>
        <td><?php echo $row_cutter_list['surplus']; ?></td>
        <td><input type="text" name="quantity[]" id="quantity-<?php echo $cutterid; ?>" value="<?php echo $row_cutter_list['surplus']; ?>" class="input_txt" size="8" /></td>
        <td>件</td>
        <td><input type="text" name="mould_number[]" id="mould_number-<?php echo $cutterid; ?>" class="input_txt" size="15" autocomplete="off" />
          <br />
          <select name="mouldid[]" id="mouldid-<?php echo $cutterid; ?>" size="4" style="width:110px; border:1px solid #DDD; position:absolute; display:none; margin-left:8px;">
          <option value="" selected="selected"></option>
          </select></td>
        <td><input type="text" name="plan_date[]" value="<?php echo date('Y-m-d',strtotime("+1 day")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
        <td><input type="text" name="remark[]" class="input_txt" size="26" />
          <input type="hidden" name="cutterid[]" value="<?php echo $cutterid; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="10"><input type="submit" name="submit" value="添加" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='cutter_apply.php'" />
          <input type="hidden" name="applyid" value="<?php echo $applyid; ?>" /></td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>