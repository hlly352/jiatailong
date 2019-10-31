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
  $isexport = $_GET['isexport'];
  if($isexport != NULL){
    $sql_isexport = " AND `db_mould_specification`.`is_export` = '$isexport'";
  }
  $quality_grade = $_GET['quality_grade'];
  if($quality_grade){
    $sql_quality_grade = " AND `db_mould`.`quality_grade` = '$quality_grade'";
  }
  $difficulty_degree = $_GET['difficulty_degree'];
  if($difficulty_degree){
    $sql_difficulty_degree = " AND `db_mould`.`difficulty_degree` = '$difficulty_degree'";
  }
  // $mould_statusid = $_GET['mould_statusid'];
  // if($mould_statusid){
  //   $sql_mould_statusid = " AND `db_mould`.`mould_statusid` = '$mould_statusid'";
  // }
  $sqlwhere = " AND `db_mould_specification`.`project_name` LIKE '%$project_name%' AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_specification`.`customer_code` LIKE '%$client_code%' $sql_isexport $sql_quality_grade $sql_difficulty_degree $sql_mould_statusid";
}
$sql = "SELECT *,`db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`image_filepath`,`db_mould_specification`.`material_specification`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`material_other`,`db_mould_specification`.`mould_name`,`db_mould_data`.`upload_final_path` as image_filepaths FROM `db_mould_specification` LEFT JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` WHERE  `db_mould_specification`.`is_approval` = '1' $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould'] = $sql;
$pages = new page($result->num_rows,20);
$sqllist = $sql . " ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_specification`.`mould_id` DESC" . $pages->limitsql;

