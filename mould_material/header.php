<?php
  //查询管理员信息
  $isconfirm = $_SESSION['system_shell'][$system_dir]['isconfirm'];
  $isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];
  ?>
<div id="header">
  <h4>物料申请</h4>
</div>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/mould_material/">帮助</a></li> -->
    <?php if($isadmin == 1){ ?>
      <li class="menulevel"><a href="mould_data.php">模具物料申请</a></li>
      <li class="menulevel"><a href="mould_material_list.php">模具物料汇总</a></li>
    <?php } ?>
    <li class="menulevel"><a href="mould_other_fee.php">期间物料申请</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>