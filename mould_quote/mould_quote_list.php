<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mould_dataid = fun_check_int($_GET['id']);
$sql = "SELECT `mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email` FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
$result = $db->query($sql);
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
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$sql_cfm = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' AND `quote_status` = 1";
	$result_cfm = $db->query($sql_cfm);
	if($result_cfm->num_rows){
		$array_crm = $result_cfm->fetch_assoc();
		$crm_quoteid = $array_crm['quoteid'];
	}else{
		$crm_quoteid = 0;
	}
?>
<div id="table_sheet">
  <h4>模具数据</h4>
  <form action="mould_quotedo.php" name="mould_quote" method="post">
    <table>
      <tr>
        <th width="10%">模具名称：</th>
        <td width="15"><?php echo $array['mould_name']; ?></td>
        <th width="10%">型腔数量：</th>
        <td width="15%"><?php echo $array_mould_cavity_type[$array['cavity_type']]; ?></td>
        <th width="10%">产品零件号：</th>
        <td width="15%"><?php echo $array['part_number']; ?></td>
        <th width="10%">首次试模时间：</th>
        <td width="15%"><?php echo $array['t_time']; ?></td>
      </tr>
      <tr>
        <th>产品大小：</th>
        <td><?php echo $array['p_length'].'*'.$array['p_width'].'*'.$array['p_height']; ?></td>
        <th>产品重量：</th>
        <td><?php echo $array['p_weight']; ?>g</td>
        <th>数据文件名：</th>
        <td><?php echo $array['drawing_file']; ?></td>
        <th>最终交付时间：</th>
        <td><?php echo $array['lead_time']; ?></td>
      </tr>
      <tr>
        <th>模具尺寸：</th>
        <td><?php echo $array['m_length'].'*'.$array['m_width'].'*'.$array['m_height']; ?></td>
        <th>模具重量：</th>
        <td><?php echo $array['m_weight']; ?>Kg</td>
        <th>模具寿命：</th>
        <td><?php echo $array['lift_time']; ?></td>
        <th>设备吨位：</th>
        <td><?php echo $array['tonnage']; ?></td>
      </tr>
      <tr>
        <th>客户名称：</th>
        <td><?php echo $array['client_name']; ?></td>
        <th>项目名称：</th>
        <td><?php echo $array['project_name']; ?></td>
        <th>联系人：</th>
        <td><?php echo $array['contacts']; ?></td>
        <th>电话：</th>
        <td><?php echo $array['tel']; ?></td>
      </tr>
      <tr>
        <th>信箱：</th>
        <td colspan="7"><?php echo $array['email']; ?></td>
      </tr>
      <tr>
        <td colspan="7" align="center"><input type="submit" name="submit" id="submit" value="报价" class="button"<?php if($crm_quoteid > 0) echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="修改" class="button" onclick="location.href='mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=edit'"<?php if($crm_quoteid > 0) echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mould_dataid" value="<?php echo $mould_dataid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
$sql_list = "SELECT `db_mould_quote`.`quoteid`,`db_mould_quote`.`quote_date`,`db_mould_quote`.`ver_num`,`db_mould_quote`.`total_price`,`db_mould_quote`.`total_price_usd`,`db_mould_quote`.`total_price_vat`,`db_mould_quote`.`total_price_txn`,`db_mould_quote`.`quote_status`,`db_employee`.`employee_name` FROM `db_mould_quote` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_quote`.`employeeid` WHERE `db_mould_quote`.`mould_dataid` = '$mould_dataid' ORDER BY `db_mould_quote`.`quoteid` ASC";
$result_list = $db->query($sql_list);
?>
<div id="table_list">
  <?php
  if($result_list->num_rows){
	  $sql_max = "SELECT `quoteid` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' ORDER BY `ver_num` DESC LIMIT 0,1";
	  $result_max = $db->query($sql_max);
	  if($result_max->num_rows){
		  $array_max = $result_max->fetch_assoc();
		  $max_quoteid = $array_max['quoteid'];
	  }
	  
  ?>
  <form action="mould_quote_listdo.php" name="list" method="post">
    <table>
      <caption>
      报价列表
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">报价日期</th>
        <th width="10%">报价人</th>
        <th width="8%">版本</th>
        <th width="11%">价格(不含税)</th>
        <th width="11%">价格(USD)</th>
        <th width="11%">价格(含税)</th>
        <th width="11%">优惠价(含税)</th>
        <th width="8%">状态</th>
        <th width="4%">确认</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
        <th width="4%">Print</th>
      </tr>
      <?php
	  while($row_list = $result_list->fetch_assoc()){
		  $quoteid = $row_list['quoteid'];
		  $quote_status = $row_list['quote_status'];
		  $total_price_txn = $quote_status?number_format($row_list['total_price_txn'],2):'--'; 
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $quoteid; ?>"<?php if($quoteid != $max_quoteid || $crm_quoteid == $quoteid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row_list['quote_date']; ?></td>
        <td><?php echo $row_list['employee_name']; ?></td>
        <td><?php echo $row_list['ver_num']; ?></td>
        <td><?php echo number_format($row_list['total_price'],2); ?></td>
        <td><?php echo number_format($row_list['total_price_usd'],2); ?></td>
        <td><?php echo number_format($row_list['total_price_vat'],2); ?></td>
        <td><?php echo $total_price_txn; ?></td>
        <td><?php echo $array_quote_status[$quote_status]; ?></td>
        <td><?php if($crm_quoteid == 0){ ?>
          <a href="mould_quote_confirm.php?id=<?php echo $quoteid; ?>"><img src="../images/system_ico/confirm_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><?php if($crm_quoteid == 0){ ?>
          <a href="mould_quote.php?id=<?php echo $quoteid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><a href="mould_quote_info.php?id=<?php echo $quoteid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
        <td><a href="mould_quote_print.php?id=<?php echo $quoteid; ?>"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无报价！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>