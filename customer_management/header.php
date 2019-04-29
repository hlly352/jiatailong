<div id="header">
  <h4>客户管理 <em>V1.0 BY Hillion 2017.07.17</em></h4>
</div>
<div id="menu">
  <ul>
     <li class="menulevel"><a href="customer_add.php?action=add">新建客户</a></li>
          <li class="menulevel"><a href="customer_status.php">跟进客户 </a></li>
     <li class="menulevel"><a href="customer_index.php">客户列表</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">登出</a></span>
  <div class="clear"></div>
</div>  