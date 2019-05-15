<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);

$employeeid = $_SESSION['employee_info']['employeeid'];
//获取当前页面的路径
$system_url =  dirname(__FILE__);

$system_pos =  strrpos($system_url,DIRECTORY_SEPARATOR);
$system_url = substr($system_url,$system_pos);
//通过路径查询对应的模块id
$system_id_sql = "SELECT `systemid` FROM `db_system` WHERE `system_dir` LIKE '%$system_url%'";
$system_id_res = $db->query($system_id_sql);
$system_id = $system_id_res->fetch_row()[0];
if($system_id ==' '){
  header('location:../myjtl/index.php');
}
//查询登录用户是否是客户管理的管理员
$system_sql = "SELECT `isadmin` FROM `db_system_employee` WHERE `employeeid`='$employeeid' AND `systemid`=".$system_id;
$system_res = $db->query($system_sql);

$system_info = [];
while($system_admin = $system_res->fetch_row()){
  $system_info = $system_admin;
}

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
  function change(vals){
      var total_machining = 0;
    if(!(vals.attr('class') == 'material_weight' || vals.attr('class') == 'material_unit_price' || vals.attr('class') == 'material_price')){
      
      //求模架的重量
    
      var base_weight = ($("#base_length").val()/1000)*($("#base_width").val()/1000)*($("#base_height").val()/1000)*7800;
      base_weight = parseInt(base_weight);
      
      $("#base_weight").val(base_weight);
    }
      //计算淬火的重量
    
        var heat_weight = 0;
        var m = $('.material_specification').size();
        //计算淬火的重量
        for(var r=0;r<m;r++){
          var material_vals = $('.material_specification').eq(r).val();
          
          if($.inArray(material_vals,pre_steel) == -1 && material_vals){
          
            if($('.material_weight').eq(r).val()){
              heat_weight += parseInt($('.material_weight').eq(r).val());
              
            }
          }
        }
        
      $('.heat_weight').eq(1).val(heat_weight);
      var mould_num = $(".mould_material").size();
      for(var i = 0;i < mould_num; i++){

      //求出模架尺寸后计算材料费的金额
       var prices  = (parseInt($(".material_weight").eq(i).val()))*(parseInt($(".materials_number").eq(i).val()))*(parseInt($(".material_unit_price").eq(i).val()));
      if(!isNaN(prices)){
        //得到的价格不是NaN则赋值
        
          $(".material_price").eq(i).val(prices);
          total_machining += parseInt(prices);
          
        }
      }
    //计算总金额
    $("#total_machining").children().val(total_machining);
    //添加模具尺寸和模具重量
    $("#m_length").val($("#base_length").val());
    $("#m_width").val($("#base_width").val());
    $("#m_height").val($("#base_height").val());
    $("#m_weight").val($("#base_weight").val());
  
      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
    
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
        }
      //产品大小输入后计算设计费金额
      $(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
      
      sum_tds(".design_trs",1,2,"#total_designs");
      //产品大小输入后计算加工费金额
      $(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
      }
      sum_tds(".manus_trs",1,2,"#total_manufacturing")
    

    //计算其它费用及模具价格
        sum_other_fee();
  }
  //统计第一列需要合并的单元格个数
  function count_trs(trs_name,tds_name,trs_total){
    //判断型腔数对合并行的影响
      var num = $(trs_name).size()+3; 
    
    $(tds_name).attr('rowspan',num);
    $(trs_total).attr('rowspan',num-1);
  }
  count_tr(".material_trs","#material_first_td","#total_machining");
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
      $("#vat_tax").val(parseInt(sum_except_others*0.13));
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
      $("#mold_price_rmb").val(price_with_vat - parseInt(sum_except_others*0.13));
      $("#mold_price_usd").val(parseInt(price_with_vat/6.5));
      $("#mold_with_vat").val(price_with_vat);
  }
  //删除材料加工时重新计算总金额
  $(".material_dels").live('click',function(){
    $(this).parent().parent().remove();
    //删除行时重新统计第一列需要合并的单元格列数
    count_del_trs(".material_trs","#material_first_td","#total_machining");
     
  })
  //页面加载完成计算金额
  sum_tds(".heat_trs",1,2,"#total_heats");
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
                 var trs = '     <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" id="mould_material" class="mould_material" style="color:black;font-weight:150;font-size:13px;width:150px">     <p class="material_dels dels">删除</p>           </td>               <td>                <div class="autocomplete">          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料">         </div>       </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" class="materials_number">             </td>             <td>                   <input name="material_length[]" id="material_length" class="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td>                 <input name="material_width[]" id="material_width" class="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td>                  <input name="material_height[]" id="material_height" type="text" class="material_height" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" class="material_weight" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>             </td>             <td>                 <input type="text" name="material_price[]" class="material_price" id="material_price">                </td>       </tr>';
              
                     count_trs(".material_trs","#material_first_td","#total_machining");
     
                         $('#machining_material').before(trs);
                          autoCom('.material_specification',material_spe);
                   
          })
       
          //动态添加热处理
          $('#add_heat').click(function(){
              var heats = '  <tr class="heat_trs">              <td colspan="4">               <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" style="color:black;font-weight:150;font-size:13px;width:150px">        <p class="dels heat_dels">删除</p>           </td>              <td colspan="2">                  <input name="heat_weight" type="text" class="heat_weight">              </td>              <td colspan="6">                 <input name="heat_unit_price" type="text" class="heat_unit_price" value="24">                </td>              <td colspan="2">                <input name="heat_price" type="text" id="heat_price" class="heat_price" value="0" style="border-style:none">              </td>              </tr>';
              count_trs(".heat_trs","#heat_first_td","#total_heats");
     
              $('#mould_heats').before(heats);
          })  
          //动态添加模具配件费
          $('#add_standard').click(function(){
                     var standard = '<tr class="parts_trs">         <td colspan="4">             <input name="mold_standard[]" id="mold_standard" style="color:black;font-weight:150;font-size:13px;width:150px" class="mold_standard" >    <p class="dels standard_dels">删除</p>      </td>        <td colspan="2">          <div class="autocomplete">          <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification" placeholder="输入规格" style="width:183px">           </div>            <!-- <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification">-->        </td>        <td colspan="5">          <div class="autocomplete">          <input id="standard_supplier" class="standard_supplier" type="text" name="standard_supplier[]" placeholder="输入品牌" style="width:200px">           </div>               </td>       <td>            <input type="text" name="standard_number[]" class="standard_number">          </td>       <td>               <input type="text" name="standard_unit_price[]" class="standard_unit_price" value="2000">        </td>       <td>           <input type="text" name="standard_price[]" class="standard_price" id="standard_price">         </td>           </tr>';
                  count_trs(".parts_trs","#parts_first_td","#total_standard");
                        $('#standard_parts').before(standard);

	  //调用函数--模具配件规格
	  autoCom('.standard_specification',standard_supplier);

	  //调用函数--模具品牌
	  autoCom('.standard_supplier',standard_supplier);
          })
          //动态添加设计费
          $('#add_designs').click(function(){
              var design_adder = '    <tr class="design_trs">              <td colspan="4">                  <input name="mold_design_name[]" id="mold_design_name" class="mold_design_name" style="color:black;font-weight:150;font-size:13px;width:150px">           <p class="dels design_dels">删除</p>                 </td>              <td colspan="2">                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" >              </td>              <td colspan="6">                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value="100">              </td>              <td colspan="2">                <input type="text" name="design_price[]" class="design_price" id="design_price">              </td>        </tr>';
              count_trs(".design_trs","#design_first_td","#total_designs");
              $('#designs').before(design_adder);

      
          })
          //动态添加加工费
          $('#add_manu').click(function(){
        var manu = '<tr class="manus_trs">              <td colspan="4">                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="color:black;font-weight:150;font-size:13px;width:150px">           <p class="dels manu_dels">删除</p>                  </td>              <td colspan="2">                  <input type="text" name="manufacturing_hour[]" class="manufacturing_hour" id="manufacturing_hour">              </td>              <td colspan="6">                  <input type="text" name="manufacturing_unit_price[]" class="manufacturing_unit_price" id="manufacturing_unit_price" value="100">              </td>              <td colspan="2">               <input type="text" name="manufacturing_price[]" class="manufacturing_price" id="manufacuring_price">               </td>            </tr>';
              count_trs(".manus_trs","#manus_first_td","#total_manufacturing");
                     $('#manu_cost').before(manu);
          })
          //动态添加其它费用
          $("#add_others").click(function(){
                 var others_adder = '   <tr class="others_trs">  <td colspan="4"><input name="other_fee_name[]" id="other_fee_name" class="other_fee_name" style="color:black;font-weight:150;font-size:13px;width:150px"><p class="dels other_dels">删除</p></td>               <td colspan="8"><input name="other_fee_instr[]" id="other_fee_instr" class="other_fee other_fee_instr" style="color:black;font-weight:150;font-size:13px;width:300px" ></td>              <td colspan="2"> <input name="other_fee_price[]" class="other_fee_price fixed_fee" id="other_fee_price" >    </td>        </tr>';
                  count_trs(".others_trs","#others_first_td","#total_others");
                     $('#others_fees').before(others_adder);
          })

          //统计总共有多少tr标签
  /*$("#submit").click(function(){
    var mould_name = $("#mould_name").val();
    if(!$.trim(mould_name)){
      $("#mould_name").focus();
      alert('dd');
      return false;
    }
    
    var p_length = $("#p_length").val();
    if(!rf_b.test(p_length)){
      $("#p_length").focus();alert('xx');
      return false;
    }
    var p_width = $("#p_width").val();
    if(!rf_b.test(p_width)){
      $("#p_width").focus();alert('yy');
      return false;
    }
    var p_height = $("#p_height").val();  
    if(!rf_b.test(p_height)){
      $("#p_height").focus();alert('uu');
      return false;
    }
    var m_length = $("#m_length").val();
    if(!rf_b.test(m_length)){
      $("#m_length").focus();
      return false;
    }
    var m_width = $("#m_width").val();
    if(!rf_b.test(m_width)){
      $("#m_width").focus();alert('mm');
      return false;
    }
    var m_height = $("#m_height").val();
    if(!rf_b.test(m_height)){
      $("#m_height").focus();alert('ff');
      return false;
    }
    var m_weight = $("#m_weight").val();
    if(!rf_b.test(m_weight)){
      $("#m_weight").focus();alert('tt');
      return false;
    }
  })*/
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
    //$("#form1").attr("target", "frameFile");
    //$("#form1").submit();
    var material_add_cavity = ' <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" class="mould_material only_add" value="型腔2/Cavity" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">               </td>               <td>                 <select name="material_specification[]" id="material_specification" class="material_specification">                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" class="materials_number" >             </td>             <td style="width:93px">                   <input name="material_length[]" id="material_length" class="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" id="material_width" material_width type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" id="material_height" class="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>             </td>   <td>                 <input type="text" name="material_price[]" class="material_price" id="material_price">                </td></tr>';
      var material_add_core = ' <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" class="mould_material only_add" value="型芯2/Core" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">               </td>               <td>                 <select name="material_specification[]" id="material_specification" >                        <option value="">请选择</option>                        <?php
                            foreach($array_material_specification as $material_specification_key => $material_specification_value){
                                echo "<option value=".$material_specification_value.'>'.$material_specification_value.'</option>';
                            }
                        ?>               </select>             </td>             <td>                   <input type="text" name="materials_number[]" id="materials_number" >             </td>             <td style="width:93px">                   <input name="material_length[]" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>             </td>   <td>                 <input type="text" name="material_price[]" id="material_price">               </td></tr>';
    
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
   del_cavity = '  <button id="del_cavitys" type="button">删除</button>';
  var add_materials = '     <?php
   
               foreach($array_mould_material as $mould_material_key=>$mould_material_value){
        ?>              <tr class="material_trs">               <td colspan="4">                  <input name="mould_material[]" class="mould_material" value="<?php echo $mould_material_value ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">               </td>               <td>                  <div class="autocomplete">                    <?php if($mould_material_key == 'electrode'){ ?>          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="Cu">        <?php }else{ ?>          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料">        <?php } ?>          </div>             </td>             <td>                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value="1">             </td>             <td style="width:93px">                   <input name="material_length[]" class="material_length" id="material_length" type="text" placeholder="长">             </td>             <td>*</td>             <td style="width:93px">                 <input name="material_width[]" class="material_width" id="material_width" type="text" placeholder="宽">             </td>             <td>*</td>             <td style="width:93px">                  <input name="material_height[]" class="material_height" id="material_height" type="text" placeholder="高">             </td>             <td>                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight">             </td>             <td>                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="70"/>             </td>             <td>                 <input type="text" name="material_price[]" class="material_price" id="material_price" value="0">                </td>           </tr><?php } ?>';
   
  //添加删除按钮
  $("#add_cavitys").one('click',function(){
    if($(this).nextAll().size() == 0){
    $(this).after(del_cavity);
    }
    $(this).before(add_cavity);
    $("#machining_material").before(add_materials);
     //自动补全
        autoCom('.material_specification',material_spe);
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
      //自动补全
      autoCom('.material_specification',material_spe); 
    }
    
  //点击添加按钮
  $("#add_cavitys").live('click',function(){
    if($(this).nextAll().size() == 0){  
        $(this).after(del_cavity);
    }
    $(".material_dels").parent().parent().remove();
    if(j != 1){
    $(this).before(add_cavity);
    $("#machining_material").before(add_materials);
     //自动补全
        autoCom('.material_specification',material_spe);
    count_tr(".material_trs","#material_first_td","#total_machining");
    }
    j +=1;
    //动态添加排位选择框
           
            var style_wid = '<span  style="display:inline-block">   <input type="text" class="cavity_names" name="" style="border-style:none;background:white" disabled value="型腔" placeholder="">            <br />                      <select  class="cavity_length" name="cavity_length[]">              <option class="first_length" value="">cavity长</option>            </select>                       <br />                                  <select  class="cavity_width" name="cavity_width[]">              <option value="" class="first_width">cavity宽</option>           </select>         </span>';
            
            $("#cavity_widths").before(style_wid);
            //动态添加布局选项框
            var cavity_styles = '<div style="display:inline-block;background:#eee;">          <span  style="padding-left:20px"><input class="cavity_style_names" value="型腔" style="border-style:none;background:white" disabled type="text"></span><br />         <span>            <select  class="cavity_style_length" name="cavity_style_length[]" style="width:80px">             <option value="" class="first_style_length">长度方向</option><option value="1">1</option>           </select>         </span><br />         <span>            <select class="cavity_style_width" name="cavity_style_width[]" style="width:80px">              <option class="first_style_width" value="">宽度方向</option>    <option value="1">1</option>        </select>         </span></div>';
            $("#cavity_width_styles").before(cavity_styles);
    
            var add_nums = $("#add_cavitys").prevAll().size()-1;
          
            $(".cavity_names").eq(add_nums-2).val('型腔'+add_nums);
            $(".cavity_style_names").eq(add_nums-2).val('型腔'+add_nums);


    var input_length = '<input type="text" name="p_length[]" id="p_length" class="p_length"/>';
    var input_width  = '<input type="text" name="p_width[]" id="p_width" class="p_width"/>';
    var input_height = '<input type="text" name="p_height[]" id="p_height" class="p_height"/>';
    var input_part    ='<input type="text" name="part_number[]" id="part_number" class="part_number" style="width:182px"/>';
    var input_file      ='<input type="text" name="drawing_file[]" id="drawing_file" class="drawing_file" style="width:310px"/>';
    var input_weight =' <input type="text" name="p_weight[]" id="p_weight" class="p_weight"/>';
    var input_material='<input type="text" name="m_material[]l" id="m_material" class="p_material"/>';
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
    var total_machining = 0;
    var cavity_number = $("#add_cavitys").prevAll().size();
    var mould_num = $(".mould_material").size();
    //k数改变时更改模架的长度
    if(k_num == 2){
      //模架的长乘以2
      var base_new_length = parseInt($("#base_length").val())*2;
      $("#base_length").val(base_new_length);
      } else {
        //改回模架的长
        var base_new_length = parseInt($("#base_length").val())/2;
        $("#base_length").val(base_new_length); 
      }
      //遍历材料的名称
      for(var i=0;i < mould_num;i++){
      
          if(k_num == '2'){
            var core_num = $(".materials_number").eq(i).val()*2;
            $(".materials_number").eq(i).val(core_num); 
            
          } else {
            var core_num = $(".materials_number").eq(i).val()/2;
            $(".materials_number").eq(i).val(core_num);
              
        }
      
    
      //k数改变时,重量也动态改变
      var prices  = ($(".material_weight").eq(i).val())*($(".materials_number").eq(i).val())*($(".material_unit_price").eq(i).val()); 
        $(".material_price").eq(i).val(prices);
        total_machining += parseInt(prices);
    
      }
    
    //计算总金额
    $("#total_machining").children().val(total_machining);
  
      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
      
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
        }
      //产品大小输入后计算设计费金额
      $(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
      
      sum_tds(".design_trs",1,2,"#total_designs");
      //产品大小输入后计算加工费金额
      $(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
      }
      sum_tds(".manus_trs",1,2,"#total_manufacturing")
    

    //计算其它费用及模具价格
        sum_other_fee();
     

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
    var cavity_num = cavity_number-1 ;
    //删除原来的布局选项
    $(".first_style_length").eq(cavity_num).siblings().remove();
    $(".first_style_width").eq(cavity_num).siblings().remove();
    //删除原来的排位选项
    $(".first_length").eq(cavity_num).siblings().remove();
    $(".first_width").eq(cavity_num).siblings().remove();
    $("#style_length_sum").val(0);
    $("#style_width_sum").val(0);
    //加入布局选项框
    for(var h=0;h<cavity_val;h++){
      
      var h1 = h+1;
      
      var h2 = cavity_val/h1 + "";
      
      var cavity_style_layout = '<option value='+h1+'>'+h1+'</option>';
      
      $(".cavity_style_length").eq(cavity_num).append(cavity_style_layout);

    }
  //重新计算材料费金额
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

  //当型腔布局发生更改时
  $(".cavity_style_length").live('change',function(){
    //获取当前是第几个型腔
    var type_val;
    var cavity_nu = $(this).parent().parent().prevAll().size() - 2;
    var type_val = $(".cavity_type").eq(cavity_nu).val();
    var p_length = $('.material_length').eq((cavity_nu)*7+1).val();
    var p_width = $('.material_width').eq((cavity_nu)*7+1).val();
    //删除宽的布局
    $(".first_style_width").eq(cavity_nu).siblings().remove();
    $(".first_width").eq(cavity_nu).siblings().remove();
    //获取当前布局的值
    var cavity_style_length = $(this).val();
    //计算型腔布局的宽并加入到布局选项中
    var cavity_style_width = Math.ceil(type_val/cavity_style_length);
    //通过获取的值计算型腔的长和宽
    var opt_length = (parseInt(p_length) )*cavity_style_length;
    var opt_width  = (parseInt(p_width))*cavity_style_width;
     
    //添加布局宽度
     var style_length = '<option selected value='+cavity_style_width+'>'+cavity_style_width+'</option>'; 
    $(".cavity_style_width").eq(cavity_nu).append(style_length);
    if(!opt_length){
      $(".p_length").eq(cavity_nu).focus();
      alert('请输入产品的长度');
      $(".cavity_style_length").eq(cavity_nu).val(0);
      $(".cavity_style_width").eq(cavity_nu).val(0);
      return false;
    }
    if(!opt_width){
      $(".p_width").eq(cavity_nu).focus();
      alert('请输入产品的宽度');
      $(".cavity_style_length").eq(cavity_nu).val(0);
      $(".cavity_style_width").eq(cavity_nu).val(0);
      return false;
    }
    
  

    //删除原来的型腔排位长的选项
    $(".first_length").eq(cavity_nu).siblings().remove();
    //把选项添加到型腔布局选项框中
    var  cavity_style_length  = '<option value='+opt_length+'>'+opt_length+'</option>';
    var  cavity_style_width  = '<option value='+opt_width+'>'+opt_width+'</option>';  
    var  cavity_zero = '<option value="0">0</option>';  
    //加入到排位长的选项中
    $(".cavity_length").eq(cavity_nu).append(cavity_zero);
    $(".cavity_length").eq(cavity_nu).append(cavity_style_length);
    $(".cavity_length").eq(cavity_nu).append(cavity_style_width);
    //加入到排位宽的选项中
    $(".cavity_width").eq(cavity_nu).append(cavity_zero);
    $(".cavity_width").eq(cavity_nu).append(cavity_style_length);
    $(".cavity_width").eq(cavity_nu).append(cavity_style_width);
    
    var cavity_nums = $(".cavity_length").size();
    var cavity_length_sums = 0;
    for(var q = 0;q<cavity_nums;q++){

    }

  })

  //当选择型腔排位长之后,动态添加宽
    $(".cavity_length").live('change',function(){

      var cavity_nu = $(this).parent().prevAll().size() -1;
      var type_val = $(".cavity_type").eq(cavity_nu).val();
      var p_length = $('.p_length').eq(cavity_nu).val();
      
      var p_width = $('.p_width').eq(cavity_nu).val();
    
      
      //获取型腔布局的长和宽的值
      var cavity_style_length = $(".cavity_style_length").eq(cavity_nu).val();
      var cavity_style_width  = $(".cavity_style_width").eq(cavity_nu).val();
      //通过获取的值计算型腔的长和宽
      ////删除原来的型腔排位宽的选项
      $(".first_width").eq(cavity_nu).siblings().remove();
      var opt_length = (parseInt( $('.material_length').eq((cavity_nu)*7+1).val()))*cavity_style_length;
      
      var opt_width  = (parseInt($('.material_width').eq((cavity_nu)*7+1).val()))*cavity_style_width;
      //获取选择的值
      var cavity_length_val  = $(this).val();
      
      //判断选择的是否是长
      if(cavity_length_val == opt_length){
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_zero);
         var  cavity_style_widths  = '<option selected value='+opt_width+'>'+opt_width+'</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_style_widths);  
      
      } else if(cavity_length_val == opt_width) {
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_zero);
        var  cavity_style_lengths  = '<option selected value='+opt_length+'>'+opt_length+'</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_style_lengths); 
      } else {
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_zero);
         var  cavity_style_widths  = '<option selected value='+opt_width+'>'+opt_width+'</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_style_widths);  
         var  cavity_style_lengths  = '<option selected value='+opt_length+'>'+opt_length+'</option>';
         $(".cavity_width").eq(cavity_nu).append(cavity_style_lengths); 
      
      }
    
    //如果型腔长度都已经选中,计算型腔的长度和
    var cavity_nus = $(".cavity_length").size();
    var max_height = $(".p_height").eq(0).val();
    var cavity_length_sum = 0;
    var cavity_width_sum = 0;
    for(var a = 0; a < cavity_nus ;a++){
      a1 = a +1;
       max_height = ($(".p_height").eq(a).val()) < ($(".p_height").eq(a1).val()) ? ($(".p_height").eq(a1).val())  : max_height;
      cavity_length_sum += parseInt($(".cavity_length").eq(a).val());
      cavity_width_sum += parseInt($(".cavity_width").eq(a).val());
    
    
    }
    //获取最高的型腔高度
    
    if(!isNaN(cavity_length_sum)){
      $("#style_length_sum").val(cavity_length_sum);
      $("#style_width_sum").val(cavity_width_sum);
      //ajax 求模架的尺寸
      $.post('../ajax_function/mould_base_size.php',{cavity_length_sum:cavity_length_sum,cavity_width_sum:cavity_width_sum,max_height:max_height},function(data){
        var arr = data.split('#');
        
        $("#base_length").val(arr[0]);
        $("#base_width").val(arr[1]);  
        $("#base_height").val(arr[2]);
      //计算各种金额
      //求模架的重量
      var total_machining = 0;
      var base_weight = ($("#base_length").val()/1000)*($("#base_width").val()/1000)*($("#base_height").val()/1000)*7800;
      base_weight = parseInt(base_weight);
      
      $("#base_weight").val(base_weight);
      var mould_num = $(".mould_material").size();
      for(var i = 0;i < mould_num; i++){

      //求出模架尺寸后计算材料费的金额
        var prices  = ($(".material_weight").eq(i).val())*($(".materials_number").eq(i).val())*($(".material_unit_price").eq(i).val());
        
        $(".material_price").eq(i).val(prices);
        total_machining += parseInt(prices);
      }
    //计算总金额
    $("#total_machining").children().val(total_machining);
    //添加模具尺寸和模具重量
    $("#m_length").val($("#base_length").val());
    $("#m_width").val($("#base_width").val());
    $("#m_height").val($("#base_height").val());
    $("#m_weight").val($("#base_weight").val());
  
      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
    
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
        }
      //产品大小输入后计算设计费金额
      $(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
      
      sum_tds(".design_trs",1,2,"#total_designs");
      //产品大小输入后计算加工费金额
      $(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
      }
      sum_tds(".manus_trs",1,2,"#total_manufacturing")
    

    //计算其它费用及模具价格
        change($(this));
      
      })
    
    }

    })
    //更改模架尺寸时,动态更改模具尺寸
    $("#base_length").change(function(){
      $("#m_length").val($(this).val());
      
    })
    $("#base_width").change(function(){
      $("#m_width").val($(this).val());
      
    })
    $("#base_height").change(function(){
      $("#m_height").val($(this).val());
      
    })
    $("#base_weight").change(function(){
      $("#m_weight").val($(this).val());
    })
    //当选择型腔排位宽之后,动态添加长
    $(".cavity_width").live('change',function(){
      var cavity_nu = $(this).parent().prevAll().size() -1;
      
      var type_val = $(".cavity_type").eq(cavity_nu).val();
      var p_length = $('.p_length').eq(cavity_nu).val();
      
      var p_width = $('.p_width').eq(cavity_nu).val();
    
      
      //获取型腔布局的长和宽的值
      var cavity_style_length = $(".cavity_style_length").eq(cavity_nu).val();
      var cavity_style_width  = $(".cavity_style_width").eq(cavity_nu).val();
      //通过获取的值计算型腔的长和宽
      ////删除原来的型腔排位宽的选项
      $(".first_length").eq(cavity_nu).siblings().remove();
      var opt_length = (parseInt($('.material_length').eq((cavity_nu)*7+1).val()) )*cavity_style_length;
      
      var opt_width  = (parseInt($('.material_length').eq((cavity_nu)*7+1).val()))*cavity_style_width;
      //获取选择的值
      var cavity_width_val  = $(this).val();
      
      //判断选择的是否是长
      if(cavity_width_val == opt_width){
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_zero);
         var  cavity_style_widths  = '<option selected value='+opt_length+'>'+opt_length+'</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_style_widths); 
      
      } else if(cavity_width_val == opt_length) {
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_zero);
        var  cavity_style_lengths  = '<option selected value='+opt_width +'>'+opt_width +'</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_style_lengths);  
      } else {
        var cavity_zero = '<option selected value="0">0</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_zero);
        var  cavity_style_widths  = '<option selected value='+opt_length+'>'+opt_length+'</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_style_widths); 
         var  cavity_style_lengths  = '<option selected value='+opt_width +'>'+opt_width +'</option>';
         $(".cavity_length").eq(cavity_nu).append(cavity_style_lengths);  
      } 
    
    //如果型腔长度都已经选中,计算型腔的长度和
    var cavity_nus = $(".cavity_length").size();
    var max_height = $(".p_height").eq(0).val();
    var cavity_length_sum = 0;
    var cavity_width_sum = 0;
    for(var a = 0; a < cavity_nus ;a++){
      a1 = a +1;
       max_height = ($(".p_height").eq(a).val()) < ($(".p_height").eq(a1).val()) ? ($(".p_height").eq(a1).val())  : max_height;
      cavity_length_sum += parseInt($(".cavity_length").eq(a).val());
      cavity_width_sum += parseInt($(".cavity_width").eq(a).val());
    
    
    }
    //获取最高的型腔高度
    max_height = max_height +100;
    if(!isNaN(cavity_length_sum)){
      $("#style_length_sum").val(cavity_length_sum);
      $("#style_width_sum").val(cavity_width_sum);
      //ajax 求模架的尺寸
      $.post('../ajax_function/mould_base_size.php',{cavity_length_sum:cavity_length_sum,cavity_width_sum:cavity_width_sum,max_length:max_height},function(data){
        var arr = data.split('#');
        
        $("#base_length").val(arr[0]);
        $("#base_width").val(arr[1]);  
        $("#base_height").val(arr[2]);
      //计算各种金额
      //求模架的重量
      var total_machining = 0;
      var base_weight = ($("#base_length").val()/1000)*($("#base_width").val()/1000)*($("#base_height").val()/1000)*7800;
      base_weight = parseInt(base_weight);
      
      $("#base_weight").val(base_weight);
      var mould_num = $(".mould_material").size();
      for(var i = 0;i < mould_num; i++){

      //求出模架尺寸后计算材料费的金额
        var prices  = ($(".material_weight").eq(i).val())*($(".materials_number").eq(i).val())*($(".material_unit_price").eq(i).val());
        
        $(".material_price").eq(i).val(prices);
        total_machining += parseInt(prices);
      }
    //计算总金额
    $("#total_machining").children().val(total_machining);
    //添加模具尺寸和模具重量
    $("#m_length").val($("#base_length").val());
    $("#m_width").val($("#base_width").val());
    $("#m_height").val($("#base_height").val());
    $("#m_weight").val($("#base_weight").val());
  
      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
      
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
        }
      //产品大小输入后计算设计费金额
      $(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
      
      sum_tds(".design_trs",1,2,"#total_designs");
      //产品大小输入后计算加工费金额
      $(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
        }
      sum_tds(".manus_trs",1,2,"#total_manufacturing")
    //计算其它费用及模具价格
        change($(this));
        })
      }
    })
  //删除型腔类型
  $("#del_cavitys").live('click',function(){
    //删除排位选项框
    $("#cavity_lengths").prev().remove();
    $("#cavity_widths").prev().remove();
    $("#cavity_length_styles").prev().remove();
    $("#cavity_width_styles").prev().remove();
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
    //计算材料费的金额
    var mould_num = $(".mould_material").size();
      var total_machining = 0;
      for(var g = 0;g < mould_num; g++){
        var material_m = $(".material_weight").eq(g).val()?$(".material_weight").eq(g).val():0;
        var material_n = $(".materials_number").eq(g).val()?$(".materials_number").eq(g).val():0;
        var material_u = $(".material_unit_price").eq(g).val()?$(".material_unit_price").eq(g).val():0;
        var prices  = (parseInt(material_m))*(parseInt(material_n))*(parseInt(material_u));
        
        $(".material_price").eq(g).val(prices);
        total_machining += parseInt(prices);

      }
      
    //计算总金额
    $("#total_machining").children().val(total_machining);


      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
      
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
        }
      //产品大小输入后计算设计费金额
      $(".design_price").eq(e).val(($(".design_hour").eq(e).val())*($(".design_unit_price").eq(e).val()));
      
      sum_tds(".design_trs",1,2,"#total_designs");
      //产品大小输入后计算加工费金额
      $(".manufacturing_price").eq(e).val(($(".manufacturing_hour").eq(e).val())*($(".manufacturing_unit_price").eq(e).val()));
      }
      sum_tds(".manus_trs",1,2,"#total_manufacturing")
    //计算其它费用及模具价格
        sum_other_fee();
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
      var weight_g = (p_length - 2) * (p_width - 2) * (p_height - 2);
      weight_g = (weight_g/1000).toFixed(2);
      //输入产品大小后计算型腔的尺寸
      for(var i = 0;i < mould_num; i++){
        if($(".mould_material").eq(i).val() == '型腔'+no1+'/Cavity' || $(".mould_material").eq(i).val() == '型芯'+no1+'/Core' ){
          //计算型腔和型芯的尺寸
          if(p_length <50){
            $('.material_length').eq(i).val(60);
          } else if(p_length >= 50 && p_length <500) {
            if(p_height > 0 && p_height< 30){
              $('.material_length').eq(i).val(parseInt(p_length*1.1+70));
            } else if(p_height >= 30 && p_height< 60) {
              $('.material_length').eq(i).val(parseInt(p_length*1.1+80));
            } else if(p_height >= 60 && p_height<90) {
              $('.material_length').eq(i).val(parseInt(p_length*1.1+90));
            } else if(p_length >= 90 && p_length<120) {
              $('.material_length').eq(i).val(parseInt(p_length * 1.1+100));
            } else if(p_length >=120) {
              $('.material_length').eq(i).val(parseInt(p_length * 1.1+110));
            }
          } else if(p_length >= 500){
            if(p_height > 0 && p_height< 30){
              $('.material_length').eq(i).val(parseInt(p_length+120));
            } else if(p_height >= 30 && p_height< 60) {
              $('.material_length').eq(i).val(parseInt(p_length+130));
            } else if(p_height >= 60 && p_height<90) {
              $('.material_length').eq(i).val(parseInt(p_length+1140));
            } else if(p_height >= 90 && p_height<120) {
              $('.material_length').eq(i).val(parseInt(p_length+150));
            } else if(p_height >=120) {
              $('.material_length').eq(i).val(parseInt(p_length+160));
            } 
          }
          if(p_width <50){
            $('.material_width').eq(i).val(60);
          } else if(p_width >= 50 && p_width <500) {
            if(p_height > 0 && p_height< 30){
              $('.material_width').eq(i).val(parseInt(p_width*1.1+70));
            } else if(p_height >= 30 && p_height< 60) {
              $('.material_width').eq(i).val(parseInt(p_width*1.1+80));
            } else if(p_height >= 60 && p_height<90) {
              $('.material_width').eq(i).val(parseInt(p_width*1.1+90));
            } else if(p_height >= 90 && p_height<120) {
              $('.material_width').eq(i).val(parseInt(p_width * 1.1+100));
            } else if(p_height >=120) {
              $('.material_width').eq(i).val(parseInt(p_width * 1.1+110));
            }
          } else if(p_width >= 500){
            if(p_height > 0 && p_height< 30){
              $('.material_width').eq(i).val(parseInt(p_length+120));
            } else if(p_height >= 30 && p_height< 60) {
              $('.material_width').eq(i).val(parseInt(p_length+130));
            } else if(p_height >= 60 && p_height<90) {
              $('.material_width').eq(i).val(parseInt(p_length+140));
            } else if(p_height >= 90 && p_height<120) {
              $('.material_width').eq(i).val(parseInt(p_length+150));
            } else if(p_height >=120) {
              $('.material_width').eq(i).val(parseInt(p_length+160));
            } 
          }
          $('.material_height').eq(i).val(parseInt(p_height * 1.2 +40));
          //输入产品大小后计算型腔和型芯的重量
          var wei = (parseInt($('.material_length').eq(i).val())/1000)*(parseInt($('.material_width').eq(i).val())/1000)*(parseInt($('.material_height').eq(i).val()) /1000)*7800;
          wei = Math.round(wei);
          $('.material_weight').eq(i).val(wei);
      

            
        } else if($(".mould_material").eq(i).val() == '电极'+no1+'/Electrode'){
           $(".material_length").eq(i).val(parseFloat(p_length)+20);
           $(".material_width").eq(i).val(parseFloat(p_width)+20);
           $(".material_height").eq(i).val(120);
          //输入产品大小后计算电极的重量
          var wei = ((parseFloat(p_length)+100)/1000)*((parseFloat(p_width)+100)/1000)*(150/1000)*8900;
          wei = Math.round(wei);
          $('.material_weight').eq(i).val(wei);
        }
        //输入产品大小后计算材料费的金额
        var prices  = (parseInt($(".material_weight").eq(i).val()))*(parseInt($(".materials_number").eq(i).val()))*(parseInt($(".material_unit_price").eq(i).val()));
        
        $(".material_price").eq(i).val(prices);
        total_machining += parseInt($(".material_price").eq(i).val());
        }
            
    //计算总金额
    $("#total_machining").children().val(total_machining);
  
      //输入产品大小后计算设计费的工时
      var design_unit_hour = Math.round(total_machining*0.2/100/4);
      var design_num = $(".design_hour").size();
      for(var n=0;n<design_num;n++){
        $(".design_hour").eq(n).val(design_unit_hour);
      }
      //输入产品大小后计算加工费的工时
      var manu_unit_hour = total_machining*standard_ratio/100/10;
      var manu_num = $(".mold_manufacturing").size();
      
      for(var e = 0; e<manu_num;e++){
        switch($(".mold_manufacturing").eq(e).val()) {
          case '一般机床/Maching':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '磨床/Grinding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.6));
            break;
          case '数控机床/CNC':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*1.2));
            break;
          case '精密数控机床':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '线切割/W.C.':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '电火花/EDM':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '抛光/Polish':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour));
            break;
          case '钳工/Fitting':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
            break;
          case '激光烧焊/Laser Welding':
            $(".manufacturing_hour").eq(e).val(parseInt(manu_unit_hour*0.8));
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
    //克重
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
        $(".standard_unit_price").eq(s).val(4000);
        break;
      case '顶杆、顶管/Ejection Pin\\Sleeve':
        $(".standard_unit_price").eq(s).val(4000);
        break;
      case '水管、油管接头/Connector':
        $(".standard_unit_price").eq(s).val(4000);
        break;
      case '标准件/Standard Components':
        $(".standard_unit_price").eq(s).val(8000);
        break;
      case '热流道/Hot Runner':
        $(".standard_unit_price").eq(s).val(0);
        break;
      case '温控器/Temp Controller':
        $(".standard_unit_price").eq(s).val(2000);
        break;
      case '油缸/Hydro-cylinder':
        $(".standard_unit_price").eq(s).val(2000);
        break;
    }
    //计算配件费金额
    $(".standard_price").eq(s).val(($(".standard_number").eq(s).val())*($(".standard_unit_price").eq(s).val()));
    
  }
  sum_tds(".parts_trs",3,4,"#total_standard")
  // 产品尺寸发生更改
  $(".p_length,.p_width,.p_height").live('change',function(){
    var size_num = $(this).prevAll().size();
    $(".cavity_style_length").eq(size_num).val(0);
    $(".cavity_style_width").eq(size_num).val(0);
    $(".first_length").eq(size_num).siblings().remove();
    $(".first_width").eq(size_num).siblings().remove();
    $("#style_length_sum").val(0);
    $("#style_width_sum").val(0);
  })
  //更改材料尺寸后重新计算金额
  $(".material_length,.material_width,.material_height").live('blur',function(){
    //重新计算材料费的金额
    var no = $(this).prevAll().size();
    var no2 = no+1;
    var total_machinings = 0;
    var mould_num = $(".mould_material").size();
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
          } else if($(".mould_material").eq(t).val() == "模架/Mould Base") {
            //计算模架的重量
            var wei = (parseFloat(material_len))*(parseFloat(material_wid))*(parseFloat(material_hei))*7800/1000000000;
            wei = Math.round(wei);
            $('.material_weight').eq(t).val(wei);
            $("#m_weight").val(wei);
          } else {
            //计算其它的重量
            var wei = (parseFloat(material_len))*(parseFloat(material_wid))*(parseFloat(material_hei))*8900/1000000000;
            wei = Math.round(wei);
            $('.material_weight').eq(t).val(wei);
          }
        var prices  = ($(".material_weight").eq(t).val())*($(".materials_number").eq(t).val())*($(".material_unit_price").eq(t).val());
            
        $(".material_price").eq(t).val(prices);
        total_machinings += parseInt($(".material_price").eq(t).val());
      }
      change($(this));
    }
  })
  //更改其它费用后重新计算金额
  $(".fixed_fee").live('change',function(){
    sum_other_fee();
  })
  //更改材料重量和单价后重新计算金额
  $(".material_weight,.material_unit_price,.materials_number").live('change',function(){
    change($(this));

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
  
  function autocomplete(inp, arr) {
      /*函数主要有两个参数：文本框元素和自动补齐的完整数据*/
      var currentFocus;
      /* 监听 - 在写入时触发 */
      inp.addEventListener("input", function(e) {
          var a, b, i, val = this.value;
          /*关闭已经打开的自动完成值列表*/
          closeAllLists();
          if (!val) { return false;}
          currentFocus = -1;
          /*创建列表*/
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "autocomplete-list");
        
          a.setAttribute("class", "autocomplete-items");
          /*添加 DIV 元素*/
          this.parentNode.appendChild(a);
          /*循环数组...*/
          for (i = 0; i < arr.length; i++) {
            /*检查选项是否以与文本字段值相同的字母开头*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              /*为匹配元素创建 DIV*/
              b = document.createElement("DIV");
              /*使匹配字母变粗体*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              
              b.addEventListener("click", function(e) {
                 
                  inp.value = this.getElementsByTagName("input")[0].value;
    
                  closeAllLists();
              });
              a.appendChild(b);
            }
          }
      });
      inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            if (currentFocus > -1) {
              if (x) x[currentFocus].click();
            }
          }
      });
        function addActive(x) {
          if (!x) return false;
          removeActive(x);
          if (currentFocus >= x.length) currentFocus = 0;
          if (currentFocus < 0) currentFocus = (x.length - 1);
          /*添加类 "autocomplete-active":*/
          x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
          for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
          }
        }
        function closeAllLists(elmnt) {
          var x = document.getElementsByClassName("autocomplete-items");
          for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
              x[i].parentNode.removeChild(x[i]);
            }
          }
        }
       
      /* document.addEventListener("click", function () {
            document.mould_data.submit();
            });*/
      }

  /*数组 - 包含所有材料牌号*/
  var material_spe = ["45#(国产)","S50C(国产)","P20(国产)","P20(进口)","718/718H(国产)","718/718H(进口)","738/738H(国产)","738/738H(进口)","2311(国产)","2311(进口)","2312(国产)","2312(进口)","NAK80(国产)","NAK80(进口)","2711(进口)","Cr12(国产)","H13(国产)","H13(进口)","S136(国产)","S136(进口)","8407(国产)","8407(进口)","8402(国产)","8402(进口)","2344(国产)","2344(进口)","2344SER(国产)","2344SER(进口)","2343(国产)","2343(进口)","2343SER(国产)","2343SER(进口)","DAC-S(进口)","CENA1(进口)","PX4(进口)","PX5(进口)","S-STAR(进口)","2083(国产)","2083(进口)","Cu"];
  var standard_supplier = ["LKM","Mold Masters","ynventive","INCOE","GUNTHER","YUDO","HASCO","DME","STAUBLI","MiSUMi","HEB","TAIYD","Parker","VEGA","HPS"];
  //调用函数,实现自动补全
  function autoCom(classnames,arr_name){
  	var input_num = $(classnames).size();
  	for(var u = 0; u<input_num;u++){
  		autocomplete($(classnames)[u],arr_name);
  	}
  }
  /*调用函数传递参数--材料牌号*/
  autoCom('.material_specification',material_spe);

  //调用函数--模具配件规格
  autoCom('.standard_specification',standard_supplier);

  //调用函数--模具品牌
  autoCom('.standard_supplier',standard_supplier);
  //材料的类别
   pre_steel = ["45#(国产)","S50C(国产)","P20(国产)","P20(进口)","718/718H(国产)","718/718H(进口)","738/738H(国产)","738/738H(进口)","2311(国产)","2311(进口)","2312(国产)","2312(进口)","NAK80(国产)","NAK80(进口)","2711(进口)","Cu"];
  //材料牌号发生改变,动态更改单价
  standard_ratio = 1.5;
  var handened_heat = 0;
  $(".material_specification").live('blur',function(){
    
    //ajax查询对应材料的价格
    var n = $(".material_specification").index($(this));

    setTimeout(function(){
      //判断是否是是型腔或者型芯的材料
      
      if(n == 1){
        //判断型腔
        var cavity_val = $('.material_specification').eq(n).val();
        var cavity_bool = $.inArray(cavity_val,pre_steel);
        if(cavity_bool != -1){
          var cavity_boolbean = true;
        } else {
          var cavity_boolbean = false;
        }
        //判断型芯
        var core_val = $('.material_specification').eq(n+1).val();
        var core_bool = $.inArray(core_val,pre_steel);

        if(core_bool != -1){
  
          var core_boolbean = true;
        } else {
          var core_boolbean = false;
        }
        
        }
        
      if(n == 2){

          //判断型腔
        var cavity_val = $('.material_specification').eq(n - 1).val();
        var cavity_bool = $.inArray(cavity_val,pre_steel);
        if(cavity_bool != -1){
          var cavity_boolbean = true;
        } else {
          var cavity_boolbean = false;
        }
        //判断型芯
        var core_val = $('.material_specification').eq(n).val();
        var core_bool = $.inArray(core_val,pre_steel);
        if(core_bool != -1){
          var core_boolbean = true;
        } else {
          var core_boolbean = false;
        }
        }
         //设置加工费的系数
      if(cavity_boolbean && core_boolbean){
        standard_ratio = 1.5;
      } else if(cavity_boolbean || core_boolbean) {
        standard_ratio = 1.75;
      } else {
        standard_ratio = 2;
      }

        //计算淬火的重量
    
        var heat_weight = 0;
        var m = $('.material_specification').size();
        //计算淬火的重量
        for(var r=0;r<m;r++){
          var material_vals = $('.material_specification').eq(r).val();
          
          if($.inArray(material_vals,pre_steel) == -1 && material_vals){
          
            if($('.material_weight').eq(r).val()){
              heat_weight += parseInt($('.material_weight').eq(r).val());
              
            }
          }
        }
        
      $('.heat_weight').eq(1).val(heat_weight);
       //输入产品大小后设置热处理的金额计算
            var heat_num = $(".heat_unit_price").size();
            for(var v=0;v<heat_num;v++){
              $('.heat_price').eq(v).val(parseInt(parseInt($('.heat_weight').eq(v).val())*parseInt($('.heat_unit_price').eq(v).val())));
            }
            sum_tds(".heat_trs",1,2,"#total_heats");
     
      //获取材料的单价
      var material_num = $(".material_specification").size();
      
      var material_name = $(".material_specification").eq(n).val();
    
      $.ajax({
      'url':'../ajax_function/mould_material_specification.php',
      'data':{material_name:material_name},
      'async':false,
      'type':'post',
      'dataType':'json',
      'success':function(data){
        if(data != null){
          $(".material_unit_price").eq(n).val(data);
          //计算其它费用
          change($(this));
        }   
      },
      'error':function(){
        alert('无法查找单价,请自行修改');
      }
    })
  
    },2000);
    
  })
  
      
  //提交时判断客户名称是否为空
  $('#submit').click(function(){
    var client_name = $('.client_name').val();
    if(client_name == ' '){
      alert('请选择客户名称')
      $('.client_name').focus();
      return false;
    }
  
  })
  //选择客户名称后,ajax 查询客户的其它信息
  $('#client_name').change(function(){
    customer_id = $('#client_name').val();
    //判断选择的值是否为空
         if(customer_id != ' '){
      $.ajax({
        'url':'../ajax_function/customer_info.php',
        'data':{customer_id:customer_id},
        'type':'post',
        'dataType':'json',
        'async':false,
        'success':function(data){
          var inp = ' <input type="text" name="contacts" value="" id="contacts_name" style="width:125px">';
          var sel = '<select id="contacts_sel" name="contacts" style="width:125px;height:25px"><option value="">请选择</option></select>'
      
          //获取联系人信息
          if(data[0].customer_id == undefined){
            $('#contacts').children().remove();
            $('#contacts').append(sel);
            //有多个联系人,添加到下拉选项框中
            
            for(var i in data[0]){
              var opt = '<option>'+data[0][i]+'</option>';
              $('#contacts_sel').append(opt);
            }
          } else {
            $('#contacts').children().remove();
            if($('#contacts').children().size() == 0){
              $('#contacts').append(inp);
            }
            $('#contacts_name').val(data[0].contacts_name);
            $('#contacts_phone').val(data[0].contacts_phone);
            $('#contacts_email').val(data[0].contacts_email);
          }
        },
        'error':function(){
          alert('获取客户信息失败');
        }
      })
    }
  })
  //更改联系人时.获取;联系人的信息
  $('#contacts_sel').live('change',function(){
    var sel_name = $(this).val();
    $.ajax({
      'url':'../ajax_function/customer_info.php',
      'data':{customer_id:customer_id,sel_name:sel_name},
      'type':'post',
      'dataType':'json',
      'async':false,
      //把获取的联系人电话和邮箱填到输入框中
      'success':function(data){
        
        $('#contacts_phone').val(data[0]);
        $('#contacts_email').val(data[1]);
      },
      'error':function(){
        alert('获取电话失败');
      }

    })
    
  })
  //跳转到导出页面
  $("#export_excel").click(function(){
    var mold_id = $(this).prev().val();
    window.open('mould_excel.php?action=mould_excel&id='+mold_id);
  })
  $('#unapproval_edit').click(function(){
  	document.mould_data_approval.action = 'mould_datado.php?action=edit';
  	document.mould_data_approval.submit();
  })
})
</script>
<title>模具报价-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'add'){
    $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
    $result_employee = $db->query($sql_employee);
    $array_employee = $result_employee->fetch_assoc();
    $sql_customer = "SELECT `customer_name`,`customer_id` FROM `db_customer_info`";
    $result_customer = $db->query($sql_customer);
    while( $customer_info = $result_customer->fetch_assoc()){
      $array_customer[] = $customer_info; 
    }
   
  ?>
  <h4>模具数据添加</h4>
  <!--提交型腔的数量类型-->
 <!-- <form action="" method="post" name="form1">
    <input type="hidden" value="" id="cavitys_type" name="cavity_types</form>
 <iframe id='frameFile' name='frameFile' style='display: none;'></iframe> -->
 
  <form action="mould_datado.php?action=add" name="mould_data_add" method="post" enctype="multipart/form-data">
 <script type="text/javascript" charset="utf-8">
  $(function(){
    //点击确定按钮时
  $("#submit").click(function(){
    var filepath = $("#file").val();  
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      return false;
    }
    })
  })
 </script>
  <style type="text/css" media="screen">
    #main_table tr td{border:1px solid grey;}
    input{width:80px;}
    *{margin:0px;}
    .dels{display:inline;float:right;width:45px;height:3px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);cursor:pointer}
    .adder{width:100px;height:30px;line-height:30px;text-align:center;display:block;border:1px solid grey;background-color:rgb(221,221,221);margin:2px auto;cursor:pointer;font-size:15px;}
    .autocomplete-items{position:absolute;z-index:22;background-color:grey;width:84px;}
  </style>
   <table id="main_table" style="word-wrap: break-word; word-break: break-all;">
    <input type="hidden" name="employeeid" value=<?php echo $employeeid ?> />
    <!--基本信息-->
    <tr>
         <td colspan="5" rowspan="5" style="text-align:center">
          <img src="../jtl.png" width="150" height="80" alt="logo">
         </td>
         <td colspan="9" rowspan="5" style="width:661px" >
           <p style="font-weight:blod;font-size:30px">嘉泰隆  模具费用分解表</p>
                    <p style="font-weight:blod;font-size:30px">JOTYLONG  Tooling Cost Break Down</p>
         </td>
       <td style="width:186px;padding-right:20px">客户名称/Customer</td>
           <td style="width:186px;padding-right:0px">
              <select name="client_name" class="client_name" id="client_name"  style="width:125px;height:25px">
                  <option value=' '>请选择</option>
                <?php foreach($array_customer as $key=>$value){ 
                  echo '<option  value="'.$value['customer_id'].'">'.$value['customer_name'].'</option>';
                }
                  ?>
      
        
              </select>
           </td>  
    </tr>
    <tr>
      <td>项目名称/Program</td>
             <td>
             <input type="text" name="project_name"  style="width:125px"/>
          </td>
    </tr>
    <tr>
          <td>联系人/Attention</td>
          <td id="contacts">
            <input type="text" name="contacts" value="" id="contacts_name" style="width:125px">
          </td>
           </tr>
           <tr>
          <td>电话/TEL</td>
          <td>
            <input  type="text" name="tel" value="" id="contacts_phone" style="width:125px"/>
          </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="" id="contacts_email" style="width:125px"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5" >模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav. Number</td>
               <td colspan="5" rowspan="6">
                <input type="file" name="file[]" id="file" class="input_file" multiple style="width:200px"/><br/>
           <span class="tag"> *支持JPG，GIF，PNG图片格式</span>
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
                    <input type="text" name="p_weight[]" id="p_weight" class="p_weight"/>
                    <span id="pp_weight"></span>
              </td>
              <td style="width:93px" id="material_no">
                <input type="text" name="m_material[]" id="m_material" class="m_material"/>
                <span id="pp_material"></span>
              </td>
              <td colspan="2" style="padding-right:8px" id="part_no">
                   <input type="text" name="part_number[]" id="part_number" class="part_number"  style="width:182px"/>
                   <span id="pp_number"></span>
               </td>
               <td colspan="2" id="file_no">
                   <input type="text" name="drawing_file[]" id="drawing_file" class="drawing_file" style="width:310px"/>
                   <span id="pp_file"></span>
              </td>
           </tr>
           <tr>
               <td colspan="5">模具尺寸/Mold Size (mm)</td>
               <td colspan="2">模具重量/Mold Weight(Kg)</td>
               <td colspan="2">模具寿命/Longevity</td>
               <td colspan="2">设备吨位/Press(Ton)</td>
           </tr>
           <tr id="adder_style">
              <td>
                <input type="text" name="m_length" id="m_length"  readonly />
              </td>
              <td>*</td>
              <td>
                 <input type="text" name="m_width" id="m_width" readonly />
              </td>
              <td>*</td>
              <td>
                    <input type="text" name="m_height" id="m_height" readonly /></td>
              </td>
              <td colspan="2">
                   <input type="text" name="m_weight" id="m_weight"  readonly style="width:176px" />
              </td>
              <td colspan="2" style="padding-right:8px">
                <input type="text" name="lift_time" id="lift_time" style="width:182px;"/>
              </td>
              <td colspan="2">
                <input type="text" name="tonnage" style="width:310px"/>
              </td>
           </tr>
           <!--选择排位-->
      <tr id="select_style" style="height:100px">
        <td colspan="16">
        <div style="display:inline">
          <span style="display:inline-block">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          合计:
          <br />
          型腔长:
            <input type="text" id="style_length_sum" name="style_length_sum">
          <br />
        
          型腔宽:
            <input type="text" id="style_width_sum" name="style_width_sum">
          </span>
          <span style="display:inline-block">           
            型腔1
            <br />
          
            <select class="cavity_length" name="cavity_length[]">
              <option value="" class="first_length">cavity长</option>
            </select>
            
            <br />
          
            
            <select class="cavity_width" name="cavity_width[]">
              <option value="" class="first_width">cavity宽</option>
            </select>
          </span>
          <span id="cavity_widths"></span>
        </div>
        <span  style="width:100px;display:inline-block"></span>
        <div style="display:inline-block;background:#eee;">
          <span style="padding-left:20px">型腔1</span><br />
          <span>
            <select  class="cavity_style_length" style="width:80px" name="cavity_style_length[]">
              <option value="" class="first_style_length">长度方向</option>
              <option value="1" >1</option>
            </select>
          </span><br />
          <span>
            <select  class="cavity_style_width" style="width:80px" name="cavity_style_width[]">
              <option value="" class="first_style_width">宽度方向</option>
              <option value="1">1<option>
            </select>
          </span>

        </div>
        <span id="cavity_width_styles"></span>
    
        </td>
      </tr>
           <!--材料费-->
           <tr id="material_last_tr">
               <td id="material_first_td" rowspan="9">材料加工费/Machining Materia</td>  
               <td colspan="4">材料名称/Material</td>
               <td >材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td style="width:93px">总重量/Weight(kg)</td>
               <td style="width:93px">单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
  <tr class="material_trs even">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="模架/Mould Base" base="" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px;color:red">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料">
           </div> 
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value="1">
             </td>
             <td style="width:93px">
                   <input name="material_length[]" id="base_length" class="material_length" type="text" placeholder="长">
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" id="base_width" class="material_width" type="text" placeholder="宽">
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" id="base_height" class="material_height" type="text" placeholder="高">
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="base_weight" class="material_weight">
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value="18">
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
                  <input name="mould_material[]" class="mould_material" value="<?php echo $mould_material_value ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">
               </td>
               <td>
                  <div class="autocomplete">
                    <?php if($mould_material_key == 'electrode'){ ?>
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="Cu">
        <?php }else{ ?>
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料">
        <?php } ?>
          </div>
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
                  <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" value="<?php echo $mould_heat_value ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight[]" type="text" class="heat_weight" value="0">
              </td>
              <td colspan="6">
                 <input name="heat_unit_price[]" type="text" class="heat_unit_price" value="18">  
              </td>
              <td colspan="2">
                <input name="heat_price[]" type="text" id="heat_price" class="heat_price" value="0" style="border-style:none">
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
             <input name="mold_standard[]" id="mold_standard" class="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:13px;width:193px" readonly value="<?php echo $mold_standard_value ?>">
        </td>
        <td colspan="2">
          <div class="autocomplete">
          <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification" placeholder="输入规格" style="width:183px">
           </div> 
           <!-- <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification">-->
        </td>
        <td colspan="5">
          <div class="autocomplete">
          <input id="standard_supplier" class="standard_supplier" type="text" name="standard_supplier[]" placeholder="输入品牌" style="width:200px">
           </div> 
      
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
                  <input name="mold_design_name[]" class="mold_design_name" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_design_value ?>">
              
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
                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_manufacturing_value ?>">
              
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
                <td colspan="4">
    <input type="text" name="other_fee_name[]" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" value=" 试模费/Trial Fee" readonly /> 
               </td>
               <td colspan="8">
    <input type="text" name="other_fee_instr[]" readonly style="width:500px" value=" 3 times mold trial(excluding raw material cost)" placeholder="">
             </td>
               <td colspan="2">
                <input type="text" name="other_fee_price[]" class="other_fee fixed_fee" id="trial_fee" value="2000">
               </td>
               <td  rowspan="6" id="total_others">
    <input type="text" name="total_others" value="0" id="tot_others" value="0">
               </td>
          </tr>
           <tr class="others_trs">
                <td colspan="4">
                  <input type="text" name="other_fee_name[]" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" value="运输费/Freight Fee" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="other_fee_instr[]" style="width:500px" readonly value="sample and tooling transport cost paid by customer" placeholder="">
     </td>
               <td colspan="2">
                   <input type="text" name="other_fee_price[]" class="other_fee fixed_fee" id="freight_fee" value="1000">
               </td>
          </tr>
           <tr class="others_trs" id="others_fees">
                <td colspan="4">
    <input type="text" name="" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" value="管理费/Management Fee" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="5%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="management_fee" class="other_fee" id="management_fee" value="0">
               </td>
     
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" value="利润/Profit" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="10%" placeholder="">
               </td>
               <td colspan="2">
                <input type="text" name="profit" class="other_fee" id="profit" value="0">
               </td>
          
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" value="税/VAT TAX(13%)" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="13%" placeholder="">
               </td>
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
                <td colspan="5">模具价格(元)含13%增值税/Mold with VAT(RMB)</td>
                <td colspan="11">
                  <input type="text" name="mold_with_vat" id="mold_with_vat" value="0">
                </td>
          </tr>
          <tr height="20"></tr>
          <tr>
              <td style="border-style:none" colspan="16" align="center"><input type="submit" name="submit" id="submit" value="保存" class="button" />
              &nbsp;&nbsp;
              <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
            
           </tr>
   </table>
  </form>
  <?php
    }elseif($action == 'approval_edit'){
      $mould_dataid = fun_check_int($_GET['id']);
    //查询模具报价的信息
    $sql = "SELECT * FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
    $result = $db->query($sql);
    if($result->num_rows){
      $array = $result->fetch_assoc();
     
      $sql_cfm = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' AND `quote_status` = 1";
      $result_cfm = $db->query($sql_cfm);
     // if(!$result_cfm->num_rows){
    
     //加工材料费的数据处理
     $old_material = [$array['mould_material'],$array['material_specification'],$array['materials_number'],$array['material_length'],$array['material_width'],$array['material_height'],$array['material_weight'],$array['material_unit_price'],$array['material_price']];
     $arrs_materials = getdata($old_material);
     //热处理费用的数据处理
     $old_heat = [$array['mould_heat_name'],$array['heat_weight'],$array['heat_unit_price'],$array['heat_price']];
     $arrs_heats = getdata($old_heat);
     //配件费的数据处理
     $old_standard = [$array['mold_standard'],$array['standard_specification'],$array['standard_supplier'],$array['standard_number'],$array['standard_unit_price'],$array['standard_price']];
     $arrs_standards = getdata($old_standard);
     //设计费的数据处理
     $old_design = [$array['mold_design_name'],$array['design_hour'],$array['design_unit_price'],$array['design_price']];
     $arrs_designs = getdata($old_design);
     //加工费的数据处理
     $old_manufacturing = [$array['mold_manufacturing'],$array['manufacturing_hour'],$array['manufacturing_unit_price'],$array['manufacturing_price']];
     $arrs_manufacturings = getdata($old_manufacturing);
    //其它费用的数据处理
    $old_others = [$array['other_fee_name'],$array['other_fee_instr'],$array['other_fee_price']];
    $arrs_others = getdata($old_others);
    //型腔数量
    $cavity_types = turn_arr($array['cavity_type']);
    //获取产品大小数据
    $p_lengths = turn_arr($array['p_length']);
    $p_widths  = turn_arr($array['p_width']);
    $p_heights  = turn_arr($array['p_height']);
    $p_weights = turn_arr($array['p_weight']);
    $p_materials = turn_arr($array['m_material']);
    $part_numbers = turn_arr($array['part_number']);
    $drawing_files = turn_arr($array['drawing_file']);
    //型腔排位数据
    $old_cavity = [$array['cavity_length'],$array['cavity_width']];
    $cavity_data = getdata($old_cavity);
    $old_cavity_style = [$array['cavity_style_length'],$array['cavity_style_width']];
    $cavity_style_data = getdata($old_cavity_style);

  ?>
  <h4>模具报价审批</h4> 
  <form action="mould_datado.php?action=approval_edit" name="mould_data" method="post" enctype="multipart/form-data">
  <style type="text/css" media="screen">
    #main_table tr td{border:1px solid grey;}
    input{width:80px;}
    *{margin:0px;}
    .dels{display:inline;float:right;width:45px;height:3px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);cursor:pointer}
    .adder{width:100px;height:30px;line-height:30px;text-align:center;display:block;border:1px solid grey;background-color:rgb(221,221,221);margin:2px auto;cursor:pointer;font-size:15px;}
    .autocomplete-items{position:absolute;z-index:22;background-color:grey;width:84px;}
  </style>
  <script type="text/javascript" charset="utf-8">
    $(function(){
      //判断型腔数如果大于1,增加删除按钮
      var cavity_num = $(".cavity_type").size();
      if(cavity_num >1){
                          $("#add_cavitys").after(del_cavity);
      } 
      //把型腔数量添加到布局
      

      //加入布局选项框
      var cavity_type_num = $(".cavity_type").size();
      for(var m=0;m<cavity_type_num;m++){
        var cavity_val = $(".cavity_type").eq(m).val();
        for(var h=0;h<cavity_val;h++){
      
      var h1 = h+1;
      
      var h2 = cavity_val/h1 + "";
      
      if(h2.indexOf('.') == -1){
      var cavity_style_layout = '<option value='+h1+'>'+h1+'</option>';

      if($(".cav_style_len").eq(m).html() != h1){
      $(".cavity_style_length").eq(m).append(cavity_style_layout);
      }
      }}
    
      
      var type_val = $(".cavity_type").eq(m).val();
      var p_length = $('.p_length').eq(m).val();
      var p_width = $('.p_width').eq(m).val();
      
      //获取当前布局的值
      var cavity_style_length = $(".cavity_style_length").eq(m).val();
      //计算型腔布局的宽并加入到布局选项中
      var cavity_style_width = Math.ceil(type_val/cavity_style_length);
      //通过获取的值计算型腔的长和宽
      var opt_length = (parseInt(p_length) + 110)*cavity_style_length;
      var opt_width  = (parseInt(p_width) + 110)*cavity_style_width;

    
      //把选项添加到型腔布局选项框中
      var  cavity_style_length  = '<option value='+opt_length+'>'+opt_length+'</option>';
      var  cavity_style_width  = '<option value='+opt_width+'>'+opt_width+'</option>';  
      var  cavity_zero = '<option value="0">0</option>';  
      //加入到排位长的选项中
      
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_length").eq(m).append(cavity_zero);
      $(".cavity_length").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_length").eq(m).append(cavity_zero);
         $(".cavity_length").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_length").eq(m).append(cavity_style_width);
        $(".cavity_length").eq(m).append(cavity_style_length);
      }
      //加入到排位宽的选项中
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_width").eq(m).append(cavity_zero);
      $(".cavity_width").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_width").eq(m).append(cavity_zero);
         $(".cavity_width").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_width").eq(m).append(cavity_style_width);
        $(".cavity_width").eq(m).append(cavity_style_length);
      }

      
    
      
    
              }
    })
  </script>
   <table id="main_table" style="word-wrap: break-word; word-break: break-all;">
    <input type="hidden" name="employeeid" value="<?php echo $employeeid ?>" />
    <input type="hidden" name="mold_id" value="<?php echo $array['mold_id'] ?>" >
    <input type="hidden" name="id" value="<?php echo $mould_dataid ?>">
    <input type="hidden" name="upload_final_path" value="<?php echo $array['upload_final_path'] ?>">
    <!--基本信息-->
    <tr>
         <td colspan="5" rowspan="5" style="text-align:center">
          <img src="../jtl.png" width="150" height="80" alt="logo">
         </td>
         <td colspan="9" rowspan="5" style="width:661px" >
       <p style="font-weight:blod;font-size:30px">嘉泰隆  模具费用分解表</p>
                    <p style="font-weight:blod;font-size:30px">JOTYLONG  Tooling Cost Break Down</p>
         </td>
       <td style="width:186px;padding-right:20px">客户名称/Customer</td>
           <td style="width:186px;padding-right:0px">
               <input type="text" name="client_name" value="<?php echo $array['client_name'] ?>" style="width:125px;" />
           </td>  
    </tr>
    <tr>
      <td>项目名称/Program</td>
             <td>
             <input type="text" name="project_name" value="<?php echo $array['project_name'] ?>" style="width:125px"/>
          </td>
    </tr>
    <tr>
          <td>联系人/Attention</td>
          <td>
            <input type="text" name="contacts" value="<?php echo $array['contacts']; ?>" style="width:125px">
          </td>
           </tr>
           <tr>
          <td>电话/TEL</td>
          <td>
            <input  type="text" name="tel" value="<?php echo $array['tel']; ?>" style="width:125px"/>
          </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="<?php echo $array['email']; ?>" style="width:125px"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5" >模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav. Number</td>
               <td colspan="5" rowspan="6">
                  <?php $image_filepath = $array['upload_final_path'];
      if(stristr($image_filepath,'$') == true){
        $image_filepath = substr($image_filepath,0,strripos($image_filepath,"$"));
      }
        
        $image_file = "<img width=\"185\" height=\"100\" src=\"".$image_filepath."\" /><br>";
        echo $image_file;
       ?>
                <input type="file" name="file[]" id="file" class="input_file" multiple style="width:200px"/><br/>
           <span class="tag"> *支持JPG，GIF，PNG图片格式</span>
               </td>
               <td colspan="2">首次试模时间/T1 Time</td>
                 <td colspan="2">最终交付时间/Lead Timeme</td>
           </tr>
           <tr>
               <td colspan="5" style="padding-right:2px">
                <input  type="text" name="mould_name" id="mould_name" value="<?php echo $array['mould_name'] ?>" class="input_tx"  style="width:315px;margin-right:2px"/>
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
      <option value="1" <?php echo ($array['k_num'] == 1)?'selected':' ' ?>>1k</option>
      <option value="2" <?php echo ($array['k_num'] == 2)?'selected':' ' ?>>2k</option>
                      </select>
                      <?php foreach($cavity_types as $k=>$v){ ?>
                         <input type="text" name="cavity_type[]" class="cavity_type" id="types_cav" value="<?php echo $v ?>" style="width:25px">
                      <?php } ?>
                     <button id="add_cavitys" type="button">添加</button>

               </td>
               <td colspan="2" style="padding_style:8px">
                <input type="text" name="t_time" class="input_tx" value="<?php echo $array['t_time'] ?>" style="width:182px"/>
              </td>
              <td colspan="2">
                <input type="text" name="lead_time" class="input_tx" value="<?php echo $array['lead_time'] ?>"  style="width:310px" />
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
                 <?php foreach($p_lengths as $k=>$v){   ?>
                <input type="text" name="p_length[]" id="p_length" value="<?php echo $v ?>" class="p_length"/>
                 <?php } ?>     
                <span id="pp_length"></span>
              </td>
              <td >*</td>
              <td style="width:93px" id="width_no">
              <?php foreach($p_widths as $k=>$v){ ?>
                <input type="text" name="p_width[]" id="p_width" value="<?php echo $v ?>" class="p_width" />
              <?php } ?>
                <span id="pp_width"></span>
              </td>
              <td>*</td>
              <td style="width:93px" id="height_no">
                <?php foreach($p_heights as $k=>$v) { ?>
                      <input type="text" name="p_height[]" id="p_height" value="<?php echo $v ?>" class="p_height" />
                 <?php } ?>
                     <span id="pp_height"></span>
              </td>
              <td style="width:93px" id="weight_no">
                 <?php foreach($p_weights as $k=>$v){ ?>
                    <input type="text" name="p_weight[]" id="p_weight" value="<?php echo $v ?>" class="p_weight"/>
                 <?php } ?>   
                    <span id="pp_weight"></span>
              </td>
              <td style="width:93px" id="material_no">
                  <?php foreach($p_materials as $k=>$v){ ?>
                <input type="text" name="m_material[]" id="m_material" value="<?php echo $v ?>" class="m_material"/>
                  <?php } ?>  
                <span id="pp_material"></span>
              </td>
              <td colspan="2" style="padding-right:8px" id="part_no">
                <?php foreach($part_numbers as $k=>$v){ ?>
                   <input type="text" name="part_number[]" id="part_number" class="part_number" value="<?php echo $v ?>"  style="width:182px"/>
                <?php } ?>   
                   <span id="pp_number"></span>
               </td>
               <td colspan="2" id="file_no">
                  <?php foreach($drawing_files as $k=>$v){ ?>
                   <input type="text" name="drawing_file[]" id="drawing_file" class="drawing_file" value="<?php echo $v ?>" style="width:310px"/>
                 <?php } ?>
                   <span id="pp_file"></span>
              </td>
           </tr>
           <tr>
               <td colspan="5">模具尺寸/Mold Size (mm)</td>
               <td colspan="2">模具重量/Mold Weight(Kg)</td>
               <td colspan="2">模具寿命/Longevity</td>
               <td colspan="2">设备吨位/Press(Ton)</td>
           </tr>
           <tr id="adder_style">
              <td>
                <input type="text" name="m_length" id="m_length" value="<?php echo $array['m_length'] ?>" readonly  />
              </td>
              <td>*</td>
              <td>
                 <input type="text" name="m_width" id="m_width" value="<?php echo $array['m_width'] ?>" readonly />
              </td>
              <td>*</td>
              <td>
                    <input type="text" name="m_height" id="m_height" value="<?php echo $array['m_height'] ?>"  readonly /></td>
              </td>
              <td colspan="2">
                   <input type="text" name="m_weight" id="m_weight" value="<?php echo $array['m_weight'] ?>" readonly  style="width:176px" />
              </td>
              <td colspan="2" style="padding-right:8px">
                <input type="text" name="lift_time" id="lift_time" value="<?php echo $array['lift_time'] ?>" style="width:182px;"/>
              </td>
              <td colspan="2">
                <input type="text" name="tonnage" value="<?php echo $array['tonnage'] ?>" style="width:310px"/>
              </td>
           </tr>
           <!--选择排位-->
      <tr id="select_style" style="height:100px">
        <td colspan="16">
        <div style="display:inline">
          <span style="display:inline-block">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          合计:
          <br />
          型腔长:
            <input type="text" id="style_length_sum" value="<?php echo $array['style_length_sum'] ?>">
          <br />
        
          型腔宽:
            <input type="text" id="style_width_sum" value="<?php echo $array['style_width_sum'] ?>">
          </span>
          <?php foreach($cavity_data as $k=>$v){ ?>
          <span style="display:inline-block">           
            型腔<?php echo $k+1 ?>
            <br />
          
            <select class="cavity_length" name="cavity_length[]">
              <option value="" class="first_length">cavity长</option>
              <option class="cav_len" value="<?php echo $v[0] ?>" selected class="first_length"><?php echo $v[0] ?></option>
            </select>
            
            <br />
          
            
            <select class="cavity_width" name="cavity_width[]">
              <option value="" class="first_width">cavity宽</option>
              <option class="cav_wid" value="<?php echo $v[1] ?>" selected class="first_width"><?php echo $v[1] ?></option>

            </select>
          </span>
          <?php } ?>
          <span id="cavity_widths"></span>
        </div>
        <span  style="width:100px;display:inline-block"></span>
        <?php foreach($cavity_style_data as $k=>$v){ ?>
        <div style="display:inline-block;background:#eee;">
          <span style="padding-left:20px">型腔<?php echo $k+1 ?></span><br />
          <span>
            <select  class="cavity_style_length" name="cavity_style_length[]" style="width:80px">
              <option value="" class="first_style_length">长度方向</option>
              <option class="cav_style_len" value="<?php echo $v[0] ?>" selected  ><?php echo $v[0] ?></option>
            </select>
          </span><br />
          <span>
            <select  class="cavity_style_width" name="cavity_style_width[]" style="width:80px">
              <option value="" class="first_style_width">宽度方向</option>
              <option value="<?php echo $v[1] ?>" selected ><?php echo $v[1] ?><option>
            </select>
          </span>

        </div>
        <?php } ?>
        <span id="cavity_width_styles"></span>
    
        </td>
      </tr>
           <!--加工材料费-->
           <tr id="material_last_tr">
               <td id="material_first_td" rowspan="9">材料加工费/Machining Materia</td>  
               <td colspan="4">材料名称/Material</td>
               <td>材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td style="width:93px">总重量/Weight(kg)</td>
               <td style="width:93px">单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
  <tr class="material_trs even">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="模架/Mould Base" base="" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px;color:red">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $arrs_materials[0][1] ?>">
           </div> 
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $arrs_materials[0][2]?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" id="base_length" class="material_length" type="text" placeholder="长" value=<?php echo $arrs_materials[0][3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" id="base_width" class="material_width" type="text" placeholder="宽" value=<?php echo $arrs_materials[0][4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" id="base_height" class="material_height" type="text" placeholder="高" value=<?php echo $arrs_materials[0][5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="base_weight" class="material_weight" value=<?php echo $arrs_materials[0][6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $arrs_materials[0][7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" id="material_price" class="material_price" value=<?php echo $arrs_materials[0][8] ?>>   
             </td>
               <td rowspan="8" id="total_machining"><input type="text" class="min_total" value="<?php echo $array['total_machining'] ?>" name="total_machining"></td>   
           </tr>
              <?php
              $i = 0;
              unset($arrs_materials[0]);
               foreach($arrs_materials as $mould_material_key=>$mould_material_value){
                

        ?>
           <tr class="material_trs">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="<?php echo $mould_material_value[0] ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $mould_material_value[1] ?>">
           </div>  
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $mould_material_value[2] ?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" class="material_length" id="material_length" type="text" placeholder="长" value=<?php echo $mould_material_value[3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" class="material_width" id="material_width" type="text" placeholder="宽" value=<?php echo $mould_material_value[4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" class="material_height" id="material_height" type="text" placeholder="高" value=<?php echo $mould_material_value[5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight" value=<?php echo $mould_material_value[6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $mould_material_value[7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" class="material_price" id="material_price" value=<?php echo $mould_material_value[8] ?>>  
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
        foreach($arrs_heats as $mould_heat_key=>$mould_heat_value){
           ?>
           <tr class="heat_trs">
              <td colspan="4">
                  <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" value=<?php echo $mould_heat_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight[]" type="text" class="heat_weight" value=<?php echo $mould_heat_value[1] ?>>
              </td>
              <td colspan="6">
                 <input name="heat_unit_price[]" type="text" class="heat_unit_price" value=<?php echo $mould_heat_value[2] ?>>  
              </td>
              <td colspan="2">
                <input name="heat_price[]" type="text" id="heat_price" class="heat_price" value="<?php echo $mould_heat_value[3] ?>" style="border-style:none">
              </td>
                <?php 
                
                 if($i == 0){
                  echo '<td rowspan="4" id="total_heats">
                    <input type="text" name="total_heat"  class="min_total" value="'.$array["total_heat"].'"/>
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
        foreach($arrs_standards as $mold_standard_key=>$mold_standard_value){
      ?>
      <tr class="parts_trs"> 
        <td colspan="4">
             <input name="mold_standard[]" id="mold_standard" class="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:13px;width:193px" readonly value="<?php echo $mold_standard_value[0] ?>">
        </td>

          <td colspan="2">
          <div class="autocomplete">
          <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification" placeholder="输入规格" style="width:183px" value="<?php echo $mold_standard_value[1] ?>">
           </div> 
           <!-- <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification">-->
        </td>
        <td colspan="5">
          <div class="autocomplete">
          <input id="standard_supplier" class="standard_supplier" type="text" name="standard_supplier[]" value="<?php echo $mold_standard_value[2] ?>" placeholder="输入品牌" style="width:200px">
           </div> 
      
        </td>
        <td>
            <input type="text" name="standard_number[]" class="standard_number" value=<?php echo $mold_standard_value[3] ?>>  
        </td>
        <td>
               <input type="text" name="standard_unit_price[]" class="standard_unit_price" value=<?php echo $mold_standard_value[4] ?>>
        </td>
        <td>
           <input type="text" name="standard_price[]" class="standard_price" value="<?php echo $mold_standard_value[5] ?>"> 
        </td>
         <?php 
                
                 if($i == 0){
                  echo '<td rowspan="8" id="total_standard">
      <input type="text" class="min_total"  name="total_standard" value='.$array['total_standard'].'>
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
            foreach($arrs_designs as $mould_design_key => $mould_design_value){ 
            ?>
           <tr class="design_trs">
              <td colspan="4">
                  <input name="mold_design_name[]" class="mold_design_name" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_design_value[0] ?>">
              
              </td>
              <td colspan="2">
                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" value=<?php echo $mould_design_value[1] ?>>
              </td>
              <td colspan="6">
                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value=<?php echo $mould_design_value[2] ?>>
              </td>
              <td colspan="2">
                <input type="text" name="design_price[]" class="design_price" id="design_price" value=<?php echo $mould_design_value[3] ?>>
              </td>
                  <?php 
                
                 if($i == 0){
                  echo '<td rowspan="5" id="total_designs">
                    <input type="text" class="min_total" name="total_designs" value='.$array['total_designs'].'>
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
          foreach($arrs_manufacturings as $mould_manufacturing_key=>$mould_manufacturing_value){
          ?>
           <tr class="manus_trs">
              <td colspan="4">
                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_manufacturing_value[0] ?>">
              
              </td>
              <td colspan="2">
                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" class="manufacturing_hour" value=<?php echo $mould_manufacturing_value[1] ?>>
              </td>
              <td colspan="6">
                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" class="manufacturing_unit_price" value=<?php echo $mould_manufacturing_value[2] ?>>
              </td>
              <td colspan="2">
               <input type="text" name="manufacturing_price[]" class="manufacturing_price" id="manufacuring_price" value=<?php echo $mould_manufacturing_value[3] ?>> 
              </td>
                      <?php 
                
                 if($i == 0){
                  echo '<td rowspan="11" id="total_manufacturing">
                         <input type="text" class="min_total" name="total_manufacturing" value='.$array['total_manufacturing'].'>
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
          <?php $i = 0;foreach($arrs_others as $other_key=>$others_value){ ?>
           <tr class="others_trs">
                <td colspan="4">
                  <input type="text" name="other_fee_name[]" value=<?php echo  $others_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" /> 
                </td>
               <td colspan="8">
                
    <input type="text" name="other_fee_instr[]" value="<?php echo $others_value[1] ?>" readonly style="width:500px">
     </td>
               <td colspan="2">
                   <input type="text" name="other_fee_price[]" class="other_fee fixed_fee" id="freight_fee" value=<?php echo $others_value[2] ?>>
               </td>
                <?php 

                  if($i==0){ 
                    echo '<td  rowspan="6" id="total_others">
        <input type="text" name="total_others" value='.$array["total_others"].' id="tot_others" />
                    </td>';
              }
               $i++;
               ?>
          </tr>
          <?php } ?>
           <tr class="others_trs" id="others_fees">
                <td colspan="4">
    <input type="text" name="" value="管理费/Management Fee" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="5%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="management_fee" class="other_fee" id="management_fee" value=<?php echo $array['management_fee'] ?>>
               </td>
     
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="利润/Profit" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="10%" placeholder="">
               </td>
               <td colspan="2">
                <input type="text" name="profit" class="other_fee" id="profit" value=<?php echo $array['profit'] ?>>
               </td>
          
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="税/VAT TAX(13%)" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="13%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="vat_tax" class="other_fee" id="vat_tax" value=<?php echo $array['vat_tax'] ?>
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
                   <input type="text" name="mold_price_rmb" id="mold_price_rmb" value=<?php echo $array['mold_price_rmb'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(USD)/Mold Price(USD) Rate=6.5</td>
                <td colspan="11">
                   <input type="text" name="mold_price_usd" id="mold_price_usd" value=<?php echo $array['mold_price_usd'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(元)含13%增值税/Mold with VAT(RMB)</td>
                <td colspan="11">
                  <input type="text" name="mold_with_vat" id="mold_with_vat" value=<?php echo $array['mold_with_vat'] ?>>
                </td>
          </tr>
          <tr height="20"></tr>
          <tr>
            
              <td style="border-style:none" colspan="16" align="center">
              <input id="id" name="id" type="hidden" value="<?php echo $_GET['id'] ?>">
              <span id="export_excel" style="width:80px;height:26px; display: inline-block;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:26px;">导出</span>
              &nbsp;&nbsp;
              <input type="submit" name="button" id="submit" value="修改" class="button" />
              &nbsp;&nbsp;
              <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
       
           </tr>
   </table>
  </form>
  <?php
      
    }
  }elseif($action == 'approval'){
      $mould_dataid = fun_check_int($_GET['id']);
    //查询模具报价的信息
    $sql = "SELECT * FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
    $result = $db->query($sql);
    if($result->num_rows){
      $array = $result->fetch_assoc();
     
      $sql_cfm = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' AND `quote_status` = 1";
      $result_cfm = $db->query($sql_cfm);
     // if(!$result_cfm->num_rows){
    
     //加工材料费的数据处理
     $old_material = [$array['mould_material'],$array['material_specification'],$array['materials_number'],$array['material_length'],$array['material_width'],$array['material_height'],$array['material_weight'],$array['material_unit_price'],$array['material_price']];
     $arrs_materials = getdata($old_material);
     //热处理费用的数据处理
     $old_heat = [$array['mould_heat_name'],$array['heat_weight'],$array['heat_unit_price'],$array['heat_price']];
     $arrs_heats = getdata($old_heat);
     //配件费的数据处理
     $old_standard = [$array['mold_standard'],$array['standard_specification'],$array['standard_supplier'],$array['standard_number'],$array['standard_unit_price'],$array['standard_price']];
     $arrs_standards = getdata($old_standard);
     //设计费的数据处理
     $old_design = [$array['mold_design_name'],$array['design_hour'],$array['design_unit_price'],$array['design_price']];
     $arrs_designs = getdata($old_design);
     //加工费的数据处理
     $old_manufacturing = [$array['mold_manufacturing'],$array['manufacturing_hour'],$array['manufacturing_unit_price'],$array['manufacturing_price']];
     $arrs_manufacturings = getdata($old_manufacturing);
    //其它费用的数据处理
    $old_others = [$array['other_fee_name'],$array['other_fee_instr'],$array['other_fee_price']];
    $arrs_others = getdata($old_others);
    //型腔数量
    $cavity_types = turn_arr($array['cavity_type']);
    //获取产品大小数据
    $p_lengths = turn_arr($array['p_length']);
    $p_widths  = turn_arr($array['p_width']);
    $p_heights  = turn_arr($array['p_height']);
    $p_weights = turn_arr($array['p_weight']);
    $p_materials = turn_arr($array['m_material']);
    $part_numbers = turn_arr($array['part_number']);
    $drawing_files = turn_arr($array['drawing_file']);
    //型腔排位数据
    $old_cavity = [$array['cavity_length'],$array['cavity_width']];
    $cavity_data = getdata($old_cavity);
    $old_cavity_style = [$array['cavity_style_length'],$array['cavity_style_width']];
    $cavity_style_data = getdata($old_cavity_style);

  ?>
  <h4>模具报价审批</h4> 
  <form action="mould_datado.php?action=approval" name="mould_data_approval" method="post" enctype="multipart/form-data">
  <style type="text/css" media="screen">
    #main_table tr td{border:1px solid grey;}
    input{width:80px;}
    *{margin:0px;}
    .dels{display:inline;float:right;width:45px;height:3px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);cursor:pointer}
    .adder{width:100px;height:30px;line-height:30px;text-align:center;display:block;border:1px solid grey;background-color:rgb(221,221,221);margin:2px auto;cursor:pointer;font-size:15px;}
    .autocomplete-items{position:absolute;z-index:22;background-color:grey;width:84px;}
  </style>
  <script type="text/javascript" charset="utf-8">
    $(function(){
      //判断型腔数如果大于1,增加删除按钮
      var cavity_num = $(".cavity_type").size();
      if(cavity_num >1){
                          $("#add_cavitys").after(del_cavity);
      } 
      //把型腔数量添加到布局
      

      //加入布局选项框
      var cavity_type_num = $(".cavity_type").size();
      for(var m=0;m<cavity_type_num;m++){
        var cavity_val = $(".cavity_type").eq(m).val();
        for(var h=0;h<cavity_val;h++){
      
      var h1 = h+1;
      
      var h2 = cavity_val/h1 + "";
      
      if(h2.indexOf('.') == -1){
      var cavity_style_layout = '<option value='+h1+'>'+h1+'</option>';

      if($(".cav_style_len").eq(m).html() != h1){
      $(".cavity_style_length").eq(m).append(cavity_style_layout);
      }
      }}
    
      
      var type_val = $(".cavity_type").eq(m).val();
      var p_length = $('.p_length').eq(m).val();
      var p_width = $('.p_width').eq(m).val();
      
      //获取当前布局的值
      var cavity_style_length = $(".cavity_style_length").eq(m).val();
      //计算型腔布局的宽并加入到布局选项中
      var cavity_style_width = Math.ceil(type_val/cavity_style_length);
      //通过获取的值计算型腔的长和宽
      var opt_length = (parseInt(p_length) + 110)*cavity_style_length;
      var opt_width  = (parseInt(p_width) + 110)*cavity_style_width;

    
      //把选项添加到型腔布局选项框中
      var  cavity_style_length  = '<option value='+opt_length+'>'+opt_length+'</option>';
      var  cavity_style_width  = '<option value='+opt_width+'>'+opt_width+'</option>';  
      var  cavity_zero = '<option value="0">0</option>';  
      //加入到排位长的选项中
      
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_length").eq(m).append(cavity_zero);
      $(".cavity_length").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_length").eq(m).append(cavity_zero);
         $(".cavity_length").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_length").eq(m).append(cavity_style_width);
        $(".cavity_length").eq(m).append(cavity_style_length);
      }
      //加入到排位宽的选项中
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_width").eq(m).append(cavity_zero);
      $(".cavity_width").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_width").eq(m).append(cavity_zero);
         $(".cavity_width").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_width").eq(m).append(cavity_style_width);
        $(".cavity_width").eq(m).append(cavity_style_length);
      }

      
    
      
    
              }
    })
  </script>
   <table id="main_table" style="word-wrap: break-word; word-break: break-all;">
    <input type="hidden" name="employeeid" value="<?php echo $employeeid ?>" />
    <!--<input type="hidden" name="mold_id" value="<?php echo $array['mold_id'] ?>" >-->
    <input type="hidden" name="id" value="<?php echo $mould_dataid ?>">
    <input type="hidden" name="upload_final_path" value="<?php echo $array['upload_final_path'] ?>">
    <!--基本信息-->
    <tr>
         <td colspan="5" rowspan="5" style="text-align:center">
          <img src="../jtl.png" width="150" height="80" alt="logo">
         </td>
         <td colspan="9" rowspan="5" style="width:661px" >
       <p style="font-weight:blod;font-size:30px">嘉泰隆  模具费用分解表</p>
                    <p style="font-weight:blod;font-size:30px">JOTYLONG  Tooling Cost Break Down</p>
         </td>
       <td style="width:186px;padding-right:20px">客户名称/Customer</td>
           <td style="width:186px;padding-right:0px">
               <input type="text" name="client_name" value="<?php echo $array['client_name'] ?>" style="width:125px;" />
           </td>  
    </tr>
    <tr>
      <td>项目名称/Program</td>
             <td>
             <input type="text" name="project_name" value="<?php echo $array['project_name'] ?>" style="width:125px"/>
          </td>
    </tr>
    <tr>
          <td>联系人/Attention</td>
          <td>
            <input type="text" name="contacts" value="<?php echo $array['contacts']; ?>" style="width:125px">
          </td>
           </tr>
           <tr>
          <td>电话/TEL</td>
          <td>
            <input  type="text" name="tel" value="<?php echo $array['tel']; ?>" style="width:125px"/>
          </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="<?php echo $array['email']; ?>" style="width:125px"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5" >模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav. Number</td>
               <td colspan="5" rowspan="6">
                  <?php $image_filepath = $array['upload_final_path'];
      if(stristr($image_filepath,'$') == true){
        $image_filepath = substr($image_filepath,0,strripos($image_filepath,"$"));
      }
        
        $image_file = "<img width=\"185\" height=\"100\" src=\"".$image_filepath."\" /><br>";
        echo $image_file;
       ?>
                <input type="file" name="file[]" id="file" class="input_file" multiple style="width:200px"/><br/>
           <span class="tag"> *支持JPG，GIF，PNG图片格式</span>
               </td>
               <td colspan="2">首次试模时间/T1 Time</td>
                 <td colspan="2">最终交付时间/Lead Timeme</td>
           </tr>
           <tr>
               <td colspan="5" style="padding-right:2px">
                <input  type="text" name="mould_name" id="mould_name" value="<?php echo $array['mould_name'] ?>" class="input_tx"  style="width:315px;margin-right:2px"/>
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
      <option value="1" <?php echo ($array['k_num'] == 1)?'selected':' ' ?>>1k</option>
      <option value="2" <?php echo ($array['k_num'] == 2)?'selected':' ' ?>>2k</option>
                      </select>
                      <?php foreach($cavity_types as $k=>$v){ ?>
                         <input type="text" name="cavity_type[]" class="cavity_type" id="types_cav" value="<?php echo $v ?>" style="width:25px">
                      <?php } ?>
                     <button id="add_cavitys" type="button">添加</button>

               </td>
               <td colspan="2" style="padding_style:8px">
                <input type="text" name="t_time" class="input_tx" value="<?php echo $array['t_time'] ?>" style="width:182px"/>
              </td>
              <td colspan="2">
                <input type="text" name="lead_time" class="input_tx" value="<?php echo $array['lead_time'] ?>"  style="width:310px" />
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
                 <?php foreach($p_lengths as $k=>$v){   ?>
                <input type="text" name="p_length[]" id="p_length" value="<?php echo $v ?>" class="p_length"/>
                 <?php } ?>     
                <span id="pp_length"></span>
              </td>
              <td >*</td>
              <td style="width:93px" id="width_no">
              <?php foreach($p_widths as $k=>$v){ ?>
                <input type="text" name="p_width[]" id="p_width" value="<?php echo $v ?>" class="p_width" />
              <?php } ?>
                <span id="pp_width"></span>
              </td>
              <td>*</td>
              <td style="width:93px" id="height_no">
                <?php foreach($p_heights as $k=>$v) { ?>
                      <input type="text" name="p_height[]" id="p_height" value="<?php echo $v ?>" class="p_height" />
                 <?php } ?>
                     <span id="pp_height"></span>
              </td>
              <td style="width:93px" id="weight_no">
                 <?php foreach($p_weights as $k=>$v){ ?>
                    <input type="text" name="p_weight[]" id="p_weight" value="<?php echo $v ?>" class="p_weight"/>
                 <?php } ?>   
                    <span id="pp_weight"></span>
              </td>
              <td style="width:93px" id="material_no">
                  <?php foreach($p_materials as $k=>$v){ ?>
                <input type="text" name="m_material[]" id="m_material" value="<?php echo $v ?>" class="m_material"/>
                  <?php } ?>  
                <span id="pp_material"></span>
              </td>
              <td colspan="2" style="padding-right:8px" id="part_no">
                <?php foreach($part_numbers as $k=>$v){ ?>
                   <input type="text" name="part_number[]" id="part_number" class="part_number" value="<?php echo $v ?>"  style="width:182px"/>
                <?php } ?>   
                   <span id="pp_number"></span>
               </td>
               <td colspan="2" id="file_no">
                  <?php foreach($drawing_files as $k=>$v){ ?>
                   <input type="text" name="drawing_file[]" id="drawing_file" class="drawing_file" value="<?php echo $v ?>" style="width:310px"/>
                 <?php } ?>
                   <span id="pp_file"></span>
              </td>
           </tr>
           <tr>
               <td colspan="5">模具尺寸/Mold Size (mm)</td>
               <td colspan="2">模具重量/Mold Weight(Kg)</td>
               <td colspan="2">模具寿命/Longevity</td>
               <td colspan="2">设备吨位/Press(Ton)</td>
           </tr>
           <tr id="adder_style">
              <td>
                <input type="text" name="m_length" id="m_length" value="<?php echo $array['m_length'] ?>" readonly  />
              </td>
              <td>*</td>
              <td>
                 <input type="text" name="m_width" id="m_width" value="<?php echo $array['m_width'] ?>" readonly />
              </td>
              <td>*</td>
              <td>
                    <input type="text" name="m_height" id="m_height" value="<?php echo $array['m_height'] ?>"  readonly /></td>
              </td>
              <td colspan="2">
                   <input type="text" name="m_weight" id="m_weight" value="<?php echo $array['m_weight'] ?>" readonly  style="width:176px" />
              </td>
              <td colspan="2" style="padding-right:8px">
                <input type="text" name="lift_time" id="lift_time" value="<?php echo $array['lift_time'] ?>" style="width:182px;"/>
              </td>
              <td colspan="2">
                <input type="text" name="tonnage" value="<?php echo $array['tonnage'] ?>" style="width:310px"/>
              </td>
           </tr>
           <!--选择排位-->
      <tr id="select_style" style="height:100px">
        <td colspan="16">
        <div style="display:inline">
          <span style="display:inline-block">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          合计:
          <br />
          型腔长:
            <input type="text" id="style_length_sum" value="<?php echo $array['style_length_sum'] ?>">
          <br />
        
          型腔宽:
            <input type="text" id="style_width_sum" value="<?php echo $array['style_width_sum'] ?>">
          </span>
          <?php foreach($cavity_data as $k=>$v){ ?>
          <span style="display:inline-block">           
            型腔<?php echo $k+1 ?>
            <br />
          
            <select class="cavity_length" name="cavity_length[]">
              <option value="" class="first_length">cavity长</option>
              <option class="cav_len" value="<?php echo $v[0] ?>" selected class="first_length"><?php echo $v[0] ?></option>
            </select>
            
            <br />
          
            
            <select class="cavity_width" name="cavity_width[]">
              <option value="" class="first_width">cavity宽</option>
              <option class="cav_wid" value="<?php echo $v[1] ?>" selected class="first_width"><?php echo $v[1] ?></option>

            </select>
          </span>
          <?php } ?>
          <span id="cavity_widths"></span>
        </div>
        <span  style="width:100px;display:inline-block"></span>
        <?php foreach($cavity_style_data as $k=>$v){ ?>
        <div style="display:inline-block;background:#eee;">
          <span style="padding-left:20px">型腔<?php echo $k+1 ?></span><br />
          <span>
            <select  class="cavity_style_length" name="cavity_style_length[]" style="width:80px">
              <option value="" class="first_style_length">长度方向</option>
              <option class="cav_style_len" value="<?php echo $v[0] ?>" selected  ><?php echo $v[0] ?></option>
            </select>
          </span><br />
          <span>
            <select  class="cavity_style_width" name="cavity_style_width[]" style="width:80px">
              <option value="" class="first_style_width">宽度方向</option>
              <option value="<?php echo $v[1] ?>" selected ><?php echo $v[1] ?><option>
            </select>
          </span>

        </div>
        <?php } ?>
        <span id="cavity_width_styles"></span>
    
        </td>
      </tr>
           <!--加工材料费-->
           <tr id="material_last_tr">
               <td id="material_first_td" rowspan="9">材料加工费/Machining Materia</td>  
               <td colspan="4">材料名称/Material</td>
               <td>材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td style="width:93px">总重量/Weight(kg)</td>
               <td style="width:93px">单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
  <tr class="material_trs even">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="模架/Mould Base" base="" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px;color:red">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $arrs_materials[0][1] ?>">
           </div> 
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $arrs_materials[0][2]?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" id="base_length" class="material_length" type="text" placeholder="长" value=<?php echo $arrs_materials[0][3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" id="base_width" class="material_width" type="text" placeholder="宽" value=<?php echo $arrs_materials[0][4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" id="base_height" class="material_height" type="text" placeholder="高" value=<?php echo $arrs_materials[0][5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="base_weight" class="material_weight" value=<?php echo $arrs_materials[0][6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $arrs_materials[0][7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" id="material_price" class="material_price" value=<?php echo $arrs_materials[0][8] ?>>   
             </td>
               <td rowspan="8" id="total_machining"><input type="text" class="min_total" value="<?php echo $array['total_machining'] ?>" name="total_machining"></td>   
           </tr>
              <?php
              $i = 0;
              unset($arrs_materials[0]);
               foreach($arrs_materials as $mould_material_key=>$mould_material_value){
                

        ?>
           <tr class="material_trs">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="<?php echo $mould_material_value[0] ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $mould_material_value[1] ?>">
           </div>  
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $mould_material_value[2] ?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" class="material_length" id="material_length" type="text" placeholder="长" value=<?php echo $mould_material_value[3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" class="material_width" id="material_width" type="text" placeholder="宽" value=<?php echo $mould_material_value[4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" class="material_height" id="material_height" type="text" placeholder="高" value=<?php echo $mould_material_value[5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight" value=<?php echo $mould_material_value[6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $mould_material_value[7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" class="material_price" id="material_price" value=<?php echo $mould_material_value[8] ?>>  
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
        foreach($arrs_heats as $mould_heat_key=>$mould_heat_value){
           ?>
           <tr class="heat_trs">
              <td colspan="4">
                  <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" value=<?php echo $mould_heat_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight[]" type="text" class="heat_weight" value=<?php echo $mould_heat_value[1] ?>>
              </td>
              <td colspan="6">
                 <input name="heat_unit_price[]" type="text" class="heat_unit_price" value=<?php echo $mould_heat_value[2] ?>>  
              </td>
              <td colspan="2">
                <input name="heat_price[]" type="text" id="heat_price" class="heat_price" value="<?php echo $mould_heat_value[3] ?>" style="border-style:none">
              </td>
                <?php 
                
                 if($i == 0){
                  echo '<td rowspan="4" id="total_heats">
                    <input type="text" name="total_heat"  class="min_total" value="'.$array["total_heat"].'"/>
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
        foreach($arrs_standards as $mold_standard_key=>$mold_standard_value){
      ?>
      <tr class="parts_trs"> 
        <td colspan="4">
             <input name="mold_standard[]" id="mold_standard" class="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:13px;width:193px" readonly value="<?php echo $mold_standard_value[0] ?>">
        </td>
        <td colspan="2">
          <div class="autocomplete">
          <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification" placeholder="输入规格" style="width:183px" value="<?php echo $mold_standard_value[1] ?>">
           </div> 
           <!-- <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification">-->
        </td>
        <td colspan="5">
          <div class="autocomplete">
          <input id="standard_supplier" class="standard_supplier" type="text" name="standard_supplier[]" value="<?php echo $mold_standard_value[2] ?>" placeholder="输入品牌" style="width:200px">
           </div> 
      
        </td>
        <td>
            <input type="text" name="standard_number[]" class="standard_number" value=<?php echo $mold_standard_value[3] ?>>  
        </td>
        <td>
               <input type="text" name="standard_unit_price[]" class="standard_unit_price" value=<?php echo $mold_standard_value[4] ?>>
        </td>
        <td>
           <input type="text" name="standard_price[]" class="standard_price" value="<?php echo $mold_standard_value[5] ?>"> 
        </td>
         <?php 
                
                 if($i == 0){
                  echo '<td rowspan="8" id="total_standard">
      <input type="text" class="min_total"  name="total_standard" value='.$array['total_standard'].'>
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
            foreach($arrs_designs as $mould_design_key => $mould_design_value){ 
            ?>
           <tr class="design_trs">
              <td colspan="4">
                  <input name="mold_design_name[]" class="mold_design_name" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_design_value[0] ?>">
              
              </td>
              <td colspan="2">
                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" value=<?php echo $mould_design_value[1] ?>>
              </td>
              <td colspan="6">
                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value=<?php echo $mould_design_value[2] ?>>
              </td>
              <td colspan="2">
                <input type="text" name="design_price[]" class="design_price" id="design_price" value=<?php echo $mould_design_value[3] ?>>
              </td>
                  <?php 
                
                 if($i == 0){
                  echo '<td rowspan="5" id="total_designs">
                    <input type="text" class="min_total" name="total_designs" value='.$array['total_designs'].'>
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
          foreach($arrs_manufacturings as $mould_manufacturing_key=>$mould_manufacturing_value){
          ?>
           <tr class="manus_trs">
              <td colspan="4">
                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_manufacturing_value[0] ?>">
              
              </td>
              <td colspan="2">
                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" class="manufacturing_hour" value=<?php echo $mould_manufacturing_value[1] ?>>
              </td>
              <td colspan="6">
                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" class="manufacturing_unit_price" value=<?php echo $mould_manufacturing_value[2] ?>>
              </td>
              <td colspan="2">
               <input type="text" name="manufacturing_price[]" class="manufacturing_price" id="manufacuring_price" value=<?php echo $mould_manufacturing_value[3] ?>> 
              </td>
                      <?php 
                
                 if($i == 0){
                  echo '<td rowspan="11" id="total_manufacturing">
                         <input type="text" class="min_total" name="total_manufacturing" value='.$array['total_manufacturing'].'>
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
          <?php $i = 0;foreach($arrs_others as $other_key=>$others_value){ ?>
           <tr class="others_trs">
                <td colspan="4">
                  <input type="text" name="other_fee_name[]" value=<?php echo  $others_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" /> 
                </td>
               <td colspan="8">
                
    <input type="text" name="other_fee_instr[]" value="<?php echo $others_value[1] ?>" readonly style="width:500px">
     </td>
               <td colspan="2">
                   <input type="text" name="other_fee_price[]" class="other_fee fixed_fee" id="freight_fee" value=<?php echo $others_value[2] ?>>
               </td>
                <?php 

                  if($i==0){ 
                    echo '<td  rowspan="6" id="total_others">
        <input type="text" name="total_others" value='.$array["total_others"].' id="tot_others" />
                    </td>';
              }
               $i++;
               ?>
          </tr>
          <?php } ?>
           <tr class="others_trs" id="others_fees">
                <td colspan="4">
    <input type="text" name="" value="管理费/Management Fee" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="5%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="management_fee" class="other_fee" id="management_fee" value=<?php echo $array['management_fee'] ?>>
               </td>
     
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="利润/Profit" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="10%" placeholder="">
               </td>
               <td colspan="2">
                <input type="text" name="profit" class="other_fee" id="profit" value=<?php echo $array['profit'] ?>>
               </td>
          
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="税/VAT TAX(13%)" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="13%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="vat_tax" class="other_fee" id="vat_tax" value=<?php echo $array['vat_tax'] ?>
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
                   <input type="text" name="mold_price_rmb" id="mold_price_rmb" value=<?php echo $array['mold_price_rmb'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(USD)/Mold Price(USD) Rate=6.5</td>
                <td colspan="11">
                   <input type="text" name="mold_price_usd" id="mold_price_usd" value=<?php echo $array['mold_price_usd'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(元)含13%增值税/Mold with VAT(RMB)</td>
                <td colspan="11">
                  <input type="text" name="mold_with_vat" id="mold_with_vat" value=<?php echo $array['mold_with_vat'] ?>>
                </td>
          </tr>
          <tr height="20"></tr>
          <tr>

              <td style="border-style:none" colspan="16" align="center">
                <input type="hidden" name="id" value="<?php echo $mould_dataid ?>" />
                <span id="unapproval_edit" style="width:80px;height:26px; display: inline-block;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:26px;">保存</span>
                &nbsp;&nbsp;
              <input type="submit" <?php echo $system_info[0] !=1?'style:"background:grey" disabled':' ' ?> name="button" id="button" value="审批" class="button" />
              &nbsp;&nbsp;
              <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />

           </tr>
   </table>
  </form>
  <?php
      
    }
    }elseif($action == 'edit'){
    $mould_dataid = fun_check_int($_GET['id']);
    //查询模具报价的信息
    $sql = "SELECT * FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
    $result = $db->query($sql);
    if($result->num_rows){
      $array = $result->fetch_assoc();
     
      $sql_cfm = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' AND `quote_status` = 1";
      $result_cfm = $db->query($sql_cfm);
     // if(!$result_cfm->num_rows){
    
     //加工材料费的数据处理
     $old_material = [$array['mould_material'],$array['material_specification'],$array['materials_number'],$array['material_length'],$array['material_width'],$array['material_height'],$array['material_weight'],$array['material_unit_price'],$array['material_price']];
     $arrs_materials = getdata($old_material);
     //热处理费用的数据处理
     $old_heat = [$array['mould_heat_name'],$array['heat_weight'],$array['heat_unit_price'],$array['heat_price']];
     $arrs_heats = getdata($old_heat);
     //配件费的数据处理
     $old_standard = [$array['mold_standard'],$array['standard_specification'],$array['standard_supplier'],$array['standard_number'],$array['standard_unit_price'],$array['standard_price']];
     $arrs_standards = getdata($old_standard);
     //设计费的数据处理
     $old_design = [$array['mold_design_name'],$array['design_hour'],$array['design_unit_price'],$array['design_price']];
     $arrs_designs = getdata($old_design);
     //加工费的数据处理
     $old_manufacturing = [$array['mold_manufacturing'],$array['manufacturing_hour'],$array['manufacturing_unit_price'],$array['manufacturing_price']];
     $arrs_manufacturings = getdata($old_manufacturing);
    //其它费用的数据处理
    $old_others = [$array['other_fee_name'],$array['other_fee_instr'],$array['other_fee_price']];
    $arrs_others = getdata($old_others);
    //型腔数量
    $cavity_types = turn_arr($array['cavity_type']);
    //获取产品大小数据
    $p_lengths = turn_arr($array['p_length']);
    $p_widths  = turn_arr($array['p_width']);
    $p_heights  = turn_arr($array['p_height']);
    $p_weights = turn_arr($array['p_weight']);
    $p_materials = turn_arr($array['m_material']);
    $part_numbers = turn_arr($array['part_number']);
    $drawing_files = turn_arr($array['drawing_file']);
    //型腔排位数据
    $old_cavity = [$array['cavity_length'],$array['cavity_width']];
    $cavity_data = getdata($old_cavity);
    $old_cavity_style = [$array['cavity_style_length'],$array['cavity_style_width']];
    $cavity_style_data = getdata($old_cavity_style);

  ?>
  <h4>模具数据修改</h4> 
  <form action="mould_datado.php" name="mould_data" method="post" enctype="multipart/form-data">
  <style type="text/css" media="screen">
    #main_table tr td{border:1px solid grey;}
    input{width:80px;}
    *{margin:0px;}
    .dels{display:inline;float:right;width:45px;height:3px;line-height:5px;border:1px solid grey;background:rgb(221,221,221);cursor:pointer}
    .adder{width:100px;height:30px;line-height:30px;text-align:center;display:block;border:1px solid grey;background-color:rgb(221,221,221);margin:2px auto;cursor:pointer;font-size:15px;}
    .autocomplete-items{position:absolute;z-index:22;background-color:grey;width:84px;}
  </style>
  <script type="text/javascript" charset="utf-8">
    $(function(){
      //判断型腔数如果大于1,增加删除按钮
      var cavity_num = $(".cavity_type").size();
      if(cavity_num >1){
                          $("#add_cavitys").after(del_cavity);
      } 
      //把型腔数量添加到布局
      

      //加入布局选项框
      var cavity_type_num = $(".cavity_type").size();
      for(var m=0;m<cavity_type_num;m++){
        var cavity_val = $(".cavity_type").eq(m).val();
        for(var h=0;h<cavity_val;h++){
      
      var h1 = h+1;
      
      var h2 = cavity_val/h1 + "";
      
      if(h2.indexOf('.') == -1){
      var cavity_style_layout = '<option value='+h1+'>'+h1+'</option>';

      if($(".cav_style_len").eq(m).html() != h1){
      $(".cavity_style_length").eq(m).append(cavity_style_layout);
      }
      }}
    
      
      var type_val = $(".cavity_type").eq(m).val();
      var p_length = $('.p_length').eq(m).val();
      var p_width = $('.p_width').eq(m).val();
      
      //获取当前布局的值
      var cavity_style_length = $(".cavity_style_length").eq(m).val();
      //计算型腔布局的宽并加入到布局选项中
      var cavity_style_width = Math.ceil(type_val/cavity_style_length);
      //通过获取的值计算型腔的长和宽
      var opt_length = (parseInt(p_length) + 110)*cavity_style_length;
      var opt_width  = (parseInt(p_width) + 110)*cavity_style_width;

    
      //把选项添加到型腔布局选项框中
      var  cavity_style_length  = '<option value='+opt_length+'>'+opt_length+'</option>';
      var  cavity_style_width  = '<option value='+opt_width+'>'+opt_width+'</option>';  
      var  cavity_zero = '<option value="0">0</option>';  
      //加入到排位长的选项中
      
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_length").eq(m).append(cavity_zero);
      $(".cavity_length").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_length").eq(m).append(cavity_zero);
         $(".cavity_length").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_length").eq(m).append(cavity_style_width);
        $(".cavity_length").eq(m).append(cavity_style_length);
      }
      //加入到排位宽的选项中
      if($(".cav_len").eq(m).html() == opt_length){
      $(".cavity_width").eq(m).append(cavity_zero);
      $(".cavity_width").eq(m).append(cavity_style_width);
      } else if($(".cav_len").eq(m).html() == opt_width){
         $(".cavity_width").eq(m).append(cavity_zero);
         $(".cavity_width").eq(m).append(cavity_style_length);
      } else{
        $(".cavity_width").eq(m).append(cavity_style_width);
        $(".cavity_width").eq(m).append(cavity_style_length);
      }

      
    
      
    
              }
    })
  </script>
   <table id="main_table" style="word-wrap: break-word; word-break: break-all;">
    <input type="hidden" name="employeeid" value="<?php echo $employeeid ?>" />
    <!--<input type="hidden" name="mold_id" value="<?php echo $array['mold_id'] ?>" >-->
    <input type="hidden" name="id" value="<?php echo $mould_dataid ?>">
    <input type="hidden" name="upload_final_path" value="<?php echo $array['upload_final_path'] ?>">
    <!--基本信息-->
    <tr>
         <td colspan="5" rowspan="5" style="text-align:center">
          <img src="../jtl.png" width="150" height="80" alt="logo">
         </td>
         <td colspan="9" rowspan="5" style="width:661px" >
           <p style="font-weight:blod;font-size:30px">嘉泰隆  模具费用分解表</p>
                    <p style="font-weight:blod;font-size:30px">JOTYLONG  Tooling Cost Break Down</p>
         </td>
       <td style="width:186px;padding-right:20px">客户名称/Customer</td>
           <td style="width:186px;padding-right:0px">
               <input type="text" name="client_name" value="<?php echo $array['client_name'] ?>" style="width:125px;" />
           </td>  
    </tr>
    <tr>
      <td>项目名称/Program</td>
             <td>
             <input type="text" name="project_name" value="<?php echo $array['project_name'] ?>" style="width:125px"/>
          </td>
    </tr>
    <tr>
          <td>联系人/Attention</td>
          <td>
            <input type="text" name="contacts" value="<?php echo $array['contacts']; ?>" style="width:125px">
          </td>
           </tr>
           <tr>
          <td>电话/TEL</td>
          <td>
            <input  type="text" name="tel" value="<?php echo $array['tel']; ?>" style="width:125px"/>
          </td>    
           </tr>
           <tr>
              <td>信箱/E-mail</td>
              <td>
                 <input type="text" name="email" value="<?php echo $array['email']; ?>" style="width:125px"/>
             </td>  
           </tr>
           <tr>
               <td colspan="5" >模具名称/Mold Specification</td>
               <td colspan="2">型腔数量/Cav. Number</td>
               <td colspan="5" rowspan="6">
                  <?php $image_filepath = $array['upload_final_path'];
      if(stristr($image_filepath,'$') == true){
        $image_filepath = substr($image_filepath,0,strripos($image_filepath,"$"));
      }
        
        $image_file = "<img width=\"185\" height=\"100\" src=\"".$image_filepath."\" /><br>";
        echo $image_file;
       ?>
                <input type="file" name="file[]" id="file" class="input_file" multiple style="width:200px"/><br/>
           <span class="tag"> *支持JPG，GIF，PNG图片格式</span>
               </td>
               <td colspan="2">首次试模时间/T1 Time</td>
                 <td colspan="2">最终交付时间/Lead Timeme</td>
           </tr>
           <tr>
               <td colspan="5" style="padding-right:2px">
                <input  type="text" name="mould_name" id="mould_name" value="<?php echo $array['mould_name'] ?>" class="input_tx"  style="width:315px;margin-right:2px"/>
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
      <option value="1" <?php echo ($array['k_num'] == 1)?'selected':' ' ?>>1k</option>
      <option value="2" <?php echo ($array['k_num'] == 2)?'selected':' ' ?>>2k</option>
                      </select>
                      <?php foreach($cavity_types as $k=>$v){ ?>
                         <input type="text" name="cavity_type[]" class="cavity_type" id="types_cav" value="<?php echo $v ?>" style="width:25px">
                      <?php } ?>
                     <button id="add_cavitys" type="button">添加</button>

               </td>
               <td colspan="2" style="padding_style:8px">
                <input type="text" name="t_time" class="input_tx" value="<?php echo $array['t_time'] ?>" style="width:182px"/>
              </td>
              <td colspan="2">
                <input type="text" name="lead_time" class="input_tx" value="<?php echo $array['lead_time'] ?>"  style="width:310px" />
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
                 <?php foreach($p_lengths as $k=>$v){   ?>
                <input type="text" name="p_length[]" id="p_length" value="<?php echo $v ?>" class="p_length"/>
                 <?php } ?>     
                <span id="pp_length"></span>
              </td>
              <td >*</td>
              <td style="width:93px" id="width_no">
              <?php foreach($p_widths as $k=>$v){ ?>
                <input type="text" name="p_width[]" id="p_width" value="<?php echo $v ?>" class="p_width" />
              <?php } ?>
                <span id="pp_width"></span>
              </td>
              <td>*</td>
              <td style="width:93px" id="height_no">
                <?php foreach($p_heights as $k=>$v) { ?>
                      <input type="text" name="p_height[]" id="p_height" value="<?php echo $v ?>" class="p_height" />
                 <?php } ?>
                     <span id="pp_height"></span>
              </td>
              <td style="width:93px" id="weight_no">
                 <?php foreach($p_weights as $k=>$v){ ?>
                    <input type="text" name="p_weight[]" id="p_weight" value="<?php echo $v ?>" class="p_weight"/>
                 <?php } ?>   
                    <span id="pp_weight"></span>
              </td>
              <td style="width:93px" id="material_no">
                  <?php foreach($p_materials as $k=>$v){ ?>
                <input type="text" name="m_material[]" id="m_material" value="<?php echo $v ?>" class="m_material"/>
                  <?php } ?>  
                <span id="pp_material"></span>
              </td>
              <td colspan="2" style="padding-right:8px" id="part_no">
                <?php foreach($part_numbers as $k=>$v){ ?>
                   <input type="text" name="part_number[]" id="part_number" class="part_number" value="<?php echo $v ?>"  style="width:182px"/>
                <?php } ?>   
                   <span id="pp_number"></span>
               </td>
               <td colspan="2" id="file_no">
                  <?php foreach($drawing_files as $k=>$v){ ?>
                   <input type="text" name="drawing_file[]" id="drawing_file" class="drawing_file" value="<?php echo $v ?>" style="width:310px"/>
                 <?php } ?>
                   <span id="pp_file"></span>
              </td>
           </tr>
           <tr>
               <td colspan="5">模具尺寸/Mold Size (mm)</td>
               <td colspan="2">模具重量/Mold Weight(Kg)</td>
               <td colspan="2">模具寿命/Longevity</td>
               <td colspan="2">设备吨位/Press(Ton)</td>
           </tr>
           <tr id="adder_style">
              <td>
                <input type="text" name="m_length" id="m_length" value="<?php echo $array['m_length'] ?>" readonly  />
              </td>
              <td>*</td>
              <td>
                 <input type="text" name="m_width" id="m_width" value="<?php echo $array['m_width'] ?>" readonly />
              </td>
              <td>*</td>
              <td>
                    <input type="text" name="m_height" id="m_height" value="<?php echo $array['m_height'] ?>"  readonly /></td>
              </td>
              <td colspan="2">
                   <input type="text" name="m_weight" id="m_weight" value="<?php echo $array['m_weight'] ?>" readonly  style="width:176px" />
              </td>
              <td colspan="2" style="padding-right:8px">
                <input type="text" name="lift_time" id="lift_time" value="<?php echo $array['lift_time'] ?>" style="width:182px;"/>
              </td>
              <td colspan="2">
                <input type="text" name="tonnage" value="<?php echo $array['tonnage'] ?>" style="width:310px"/>
              </td>
           </tr>
           <!--选择排位-->
      <tr id="select_style" style="height:100px">
        <td colspan="16">
        <div style="display:inline">
          <span style="display:inline-block">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          合计:
          <br />
          型腔长:
            <input type="text" id="style_length_sum" value="<?php echo $array['style_length_sum'] ?>">
          <br />
        
          型腔宽:
            <input type="text" id="style_width_sum" value="<?php echo $array['style_width_sum'] ?>">
          </span>
          <?php foreach($cavity_data as $k=>$v){ ?>
          <span style="display:inline-block">           
            型腔<?php echo $k+1 ?>
            <br />
          
            <select class="cavity_length" name="cavity_length[]">
              <option value="" class="first_length">cavity长</option>
              <option class="cav_len" value="<?php echo $v[0] ?>" selected class="first_length"><?php echo $v[0] ?></option>
            </select>
            
            <br />
          
            
            <select class="cavity_width" name="cavity_width[]">
              <option value="" class="first_width">cavity宽</option>
              <option class="cav_wid" value="<?php echo $v[1] ?>" selected class="first_width"><?php echo $v[1] ?></option>

            </select>
          </span>
          <?php } ?>
          <span id="cavity_widths"></span>
        </div>
        <span  style="width:100px;display:inline-block"></span>
        <?php foreach($cavity_style_data as $k=>$v){ ?>
        <div style="display:inline-block;background:#eee;">
          <span style="padding-left:20px">型腔<?php echo $k+1 ?></span><br />
          <span>
            <select  class="cavity_style_length" name="cavity_style_length[]" style="width:80px">
              <option value="" class="first_style_length">长度方向</option>
              <option class="cav_style_len" value="<?php echo $v[0] ?>" selected  ><?php echo $v[0] ?></option>
            </select>
          </span><br />
          <span>
            <select  class="cavity_style_width" name="cavity_style_width[]" style="width:80px">
              <option value="" class="first_style_width">宽度方向</option>
              <option value="<?php echo $v[1] ?>" selected ><?php echo $v[1] ?><option>
            </select>
          </span>

        </div>
        <?php } ?>
        <span id="cavity_width_styles"></span>
    
        </td>
      </tr>
           <!--加工材料费-->
           <tr id="material_last_tr">
               <td id="material_first_td" rowspan="9">材料加工费/Machining Materia</td>  
               <td colspan="4">材料名称/Material</td>
               <td>材料牌号/Specification</td>
               <td>数量/Number</td>
               <td colspan="5">尺寸/Size(mm*mm*mm)</td>
               <td style="width:93px">总重量/Weight(kg)</td>
               <td style="width:93px">单价(元)/Unit Price</td>
               <td>金额/Price(RMB)</td>
               <td>小计(元)</td>
           </tr>
  <tr class="material_trs even">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="模架/Mould Base" base="" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px;color:red">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $arrs_materials[0][1] ?>">
           </div> 
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $arrs_materials[0][2]?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" id="base_length" class="material_length" type="text" placeholder="长" value=<?php echo $arrs_materials[0][3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" id="base_width" class="material_width" type="text" placeholder="宽" value=<?php echo $arrs_materials[0][4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" id="base_height" class="material_height" type="text" placeholder="高" value=<?php echo $arrs_materials[0][5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="base_weight" class="material_weight" value=<?php echo $arrs_materials[0][6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $arrs_materials[0][7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" id="material_price" class="material_price" value=<?php echo $arrs_materials[0][8] ?>>   
             </td>
               <td rowspan="8" id="total_machining"><input type="text" class="min_total" value="<?php echo $array['total_machining'] ?>" name="total_machining"></td>   
           </tr>
              <?php
              $i = 0;
              unset($arrs_materials[0]);
               foreach($arrs_materials as $mould_material_key=>$mould_material_value){
                

        ?>
           <tr class="material_trs">
               <td colspan="4">
                  <input name="mould_material[]" class="mould_material" value="<?php echo $mould_material_value[0] ?>" readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px">
               </td>
               <td>
                <div class="autocomplete">
          <input id="material_specification" class="material_specification" type="text" name="material_specification[]" placeholder="输入材料" value="<?php echo $mould_material_value[1] ?>">
           </div>  
             </td>
             <td>
                   <input type="text" name="materials_number[]" class="materials_number" id="materials_number" value=<?php echo $mould_material_value[2] ?>>
             </td>
             <td style="width:93px">
                   <input name="material_length[]" class="material_length" id="material_length" type="text" placeholder="长" value=<?php echo $mould_material_value[3] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                 <input name="material_width[]" class="material_width" id="material_width" type="text" placeholder="宽" value=<?php echo $mould_material_value[4] ?>>
             </td>
             <td>*</td>
             <td style="width:93px">
                  <input name="material_height[]" class="material_height" id="material_height" type="text" placeholder="高" value=<?php echo $mould_material_value[5] ?>>
             </td>
             <td>
                 <input type="text" name="material_weight[]" id="material_weight" class="material_weight" value=<?php echo $mould_material_value[6] ?>>
             </td>
             <td>
                 <input type="text" name="material_unit_price[]" id="material_unit_price" class="material_unit_price" value=<?php echo $mould_material_value[7] ?>>
             </td>
             <td>
                 <input type="text" name="material_price[]" class="material_price" id="material_price" value=<?php echo $mould_material_value[8] ?>>  
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
        foreach($arrs_heats as $mould_heat_key=>$mould_heat_value){
           ?>
           <tr class="heat_trs">
              <td colspan="4">
                  <input name="mould_heat_name[]" id="mould_heat_name" class="mould_heat_name" value=<?php echo $mould_heat_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" class="fix_txt">
              </td>
              <td colspan="2">
                  <input name="heat_weight[]" type="text" class="heat_weight" value=<?php echo $mould_heat_value[1] ?>>
              </td>
              <td colspan="6">
                 <input name="heat_unit_price[]" type="text" class="heat_unit_price" value=<?php echo $mould_heat_value[2] ?>>  
              </td>
              <td colspan="2">
                <input name="heat_price[]" type="text" id="heat_price" class="heat_price" value="<?php echo $mould_heat_value[3] ?>" style="border-style:none">
              </td>
                <?php 
                
                 if($i == 0){
                  echo '<td rowspan="4" id="total_heats">
                    <input type="text" name="total_heat"  class="min_total" value="'.$array["total_heat"].'"/>
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
        foreach($arrs_standards as $mold_standard_key=>$mold_standard_value){
      ?>
      <tr class="parts_trs"> 
        <td colspan="4">
             <input name="mold_standard[]" id="mold_standard" class="mold_standard" style="border-style:none;color:black;font-weight:150;font-size:13px;width:193px" readonly value="<?php echo $mold_standard_value[0] ?>">
        </td>
          <td colspan="2">
          <div class="autocomplete">
          <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification" placeholder="输入规格" style="width:183px" value="<?php echo $mold_standard_value[1] ?>">
           </div> 
           <!-- <input type="text" name="standard_specification[]" class="standard_specification" id ="standard_specification">-->
        </td>
        <td colspan="5">
          <div class="autocomplete">
          <input id="standard_supplier" class="standard_supplier" type="text" name="standard_supplier[]" value="<?php echo $mold_standard_value[2] ?>" placeholder="输入品牌" style="width:200px">
           </div> 
      
        </td>
        <td>
            <input type="text" name="standard_number[]" class="standard_number" value=<?php echo $mold_standard_value[3] ?>>  
        </td>
        <td>
               <input type="text" name="standard_unit_price[]" class="standard_unit_price" value=<?php echo $mold_standard_value[4] ?>>
        </td>
        <td>
           <input type="text" name="standard_price[]" class="standard_price" value="<?php echo $mold_standard_value[5] ?>"> 
        </td>
         <?php 
                
                 if($i == 0){
                  echo '<td rowspan="8" id="total_standard">
      <input type="text" class="min_total"  name="total_standard" value='.$array['total_standard'].'>
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
            foreach($arrs_designs as $mould_design_key => $mould_design_value){ 
            ?>
           <tr class="design_trs">
              <td colspan="4">
                  <input name="mold_design_name[]" class="mold_design_name" id="mold_design_name" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_design_value[0] ?>">
              
              </td>
              <td colspan="2">
                 <input type="text" name="design_hour[]" id="design_hour" class="design_hour" value=<?php echo $mould_design_value[1] ?>>
              </td>
              <td colspan="6">
                 <input type="text" name="design_unit_price[]" id="design_unit_price" class="design_unit_price" value=<?php echo $mould_design_value[2] ?>>
              </td>
              <td colspan="2">
                <input type="text" name="design_price[]" class="design_price" id="design_price" value=<?php echo $mould_design_value[3] ?>>
              </td>
                  <?php 
                
                 if($i == 0){
                  echo '<td rowspan="5" id="total_designs">
                    <input type="text" class="min_total" name="total_designs" value='.$array['total_designs'].'>
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
          foreach($arrs_manufacturings as $mould_manufacturing_key=>$mould_manufacturing_value){
          ?>
           <tr class="manus_trs">
              <td colspan="4">
                  <input name="mold_manufacturing[]" id="mold_manufacturing" class="mold_manufacturing" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly value="<?php echo $mould_manufacturing_value[0] ?>">
              
              </td>
              <td colspan="2">
                  <input type="text" name="manufacturing_hour[]" id="manufacturing_hour" class="manufacturing_hour" value=<?php echo $mould_manufacturing_value[1] ?>>
              </td>
              <td colspan="6">
                  <input type="text" name="manufacturing_unit_price[]" id="manufacturing_unit_price" class="manufacturing_unit_price" value=<?php echo $mould_manufacturing_value[2] ?>>
              </td>
              <td colspan="2">
               <input type="text" name="manufacturing_price[]" class="manufacturing_price" id="manufacuring_price" value=<?php echo $mould_manufacturing_value[3] ?>> 
              </td>
                      <?php 
                
                 if($i == 0){
                  echo '<td rowspan="11" id="total_manufacturing">
                         <input type="text" class="min_total" name="total_manufacturing" value='.$array['total_manufacturing'].'>
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
          <?php $i = 0;foreach($arrs_others as $other_key=>$others_value){ ?>
           <tr class="others_trs">
                <td colspan="4">
                  <input type="text" name="other_fee_name[]" value=<?php echo  $others_value[0] ?> readonly style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" /> 
                </td>
               <td colspan="8">
                
    <input type="text" name="other_fee_instr[]" value="<?php echo $others_value[1] ?>" readonly style="width:500px">
     </td>
               <td colspan="2">
                   <input type="text" name="other_fee_price[]" class="other_fee fixed_fee" id="freight_fee" value=<?php echo $others_value[2] ?>>
               </td>
                <?php 

                  if($i==0){ 
                    echo '<td  rowspan="6" id="total_others">
        <input type="text" name="total_others" value='.$array["total_others"].' id="tot_others" />
                    </td>';
              }
               $i++;
               ?>
          </tr>
          <?php } ?>
           <tr class="others_trs" id="others_fees">
                <td colspan="4">
    <input type="text" name="" value="管理费/Management Fee" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="5%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="management_fee" class="other_fee" id="management_fee" value=<?php echo $array['management_fee'] ?>>
               </td>
     
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="利润/Profit" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="10%" placeholder="">
               </td>
               <td colspan="2">
                <input type="text" name="profit" class="other_fee" id="profit" value=<?php echo $array['profit'] ?>>
               </td>
          
          </tr>
           <tr class="others_trs">
                <td colspan="4">
    <input type="text" name="" value="税/VAT TAX(13%)" style="border-style:none;color:black;font-weight:150;font-size:13px;width:163px" readonly /> 
                </td>
               <td colspan="8">
    <input type="text" name="" readonly value="13%" placeholder="">
               </td>
               <td colspan="2">
                    <input type="text" name="vat_tax" class="other_fee" id="vat_tax" value=<?php echo $array['vat_tax'] ?>
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
                   <input type="text" name="mold_price_rmb" id="mold_price_rmb" value=<?php echo $array['mold_price_rmb'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(USD)/Mold Price(USD) Rate=6.5</td>
                <td colspan="11">
                   <input type="text" name="mold_price_usd" id="mold_price_usd" value=<?php echo $array['mold_price_usd'] ?>>
                </td>
            </tr>
            <tr>
                <td colspan="5">模具价格(元)含13%增值税/Mold with VAT(RMB)</td>
                <td colspan="11">
                  <input type="text" name="mold_with_vat" id="mold_with_vat" value=<?php echo $array['mold_with_vat'] ?>>
                </td>
          </tr>
          <tr height="20"></tr>
          <tr>
              <td style="border-style:none" colspan="16" align="center"><input type="submit" name="submit" id="submit" value="修改" class="button" />
              &nbsp;&nbsp;
              <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
              <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
           </tr>
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