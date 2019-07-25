<div id="header">
  <h4>财务管理 <em>V1.0 BY Hillion 2016.04.12</em></h4>
</div>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/financial_management/">首页</a></li> -->
    <li class="menulevel"><a href="#">物料订单</a>
      <ul>
        <li><a href="wait_material_order_payment_list.php">待付订单</a></li>
        <li><a href="material_order_payment_list.php">已付订单</a></li>
        <li><a href="material_order_list.php">订单明细</a></li>
        <li><a href="material_inout_list_in.php">入库记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">刀具订单</a>
      <ul>
        <li><a href="wait_cutter_order_payment_list.php">待付订单</a></li>
        <li><a href="cutter_order_payment_list.php">已付订单</a></li>
        <li><a href="cutter_order_list.php">订单明细</a></li>
        <li><a href="cutter_inout_list_in.php">入库记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">外协加工</a>
      <ul>
        <li><a href="wait_mould_outward_payment_list.php">待付加工</a></li>
        <li><a href="mould_outward_payment_list.php">已付加工</a></li> 
      </ul>
    </li>
     <li class="menulevel"><a href="#">对账审核</a>
      <ul>
        <li><a href="material_balance_account.php">模具物料</a></li>
        <li><a href="cutter_balance_account.php">加工刀具</a></li>
        <li><a href="cutter_inout_list_in.php">期间物料</a></li>
      </ul>
     </li>
      <li class="menulevel"><a href="material_invoice_manage.php">发票接收</a></li>
     <li class="menulevel"><a href="funds_plan_approval.php">付款审核</a></li>
     <li class="menulevel"><a href="#">应付账款管理</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