$result = $db->query($sqllist);
//显示有资料的信息
function showData($str,$rows){
   if($rows[$str]){      
         $modify_id = $rows['modify_id'];  
         return  '<a href="mould_modify_show.php?data='.$str.'&action=show&modify_id='.$modify_id.'">           <img src="../images/system_ico/article_12_16.png" />        </a>';
       }
}

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
    var show = true;
    $('.img').live('click',function(){
      if(show == true){
        //图片地址
        var img_file = $(this).html();
        var client_width = (window.screen.availWidth-600)/2;
        var client_height = (window.screen.availHeight-300)/2;
        var divs = '<div  id="divs" style="position:absolute;top:'+client_height+'px;left:'+client_width+'px">'+img_file+'</div>';
        $('#table_list').prepend(divs);
        $('#divs').children('img').css('width','600px');
        $('#divs').children('img').css('height','300px');
        show = false;
      }else if(show == false){
        $('#divs').remove();
        show = true;
      }
      })
        $(document).mouseup(function (e) {
          var con = $("#divs");   // 设置目标区域
          if (!con.is(e.target) && con.has(e.target).length === 0) {
              $('#divs').remove();
          }

    });
  function showData(str,rows){
    var index = str;
   if(rows[index]){      
         var modify_id = rows.modify_id;  
         return '<a href="mould_modify_show.php?data='+str+'&action=show&modify_id='+modify_id+'">           <img src="../images/system_ico/article_12_16.png" />        </a>';
       }else{
         return '';
      }
    }
  //点击查看其它版本的改模资料
  display = true;
  $('.version').live('click',function(){
    var id = $(this).attr('id');
    var offset = $(this).parent().parent().next();
    var specificationid = id.substr(id.lastIndexOf('_')+1);
    if(display == true){
    $.post('../ajax_function/get_mould_modify_version.php',{specificationid:specificationid},function(data){
      for(var i=0;i<data.length;i++){
        var rows = data[i];
        var modify_id = rows.modify_id;
        var trs = ' <tr class="tr_'+specificationid+'"><td colspan="6"><td>'+'T'+rows.t_number+'</td></td>        <td>'+showData("last_report",rows)+'</td> <td>'+showData("customer_data",rows)+'</td>       <td>'+showData('modify_data',rows)+'</td>        <td>'+showData('modify_plan',rows)+'</td>        <td>'+showData('drawing_connection',rows)+'</td>        <td>'+showData('before_check',rows)+'</td>        <td>'+showData('try_apply',rows)+'</td>        <td>'+showData('dan_photo',rows)+'</td>        <td>'+showData('sample_photo',rows)+'</td>        <td>'+showData('try_report',rows)+'</td>        <td>'+showData('sample_check',rows)+'</td>        <td>'+showData('sample_delivery',rows)+'</td>          <td></td>   </tr>';
        offset.before(trs);

      }
    },'json')
    display = false;
    $(this).html('收起');
  }else if(display == false){
    display = true;
    $('.tr_'+specificationid).remove();
    $(this).html('查看');

  }
  })

})
</script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4></h4>
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
          <!-- <input type="button" name="button" value="添加" class="button" onclick="location.href='mouldae.php?action=add'"<?php if(!$_SESSION['system_shell'][$system_dir]['isadmin']) echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_mould.php'" /> -->
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
        <th width="3%">ID</th>
        <th width="3%">代码</th>
        <th width="4%">项目名称</th>
        <th width="5%" >模具编号</th>
        <th width="6%">零件名称</th>
        <th width="6%">零件图片</th>
        <th>试模次数</th>
        <th>上次试模报告</th>
        <th>客户改模资料</th>
        <th>内部改模资料</th>
        <th>改模计划</th>
        <th>图纸联络单</th>
        <th>改模前检查表</th>
        <th>试模申请</th>
        <th>机上红丹照片</th>
        <th>样品图片</th>
        <th>试模报告</th>
        <th>样品检测报告</th>
        <th>样品交付</th>
        <th>历史资料</th>
        <th>操作</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
        $specification_id = $row['mould_specification_id'];
        $rows = array();
      //通过模具id查询模具对应的改模资料
      $sql_max_modify = "SELECT * FROM `db_mould_modify` WHERE `t_number` = (SELECT MAX(`t_number`) FROM `db_mould_modify` WHERE `specification_id` = '$specification_id') AND `specification_id` = '$specification_id'";
      $result_max_modify = $db->query($sql_max_modify);
      if($result_max_modify->num_rows){
        $rows = $result_max_modify->fetch_assoc();
      }
      $specificationid = $rows['specification_id'];
      //查询当前模具下的其它版本数
      $sql_modify_count = "SELECT COUNT(*) AS `count` FROM `db_mould_modify` WHERE `specification_id` = '$specificationid'";
      $result_modify_count = $db->query($sql_modify_count);
      if($result_modify_count->num_rows){
        $modify_count = $result_modify_count->fetch_assoc()['count'];
      }

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
        $image_file = "<img src=\"".$image_filepath."\" width=\"85\" height=\"45\"/>";
      }else{
        $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
      }
      //查询模具状态
      $mould_status_sql = "SELECT `mould_statusname` FROM `db_mould_status` WHERE `mould_statusid`=".$row['mould_statusid'];
      $result_status = $db->query($mould_status_sql);
      if($result_status->num_rows){
        $mould_status = $result_status->fetch_assoc()['mould_statusname'];
      }
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $mouldid; ?>"<?php if(in_array($mouldid,$array_mould_material)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['customer_code']; ?></td>
        <td><?php echo $row['project_name']; ?></td>
        <td ><!-- <?php if($_SESSION['system_shell'][$system_dir]['isadmin']){ ?><a href="mouldae.php?id=<?php echo $mouldid; ?>&action=edit"><?php echo $row['mould_number']; ?></a><?php }else{ echo $row['mould_number']; }; ?> -->
          <?php echo $row['mould_no'] ?>
        </td>
        <td><?php echo $row['mould_name']; ?></td>
        <td class="img"><?php echo $image_file; ?></td>
        <td><?php echo $rows['t_number']?'T'.$rows['t_number']:''; ?></td>
        <td><?php echo showData('last_report',$rows) ?></td>
        <td><?php echo showData('customer_data',$rows); ?></td>
        <td><?php echo showData('modify_data',$rows); ?></td>
        <td><?php echo showData('modify_plan',$rows); ?></td>
        <td><?php echo showData('drawing_connection',$rows); ?></td>
        <td><?php echo showData('before_check',$rows); ?></td>
        <td><?php echo showData('try_apply',$rows); ?></td>
        <td><?php echo showData('dan_photo',$rows);?></td>
        <td><?php echo showData('sample_photo',$rows);?></td>
        <td><?php echo showData('try_report',$rows); ?></td>
        <td><?php echo showData('sample_check',$rows); ?></td>
        <td><?php echo showData('sample_delivery',$rows);  ?></td>
        <td><?php echo $modify_count>1?'<a href="" onclick="return false" class="version" id="version_'.$specificationid.'">查看</a>':'查看'; ?></td>
        <td><a href="<?php echo $system_info[0] == '1'?'mould_modify_edit.php?action=add&from=technology&specification_id='.$row['mould_specification_id'].'&mouldid='.$row['mould_dataid']:'#' ?>">更新</a></td>
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