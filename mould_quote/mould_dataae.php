<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
          //动态添加材料费
          $('#add_materia').click(function(){
                var trs = ' <tr><td colspan="4"><hr/></td></tr><tr><td>材料名称/Material</td><td>材料牌号/Specification</td><td>数量/Number</td><td>单价(元)/Unit Price</td></tr><tr><td><select name="mould_material[]" id="mould_material"><option value="">请选择</option> <?php foreach($array_mould_material as $mould_material_key=>$mould_material_value){             echo "<option value=\"".$mould_material_key."\">".$mould_material_value."</option>";
                        }
                        ?>          </td>          <td>              <select name="material_specification[]" id="material_specification">                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=\"".$material_specification_value.'\">'.$material_specification_value.'</option>';
                            }
                        ?>              </select>          </td>          <td>              <input type="text" name="number[]" id="number">          </td>          <td>              <input type="text" name="unit_price[]" id="unit_price" />          </td>                                  </tr>      <tr>          <td colspan="2">尺寸/Size(mm*mm*mm)</td>          <td>重量/Weight(kg)</td>          <td>金额/Price(RMB)</td>      </tr>      <tr id="machining_materia">          <td colspan="2">              <input name="material_length[]" type="text" placeholder="长">              *              <input name="material_width[]" type="text" placeholder="宽">              *              <input name="material_height[]" type="text" placeholder="高">          </td>          <td>              <input type="text" name="material_weight[]" id="material_weight">          </td>          <td>              <input type="text" name="material_price[]" id="material_price">          </td>      </tr> ';
                         $('#machining_materia').after(trs);
          })
          //动态添加模具配件费
          $('#add_standard').click(function(){
                    var standard = '<tr><td colspan="4"><hr/></td></tr><tr>  <td>装配件/Item</td>          <td>数量/Number</td>          <td>单价(元)/Unit Price</td>          <td>金额(RMB)/price</td>      </tr>      <tr>          <td>              <select name="mold_standard[]" id="mold_standard">                        <option value="">请选择</option>                        <?php
                            foreach($array_mold_standard as $mold_standard_key => $mold_standard_value){
                                echo "<option value=\"".$mold_standard_value.'\">'.$mold_standard_value.'</option>';
                            }
                        ?>              </select>         </td>         <td>             <input type="text" name="standard_number[]" id="standard_number">         </td>         <td>             <input type="text" name="standard_unit_price[]" id="standard_unit_price">         </td>         <td>             <input type="text" name="standard_price[]" id="standard_price">         </td>      </tr>      <tr>          <td>规格型号/Specification</td>          <td>品牌/Supplier</td>      </tr>      <tr id="mold_standard">          <td>                <input type="text" name="standard_specificatioin[]">          </td>          <td>                  <input type="text" name="standard_supplier[]">          </td>      </tr>';
                        $('#standard_parts').after(standard);
          })
          //动态添加加工费
          $('#add_manu').click(function(){
                var manu = ' <tr><td colspan="4"><hr/></td></tr><tr>            <td>名称/Item</td>            <td>工时(小时)/Hour</td>            <td>单价(元)/Unit Price(RMB)</td>            <td>金额(元)/Price(RMB)</td>        </tr>        <tr id="manu_cost">            <td>              <select name="mold_manufacturing[]" id="mold_manufacturing">                        <option value="">请选择</option>                        <?php
                            foreach($array_mould_manufacturing as $mold_manufacturing_key => $mold_manufacturing_value){
                                echo "<option value=\"".$mold_manufacturing_value.'\">'.$mold_manufacturing_value.'</option>';
                            }
                        ?>              </select>         </td>         <td>             <input type="text" name="manufacturing_hour[]" id="manufacturing">         </td>         <td>             <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price">         </td>         <td>             <input type="text" name="manufacturing_price[]" id="manufacuring_price">          </td>        </tr>';
                     $('#manu_cost').after(manu);
          })
	$("#submit").click(function(){
		var mould_name = $("#mould_name").val();
		if(!$.trim(mould_name)){
			$("#mould_name").focus();
			return false;
		}
		var cavity_type = $("#cavity_type").val();
		if(!$.trim(cavity_type)){
			$("#cavity_type").focus();
			return false;
		}
		var p_length = $("#p_length").val();
		if(!rf_b.test(p_length)){
			$("#p_length").focus();
			return false;
		}
		var p_width = $("#p_width").val();
		if(!rf_b.test(p_width)){
			$("#p_width").focus();
			return false;
		}
		var p_height = $("#p_height").val();
		if(!rf_b.test(p_height)){
			$("#p_height").focus();
			return false;
		}
		var m_length = $("#m_length").val();
		if(!rf_b.test(m_length)){
			$("#m_length").focus();
			return false;
		}
		var m_width = $("#m_width").val();
		if(!rf_b.test(m_width)){
			$("#m_width").focus();
			return false;
		}
		var m_height = $("#m_height").val();
		if(!rf_b.test(m_height)){
			$("#m_height").focus();
			return false;
		}
		var m_weight = $("#m_weight").val();
		if(!rf_b.test(m_weight)){
			$("#m_weight").focus();
			return false;
		}
	})
	$("#p_weight").blur(function(){
		var p_weight = $(this).val();
		if($.trim(p_weight) && !rf_a.test(p_weight)){
			alert('产品重量请输入数字');
			$(this).val('');
			$("#p_weight").focus();
		}
	})
	$("#lift_time").blur(function(){
		var lift_time = $(this).val();
		if($.trim(lift_time) && !ri_a.test(lift_time)){
			alert('模具寿命请输入数字');
			$(this).val('');
			$("#lift_time").focus();
		}
	})
	$("#p_length,#p_width,#p_height").focus(function(){
		var cavity_type = $("#cavity_type").val();
		if(!cavity_type){
			$("#cavity_type").focus();
		}
	})
	$("#p_length,#p_width,#p_height").blur(function(){
		var cavity_type = $("#cavity_type").val();
		var p_length = $("#p_length").val();
		var p_width = $("#p_width").val();
		var p_height = $("#p_height").val();
		var p_value = $(this).val();
		var p_default_value = this.defaultValue;
		if($.trim(p_value) && !rf_b.test(p_value)){
			alert('请输入数字');
			$(this).val(p_default_value);
			$(this).focus();
		}else if($.trim(p_value)){
			$(this).val(parseFloat(p_value).toFixed(1));
		}
           //ajax请求动态获取模具的长宽高
		if(cavity_type && $.trim(p_length) && $.trim(p_width) && $.trim(p_height)){
			$.post("../ajax_function/product_mould_size.php",{
				   cavity_type:cavity_type,
				   p_length:p_length,
				   p_width:p_width,
				   p_height:p_height
			},function(data,textStatus){
				var array_data = data.split('#');
				$("#m_length").val(array_data[0]);
				$("#m_width").val(array_data[1]);
				$("#m_height").val(array_data[2]);
				$("#m_weight").val(array_data[3]);
			})
		}
	})
	$("#cavity_type").change(function(){
		var cavity_type = $(this).val();
		var p_length = $("#p_length").val();
		var p_width = $("#p_width").val();
		var p_height = $("#p_height").val();
		if(cavity_type && $.trim(p_length) && $.trim(p_width) && $.trim(p_height)){
			$.post("../ajax_function/product_mould_size.php",{
				   cavity_type:cavity_type,
				   p_length:p_length,
				   p_width:p_width,
				   p_height:p_height
			},function(data,textStatus){
				var array_data = data.split('#');
				$("#m_length").val(array_data[0]);
				$("#m_width").val(array_data[1]);
				$("#m_height").val(array_data[2]);
				$("#m_weight").val(array_data[3]);
			})
		}
	})
})
</script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'add'){
	  $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
	  $result_employee = $db->query($sql_employee);
	  $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4>模具数据添加</h4>
  <form action="mould_datado.php" name="mould_data" method="post">
    <table>
      <tr>
        <td width="25%">模具名称/Mold Specification</td>
        <td width="25%">型腔数量/Cav.</td>
        <td width="25%">产品零件号/Part No.</td>
        <td width="25%">首次试模时间/T1 Time</td>
      </tr>
      <tr>
        <td><input type="text" name="mould_name" id="mould_name" class="input_txt" size="35" /></td>
        <td><select name="cavity_type" id="cavity_type">
            <option value="">请选择</option>
            <?php
			foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){
				echo "<option value=\"".$cavity_type_key."\">".$cavity_type_value."</option>";
			}
			?>
          </select></td>
        <td><input type="text" name="part_number" class="input_txt" size="35" /></td>
        <td><input type="text" name="t_time" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>产品大小/Part Size (mm)</td>
        <td>产品重量/Part Weight(g)</td>
        <td>数据文件名/Drawing No.</td>
        <td>最终交付时间/Lead Timeme</td>
      </tr>
      <tr>
        <td><input type="text" name="p_length" id="p_length" class="input_txt" size="10" />
          *
          <input type="text" name="p_width" id="p_width" class="input_txt" size="10" />
          *
          <input type="text" name="p_height" id="p_height" class="input_txt" size="10" /></td>
        <td><input type="text" name="p_weight" id="p_weight" class="input_txt" size="35" /></td>
        <td><input type="text" name="drawing_file" class="input_txt" size="35" /></td>
        <td><input type="text" name="lead_time" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>模具尺寸/Mold Size (mm)</td>
        <td>模具重量/Mold Weight(Kg)</td>
        <td>模具寿命/Longevity</td>
        <td>设备吨位/Press(Ton)</td>
      </tr>
      <tr>
        <td><input type="text" name="m_length" id="m_length" class="input_txt" size="10" readonly="readonly" />
          *
          <input type="text" name="m_width" id="m_width" class="input_txt" size="10" readonly="readonly" />
          *
          <input type="text" name="m_height" id="m_height" class="input_txt" size="10" readonly="readonly" /></td>
        <td><input type="text" name="m_weight" id="m_weight" class="input_txt" size="35" readonly="readonly" /></td>
        <td><input type="text" name="lift_time" id="lift_time" class="input_txt" size="35" /></td>
        <td><input type="text" name="tonnage" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>客户名称/Customer</td>
        <td>项目名称/Program</td>
        <td>联系人/Attention</td>
        <td>电话/TEL</td>
      </tr>
      <tr>
        <td><input type="text" name="client_name" class="input_txt" size="35" /></td>
        <td><input type="text" name="project_name" class="input_txt" size="35"/></td>
        <td><input type="text" name="contacts" value="<?php echo $array_employee['employee_name']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="tel" value="<?php echo $array_employee['phone']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td colspan="4">信箱/E-mail</td>
      </tr>
      <tr>
        <td colspan="4"><input type="text" name="email" value="<?php echo $array_employee['email']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <!--热处理-->
      <tr>
            <td colspan="4" style="font-size:20px;font-weight:blod;text-align:center">热处理/Heat Treatment</td>
      </tr>
      <tr>
          <td>热处理名称/Item</td>
          <td>重量/weight(kg)</td>
          <td>单价/Unit Price(RMB)</td>
          <td>金额/Price(RMB)</td>
      </tr>
      <tr>
          <td>
              <select name="heat_name" id="heat_name">
                  <option value="">请选择</option>
                  <?php
                        foreach($array_mould_heat as $mould_heat_key =>$mould_heat_value){
                            echo '<option value='.$mould_heat_key.'>'.$mould_heat_value.'</option>';
                        }
                  ?>
              </select>
          </td>  
          <td>
              <input name="heat_weight" type="text" id="heat_weight">
          </td>        
          <td>
              <input name="heat_unit_price" type="text" id="heat_unit_price">
          </td>
          <td>
              <input name="heat_price" type="text" id="heat_price">
          </td>
      </tr>
     <!--加工材料费-->
      <tr>
          <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">材料加工费/Machining Materia</td>
      </tr>
      <tr>
         <td>材料名称/Material</td>
         <td>材料牌号/Specification</td>
         <td>数量/Number</td>
         <td>单价(元)/Unit Price</td>
      </tr>
      <tr>
          <td>
              <select name="mould_material[]" id="mould_material">
                        <option value="">请选择</option>
                        <?php
                             foreach($array_mould_material as $mould_material_key=>$mould_material_value){
                                echo "<option value=\"".$mould_material_key."\">".$mould_material_value."</option>";
                        }
                        ?>
          </td>
          <td>
              <select name="material_specification[]" id="material_specification">
                        <option value="">请选择</option>
                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>
              </select>
          </td>
          <td>
              <input type="text" name="number[]" id="number">
          </td>
          <td>
              <input type="text" name="unit_price[]" id="unit_price" />
          </td>                             
      </tr>
      <tr>
          <td colspan="2">尺寸/Size(mm*mm*mm)</td>
          <td>重量/Weight(kg)</td>
          <td>金额/Price(RMB)</td>
      </tr>
      <tr id="machining_materia">
          <td colspan="2">
              <input name="material_length[]" type="text" placeholder="长">
              *
              <input name="material_width[]" type="text" placeholder="宽">
              *
              <input name="material_height[]" type="text" placeholder="高">
          </td>
          <td>
              <input type="text" name="material_weight[]" id="material_weight">
          </td>
          <td>
              <input type="text" name="material_price[]" id="material_price">
          </td>
      </tr>
      <tr>
          <td colspan="4" style="text-align:center">
              <p id="add_materia" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
      <!--模具配件-->
      <tr>
          <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">模具配件/Mold standard parts</td>
      </tr>
      <tr>
          <td>装配件/Item</td>
          <td>数量/Number</td>
          <td>单价(元)/Unit Price</td>
          <td>金额(RMB)/price</td>
      </tr>
      <tr>
          <td>
              <select name="mold_standard[]" id="mold_standard">
                        <option value="">请选择</option>
                        <?php
                            foreach($array_mold_standard as $mold_standard_key => $mold_standard_value){
                                echo "<option value=".$mold_standard_value.'>'.$mold_standard_value.'</option>';
                            }
                        ?>
              </select>
         </td>
         <td>
             <input type="text" name="standard_number[]" id="standard_number">
         </td>
         <td>
             <input type="text" name="standard_unit_price[]" id="standard_unit_price">
         </td>
         <td>
             <input type="text" name="standard_price[]" id="standard_price">
         </td>
      </tr>
      <tr>
          <td>规格型号/Specification</td>
          <td>品牌/Supplier</td>
      </tr>
      <tr id="standard_parts">
          <td>
                <input type="text" name="standard_specificatioin[]">
          </td>
          <td>
                  <input type="text" name="standard_supplier[]">
          </td>
      </tr>
      <tr>
          <td colspan="4" style="text-align:center">
              <p id="add_standard" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
      <!--设计费-->
        <tr>
               <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">设计费/Design</td>
        </tr>
        <tr>
            <td>设计名称/Item</td>
            <td>工时(小时)/Hour</td>
            <td>单价(元)/Unit price(RBM)</td>
            <td>金额(元)/Price(RMB)</td>
        </tr>
        <tr>
            <td>
                
                <?php
                    $i = 1;
                    foreach($array_mould_design as $mould_design_key => $mould_design_value){
                        echo '<label><input type="checkbox" name="mould_design[]" value='.$mould_design_key.'>&nbsp;'.$mould_design_value.'</label>&nbsp;&nbsp;';
                        echo ($i++)%2 == 0?'<br/>':'';
                    }
                ?>
            </td>
            <td>
                <input type="text" name="design_hour">
            </td>
            <td>
                <input type="text" name="design_unit_price">
            </td>
            <td>
                <input type="text" name="design_price">
            </td>
        </tr>
        <!--加工费-->
        <tr>
               <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">加工费/Manufacturing Cost</td>
        </tr>
        <tr>
            <td>名称/Item</td>
            <td>工时(小时)/Hour</td>
            <td>单价(元)/Unit Price(RMB)</td>
            <td>金额(元)/Price(RMB)</td>
        </tr>
        <tr id="manu_cost">
            <td>
              <select name="mold_manufacturing[]" id="mold_manufacturing">
                        <option value="">请选择</option>
                        <?php
                            foreach($array_mould_manufacturing as $mold_manufacturing_key => $mold_manufacturing_value){
                                echo "<option value=\"".$mold_manufacturing_key.'">'.$mold_manufacturing_value.'</option>';
                            }
                        ?>
              </select>
         </td>
         <td>
             <input type="text" name="manufacturing_hour[]" id="manufacturing">
         </td>
         <td>
             <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price">
         </td>
         <td>
             <input type="text" name="manufacturing_price[]" id="manufacuring_price"> 
         </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center">
              <p id="add_manu" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
        <!--其他费用-->
          <tr>
               <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">其它费用/Other Fee</td>
        </tr>
        <tr>
            <td>试模费/Trial Fee</td>
            <td>运输费/Freight Fee</td>
            <td>管理费/Management Fee</td>
            <td>利润/Profit</td>            
        </tr>
        <tr>
            <td>
                <input type="text" name="trial_fee" id="trial_fee">
            </td>
            <td>
                <input type="text" name="freight_fee" id="freight_fee">
            </td>
            <td>
                <input type="text" name="management_fee" id="management_fee">
            </td>
            <td>
                <input type="text" name="profit" id="profit" >
            </td>
        </tr>
           <tr>
            <td>税/VAT TAX(17%)</td>
        </tr>
        <tr>
               <td>
                <input type="text" name="vat_tax" id="vat_tax">
            </td>
        </tr>
        <!--总计-->
          <tr>
               <td colspan="4" style="text-align:center;font-size:20px;font-weight:blod">模具价格/Mold Price</td>
        </tr>
        <tr>
            <td colspan="2">模具价格(元)不含税/Mold Price without VAT(RMB)</td>
            <td colspan="2">模具价格(USD)/Mold Price(USD) Rate=6.5</td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="text" name="mold_price_rmb" id="mold_price_rmb">
            </td>
            <td colspan="2">
                <input type="text" name="mold_price_usd" id="mold_price_usd">
            </td>
        </tr>
      <tr>
        <td colspan="4" align="center"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == 'edit'){
	  $mould_dataid = fun_check_int($_GET['id']);
	  $sql = "SELECT `mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email` FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $sql_cfm = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' AND `quote_status` = 1";
		  $result_cfm = $db->query($sql_cfm);
		  if(!$result_cfm->num_rows){
		  
  ?>
  <h4>模具数据修改</h4>
  <form action="mould_datado.php" name="mould_data" method="post">
    <table>
      <tr>
        <td width="25%">模具名称/Mold Specification</td>
        <td width="25%">型腔数量/Cav.</td>
        <td width="25%">产品零件号/Part No.</td>
        <td width="25%">首次试模时间/T1 Time</td>
      </tr>
      <tr>
        <td><input type="text" name="mould_name" id="mould_name" value="<?php echo $array['mould_name']; ?>" class="input_txt" size="35" /></td>
        <td><select name="cavity_type" id="cavity_type">
            <?php foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){ ?>
            <option value="<?php echo $cavity_type_key; ?>"<?php if($cavity_type_key == $array['cavity_type']) echo " selected=\"selected\""; ?>><?php echo $cavity_type_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="text" name="part_number" class="input_txt" value="<?php echo $array['part_number']; ?>" size="35" /></td>
        <td><input type="text" name="t_time" class="input_txt" value="<?php echo $array['t_time']; ?>" size="35" /></td>
      </tr>
      <tr>
        <td>产品大小/Part Size (mm)</td>
        <td>产品重量/Part Weight(g)</td>
        <td>数据文件名/Drawing No.</td>
        <td>最终交付时间/Lead Timeme</td>
      </tr>
      <tr>
        <td><input type="text" name="p_length" id="p_length" value="<?php echo $array['p_length']; ?>" class="input_txt" size="10" />
          *
          <input type="text" name="p_width" id="p_width" value="<?php echo $array['p_width']; ?>" class="input_txt" size="10" />
          *
          <input type="text" name="p_height" id="p_height" value="<?php echo $array['p_height']; ?>" class="input_txt" size="10" /></td>
        <td><input type="text" name="p_weight" id="p_weight" value="<?php echo $array['p_weight']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="drawing_file" value="<?php echo $array['drawing_file']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="lead_time" value="<?php echo $array['lead_time']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>模具尺寸/Mold Size (mm)</td>
        <td>模具重量/Mold Weight(Kg)</td>
        <td>模具寿命/Longevity</td>
        <td>设备吨位/Press(Ton)</td>
      </tr>
      <tr>
        <td><input type="text" name="m_length" value="<?php echo $array['m_length']; ?>" id="m_length" class="input_txt" size="10" readonly="readonly" />
          *
          <input type="text" name="m_width" id="m_width" value="<?php echo $array['m_width']; ?>" class="input_txt" size="10" readonly="readonly" />
          *
          <input type="text" name="m_height" id="m_height" value="<?php echo $array['m_height']; ?>" class="input_txt" size="10" readonly="readonly" /></td>
        <td><input type="text" name="m_weight" id="m_weight" value="<?php echo $array['m_weight']; ?>" class="input_txt" size="35" readonly="readonly" /></td>
        <td><input type="text" name="lift_time" id="lift_time" value="<?php echo $array['lift_time']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="tonnage" value="<?php echo $array['tonnage']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>客户名称/Customer</td>
        <td>项目名称/Program</td>
        <td>联系人/Attention</td>
        <td>电话/TEL</td>
      </tr>
      <tr>
        <td><input type="text" name="client_name" value="<?php echo $array['client_name']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="project_name" value="<?php echo $array['project_name']; ?>" class="input_txt" size="35"/></td>
        <td><input type="text" name="contacts" value="<?php echo $array['contacts']; ?>" class="input_txt" size="35" /></td>
        <td><input type="text" name="tel" value="<?php echo $array['tel']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td colspan="4">信箱/E-mail</td>
      </tr>
      <tr>
        <td colspan="4"><input type="text" name="email" value="<?php echo $array['email']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td colspan="4" align="center"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
          <input type="hidden" name="mould_dataid" value="<?php echo $mould_dataid; ?>" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
		  }
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>