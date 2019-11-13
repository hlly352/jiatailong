<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';

//查看当前用户是否是管理员
//获取当前页面的路径
$employeeid = $_SESSION['employee_info']['employeeid'];
  $system_url =  dirname(__FILE__);

  $system_pos =  strrpos($system_url,DIRECTORY_SEPARATOR);
  $system_url = substr($system_url,$system_pos);
  //通过路径查询对应的模块id
  $system_id_sql = "SELECT `systemid` FROM `db_system` WHERE `system_dir` LIKE '%$system_url%'";
  $system_id_res = $db->query($system_id_sql);
  $system_id = $system_id_res->fetch_row()[0];
  if($system_id ==' '){
    header('location:../myjtl/index.php');
  }
  //查询登录用户是否是客户管理的管理员
  $system_sql = "SELECT `isadmin`,`isconfirm` FROM `db_system_employee` WHERE `employeeid`='$employeeid' AND `systemid`=".$system_id;
  $system_res = $db->query($system_sql);

  $system_info = [];
  while($system = $system_res->fetch_row()){
    $system_info = $system;
  }
$isconfirm = $system_info[0];
$isadmin   = $system_info[1];
//查询模具状态
$sql_mould_status = "SELECT `mould_statusid`,`mould_statusname` FROM `db_mould_status` ORDER BY `mould_statusid` ASC";
$result_mould_status = $db->query($sql_mould_status);
if($_GET['submit']){
  $client_code = trim($_GET['client_code']);
  $mould_number = trim($_GET['mould_number']);
  $project_name = trim($_GET['project_name']);
  // $mould_statusid = $_GET['mould_statusid'];
  // if($mould_statusid){
  //   $sql_mould_statusid = " AND `db_mould`.`mould_statusid` = '$mould_statusid'";
  // }
  $sqlwhere = " AND `db_mould_specification`.`project_name` LIKE '%$project_name%' AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_specification`.`customer_code` LIKE '%$client_code%' $sql_isexport $sql_quality_grade $sql_difficulty_degree $sql_mould_statusid";
}
$sql = "SELECT *,`db_drawer`.`employee_name` AS `drawer_2d`,`db_design_group`.`employee_name` AS `design_group`,`db_projecter`.`employee_name` AS `projecter`,`db_designer`.`employee_name` AS `designer`,`db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`image_filepath`,`db_mould_specification`.`material_specification`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`material_other`,`db_mould_specification`.`mould_name`,`db_mould_data`.`upload_final_path` as image_filepaths FROM `db_mould_specification` LEFT JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` LEFT JOIN `db_employee` AS `db_projecter` ON `db_mould_specification`.`projecter` = `db_projecter`.`employeeid` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould_specification`.`designer` LEFT JOIN `db_design_plan` ON `db_mould_specification`.`mould_specification_id` = `db_design_plan`.`specification_id` LEFT JOIN `db_employee` AS `db_drawer` ON `db_drawer`.`employeeid` = `db_design_plan`.`drawer_2d` LEFT JOIN `db_employee` AS `db_design_group` ON `db_design_group`.`employeeid`= `db_design_plan`.`design_group` WHERE `db_mould_specification`.`is_approval` = '1' $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_specification`.`mould_id` DESC" . $pages->limitsql;

