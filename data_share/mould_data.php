<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
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
$sql = "SELECT `db_mould`.`mouldid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_mould`.`part_name`,`db_mould`.`plastic_material`,`db_mould`.`shrinkage_rate`,`db_mould`.`surface`,`db_mould`.`cavity_number`,`db_mould`.`gate_type`,`db_mould`.`core_material`,`db_mould`.`isexport`,`db_mould`.`quality_grade`,`db_mould`.`difficulty_degree`,`db_mould`.`first_time`,`db_mould`.`remark`,`db_mould`.`assembler`,`db_mould`.`image_filedir`,`image_filename`,`db_client`.`client_code`,`db_mould_status`.`mould_statusname`,`db_projecter`.`employee_name` AS `projecter_name`,`db_designer`.`employee_name` AS `designer_name`,`db_steeler`.`employee_name` AS `steeler_name`,`db_electroder`.`employee_name` AS `electroder_name` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` LEFT JOIN `db_employee` AS `db_projecter` ON `db_projecter`.`employeeid` = `db_mould`.`projecter` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould`.`designer` LEFT JOIN `db_employee` AS `db_steeler` ON `db_steeler`.`employeeid` = `db_mould`.`steeler` LEFT JOIN `db_employee` AS `db_electroder` ON `db_electroder`.`employeeid` = `db_mould`.`electroder` $sqlwhere";
$result = $db->query($sql);
$_SESSION['mould'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould`.`mould_number` DESC,`db_mould`.`mouldid` DESC" . $pages->limitsql;
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
<title>模具数据-希尔林</title>
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_mould.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th rowspan="2" width="3%">ID</th>
        <th rowspan="2" width="3%">代码</th>
        <th rowspan="2" width="4%">项目名称</th>
        <th rowspan="2" width="5%">模具编号</th>
        <th rowspan="2" width="6%">零件名称/<br />
          编号</th>
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
        <th colspan="5">责任人</th>
        <th rowspan="2" width="5%">首板时间</th>
        <th rowspan="2" width="5%">重点提示</th>
        <th rowspan="2" width="4%">目前状态</th>
      </tr>
      <tr>
        <th width="4%">项目</th>
        <th width="4%">设计</th>
        <th width="4%">钢料</th>
        <th width="4%">电极</th>
        <th width="4%">装配</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $mouldid = $row['mouldid'];
		  $image_filedir = $row['image_filedir'];
		  $image_filename = $row['image_filename'];
		  $image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
		  if(is_file($image_filepath)){
			  $image_file = "<img src=\"".$image_filepath."\" />";
		  }else{
			  $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
		  }
	  ?>
      <tr>
        <td><?php echo $mouldid; ?></td>
        <td><?php echo $row['client_code']; ?></td>
        <td><?php echo $row['project_name']; ?></td>
        <td><a href="mould_info.php?id=<?php echo $mouldid; ?>"><?php echo $row['mould_number']; ?></a></td>
        <td><?php echo $row['part_name']; ?></td>
        <td><?php echo $image_file; ?></td>
        <td><?php echo $row['plastic_material']; ?></td>
        <td><?php echo $row['shrinkage_rate']; ?></td>
        <td><?php echo $row['surface']; ?></td>
        <td><?php echo $row['cavity_number']; ?></td>
        <td><?php echo $row['gate_type']; ?></td>
        <td><?php echo $row['core_material']; ?></td>
        <td><?php echo $array_is_status[$row['isexport']]; ?></td>
        <td><?php echo $row['quality_grade']; ?></td>
        <td><?php echo $row['difficulty_degree']; ?></td>
        <td><?php echo $row['projecter_name']; ?></td>
        <td><?php echo $row['designer_name']; ?></td>
        <td><?php echo $row['steeler_name']; ?></td>
        <td><?php echo $row['electroder_name']; ?></td>
        <td><?php echo $array_mould_assembler[$row['assembler']]; ?></td>
        <td><?php echo $row['first_time']; ?></td>
        <td><?php echo $row['remark']; ?></td>
        <td><?php echo $row['mould_statusname']; ?></td>
      </tr>
      <?php } ?>
    </table>
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