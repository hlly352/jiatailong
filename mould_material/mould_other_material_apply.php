<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$array_system_shell = $_SESSION['system_shell'][$system_dir];
$action = fun_check_action($_GET['action']);
//查询组别
$sql_workteam = "SELECT `workteamid`,`workteam_name` FROM `db_mould_workteam` ORDER BY `workteamid` ASC";
$result_workteam = $db->query($sql_workteam);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
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
$(function(){
	$("#submit").click(function(){
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
		var supplierid = $("#supplierid").val();
		if(!supplierid){
			$("#supplierid").focus();
			return false;
		}
		var outward_typeid = $("#outward_typeid").val();
		if(!outward_typeid){
			$("#outward_typeid").focus();
			return false;
		}
		var cost = $("#cost").val();
		if(!rf_a.test($.trim(cost))){
			$("#cost").focus();
			return false;
		}
		var applyer = $("#applyer").val();
		if(!$.trim(applyer)){
			$("#applyer").focus();
			return false;
		}
		var inout_status = $("#inout_status").val();
		if(!$.trim(inout_status)){
			$("#inout_status").focus();
			return false;
		}
		var actual_date = $("#actual_date").val();
		if(inout_status == 1 && actual_date == '0000-00-00'){
			alert('请选择实际回厂时间');
			return false;
		}
	})
	$("#inout_status").change(function(){
		var inout_status = $(this).val();
		if(inout_status == 1){
			$("#actual_date").attr('disabled',false);
		}else if(inout_status == 0){
			$("#actual_date").attr('disabled',true);
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
  if($action == "add"){
	  if(isset($_GET['id'])){
		  $mouldid = $_GET['id'];
		  $sql_mould = "SELECT `mouldid`,`mould_number` FROM `db_mould` WHERE `mouldid` = '$mouldid'";
		  $result_mould = $db->query($sql_mould);
		  if($result_mould->num_rows){
			  $array_mould = $result_mould->fetch_assoc();
			  $mouldid = $array_mould['mouldid'];
			  $mould_number = $array_mould['mould_number'];
		  }else{
			  $mouldid = 0;
			  $mould_number = '--';
		  }
	  }else{
		  $mouldid = 0;
		  $mould_number = '--';
	  }
  ?>
  <h4>期间物料申请</h4>
  <form action="mould_outwarddo.php" name="mould_outward" method="post">
    <table>
      <tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><?php echo $mould_number; ?></td>
      </tr>
      <tr>
        <th>物料名称：</th>
        <td><input type="text" name=""></td>
      </tr>
      <tr>
        <th>物料：</th>
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
        <th>申请日期：</th>
        <td><input type="text" name="order_number" id="order_number" class="input_txt" />
          <span class="tag"> *长度7位</span></td>
      </tr>
      <tr>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" class="input_txt" /></td>
      </tr>
      <tr>
        <th>申请人：</th>
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
      </tr>
      <tr>
        <th>计划回厂时间：</th>
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
        <th>备注：</th>
        <td><input type="text" name="remark" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mouldid" value="<?php echo $mouldid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $outwardid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`workteamid`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`outward_typeid`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`remark`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`supplierid`,`db_mould`.`mould_number` FROM `db_mould_outward` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` WHERE `db_mould_outward`.`outwardid` = '$outwardid'";
	  $result = $db->query($sql);
	  if($result->num_rows && $array_system_shell['isconfirm']){
		  $array = $result->fetch_assoc();
		  $mould_number = $array['mouldid']?$array['mould_number']:'--';
		  $inout_status = $array['inout_status'];
		  $sql_pay = "SELECT * FROM `db_cash_pay` WHERE `linkid` = '$outwardid' AND `data_type` = 'MO'";
		  $result_pay = $db->query($sql_pay);
  ?>
  <h4>外协加工修改</h4>
  <form action="mould_outwarddo.php" name="mould_outward" method="post">
    <table>
      <tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><?php echo $mould_number; ?></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><textarea name="part_number" cols="50" rows="3" class="input_txt" id="part_number"><?php echo $array['part_number']; ?></textarea></td>
      </tr>
      <tr>
        <th>外协时间：</th>
        <td><input type="text" name="order_date" value="<?php echo $array['order_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>申请组别：</th>
        <td><select name="workteamid" id="workteamid">
            <option value="">请选择</option>
            <?php
            if($result_workteam->num_rows){
				while($row_workteam = $result_workteam->fetch_assoc()){
			?>
            <option value="<?php echo $row_workteam['workteamid']; ?>"<?php if($row_workteam['workteamid'] == $array['workteamid']) echo " selected=\"selected\""; ?>><?php echo $row_workteam['workteam_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>外协单号：</th>
        <td><input type="text" name="order_number" id="order_number" value="<?php echo $array['order_number']; ?>" class="input_txt" />
        <span class="tag"> *长度7位</span></td>
      </tr>
      <tr>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array['quantity']; ?>" class="input_txt" /></td>
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
      </tr>
      <tr>
        <th>类型：</th>
        <td><select name="outward_typeid" id="outward_typeid">
            <option value="">请选择</option>
            <?php
            if($result_outward_type->num_rows){
				while($row_outward_type = $result_outward_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_outward_type['outward_typeid']; ?>"<?php if($row_outward_type['outward_typeid'] == $array['outward_typeid']) echo " selected=\"selected\""; ?>><?php echo $row_outward_type['outward_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>金额(元)：</th>
        <td><input type="text" name="cost" id="cost" value="<?php echo $array['cost']; ?>" class="input_txt"<?php if($result_pay->num_rows) echo " readonly=\"readonly\""; ?> /></td>
      </tr>
      <tr>
        <th>现金：</th>
        <td><select name="iscash">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $array['iscash']) echo " selected=\"selected\""; ?><?php if($result_pay->num_rows && $is_status_key == 0) echo " disabled=\"disabled\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><input type="text" name="applyer" id="applyer" value="<?php echo $array['applyer']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>计划回厂：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>进度状态：</th>
        <td><select name="inout_status" id="inout_status">
            <?php foreach($array_mould_inout_status as $inout_status_key=>$inout_status_value){ ?>
            <option value="<?php echo $inout_status_key; ?>"<?php if($inout_status_key == $inout_status) echo " selected=\"selected\""; ?>><?php echo $inout_status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>实际回厂：</th>
        <td><input type="text" name="actual_date" id="actual_date" value="<?php echo $array['actual_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"<?php if($inout_status == 0) echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
      <tr>
        <th>状态：</th>
        <td><select name="outward_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['outward_status']) echo " selected=\"selected\""; ?><?php if($result_pay->num_rows && $status_key == 0) echo " disabled=\"disabled\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="outwardid" value="<?php echo $outwardid; ?>" />
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