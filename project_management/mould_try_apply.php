<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$approve_status = $_GET['approve_status'];
	if($approve_status){
		$sql_approve_status = " AND `db_mould_try`.`approve_status` = '$approve_status'";
	}
	$try_status = $_GET['try_status'];
	if($try_status != NULL){
		$sql_try_status = " AND `db_mould_try`.`try_status` = '$try_status'";
	}
	$sqlwhere = " WHERE `db_mould`.`mould_number` LIKE '%$mould_number%' $sql_approve_status $sql_try_status";
}
$sql = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`mould_size`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`plan_date`,`db_mould_try`.`employeeid`,`db_mould_try`.`approve_status`,`db_mould_try`.`try_status`,`db_mould`.`mould_number`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_mould_try_cause`.`try_causename`,`db_applyer`.`employee_name` AS `applyer_name` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_mould_try`.`employeeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_try`.`tryid` DESC" . $pages->limitsql;
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
<title>项目管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>试模记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>审批：</th>
        <td><select name="approve_status">
            <option value="">所有</option>
            <?php
			foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
				echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="try_status">
            <option value="">所有</option>
            <?php
			foreach($array_status as $status_key=>$status_value){
				echo "<option value=\"".$status_key."\">".$status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="试模申请" class="button" onclick="location.href='mould_try_applyae.php?action=add'" />
          <input type="text" style="display:none" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="mould_try_applydo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="5%">模具编号</th>
        <th width="7%">模具尺寸</th>
        <th width="5%">穴数</th>
        <th width="3%">难度<br />
          系数</th>
        <th width="3%">试模<br />
          次数</th>
        <th width="5%">试模<br />
          原因</th>
        <th width="4%">啤机<br />
          吨位(T)</th>
        <th width="4%">成型<br />
          周期(S)</th>
        <th width="10%">胶料<br />
          品种</th>
        <th width="4%">胶料<br />
          颜色</th>
        <th width="4%">胶料<br />
          来源</th>
        <th width="4%">产品<br />
          单重(g)</th>
        <th width="4%">样板<br />
          啤数(啤)</th>
        <th width="4%">需要<br />
          用料(Kg)</th>
        <th width="4%">钳工<br />组别</th>
        <th width="6%">要求日期</th>
        <th width="4%">申请人</th>
        <th width="4%">审批</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $tryid = $row['tryid'];
		  $approve_status = $row['approve_status'];
		  $try_status = $row['try_status'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $tryid; ?>"<?php if($employeeid != $row['employeeid'] || $approve_status != 'C') echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['mould_size']; ?></td>
        <td><?php echo $row['cavity_number']; ?></td>
        <td><?php echo $row['difficulty_degree']; ?></td>
        <td><?php echo $row['try_times']; ?></td>
        <td><?php echo $row['try_causename']; ?></td>
        <td><?php echo $row['tonnage']; ?></td>
        <td><?php echo $row['molding_cycle']; ?></td>
        <td><?php echo $row['plastic_material']; ?></td>
        <td><?php echo $row['plastic_material_color']; ?></td>
        <td><?php echo $row['plastic_material_offer']; ?></td>
        <td><?php echo $row['product_weight']; ?></td>
        <td><?php echo $row['product_quantity']; ?></td>
        <td><?php echo $row['material_weight']; ?></td>
        <td><?php echo $array_mould_assembler[$row['assembler']]; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php echo $row['applyer_name']; ?></td>
        <td><?php echo $array_office_approve_status[$approve_status]; ?></td>
        <td><?php echo $array_status[$row['try_status']]; ?></td>
        <td><?php if($employeeid == $row['employeeid'] && $approve_status == 'C' && $try_status == 1){ ?>
          <a href="mould_try_applyae.php?id=<?php echo $tryid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><a href="mould_try_info.php?id=<?php echo $tryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
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