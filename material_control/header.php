<div id="header">
  <h4>物控管理 <em>V1.0 BY Hillion 2016.04.06</em></h4>
</div>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/material_control/">首页</a></li> -->
    <li class="menulevel"><a href="#">待入库</a>
      <ul>
        <li><a href="material_in_list.php">物料</a></li>
        <li><a href="cutter_in_list.php">刀具</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">待出库</a>
      <ul>
        <li><a href="material_out_list.php">物料</a></li>
        <li><a href="cutter_out_list.php">刀具</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">入库记录</a>
      <ul>
        <li><a href="material_inout_list_in.php">物料</a></li>
        <li><a href="cutter_inout_list_in.php">刀具</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">出库记录</a>
      <ul>
        <li><a href="material_inout_list_out.php">物料</a></li>
        <li><a href="cutter_inout_list_out.php">刀具</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">异常入库</a>
      <ul>
        <li><a href="material_abnormal_in.php">物料</a></li>
        <li><a href="cutter_abnormal_in.php">刀具</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">入库单</a>
      <ul>
        <li><a href="material_godown_entry.php">物料入库单</a></li>
        <li><a href="cutter_godown_entry.php">刀具入库单</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="copper_material.php">铜料分析</a></li>
    <li class="menulevel"><a href="mould_cost.php">模具成本</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
