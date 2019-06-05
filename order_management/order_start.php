<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>

<title>订单管理-嘉泰隆</title>
<style type="text/css">
      table{width:100%;border-collapse: collapse;border-spacing: 2px;}

      table tr td{border:1px solid grey;font-size:11px;display:table-cell;text-align:center;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){

   
 
    })
</script>
</head>

<body>
<?php include "header.php"; ?>

  <h4 style="padding-left:10px">
      发起简易项目（简易项目与模具规格书二选一）
  </h4>
  <div id="table_list">
  <form action="" name="search" method="get">
    <table>
        <tr>
          <td colspan="6">客户合同编号</td>
          <td></td>
        </tr>
         <tr>
          <td rowspan="4">基本信息</td>
          <td>客户代码</td>
          <td></td>
          <td>项目名称</td>
          <td></td>
          <td>产品名称</td>
          <td></td>
        </tr>
         <tr>
          <td>材质/其他</td>
          <td></td>
          <td>图纸类型</td>
          <td></td>
          <td>重点要求</td>
          <td></td>
        </tr>
         <tr>
          <td>模具编号<br>任务编号</td>
          <td></td>
          <td>任务内容</td>
          <td colspan="3"></td>
        </tr>
         <tr>
          <td>启动时间</td>
          <td></td>
          <td>完成时间</td>
          <td></td>
          <td>验收时间</td>
          <td></td>
        </tr>
        <tr class="distance"></tr>
         <tr>
          <td rowspan="2">责任人与审核</td>
          <td>销售经理</td>
          <td></td>
          <td>项目经理</td>
          <td></td>
          <td>责任人</td>
          <td></td>
        </tr>
         <tr>
          <td>填表（销售及项目经理）：</td>
          <td></td>
          <td>审核（责任部门经理）：</td>
          <td></td>
          <td>批准（副总经理）：</td>
          <td></td>
        </tr>
    </table>
  </form>
  <h4 style="padding-left:10px">
      发起模具规格书（模具规格书与简易项目二选一）
  </h4>
  <form action="order_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
       <td rowspan="4">基本资料</td>
       <td>客户代码</td>
       <td></td>
       <td>项目名称</td>
       <td></td>
       <td>产品名称</td>
       <td></td>
      </tr>
      <tr>
       <td>塑胶材料</td>
       <td></td>
       <td>图纸类型</td>
       <td></td>
       <td>重点要求</td>
       <td></td>
      </tr>
      <tr>
       <td>模具编号</td>
       <td></td>
       <td>型腔数</td>
       <td></td>
       <td>产品缩水率</td>
       <td></td>
      </tr>
      <tr>
       <td>启动时间</td>
       <td></td>
       <td>首板时间</td>
       <td></td>
       <td>预计走模时间</td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="4">注塑机及周边匹配信息</td>
       <td>机器品牌</td>
       <td></td>
       <td>机器吨位</td>
       <td></td>
       <td>模具装夹方式</td>
       <td></td>
      </tr>
      <tr>
       <td>定位环直径</td>
       <td></td>
       <td>唧嘴SR</td>
       <td></td>
       <td>KO直径、螺牙</td>
       <td></td>
      </tr>
        <tr>
       <td>集水块接头规格</td>
       <td></td>
       <td>集油块接头规格</td>
       <td></td>
       <td>气阀接头规格</td>
       <td></td>
      </tr>
        <tr>
       <td>电子阀接头规格</td>
       <td></td>
       <td>热流道温控箱接头规格</td>
       <td></td>
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="4">模具布局</td>
       <td>模具要求</td>
       <td></td>
       <td>模具类型</td>
       <td></td>
       <td>模具形式</td>
       <td></td>
      </tr>
      <tr>
       <td>型腔/型芯方式</td>
       <td></td>
       <td>组合互换</td>
       <td></td>
       <td>图纸标准</td>
       <td></td>
      </tr>
      <tr>
       <td>难度系数</td>
       <td></td>
       <td>质量等级</td>
       <td></td>
       <td>模具寿命</td>
       <td></td>
      </tr>
      <tr>
       <td>模具是否出口</td>
       <td></td>
       <td>成型周期</td>
       <td></td>
       <td></td>
       <td></td>
      </tr>
      <tr class="distance"></tr>
      <tr>
       <td rowspan="3">进胶、冷却加热、顶出</td>
       <td>浇口类型INJECTION GATE</td>
       <td></td>
       <td>阀针类型</td>
       <td></td>
       <td>流道类型</td>
       <td></td>
      </tr>
      <tr>
       <td>热流道品牌</td>
       <td></td>
       <td>冷却加热介质</td>
       <td></td>
       <td>特殊冷却加热</td>
       <td></td>
      </tr>
      <tr>
       <td>顶出系统EJECTION SYSTEM</td>
       <td></td>
       <td>取件方式</td>
       <td></td>
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
        <td>审核（设计部经理）：</td>
        <td></td>
      </tr>
       </table>
      
</div>
 <?php include "../footer.php"; ?>
</body>
</html>