$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript">
  $(function(){
    //鼠标滑过
    $('.detail').css('color','blue').hover(function(){
        $(this).css('cursor','pointer');
        $(this).css('color','black');
      },function(){
        $(this).css('color','blue');
      //点击事件
      }).live('click',function(){
        var specification_id = $(this).children('input:hidden').val();
        window.open('mould_specification_edit.php?show=show&specification_id='+specification_id,'_self');
      })
    //点击图片
    $('.img').live('click',function(){
      //图片地址
      var img_file = $(this).html();
      var client_width = (window.screen.availWidth-600)/2;
      var client_height = (window.screen.availHeight-300)/2;
      var divs = '<div  id="divs" style="position:absolute;top:'+client_height+'px;left:'+client_width+'px">'+img_file+'</div>';
      $('#table_list').prepend(divs);
      $('#divs').children('img').css('width','600px');
      $('#divs').children('img').css('height','300px');
    })
      $(document).mouseup(function (e) {
        var con = $("#divs");   // 设置目标区域
        if (!con.is(e.target) && con.has(e.target).length === 0) {
            $('#divs').remove();
        }
    });
  $('#export').live('click',function(){
    var param = '';
   $('input[name ^= id]:checked').each(function(){
       param += 'designid[]='+$(this).val()+'&';
   } )
    param = param.substr(0,param.lastIndexOf('&'));
    window.location.href = 'excel_design_plan.php?'+param;
  })
})
</script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>设计计划</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>代码：</th>
        <td><input type="text" name="client_code" class="input_txt" /></td>
        <th>项目名称：</th>
        <td><input type="text" name="project_name" class="input_txt" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
       <!--  <th>是否出口：</th>
        <td><select name="isexport">
            <option value="">所有</option>
            <?php
            foreach($array_is_status as $is_status_key=>$is_status_value){
        echo "<option value=\"".$is_status_key."\">".$is_status_value."</option>";
      }
      ?>
          </select></td>
        <th>质量等级：</th>
        <td><select name="quality_grade">
            <option value="">所有</option>
            <?php
            foreach($array_mould_quality_grade as $quality_grade_key=>$quality_grade_value){
        echo "<option value=\"".$quality_grade_value."\">".$quality_grade_value."</option>";
      }
      ?>
          </select></td>
        <th>难度系数：</th>
        <td><select name="difficulty_degree">
            <option value="">所有</option>
            <?php
      for($i=0.5;$i<1.4;$i+=0.1){
        echo "<option value=\"".$i."\">".$i."</option>";
      }
      ?>
          </select></td>
        <th>目前状态：</th>
        <td><select name="mould_statusid">
            <option value="">所有</option>
            <?php
      if($result_mould_status->num_rows){
        while($row_mould_status = $result_mould_status->fetch_assoc()){
          echo "<option value=\"".$row_mould_status['mould_statusid']."\">".$row_mould_status['mould_statusname']."</option>";
        }
      }
      ?>
          </select></td> -->
        <td><input type="submit" name="submit" value="查询" class="button" />
          <!-- <input type="button" name="button" value="添加" class="button" onclick="location.href='mouldae.php?action=add'"<?php if(!$_SESSION['system_shell'][$system_dir]['isadmin']) echo " disabled=\"disabled\""; ?> />-->
          <input type="button" id="export" name="button" value="导出" class="button"/> 
        </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_id = $result_id->fetch_assoc()){
      $array_mouldid .= $row_id['mouldid'].',';
    }
    $array_mouldid = rtrim($array_mouldid,',');
    $sql_mould_material = "SELECT `mouldid` FROM `db_mould_material` WHERE `mouldid` IN ($array_mouldid) GROUP BY `mouldid`";
    $result_mould_material = $db->query($sql_mould_material);
    if($result_mould_material->num_rows){
      while($row_mould_material = $result_mould_material->fetch_assoc()){
        $array_mould_material[] = $row_mould_material['mouldid'];
      }
    }else{
      $array_mould_material = array();
    }
    //print_r($array_mould_material);
  ?>
  <form action="moulddo.php" name="mould_list" method="post">
    <table>
      <tr>
        <th>ID</th>
        <th width="1%">代码</th>
        <th width="2%">项目名称</th>
        <th width="2%">模具编号</th>
        <th width="2%">产品名称</th>
        <th width="2%">零件图片</th>
        <th width="2%">塑胶材料</th>
        <th width="2%">出模数</th>
        <th width="1%">缩水</th>
        <th width="2%">项目经理</th>
        <th width="2%">设计师</th>
        <th width="2%">2D绘图员</th>
        <th width="2%">设计组长</th>
        <th width="2%">优先等级</th>
        <th width="2%">模具最终<br />确认时间</th>
        <th width="2%">T0时间</th>
        <th>内容</th>
        <th>DFM</th>
        <th>方案定案会</th>
        <th>产品数据</th>
        <th>设计开始时间</th>
        <th>2D结构图</th>
        <th>3D-V1传图</th>
        <th>3D-V2传图</th>
        <th>客户确认<br />ok可定料<br />时间</th>
        <th>订购模仁</th>
        <th>订购热嘴</th>
        <th>精加工评审</th>
        <th>模仁NC开粗图</th>
        <th>订购模胚</th>
        <th>机加工图</th>
        <th>模仁NC精加工图</th>
        <th>订购散件料</th>
        <th>订购标准件</th>
        <th>模仁2D图</th>
        <th>其他散件图</th>
        <th>晒字图下发</th>
        <th>文件</th>
        <th>操作</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      //处理表面要求
      if(strpos($row['surface_require'],'$$')){
        $surface_require = explode('$$',$row['surface_require'])[4];
      }elseif(strpos($row['surface_require'],'//')){
        $surface_requires = substr($row['surface_require'],0,strlen($row['surface_require'])-2);
      }else{
        $surface_require = '';
      }

      //查找型芯和型腔的材质
      $cavity_sql = "SELECT `material_specification` FROM `db_mould_data` WHERE `mould_dataid`=".$row['mould_id'];
      $res = $db->query($cavity_sql);
      if($res->num_rows){
        $cavity = $res->fetch_row();
      }
      //处理是否出口
      if(strlen($row['is_export']) == 0){
        $export = '';
      }else{
        $export = $row['is_export'] == '1'?'是':'否';
      }
      //转换为数组
       $cavity = explode('$$',$cavity[0]);
      //图片处理
      $image_filedir = $row['image_filedir'];
      $image_filename = $row['image_filename'];
      //$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
      $image_filepath = empty($row['image_filepath'])?$row['image_filepaths']:$row['image_filepath'];
      
      if(is_file($image_filepath)){
        $image_file = "<img src=\"".$image_filepath."\" width=\"45\" height=\"25\"/>";
      }else{
        $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"45\" height=\"25\" />";
      }
      //查询模具状态
      $mould_status_sql = "SELECT `mould_statusname` FROM `db_mould_status` WHERE `mould_statusid`=".$row['mould_statusid'];
      $result_status = $db->query($mould_status_sql);
      if($result_status->num_rows){
        $mould_status = $result_status->fetch_assoc()['mould_statusname'];
      }
    ?>
      <tr>
        <td rowspan="2"><input type="checkbox" name="id[]" value="<?php echo $row['designid']; ?>"<?php if(!($row['designid'])) echo " disabled=\"disabled\""; ?> /></td>
        <td rowspan="2"><?php echo $row['customer_code']; ?></td>
        <td rowspan="2"><?php echo $row['project_name']; ?></td>
        <td rowspan="2" ><!-- <?php if($_SESSION['system_shell'][$system_dir]['isadmin']){ ?><a href="mouldae.php?id=<?php echo $mouldid; ?>&action=edit"><?php echo $row['mould_number']; ?></a><?php }else{ echo $row['mould_number']; }; ?> -->
          <?php echo $row['mould_no'] ?>
        </td>
        <td rowspan="2"><?php echo $row['mould_name']; ?></td>
        <td rowspan="2" class="img"><?php echo $image_file; ?></td>
        <td rowspan="2"><?php echo $row['material_other']; ?></td>
        <td rowspan="2"><?php echo $row['cavity_num']; ?></td>
        <td rowspan="2"><?php echo $row['shrink']; ?></td>
        <td rowspan="2"><?php echo $row['projecter']; ?></td>
        <td rowspan="2"><?php echo $row['designer']; ?></td>
        <td rowspan="2"><?php echo $row['drawer_2d']; ?></td>
        <td rowspan="2"><?php echo $row['design_group']; ?></td>
        <td rowspan="2"><?php echo $row['first_degree']; ?></td>
        <td rowspan="2"><?php echo substr($row['final_confirm'],5); ?></td>
        <td rowspan="2"><?php echo substr($row['t0_time'],5); ?></td>
        <td>计划</td>
        </td>
        <?php foreach($array_design_plan as $value){ 
          $plan_k = 'plan_'.$value;
          $real_k = 'real_'.$value;
         // echo $row[$plan_k];

         
        
        ?>
          <td <?php if($row[$plan_k] && !$row[$real_k]){
              $offset = ceil((strtotime($row[$plan_k]) - time())/(24*60*60));
              if($offset<=1 && $offset>=0){
                echo 'style="background:yellow"';
              }elseif($offset<0){
                echo 'style="background:red"';
              }}
              ?>>
            <?php 
               echo substr($row[$plan_k],5);
            ?>
          </td>
        <?php }?>
        <td rowspan="2">
            <?php
              if(!empty($row['design_plan_path'])){
                echo '<a href="design_plan_show.php?action=show&designid='.$row['designid'].'"><img src="../images/system_ico/info_8_10.png" width="12"></a>';
              }
            ?>
        </td>
        <td rowspan="2"><a href="<?php echo 'design_plan_edit.php?action=add&specification_id='.$row['mould_specification_id'].'&designid='.$row['designid']; ?>">更新</a></td>
      </tr>
      <tr>
        <td>实际</td>
         <?php foreach($array_design_plan as $value){ ?>
          <td>
            <?php $k='real_'.$value; echo substr($row[$k],5); ?>
          </td>
        <?php }?>
      </tr>
      <?php } ?>
    </table>
   <!--  <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div> -->
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>