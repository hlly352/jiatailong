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
	//统计第一列需要合并的单元格个数
	function count_trs(trs_name,tds_name){
		
		 var num = $(trs_name).size()+2;

 		$(tds_name).attr('rowspan',num);
	}
          //动态添加材料费
          $('#add_materia').click(function(){
          	     var trs = '     <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" id="mould_material"style="color:black;font-weight:150;font-size:15px">               </td>               <td>               	<select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" >             </td>             <td>                   <input name="material_length[]" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td>                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td>                  <input name="material_height[]" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" value="70"/>             </td>             <td>                 <input type="text" name="material_price[]" id="material_price"> 	             </td>   <td>     <p class="dels"  style="margin:2px auto;width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td> </tr>';
              
        	           count_trs(".material_trs","#material_first_td");
     
                         $('#machining_materia').before(trs);
          })
          //动态删除表格行
          $('.dels').live('click',function(){
          	    $(this).parent().parent().remove();
          })
          //动态添加热处理
          $('#add_heat').click(function(){
          		var heats = '  <tr class="heat_trs">              <td colspan="4">               <input name="mould_heat_name[]" id="mould_heat_name"  style="color:black;font-weight:150;font-size:15px">             </td>              <td colspan="2">                  <input name="heat_weight" type="text" id="heat_weight">              </td>              <td colspan="6">                 <input name="heat_unit_price" type="text" id="heat_unit_price" value="24">	              </td>              <td colspan="2">                <input name="heat_price" type="text" id="heat_price">              </td>             <td><p class="dels"  style="width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td>         </tr>';
          		count_trs(".heat_trs","#heat_first_td");
     
          		$('#mould_heats').before(heats);
          })  
          //动态添加模具配件费
          $('#add_standard').click(function(){
          	         var standard = '<tr class="parts_trs">       	<td colspan="4">      	     <input name="mold_standard[]" id="mold_standard" style="color:black;font-weight:150;font-size:15px" >      	</td>      	<td colspan="2">      	    <input type="text" name="standard_specification[]" id="standard_specification">      	</td>      	<td colspan="5">      	     <select name="standard_supplier[]" id="standard_supplier" style="width:150px">                  	<option>请选择</option>                  </select>      	</td>      	<td>      	    <input type="text" name="standard_number[]" id="standard_number">	      	</td>      	<td>               <input type="text" name="standard_unit_price[]" id="standard_unit_price">      	</td>      	<td>      	   <input type="text" name="standard_price[]" id="standard_price">	      	</td>     <td><p class="dels"  style="width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td>	      </tr>';
               	  count_trs(".parts_trs","#parts_first_td");
                        $('#standard_parts').before(standard);
          })
          //动态添加设计费
          $('#add_designs').click(function(){
          		var design_adder = '    <tr class="design_trs">              <td colspan="4">                  <input name="mold_design_name[]" id="mold_design_name" style="color:black;font-weight:150;font-size:15px">                           </td>              <td colspan="2">                 <input type="text" name="design_hour[]" id="design_hour" value="109">              </td>              <td colspan="6">                 <input type="text" name="design_unit_price[]" id="design_unit_price" value="100">              </td>              <td colspan="2">                <input type="text" name="design_price[]" id="design_price">              </td>        <td><p class="dels"  style="width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td> </tr>';
          		count_trs(".design_trs","#design_first_td");
          		$('#designs').before(design_adder);

  		
          })
          //动态添加加工费
          $('#add_manu').click(function(){
     		var manu = '<tr class="manus_trs">              <td colspan="4">                  <input name="mold_manufacturing[]" id="mold_manufacturing" style="color:black;font-weight:150;font-size:15px">                            </td>              <td colspan="2">                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" value="124">              </td>              <td colspan="6">                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" value="100">              </td>              <td colspan="2">               <input type="text" name="manufacturing_price[]" id="manufacuring_price">               </td>                 <td><p class="dels"  style="width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td>     </tr>';
             	count_trs(".manus_trs","#manus_first_td");
                     $('#manu_cost').before(manu);
          })
          //动态添加其它费用
          $("#add_others").click(function(){
          	     var others_adder = '   <tr class="others_trs">  <td colspan="4"><input name="other_fee_name[]" id="other_fee_name" ></td>          	   <td colspan="8"><input name="other_fee_instr[]" id="other_fee_instr" ></td>          	   <td colspan="2"> <input name="other_fee_price[]" id="other_fee_price" >    </td>  <td><p class="dels"  style="width:50px;height:5px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;cursor:pointer">删除</p></td>        </tr>';
          	     	count_trs(".others_trs","#others_first_td");
                     $('#others_fees').before(others_adder);
          })
          //统计总共有多少tr标签
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
	var tr_length = $("#materials").children("tr").length();
          		alert('dd');
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
  <style type="text/css" media="screen">
  	#main_table tr td{border:1px solid red;}
  	input{width:122px;}
  </style>
   <table id="main_table" style="width:95%">
   	<!--基本信息-->
   	<tr>
   	     <td colspan="5" rowspan="5">
   	     	<img src="" alt="logo">
   	     </td>
   	     <td colspan="9" rowspan="5">
   	     	 <p style="font-weight:blod;font-size:30px">模具费用分解表</p>
      	            <p style="font-weight:blod;font-size:30px">Tooling Cost Break Down</p>
   	     </td>
   	   <td>客户名称/Customer</td>
      	   <td>
      	       <input type="text" name="client_name" />
      	   </td>	
   	</tr>
   	<tr>
   	  <td>项目名称/Program</td>
             <td>
      	     <input type="text" name="project_name"/>
      	  </td>
   	</tr>
   	<tr>
      	  <td>联系人/Attention</td>
      	  <td>
      	    <input type="text" name="contacts" value="<?php echo $array_employee['employee_name']; ?>">
      	  </td>
           </tr>
           <tr>
      	  <td>电话/TEL</td>
      	  <td>
      	    <input  type="text" name="tel" value="<?php echo $array_employee['phone']; ?>"/>
      	  </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="<?php echo $array_employee['email']; ?>"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5">模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav.</td>
               <td colspan="5" rowspan="6">
               	<div style="border:1px solid grey;width:300px;">
        			<p style="margin:0px auto">产品图片</p>
        		</div>
               </td>
               <td colspan="2">产品零件号/Part No.</td>
               <td colspan="2">首次试模时间/T1 Time</td>
           </tr>
           <tr>
               <td colspan="5">
               	<input type="text" name="mould_name" id="mould_name" class="input_tx"/>
               </td>
               <td colspan="2">
               	<select name="cavity_type" id="cavity_type" style="">
                        <option value="">请选择</option>
                          <?php
			foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){
				echo "<option value=\"".$cavity_type_key."\">".$cavity_type_value."</option>";
			}
			?>
                     </select>
               </td>
               <td colspan="2">
               	<input type="text" name="part_number" class="input_tx"/>
               </td>
               <td colspan="2">
               	<input type="text" name="t_time" class="input_tx"/>
               </td>
           </tr>
           <tr>
               <td colspan="5">产品大小/Part Size (mm)</td>
               <td>产品重量/Part Weight(g)</td>
               <td>材料/Material</td>
               <td colspan="2">数据文件名/Drawing No.</td>
               <td colspan="2">最终交付时间/Lead Timeme</td>
           </tr>
           <tr>
              <td>
              	<input type="text" name="p_length" id="p_length" class="input_tx"/>
              </td>
              <td>*</td>
              <td>
              	<input type="text" name="p_width" id="p_width" class="input_tx" />
              </td>
              <td>*</td>
              <td>
                     <input type="text" name="p_height" id="p_height" class="input_tx" />
              </td>
              <td>
                    <input type="text" name="p_weight" id="p_weight" class="input_tx"/>
              </td>
              <td>
              	<input type="text" name="m_material" id="m_material" class="input_tx"/>
              </td>
              <td colspan="2">
              	<input type="text" name="drawing_file" class="input_tx"/>
              </td>
              <td colspan="2">
              	<input type="text" name="lead_time" class="input_tx" />
              </td>
           </tr>
           <tr>
               <td colspan="5">模具尺寸/Mold Size (mm)</td>
               <td colspan="2">模具重量/Mold Weight(Kg)</td>
               <td colspan="2">模具寿命/Longevity</td>
               <td colspan="2">设备吨位/Press(Ton)</td>
           </tr>
           <tr>
              <td>
              	<input type="text" name="m_length" id="m_length"  readonly="readonly" />
              </td>
              <td>*</td>
              <td>
              	 <input type="text" name="m_width" id="m_width" readonly="readonly" />
              </td>
              <td>*</td>
              <td>
                    <input type="text" name="m_height" id="m_height" readonly="readonly" /></td>
              </td>
              <td colspan="2">
                   <input type="text" name="m_weight" id="m_weight" readonly="readonly" />
              </td>
              <td colspan="2">
              	<input type="text" name="lift_time" id="lift_time" />
              </td>
              <td colspan="2">
              	<input type="text" name="tonnage"/>
              </td>
           </tr>
           <!--加工材料费-->
           <tr>
               <td id="material_first_td" rowspan="7">材料加工费/Machining Materia</td> 	
               <td colspan="4">材料名称/Material</td>
               <td>材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td>重量/Weight(kg)</td>
               <td>单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
              <?php
              $i = 0;
               foreach($array_mould_material as $mould_material_Key=>$mould_material_value){

      	?>
           <tr class="material_trs">
               <td colspan="4">
                  <input name="mould_material[]" id="mould_material" value=<?php echo $mould_material_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:15px">
               </td>
               <td>
               	<select name="material_specification[]" id="material_specification" >
                        <option value="">请选择</option>
                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>
               </select>
             </td>
             <td>
                   <input type="text" name="materials_number[]" id="materials_number" >
             </td>
             <td>
                   <input name="material_length[]" id="material_length" type="text" placeholder="长">
             </td>
             <td>*</td>
             <td>
                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">
             </td>
             <td>*</td>
             <td>
                  <input name="material_height[]" id="material_height" type="text" placeholder="高">
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight">
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" value="70"/>
             </td>
             <td>
                 <input type="text" name="material_price[]" id="material_price"> 	
             </td>
               <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="6" id="total_machining" ></td>  ';
          	     }
  	     $i++;
          ?> 
           </tr>
           <?php } ?>
           <tr id="machining_materia"></tr>
           <tr >
             <td colspan="16" style="">
              <p id="add_materia" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
             </td>
           </tr>
           <!--热处理-->
           <tr>
             <td id="heat_first_td" rowspan="4">热处理/Heat Treatment</td>
             <td colspan="4">热处理名称/Item</td>
             <td colspan="2">重量/weight(kg)</td>
             <td colspan="6">单价/Unit Price(RMB)</td>
             <td colspan="2">金额/Price(RMB)</td>
             <td>小计(元)</td>
           </tr>
           <?php
      	$i = 0;
      	foreach($array_mould_heat as $mould_heat_key=>$mould_heat_value){
           ?>
           <tr class="heat_trs">
              <td colspan="4">
                  <input name="mould_heat_name[]" id="mould_heat_name" value=<?php echo $mould_heat_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:15px" class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight" type="text" id="heat_weight">
              </td>
              <td colspan="6">
                 <input name="heat_unit_price" type="text" id="heat_unit_price" value="24">	
              </td>
              <td colspan="2">
                <input name="heat_price" type="text" id="heat_price">
              </td>
                <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="3" id="total_heats"></td>  ';
          	     }
  	     $i++;
          ?> 
           </tr>
           <?php } ?>
           <tr id="mould_heats"></tr>
           <tr >
             <td colspan="16" style="">
                <p id="add_heat" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
              </p>
          </td>
      </tr>
      <!--模具配件-->
      <tr>
      	<td id="parts_first_td" rowspan="8">模具配件/Mold standard parts</td>
      	<td colspan="4">装配件/Item</td>
      	<td colspan="2">规格型号/Specification</td>
      	<td colspan="5">品牌/Supplier</td>
      	<td>数量/Number</td>
      	<td>单价(元)/Unit Price</td>
          <td>金额(RMB)/price</td>
          <td>小计(元)</td>
      </tr>
      <?php
      	$i = 0;
      	foreach($array_mold_standard as $mold_standard_key=>$mold_standard_value){
      ?>
      <tr class="parts_trs"> 
      	<td colspan="4">
      	     <input name="mold_standard[]" id="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mold_standard_value ?>">
      	</td>
      	<td colspan="2">
      	    <input type="text" name="standard_specification[]" id="standard_specification">
      	</td>
      	<td colspan="5">
      	     <select name="standard_supplier[]" id="standard_supplier" style="width:150px">
                  	<option>请选择</option>
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
      	 <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="7" id="total_standard"></td>  ';
          	     }
  	     $i++;
          ?> 
      	      
      </tr>
      <?php }?>
      <tr id="standard_parts"></tr>
      <tr>
          <td colspan="16" style="">
              <p id="add_standard" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
      <!--设计费-->
       <tr>
             <td id="design_first_td" rowspan="5">设计费/Design</td>
             <td colspan="4">设计名称/Item</td>
             <td colspan="2">工时(小时)/Hour</td>
             <td colspan="6">单价(元)/Unit Price(RMB)</td>
             <td colspan="2">金额(元)/Price(RMB)</td>
             <td>小计(元)</td>
           </tr>
           <?php 
            $i = 0;
        	  foreach($array_mould_design as $mould_design_key => $mould_design_value){	
            ?>
           <tr class="design_trs">
              <td colspan="4">
                  <input name="mold_design_name[]" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mould_design_value ?>">
              
              </td>
              <td colspan="2">
                 <input type="text" name="design_hour[]" id="design_hour" value="109">
              </td>
              <td colspan="6">
                 <input type="text" name="design_unit_price[]" id="design_unit_price" value="100">
              </td>
              <td colspan="2">
                <input type="text" name="design_price[]" id="design_price">
              </td>
                  <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="4" id="total_designs"></td>  ';
          	     }
  	     $i++;
          ?> 
           </tr>
           <?php } ?>
           <tr id="designs"></tr>
           <tr>
             <td colspan="16" style="">
                <p id="add_designs" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
               </p>
            </td>
          </tr>
          <!--加工费-->
           <tr>
             <td id="manus_first_td" rowspan="11">加工费/Manufacturing Cost</td>
             <td colspan="4">名称/Item</td>
             <td colspan="2">工时(小时)/Hour</td>
             <td colspan="6">单价(元)/Unit Price(RMB)</td>
             <td colspan="2">金额(元)/Price(RMB)</td>
             <td>小计(元)</td>
           </tr>
          <?php 
        	$i = 0;
        	foreach($array_mould_manufacturing as $mould_manufacturing_key=>$mould_manufacturing_value){
        	?>
           <tr class="manus_trs">
              <td colspan="4">
                  <input name="mold_manufacturing[]" id="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mould_manufacturing_value ?>">
              
              </td>
              <td colspan="2">
                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" value="124">
              </td>
              <td colspan="6">
                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" value="100">
              </td>
              <td colspan="2">
               <input type="text" name="manufacturing_price[]" id="manufacuring_price"> 
              </td>
                      <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="10" id="total_manufacturing"></td>  ';
          	     }
  	     $i++;
             ?> 
           </tr>
           <?php } ?>
           <tr  id="manu_cost"></tr>
        <tr>
            <td colspan="16" style="">
              <p id="add_manu" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
          </tr>
          <!--其它费用-->
          <tr>
          	   <td id="others_first_td" rowspan="6">其它费用/Other Fee</td>
          	   <td colspan="4">费用名称</td>
          	   <td colspan="8">费用计算说明</td>
          	   <td colspan="2">金额(元)</td>
          	   <td>小计(元)</td>
          </tr>
          <tr class="others_trs">
          	    <td colspan="4">试模费/Trial Fee</td>
          	   <td colspan="8">3 times mold trial(excluding raw material cost)</td>
          	   <td colspan="2">
          	   	<input type="text" name="trial_fee" id="trial_fee">
          	   </td>
          	   <td rowspan="2" id="total_others"></td
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">运输费/Freight Fee</td>
          	   <td colspan="8">sample and tooling transport cost paid by customer</td>
          	   <td colspan="2">
          	       <input type="text" name="freight_fee" id="freight_fee">
          	   </td>
          </tr>
           <tr class="others_trs" id="others_fees">
          	    <td colspan="4">管理费/Management Fee</td>
          	   <td colspan="8">5%</td>
          	   <td colspan="2">
          	        <input type="text" name="management_fee" id="management_fee">
          	   </td>
          	   <td></td>
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">利润/Profit</td>
          	   <td colspan="8">10%</td>
          	   <td colspan="2">
          	   	<input type="text" name="profit" id="profit" >
          	   </td>
          	   <td></td>
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">税/VAT TAX(16%)</td>
          	   <td colspan="8">16%</td>
          	   <td colspan="2">
          	        <input type="text" name="vat_tax" id="vat_tax">
          	   </td>
          	   <td></td>
          </tr>
          <tr>
            <td colspan="16" style="">
              <p id="add_others" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
          </tr>
   </table>










