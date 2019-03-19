<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$employee_name = $_GET['employee_name'];
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_employee`.`employee_name` LIKE '%$employee_name%'";
}
$sql = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`mould_size`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`plan_date`,`db_mould_try`.`approve_status`,`db_mould_try`.`try_status`,`db_mould_try`.`supplierid`,`db_mould`.`mould_number`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_employee`.`employee_name` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_try`.`employeeid` WHERE `db_mould_try`.`approve_status` = 'B' AND `db_mould_try`.`try_status` = 1 AND `db_mould_try`.`finish_status` = 0 $sqlwhere";
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
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>试模确认</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="8%">模具尺寸</th>
        <th width="6%">穴数</th>
        <th width="4%">难度<br />
          系数</th>
        <th width="4%">试模<br />
          次数</th>
        <th width="4%">啤机<br />
          吨位(T)</th>
        <th width="4%">成型<br />
          周期(S)</th>
        <th width="15%">胶料<br />
          品种</th>
        <th width="4%">胶料<br />
          颜色</th>
        <th width="4%">胶料<br />
          来源</th>
        <th width="5%">产品<br />
          单重(g)</th>
        <th width="5%">样板<br />
          啤数(啤)</th>
        <th width="5%">需要<br />
          用料(Kg)</th>
        <th width="6%">钳工组别</th>
        <th width="6%">要求时间</th>
        <th width="6%">申请人</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $tryid = $row['tryid'];
	  ?>
      <tr>
        <td><?php echo $tryid; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['mould_size']; ?></td>
        <td><?php echo $row['cavity_number']; ?></td>
        <td><?php echo $row['difficulty_degree']; ?></td>
        <td><?php echo $row['try_times']; ?></td>
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
        <td><?php echo $row['employee_name']; ?></td>
        <td><a href="mould_try_finish.php?id=<?php echo $tryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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