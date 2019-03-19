<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$sql_type = "SELECT * FROM `db_quote_item_type` ORDER BY `item_typeid` ASC";
$result_type = $db->query($sql_type);
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
		var item_sn = $("#item_sn").val();
		if(!$.trim(item_sn)){
			$("#item_sn").focus();
			return false;
		}
		var item_typeid = $("#item_typeid").val();
		if(!item_typeid){
			$("#item_typeid").focus();
			return false;
		}
		var item_name = $("#item_name").val();
		if(!$.trim(item_name)){
			$("#item_name").focus();
			return false;
		}
		var unit_price = $("#unit_price").val();
		if(!rf_a.test(unit_price)){
			$("#unit_price").focus();
			return false;
		}
		if(item_typeid == 7){
			var reg = /^((\d+\.?\d*)|(\d*\.\d+))\%$/;
			var array_f_item_sn = ['C','D','E'];
			var item_sn = $("#item_sn").val();
			var descripition = $("#descripition").val();
			if($.inArray(item_sn,array_f_item_sn) != -1 && !reg.test(descripition)){
				alert('请输入百分数');
				$("#descripition").focus();
				return false;
			}
		}
	})
	$("#item_sn").blur(function(){
		var reg_sn = /[A-Z]+$/;
		var item_sn = $(this).val();
		var item_sn_defaultvalue = this.defaultValue;
		if($.trim(item_sn) && !reg_sn.test(item_sn)){
			alert('请输入英文大写字母');
			$(this).val(item_sn_defaultvalue);
		}
	})
})
</script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>报价项目添加</h4>
  <form action="quote_itemdo.php" name="quote_item" method="post">
    <table>
      <tr>
        <th width="20%">序号：</th>
        <td width="80%"><input type="text" name="item_sn" id="item_sn" class="input_txt" /></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="item_typeid" id="item_typeid">
            <option value="">请选择</option>
            <?php
			if($result_type->num_rows){
				while($row_type = $result_type->fetch_assoc()){
					echo "<option value=\"".$row_type['item_typeid']."\">".$row_type['item_typename']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>名称：</th>
        <td><input type="text" name="item_name" id="item_name" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>规格型号/牌号：</th>
        <td><input type="text" name="specification" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="0.00" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注/说明：</th>
        <td><input type="text" name="descripition" id="descripition" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      <tr> </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $itemid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_quote_item` WHERE `itemid` = '$itemid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>报价项目修改</h4>
  <form action="quote_itemdo.php" name="quote_item" method="post">
    <table>
      <tr>
        <th width="20%">序号：</th>
        <td width="80%"><input type="text" name="item_sn" id="item_sn" value="<?php echo $array['item_sn']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="item_typeid" id="item_typeid">
            <option value="">请选择</option>
            <?php
			if($result_type->num_rows){
				while($row_type = $result_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_type['item_typeid']; ?>"<?php if($row_type['item_typeid'] == $array['item_typeid']) echo " selected=\"selected\""; ?>><?php echo $row_type['item_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>名称：</th>
        <td><input type="text" name="item_name" id="item_name" value="<?php echo $array['item_name']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>规格型号/牌号：</th>
        <td><input type="text" name="specification" value="<?php echo $array['specification']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注/说明：</th>
        <td><input type="text" name="descripition" id="descripition" value="<?php echo $array['descripition']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="itemid" value="<?php echo $itemid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      <tr> </tr>
    </table>
  </form>
  <?php
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>