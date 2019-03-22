
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
        <td><input type="text" name="mould_name" id="mould_name" class="input_txt" size="27" /></td>
        <td><select name="cavity_type" id="cavity_type" style="width:200px">
            <option value="">请选择</option>
            <?php
			foreach($array_mould_cavity_type as $cavity_type_key=>$cavity_type_value){
				echo "<option value=\"".$cavity_type_key."\">".$cavity_type_value."</option>";
			}
			?>
          </select></td>
        <td><input type="text" name="part_number" class="input_txt" size="30" /></td>
        <td colspan="2"><input type="text" name="t_time" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>产品大小/Part Size (mm)</td>
        <td>产品重量/Part Weight(g)&nbsp;<span>材料/Material</span></td>
        <td>数据文件名/Drawing No.</td>
        <td colspan="2">最终交付时间/Lead Timeme</td>
      </tr>
      <tr>
        <td><input type="text" name="p_length" id="p_length" class="input_txt" size="5" />
          *
          <input type="text" name="p_width" id="p_width" class="input_txt" size="5" />
          *
          <input type="text" name="p_height" id="p_height" class="input_txt" size="5" /></td>
        <td>
        	<input type="text" name="p_weight" id="p_weight" class="input_txt" size="10" />        	
        	 <input type="text" name="m_material" id="m_material" class="input_txt" size="5" style="margin-left:48px" />
        </td>

        <td><input type="text" name="drawing_file" class="input_txt" size="30" /></td>
        <td colspan="2"><input type="text" name="lead_time" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <td>模具尺寸/Mold Size (mm)</td>
        <td>模具重量/Mold Weight(Kg)</td>
        <td>模具寿命/Longevity</td>
        <td colspan="2">设备吨位/Press(Ton)</td>
      </tr>
      <tr>
        <td><input type="text" name="m_length" id="m_length" class="input_txt" size="5" readonly="readonly" />
          *
          <input type="text" name="m_width" id="m_width" class="input_txt" size="5" readonly="readonly" />
          *
          <input type="text" name="m_height" id="m_height" class="input_txt" size="5" readonly="readonly" /></td>
        <td>
        	  <input type="text" name="m_weight" id="m_weight" class="input_txt" size="25" readonly="readonly" />
        </td>
        <td><input type="text" name="lift_time" id="lift_time" class="input_txt" size="30" /></td>
        <td colspan="2"><input type="text" name="tonnage" class="input_txt" size="35" /></td>
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