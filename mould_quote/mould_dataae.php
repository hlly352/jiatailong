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
	function count_trs(trs_name,tds_name,trs_total){
		//判断型腔数对合并行的影响
		  var num = $(trs_name).size()+3;	
		
 		$(tds_name).attr('rowspan',num);
 		$(trs_total).attr('rowspan',num-1);
	}

	//统计第一列需要合并的单元格个数
	function count_tr(trs_name,tds_name,trs_total){
		//判断型腔数对合并行的影响
		  var num = $(trs_name).size()+2;	

 		$(tds_name).attr('rowspan',num);
 		$(trs_total).attr('rowspan',num-1);
	}
	//删除行时统计第一列需要合并的单元格个数
	function count_del_trs(trs_name,tds_name,trs_total){
		//判断型腔数对合并行的影响
		 var num = $(trs_name).size()+2;
 		$(tds_name).attr('rowspan',num);
 		$(trs_total).attr('rowspan',num-1);
	}

	
	//更改重量和单价后计算热处理的金额
	$(".heat_weight,.heat_unit_price").live("blur",function(){
		if($(this).attr('class') == 'heat_weight'){
			
			var heats_totals = ($(this).val())*( $(this).parent().next().children().val());
		}else{
			var heats_totals = ($(this).val())*($(this).parent().prev().children().val());
		}
		 $(this).parent().parent().children().eq(3).text(heats_totals);
		 sum_tds(".heat_trs",1,2,"#total_heats");
		 //计算其它费用及模具价格
	    	sum_other_fee();

	})
	//更改配件数量和单价后计算模具配件的金额
	$(".standard_number,.standard_unit_price").live("blur",function(){
		if($(this).attr('class') == 'standard_number'){
			var standard_totals = ($(this).val())*( $(this).parent().next().children().val());
		} else {
		          var standard_totals = ($(this).val())*($(this).parent().prev().children().val());
		}
		$(this).parent().parent().children().eq(5).text(standard_totals);
		sum_tds(".parts_trs",3,4,"#total_standard")
		//计算其它费用及模具价格
	    	sum_other_fee();
	})
	//动态计算小计的金额
	//tr_val   需要求值行的类名
	//d1,d2,d4  需要求值的乘数的列号
	//d3        需要填写乘积的类名      
	
	function sum_tds(tr_val,d1,d2,d3){
		var s = 0;
		var tr1 = 0;
		var tr2 = 0;
		var tr_nu = $(tr_val);

		for(var i=0;i<(tr_nu.size());i++){
			var tr1 = (tr_nu.eq(i).children().eq(d1).children().val())? (tr_nu.eq(i).children().eq(d1).children().val()):'0';
			 var tr2 = (tr_nu.eq(i).children().eq(d2).children().val())? (tr_nu.eq(i).children().eq(d2).children().val()):'0';
			 s += tr1*tr2;
		}
		$(d3).children().val(s);
			
	}
	function sum_other_fee(){
		var min_tot = 0;
	   	 var tot_num = $(".min_total").size();
	   	 for(var x = 0;x<tot_num;x++){
	   	 	var mins_tot = ($(".min_total").eq(x).val())?($(".min_total").eq(x).val()):0;
	   	 	min_tot +=(parseInt(mins_tot));
	   	 }
	   	//计算管理费,利润和税
	   	//先统计除了利润,税,管理费的固定费用
	   	var fixed_num = $(".fixed_fee").size();
	   	var fixed_fee_sum = 0;
	   	for(var o = 0 ;o<fixed_num;o++){
	   		var mins_fixed_fee = $(".fixed_fee").eq(o).val()?$(".fixed_fee").eq(o).val():0;
	   		fixed_fee_sum += parseInt(mins_fixed_fee);
	   	}
	 
	   	var sum_except_others =  (parseInt(fixed_fee_sum) + parseInt(min_tot));
	   	$("#management_fee").val(parseInt(sum_except_others*0.05));
	   	$("#profit").val(parseInt(sum_except_others*0.1));
	   	$("#vat_tax").val(parseInt(sum_except_others*0.16));
	   	//计算其它费用的小计
	   	var other_num = $(".other_fee").size();
	   	var tot_others = 0;
	   	for(var v=0;v<other_num;v++){
	   		var mins_other_fee = $(".other_fee").eq(v).val()?$(".other_fee").eq(v).val():0;
	   		tot_others += parseInt(mins_other_fee);
	   	}
	   	$("#tot_others").val(tot_others);
	   	//计算模具的价格
	   	var price_with_vat = tot_others+min_tot;
	   	$("#mold_price_rmb").val(price_with_vat - parseInt(sum_except_others*0.16));
	   	$("#mold_price_usd").val(parseInt(price_with_vat/6.5));
	   	$("#mold_with_vat").val(price_with_vat);
	}
	//删除材料加工时重新计算总金额
	$(".material_dels").live('click',function(){
		$(this).parent().parent().remove();
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".material_trs","#material_first_td","#total_machining");
     
	})
	//删除热处理数据时重新计算总金额
	$(".heat_dels").live('click',function(){
		$(this).parent().parent().remove();
		sum_tds(".heat_trs",1,2,"#total_heats");
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".heat_trs","#heat_first_td","#total_heats");
     
	})
	//删除模具配件费是重新计算总金额
	$(".standard_dels").live("click",function(){
		$(this).parent().parent().remove();
		sum_tds(".parts_trs",3,4,"#total_standard")
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".parts_trs","#parts_first_td","#total_standard");
	})
	//删除设计费是重新计算总金额
	$(".design_dels").live("click",function(){
		$(this).parent().parent().remove();
		sum_tds(".design_trs",1,2,"#total_designs");
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".design_trs","#design_first_td","#total_designs");
	})
	//删除加工费是重新计算总金额
	$(".manu_dels").live("click",function(){
		$(this).parent().parent().remove();
		sum_tds(".manus_trs",1,2,"#total_manufacturing")
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".manus_trs","#manus_first_td","#total_manufacturing");
	})
	//删除其它费用是重新计算总金额
	$(".other_dels").live("click",function(){
		$(this).parent().parent().remove();
		sum_tds(".others_trs",1,2,"#total_others")
		//删除行时重新统计第一列需要合并的单元格列数
		count_del_trs(".others_trs","#others_first_td","#total_others");
	})
          //动态添加工材料费
          $('#add_material').click(function(){
          	     var trs = '     <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" id="mould_material" class="mould_material" style="color:black;font-weight:150;font-size:13px;width:150px">     <p class="material_dels dels">删除</p>           </td>               <td>               	<select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" >             </td>             <td>                   <input name="material_length[]" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td>                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td>                  <input name="material_height[]" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" value="70"/>             </td>             <td>                 <input type="text" name="material_price[]" id="material_price"> 	             </td>       </tr>';
              
        	           count_trs(".material_trs","#material_first_td","#total_machining");
     
                         $('#machining_material').before(trs);
                   
          })
       
          //动态添加热处理
          $('#add_heat').click(function(){
          		var heats = '  <tr class="heat_trs">              <td colspan="4">               <input name="mould_heat_name[]" id="mould_heat_name"  style="color:black;font-weight:150;font-size:13px;width:150px">        <p class="dels heat_dels">删除</p>           </td>              <td colspan="2">                  <input name="heat_weight" type="text" class="heat_weight">              </td>              <td colspan="6">                 <input name="heat_unit_price" type="text" class="heat_unit_price" value="24">	              </td>              <td colspan="2">                <input name="heat_price" type="text" id="heat_price" disabled value="0" style="border-style:none">              </td>              </tr>';
          		count_trs(".heat_trs","#heat_first_td","#total_heats");
     
          		$('#mould_heats').before(heats);
          })  
          //动态添加模具配件费
          $('#add_standard').click(function(){
          	         var standard = '<tr class="parts_trs">       	<td colspan="4">      	     <input name="mold_standard[]" id="mold_standard" style="color:black;font-weight:150;font-size:13px;width:150px" >    <p class="dels standard_dels">删除</p>    	</td>      	<td colspan="2">      	    <input type="text" name="standard_specification[]" id="standard_specification">      	</td>      	<td colspan="5">      	     <select name="standard_supplier[]" id="standard_supplier" >                  	<option>请选择</option>                  </select>      	</td>      	<td>      	    <input type="text" name="standard_number[]" class="standard_number">	      	</td>      	<td>               <input type="text" name="standard_unit_price[]" class="standard_unit_price" value="2000">      	</td>      	<td>      	   <input type="text" name="standard_price[]" id="standard_price">	      	</td>   	      </tr>';
               	  count_trs(".parts_trs","#parts_first_td","#total_standard");
                        $('#standard_parts').before(standard);
          })
          //动态添加设计费
          $('#add_designs').click(function(){
          		var design_adder = '    <tr class="design_trs">              <td colspan="4">                  <input name="mold_design_name[]" id="mold_design_name" class="mold_design_name" style="color:black;font-weight:150;font-size:13px;width:150px">           <p class="dels design_dels">删除</p>                 </td>              <td colspan="2">                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" value="109">              </td>              <td colspan="6">                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value="100">              </td>              <td colspan="2">                <input type="text" name="design_price[]" class="design_price" id="design_price">              </td>        </tr>';
          		count_trs(".design_trs","#design_first_td","#total_designs");
          		$('#designs').before(design_adder);

  		
          })
          //动态添加加工费
          $('#add_manu').click(function(){
     		var manu = '<tr class="manus_trs">              <td colspan="4">                  <input name="mold_manufacturing[]" id="mold_manufacturing" style="color:black;font-weight:150;font-size:13px;width:150px">           <p class="dels manu_dels">删除</p>                  </td>              <td colspan="2">                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" value="124">              </td>              <td colspan="6">                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" value="100">              </td>              <td colspan="2">               <input type="text" name="manufacturing_price[]" id="manufacuring_price">               </td>            </tr>';
             	count_trs(".manus_trs","#manus_first_td","#total_manufacturing");
                     $('#manu_cost').before(manu);
          })
          //动态添加其它费用
          $("#add_others").click(function(){
          	     var others_adder = '   <tr class="others_trs">  <td colspan="4"><input name="other_fee_name[]" id="other_fee_name" style="color:black;font-weight:150;font-size:13px;width:150px"><p class="dels other_dels">删除</p></td>          	   <td colspan="8"><input name="other_fee_instr[]" id="other_fee_instr" class="other_fee fixed_fee" style="color:black;font-weight:150;font-size:13px;width:300px" ></td>          	   <td colspan="2"> <input name="other_fee_price[]" id="other_fee_price" >    </td>        </tr>';
          	     	count_trs(".others_trs","#others_first_td","#total_others");
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
	//型腔数量变化,动态改变表格样式
	$("#cavity_type").change(function(){
		 mold_name = $("#cavity_type").val();
		 $(".dels").parent().parent().remove();
		$("#cavitys_type").val(mold_name);
		//通过隐藏的iframe提交表单,实现不刷新
		$("#form1").attr("target", "frameFile");
		$("#form1").submit();
		var material_add_cavity = ' <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" class="mould_material only_add" value="型腔2/Cavity" disabled style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled>               </td>               <td>               	<select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" class="materials_number" >             </td>             <td style="width:93px">                   <input name="material_length[]" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" value="70"/>             </td>   <td>                 <input type="text" name="material_price[]" id="material_price"> 	             </td></tr>';
   		var material_add_core = ' <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" class="mould_material only_add" value="型芯2/Core" disabled style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled>               </td>               <td>               	<select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" >             </td>             <td style="width:93px">                   <input name="material_length[]" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>             </td>   <td>                 <input type="text" name="material_price[]" id="material_price"> 	             </td></tr>';
		
		count_trs(".material_trs","#material_first_td","#total_machining");
		if($("#cavity_type").val() == "A"||$("#cavity_type").val()== "B" || $("#cavity_type").val() == "C"){
			
			$("#p_length").next().remove();
			$("#p_width").next().remove();
			$("#p_height").next().remove();
			$(".only_add").parent().parent().remove();
			// 初始化合并的行数
			$("#material_first_td").attr('rowspan',8);
			$("#total_machining").attr('rowspan',7);
			
		} else {
			$("#p_length").next().remove();
			$("#p_width").next().remove();
			$("#p_height").next().remove();
			var input_length = '<input type="text" name="p_length_other">';
			var input_width = '<input type="text" name="p_width_other">';
			var input_height = '<input type="text" name="p_height_other">';
			$("#p_length").after(input_length);
			$("#p_width").after(input_width);
			$("#p_height").after(input_height);
			//先删除添加过的td项
			$(".only_add").parent().parent().remove();
			//初始化合并的行数
			$("#material_first_td").attr('rowspan',10);
			$("#total_machining").attr('rowspan',9);
			$("#material_last_tr").next().next().after(material_add_cavity);
			$("#material_last_tr").next().next().next().next().after(material_add_core);
		}

     
	})
	//动态添加型腔数量输入框
	var add_cavity = '<input type="text" class="cavity_type" id="add_cav" value="1" name="cavity_type[]" style="width:25px">';
	var del_cavity = '  <button id="del_cavitys" type="button">删除</button>';
	var add_materials = '     <?php
	 
               foreach($array_mould_material as $mould_material_key=>$mould_material_value){
      	?>           <tr class="material_trs materia_tr">               <td colspan="4">                  <input name="mould_material[]" class="mould_material material_input" value=<?php echo $mould_material_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled>               </td>               <td>               	<select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php

      		       unset($array_material_specification['base']);
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value="1">             </td>             <td style="width:93px">                   <input name="material_length[]" id="material_length" class="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" id="material_width" class="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" id="material_height" class="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" class="material_weight" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" class="material_unit_price" id="material_unit_price" value="70"/>             </td>             <td>                 <input type="text" name="material_price[]" id="material_price" class="material_price"> 	             </td>        </tr> <?php } ?>';
	//添加删除按钮
	$("#add_cavitys").one('click',function(){

		$(this).after(del_cavity);
		$(this).before(add_cavity);
		$("#machining_material").before(add_materials);
		//初始化合并的单元格行数
		$("#material_first_td").attr('rowspan',15);
		$("#total_machining").attr('rowspan',14);
	        	j = 1;
	})
	 m = 0;
	//页面加载完成遍历材料名称把点击数改为2
	var mould_num = $(".mould_material").size();
		
		for(var i=0;i<mould_num;i++){
			if($(".mould_material").eq(i).val() == '电极/Electrode'){
				$(".materials_number").eq(i).val(2);
			} 
			
			
		}
		
	//点击添加按钮
	$("#add_cavitys").click(function(){
		if($(this).nextAll().size() == 0){	
		    $(this).after(del_cavity);
		}
		$(".material_dels").parent().parent().remove();
		if(j != 1){
		$(this).before(add_cavity);
		$("#machining_material").before(add_materials);
		
		count_tr(".material_trs","#material_first_td","#total_machining");
		}
		j +=1;
		var input_length = '<input type="text" name="p_length[]" id="p_length" class="p_length"/>';
		var input_width  = '<input type="text" name="p_width[]" id="p_width" class="p_width"/>';
		var input_height = '<input type="text" name="p_height[]" id="p_height" class="p_height"/>';
		var input_part    ='<input type="text" name="part_number[]" id="part_number" class="part_number" style="width:182px"/>';
		var input_file      ='<input type="text" name="drawing_file[]" id="drawint_file" class="input_tx" style="width:310px"/>';
		var input_weight =' <input type="text" name="p_weight[]" id="p_weight" class="input_tx"/>';
		var input_material='<input type="text" name="m_materia[]l" id="m_material" class="input_tx"/>';
		$("#pp_length").before(input_length);
		$("#pp_width").before(input_width);
		$("#pp_height").before(input_height);
		$("#pp_number").before(input_part);
		$("#pp_file").before(input_file);
		$("#pp_weight").before(input_weight);
		$("#pp_material").before(input_material);
		//给添加的材料名称后面添加下标
		var mould_num = $(".mould_material").size();
		var cavity_number = $(this).prevAll().size()-1;
		for(var i=0;i<mould_num;i++){
			switch($(".mould_material").eq(i).val()) {
				case  '型腔/Cavity':
					m +=1;
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
				case '型芯/Core':
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
				case '滑块/Slide&Lifter':
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
				case '斜顶/Lifter':
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
				case '镶件/Insert':
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
				case '电极/Electrode':
					var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
				           $(".mould_material").eq(i).val(new_mould);
					break;
			}

			
		}
		//添加时更改电极数
		for(var i=0;i<mould_num;i++){
			if($(".mould_material").eq(i).val() == '电极'+cavity_number+'/Electrode'){
				$(".materials_number").eq(i).val(2);
			} 	
		}
		

	})
	//k_num选项更改时
	$("#k_num").change(function(){
		var k_num = $(this).val();
		var cavity_number = $("#add_cavitys").prevAll().size();
		var mould_num = $(".mould_material").size();
			//遍历材料的名称
			for(var i=0;i < mould_num;i++){
			
					if(k_num == '2'){
						var core_num = $(".materials_number").eq(i).val()*2;
						$(".materials_number").eq(i).val(core_num);	
					} else {
						var core_num = $(".materials_number").eq(i).val()/2;
						$(".materials_number").eq(i).val(core_num);	
				
					
				}
		}


	})
	//型腔类型发生变化
	$(".cavity_type").live('change',function(){
		var cavity_val = $(this).val();
		var cavity_number = $(this).prevAll().size();
		var mould_num = $(".mould_material").size();
		var k_num = $("#k_num").val();
		//动态更改型腔的数量
		//当只有一个输入框时更给型腔数
		if($("#add_cavitys").prevAll().size() == 2) {
			cavity_number = '';
		}
			for(var i=0;i<mould_num;i++){
				if($(".mould_material").eq(i).val() == '型腔'+cavity_number+'/Cavity' || $(".mould_material").eq(i).val() == '型芯'+cavity_number+'/Core' ||  $(".mould_material").eq(i).val() == '滑块'+cavity_number+'/Slide&Lifter' || $(".mould_material").eq(i).val() == '镶件'+cavity_number+'/Insert' || $(".mould_material").eq(i).val() == '斜顶'+cavity_number+'/Lifter'){
					 $(".materials_number").eq(i).val(cavity_val*k_num);
				}
				if($(".mould_material").eq(i).val() == '电极'+cavity_number+'/Electrode'){
					 $(".materials_number").eq(i).val(cavity_val * 2*k_num);
				}
		}
	
	})
	//删除型腔类型
	$("#del_cavitys").live('click',function(){
		//只有一个输入框时去除删除按钮
		if($("#add_cavitys").prevAll().size() == 3){
			$(this).remove();
			m = 0;
			var mould_num = $(".mould_material").size();
			for(var i=0;i<mould_num;i++){
				switch($(".mould_material").eq(i).val()) {
					case  '型腔1/Cavity':
						var new_mould =  '型腔/Cavity';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '型芯1/Core':
						var new_mould = '型芯/Core';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '滑块1/Slide&Lifter':
						var new_mould = '滑块/Slide&Lifter';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '斜顶1/Lifter':
						var new_mould = '斜顶/Lifter';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '镶件1/Insert':
						var new_mould = '镶件/Insert';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '电极1/Electrode':
						var new_mould = '电极/Electrode';
					           $(".mould_material").eq(i).val(new_mould);
						break;
					}
			}
		}
		//删除已经添加的项目
		$(".material_dels").parent().parent().remove();
		$("#add_cavitys").prev().remove();
		//删除尺寸输入框
		$("#pp_length").prev().remove();
		$("#pp_width").prev().remove();
		$("#pp_height").prev().remove();
		$("#pp_number").prev().remove();
		$("#pp_file").prev().remove();
		$("#pp_weight").prev().remove();
		$("#pp_material").prev().remove();
		//删除加工型腔类型输入框
		$("#machining_material").prevAll().slice(0,6).remove();
		count_tr(".material_trs","#material_first_td","#total_machining");
		//给减少的材料名称后面添加下标
		var mould_num = $(".mould_material").size();
		var cavity_number = $(this).prevAll().size()-2;
		     if($("#add_cavitys").prevAll().size() >3){
			for(var i=0;i<mould_num;i++){
				switch($(".mould_material").eq(i).val()) {
					case  '型腔/Cavity':
						m -=1;
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '型芯/Core':
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '斜顶/Lifter':
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '滑块/Slide&Lifter':
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '镶件/Insert':
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
					case '电极/Electrode':
						var new_mould = $(".mould_material").eq(i).val().replace(/\//,m+"/");
					           $(".mould_material").eq(i).val(new_mould);
						break;
				}

			}
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
			//$(this).val(parseFloat(p_value).toFixed(1));
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
	//输入产品大小后对相关数据进行计算
	$(".p_length,.p_width,.p_height").live('blur',function(){
		//判断是第几个输入框
		var no = $(this).prevAll().size();
		var no1 = no+1;
		var p_length = $("#length_no").children().eq(no).val();
		var p_width = $("#width_no").children().eq(no).val();
		var p_height = $("#height_no").children().eq(no).val();
		var mould_num = $(".mould_material").size();
		var total_machining = 0;
		if($.trim(p_length) && $.trim(p_width) && $.trim(p_height)){
			
			if($("#add_cavitys").prevAll().size() == 2) {
			no1 = '';

			}
			var weight_g = (p_length - 4) * (p_width - 4) * (p_height - 2);
			//输入产品大小后计算型腔的尺寸
			for(var i = 0;i < mould_num; i++){
				if($(".mould_material").eq(i).val() == '型腔'+no1+'/Cavity' || $(".mould_material").eq(i).val() == '型芯'+no1+'/Core' ){
					 $(".material_length").eq(i).val(parseFloat(p_length)+110);
					 $(".material_width").eq(i).val(parseFloat(p_width)+110);
					 $(".material_height").eq(i).val(parseFloat(p_height)+100);
					//输入产品大小后计算型腔和型芯的重量
					var wei = ((parseFloat(p_length)+110)/1000)*((parseFloat(p_width)+110)/1000)*((parseFloat(p_height)+100)/1000)*7800;
					wei = Math.round(wei);
					$('.material_weight').eq(i).val(wei);
					
				} else if($(".mould_material").eq(i).val() == '电极'+no1+'/Electrode'){
					 $(".material_length").eq(i).val(parseFloat(p_length)+100);
					 $(".material_width").eq(i).val(parseFloat(p_width)+100);
					 $(".material_height").eq(i).val(150);
					//输入产品大小后计算电极的重量
					var wei = ((parseFloat(p_length)+100)/1000)*((parseFloat(p_width)+100)/1000)*(150/1000)*8900;
					wei = Math.round(wei);
					$('.material_weight').eq(i).val(wei);
				}
				//输入产品大小后计算材料费的金额
			 	var prices  = ($(".material_weight").eq(i).val())*($(".materials_number").eq(i).val())*($(".material_unit_price").eq(i).val());
			 	
				$(".material_price").eq(i).val(prices);
				total_machining += parseFloat($(".material_price").eq(i).val());

			}
		//计算总金额
		$("#total_machining").children().val(total_machining);
		//输入产品大小后计算淬火的重量
		var handened_weight = 0;
		for(var j = 0;j < mould_num; j++){
			if($(".mould_material").eq(j).val().indexOf('型腔') !=-1 || $(".mould_material").eq(j).val().indexOf('型芯') !=-1 || $(".mould_material").eq(j).val().indexOf('滑块') !=-1 || $(".mould_material").eq(j).val().indexOf('镶件') !=-1 ){
				var res_weight = parseInt($(".material_weight").eq(j).val())?parseInt($(".material_weight").eq(j).val()):0;
				handened_weight +=res_weight;

			}
			}
			$(".heat_weight").eq(1).val(handened_weight);
			//输入产品大小后设置热处理的金额计算
	 		sum_tds(".heat_trs",1,2,"#total_heats");
	 		//输入产品大小后计算设计费的工时
			var design_unit_hour = Math.round(total_machining*0.15/100/4);
			var design_num = $(".design_hour").size();
			for(var n=0;n<design_num;n++){
				$(".design_hour").eq(n).val(design_unit_hour);
			}
			//输入产品大小后计算加工费的工时
			var manu_unit_hour = total_machining*1.5/100/10;
			var manu_num = $(".mold_manufacturing").size();
			
			for(var e = 0; e<manu_num;e++){
				switch($(".mold_manufacturing").eq(e).val()) {
					case '一般机床/Maching':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour);
						break;
					case '磨床/Grinding':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*0.6);
						break;
					case '数控机床/CNC':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*1.2);
						break;
					case '精密数控机床':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*0.8);
						break;
					case '线切割/W.C.':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*0.8);
						break;
					case '电火花/EDM':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour);
						break;
					case '抛光/Polish':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour);
						break;
					case '钳工/Fitting':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*0.8);
						break;
					case '激光烧焊/Laser Welding':
						$(".manufacturing_hour").eq(e).val(manu_unit_hour*0.5);
						break;
				}
			//产品大小输入后计算设计费金额
			$(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
			
			sum_tds(".design_trs",1,2,"#total_designs");
			//产品大小输入后计算加工费金额
			$(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
			}
			sum_tds(".manus_trs",1,2,"#total_manufacturing")
		

		}
		if(weight_g > 0 && weight_g != ' '){	
		$("#weight_no").children().eq(no).val(weight_g);
	    }
	    //产品大小输入后计算其它费用
	    if($.trim(p_length) && $.trim(p_width) && $.trim(p_height)){
	    	//计算其它费用及模具价格
	    	sum_other_fee();
	    }
	})
	
	//更改配件数量和单价后计算配件费的金额
	$(".standard_number,.standard_unit_price").live("blur",function(){
	if($(this).attr('class') == 'standard_number'){
		var standard_totals = ($(this).val())*( $(this).parent().next().children().val());
		} else {
	          var standard_totals = ($(this).val())*($(this).parent().prev().children().val());
		}
		$(this).parent().parent().children().eq(5).text(standard_totals);
		sum_tds(".parts_trs",3,4,"#total_standard")
		//计算其它费用及模具价格
	    	sum_other_fee();
		})
	
	//加载完成后计算配件费
	var standard_nu = $(".standard_number").size();

	for(var s=0;s<=standard_nu;s++){
		//设置默认单价
		switch($(".mold_standard").eq(s).val()) {
			case '镶件、日期章/Inserts':
				$(".standard_unit_price").eq(s).val(5000);
				break;
			case '顶杆、顶管/Ejection Pin\\Sleeve':
				$(".standard_unit_price").eq(s).val(5000);
				break;
			case '水管、油管接头/Connector':
				$(".standard_unit_price").eq(s).val(8000);
				break;
			case '标准件/Standard Components':
				$(".standard_unit_price").eq(s).val(16000);
				break;
			case '热流道/Hot Runner':
				$(".standard_unit_price").eq(s).val(10000);
				break;
			case '温控器/Temp Controller':
				$(".standard_unit_price").eq(s).val(8000);
				break;
			case '油缸/Hydro-cylinder':
				$(".standard_unit_price").eq(s).val(8000);
				break;
		}
		//计算配件费金额
		$(".standard_price").eq(s).val(($(".standard_number").eq(s).val())*($(".standard_unit_price").eq(s).val()));
		
	}
	sum_tds(".parts_trs",3,4,"#total_standard")
	//更改材料尺寸后重新计算金额
	$(".material_length,.material_width,.material_height").live('blur',function(){
		//重新计算材料费的金额
		var no = $(this).prevAll().size();
		var no2 = no+1;
		var total_machinings = 0;
		if($("#add_cavitys").prevAll().size() == 2) {
			no2 = '';

			}
		if(($.trim($(this).parent().parent().children().find('.material_length').val())) && ($.trim($(this).parent().parent().children().find('.material_width').val()))  && ($.trim($(this).parent().parent().children().find('.material_height').val())) ){
			for(var t=0;t<mould_num;t++){
				var material_len = $(".material_length").eq(t).val()?$(".material_length").eq(t).val():0;
				var material_wid = $(".material_width").eq(t).val()?$(".material_width").eq(t).val():0;
				var material_hei = $(".material_height").eq(t).val()?$(".material_height").eq(t).val():0;
				if($(".mould_material").eq(t).val() == '型腔'+no2+'/Cavity' || $(".mould_material").eq(t).val() == '型芯'+no2+'/Core' ){
						//计算型腔和型芯的重量
						var wei = (parseFloat(material_len))*(parseFloat(material_wid))*(parseFloat(material_hei))*7800/1000000000;
						wei = Math.round(wei);
						$('.material_weight').eq(t).val(wei);
						
					} else if($(".mould_material").eq(t).val() == '电极'+no2+'/Electrode'){
						 
						//计算电极的重量
						var wei = (parseFloat(material_len))*(parseFloat(material_wid))*(parseFloat(material_hei))*8900/1000000000;
						wei = Math.round(wei);
						$('.material_weight').eq(t).val(wei);
					}
				var prices  = ($(".material_weight").eq(t).val())*($(".materials_number").eq(t).val())*($(".material_unit_price").eq(t).val());
					 	
				$(".material_price").eq(t).val(prices);
				total_machinings += parseInt($(".material_price").eq(t).val());
			}
			//计算总金额
			$("#total_machining").children().val(total_machinings);
			//计算其它费用及模具价格
	    		sum_other_fee();
		}
	})
	//更改材料重量和单价后重新计算金额
	$(".material_weight,.material_unit_price").live('blur',function(){
		var total_machinings = 0;
		for(var t=0;t<mould_num;t++){
			var prices  = ($(".material_weight").eq(t).val())*($(".materials_number").eq(t).val())*($(".material_unit_price").eq(t).val());
				 	
			$(".material_price").eq(t).val(prices);
			total_machinings += parseInt($(".material_price").eq(t).val());
		}
		//计算总金额
		$("#total_machining").children().val(total_machinings);
		//计算其它费用及模具价格
	    	sum_other_fee();

	})
	//更改设计费的单价和工时后重新计算金额
	$(".design_hour,.design_unit_price").live('blur',function(){
		//计算设计费金额
		
		var design_nu = $(".design_hour").size();
		for(var e = 0; e<design_nu;e++){

			$(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
				
		}
		//计算设计费小计
		sum_tds(".design_trs",1,2,"#total_designs");
		//计算其它费用及模具价格
	    	sum_other_fee();
	})
	//更改加工费的单价和工时后重新计算金额
	$(".manufacturing_hour,.manufacturing_unit_price").live('blur',function(){
		//计算设计费金额
		var design_nu = $(".manufacturing_hour").size();
		for(var e = 0; e<design_nu;e++){
			$(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
				
			
		}
		//计算加工费小计
		sum_tds(".manus_trs",1,2,"#total_manufacturing");
		//计算其它费用及模具价格
	    	sum_other_fee();
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
  <!--提交型腔的数量类型-->
 <form action="" method="post" name="form1">
  	<input type="hidden" value="" id="cavitys_type" name="cavity_types</form>
 <iframe id='frameFile' name='frameFile' style='display: none;'></iframe>
 
  <form action="mould_datado.php" name="mould_data" method="post">
  <style type="text/css" media="screen">
  	#main_table tr td{border:1px solid grey;}
  	input{width:80px;}
  	*{margin:0px;}
  	.dels{display:inline;float:right;width:45px;height:3px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);cursor:pointer}
  	.adder{width:100px;height:30px;line-height:30px;text-align:center;display:block;border:1px solid grey;background-color:rgb(221,221,221);margin:2px auto;cursor:pointer;font-size:15px;}
  </style>
   <table id="main_table" style="word-wrap: break-word; word-break: break-all;">
   	<!--基本信息-->
   	<tr>
   	     <td colspan="5" rowspan="5">
   	     	<img src="" alt="logo">
   	     </td>
   	     <td colspan="9" rowspan="5" style="width:661px" >
   	     	 <p style="font-weight:blod;font-size:30px">模具费用分解表</p>
      	            <p style="font-weight:blod;font-size:30px">Tooling Cost Break Down</p>
   	     </td>
   	   <td style="width:186px;padding-right:20px">客户名称/Customer</td>
      	   <td style="width:186px;padding-right:0px">
      	       <input type="text" name="client_name" style="width:125px;" />
      	   </td>	
   	</tr>
   	<tr>
   	  <td>项目名称/Program</td>
             <td>
      	     <input type="text" name="project_name" style="width:125px"/>
      	  </td>
   	</tr>
   	<tr>
      	  <td>联系人/Attention</td>
      	  <td>
      	    <input type="text" name="contacts" value="<?php echo $array_employee['employee_name']; ?>" style="width:125px">
      	  </td>
           </tr>
           <tr>
      	  <td>电话/TEL</td>
      	  <td>
      	    <input  type="text" name="tel" value="<?php echo $array_employee['phone']; ?>" style="width:125px"/>
      	  </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="<?php echo $array_employee['email']; ?>" style="width:125px"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5" >模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav. Number</td>
               <td colspan="5" rowspan="6">
               	<div style="border:1px solid grey;">
        			<p style="margin:0px auto">产品图片</p>
        		</div>
               </td>
               <td colspan="2">首次试模时间/T1 Time</td>
          	     <td colspan="2">最终交付时间/Lead Timeme</td>
           </tr>
           <tr>
               <td colspan="5" style="padding-right:2px">
               	<input  type="text" name="mould_name" id="mould_name" class="input_tx"  style="width:315px;margin-right:2px"/>
               </td>
               <td colspan="2" id="cavity_no">
               	<!-- <select name="cavity_type" id="cavity_type" style="">
                        <option value="">请选择</option>
                          <?php
			foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){
				echo "<option value=\"".$cavity_type_key."\">".$cavity_type_value."</option>";
			}
			?>
                     </select> -->
                     <select name="k_num" id="k_num">
			<option value="1">1k</option>
			<option value="2">2k</option>
                     	</select>
                     <input type="text" name="cavity_type[]" class="cavity_type" id="types_cav" value="1" style="width:25px">
                     <button id="add_cavitys" type="button">添加</button>

               </td>
               <td colspan="2" style="padding_style:8px">
               	<input type="text" name="t_time" class="input_tx" style="width:182px"/>
              </td>
              <td colspan="2">
              	<input type="text" name="lead_time" class="input_tx" style="width:310px" />
               </td>
           </tr>
           <tr>
               <td colspan="5">产品大小/Part Size (mm)</td>
               <td>克重/Part Weight(g)</td>
               <td>材料/Material</td>
               <td colspan="2" style="width:186px">产品零件号/Part No.</td>
               <td colspan="2">数据文件名/Drawing No.</td>
          
           </tr>
           <tr>
              <td style="width:93px" id="length_no">
              	<input type="text" name="p_length[]" id="p_length" class="p_length"/>
              	<span id="pp_length"></span>
              </td>
              <td >*</td>
              <td style="width:93px" id="width_no">
              	<input type="text" name="p_width[]" id="p_width" class="p_width" />
              	<span id="pp_width"></span>
              </td>
              <td>*</td>
              <td style="width:93px" id="height_no">
                     <input type="text" name="p_height[]" id="p_height" class="p_height" />
                     <span id="pp_height"></span>
              </td>
              <td style="width:93px" id="weight_no">
                    <input type="text" name="p_weight[]" id="p_weight" class="input_tx"/>
                    <span id="pp_weight"></span>
              </td>
              <td style="width:93px" id="material_no">
              	<input type="text" name="m_material[]" id="m_material" class="input_tx"/>
              	<span id="pp_material"></span>
              </td>
              <td colspan="2" style="padding-right:8px" id="part_no">
                   <input type="text" name="part_number[]" id="part_number" class="input_tx"  style="width:182px"/>
                   <span id="pp_number"></span>
               </td>
               <td colspan="2" id="file_no">
                   <input type="text" name="drawing_file[]" id="drawing_file" class="input_tx" style="width:310px"/>
                   <span id="pp_file"></span>
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
                   <input type="text" name="m_weight" id="m_weight" readonly="readonly" style="width:176px" />
              </td>
              <td colspan="2" style="padding-right:8px">
              	<input type="text" name="lift_time" id="lift_time" style="width:182px;"/>
              </td>
              <td colspan="2">
              	<input type="text" name="tonnage" style="width:310px"/>
              </td>
           </tr>
           <!--加工材料费-->
           <tr id="material_last_tr">
               <td id="material_first_td" rowspan="9">材料加工费/Machining Materia</td> 	
               <td colspan="4">材料名称/Material</td>
               <td>材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td style="width:93px">重量/Weight(kg)</td>
               <td style="width:93px">单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
	<tr class="material_trs even">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="模架/Mode" base="" disabled="" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px;color:red">
               </td>
               <td>
               	<select name="material_specification[]" id="material_specification">
                        <option value="">请选择</option>
                        <option value="1.2312">1.2312</option><option value="1.2343">1.2343</option><option value="Cu">Cu</option>               </select>
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value="1">
             </td>
             <td style="width:93px">
                   <input name="material_length[]" id="material_length" class="material_length" type="text" placeholder="长">
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" id="material_width" class="material_width" type="text" placeholder="宽">
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" id="material_height" class="material_height" type="text" placeholder="高">
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight">
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70">
             </td>
             <td>
                 <input type="text" name="material_price[]" id="material_price" class="material_price" value="0"> 	
             </td>
               <td rowspan="8" id="total_machining"><input type="text" class="min_total" value="0" name="total_machining"></td>   
           </tr>
              <?php
              $i = 0;
               foreach($array_mould_material as $mould_material_key=>$mould_material_value){
               	

      	?>
           <tr class="material_trs">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value=<?php echo $mould_material_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled>
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
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value="1">
             </td>
             <td style="width:93px">
                   <input name="material_length[]" class="material_length" id="material_length" type="text" placeholder="长">
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" class="material_width" id="material_width" type="text" placeholder="宽">
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" class="material_height" id="material_height" type="text" placeholder="高">
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight">
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>
             </td>
             <td>
                 <input type="text" name="material_price[]" class="material_price" id="material_price" value="0"> 	
             </td>
           </tr>
           <?php } ?>
           <tr id="machining_material">
             <td colspan="14">
              <span id="add_material" class="adder">
                    添加项目
             </span>
             </td>
           </tr>
           <!--热处理-->
           <tr>
             <td id="heat_first_td" rowspan="5">热处理/Heat Treatment</td>
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
                  <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" value=<?php echo $mould_heat_value ?> disabled style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight" type="text" class="heat_weight" value="0">
              </td>
              <td colspan="6">
                 <input name="heat_unit_price" type="text" class="heat_unit_price" value="18">	
              </td>
              <td colspan="2">
                <input name="heat_price" type="text" id="heat_price" disabled value="0" style="border-style:none">
              </td>
                <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="4" id="total_heats">
          	     		<input type="text" value="0" class="min_total" />
          	     		</td>  ';
          	     }
  	     $i++;
          ?> 
           </tr>
           <?php } ?>
           <tr id="mould_heats">
             <td colspan="14" style="">
                <span id="add_heat" class="adder">
                    添加项目
              </span>
          </td>
      </tr>
      <!--模具配件-->
      <tr>
      	<td id="parts_first_td" rowspan="9">模具配件/Mold standard parts</td>
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
      	     <input name="mold_standard[]" id="mold_standard" class="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:13px;width:193px" disabled value="<?php echo $mold_standard_value ?>">
      	</td>
      	<td colspan="2">
      	    <input type="text" name="standard_specification[]" id ="standard_specification">
      	</td>
      	<td colspan="5">
      	     <select name="standard_supplier[]" id="standard_supplier">
                  	<option>请选择</option>
                  </select>
      	</td>
      	<td>
      	    <input type="text" name="standard_number[]" class="standard_number" value="1">	
      	</td>
      	<td>
               <input type="text" name="standard_unit_price[]" class="standard_unit_price">
      	</td>
      	<td>
      	   <input type="text" name="standard_price[]" class="standard_price" value="0">	
      	</td>
      	 <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="8" id="total_standard">
			<input type="text" class="min_total"  name="total_standard">
          	     	</td>  ';
          	     }
  	     $i++;
          ?> 
      	      
      </tr>
      <?php }?>
      <tr id="standard_parts">
          <td colspan="14" style="">
              <span id="add_standard" class="adder">
                    添加项目
             </span>
          </td>
      </tr>
      <!--设计费-->
       <tr>
             <td id="design_first_td" rowspan="6">设计费/Design</td>
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
                  <input name="mold_design_name[]" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled value="<?php echo $mould_design_value ?>">
              
              </td>
              <td colspan="2">
                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" value="0">
              </td>
              <td colspan="6">
                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value="100">
              </td>
              <td colspan="2">
                <input type="text" name="design_price[]" class="design_price" id="design_price" value="0">
              </td>
                  <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="5" id="total_designs">
          	     		<input type="text" class="min_total" name="total_designs" value="0">
          	     		</td>  ';
          	     }
  	     $i++;
          ?> 
           </tr>
           <?php } ?>
           <tr id="designs">
             <td colspan="14" style="">
                <span id="add_designs" class="adder">
                    添加项目
               </span>
            </td>
          </tr>
          <!--加工费-->
           <tr>
             <td id="manus_first_td" rowspan="12">加工费/Manufacturing Cost</td>
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
                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" disabled value="<?php echo $mould_manufacturing_value ?>">
              
              </td>
              <td colspan="2">
                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" class="manufacturing_hour" value="0">
              </td>
              <td colspan="6">
                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" class="manufacturing_unit_price" value="100">
              </td>
              <td colspan="2">
               <input type="text" name="manufacturing_price[]" class="manufacturing_price" id="manufacuring_price" value="0"> 
              </td>
                      <?php 
          	    
          	     if($i == 0){
          	     	echo '<td rowspan="11" id="total_manufacturing">
          	     	       <input type="text" class="min_total" name="total_manufacturing" value="0">
          	     	 </td>  ';
          	     }
  	     $i++;
             ?> 
           </tr>
           <?php } ?>
        <tr id="manu_cost">
            <td colspan="14" style="">
              <span id="add_manu" class="adder">
                    添加项目
             </span>
          </td>
          </tr>
          <!--其它费用-->
          <tr>
          	   <td id="others_first_td" rowspan="7">其它费用/Other Fee</td>
          	   <td colspan="4">费用名称</td>
          	   <td colspan="8">费用计算说明</td>
          	   <td colspan="2">金额(元)</td>
          	   <td>小计(元)</td>
          </tr>
          <tr class="others_trs">
          	    <td colspan="4">试模费/Trial Fee</td>
          	   <td colspan="8">3 times mold trial(excluding raw material cost)</td>
          	   <td colspan="2">
          	   	<input type="text" name="trial_fee" class="other_fee fixed_fee" id="trial_fee" value="2000">
          	   </td>
          	   <td  rowspan="6" id="total_others">
		<input type="text" name="total_others" value="0" id="tot_others" value="0">
          	   </td>
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">运输费/Freight Fee</td>
          	   <td colspan="8">sample and tooling transport cost paid by customer</td>
          	   <td colspan="2">
          	       <input type="text" name="freight_fee" class="other_fee fixed_fee" id="freight_fee" value="1000">
          	   </td>
          </tr>
           <tr class="others_trs" id="others_fees">
          	    <td colspan="4">管理费/Management Fee</td>
          	   <td colspan="8">5%</td>
          	   <td colspan="2">
          	        <input type="text" name="management_fee" class="other_fee" id="management_fee" value="0">
          	   </td>
     
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">利润/Profit</td>
          	   <td colspan="8">10%</td>
          	   <td colspan="2">
          	   	<input type="text" name="profit" class="other_fee" id="profit" value="0">
          	   </td>
          
          </tr>
           <tr class="others_trs">
          	    <td colspan="4">税/VAT TAX(16%)</td>
          	   <td colspan="8">16%</td>
          	   <td colspan="2">
          	        <input type="text" name="vat_tax" class="other_fee" id="vat_tax" value="0">
          	   </td>
         
          </tr>
          <tr>
            <td colspan="14" style="">
              <span id="add_others" class="adder">
                    添加项目
             </span>
          </td>
          </tr>
          <!--模具价格-->
          <tr>
          	    <td colspan="5">模具价格(元)不含税/Mold Price without VAT(RMB)</td>
          	    <td colspan="11">
          	    	 <input type="text" name="mold_price_rmb" id="mold_price_rmb" value="0">
          	    </td>
          	</tr>
          	<tr>
          	    <td colspan="5">模具价格(USD)/Mold Price(USD) Rate=6.5</td>
          	    <td colspan="11">
          	    	 <input type="text" name="mold_price_usd" id="mold_price_usd" value="0">
          	    </td>
          	</tr>
          	<tr>
          	    <td colspan="5">模具价格(元)含17%增值税/Mold with VAT(RMB)</td>
          	    <td colspan="11">
          	    	<input type="text" name="mold_with_vat" id="mold_with_vat" value="0">
          	    </td>
          </tr>
          <tr height="20"></tr>
          <tr>
              <td style="border-style:none" colspan="16" align="center"><input type="submit" name="submit" id="submit" value="确定" class="button" />
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