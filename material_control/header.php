<div id="header">
  <h4>物控管理 <em>V1.0 BY Hillion 2016.04.06</em></h4>
</div>
<script type="text/javascript">
$(function(){
  $('#menu ul li').unbind();
})
</script>
<style type="text/css">
  #menu ul li.menulevel ul{display:block;position: relative;}
  #menu ul li.menulevel ul li a{background: skyblue;font-weight:200; color:black;font-size:13px;}
</style>
<div id="menu">
  <ul>
    <!-- <li class="menulevel"><a href="/material_control/">首页</a></li> -->
    <li class="menulevel"><a href="#">待入库</a>
      <ul>
        <li><a href="material_in_list.php">模具物料</a></li>
        <li><a href="cutter_in_list.php">加工刀具</a></li>
        <li><a href="other_material_in_list.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">入库处理</a>
      <ul>
        <li><a href="material_godown_entry.php">模具物料</a></li>
        <li><a href="cutter_godown_entry.php">加工刀具</a></li>
        <li><a href="other_material_godown_entry.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">入库记录</a>
      <ul>
        <li><a href="material_inout_list_in.php">模具物料</a></li>
        <li><a href="cutter_inout_list_in.php">加工刀具</a></li>
        <li><a href="other_inout_list_in.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">待出库</a>
      <ul>
        <li><a href="material_out_list.php">模具物料</a></li>
        <li><a href="cutter_out_list.php">加工刀具</a></li>
        <li><a href="other_material_out_list.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">出库记录</a>
      <ul>
        <li><a href="material_inout_list_out.php">模具物料</a></li>
        <li><a href="cutter_inout_list_out.php">加工刀具</a></li>
        <li><a href="other_inout_list_out.php">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="#">异常入库</a>
      <ul>
        <li><a href="material_abnormal_in.php">模具物料</a></li>
        <li><a href="cutter_abnormal_in.php">加工刀具</a></li>
        <li><a href="#">期间物料</a></li>
      </ul>
    </li>
    <li class="menulevel"><a href="copper_material.php">铜料分析</a></li>
    <li class="menulevel"><a href="mould_cost.php">模具成本</a></li>
    <li class="menulevel"><a href="/myjtl/">内网首页</a></li>
  </ul>
  <span><?php echo $_SESSION['employee_info']['employee_name']; ?> <a href="../passport/logout.php">退出</a></span>
  <div class="clear"></div>
</div>
<div class="clear"></div>
