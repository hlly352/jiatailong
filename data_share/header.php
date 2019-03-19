<div id="header">
  <h4>信息共享 <em>V1.0 BY Hillion 2018.07.17</em></h4>
</div>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/data_share/">首页</a></li> -->
    <li class="menulevel"><a href="mould_data.php">模具数据</a></li>
    <li class="menulevel"><a href="mould_material.php">模具物料</a></li>
    <li class="menulevel"><a href="#">采购管理</a>
      <ul>
        <li><a href="material_order.php">物料订单</a></li>
        <li><a href="material_order_list.php">物料订单明细</a></li>
        <li><a href="cutter_order.php">刀具订单</a></li>
        <li><a href="cutter_order_list.php">刀具订单明细</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">物控管理</a>
      <ul>
        <li><a href="material_inout_list_in.php">物料入库记录</a></li>
        <li><a href="material_inout_list_out.php">物料出库记录</a></li>
        <li><a href="material_abnormal_entry.php">物料异常入库</a></li>
        <li><a href="cutter_inout_list_in.php">刀具入库记录</a></li>
        <li><a href="cutter_inout_list_out.php">刀具出库记录</a></li>
        <li><a href="cutter_abnormal_entry.php">刀具异常入库</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="mould_cutter.php">刀具库存</a></li>
    <li class="menulevel"><a href="#">模具加工</a>
      <ul>
        <li><a href="mould_outward.php">外协加工</a></li>
        <li><a href="mould_weld.php">零件烧焊</a></li>
        <li><a href="mould_try.php">模具试模</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>
