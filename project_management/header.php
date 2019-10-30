<div id="header">
  <h4>项目管理</h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="new_project.php">新的项目</a></li>
    <li class="menulevel"><a href="project_summary.php">项目汇总</a></li>
    <!-- <li class="menulevel"><a href="mould.php">项目汇总(旧)</a></li> -->
    <li class="menulevel"><a href="technical_information.php">项目信息</a></li>
    <li class="menulevel"><a href="technical_info.php">技术资料</a></li>
    <li class="menulevel"><a href="project_start.php">项目启动会</a></li>
    <li class="menulevel"><a href="#">模具试模</a>
      <ul>
        <li><a href="mould_try_applyae.php?action=add">试模申请</a></li>
        <li><a href="mould_try_approve_list.php">试模审批</a></li>
        <li><a href="mould_try_apply.php">试模记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="mould_modify.php">模具修改</a></li>
    <li class="menulevel"><a href="delivery_service.php">交付及售后</a></li>
    <li class="menulevel"><a href="#">项目总结</a></li>
    <li class="menulevel"><a href="#">物料申请</a>
      <ul>
        <li><a href="mould_data_material.php">物料管理</a></li>
        <li><a href="mould_material_list.php">物料清单</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
