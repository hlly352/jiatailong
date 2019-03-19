<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//查询组别
$sql_workteam = "SELECT `workteamid`,`workteam_name` FROM `db_mould_workteam` ORDER BY `workteamid` ASC";
$result_workteam = $db->query($sql_workteam);
//外发类型
$sql_outward_type = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` ORDER BY `outward_typeid` ASC";
$result_outward_type = $db->query($sql_outward_type);
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
	$(function (){
		$("#submit").click(function() {
			var mouldid = $("#mouldid").val();
			if (!mouldid) {
				$("#mould_number").focus();
				return false;
			}
			var part_number = $("#part_number").val();
			if(!$.trim(part_number)){
				$("#part_number").focus();
				return false;
			}
			var workteamid = $("#workteamid").val();
			if(!workteamid){
				$("#workteamid").focus();
				return false;
			}
			var order_number = $("#order_number").val();
			if(!$.trim(order_number)){
				$("#order_number").focus();
				return false;
			}else if(order_number.length != 7){
				$("#order_number").focus();
				return false;
			}
			var quantity = $("#quantity").val();
			if(!ri_b.test($.trim(quantity))){
				$("#quantity").focus();
				return false;
			}
			var outward_typeid = $("#outward_typeid").val();
			if(!outward_typeid){
				$("#outward_typeid").focus();
				return false;
			}
			var applyer = $("#applyer").val();
			if(!$.trim(applyer)){
				$("#applyer").focus();
				return false;
			}
		});
		$("#mould_number").keyup(function() {
			var mould_number = $(this).val();
			if ($.trim(mould_number)) {
				$.post('../ajax_function/mould_number.php', {
					mould_number: mould_number
				}, function(data, textStatus) {
					if (data) {
						$("#mouldid").html(data);
						$("#mouldid").show();
					}
				});
			} else {
				$("#mouldid").html('');
				$("#mouldid").hide();
			}
		});
		$("#mouldid").click(function() {
			var mouldid = $("#mouldid option:selected").val();
			if (mouldid) {
				var mould_number = $("#mouldid option:selected").text();
				$("#mould_number").val(mould_number);
			} else {
				$("#mould_number").val('');
			}
			$("#mouldid").hide();
		});
	})
</script>
<title>信息共享-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
	<h4>外协加工添加</h4>
	<form action="mould_outwarddo.php" name="mould_outward" method="post">
		<table>
			<tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><input type="text" name="mould_number" id="mould_number" class="input_txt">
        	<br />
        	<select name="mouldid" id="mouldid" size="6" style="width: 149px; position: absolute; display: none;">
        	</select></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><textarea name="part_number" cols="50" rows="3" class="input_txt" id="part_number"></textarea></td>
      </tr>
      <tr>
        <th>外协日期：</th>
        <td><input type="text" name="order_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>申请组别：</th>
        <td><select name="workteamid" id="workteamid">
            <option value="">请选择</option>
            <?php
            if($result_workteam->num_rows){
            	while($row_workteam = $result_workteam->fetch_assoc()){
            		echo "<option value=\"".$row_workteam['workteamid']."\">".$row_workteam['workteam_name']."</option>";
            	}
            }
            ?>
          </select></td>
      </tr>
      <tr>
        <th>外协单号：</th>
        <td><input type="text" name="order_number" id="order_number" class="input_txt" />
          <span class="tag"> *长度7位</span></td>
      </tr>
      <tr>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" class="input_txt" /></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="outward_typeid" id="outward_typeid">
            <option value="">请选择</option>
            <?php
            if($result_outward_type->num_rows){
            	while($row_outward_type = $result_outward_type->fetch_assoc()){
            		echo "<option value=\"".$row_outward_type['outward_typeid']."\">".$row_outward_type['outward_typename']."</option>";
            	}
            }
            ?>
          </select></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><input type="text" name="applyer" id="applyer" class="input_txt" /></td>
      </tr>
      <tr>
        <th>计划回厂：</th>
        <td><input type="text" name="plan_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
      	<th>&nbsp;</th>
      	<td><input type="submit" name="submit" id="submit" value="确定" class="button">
      		<input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);"></td>
      </tr>
		</table>
	</form>
</div>
<?php include "../footer.php"; ?>
</body>
</html>