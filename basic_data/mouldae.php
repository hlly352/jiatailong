<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询客户
$sql_client = "SELECT `clientid`,`client_code`,`client_cname` FROM `db_client` ORDER BY `client_code` ASC,`clientid` ASC";
$result_client = $db->query($sql_client);
//查询状态
$sql_mould_status = "SELECT `mould_statusid`,`mould_statusname` FROM `db_mould_status` ORDER BY `mould_statusid` ASC";
$result_mould_status = $db->query($sql_mould_status);
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
<script language="javascript">
$(function(){
	$("#submit").click(function(){
		var clientid = $("#clientid").val();
		if(!clientid){
			$("#clientid").focus();
			return false;
		}
		var project_name = $("#project_name").val();
		if(!$.trim(project_name)){
			$("#project_name").focus();
			return false;
		}
		var mould_number = $("#mould_number").val();
		if(!$.trim(mould_number)){
			$("#mould_number").focus();
			return false;
		}
		var part_name = $("#part_name").val();
		if(!$.trim(part_name)){
			$("#part_name").focus();
			return false;
		}
		var plastic_material = $("#plastic_material").val();
		if(!$.trim(plastic_material)){
			$("#plastic_material").focus();
			return false;
		}
		/*
		var shrinkage_rate = $("#shrinkage_rate").val();
		if(!rf_b.test($.trim(shrinkage_rate))){
			$("#shrinkage_rate").focus();
			return false;
		}
		var surface = $("#surface").val();
		if(!$.trim(surface)){
			$("#surface").focus();
			return false;
		}
		*/
		var cavity_number = $("#cavity_number").val();
		if(!$.trim(cavity_number)){
			$("#cavity_number").focus();
			return false;
		}
		var gate_type = $("#gate_type").val();
		if(!$.trim(gate_type)){
			$("#gate_type").focus();
			return false;
		}
		/*
		var core_material = $("#core_material").val();
		if(!$.trim(core_material)){
			$("#core_material").focus();
			return false;
		}
		*/
		var isexport = $("#isexport").val();
		if(!isexport){
			$("#isexport").focus();
			return false;
		}
		var quality_grade = $("#quality_grade").val();
		if(!quality_grade){
			$("#quality_grade").focus();
			return false;
		}
		var difficulty_degree = $("#difficulty_degree").val();
		if(!difficulty_degree){
			$("#difficulty_degree").focus();
			return false;
		}
		var mould_statusid = $("#mould_statusid").val();
		if(!mould_statusid){
			$("#mould_statusid").focus();
			return false;
		}
		/*
		var mould_status = $("#mould_status").val();
		if(!mould_status){
			$("#mould_status").focus();
			return false;
		}
		var select_projecter = $("#select_projecter").val();
		if(!select_projecter){
			$("#name_projecter").focus();
			return false;
		}
		var select_designer = $("#select_designer").val();
		if(!select_designer){
			$("#name_designer").focus();
			return false;
		}
		var select_steeler = $("#select_steeler").val();
		if(!select_steeler){
			$("#name_steeler").focus();
			return false;
		}
		var select_electroder = $("#select_electroder").val();
		if(!select_electroder){
			$("#name_electroder").focus();
			return false;
		}
		var assembler = $("#assembler").val();
		if(!assembler){
			$("#assembler").focus();
			return false;
		}
		*/
	})
	$("#mould_number").blur(function(){
		var mould_number = $(this).val();
		var mouldid = $("#mouldid").val();
		var action = $("#action").val();
		if($.trim(mould_number)){
			$.post("../ajax_function/mould_number_check.php",{
				   mould_number:mould_number,
				   mouldid:mouldid,
				   action:action
			},function(data,textStatus){
				if(data == 0){
					alert('模具编号重复，请重新输入！');
					$("#mould_number").val('');
				}
			})
		}
	})
	$("input[name^=name]").keyup(function(){
		var employee_name = $(this).val();
		var value_id = $(this).attr('id');
		var array_id = value_id.split('_');
		var select_id = array_id[1];
		if($.trim(employee_name)){
			$.post('../ajax_function/employee_name_all.php',{
				employee_name:employee_name
			},function(data,textstatus){
				$("#select_"+select_id).show();
				$("#select_"+select_id).html(data);
			})
		}else{
			$("#select_"+select_id).hide();
			$("#select_"+select_id).val('');
		}
	})
	$("select[id^=select]").dblclick(function(){
		var name_value = $(this).attr('name');
		var employee_name = $("#select_"+name_value+" option:selected").text();
		var employeeid = $("#select_"+name_value+" option:selected").val();
		if(employeeid != ''){
			$("#name_"+name_value).val(employee_name);
			$(this).hide();
		}
	})
})
</script>
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>模具添加</h4>
  <form action="moulddo.php" name="mould" method="post">
    <table>
      <tr>
        <th width="10%">客户代码：</th>
        <td width="15%"><select name="clientid" id="clientid">
            <option value="">请选择</option>
            <?php
			if($result_client->num_rows){
				while($row_client = $result_client->fetch_assoc()){
					echo "<option value=\"".$row_client['clientid']."\">".$row_client['client_code'].'-'.$row_client['client_cname']."</option>";
				}
			}
			?>
          </select></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><input type="text" name="project_name" id="project_name" class="input_txt" /></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><input type="text" name="mould_number" id="mould_number" class="input_txt" /></td>
        <th width="10%">零件名称/编号：</th>
        <td width="15%"><input type="text" name="part_name" id="part_name" class="input_txt" /></td>
      </tr>
      <tr>
        <th>塑胶材料：</th>
        <td><input type="text" name="plastic_material" id="plastic_material" class="input_txt" /></td>
        <th>缩水率：</th>
        <td><input type="text" name="shrinkage_rate" id="shrinkage_rate" class="input_txt" /></td>
        <th>表面要求：</th>
        <td><input type="text" name="surface" id="surface" class="input_txt" /></td>
        <th>模穴数：</th>
        <td><input type="text" name="cavity_number" id="cavity_number" class="input_txt" /></td>
      </tr>
      <tr>
        <th>浇口类型：</th>
        <td><input type="text" name="gate_type" id="gate_type" class="input_txt" /></td>
        <th>型腔/型芯材质：</th>
        <td><input type="text" name="core_material" id="core_material" class="input_txt" /></td>
        <th>是否出口：</th>
        <td><select name="isexport" id="isexport">
            <option value="">请选择</option>
            <?php
            foreach($array_is_status as $is_status_key=>$is_status_value){
				echo "<option value=\"".$is_status_key."\">".$is_status_value."</option>";
			}
			?>
          </select></td>
        <th>质量等级：</th>
        <td><select name="quality_grade" id="quality_grade">
            <option value="">请选择</option>
            <?php
            foreach($array_mould_quality_grade as $quality_grade_key=>$quality_grade_value){
				echo "<option value=\"".$quality_grade_value."\">".$quality_grade_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>难度系数：</th>
        <td><select name="difficulty_degree" id="difficulty_degree">
            <option value="">请选择</option>
            <?php
			for($i=0.5;$i<1.5;$i+=0.1){
				echo "<option value=\"".$i."\">".$i."</option>";
			}
			?>
          </select></td>
        <th>首板时间：</th>
        <td><input type="text" name="first_time"  onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>重点提示：</th>
        <td><input type="text" name="remark" class="input_txt" /></td>
        <th>目前状态：</th>
        <td><select name="mould_statusid" id="mould_statusid">
            <option value="">请选择</option>
            <?php
			if($result_mould_status->num_rows){
				while($row_mould_status = $result_mould_status->fetch_assoc()){
					echo "<option value=\"".$row_mould_status['mould_statusid']."\">".$row_mould_status['mould_statusname']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>项目：</th>
        <td><input type="text" name="name_projecter" id="name_projecter" class="input_txt" />
          <br />
          <select name="projecter" id="select_projecter" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value=""></option>
          </select></td>
        <th>设计：</th>
        <td><input type="text" name="name_designer" id="name_designer" class="input_txt" />
          <br />
          <select name="designer" id="select_designer" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value=""></option>
          </select></td>
        <th>钢料：</th>
        <td><input type="text" name="name_steeler" id="name_steeler" class="input_txt" />
          <br />
          <select name="steeler" id="select_steeler" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value=""></option>
          </select></td>
        <th>电极：</th>
        <td><input type="text" name="name_electroder" id="name_electroder" class="input_txt" />
          <br />
          <select name="electroder" id="select_electroder" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value=""></option>
          </select></td>
      </tr>
      <tr>
        <th>装配：</th>
        <td colspan="7"><select name="assembler" id="assembler">
            <option value="">请选择</option>
            <?php
			foreach($array_mould_assembler as $mould_assembler_key=>$mould_assembler_value){
				echo "<option value=\"".$mould_assembler_key."\">".$mould_assembler_value."</option>";
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $mouldid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_mould`.`clientid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_mould`.`part_name`,`db_mould`.`plastic_material`,`db_mould`.`shrinkage_rate`,`db_mould`.`surface`,`db_mould`.`cavity_number`,`db_mould`.`gate_type`,`db_mould`.`core_material`,`db_mould`.`isexport`,`db_mould`.`quality_grade`,`db_mould`.`difficulty_degree`,`db_mould`.`first_time`,`db_mould`.`remark`,`db_mould`.`mould_statusid`,`db_mould`.`projecter`,`db_mould`.`designer`,`db_mould`.`steeler`,`db_mould`.`electroder`,`db_mould`.`assembler`,`db_projecter`.`employee_name` AS `projecter_name`,`db_dept_projecter`.`dept_name` AS `dept_name_projecter`,`db_designer`.`employee_name` AS `designer_name`,`db_dept_designer`.`dept_name` AS `dept_name_designer`,`db_steeler`.`employee_name` AS `steeler_name`,`db_dept_steeler`.`dept_name` AS `dept_name_steeler`,`db_electroder`.`employee_name` AS `electroder_name`,`db_dept_electroder`.`dept_name` AS `dept_name_electroder` FROM `db_mould` LEFT JOIN `db_employee` AS `db_projecter` ON `db_projecter`.`employeeid` = `db_mould`.`projecter` LEFT JOIN `db_department` AS `db_dept_projecter` ON `db_dept_projecter`.`deptid` = `db_projecter`.`deptid` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould`.`designer` LEFT JOIN `db_department` AS `db_dept_designer` ON `db_dept_designer`.`deptid` = `db_designer`.`deptid` LEFT JOIN `db_employee` AS `db_steeler` ON `db_steeler`.`employeeid` = `db_mould`.`steeler` LEFT JOIN `db_department` AS `db_dept_steeler` ON `db_dept_steeler`.`deptid` = `db_steeler`.`deptid` LEFT JOIN `db_employee` AS `db_electroder` ON `db_electroder`.`employeeid` = `db_mould`.`electroder` LEFT JOIN `db_department` AS `db_dept_electroder` ON `db_dept_electroder`.`deptid` = `db_electroder`.`deptid` WHERE `db_mould`.`mouldid` = '$mouldid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>模具修改</h4>
  <form action="moulddo.php" name="mould" method="post">
    <table>
      <tr>
        <th width="10%">客户代码：</th>
        <td width="15%"><select name="clientid" id="clientid">
            <option value="">请选择</option>
            <?php
			if($result_client->num_rows){
				while($row_client = $result_client->fetch_assoc()){
			?>
            <option value="<?php echo $row_client['clientid']; ?>"<?php if($row_client['clientid'] == $array['clientid']) echo " selected=\"selected\""; ?>><?php echo $row_client['client_code'].'-'.$row_client['client_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><input type="text" name="project_name" id="project_name" value="<?php echo $array['project_name']; ?>" class="input_txt" /></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><input type="text" name="mould_number" id="mould_number" value="<?php echo $array['mould_number']; ?>" class="input_txt" /></td>
        <th width="10%">零件名称/编号：</th>
        <td width="15%"><input type="text" name="part_name" id="part_name" value="<?php echo $array['part_name']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>塑胶材料：</th>
        <td><input type="text" name="plastic_material" id="plastic_material" value="<?php echo $array['plastic_material']; ?>"  class="input_txt" /></td>
        <th>缩水率：</th>
        <td><input type="text" name="shrinkage_rate" id="shrinkage_rate" value="<?php echo $array['shrinkage_rate']; ?>"  class="input_txt" /></td>
        <th>表面要求：</th>
        <td><input type="text" name="surface" id="surface" value="<?php echo $array['surface']; ?>" class="input_txt" /></td>
        <th>模穴数：</th>
        <td><input type="text" name="cavity_number" id="cavity_number" value="<?php echo $array['cavity_number']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>浇口类型：</th>
        <td><input type="text" name="gate_type" id="gate_type" value="<?php echo $array['gate_type']; ?>" class="input_txt" /></td>
        <th>型腔/型芯材质：</th>
        <td><input type="text" name="core_material" id="core_material" value="<?php echo $array['core_material']; ?>" class="input_txt" /></td>
        <th>是否出口：</th>
        <td><select name="isexport" id="isexport">
            <option value="">请选择</option>
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $array['isexport']) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
        <th>质量等级：</th>
        <td><select name="quality_grade" id="quality_grade">
            <option value="">请选择</option>
            <?php foreach($array_mould_quality_grade as $quality_grade_key=>$quality_grade_value){ ?>
            <option value="<?php echo $quality_grade_value; ?>"<?php if($quality_grade_value == $array['quality_grade']) echo " selected=\"selected\""; ?>><?php echo $quality_grade_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>难度系数：</th>
        <td><select name="difficulty_degree" id="difficulty_degree">
            <option value="">请选择</option>
            <?php for($i=0.5;$i<1.5;$i+=0.1){ ?>
            <option value="<?php echo $i; ?>"<?php if(bccomp($i,$array['difficulty_degree'],1) == 0) echo " selected=\"selected\""; ?>><?php echo $i; ?></option>
            <?php } ?>
          </select></td>
        <th>首板时间：</th>
        <td><input type="text" name="first_time" value="<?php echo $array['first_time']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>重点提示：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
        <th>目前状态：</th>
        <td><select name="mould_statusid" id="mould_statusid">
            <option value="">请选择</option>
            <?php
			if($result_mould_status->num_rows){
				while($row_mould_status = $result_mould_status->fetch_assoc()){
			?>
            <option value="<?php echo $row_mould_status['mould_statusid']; ?>"<?php if($row_mould_status['mould_statusid'] == $array['mould_statusid']) echo " selected=\"selected\""; ?>><?php echo $row_mould_status['mould_statusname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>项目：</th>
        <td><input type="text" name="name_projecter" id="name_projecter" value="<?php echo $array['dept_name_projecter'].'-'.$array['projecter_name']; ?>" class="input_txt" />
          <br />
          <select name="projecter" id="select_projecter" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['projecter']; ?>" selected="selected"><?php echo $array['dept_name_projecter'].'-'.$array['projecter_name']; ?></option>
          </select></td>
        <th>设计：</th>
        <td><input type="text" name="name_designer" id="name_designer" value="<?php echo $array['dept_name_designer'].'-'.$array['designer_name']; ?>" class="input_txt" />
          <br />
          <select name="designer" id="select_designer" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['designer']; ?>" selected="selected"><?php echo $array['dept_name_designer'].'-'.$array['designer_name']; ?></option>
          </select></td>
        <th>钢料：</th>
        <td><input type="text" name="name_steeler" id="name_steeler" value="<?php echo $array['dept_name_steeler'].'-'.$array['steeler_name']; ?>" class="input_txt" />
          <br />
          <select name="steeler" id="select_steeler" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['steeler']; ?>" selected="selected"><?php echo $array['dept_name_steeler'].'-'.$array['steeler_name']; ?>></option>
          </select></td>
        <th>电极：</th>
        <td><input type="text" name="name_electroder" id="name_electroder" value="<?php echo $array['dept_name_electroder'].'-'.$array['electroder_name']; ?>" class="input_txt" />
          <br />
          <select name="electroder" id="select_electroder" size="5" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array['electroder']; ?>" selected="selected"><?php echo $array['dept_name_electroder'].'-'.$array['electroder_name']; ?></option>
          </select></td>
      </tr>
      <tr>
        <th>装配：</th>
        <td colspan="7"><select name="assembler" id="assembler">
            <option value="">请选择</option>
            <?php foreach($array_mould_assembler as $mould_assembler_key=>$mould_assembler_value){ ?>
            <option value="<?php echo $mould_assembler_key; ?>"<?php if($mould_assembler_key == $array['assembler']) echo " selected=\"selected\""; ?>><?php echo $mould_assembler_value; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mouldid" id="mouldid" value="<?php echo $mouldid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
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