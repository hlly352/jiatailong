<div id="header">
  <h4>采购管理 <em>V1.0 BY Hillion 2016.03.30</em></h4>
</div>
<style type="text/css">
  #menu ul li.menulevel ul li a{background: skyblue;font-weight:200; color:black;font-size:13px;}
</style>
<div id="menu">
  <ul>
<!--     <li class="menulevel"><a href="/material_purchase/">帮助</a></li> -->
    <li class="menulevel"><a href="#">待询事项</a>
      <ul>
        <li><a href="material_inquiry.php">模具物料</a></li>
        <li><a href="cutter_inquiry.php">加工刀具</a></li>
        <li><a href="other_material.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">询价处理</a>
      <ul>
        <li><a href="material_inquiry_order.php">模具物料</a></li>
        <li><a href="cutter_inquiry_list.php">加工刀具</a></li>
        <li><a href="other_inquiry_material.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">采购订单</a>
      <ul>
        <li><a href="material_order.php">模具物料</a></li>
        <li><a href="cutter_order.php">加工刀具</a></li>
        <li><a href="other_material_order.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">订单明细</a>
      <ul>
        <li><a href="material_orderlist.php">模具物料</a></li>
        <li><a href="cutter_orderlist.php">加工刀具</a></li>
        <li><a href="other_material_list.php">期间物料</a></li>
      </ul>
    <li class="menulevel"><a href="#">入库记录</a>
      <ul>
        <li><a href="material_inout_list_in.php">模具物料</a></li>
        <li><a href="cutter_inout_list_in.php">加工刀具</a></li>
        <li><a href="other_inout_list_in.php">期间物料</a></li> 
      </ul>
    </li>
    <li class="menulevel"><a href="#">异常入库</a>
      <ul>
        <li><a href="material_abnormal_in.php">模具物料</a></li>
        <li><a href="cutter_abnormal_in.php">加工刀具</a></li>
        <li><a href="other_material_abnormal_in.php">期间物料</a></li> 
      </ul>
    </li>
    <li class="menulevel"><a href="mould_outward.php">外协加工</a></li>
    <li class="menulevel"><a href="#">采购对账</a>
      <ul>
        <li><a href="material_balance_account.php">模具物料</a></li>
        <li><a href="cutter_balance_account.php">加工刀具</a></li>
        <li><a href="other_material_balance_account.php">期间物料</a></li> 
        <li><a href="outward_balance_account.php">外协加工</a></li> 
      </ul>
    </li>
    <li class="menulevel"><a href="material_invoice_manage.php">发票管理</a></li>
    <li class="menulevel"><a href="material_funds_manage.php">应付账款</a></li>
    <!-- <li class="menulevel"><a href="prepayment_manage.php">预付账款</a></li> -->
    <li class="menulevel"><a href="material_funds_plan.php">付款管理</a></li>
    <li class="menulevel"><a href="material_funds_summary.php">账款管理</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
</div>
<div class="clear"></div>
