<div id="header">
  <h4>门卫管理 <em>V1.0 BY Hillion 2016.03.15</em></h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="/guard/">帮助</a></li>
    <li class="menulevel"><a href="today_info.php">今日信息</a>
    <li class="menulevel"><a href="employee_goout.php">出门确认</a></li>
    <li class="menulevel"><a href="employee_leave.php">请假确认</a></li>
    <li class="menulevel"><a href="#">出货确认</a></li>
    <li class="menulevel"><a href="employee_vehicle.php">用车结算</a></li>
    <li class="menulevel"><a href="#">快递结算</a>
      <ul>
        <li><a href="employee_express.php">寄快递</a></li>
        <li><a href="employee_express_receive.php">收快递</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>
