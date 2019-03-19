<div id="header">
  <h4>我的办公 My Office <em>V1.0 BY Hillion 2016.03.12</em></h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="/my_office/">帮助</a></li>
    <li class="menulevel"><a href="#">我的工作</a>
      <ul>
        <li><a href="routine_work.php">例行工作</a></li>
        <li><a href="job_plan.php?type=A">日计划</a></li>
        <li><a href="job_plan.php?type=B">周计划</a></li>
        <li><a href="job_plan.php?type=C">月计划</a></li>
        <li><a href="job_plan.php?type=D">年计划</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">我的申请</a>
      <ul>
        <li><a href="employee_goout_apply.php">出门</a></li>
        <li><a href="employee_leave_apply.php">请假</a></li>
        <li><a href="employee_overtime_apply.php">加班</a></li>
        <li><a href="employee_vehicle_apply.php">用车</a></li>
        <li><a href="employee_express_apply.php">快递</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">我的审批</a>
      <ul>
        <li><a href="employee_goout_approve_list.php">出门</a></li>
        <li><a href="employee_leave_approve_list.php">请假</a></li>
        <li><a href="employee_overtime_approve_list.php">加班</a></li>
        <li><a href="employee_vehicle_approve_list.php">用车</a></li>
        <li><a href="employee_express_approve_list.php">快递</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="today_info.php">今日信息</a></li>
    <li class="menulevel"><a href="#">历史记录</a>
      <ul>
        <li><a href="employee_goout.php">出门</a></li>
        <li><a href="employee_leave.php">请假</a></li>
        <li><a href="employee_overtime.php">加班</a></li>
        <li><a href="employee_vehicle.php">用车</a></li>
        <li><a href="employee_express.php">寄快递</a></li>
        <li><a href="employee_express_receive.php">收快递</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">统计</a>
      <ul>
        <li><a href="report_employee_goout.php">出门</a></li>
        <li><a href="report_employee_leave.php">请假</a></li>
        <li><a href="report_employee_overtime.php">加班</a></li>
        <li><a href="#">快递</a>
          <ul style="position:relativel; left:101px; top:96px;">
            <li><a href="report_employee_express.php">寄快递</a></li>
            <li><a href="report_employee_express_receive.php">收快递</a></li>
          </ul>
        </li>
        <li><a href="job_plan_list.php">计划</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>