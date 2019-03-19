<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询组别
$sql_workteam = "SELECT `workteamid`,`workteam_name` FROM `db_mould_workteam` ORDER BY `workteamid` ASC";
$result_workteam = $db->query($sql_workteam);
//查询责任组别
$sql_team = "SELECT `teamid`,`team_name` FROM `db_responsibility_team` ORDER BY `teamid` ASC";
$result_team = $db->query($sql_team);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname`FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
//焊接类型
$sql_weld_type = "SELECT `weld_typeid`,`weld_typename` FROM `db_mould_weld_type` ORDER BY `weld_typeid` ASC";
$result_weld_type = $db->query($sql_weld_type);
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
		}
		var quantity = $("#quantity").val();
		if(!ri_b.test($.trim(quantity))){
			$("#quantity").focus();
			return false;
		}
		var weld_cause = $("#weld_cause").val();
		if(!$.trim(weld_cause)){
			$("#weld_cause").focus();
			return false;
		}
		var teamid = $("#teamid").val();
		if(!teamid){
			$("#teamid").focus();
			return false;
		}
		var supplierid = $("#supplierid").val();
		if(!supplierid){
			$("#supplierid").focus();
			return false;
		}
		var weld_typeid = $("#weld_typeid").val();
		if(!weld_typeid){
			$("#weld_typeid").focus();
			return false;
		}
		var cost = $("#cost").val();
		if(!ri_b.test($.trim(cost))){
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
  <h4>零件烧焊添加</h4>
  <form action="mould_welddo.php" name="mould_weld" method="post">
    <table>
      <tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><?php echo $mould_number; ?></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><input type="text" name="part_number" id="part_number" class="input_txt" size="30" /></td>
      </tr>
      <tr>
        <th>外发时间：</th>
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
        <td><input type="text" name="order_number" id="order_number" class="input_txt" /></td>
      </tr>
      <tr>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" class="input_txt" /></td>
      </tr>
      <tr>
        <th>烧焊原因：</th>
        <td><input type="text" name="weld_cause" id="weld_cause" class="input_txt" size="30" /></td>
      </tr>
      <tr>
        <th>责任组别：</th>
        <td><select name="teamid" id="teamid">
            <option value="">请选择</option>
            <?php
            if($result_team->num_rows){
				while($row_team = $result_team->fetch_assoc()){
					echo "<option value=\"".$row_team['teamid']."\">".$row_team['team_name']."</option>";
				}
			}
			?>
          </select></td>
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
      </tr>
      <tr>
        <th>加工类型：</th>
        <td><select name="weld_typeid" id="weld_typeid">
            <option value="">请选择</option>
            <?php
            if($result_weld_type->num_rows){
				while($row_weld_type = $result_weld_type->fetch_assoc()){
					echo "<option value=\"".$row_weld_type['weld_typeid']."\">".$row_weld_type['weld_typename']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>金额(元)：</th>
        <td><input type="text" name="cost" id="cost" class="input_txt" /></td>
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
        <th>进度状态：</th>
        <td><select name="inout_status" id="inout_status">
            <option value="">请选择</option>
            <?php
			foreach($array_mould_inout_status as $inout_status_key=>$inout_status_value){
				echo "<option value=\"".$inout_status_key."\">".$inout_status_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>实际回厂：</th>
        <td><input type="text" name="actual_date" id="actual_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" disabled="disabled" /></td>
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
	  $weldid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_mould_weld`.`weldid`,`db_mould_weld`.`mouldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`workteamid`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`teamid`,`db_mould_weld`.`weld_typeid`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`actual_date`,`db_mould_weld`.`remark`,`db_mould_weld`.`inout_status`,`db_mould_weld`.`weld_status`,`db_mould_weld`.`supplierid`,`db_mould`.`mould_number` FROM `db_mould_weld` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` WHERE `db_mould_weld`.`weldid` = '$weldid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $mould_number = $array['mouldid']?$array['mould_number']:'--';
		  $inout_status = $array['inout_status'];
  ?>
  <h4>零件烧焊修改</h4>
  <form action="mould_welddo.php" name="mould_weld" method="post">
    <table>
      <tr>
        <th width="20%">模具编号：</th>
        <td width="80%"><?php echo $mould_number; ?></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><input type="text" name="part_number" id="part_number" value="<?php echo $array['part_number']; ?>" class="input_txt" size="30" /></td>
      </tr>
      <tr>
        <th>外发时间：</th>
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
        <td><input type="text" name="order_number" id="order_number" value="<?php echo $array['order_number']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array['quantity']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>烧焊原因：</th>
        <td><input type="text" name="weld_cause" id="weld_cause" value="<?php echo $array['weld_cause']; ?>" class="input_txt" size="30" /></td>
      </tr>
      <tr>
        <th>责任组别：</th>
        <td><select name="teamid" id="teamid">
            <option value="">请选择</option>
            <?php
            if($result_team->num_rows){
				while($row_team = $result_team->fetch_assoc()){
			?>
            <option value="<?php echo $row_team['teamid']; ?>"<?php if($row_team['teamid'] == $array['teamid']) echo " selected=\"selected\""; ?>><?php echo $row_team['team_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
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
        <th>加工类型：</th>
        <td><select name="weld_typeid" id="weld_typeid">
            <option value="">请选择</option>
            <?php
            if($result_weld_type->num_rows){
				while($row_weld_type = $result_weld_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_weld_type['weld_typeid']; ?>"<?php if($row_weld_type['weld_typeid'] == $array['weld_typeid']) echo " selected=\"selected\""; ?>><?php echo $row_weld_type['weld_typename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>金额(元)：</th>
        <td><input type="text" name="cost" id="cost" value="<?php echo $array['cost']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><input type="text" name="applyer" id="applyer" value="<?php echo $array['applyer']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>计划回厂：</th>
        <td><input type="text" name="plan_date" value="<?php echo$array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
        <th>状态：</th>
        <td><select name="weld_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $array['weld_status']) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
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
          <input type="hidden" name="weldid" value="<?php echo $weldid; ?>" />
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