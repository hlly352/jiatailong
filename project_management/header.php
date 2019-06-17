<div id="header">
  <h4>项目管理</h4>
</div>
<div id="menu">
  <ul>
    <li class="menulevel"><a href="new_project.php">新的项目</a></li>
    <li class="menulevel"><a href="project_summary.php">项目汇总(新)</a></li>
    <li class="menulevel"><a href="mould.php">项目汇总(旧)</a></li>
    <li class="menulevel"><a href="#">项目评审</a></li>
    <li class="menulevel"><a href="#">项目进度</a></li>
    <li class="menulevel"><a href="#">模具试模</a>
      <ul>
        <li><a href="mould_try_applyae.php?action=add">试模申请</a></li>
        <li><a href="mould_try_approve_list.php">试模审批</a></li>
        <li><a href="mould_try_apply.php">试模记录</a></li>
      </ul>
    </li>
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
