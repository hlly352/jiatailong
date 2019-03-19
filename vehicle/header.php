<div id="header">
  <h4>用车管理 <em>V1.0 BY Hillion 2016.03.15</em></h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="/vehicle/">首页</a></li>
    <li class="menulevel"><a href="employee_vehicle_list.php">用车数据</a>
    <li class="menulevel"><a href="vehicle.php">车辆数据</a>
    <li class="menulevel"><a href="vehicle_budget.php">费用预算</a></li>
    <li class="menulevel"><a href="approve_flow.php">审批流程</a></li>
    <li class="menulevel"><a href="vehicle_settle_list.php">结算清单</a></li>
    <li class="menulevel"><a href="#">报表</a>
      <ul>
        <li><a href="report_vehicle_day.php">日报表</a></li>
        <li><a href="report_vehicle_month.php">月报表</a></li>
        <li><a href="report_vehicle_month_dept.php">部门报表</a></li>
        <li><a href="report_vehicle_month_dotype.php">类型报表</a></li>
        <li><a href="report_vehicle_month_cost.php">车辆报表</a></li>
        <li><a href="report_vehicle_month_settle.php">月结报表</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>