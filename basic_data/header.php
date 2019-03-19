<div id="header">
  <h4>基础数据 <em>V1.0 BY Hillion 2016.03.20</em></h4>
</div>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/basic_data/">首页</a></li> -->
    <li class="menulevel"><a href="mould.php">模具数据</a>
      <ul>
        <li><a href="mould_status.php">模具状态</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">刀具数据</a>
      <ul>
        <li><a href="cutter_specification.php">刀具规格</a></li>
        <li><a href="cutter_type.php">刀具类型</a></li>
        <li><a href="cutter_hardness.php">刀具硬度</a></li>
        <li><a href="cutter_brand.php">刀具品牌</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="material_type.php">物料类型</a></li>
    <li class="menulevel"><a href="#">加工类型</a>
      <ul>
        <li><a href="mould_outward_type.php">外发类型</a></li>
        <li><a href="mould_weld_type.php">烧焊类型</a></li>
        <li><a href="mould_workteam.php">申请组别</a></li>
        <li><a href="mould_responsibility_team.php">责任组别</a></li>
        <li><a href="mould_try_cause.php">试模原因</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="notice_type.php">公告类型</a></li>
    <li class="menulevel"><a href="client.php">客户</a></li>
    <li class="menulevel"><a href="supplier.php">供应商</a>
      <ul>
        <li><a href="supplier_type.php">供应商类型</a></li>
        <li><a href="supplier_business_type.php">供应商业务</a></li>
        <li><a href="express_inc.php">快递公司</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="unit.php">计量单位</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
