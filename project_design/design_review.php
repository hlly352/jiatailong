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
//查找模具基本信息
$sql = "SELECT *,`db_design_review`.`reviewid`,`db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`image_filepath`,`db_mould_specification`.`material_specification`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`material_other`,`db_mould_specification`.`mould_name`,`db_mould_data`.`upload_final_path` as image_filepaths FROM `db_mould_specification` LEFT JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` LEFT JOIN `db_design_review` ON `db_mould_specification`.`mould_specification_id` = `db_design_review`.`specification_id` WHERE `db_mould_specification`.`is_approval` = '1' $sqlwhere";
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
       param += 'mouldid[]='+$(this).val()+'&';
   } )
    param = param.substr(0,param.lastIndexOf('&')-1);
    window.location.href = 'excel_design_plan.php?'+param;
  })
})
</script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具设计评审记录表</h4>
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
          <input type="button" id="export" name="button" value="导出" class="button"/> -->
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
    //列举所有的检查项目
    $sql_data = "SELECT `db_mould_check_type`.`typename`,`db_mould_check_type`.`id`,COUNT(`db_mould_check_data`.`id`) AS `count` FROM `db_mould_check_type` INNER JOIN `db_mould_check_data` ON `db_mould_check_type`.`id` = `db_mould_check_data`.`categoryid` WHERE `db_mould_check_type`.`pid` = '0' GROUP BY `db_mould_check_data`.`categoryid`";
    $result_data = $db->query($sql_data);
    //评审会项目数目
    $sql_review_meeting = "SELECT COUNT(*) AS `meeting_number` FROM `db_mould_check_data` WHERE `degree` = 'B'";
    $result_meeting = $db->query($sql_review_meeting);
    $count_meeting = 0;
    if($result_meeting->num_rows){
      $count_meeting = $result_meeting->fetch_assoc()['meeting_number'];
    }    
  ?>
  <form action="moulddo.php" name="mould_list" method="post">
    <table>
      <tr>
        <th>ID</th>
        <th>代码</th>
        <!-- <th>文件编号</th> -->
        <th>项目名称</th>
        <th>模具编号</th>
        <th>产品名称</th>
        <th>零件图片</th>
        <th>模具穴数</th>
        <?php
          if($result_data->num_rows){
            while($row_data = $result_data->fetch_assoc()){
        ?>
        <th><?php echo $row_data['typename'] ?></th>
        <?php }} ?>
        <th>评审会项目</th>
        <th>未通过项目</th>
        <th>Add</th>
        <th>查看</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
        $specification_id = $row['mould_specification_id'];
        $reviewid = $row['reviewid'];
      //图片处理
      $image_filepath = empty($row['image_filepath'])?$row['image_filepaths']:$row['image_filepath'];
      
      if(is_file($image_filepath)){
        $image_file = "<img src=\"".$image_filepath."\" width=\"85\" height=\"45\"/>";
      }else{
        $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
      }
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['mould_specification_id']; ?>"<?php if(in_array($mouldid,$array_mould_material)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['customer_code']; ?></td>
        <!-- <td><?php echo $row['document_no']; ?></td> -->
        <td><?php echo $row['project_name']; ?></td>
        <td ><!-- <?php if($_SESSION['system_shell'][$system_dir]['isadmin']){ ?><a href="mouldae.php?id=<?php echo $mouldid; ?>&action=edit"><?php echo $row['mould_number']; ?></a><?php }else{ echo $row['mould_number']; }; ?> -->
          <?php echo $row['mould_no'] ?>
        </td>
        <td><?php echo $row['mould_name']; ?></td>
        <td class="img"><?php echo $image_file; ?></td>
        <td><?php echo $row['cavity_num']; ?></td>
         <?php
          $result_data = $db->query($sql_data);
          if($result_data->num_rows && $reviewid){
            while($row_data = $result_data->fetch_assoc()){
              $categoryid = $row_data['id'];
              //查找已经审批的项目数目
              $sql_complete = "SELECT COUNT(`db_design_review_list`.`reviewid`) AS `complete`,`db_mould_check_data`.`categoryid` FROM `db_design_review_list` INNER JOIN `db_design_review` ON `db_design_review`.`reviewid` = `db_design_review_list`.`reviewid` INNER JOIN `db_mould_check_data` ON `db_design_review_list`.`dataid` = `db_mould_check_data`.`id` WHERE `db_design_review`.`specification_id` = '$specification_id' AND `db_design_review`.`reviewid` = '$reviewid' AND `db_mould_check_data`.`categoryid` = '$categoryid' GROUP BY `db_mould_check_data`.`categoryid`";
              $result_complete = $db->query($sql_complete);
              $complete_data = 0;
              if($result_complete->num_rows){
                $complete_data = $result_complete->fetch_assoc()['complete'];
              }
        ?>
        <td>
          <a href="design_review_info.php?action=edit&specification_id=<?php echo $specification_id; ?>&reviewid=<?php echo $reviewid ?>&categoryid=<?php echo $categoryid; ?>" <?php if($complete_data == $row_data['count']) echo 'style="color:green"' ?>>

            <?php echo $complete_data.'/'.$row_data['count'] ?>  
          </a>
        </td>
        <?php 
          }
        }else{
            while($row_data = $result_data->fetch_assoc()){
              echo '<td></td>';
            }
        } 
        ?>
        <td>
          <?php
            //评审会通过项目
            $sql_meeting_complete = "SELECT COUNT(`db_design_review_list`.`listid`) AS `count_meeting_complete` FROM `db_design_review_list` INNER JOIN `db_mould_check_data` ON `db_design_review_list`.`dataid` = `db_mould_check_data`.`id` WHERE `db_mould_check_data`.`degree` = 'B' AND `db_design_review_list`.`reviewid` = '$reviewid' GROUP BY `db_design_review_list`.`reviewid`";
            $result_meeting_complete = $db->query($sql_meeting_complete);
            $count_meeting_complete = 0;
            if($result_meeting_complete->num_rows){
              $count_meeting_complete = $result_meeting_complete->fetch_assoc()['count_meeting_complete'];
            }
            if($reviewid){
               echo '<a href="design_review_info.php?action=edit&specification_id='.$specification_id.'&reviewid='.$reviewid.'">'. $count_meeting_complete.'/'.$count_meeting.'</a>';
            }
          ?>        
        </td>
        <td>
          <?php
            if($reviewid){
              //未通过项目数目
               $sql_eng_count = "SELECT COUNT(`reviewid`) AS `eng` FROM `db_design_review_list` WHERE `reviewid` = '$reviewid' AND `approval` = '0'";
               $result_eng_count = $db->query($sql_eng_count);
               if($result_eng_count->num_rows){
                  $eng_count = $result_eng_count->fetch_assoc()['eng'];
               }else{
                  $eng_count = 0;
               }
              echo '<a href="design_review_info.php?action=edit&categoryid=eng&specification_id='.$specification_id.'&reviewid='.$reviewid.'">'.$eng_count.'</a>';
            }
          ?>
        </td>
        <td>
          <?php if(!$reviewid){ ?>
            <a href="<?php echo 'design_review_edit.php?action=add&specification_id='.$row['mould_specification_id']; ?>">
              <img src="../images/system_ico/edit_10_10.png" width="10">
            </a>
          <?php } ?>
        </td>
        <td>
          <?php if($reviewid){ ?>
          <a href="<?php echo 'design_review_edit.php?action=edit&specification_id='.$row['mould_specification_id'].'&reviewid='.$row['reviewid']; ?>">
            <img src="../images/system_ico/info_8_10.png" width="10" />
          </a>
          <?php } ?>
        </td>
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