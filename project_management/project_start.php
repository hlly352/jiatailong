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
$sql = "SELECT *,`db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`image_filepath`,`db_mould_specification`.`material_specification`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`material_other`,`db_mould_specification`.`mould_name`,`db_mould_data`.`upload_final_path` as image_filepaths FROM `db_mould_specification` LEFT JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` LEFT JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_mould_specification`.`is_approval` = '1' $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_specification`.`mould_id` DESC" . $pages->limitsql;
$result = $db->query($sqllist);

//获取地址每个资料的地址信息
function show($row,$from){
          $title_key = $from.'_title';
          $count = substr_count($row[$from],'&');

  $data = explode('&',$row[$title_key]);
  $new_data = array();
  foreach($data as $ks=>$vs){
    if(!empty($vs)){
      $new_data[$ks] = $vs;
    }
  }
              foreach($new_data as $k=>$v){
              if($k<3){
                if (preg_match('/[\x{4e00}-\x{9fa5}]+/u',$v)) {
                  $num = 20;
                } else {
                  $num = 10;
                }
                $title .= substr($v,0,$num).'<br>';
              }
          }
          // if($count >0){
            $str = '<a href="technical_data_list.php?action=show&data='.$from.'&informationid='.$row['information_id'].'">'.$title.'</a>';
          // }else{
          //   $str = '<a href="http://'.$_SERVER['HTTP_HOST'].substr($row[$from],2).'">'.$title.'</a>';
          // }
         return $str;
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
<script language="javascript" type="text/javascript" src="../js/enlarge_img.js"></script>
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>项目启动会</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>代码：</th>
        <td><input type="text" name="client_code" class="input_txt" /></td>
        <th>项目名称：</th>
        <td><input type="text" name="project_name" class="input_txt" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
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
        <th>代码</th>
        <th>项目名称</th>
        <th>模具编号</th>
        <th>零件名称</th>
        <th>零件图片</th>
        <th>评审记录</th>
        <th>DFM报告</th>
        <th>进度规划</th>
        <th>客户方案确认</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
        $specification_id = $row['mould_specification_id'];

      //图片处理
      $image_filepath = empty($row['image_filepath'])?$row['image_filepaths']:$row['image_filepath'];
      if(is_file($image_filepath)){
        $image_file = "<img src=\"".$image_filepath."\" width=\"85\" height=\"45\"/>";
      }else{
        $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
      }
      //查找是否有项目评审内容
      $sql_review = "SELECT * FROM `db_project_review_list` WHERE `specification_id` = '$specification_id'";
      $result_review = $db->query($sql_review);
      if($result_review->num_rows){
        $src = "../images/system_ico/info_8_10.png";
      }else{
        $src = "../images/system_ico/edit_10_10.png";
      }
      //查找是否有项目计划
      $sql_plan = "SELECT * FROM `db_project_plan` WHERE `specification_id` = '$specification_id'";
      $result_plan = $db->query($sql_plan);
      if($result_plan->num_rows){
        $progress = '../images/system_ico/info_8_10.png';
        $todo = 'edit';
        $project_planid = $result_plan->fetch_assoc()['project_planid'];
      }else{
        $progress = '../images/system_ico/edit_10_10.png';
        $todo = 'add';
      }
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $mouldid; ?>"<?php if(in_array($mouldid,$array_mould_material)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['customer_code']; ?></td>
        <td><?php echo $row['project_name']; ?></td>
        <td ><?php echo $row['mould_no'] ?></td>
        <td><?php echo $row['mould_name']; ?></td>
        <td class="img"><?php echo $image_file; ?></td>
        <td>
          <a href="project_review.php?action=add&specification_id=<?php echo $specification_id; ?>">
            <img src="<?php echo $src; ?>" width="15">
          </a>
        </td>
          <?php echo show_detail($row,'dfm_report',$specification_id,'project_start',$array_project_data); ?>
        <td>
          <a href="project_plan_edit.php?action=<?php echo $todo; ?>&specification_id=<?php echo $specification_id; ?>&project_planid=<?php echo $project_planid ?>">
            <img src="<?php echo $progress; ?>" width="15">
          </a>
        </td>
          <?php echo show_detail($row,'customer_confirm',$specification_id,'project_start',$array_project_data); ?>
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