<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询模具状态
$sql_mould_status = "SELECT `mould_statusid`,`mould_statusname` FROM `db_mould_status` ORDER BY `mould_statusid` ASC";
$result_mould_status = $db->query($sql_mould_status);
if($_GET['submit']){
  $client_code = trim($_GET['client_code']);
  $mould_number = trim($_GET['mould_number']);
  $isexport = $_GET['isexport'];
  if($isexport != NULL){
    $sql_isexport = " AND `db_mould`.`isexport` = '$isexport'";
  }
  $quality_grade = $_GET['quality_grade'];
  if($quality_grade){
    $sql_quality_grade = " AND `db_mould`.`quality_grade` = '$quality_grade'";
  }
  $difficulty_degree = $_GET['difficulty_degree'];
  if($difficulty_degree){
    $sql_difficulty_degree = " AND `db_mould`.`difficulty_degree` = '$difficulty_degree'";
  }
  $mould_statusid = $_GET['mould_statusid'];
  if($mould_statusid){
    $sql_mould_statusid = " AND `db_mould`.`mould_statusid` = '$mould_statusid'";
  }
  $sqlwhere = " WHERE `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_client`.`client_code` LIKE '%$client_code%' $sql_isexport $sql_quality_grade $sql_difficulty_degree $sql_mould_statusid";
}
$sql = "SELECT *,`db_mould_data`.`upload_final_path` as image_filepath,`db_designer`.`employee_name` as designer,`db_projecter`.`employee_name` as projecter,`db_saler`.`employee_name` as saler,`db_assembler`.`employee_name` as assembler,`db_programming`.`employee_name` as programming FROM `db_mould_specification` INNER JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` LEFT JOIN `db_employee` AS `db_saler` ON `db_saler`.`employeeid`=`db_mould_specification`.`saler` LEFT JOIN `db_employee` AS `db_projecter` ON `db_projecter`.`employeeid` = `db_mould_specification`.`projecter` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould_specification`.`designer` LEFT JOIN `db_employee` AS `db_programming` ON `db_programming`.`employeeid` = `db_mould_specification`.`programming` LEFT JOIN `db_employee` AS `db_assembler` ON `db_mould_specification`.`assembler` = `db_assembler`.`employeeid` WHERE `db_mould_specification`.`is_approval` = '1' $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_specification`.`specification_time` DESC,`db_mould_specification`.`mould_id` DESC" . $pages->limitsql;

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
<title>项目管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>代码：</th>
        <td><input type="text" name="client_code" class="input_txt" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>是否出口：</th>
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
          </select></td>
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
        <th rowspan="2" width="3%">ID</th>
        <th rowspan="2" width="3%">代码</th>
        <th rowspan="2" width="4%">项目名称</th>
        <th rowspan="2" width="5%">模具编号</th>
        <th rowspan="2" width="6%">零件名称</th>
        <th rowspan="2" width="6%">零件图片</th>
        <th rowspan="2" width="6%">塑胶<br />
          材料</th>
        <th rowspan="2" width="4%">缩水率</th>
        <th rowspan="2" width="5%">表面<br />
          要求</th>
        <th rowspan="2" width="3%">模穴数</th>
        <th rowspan="2" width="6%">浇口<br />
          类型</th>
        <th rowspan="2" width="6%">型腔/型芯<br />
          材质</th>
        <th rowspan="2" width="3%">是否<br />
          出口</th>
        <th rowspan="2" width="3%">质量<br />
          等级</th>
        <th rowspan="2" width="3%">难度<br />
          系数</th>
        <th colspan="4">责任人</th>
        <th rowspan="2" width="5%">首板时间</th>
        <th rowspan="2" width="5%">重点提示</th>
        <th rowspan="2" width="4%">目前状态</th>
        <th rowspan="2" width="4%">操作</th>
      </tr>
      <tr>
        <th width="4%">项目</th>
        <th width="4%">设计</th>
        <th width="4%">钢料</th>
        <th width="4%">装配</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $image_filedir = $row['image_filedir'];
      $image_filename = $row['image_filename'];
      //$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
      $image_filepath = $row['image_filepath'];
      if(is_file($image_filepath)){
        $image_file = "<img src=\"".$image_filepath."\" width=\"85\" height=\"45\"/>";
      }else{
        $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
      }
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $mouldid; ?>"<?php if(in_array($mouldid,$array_mould_material)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['customer_code']; ?></td>
        <td><?php echo $row['project_name']; ?></td>
        <td><!-- <?php if($_SESSION['system_shell'][$system_dir]['isadmin']){ ?><a href="mouldae.php?id=<?php echo $mouldid; ?>&action=edit"><?php echo $row['mould_number']; ?></a><?php }else{ echo $row['mould_number']; }; ?> -->
          <?php echo $row['mould_no'] ?>
        </td>
        <td><?php echo $row['mould_name']; ?></td>
        <td><!-- <a href="mould_photo.php?id=<?php echo $row['mould_id']; ?>"> --><?php echo $image_file; ?><!-- </a> --></td>
        <td><?php echo $row['material_other']; ?></td>
        <td><?php echo $row['shrink']; ?></td>
        <td><?php echo $row['surface_require']; ?></td>
        <td><?php echo $row['cavity_num']; ?></td>
        <td><?php echo $array_injection_type[$row['injection_type']]; ?></td>
        <td><?php echo $row['core_material']; ?></td>
        <td><?php echo $row['is_export'] == '1'?'是':'否'; ?></td>
        <td><?php echo $array_quality_degree[$row['quality_degree']]; ?></td>
        <td><?php echo $array_difficulty_degree[$row['difficulty_degree']]; ?></td>
        <td><?php echo $row['projecter']; ?></td>
        <td><?php echo $row['designer']; ?></td>
        <td><?php echo $row['programming']; ?></td>
        <td><?php echo $row['assembler']; ?></td>
        <td><?php echo $row['checkbox_time']; ?></td>
        <td><?php ?></td>
        <td><?php echo $row['mould_statusname']; ?></td>
        <td><a href="mould_specification_edit.php?action=edit&from=summary&specification_id=<?php echo $row['mould_specification_id'] ?>">变更</a></td>
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