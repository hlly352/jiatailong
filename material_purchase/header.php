<div id="header">
  <h4>采购管理 <em>V1.0 BY Hillion 2016.03.30</em></h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="/material_purchase/">帮助</a></li>
    <li class="menulevel"><a href="#">待询事项</a>
      <ul>
        <li><a href="material_inquiry.php">待询物料</a></li>
        <li><a href="cutter_inquiry.php">待询刀具</a></li>
        <li><a href="other_material.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">询价单</a>
      <ul>
        <li><a href="material_inquiry_list.php">物料询价单</a></li>
        <li><a href="cutter_inquiry_list.php">刀具询价单</a></li>
        <li><a href="other_inquiry_material.php">期间物料询价单</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">采购订单</a>
      <ul>
        <li><a href="material_order.php">物料订单</a></li>
        <li><a href="cutter_order.php">刀具订单</a></li>
        <li><a href="other_material_order.php">期间物料订单</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">订单明细</a>
      <ul>
        <li><a href="material_orderlist.php">物料明细</a></li>
        <li><a href="cutter_orderlist.php">刀具明细</a></li>
      </ul>
    <li class="menulevel"><a href="material_inout_list_in.php">入库记录</a>
      <ul>
        <li><a href="material_inout_list_in.php">物料记录</a></li>
        <li><a href="cutter_inout_list_in.php">刀具记录</a></li>
        <li><a href="mould_outward.php">外协加工</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
