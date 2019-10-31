<div id="header">
  <h4>模具加工 <em>V1.0 BY Hillion 2016.03.30</em></h4>
</div>
<?php $isadmin = $_SESSION['system_shell'][$system_dir]['isadmin']; ?>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/mould_processing/">首页</a></li> -->
    <li class="menulevel"><a href="#">加工工艺</a></li>
    <li class="menulevel"><a href="#">生产进度</a></li>
    <li class="menulevel"><a href="#">外协加工</a>
        <ul>
          <li><a href="mould_material.php">待询外协</a></li>
          <li><a href="outward_inquiry_list.php">询价处理</a></li>
          <?php if($isadmin == 1){ ?>
          <li><a href="outward_inquiry_order.php">询价单</a></li>
          <li><a href="mould_outward_order.php">外协订单</a></li>
          <?php }?>
          <li><a href="mould_outward_order_list.php">外协明细</a></li>
        </ul>
    </li>
    <li class="menulevel"><a href="mould_outward.php">外协加工（旧）</a></li>
    <li class="menulevel"><a href="processing_data.php">加工资料</a></li>
    <li class="menulevel"><a href="mould.php">外协成本</a></li>
    <!-- <li class="menulevel"><a href="mould.php">模具数据</a></li>
    <li class="menulevel"><a href="mould_material.php">模具物料</a></li> -->
    <li class="menulevel"><a href="mould_outward_order.php">加工申请</a></li>
    <li class="menulevel"><a href="mould_weld.php">零件烧焊</a></li>
    <li class="menulevel"><a href="#">模具试模</a>
      <ul>
        <li><a href="mould_try_finish_list.php">试模确认</a></li>
        <li><a href="mould_try.php">试模记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="mould_try.php">报表</a>
      <ul>
        <li><a href="#">外协</a>
          <ul style="position:relativel; left:101px; top:0px;">
            <li><a href="report_mould_outward_month.php">费用月报表</a></li>
            <li><a href="report_supplier_outward.php">供应商报表</a></li>
            <li><a href="report_mould_outward_workteam.php">组别报表</a></li>
            <li><a href="report_mould_outward_type.php">类型报表</a></li>
          </ul>
        </li>
        <li><a href="#">烧焊</a>
          <ul style="position:relativel; left:101px; top:32px;">
            <li><a href="report_mould_weld_month.php">费用月报表</a></li>
            <li><a href="report_supplier_weld.php">供应商报表</a></li>
            <li><a href="report_mould_weld_workteam.php">组别报表</a></li>
            <li><a href="report_mould_weld_type.php">类型报表</a></li>
            <li><a href="report_mould_weld_responsibility_team.php">责任组别报表</a></li>
          </ul>
        </li>
        <li><a href="#">试模</a>
          <ul style="position:relativel; left:101px; top:64px;">
            <li><a href="report_mould_try_month.php">费用月报表</a></li>
            <li><a href="report_supplier_try.php">供应商报表</a></li>
            <li><a href="report_mould_try_times.php">次数报表</a></li>
            <li><a href="report_mould_try_tonnage.php">吨位报表</a></li>
            <li><a href="report_mould_try_cause.php">原因报表</a></li>
          </ul>
        </li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>
