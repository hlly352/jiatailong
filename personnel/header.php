<div id="header">
  <h4>人事系统 <em>V1.0 BY Hillion 2016.03.06</em></h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="/personnel/">首页</a></li>
    <li class="menulevel"><a href="#">基础数据</a>
      <ul>
        <li><a href="department.php">部门</a></li>
        <li><a href="position.php">职位</a></li>
        <li><a href="staffing.php">人员编制</a></li>
        <!-- <li><a href="vacation.php">假期类型</a></li> -->
      </ul>
    </li>
    <li class="menulevel"><a href="personnel_organization.php">组织架构</a></li>
    <li class="menulevel"><a href="employee.php">员工信息</a></li>
    <li class="menulevel"><a href="account.php">员工账号</a></li>
    <li class="menulevel"><a href="employee_photo.php">员工看板</a></li>
    <li class="menulevel"><a href="employee_level_chart.php" target="_blank">员工关系</a></li>
    <li class="menulevel"><a href="#">加班/请假</a>
      <ul>
        <li><a href="employee_overtime.php">加班确认</a></li>
        <li><a href="employee_leave.php">请假确认</a></li>
        <li><a href="employee_leave_overtime.php">抵扣记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">报表</a>
      <ul>
        <li><a href="report_attendance_month.php">考勤月报表</a></li>
        <li><a href="report_attendance_year.php">考勤年报表</a></li>
        <li><a href="report_staffing.php">编制报表</a></li>
        <li><a href="employee_in_out.php">入/离职记录</a></li>
        <li><a href="report_in_out.php">入/离月报表</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>