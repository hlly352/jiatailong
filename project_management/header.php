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
    <li class="menulevel"><a href="#">项目启动会</a>
      <ul>
        <li><a href="#">评审记录</a></li>
        <li><a href="#">DFM报告</a></li>
        <li><a href="#">进度规划</a></li>
        <li><a href="#">客户确认</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">模具试模</a>
      <ul>
        <li><a href="mould_try_applyae.php?action=add">试模申请</a></li>
        <li><a href="mould_try_approve_list.php">试模审批</a></li>
        <li><a href="mould_try_apply.php">试模记录</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="project_summary.php">模具修改</a></li>
    <li class="menulevel"><a href="#">模具交付</a>
      <ul>
        <li><a href="mould_try_applyae.php?action=add">出厂检查表</a></li>
        <li><a href="mould_try_approve_list.php">出货放行单</a></li>
        <li><a href="mould_try_apply.php">送货单</a></li>
        <li><a href="mould_try_apply.php">收货单</a></li>
        <li><a href="mould_try_apply.php">装箱装车照片</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">售后服务</a>
      <ul>
        <li><a href="mould_try_applyae.php?action=add">服务记录表</a></li>
        <li><a href="mould_try_approve_list.php">服务验收单</a></li>
      </ul>
    </li>
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
