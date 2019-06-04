<div id="header">
  <h4>订单管理</h4>
</div>
<div id="menu">
  <ul>
     <li class="menulevel"><a href="order_task.php">临时订单</a></li>
     <li class="menulevel"><a href="order_approval.php">订单审核</a></li>
     <li class="menulevel"><a href="order_gather.php">订单汇总</a></li>
     <li class="menulevel"><a href="order_pay.php">收款管理</a></li>
     <li class="menulevel"><a href="order_bill.php">发票管理</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>  