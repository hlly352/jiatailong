<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查询系统确认人
$systemid = array_search($system_dir,$_SESSION['system_dir']);
$sql_confirm = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_system_employee` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_system_employee`.`employeeid` WHERE `db_system_employee`.`systemid` = '$systemid' AND `db_system_employee`.`isconfirm` = 1 ORDER BY `db_employee`.`employeeid` ASC";
$result_confirm = $db->query($sql_confirm);
//查询试模原因
$sql_try_cause = "SELECT `try_causeid`,`try_causename` FROM `db_mould_try_cause` ORDER BY `try_causeid` ASC";
$result_try_cause = $db->query($sql_try_cause);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var mould_size = $("#mould_size").val();
		if(!$.trim(mould_size)){
			$("#mould_size").focus();
			return false;
		}
		var try_times = $("#try_times").val();
		if(!try_times){
			$("#try_times").focus();
			return false;
		}
		var try_causeid = $("#try_causeid").val();
		if(!try_causeid){
			$("#try_causeid").focus();
			return false;
		}
		var tonnage = $("#tonnage").val();
		if(!ri_b.test(tonnage)){
			$("#tonnage").focus();
			return false;
		}
		var molding_cycle = $("#molding_cycle").val();
		if(!rf_a.test(molding_cycle)){
			$("#molding_cycle").focus();
			return false;
		}
		var plastic_material_color = $("#plastic_material_color").val();
		if(!$.trim(plastic_material_color)){
			$("#plastic_material_color").focus();
			return false;
		}
		var plastic_material_offer = $("#plastic_material_offer").val();
		if(!$.trim(plastic_material_offer)){
			$("#plastic_material_offer").focus();
			return false;
		}
		var product_weight = $("#product_weight").val();
		if(!ri_b.test(product_weight)){
			$("#product_weight").focus();
			return false;
		}
		var product_quantity = $("#product_quantity").val();
		if(!ri_b.test(product_quantity)){
			$("#product_quantity").focus();
			return false;
		}
		var material_weight = $("#material_weight").val();
		if(!ri_b.test(material_weight)){
			$("#material_weight").focus();
			return false;
		}
		var approver = $("#approver").val();
		if(!approver){
			$("#approver").focus();
			return false;
		}
	})
})
</script>
<title>模具加工-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $mouldid = fun_check_int($_GET['id']);
	  //查询是否有最大值
	  $sql = "SELECT `db_mould_try`.`mould_size`,`db_mould_try`.`plan_date`,`db_mould_try`.`try_times`,`db_mould_try`.`try_causeid`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`approver`,`db_mould_try`.`remark`,`db_mould`.`mould_number`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `tryid` = (SELECT MAX(`tryid`) FROM `db_mould_try` WHERE `mouldid` = '$mouldid' AND `try_status` = 1)";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
   ?>
  <h4>模具试模申请</h4>
  <form action="mould_try_applydo.php" name="mould_try_apply" method="post">
    <table>
      <tr>
        <th width="10%">客户代码：</th>
        <td width="15%"><?php echo $array['client_code']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array['project_name']; ?></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">模具尺寸：</th>
        <td width="15%"><input type="text" name="mould_size" id="mould_size" value="<?php echo $array['mould_size']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>穴数：</th>
        <td><?php echo $array['cavity_number']; ?></td>
        <th>难度系数：</th>
        <td><?php echo $array['difficulty_degree']; ?></td>
        <th>试模次数：</th>
        <td><select name="try_times" id="try_times">
            <option value="">请选择</option>
            <?php for($i=0;$i<11;$i++){ ?>
            <option value="<?php echo $i; ?>"<?php if($i == $array['try_times']) echo " selected=\"selected\""; ?>>T<?php echo $i; ?></option>
            <?php } ?>
          </select></td>
        <th>试模原因：</th>
        <td><select name="try_causeid" id="try_causeid">
            <option value="">请选择</option>
            <?php
			if($result_try_cause->num_rows){
				while($row_type_cause = $result_try_cause->fetch_assoc()){
			?>
            <option value="<?php echo $row_type_cause['try_causeid']; ?>"<?php if($row_type_cause['try_causeid'] == $array['try_causeid']) echo " selected=\"selected\""; ?>><?php echo $row_type_cause['try_causename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>啤机吨位(T)：</th>
        <td><input type="text" name="tonnage" id="tonnage" value="<?php echo $array['tonnage']; ?>" class="input_txt" /></td>
        <th>成型周期(S)：</th>
        <td><input type="text" name="molding_cycle" id="molding_cycle" value="<?php echo $array['molding_cycle']; ?>" class="input_txt" /></td>
        <th>胶料品种：</th>
        <td><?php echo $array['plastic_material']; ?></td>
        <th>胶料颜色：</th>
        <td><input type="text" name="plastic_material_color" id="plastic_material_color" value="<?php echo $array['plastic_material_color']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>胶料来源：</th>
        <td><input type="text" name="plastic_material_offer" id="plastic_material_offer" value="<?php echo $array['plastic_material_offer']; ?>" class="input_txt" /></td>
        <th>产品单重(g)：</th>
        <td><input type="text" name="product_weight" id="product_weight" value="<?php echo $array['product_weight']; ?>" class="input_txt" /></td>
        <th>样板啤数(啤)：</th>
        <td><input type="text" name="product_quantity" id="product_quantity" value="<?php echo $array['product_quantity']; ?>" class="input_txt" /></td>
        <th>需要用料(kg)：</th>
        <td><input type="text" name="material_weight" id="material_weight" value="<?php echo $array['material_weight']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>要求日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>钳工组别：</th>
        <td><?php echo $array_mould_assembler[$array['assembler']]; ?></td>
        <th>审批人：</th>
        <td><select name="approver" id="approver">
            <option value="">请选择</option>
            <?php
			if($result_confirm->num_rows){
				while($row_confirm = $result_confirm->fetch_assoc()){
			?>
            <option value="<?php echo $row_confirm['employeeid']; ?>"<?php if($row_confirm['employeeid'] == $array['approver']) echo " selected=\"selected\""; ?>><?php echo $row_confirm['employee_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
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
		  //查询模具
		  $sql_mould = "SELECT `db_mould`.`mould_number`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `db_mould`.`mouldid` = '$mouldid'";
		  $result_mould = $db->query($sql_mould);
		  if($result_mould->num_rows){
			  $array_mould = $result_mould->fetch_assoc();
  ?>
  <h4>模具试模申请</h4>
  <form action="mould_try_applydo.php" name="mould_try_apply" method="post">
    <table>
      <tr>
        <th width="10%">客户代码：</th>
        <td width="15%"><?php echo $array_mould['client_code']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array_mould['project_name']; ?></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array_mould['mould_number']; ?></td>
        <th width="10%">模具尺寸：</th>
        <td width="15%"><input type="text" name="mould_size" id="mould_size" class="input_txt" /></td>
      </tr>
      <tr>
        <th>穴数：</th>
        <td><?php echo $array_mould['cavity_number']; ?></td>
        <th>难度系数：</th>
        <td><?php echo $array_mould['difficulty_degree']; ?></td>
        <th>试模次数：</th>
        <td><select name="try_times" id="try_times">
            <option value="">请选择</option>
            <?php
			for($i=0;$i<11;$i++){
				echo "<option value=\"".$i."\">T".$i."</option>";
			}
			?>
          </select></td>
        <th>试模原因：</th>
        <td><select name="try_causeid" id="try_causeid">
            <option value="">请选择</option>
            <?php
			if($result_try_cause->num_rows){
				while($row_type_cause = $result_try_cause->fetch_assoc()){
					echo "<option value=\"".$row_type_cause['try_causeid']."\">".$row_type_cause['try_causename']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>啤机吨位(T)：</th>
        <td><input type="text" name="tonnage" id="tonnage" class="input_txt" /></td>
        <th>成型周期(S)：</th>
        <td><input type="text" name="molding_cycle" id="molding_cycle" class="input_txt" /></td>
        <th>胶料品种：</th>
        <td><?php echo $array_mould['plastic_material']; ?></td>
        <th>胶料颜色：</th>
        <td><input type="text" name="plastic_material_color" id="plastic_material_color" class="input_txt" /></td>
      </tr>
      <tr>
        <th>胶料来源：</th>
        <td><input type="text" name="plastic_material_offer" id="plastic_material_offer" class="input_txt" /></td>
        <th>产品单重(g)：</th>
        <td><input type="text" name="product_weight" id="product_weight" class="input_txt" /></td>
        <th>样板啤数(啤)：</th>
        <td><input type="text" name="product_quantity" id="product_quantity" class="input_txt" /></td>
        <th>需要用料(kg)：</th>
        <td><input type="text" name="material_weight" id="material_weight" class="input_txt" /></td>
      </tr>
      <tr>
        <th>要求日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>钳工组别：</th>
        <td><?php echo $array_mould_assembler[$array_mould['assembler']]; ?></td>
        <th>审批人：</th>
        <td><select name="approver" id="approver">
            <option value="">请选择</option>
            <?php
			if($result_confirm->num_rows){
				while($row_confirm = $result_confirm->fetch_assoc()){
					echo "<option value=\"".$row_confirm['employeeid']."\">".$row_confirm['employee_name']."</option>";
				}
			}
			?>
          </select></td>
        <th>备注：</th>
        <td><input type="text" name="remark" class="input_txt" /></td>
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
	  }
  }elseif($action == "edit"){
	  $tryid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_mould_try`.`mould_size`,`db_mould_try`.`plan_date`,`db_mould_try`.`try_times`,`db_mould_try`.`try_causeid`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`approver`,`db_mould_try`.`remark`,`db_mould`.`mould_number`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `db_mould_try`.`tryid` = '$tryid' AND `db_mould_try`.`approve_status` = 'C' AND `db_mould_try`.`try_status` = 1 AND `db_mould_try`.`employeeid` = '$employeeid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>模具试模申请修改</h4>
  <form action="mould_try_applydo.php" name="mould_try_apply" method="post">
    <table>
      <tr>
        <th width="10%">客户代码：</th>
        <td width="15%"><?php echo $array['client_code']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array['project_name']; ?></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">模具尺寸：</th>
        <td width="15%"><input type="text" name="mould_size" id="mould_size" value="<?php echo $array['mould_size']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>穴数：</th>
        <td><?php echo $array['cavity_number']; ?></td>
        <th>难度系数：</th>
        <td><?php echo $array['difficulty_degree']; ?></td>
        <th>试模次数：</th>
        <td><select name="try_times" id="try_times">
            <option value="">请选择</option>
            <?php for($i=0;$i<11;$i++){ ?>
            <option value="<?php echo $i; ?>"<?php if($i == $array['try_times']) echo " selected=\"selected\""; ?>>T<?php echo $i; ?></option>
            <?php } ?>
          </select></td>
        <th>试模原因：</th>
        <td><select name="try_causeid" id="try_causeid">
            <option value="">请选择</option>
            <?php
			if($result_try_cause->num_rows){
				while($row_type_cause = $result_try_cause->fetch_assoc()){
			?>
            <option value="<?php echo $row_type_cause['try_causeid']; ?>"<?php if($row_type_cause['try_causeid'] == $array['try_causeid']) echo " selected=\"selected\""; ?>><?php echo $row_type_cause['try_causename']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>啤机吨位(T)：</th>
        <td><input type="text" name="tonnage" id="tonnage" value="<?php echo $array['tonnage']; ?>" class="input_txt" /></td>
        <th>成型周期(S)：</th>
        <td><input type="text" name="molding_cycle" id="molding_cycle" value="<?php echo $array['molding_cycle']; ?>" class="input_txt" /></td>
        <th>胶料品种：</th>
        <td><?php echo $array['plastic_material']; ?></td>
        <th>胶料颜色：</th>
        <td><input type="text" name="plastic_material_color" id="plastic_material_color" value="<?php echo $array['plastic_material_color']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>胶料来源：</th>
        <td><input type="text" name="plastic_material_offer" id="plastic_material_offer" value="<?php echo $array['plastic_material_offer']; ?>" class="input_txt" /></td>
        <th>产品单重(g)：</th>
        <td><input type="text" name="product_weight" id="product_weight" value="<?php echo $array['product_weight']; ?>" class="input_txt" /></td>
        <th>样板啤数(啤)：</th>
        <td><input type="text" name="product_quantity" id="product_quantity" value="<?php echo $array['product_quantity']; ?>" class="input_txt" /></td>
        <th>需要用料(kg)：</th>
        <td><input type="text" name="material_weight" id="material_weight" value="<?php echo $array['material_weight']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>要求日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>钳工组别：</th>
        <td><?php echo $array_mould_assembler[$array['assembler']]; ?></td>
        <th>审批人：</th>
        <td><select name="approver" id="approver">
            <option value="">请选择</option>
            <?php
			if($result_confirm->num_rows){
				while($row_confirm = $result_confirm->fetch_assoc()){
			?>
            <option value="<?php echo $row_confirm['employeeid']; ?>"<?php if($row_confirm['employeeid'] == $array['approver']) echo " selected=\"selected\""; ?>><?php echo $row_confirm['employee_name']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="tryid" value="<?php echo $tryid; ?>" />
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