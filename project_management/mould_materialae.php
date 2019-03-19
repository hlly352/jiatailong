<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var material_list_number = $("#material_list_number").val();
		if(!$.trim(material_list_number)){
			$("#material_list_number").focus();
			return false;
		}
		var material_list_sn = $("#material_list_sn").val();
		if(!$.trim(material_list_sn)){
			$("#material_list_sn").focus();
			return false;
		}
		var material_number = $("#material_number").val();
		if(!ri_a.test(material_number.substr(0,1))){
			$("#material_number").focus();
			return false;
		}
		var material_name = $("#material_name").val();
		if(!$.trim(material_name)){
			$("#material_name").focus();
			return false;
		}
		var specification = $("#specification").val();
		if(!$.trim(specification)){
			$("#specification").focus();
			return false;
		}
		if(material_number.substr(0,1) == 9 && !rf_b.test(specification)){
			$("#specification").focus();
			return false;
		}
		var material_quantity = $("#material_quantity").val();
		if(!rf_b.test(material_quantity)){
			$("#material_quantity").focus();
			return false;
		}
		var texture = $("#texture").val();
		if(!$.trim(texture)){
			$("#texture").focus();
			return false;
		}
	})
	$("#material_number").blur(function(){
		var material_number = $(this).val();
		if($.trim(material_number) == '900'){
			$("#material_name").val('红铜');
			$("#texture").val('红铜');
		}else{
			$("#material_name").val('');
			$("#texture").val('');
		}
	})
})
</script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $mouldid = fun_check_int($_GET['id']);
	  $sql_mould = "SELECT `mould_number` FROM `db_mould` WHERE `mouldid` = '$mouldid'";
	  $result_mould = $db->query($sql_mould);
	  if($result_mould->num_rows){
		  $array_mould = $result_mould->fetch_assoc();
  ?>
  <h4>物料添加</h4>
  <form action="mould_materialdo.php" name="mould_material" method="post">
    <table>
      <tr>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array_mould['mould_number']; ?></td>
        <th width="10%">下单日期：</th>
        <td width="15%"><?php echo date('Y-m-d'); ?></td>
        <th width="10%">料单编号：</th>
        <td width="15%"><input type="text" name="material_list_number" id="material_list_number" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th width="10%">料单序号：</th>
        <td width="15%"><input type="text" name="material_list_sn" id="material_list_sn" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>物料编码：</th>
        <td><input type="text" name="material_number" id="material_number" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" id="material_name" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>规格：</th>
        <td><input type="text" name="specification" id="specification" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>数量：</th>
        <td><input type="text" name="material_quantity" id="material_quantity" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>材质：</th>
        <td><input type="text" name="texture" id="texture" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>硬度：</th>
        <td><input type="text" name="hardness" class="input_txt" /></td>
        <th>品牌：</th>
        <td><input type="text" name="brand" class="input_txt" /></td>
        <th>备件数量：</th>
        <td><input type="text" name="spare_quantity" value="0" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td colspan="7"><input type="text" name="remark" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mouldid" value="<?php echo $mouldid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }elseif($action == "edit"){
	  $materialid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould`.`mould_number` FROM `db_mould_material` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`materialid` = '$materialid' AND `db_mould_material`.`materialid` NOT IN (SELECT `materialid` FROM `db_material_order_list` GROUP BY `materialid`)";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>物料修改</h4>
  <form action="mould_materialdo.php" name="mould_material" method="post">
    <table>
      <tr>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">下单日期：</th>
        <td width="15%"><?php echo date('Y-m-d'); ?></td>
        <th width="10%">料单编号：</th>
        <td width="15%"><input type="text" name="material_list_number" id="material_list_number" value="<?php echo $array['material_list_number']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th width="10%">料单序号：</th>
        <td width="15%"><input type="text" name="material_list_sn" id="material_list_sn" value="<?php echo $array['material_list_sn']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>物料编码：</th>
        <td><input type="text" name="material_number" id="material_number" value="<?php echo $array['material_number']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" id="material_name" value="<?php echo $array['material_name']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>规格：</th>
        <td><input type="text" name="specification" id="specification" value="<?php echo $array['specification']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>数量：</th>
        <td><input type="text" name="material_quantity" id="material_quantity" value="<?php echo $array['material_quantity']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>材质：</th>
        <td><input type="text" name="texture" id="texture" value="<?php echo $array['texture']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
        <th>硬度：</th>
        <td><input type="text" name="hardness" value="<?php echo $array['hardness']; ?>" class="input_txt" /></td>
        <th>品牌：</th>
        <td><input type="text" name="brand" value="<?php echo $array['brand']; ?>" class="input_txt" /></td>
        <th>备件数量：</th>
        <td><input type="text" name="spare_quantity" value="<?php echo $array['spare_quantity']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td colspan="7"><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="materialid" value="<?php echo $materialid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
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