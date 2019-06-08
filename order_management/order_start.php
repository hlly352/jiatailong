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
  echo '<meta charset="utf-8">';
  var_dump($info);
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

<title>订单管理-嘉泰隆</title>
<style type="text/css">
      table{width:100%;border-collapse: collapse;border-spacing: 2px;}
      .distance{height:10px;}
      table tr td{border:1px solid grey;font-size:11px;display:table-cell;text-align:center;}
      .submit{width:80px;height:25px;}
      select{width:155px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){

   
 
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
      发起简易项目
  </h4>
  <div id="table_list">
  <form action="" name="search" method="get">
    <table>
        <tr>
          <td colspan="6" style="text-align:right">客户合同编号</td>
          <td>
              <input type="text" name="customer_order_no" value="<?php echo $info['customer_order_no'] ?>" >
          </td>
        </tr>
         <tr>
          <td rowspan="4">基本信息</td>
          <td>客户代码</td>
          <td>
              <input type="text" name="customer_id" value="<?php echo $info['customer_code'] ?>" >
          </td>
          <td>项目名称</td>
          <td>
              <input type="text" name="project_name" vlaue="<?php echo $info['project_name'] ?>">
          </td>
          <td>产品名称</td>
          <td>
            <input type="text" name="mould_name" value="<?php echo $info['mould_name'] ?>">
          </td>
        </tr>
         <tr>
          <td>塑胶材料</td>
          <td>
            <input type="text" name="plastic_material" >
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
            <input type="text" name="task_content" >
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
      发起模具规格书
  </h4>
  <div id='table_list'>
  <form action="order_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
       <td rowspan="4">基本资料</td>
       <td>客户代码</td>
       <td>
         <input type="text" name="customer_code" value="<?php echo $info['customer_code'] ?>">
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
       <td>塑胶材料</td>
       <td>
         <input type="text" name="plastic_material">
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
       <td>模具编号</td>
       <td>
         <input type="text" name="mould_no" value="<?php echo $info['mould_no'] ?>">
       </td>
       <td>型腔数</td>
       <td>
         <input type="text" name="cavity_num" >
       </td>
       <td>产品缩水率</td>
       <td>
         <input type="text" name="shrink">
       </td>
      </tr>
      <tr>
       <td>启动时间</td>
       <td>
         <input type="text" name="start_time">
       </td>
       <td>首板时间</td>
       <td>
         <input type="text" name="check_time">
       </td>
       <td>预计走模时间</td>
       <td>
         <input type="text" name="finish_time">
       </td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="4">注塑机及周边匹配信息</td>
       <td>机器品牌</td>
       <td>
         <input type="text" name="machine_supplier">
       </td>
       <td>机器吨位</td>
       <td>
         <input type="text" name="machine_tonnage">
       </td>
       <td>模具装夹方式</td>
       <td>
          <select name="install_way">
                  <?php
                     echo '<option value="">--请选择--</option>';
                      foreach($array_install_way as $k=>$v){

                        echo '<option value="'.$k.'">'.$v.'</option>';
                      }
                   ?>
          </select>
       </td>
      </tr>
      <tr>
       <td>定位环直径</td>
       <td>
         <input type="text" name="locator">
       </td>
       <td>唧嘴SR</td>
       <td>
         <select name="ji_sr">
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
         <input type="text" name="screw">
       </td>
      </tr>
        <tr>
       <td>集水块接头规格</td>
       <td>
         <input type="text" name="catchment">
       </td>
       <td>集油块接头规格</td>
       <td>
         <input type="text" name="oil_collection">
       </td>
       <td>气阀接头规格</td>
       <td>
         <input type="text" name="air_valve">
       </td>
      </tr>
        <tr>
       <td>电子阀接头规格</td>
       <td>
         <input type="text" name="electron_valae">
       </td>
       <td>热流道温控箱接头规格</td>
       <td>
         <input type="text" name="temperature_control">
       </td>
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="4">模具布局</td>
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
           <select name="mould_type">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_mould_type as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
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
       	 <select name="mould_group">
                    <?php
                        foreach($array_mould_group as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
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
       	<input type="text" name="mould_life">
       </td>
      </tr>
      <tr>
       <td>模具是否出口</td>
       <td>
       	<label><input type="radio" name="is_export"> 是</label>
        <label><input type="radio" name="is_export"> 否</label>  
       </td>
       <td>成型周期</td>
       <td>
         <input type="text" name="molding_cycle">
       </td>
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="3">进胶、冷却加热、顶出</td>
       <td>浇口类型INJECTION GATE</td>
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
         <select name="cool_medium">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_cool_medium as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
       <td>特殊冷却加热</td>
       <td>
         <select name="sepcial_cool">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_sepcial_cool as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
       </td>
      </tr>
      <tr>
       <td>顶出系统EJECTION SYSTEM</td>
       <td>
         <select name="ejection_system">
                    <?php
                       echo '<option value="">--请选择--</option>';
                        foreach($array_ejection_system as $k=>$v){

                          echo '<option value="'.$k.'">'.$v.'</option>';
                        }
                     ?>
            </select>
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
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
        <tr>
       <td>项目</td>
       <td>材料品牌</td>
       <td>材料牌号</td>
       <td>材料硬度</td>
       <td>特殊处理</td>
       <td>其他</td>
       <td>表面要求</td>
      </tr>
        <tr>
       <td>模架</td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
        <tr>
       <td>项目</td>
       <td>品牌/Supplier</td>
       <td>规格型号/Specification</td>
       <td rowspan="3">设计要求</td>
       <td><label><input type="checkbox">产品设计</td>
       <td><label><input type="checkbox">模流分析</label></td>
       <td><label><input type="checkbox">DFM报告</label></td>
      </tr>
        <tr>
       <td>镶件、日期章/Inserts</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox">2D模具结构图</label></td>
       <td><label><input type="checkbox">3D模具图</label></td>
       <td><label><input type="checkbox">图纸检查对照表</label></td>
      </tr>
        <tr>
       <td>标准件</td>
       <td></td>
       <td></td>
       <td>客户确认书</td>
       <td>客户当面检讨</td>
       <td></td>
      </tr>
        <tr>
       <td>水管接头</td>
       <td></td>
       <td></td>
       <td rowspan="6">走模要求</td>
       <td>是否移模</td>
       <td></td>
       <td></td>
      </tr>
        <tr>
       <td>油管接头</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox" >纸制模具图纸</label></td>
       <td><label><input type="checkbox" >模具手册</label></td>
       <td><label><input type="checkbox" >钢材材质证明</label></td>
      </tr>
        <tr>
       <td>热流道接头</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox" >模具检测报告</label></td>
       <td><label><input type="checkbox" >样品检查报告</label></td>
       <td><label><input type="checkbox" >试模成型报告</label></td>
      </tr>
        <tr>
       <td>油缸</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox" >模具末次试模照片、视频</label></td>
       <td><label><input type="checkbox" >走模装箱照片、视频</label></td>
       <td><label><input type="checkbox" >光盘、USB电子数据</label></td>
      </tr>
        <tr>
       <td>皮纹</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox" >外观喷漆</label></td>
       <td><label><input type="checkbox" >客户铭牌</label></td>
       <td><label><input type="checkbox" >吊环</label></td>
      </tr>
        <tr>
       <td>特殊表面处理</td>
       <td></td>
       <td></td>
       <td><label><input type="checkbox" >易损备件</label></td>
       <td><label><input type="checkbox" >电极</label></td>
       <td><label><input type="checkbox" >检具、夹具</label></td>
      </tr>
      <tr class="distance"></tr>
        <tr>
       <td rowspan="3">样品、模具交付</td>
       <td>试模、打样胶料</td>
       <td></td>
       <td>免费样品数量/次数</td>
       <td></td>
       <td>寄样方式</td>
       <td></td>
      </tr>
        <tr>
       <td>模具包装方式</td>
       <td></td>
       <td>模具运输方式</td>
       <td></td>
       <td>模具交付地点</td>
       <td></td>
      </tr>
        <tr>
       <td>客户公司交模</td>
       <td></td>
       <td>收取售后服务费用</td>
       <td></td>
       <td>交货结算方式</td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
        <td>流程控制</td>
        <td>产品评审会</td>
        <td></td>
        <td>模具评审会</td>
        <td></td>
        <td>加工工艺评审会</td>
        <td></td>
      </tr>
         <tr>
        <td>重点提示</td>
        <td colspan="6"></td>
      </tr>
      <tr class="distance"></tr>
        <tr>
        <td rowspan="3">责任人与审核</td>
        <td>销售经理</td>
        <td></td>
        <td>项目经理</td>
        <td></td>
        <td>主设计工程师</td>
        <td></td>
      </tr>
         <tr>
        <td>主编程工程师</td>
        <td></td>
        <td>钳工组别</td>
        <td></td>
        <td>钳工技师</td>
        <td></td>
      </tr>
         <tr>
        <td>填表（销售及项目经理）：</td>
        <td></td>
        <td>审核（设计部经理）：</td>
        <td></td>
        <td>批准（副总经理）：</td>
        <td></td>
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
<?php } ?>
 <?php include "../footer.php"; ?>
</body>
</html>