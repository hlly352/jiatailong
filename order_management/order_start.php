<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once '../config/config.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//获取模具id
  $id = $_GET['mould_id'];
  $mould_type = $_GET['mould_type'];
//查询对应模具的数据
$sql = "SELECT * FROM `db_mould_data` INNER JOIN `db_customer_info` ON `db_mould_data`.`client_name` = `db_customer_info`.`customer_id` WHERE `db_mould_data`.`mould_dataid` = ".$id;
$result = $db->query($sql);
  $mould_info = [];
  if($result->num_rows){
      $info = $result->fetch_assoc();
  }
//查找负责人员
$depart_name = array('saler'=>'市场部','projecter'=>'项目部','designer'=>'设计部','programming'=>'CNC','assembler'=>'钳工');
foreach($depart_name as $k=>$v){
    $sql_employee = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` LEFT JOIN `db_department` as saler ON `db_employee`.deptid = saler.`deptid` WHERE saler.`dept_name` LIKE '%$v%'";
    $res = $db->query($sql_employee);
   
    ${$k} = array();
    
    if($res->num_rows){
      while($row = $res->fetch_row()){
        ${$k}[] = $row;
      }
      
    }
}

  echo '<meta charset="utf-8">';
  //获取图片
   $image_filepath = $info['upload_final_path'];
      if(is_file($image_filepath)){
        $image_file = "<img style=\"display:block;margin:10px auto\" src=\"".$image_filepath."\" width=\"300\" height=\"150\"/>";
      }else{
        $image_file = "<img style=\"display:block;margin:10px auto\" src=\"../images/no_image_85_45.png\" width=\"300\" height=\"150\" />";
      }
 //获取型腔数
 $cavity_num = $info['cavity_type'];
 if($cavity_num){
   if(strpos($cavity_num,'$$')){
      $cavity = str_replace('$$','+',$cavity_num);
   }else{
      $cavity = '1*'.$cavity_num;
   }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="../js/jquery-ui.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>


<title>订单管理-嘉泰隆</title>
<style type="text/css">
      table{width:100%;border-collapse: collapse;border-spacing: 2px;}
      .distance{height:10px;}
      table tr td{border:1px solid grey;font-size:11px;display:table-cell;}
      .submit{width:80px;height:25px;}
      select{width:75%;height:22px;}
      table tr td input[type='text']{width:75%;height:22px;}
      #table_list table tr .noborder{border:0;}
      #table_list table tr td .tips{width:15%;background:white;display:inline-block;height:10px;margin-top:-3px;}
      #table_list table tr td:nth-of-type(odd):not(.title){background:#ddd;}
      #table_list table tr .title{font-family:'黑体', serif;font-size:15px;color:blue;text-align:left;padding-left:10px;font-weight:700;}
      #back{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;margin-left:10px;}
      #save{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;margin-top:5px;height:29px;width:80px}
</style>
<script type="text/javascript" charset="utf-8">
//上传产品图片预览
function view_data(file){
    var filepath = $(file).val();  
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    $(file).prev().empty()
    if (file.files && file.files[0]){ 

    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).prev().html('<img src="' + evt.target.result + '" width="300px" height="150px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).prev().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = ' <input type="file" name="file[]" onchange="view(this)"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);
  } 


 //上传图片之前预览图片
function view(file){
    var filepath = $(file).val();  
    var extStart = filepath.lastIndexOf(".")+1;
    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
    var allowtype = ["JPG","GIF","PNG"];
    if($.inArray(ext,allowtype) == -1)
    {
      alert("请选择正确文件类型");
      $(file).val('');
      return false;
    }
    if($(file).prevAll().size()<19){
    $(file).css('display','none');
    if (file.files && file.files[0]){ 

    var reader = new FileReader(); 

    reader.onload = function(evt){ 

    $(file).next().html('<img src="' + evt.target.result + '" width="95px" height="50px" />'); 

    } 

    reader.readAsDataURL(file.files[0]); 

    }else{

    $(file).next().html('<p style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></p>'); 

    } 
    var files = ' <input type="file" name="file[]" onchange="view(this)"/><span style="margin-left:20px"></span>';
    $(file).next().after(files);
  } else {
    alert('最多上传十张图片');
    $(file).remove();
  }
 }
  $(function(){

    //唧嘴选中sr时变为输入框
    $('#ji_sr').bind('change',function(){
      var ji_sr = $(this).val();
      var inp = '<input type="text" name="ji_sr" value="sr">';
      if(ji_sr == '2'){
        var par = $(this).parent();
        $(this).remove();
        par.append(inp);
      }
    })

    })
</script>
</head>

<body>
<?php include "header.php"; ?>

<?php 
  //判断显示哪一个页面
  if($mould_type == 'task'){


?>


  <h4 style="padding-left:10px">
  </h4>
  <div id="table_list">
  <form action="order_start_do.php?action=add" name="" method="post">
    <table>
        <tr>
          <td colspan="6" style="text-align:right;padding-right:20px">客户合同编号</td>
          <td>
              <input type="hidden" name="mould_id" value="<?php echo $_GET['mould_id']?>">
              <input type="text" name="customer_order_no" value="<?php echo $info['customer_order_no'] ?>" >
          </td>
        </tr>
         <tr>
          <td rowspan="4">基本信息</td>
          <td>客户代码</td>
          <td>
              <input type="text" name="customer_code" value="<?php echo $info['customer_code'] ?>" >
          </td>
          <td>项目名称</td>
          <td>
              <input type="text" name="project_name" value="<?php echo $info['project_name'] ?>">
          </td>
          <td>产品名称</td>
          <td>
            <input type="text" name="mould_name" value="<?php echo $info['mould_name'] ?>">
          </td>
        </tr>
         <tr>
          <td>材质/其它</td>
          <td>
            <input type="text" name="material_other" >
          </td>
          <td>图纸类型</td>
          <td>
            <select name="drawing_type">
                  <?php
                     echo '<option value="">--请选择--</option>';
                      foreach($array_drawing_type as $k=>$v){

                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                   ?>
            </select>
          </td>
          <td>重点要求</td>
          <td>
          <select name="require">
                  <?php
                     echo '<option value="">--请选择--</option>';
                      foreach($array_require as $k=>$v){

                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                   ?>
          </select>
          </td>
        </tr>
         <tr>
          <td>模具编号<br>任务编号</td>
          <td>
            <input type="text" name="mould_no" value="<?php echo $info['mould_no'] ?>" >
          </td>
          <td>任务内容</td>
          <td colspan="3">
            <input type="text" name="task_content" style="width:91%;text-align:left">
          </td>
        </tr>
         <tr>
          <td>启动时间</td>
          <td>
            <input type="text" name="start_time" >
          </td>
          <td>完成时间</td>
          <td>
            <input type="text" name="finish_time">
          </td>
          <td>验收时间</td>
          <td>
            <input type="text" name="check_time">
          </td>
        </tr>
        <tr class="distance"></tr>
        <tr>
          <td rowspan="2">责任人与审核</td>
          <td>销售经理</td>
          <td>
              <input type="text" name="sales_manager" value="">
          </td>
          <td>项目经理</td>
          <td>
            <input type="text" name="project_manager">
          </td>
          <td>责任人</td>
          <td>
            <input type="text" name="leading">
          </td>
        </tr>
         <tr>
          <td>填表（销售及项目经理）：</td>
          <td>
            <input type="text" name="writer">
          </td>
          <td>审核（责任部门经理）：</td>
          <td>
            <input type="text" name="assessor">
          </td>
          <td>批准（副总经理）：</td>
          <td>
            <input type="text" name="approver">
          </td>
        </tr>
        <tr class="distance"></tr>
        <tr>
        <td colspan="7">
          <input type="submit" class="submit" value="发起项目">
        </td>
      </tr>
    </table>
  </form>
  </div>
  <?php } 
    elseif($mould_type == 'normal'){
  ?>
  <h4 style="padding-left:10px">
  </h4>
  <div id='table_list'>
  <form action="order_start_do.php?action=add" name="list" method="post" enctype="multipart/form-data">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
          <td class="noborder title">基本信息</td>
          <td colspan="3" class="noborder"></td>
          <td class="noborder">客户合同编号</td>
          <td class="noborder">
              <input type="hidden" name="mould_id" value="<?php echo $_GET['mould_id'] ?>">
              <input type="text" name="customer_order_no" value="<?php echo $info['customer_order_no'] ?>" >
          </td>
        </tr>
      <tr>
       <td>客户代码</td>
       <td>
         <input type="text" name="customer_code" value="<?php echo $info['customer_code'] ?>">
       </td>
       <td>项目名称</td>
       <td>
         <input type="text" name="project_name" value="<?php echo $info['project_name'] ?>">
       </td>
       <td colspan="2" rowspan="6" style="background:white">
         <span>
          <?php echo $image_file ?>  
         </span>
         <input type="file" name="image" onchange="view_data(this)" style="display:block;margin:5px auto">
       </td>
      </tr>
      <tr>
        <td>模具编号</td>
       <td>
         <input type="text" name="mould_no" value="<?php echo $info['mould_no'] ?>">
       </td>
        <td>产品名称</td>
       <td>
         <input type="text" name="mould_name" value="<?php echo $info['mould_name'] ?>">
       </td>     
      </tr>
      <tr>
       
       <td>型腔数</td>
       <td>
         <input type="text" name="cavity_num" value="<?php echo $cavity ?>">
       </td>
        <td>图纸类型</td>
       <td>
          <?php
            foreach($array_drawing_type as $k=>$v){
                echo '<label><input type="checkbox" name="drawing_type[]" value="'.$k.'">'.$v.'</label> ';
            }
          ?>
       </td>
      </tr>
      <tr>
       <td>启动时间</td>
       <td>
         <input type="text" name="start_time">
       </td>
       <td>塑胶材料</td>
       <td>
         <input type="text" name="material_other">
       </td>
       
      
      </tr>
      <tr>
        <td>首板时间</td>
       <td>
         <input type="text" name="check_time">
       </td>
       <td>产品缩水率</td>
       <td style="background:yellow">
         <input type="text" name="shrink">
       </td>
      </tr>
      <tr>
         <td>预计走模时间</td>
       <td>
         <input type="text" name="finish_time">
       </td>
        <td>重点要求</td>
       <td>
             <?php
            foreach($array_require as $k=>$v){
                echo '<label><input type="checkbox" name="require[]" value="'.$k.'">'.$v.'</label> ';
            }
          ?>
       </td>
      </tr>
      <tr>
        <td class="noborder title">注塑机及周边匹配信息</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
       <td>机器品牌</td>
       <td>
         <input type="text" name="machine_supplier">
       </td>
       <td>机器吨位</td>
       <td>
         <input type="text" name="machine_tonnage" style="width:58%">
         <p class="tips">T</p>
       </td>
       <td>模具装夹方式</td>
       <td>
           <?php
            foreach($array_install_way as $k=>$v){
                echo '<label><input type="checkbox" name="install_way[]" value="'.$k.'">'.$v.'</label> ';
            }
          ?>
       </td>
      </tr>
      <tr>
       <td>定位环直径</td>
       <td>
         <input type="text" name="locator" style="width:58%">
         <p class="tips">mm</p>
       </td>
       <td>唧嘴SR</td>
       <td>
         <select name="ji_sr" id="ji_sr">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_ji_sr as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>KO直径、螺牙</td>
       <td>
          直径:
          <input type="text" name="screw_diameter" style="width:30px;">mm螺牙:
          <input type="text" name="screw" style="width:30px">M
       </td>
      </tr>
        <tr>
       <td>集水块接头规格</td>
       <td>
         <input type="text" name="catchment">
       </td>
       <td>电子阀接头规格</td>
       <td>
         <input type="text" name="electron_valve">
       </td>
       
       <td>气阀接头规格</td>
       <td>
         <input type="text" name="air_valve">
       </td>
      </tr>
      <tr>
       <td>集油块接头规格</td>
       <td>
         <input type="text" name="oil_collection">
       </td>
       <td>热流道温控箱接头规格</td>
       <td>
         <input type="text" name="temperature_control">
       </td>
       <td>其它要求</td>
       <td>
         <input type="text" name="other_require">
       </td>
      </tr>
      <tr>
        <td class="noborder title">模具布局</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
       <td>模具要求</td>
       <td>
            <select name="mould_require">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_mould_require as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>模具类型</td>
       <td>
           <?php
            foreach($array_mould_type as $k=>$v){
                echo '<label><input type="checkbox" name="mould_type[]" value="'.$k.'">'.$v.'</label> ';
                echo $k==3?'<br>':'';
            }
          ?>
       </td>
       <td>模具形式</td>
       <td>
         <select name="mould_way">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_mould_way as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
      </tr>
      <tr>
       <td>型腔/型芯方式</td>
       <td>
           <select name="cavity_mode">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_cavity_mode as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>组合互换</td>
       <td>
           <?php
            foreach($array_mould_group as $k=>$v){
                echo '<label><input type="checkbox" name="mould_group[]" value="'.$k.'">'.$v.'</label> ';
                echo $k == 1?'<br>':'';
            }
          ?>
       </td>
       <td>图纸标准</td>
       <td>
         <select name="drawing_standard">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_drawing_standard as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
      </tr>
      <tr>
       <td>难度系数</td>
       <td>
         <select name="difficulty_degree">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_difficulty_degree as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>质量等级</td>
       <td>
         <select name="quality_degree">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_quality_degree as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>模具寿命</td>
       <td>
        <input type="text" name="mould_life" style="width:58%">
        <p class="tips">万</p>
       </td>
      </tr>
      <tr>
       <td>模具是否出口</td>
       <td>
        <label>
          <input type="radio" name="is_export" value="1">
          是
        </label>
        <label>
          <input type="radio" name="is_export" value="0">
          否
        </label>  
       </td>
       <td>备模或类似参考</td>
       <td>
         <p class="tips">模号:</p>
         <input type="text" name="is_reference" style="width:58%">
       </td>
       <td>成型周期</td>
       <td>
         <input type="text" name="molding_cycle" style="width:58%">
         <p class="tips">S</p>
       </td>
      </tr>
      <tr>
        <td class="noborder title">进胶、冷却加热、抽芯、顶出等</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
       <td>浇口类型</td>
       <td>
         <select name="injection_type">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_injection_type as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>阀针类型</td>
       <td>
         <select name="needle_type">
                    <?php
         
                        foreach($array_needle_type as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>流道类型</td>
       <td>
         <select name="runner_type">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_runner_type as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
      </tr>
      <tr>
       <td>热流道品牌</td>
       <td>
         <select name="hot_runner_supplier">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_hot_runner_supplier as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>冷却加热介质</td>
       <td>
           <?php
            foreach($array_cool_medium as $k=>$v){
                echo '<label><input type="checkbox" name="cool_medium[]" value="'.$k.'">'.$v.'</label> ';
            }
          ?>
       </td>
       <td>特殊冷却加热</td>
       <td>
           <?php
            foreach($array_sepcial_cool as $k=>$v){
                echo '<label><input type="checkbox" name="sepcial_cool[]" value="'.$k.'">'.$v.'</label> ';
                echo $k == 2?'<br>':'';
            }
          ?>
       </td>
      </tr>
      <tr>
       <td>顶出系统</td>
       <td>
        <?php
          foreach($array_ejection_system as $k=>$v){
            echo '<label><input type="checkbox" name="ejection_system[]" value="'.$k.'">'.$v.'</label> ';
            echo $k==4?'<br>':'';
          } 
        ?>
       </td>
       <td>取件方式</td>
       <td>
         <select name="pickup_way">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_pickup_way as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>其它要求</td>
       <td>
         <input type="text" name="ejection_require">
       </td>
      </tr>
      <tr>
        <td class="noborder title">模具材料及要求</td>
        <td class="noborder" colspan="5"></td>
      </tr>
        <tr>
       <td>项目</td>
       <td>材料品牌</td>
       <td>材料牌号</td>
       <td>材料硬度</td>
       <td>特殊处理</td>
       <td>表面要求</td>
      </tr>
      <?php foreach($array_project_name as $key=>$value){ ?>
        <tr>
         <td><?php echo $value ?></td>
         <td>
            <?php if($key<4){ ?>
               <select name="material_supplier[]" class="material_supplier">
                 <?php
                    echo '<option value="">--请选择--</option>';
                    foreach($array_material_supplier as $k=>$v){
                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                     ?>
              </select>
            <?php }else{ ?>
              <select name="material_supplier[]" class="material_supplier">
                 <?php
                    echo '<option value="">--请选择--</option>';
                    foreach($array_material_county as $k=>$v){
                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                     ?>
              </select>
            <?php } ?>
         </td>
         <td>
           <select name="material_specification[]" class="material_specification">
                 <?php
                    echo '<option value="">--请选择--</option>';
                    foreach($array_material_specification as $k=>$v){
                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                     ?>
              </select>
         </td>
         <td>
            <select name="material_hard[]">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_material_hard as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
         </td>
         <td>
            <select name="special_handle[]">
                    <?php
                        foreach($array_special_handle as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
         </td>
         <td> 
              <select name="surface_require[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_surface_require as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
         </td>
      </tr>
      <?php }?>
      <tr>
        <td class="noborder title">配件标准</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td>项目</td>
        <td>品牌</td>
        <td>规格</td>
        <td>项目</td>
        <td>品牌</td>
        <td>规格</td>
      </tr>
      <tr>
        <td>标准件</td>
        <td>
           <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_water_connector as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
        <td>水管接头</td>
        <td>
          <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_water_connector as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
      </tr>
      <tr>
        <td>日期章</td>
        <td>
          <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_supplier as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
        <td>电子阀接头</td>
        <td>
          <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_air_connector as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
      </tr>
      <tr>
        <td>油缸</td>
        <td>
           <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_cylinder as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
        <td>气动接头</td>
        <td>
           <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_air_connector as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
      </tr>
      <tr>
        <td>皮纹</td>
        <td>
           <select name="skin_texture">
               <?php
                  echo '<option value="">--请选择--</option>';
                  foreach($array_skin_texture as $k=>$v){
                      echo '<option value="'.$k.'">'.$v.'</option>';
                       }
               ?>
            </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
        <td>油压接头</td>
        <td>
           <select name="supplier[]">
                      <?php
                         echo '<option value="">--请选择--</option>';
                          foreach($array_oil_connector as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>
          <input type="text" name="specification[]" class="specification">
        </td>
      </tr>
      <tr>
        <td class="noborder title">试模打样</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td>客户参与试模</td>
        <td>
          <label>
            <input type="radio" name="customer_join" value="1">
            是
          </label>
          <label>
            <input type="radio" name="customer_join" value="0">
            否
          </label>
        </td>
        <td>严格按客户要求试模</td>
        <td>
          <label>
            <input type="radio" name="customer_require" value="1">
            是
          </label>
          <label>
            <input type="radio" name="customer_require" value="0">
            否
          </label>
        </td>
        <td>客户是否需要走水板</td>
        <td>
          <label>
            <input type="radio" name="customer_water" value="1">
            是
          </label>
          <label>
            <input type="radio" name="customer_water" value="0">
            否
          </label>
        </td>
      </tr>
      <tr>
        <td>试模、打样胶料</td>
        <td>
         <select name="draw_material">
                      <?php
                          echo '<option value="">--请选择--</option>';
                          foreach($array_draw_material as $k=>$v){

                            echo '<option value="'.$k.'">'.$v.'</option>';
                          }
                       ?>
              </select>
        </td>
        <td>免费样品数量/次数</td>
        <td>
          <input type="text" name="draw_num" style="width:57px;"> 次 共
          <input type="text" name="total_num" style="width:57px"> 次
        </td>
        <td>寄样方式</td>
        <td>
         <select name="draw_post">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_draw_post as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
         </td>
      </tr>
      <tr>
        <td>产品检查报告</td>
        <td>
          <?php
            foreach($array_product_check as $k=>$v){
              echo '<label><input type="checkbox" name="product_check[]" value="'.$k.'">'.$v.'</label> ';
              echo $k==4?'<br>':'';
            } 
          ?>
        </td>
        <td>包装方式</td>
        <td>
           <select name="pack_method">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_pack_method as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>其它事项</td>
        <td>
          <input type="text" name="other_thing">
        </td>
      </tr>
      <tr>
        <?php for($i=0;$i<6;$i++){ 
          echo '<td>T'.$i.': <input type="text" style="width:100px" name="t_num[]"> 模</td>';
        } ?>
      </tr>
      <tr>
        <td class="noborder title">走模要求</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td>是否移模</td>
        <td>
         <label>
            <input type="radio" name="is_move" value="1">
            是
         </label> 
         <label>
            <input type="radio" name="is_move" value="0">
            否
         </label>
       </td>
       <td>模具交付目的地</td>
       <td>
           <select name="hand_over">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_hand_over as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
       </td>
       <td>交易结算方式</td>
       <td>
          <select name="settle_way">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_settle_way as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
       </td>
      </tr>
      <tr>
        <td>模具外观喷漆</td>
         <td>
          <select name="surface_spray">
                   <?php
                      foreach($array_surface_spray as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
         </td>
         <td>热流道、运水、动作铭牌</td>
         <td>
            <?php
              foreach($array_action_plate as $k=>$v){
                echo '<label><input type="checkbox" name="action_plate[]" value="'.$k.'">'.$v.'</label> ';
              } 
            ?>
         </td>
         <td>客户及我司铭牌</td>
         <td>
          <?php
            foreach($array_customer_plate as $k=>$v){
              echo '<label><input type="checkbox" name="customer_plate[]" value="'.$k.'">'.$v.'</label> ';
            } 
          ?>
         </td>
      </tr>
      <tr>
        <td>吊环、备件、电极、末次样品</td>
        <td>
          <?php
            foreach($array_mould_ring as $k=>$v){
              echo '<label><input type="checkbox" name="mould_ring[]" value="'.$k.'">'.$v.'</label> ';
            } 
          ?>
        </td>
        <td>模具手册、2D图纸、数据光盘</td>
        <td>
          <select name="mould_handbook">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_mould_handbook as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>钢材材质证明、热处理证明</td>
        <td>
          <label>
            <input type="radio" name="steel_material" value="1">
            要
          </label>
          <label>
            <input type="radio" name="steel_material" value="0">
            否
          </label>
        </td>
      </tr>
      <tr>
        <td>零件检查报告、走模前检查报告</td>
        <td>
            <label>
            <input type="radio" name="mould_check" value="1">
            要
          </label>
          <label>
            <input type="radio" name="mould_check" value="0">
            否
          </label>
        </td>
        <td>试模报告、样品检测报告</td>
        <td>
           <label>
            <input type="radio" name="sample_check" value="1">
            是
          </label>
          <label>
            <input type="radio" name="sample_check" value="0">
            否
          </label>
        </td>
         <td>末次试模照片、视频</td>
        <td>
          <label>
            <input type="radio" name="mould_photo" value="1">
            要
          </label>
          <label>
            <input type="radio" name="mould_photo" value="0">
            否
          </label>
        </td>
      </tr>
      <tr>
        <td>走模装箱照片、视频</td>
        <td>
          <label>
            <input type="radio" name="photo_vedio" value="1">
            要
          </label>
          <label>
            <input type="radio" name="photo_vedio" value="0">
            否
          </label>
        </td>
        <td>模具包装方式</td>
        <td>
          <select name="mould_pack">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_mould_pack as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
           <td>模具运输方式</td>
        <td>
           <select name="mould_transport">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_mould_transport as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>客户处交模、验模</td>
        <td>
           <label>
            <input type="radio" name="customer_try" value="1">
            是
          </label>
          <label>
            <input type="radio" name="customer_try" value="0">
            否
          </label>
        </td>
        <td>售后服务</td>
        <td>
           <select name="service_fee">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_service_fee as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>其它要求</td>
        <td>
          <input type="text" name="go_mould_require">
        </td>
      </tr>
      <tr>
        <td class="noborder title">流程控制</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td>产品设计</td>
        <td>
          <label>
            <input type="radio" name="product_design" value="1">
            是
          </label>
          <label>
            <input type="radio" name="product_design" value="0">
            否
          </label>
       </td>
       <td>模流分析</td>
       <td>
          <label>
            <input type="radio" name="mould_analyse" value="1">
            是
          </label>
          <label>
            <input type="radio" name="mould_analyse" value="0">
            否
          </label>
       </td>
        <td>DFM报告</td>
        <td>
          <label>
            <input type="radio" name="dfm_report" value="1">
            是
          </label>
          <label>
            <input type="radio" name="dfm_report" value="0">
            否
          </label>
        </td>
      </tr>
        <td>2D模具结构设计图</td>
        <td>
          <label>
            <input type="radio" name="drawing_2d" value="1">
            是
          </label>
          <label>
            <input type="radio" name="drawing_2d" value="0">
            否
          </label>
        </td>
        <td>全3D模具图</td>
        <td>
          <label>
            <input type="radio" name="drawing_3d" value="1">
            是
          </label>
          <label>
            <input type="radio" name="drawing_3d" value="0">
            否
          </label>
        </td>
        <td>图纸检查对照表</td>
        <td>
          <?php
            foreach($array_drawing_check as $k=>$v){
              echo '<label><input type="checkbox" name="drawing_check[]" value="'.$k.'">'.$v.'</label> ';
            } 
          ?>
        </td>
      </tr>
      <tr>
         <td>项目启动会</td>
        <td>
          <label>
            <input type="radio" name="project_start" value="1">
            是
          </label>
          <label>
            <input type="radio" name="project_start" value="0">
            否
          </label>
        </td>
         <td>产品评审会</td>
        <td>
          <label>
            <input type="radio" name="product_judge" value="1">
            是
          </label>
          <label>
            <input type="radio" name="product_judge" value="0">
            否
          </label>
        </td>
         <td>模具结构评审会</td>
        <td>
          <label>
            <input type="radio" name="mould_judge" value="1">
            是
          </label>
          <label>
            <input type="radio" name="mould_judge" value="0">
            否
          </label>
        </td>
      </tr>
      <tr>
         <td>零件加工工艺评审会</td>
        <td>
          <label>
            <input type="radio" name="machining_judge" value="1">
            是
          </label>
          <label>
            <input type="radio" name="machining_judge" value="0">
            否
          </label>
        </td>
        <td>客户评审方式</td>
        <td>
           <select name="judge_method">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_judge_method as $k=>$v){
                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>客户确认图纸方式</td>
        <td>
           <select name="customer_confirm">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_customer_confirm as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>项目进度汇报</td>
        <td>
           <select name="project_progress">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_project_progress as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
         <td>出错处理</td>
        <td>
           <select name="error_report">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($array_error_report as $k=>$v){

                         echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>其它要求</td>
        <td>
          <input type="text" name="control_require">
        </td>
      </tr>
      <tr>
        <td class="noborder title">草图及重点提示</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td colspan="6" style="height:100px;text-align:left;background:white">
          <input type="file" name="file[]" onchange="view(this)"/>

          <span id="preview"></span>
        </td>
      </tr>
      <tr>
        <td class="noborder title">负责人与审核</td>
        <td class="noborder" colspan="5"></td>
      </tr>
      <tr>
        <td>销售</td>
        <td>
           <select name="saler">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($saler as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>部门经理</td>
        <td>
           <select name="manager[]">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($saler as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>意见</td>
        <td>
          <input type="text" name="suggestion[]">
        </td>
      </tr>
         <tr>
        <td>项目</td>
        <td>
          <select name="projecter">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($projecter as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>部门经理</td>
        <td>
          <select name="manager[]">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($projecter as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>意见</td>
        <td>
          <input type="text" name="suggestion[]">
        </td>
      </tr>
      <tr>
        <td>设计</td>
        <td>
          <select name="designer">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($designer as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>部门经理</td>
        <td>
          <select name="manager[]">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($designer as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>意见</td>
        <td>
          <input type="text" name="suggestion[]">
        </td>
      </tr>
       <tr>
        <td>编程</td>
        <td>
          <select name="programming">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($programming as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>部门经理</td>
        <td>
          <select name="manager[]">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($programming as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>意见</td>
        <td>
          <input type="text" name="suggestion[]">
        </td>
      </tr>
       <tr>
        <td>装配</td>
        <td>
          <select name="assembler">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($assembler as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>部门经理</td>
        <td>
          <select name="manager[]">
                   <?php
                      echo '<option value="">--请选择--</option>';
                      foreach($assembler as $k=>$v){

                         echo '<option value="'.$v[0].'">'.$v[1].'</option>';
                        }
                   ?>
          </select>
        </td>
        <td>意见</td>
        <td>
          <input type="text" name="suggestion[]">
        </td>
      </tr>
      <tr class="distance"></tr>
      <tr>
        <td colspan="7" style="background:white">
          <input type="submit" id="save" class="submit" value="发起项目">
          <span id="back" onclick="window.history.go(-1);">返回</span>
        </td>
      </tr>
       </table>
   </form>  
</div>
<?php } ?>
 <?php include "../footer.php"; ?>
</body>
</html>