<div style="height:20px"></div>
    <table id="basic_info" style="width:91%" border="1">

      
      <tr>
      	<td style="width:100px" rowspan="5">
      		<img src="" alt="logo">
      	</td>
      	<td colspan="3" rowspan="5" >
      	    <p style="font-weight:blod;font-size:30px">模具费用分解表</p>
      	    <p style="font-weight:blod;font-size:30px">Tooling Cost Break Down</p>
      	</td>
      	<td>客户名称/Customer</td>
      	<td >
      	   <input type="text" name="client_name" />
      	 </td>
        </tr>
        <tr>
         <td>信箱/E-mail</td>
         <td>
             <input type="text" name="email" value="<?php echo $array_employee['email']; ?>" />
         </td>  
      </tr>
      </tr>
      <tr>    	
      	<td>项目名称/Program</td>
      	<td>
      	   <input type="text" name="project_name"/>
      	</td>
      </tr>
      <tr>
      	<td>联系人/Attention</td>
      	<td>
      	    <input type="text" name="contacts" value="<?php echo $array_employee['employee_name']; ?>">
      	</td>
      </tr>
      <tr>
      	<td>电话/TEL</td>
      	<td>
      	    <input  type="text" name="tel" value="<?php echo $array_employee['phone']; ?>"/>
      	</td>    
      </tr>
     <tr>
         <td>信箱/E-mail</td>
         <td>
             <input type="text" name="email" value="<?php echo $array_employee['email']; ?>"/>
         </td>  
      </tr>
      <tr>
        <td>模具名称/Mold Specification</td>
        <td>型腔数量/Cav.</td>
        <td width="350" rowspan="6" >
        		<div style="border:1px solid grey;width:300px;height:200px">
        			<p style="margin:90px auto">产品图片</p>
        		</div>
        </td>
        <td>产品零件号/Part No.</td>
        <td colspan="2">首次试模时间/T1 Time</td>
      </tr>
      <tr>
        <td><input type="text" name="mould_name" id="mould_name"  size="27" /></td>
        <td><select name="cavity_type" id="cavity_type" style="width:200px">
            <option value="">请选择</option>
            <?php
			foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){
				echo "<option value=\"".$cavity_type_key."\">".$cavity_type_value."</option>";
			}
			?>
          </select></td>
        <td><input type="text" name="part_number"  size="30" /></td>
        <td colspan="2"><input type="text" name="t_time"  size="35" /></td>
      </tr>
      <tr>
        <td>产品大小/Part Size (mm)</td>
        <td>产品重量/Part Weight(g)&nbsp;<span>材料/Material</span></td>
        <td>数据文件名/Drawing No.</td>
        <td colspan="2">最终交付时间/Lead Timeme</td>
      </tr>
      <tr>
        <td><input type="text" name="p_length" id="p_length"  size="5" />
          *
          <input type="text" name="p_width" id="p_width"  size="5" />
          *
          <input type="text" name="p_height" id="p_height"  size="5" /></td>
        <td>
        	<input type="text" name="p_weight" id="p_weight"  size="10" />        	
        	 <input type="text" name="m_material" id="m_material"  size="5" style="margin-left:48px" />
        </td>

        <td><input type="text" name="drawing_file"  size="30" /></td>
        <td colspan="2"><input type="text" name="lead_time"  size="35" /></td>
      </tr>
      <tr>
        <td>模具尺寸/Mold Size (mm)</td>
        <td>模具重量/Mold Weight(Kg)</td>
        <td>模具寿命/Longevity</td>
        <td colspan="2">设备吨位/Press(Ton)</td>
      </tr>
      <tr>
        <td><input type="text" name="m_length" id="m_length"  size="5" readonly="readonly" />
          *
          <input type="text" name="m_width" id="m_width"  size="5" readonly="readonly" />
          *
          <input type="text" name="m_height" id="m_height"  size="5" readonly="readonly" /></td>
        <td>
        	  <input type="text" name="m_weight" id="m_weight"  size="25" readonly="readonly" />
        </td>
        <td><input type="text" name="lift_time" id="lift_time"  size="30" /></td>
        <td colspan="2"><input type="text" name="tonnage"  size="35" /></td>
      </tr>
      </table>
      <div style="height:10px"></div>
     <!--加工材料费-->
     <style type="text/css" media="screen">
     	#heats tr td,#materials tr td,#molds_parts tr td,#basic_info tr td,#molds_designs tr td,#molds_others tr td,#molds_manus tr td{border:1px solid grey;}

     </style>
     <table id="materials" style="width:91%;" border="1" >
      <tr>
          <td colspan="8" style=";font-size:20px;font-weight:blod">材料加工费/Machining Materia</td>
      </tr>
    
      <tr >
         <td width="230">材料名称/Material</td>
         <td width="48">材料牌号/Specification</td>
         <td width="48">数量/Number</td>
         <td width="390">尺寸/Size(mm*mm*mm)</td>
         <td width="110">重量/Weight(kg)</td>
         <td>单价(元)/Unit Price</td>
         <td width="80">金额/Price(RMB)</td>
         <td width="185">小计(元)</td>
      </tr>
         <?php $i=0;foreach($array_mould_material as $mould_material_Key=>$mould_material_value){

      	?>
      <tr>
          <td>
              <input name="mould_material[]" id="mould_material" value=<?php echo $mould_material_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:15px">
          </td>
          <td>
              <select name="material_specification[]" id="material_specification"  style="width:100px">
                        <option value="">请选择</option>
                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>
              </select>
          </td>
          <td>
              <input type="text" name="number[]" id="number" size="11">
          </td>
                 <td>
              <input name="material_length[]" type="text" placeholder="长" size="7">
              *
              <input name="material_width[]" type="text" placeholder="宽" size="7">
              *
              <input name="material_height[]" type="text" placeholder="高" size="7">
          </td>   
           <td>
              <input type="text" name="material_weight[]" id="material_weight" size="12">
          </td>
           <td>
              <input type="text" name="unit_price[]" id="unit_price" value="70" size="15"/>
          </td>  
          <td>
              <input type="text" name="material_price[]" id="material_price" size="15">
          </td>
          <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="6" id="total_machining" ></td>  ';
          	     }
  	     $i++;
          ?> 
      	                          
      </tr>
   
      <?php } ?>
      <tr id="machining_materia"></tr>
      <tr >
          <td colspan="9" style="">
              <p id="add_materia" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>

      </table>
      <div style="height:10px">
      	
      </div>
      <!--热处理-->
      <table id="heats" style="width:91%">
        <tr>
            <td colspan="5" style="font-size:20px;font-weight:blod;">热处理/Heat Treatment</td>
      </tr>
         <tr>
          <td width="212">热处理名称/Item</td>
          <td>重量/weight(kg)</td>
          <td>单价/Unit Price(RMB)</td>
          <td>金额/Price(RMB)</td>
          <td width="52">小计(元)</td>
      </tr>
      <?php
      	$i = 0;
      	foreach($array_mould_heat as $mould_heat_key=>$mould_heat_value){
      ?>
      <tr>
          <td>
              <input name="mould_heat_name[]" id="mould_heat_name" value=<?php echo $mould_heat_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:15px" class="fix_txt">
          </td>
          <td>
              <input name="heat_weight" type="text" id="heat_weight">
          </td>        
          <td>
              <input name="heat_unit_price" type="text" id="heat_unit_price" value="24">
          </td>
          <td>
              <input name="heat_price" type="text" id="heat_price">
          </td>
           <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="3" id="total_heats"></td>  ';
          	     }
  	     $i++;
          ?> 
      	                          
      </tr>
   
 
      </tr>
      <?php } ?>
      <tr id="mould_heats"></tr>
        <tr >
          <td colspan="5" style="">
              <p id="add_heat" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
    </table>
    <div style="height:10px"></div>
      <!--模具配件-->
      <table id="molds_parts" style="width:91%">
      <tr>
          <td colspan="7" style=";font-size:20px;font-weight:blod">模具配件/Mold standard parts</td>
      </tr>
           <tr>
          <td width="212">装配件/Item</td>
           <td>规格型号/Specification</td>
          <td>品牌/Supplier</td>
          <td>数量/Number</td>
          <td>单价(元)/Unit Price</td>
          <td>金额(RMB)/price</td>
          <td>小计(元)</td>
      </tr>
      <?php
      	$i = 0;
      	foreach($array_mold_standard as $mold_standard_key=>$mold_standard_value){
      ?>
      <tr>
          <td>
             <input name="mold_standard[]" id="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mold_standard_value ?>">
         </td>
              <td>
                <input type="text" name="standard_specification[]">
          </td>
          <td>
             
                  <select name="standard_supplier[]" id="standard_supplier" style="width:150px">
                  	<option>请选择</option>
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
           <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="7" id="total_standard"></td>  ';
          	     }
  	     $i++;
          ?> 
      	      
        
      </tr>
        
     
       <?php } ?>
       <tr id="standard_parts"></tr>
      <tr>
          <td colspan="7" style="">
              <p id="add_standard" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
    </table>
      <!--设计费-->
      <div style="height:10px"></div>
    <table id="molds_designs" style="width:91%">
        <tr>
               <td colspan="5" style=";font-size:20px;font-weight:blod">设计费/Design</td>
        </tr>
           <tr>
            <td width="212">设计名称/Item</td>
            <td>工时(小时)/Hour</td>
            <td>单价(元)/Unit price(RBM)</td>
            <td>金额(元)/Price(RMB)</td>
            <td width="52">小计(元)</td>
        </tr>
         <?php 
            $i = 0;
        	  foreach($array_mould_design as $mould_design_key => $mould_design_value){	
        ?>
     
        <tr>
            <td>
                  <input name="mold_design_name[]" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mould_design_value ?>">
              
            </td>
            <td>
                <input type="text" name="design_hour[]" id="design_hour" value="109">
            </td>
            <td>
                <input type="text" name="design_unit_price[]" id="design_unit_price" value="100">
            </td>
            <td>
                <input type="text" name="design_price[]" id="design_price">
            </td>
                  <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="4" id="total_designs"></td>  ';
          	     }
  	     $i++;
          ?> 
        </tr>
        <?php }?>
       <tr id="designs"></tr>
      <tr>
          <td colspan="5" style="">
              <p id="add_designs" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
      </table>
        <!--加工费-->
       <table id="molds_manus" style="width:91%">
        <tr>
               <td colspan="5" style=";font-size:20px;font-weight:blod">加工费/Manufacturing Cost</td>
        </tr>
        <tr>
            <td width="212">名称/Item</td>
            <td>工时(小时)/Hour</td>
            <td>单价(元)/Unit Price(RMB)</td>
            <td>金额(元)/Price(RMB)</td>
            <td width="52">小计(元)</td>
        </tr>
        <?php 
        	$i = 0;
        	foreach($array_mould_manufacturing as $mould_manufacturing_key=>$mould_manufacturing_value){
        	?>
	        <tr>
	            <td>
	              <input name="mold_manufacturing[]" id="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:15px" value="<?php echo $mould_manufacturing_value ?>">
	         </td>
	         <td>
	             <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" value="124">
	         </td>
	         <td>
	             <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" value="100">
	         </td>
	         <td>
	             <input type="text" name="manufacturing_price[]" id="manufacuring_price"> 
	         </td>
	         <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="10" id="total_manufacturing"></td>  ';
          	     }
  	     $i++;
             ?> 
	        </tr>
	<?php } ?>
	<tr  id="manu_cost"></tr>
        <tr>
            <td colspan="5" style="">
              <p id="add_manu" style="width:200px;height;50px;border:1px solid grey;background:rgb(221,221,221);dispaly:inline-block;margin:10px auto;cursor:pointer">
                    添加项目
             </p>
          </td>
      </tr>
      </table>
      <div style="height:10px"></div>
        <!--其他费用-->
       <table id="molds_others" style="width:91%">
          <tr>
               <td colspan="5" style=";font-size:20px;font-weight:blod">其它费用/Other Fee</td>
        </tr>
        <tr>
        	<td width="212">费用名称</td>
        	<td colspan="2">费用计算说明</td>
        	<td>金额(元)</td>
        	<td width="52">小计(元)</td>
        </tr>
        <tr>
        	<td>试模费/Trial Fee</td>
        	<td colspan="2">3 times mold trial(excluding raw material cost)</td>
        	<td>
               <input type="text" name="trial_fee" id="trial_fee">	
        	</td>
        	<td rowspan="5" id="total_others"></td>
        </tr>
        <tr>
        	<td>运输费/Freight Fee</td>
        	<td colspan="2">sample and tooling transport cost paid by customer</td>
        	<td>
                <input type="text" name="freight_fee" id="freight_fee">
            </td>
        </tr>
        <tr>
        	<td>管理费/Management Fee</td>
        	<td colspan="2">5%</td>
        	<td>
                <input type="text" name="management_fee" id="management_fee">
            </td>
        </tr>
        <tr>
            <td>利润/Profit</td> 
            <td colspan="2">10%</td>  
            <td>
                <input type="text" name="profit" id="profit" >
            </td>         
        </tr>
           <tr>
            <td>税/VAT TAX(16%)</td>
            <td colspan="2">16%</td>
            <td>
                <input type="text" name="vat_tax" id="vat_tax">
            </td>
        </tr>
        <!--总计-->
          <tr>
               <td colspan="5" style=";font-size:20px;font-weight:blod">模具价格/Mold Price</td>
        </tr>
        <tr>
            <td>模具价格(元)不含税/Mold Price without VAT(RMB)</td>
            <td colspan="3">
                <input type="text" name="mold_price_rmb" id="mold_price_rmb">
            </td>
         <tr>
         	  <td>模具价格(USD)/Mold Price(USD) Rate=6.5</td>
         	  <td colspan="3">
                <input type="text" name="mold_price_usd" id="mold_price_usd">
            </td>
         </tr>
        </tr>
            <td>模具价格(元)含17%增值税/Mold with VAT(RMB)</td>
            <td colspan="3">
               <input type="text" name="mold_with_vat" id="mold_with_vat">
            </td>
        </tr>
      <tr>
        <td style="border-style:none" colspan="5" align="center"><input type="submit" name="submit" id="submit" value="确定" class="button" />
        	&nbsp;&nbsp;
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
        <td><input type="text" name="mould_name" id="mould_name" value="<?php echo $array['mould_name']; ?>"  size="35" /></td>
        <td><select name="cavity_type" id="cavity_type">
            <?php foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){ ?>
            <option value="<?php echo $cavity_type_key; ?>"<?php if($cavity_type_key == $array['cavity_type']) echo " selected=\"selected\""; ?>><?php echo $cavity_type_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="text" name="part_number"  value="<?php echo $array['part_number']; ?>" size="35" /></td>
        <td><input type="text    " name="t_time"  value="<?php echo $array['t_time']; ?>" size="35" /></td>
      </tr>
      <tr>
        <td>产品大小/Part Size (mm)</td>
        <td>产品重量/Part Weight(g) 材料/Material</td>
        <td>数据文件名/Drawing No.</td>
        <td>最终交付时间/Lead Timeme</td>
      </tr>
      <tr>
        <td><input type="text" name="p_length" id="p_length" value="<?php echo $array['p_length']; ?>"  size="10" placeholder="长" />
          *
          <input type="text" name="p_width" id="p_width" value="<?php echo $array['p_width']; ?>"  size="10"  placeholder="宽"/>
          *
          <input type="text" name="p_height" id="p_height" value="<?php echo $array['p_height']; ?>"  size="10" placeholder="高" /></td>
        <td><input type="text" name="p_weight" id="p_weight" value="<?php echo $array['p_weight']; ?>"  size="35" /></td>
        <td><input type="text" name="drawing_file" value="<?php echo $array['drawing_file']; ?>"  size="35" /></td>
        <td><input type="text" name="lead_time" value="<?php echo $array['lead_time']; ?>"  size="35" /></td>
      </tr>
      <tr>
        <td>模具尺寸/Mold Size (mm)</td>
        <td>模具重量/Mold Weight(Kg)</td>
        <td>模具寿命/Longevity</td>
        <td>设备吨位/Press(Ton)</td>
      </tr>
      <tr>
        <td><input type="text" name="m_length" value="<?php echo $array['m_length']; ?>" id="m_length"  size="10" readonly="readonly" />
          *
          <input type="text" name="m_width" id="m_width" value="<?php echo $array['m_width']; ?>"  size="10" readonly="readonly" />
          *
          <input type="text" name="m_height" id="m_height" value="<?php echo $array['m_height']; ?>"  size="10" readonly="readonly" /></td>
        <td><input type="text" name="m_weight" id="m_weight" value="<?php echo $array['m_weight']; ?>"  size="35" readonly="readonly" /></td>
        <td><input type="text" name="lift_time" id="lift_time" value="<?php echo $array['lift_time']; ?>"  size="35" /></td>
        <td><input type="text" name="tonnage" value="<?php echo $array['tonnage']; ?>"  size="35" /></td>
      </tr>
      <tr>
        <td>客户名称/Customer</td>
        <td>项目名称/Program</td>
        <td>联系人/Attention</td>
        <td>电话/TEL</td>
      </tr>
      <tr>
        <td><input type="text" name="client_name" value="<?php echo $array['client_name']; ?>"  size="35" /></td>
        <td><input type="text" name="project_name" value="<?php echo $array['project_name']; ?>"  size="35"/></td>
        <td><input type="text" name="contacts" value="<?php echo $array['contacts']; ?>"  size="35" /></td>
        <td><input type="text" name="tel" value="<?php echo $array['tel']; ?>"  size="35" /></td>
      </tr>
      <tr>
        <td colspan="4">信箱/E-mail</td>
      </tr>
      <tr>
        <td colspan="4"><input type="text" name="email" value="<?php echo $array['email']; ?>"  size="35" /></td>
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