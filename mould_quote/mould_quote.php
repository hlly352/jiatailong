<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$quoteid = fun_check_int($_GET['id']);
$sql_mould = "SELECT `db_mould_data`.`mould_name`,`db_mould_data`.`cavity_type`,`db_mould_data`.`part_number`,`db_mould_data`.`t_time`,`db_mould_data`.`p_length`,`db_mould_data`.`p_width`,`db_mould_data`.`p_height`,`db_mould_data`.`p_weight`,`db_mould_data`.`drawing_file`,`db_mould_data`.`lead_time`,`db_mould_data`.`m_length`,`db_mould_data`.`m_width`,`db_mould_data`.`m_height`,`db_mould_data`.`m_weight`,`db_mould_data`.`lift_time`,`db_mould_data`.`tonnage`,`db_mould_data`.`client_name`,`db_mould_data`.`project_name`,`db_mould_data`.`contacts`,`db_mould_data`.`tel`,`db_mould_data`.`email`,`db_mould_data`.`image_filedir`,`db_mould_data`.`image_filename` FROM `db_mould_quote` INNER JOIN `db_mould_data` ON `db_mould_data`.`mould_dataid` = `db_mould_quote`.`mould_dataid` WHERE `db_mould_quote`.`quoteid` = '$quoteid' AND `db_mould_quote`.`quote_status` = 0";
$result_mould = $db->query($sql_mould);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
/* system_base */
body, html {
	height:100%;
}
* {
	margin:0;
	padding:0;
	list-style:none;
	text-decoration:none;
	font-family:"微软雅黑", "宋体";
}
img {
	border:none;
}
#div_tb {
	width:1200px;
	margin:0 auto;
	background:#CCC;
	padding-bottom:10px;
}
table {
	width:100%;
	border-collapse:collapse;
	background:#FFF;
	border:2px solid #000;
	margin-top:10px;
}
table th, table td {
	border:1px solid #000;
	font-size:12px;
	height:25px;
}
table th {
	padding-left:2px;
	text-align:left;
	font-weight:normal;
}
table td {
	overflow:hidden;
	padding-left:2px;
}
.quote_input_txt {
	width:100%;
	height:25px;
	line-height:25px;
	border:none;
	padding-left:2px;
	margin-left:-2px;
	font-size:12px;
}
.quote_input_txt_focus {
	background:#999;
	color:#FFF;
	font-size:12px;
}
</style>
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript" src="js/quote.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$(".quote_input_txt:input").focus(function(){
		$(this).addClass("quote_input_txt_focus");
	}).blur(function(){
		$(this).removeClass("quote_input_txt_focus");
	})
	
})
</script>
<title>模具报价-嘉泰隆</title>
</head>

<body>
<?php
if($result_mould->num_rows){
	$array_mould = $result_mould->fetch_assoc();
	$image_filedir = $array_mould['image_filedir'];
	$image_filename = $array_mould['image_filename'];
	$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
	$image_big_filepath = "../upload/mould_image/".$image_filedir.'/B'.$image_filename;
	if(is_file($image_filepath)){
		$image_file = "<a href=\"".$image_big_filepath."\" target=\"_blank\"><img src=\"".$image_filepath."\" /></a>";
	}else{
		$image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
	}
	$sql_group = "SELECT `db_quote_item_type`.`item_type_sn`,`db_quote_item_type`.`item_typename`,SUM(`db_mould_quote_list`.`total_price`) AS `sum_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' GROUP BY `db_quote_item_type`.`item_type_sn`";
	$result_group = $db->query($sql_group);
	if($result_group->num_rows){
		while($row_group = $result_group->fetch_assoc()){
			$array_group[$row_group['item_type_sn']] = array('item_typename'=>$row_group['item_typename'],'sum_price'=>$row_group['sum_price']);
			$total_sum_price += $row_group['sum_price'];
		}
	}else{
		$array_group = array();
		$total_sum_price = 0;
	}
?>
<div id="div_tb">
  <table>
    <tr>
      <th rowspan="5" width="24%" style="font-size:16px; color:#03C; text-align:center; background:url(../images/logo/logo_quote.png) no-repeat center center;"><b style="margin-right:100px;">JoTyLong</b><b>Company</b></th>
      <td rowspan="5" width="56%" style="font-size:20px; font-weight:bold; color:#03C; text-align:center;">模具费用分解表<br />
        Tooling Cost Break Down</td>
      <th width="10%">客户名称/Customer:</th>
      <td width="10%"><?php echo $array_mould['client_name']; ?></td>
    </tr>
    <tr>
      <th>项目名称/Program:</th>
      <td><?php echo $array_mould['project_name']; ?></td>
    </tr>
    <tr>
      <th>联系人/Attention:</th>
      <td><?php echo $array_mould['contacts']; ?></td>
    </tr>
    <tr>
      <th>电话/TEL:</th>
      <td><?php echo $array_mould['tel']; ?></td>
    </tr>
    <tr>
      <th>信箱/E-mail:</th>
      <td><?php echo $array_mould['email']; ?></td>
    </tr>
  </table>
  <table>
    <tr>
      <th colspan="5">模具名称/Mold Specification</th>
      <th width="14%" class="title">型腔数量/Cav.</th>
      <td rowspan="6" width="24%" style="text-align:center;"><?php echo $image_file; ?></td>
      <th width="18%">产品零件号/Part No.</th>
      <th width="20%">首次试模时间/T1 Time</th>
    </tr>
    <tr>
      <td colspan="5"><?php echo $array_mould['mould_name']; ?></td>
      <td><?php echo $array_mould_cavity_type[$array_mould['cavity_type']]; ?></td>
      <td><?php echo $array_mould['part_number']; ?></td>
      <td><?php echo $array_mould['t_time']; ?></td>
    </tr>
    <tr>
      <th colspan="5">产品大小/Part Size(mm)</th>
      <th>产品重量/Part Weight(g)</th>
      <th>数据文件名/Drawing No.</th>
      <th>最终交付时间/Lead Time</th>
    </tr>
    <tr>
      <td width="6%"><?php echo $array_mould['p_length']; ?></td>
      <td width="3%" style="text-align:center">*</td>
      <td width="6%"><?php echo $array_mould['p_width']; ?></td>
      <td width="3%" style="text-align:center">*</td>
      <td width="6%"><?php echo $array_mould['p_height']; ?></td>
      <td><?php echo $array_mould['p_weight']; ?></td>
      <td><?php echo $array_mould['drawing_file']; ?></td>
      <td><?php echo $array_mould['lead_time']; ?></td>
    </tr>
    <tr>
      <th colspan="5">模具尺寸/Mold Size(mm)</th>
      <th>模具重量/Mold Weight(Kg)</th>
      <th>模具寿命/Longevity</th>
      <th>设备吨位/Press(Ton)</th>
    </tr>
    <tr>
      <td><?php echo $array_mould['m_length']; ?></td>
      <td style="text-align:center">*</td>
      <td><?php echo $array_mould['m_width']; ?></td>
      <td style="text-align:center">*</td>
      <td><?php echo $array_mould['m_height']; ?></td>
      <td><?php echo $array_mould['m_weight']; ?></td>
      <td><?php echo $array_mould['lift_time']; ?></td>
      <td><?php echo $array_mould['tonnage']; ?></td>
    </tr>
  </table>
  <?php
  $type_key = 'A';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`specification`,`db_mould_quote_list`.`number`,`db_mould_quote_list`.`length`,`db_mould_quote_list`.`width`,`db_mould_quote_list`.`height`,`db_mould_quote_list`.`weight`,`db_mould_quote_list`.`unit_price`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th rowspan="<?php echo $count+1; ?>" width="9%"><?php echo $item_typename; ?></th>
      <th width="15%">材料名称<br />
        Material</th>
      <th width="7%">材料牌号<br />
        Specification</th>
      <th width="7%">数量<br />
        Number</th>
      <th width="24%" colspan="5">尺寸<br />
        Size(mm×mm×mm)</th>
      <th width="8%">重量<br />
        Weight(Kg)</th>
      <th width="10%">单价(元)<br />
        Unit Price(RMB)</th>
      <th width="10%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$quote_location = $row['item_type_sn'].'-'.$row['item_sn']
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><input type="text" name="specification" id="<?php echo $quote_location; ?>-specification-<?php echo $quote_listid; ?>" value="<?php echo $row['specification']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="number" id="<?php echo $quote_location; ?>-number-<?php echo $quote_listid; ?>" value="<?php echo $row['number']; ?>" class="quote_input_txt" /></td>
      <td width="6%"><input type="text" name="length" id="<?php echo $quote_location; ?>-length-<?php echo $quote_listid; ?>" value="<?php echo $row['length']; ?>" class="quote_input_txt" /></td>
      <td width="3%" style="text-align:center;">*</td>
      <td width="6%"><input type="text" name="width" id="<?php echo $quote_location; ?>-width-<?php echo $quote_listid; ?>" value="<?php echo $row['width']; ?>" class="quote_input_txt" /></td>
      <td width="3%" style="text-align:center;">*</td>
      <td width="6%"><input type="text" name="height" id="<?php echo $quote_location; ?>-height-<?php echo $quote_listid; ?>" value="<?php echo $row['height']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-weight-<?php echo $quote_listid; ?>"><?php echo $row['weight']; ?></span></td>
      <td><input type="text" name="unit_price" id="<?php echo $quote_location; ?>-unit_price-<?php echo $quote_listid; ?>" value="<?php echo $row['unit_price']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $type_key = 'B';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`weight`,`db_mould_quote_list`.`unit_price`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th width="9%" rowspan="<?php echo $count+1; ?>"><?php echo $item_typename; ?></th>
      <th width="15%">热处理名称<br />
        Item</th>
      <th width="14%">重量<br />
        Weight(Kg)</th>
      <th width="32%">单价(元)<br />
        Unit Price(RMB)</th>
      <th width="20%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$quote_location = $row['item_type_sn'].'-'.$row['item_sn']
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><input type="text" name="<?php echo $quote_location; ?>-weight" id="<?php echo $quote_location; ?>-weight-<?php echo $quote_listid; ?>" value="<?php echo $row['weight']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="unit_price" id="<?php echo $quote_location; ?>-unit_price-<?php echo $quote_listid; ?>" value="<?php echo $row['unit_price']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $type_key = 'C';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`specification`,`db_mould_quote_list`.`supplier`,`db_mould_quote_list`.`number`,`db_mould_quote_list`.`unit_price`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th width="9%" rowspan="<?php echo $count+1; ?>"><?php echo $item_typename; ?></th>
      <th width="15%">装配件<br />
        Item</th>
      <th width="14%">规格型号<br />
        Specification</th>
      <th width="24%">品牌<br />
        Supplier</th>
      <th width="8%">数量<br />
        Number</th>
      <th width="10%">单价(元)<br />
        Unit Price(RMB)</th>
      <th width="10%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$quote_location = $row['item_type_sn'].'-'.$row['item_sn']
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><input type="text" name="specification" id="<?php echo $quote_location; ?>-specification-<?php echo $quote_listid; ?>" value="<?php echo $row['specification']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="supplier" id="<?php echo $quote_location; ?>-supplier-<?php echo $quote_listid; ?>" value="<?php echo $row['supplier']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="number" id="<?php echo $quote_location; ?>-number-<?php echo $quote_listid; ?>" value="<?php echo $row['number']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="unit_price" id="<?php echo $quote_location; ?>-unit_price-<?php echo $quote_listid; ?>" value="<?php echo $row['unit_price']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $type_key = 'D';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`hour`,`db_mould_quote_list`.`unit_price`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th width="9%" rowspan="<?php echo $count+1; ?>"><?php echo $item_typename; ?></th>
      <th width="15%">名称<br />
        Item</th>
      <th width="14%">工时(小时)<br />
        Hour</th>
      <th width="32%">单价(元)<br />
        Unit Price(RMB)</th>
      <th width="20%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$quote_location = $row['item_type_sn'].'-'.$row['item_sn']
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><input type="text" name="<?php echo $type_key; ?>-hour" id="<?php echo $quote_location; ?>-hour-<?php echo $quote_listid; ?>" value="<?php echo $row['hour']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="unit_price" id="<?php echo $quote_location; ?>-unit_price-<?php echo $quote_listid; ?>" value="<?php echo $row['unit_price']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $type_key = 'E';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`hour`,`db_mould_quote_list`.`unit_price`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th width="9%" rowspan="<?php echo $count+1; ?>"><?php echo $item_typename; ?></th>
      <th width="15%">名称<br />
        Item</th>
      <th width="14%">工时(小时)<br />
        Hour</th>
      <th width="32%">单价(元)<br />
        Unit Price(RMB)</th>
      <th width="20%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$quote_location = $row['item_type_sn'].'-'.$row['item_sn']
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><input type="text" name="<?php echo $type_key; ?>-<?php echo $row['item_sn']; ?>-hour" id="<?php echo $quote_location; ?>-hour-<?php echo $quote_listid; ?>" value="<?php echo $row['hour']; ?>" class="quote_input_txt" /></td>
      <td><input type="text" name="unit_price" id="<?php echo $quote_location; ?>-unit_price-<?php echo $quote_listid; ?>" value="<?php echo $row['unit_price']; ?>" class="quote_input_txt" /></td>
      <td><span id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $type_key = 'F';
  $sql = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`descripition`,`db_mould_quote_list`.`total_price`,`db_quote_item`.`item_name`,`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_quote_item_type`.`item_type_sn` = '$type_key' AND `db_mould_quote_list`.`quoteid` = '$quoteid' ORDER BY `db_quote_item`.`item_sn` ASC";
  $result = $db->query($sql);
  if($count = $result->num_rows){
	  $item_typename = array_key_exists($type_key,$array_group)?$array_group[$type_key]['item_typename']:'--';
	  $sum_price = array_key_exists($type_key,$array_group)?$array_group[$type_key]['sum_price']:0;
  ?>
  <table>
    <tr>
      <th width="9%" rowspan="<?php echo $count+1; ?>"><?php echo $item_typename; ?></th>
      <th width="15%">费用名称<br />
        Item</th>
      <th width="46%">费用计算说明<br />
        Descripition</th>
      <th width="20%">金额(元)<br />
        Price(RMB)</th>
      <th width="10%">小计(元)<br />
        Subtotal(RMB)</th>
    </tr>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
		$quote_listid = $row['quote_listid'];
		$item_type_sn = $row['item_type_sn'];
		$item_sn = $row['item_sn'];
		$quote_location = $item_type_sn.'-'.$item_sn;
		if($item_sn == 'E'){
			$total_price_vat = $row['total_price'];
		}
	?>
    <tr>
      <td><?php echo $row['item_name']; ?></td>
      <td><?php if(in_array($item_sn,array('A','B'))){ echo $row['descripition']; }else{ ?><input type="text" name="<?php echo $quote_location; ?>-descripition" id="<?php echo $quote_location; ?>-descripition-<?php echo $quote_listid; ?>" value="<?php echo $row['descripition']; ?>" class="quote_input_txt" /><?php } ?></td>
      <td><?php if(in_array($item_sn,array('C','D','E'))){ ?>
        <span name="<?php echo $quote_location; ?>-total_price" id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>"><?php echo number_format($row['total_price'],2); ?></span>
        <?php }else{ ?>
        <input type="text" name="<?php echo $quote_location; ?>-total_price" id="<?php echo $quote_location; ?>-total_price-<?php echo $quote_listid; ?>" value="<?php echo number_format($row['total_price'],2); ?>" class="quote_input_txt" />
        <?php } ?></td>
      <?php if($i == 1){ ?>
      <td rowspan="<?php echo $count; ?>"><span id="<?php echo $type_key; ?>-sum_price"><?php echo number_format($sum_price,2); ?></span></td>
      <?php } ?>
    </tr>
    <?php
    $i++;
	}
	?>
  </table>
  <?php } ?>
  <?php
  $total_sum_price -= $total_price_vat; 
  ?>
  <table>
    <tr>
      <th width="24%" style="color:#03C; font-weight:bold;">模具价格(元)不含税/Mold Price without VAT(RMB)</th>
      <td width="76%" style="color:#03C; font-weight:bold;"><span id="total_sum_price">&yen;<?php echo number_format($total_sum_price,2); ?></span></td>
    </tr>
  </table>
  <table>
    <tr>
      <th width="24%" style="color:#03C; font-weight:bold;">模具价格(USD)/Mold Price(USD) Rate=6.5</th>
      <td width="76%" style="color:#03C; font-weight:bold;"><span id="total_sum_price_usd">$<?php echo number_format(($total_sum_price/1.02/6.5),2); ?></span></td>
    </tr>
  </table>
  <table>
    <tr>
      <th width="24%" style="color:#03C; font-weight:bold;">模具价格(元)含17%增值税/Mold Price with VAT(RMB)</th>
      <td width="76%" style="color:#03C; font-weight:bold;"><span id="total_sum_price_vat">&yen;<?php echo number_format($total_sum_price+$total_price_vat,2); ?></span></td>
    </tr>
  </table>
  <table>
    <tr>
      <th>Our price excluding texture price and mold trial material cost .<br />
        Payment term : 
        1,50% be paid with PO ;<br />
        2,40% be paid after received T1 sample ;<br />
        3,10% be paid before mold leave JTL ; </th>
    </tr>
  </table>
  <table>
    <tr>
      <th width="70%" style="color:#03C; font-weight:bold; height:50px; border-right:none;" valign="top">供应商/Supplier：</th>
      <td width="30%" style="color:#03C; font-weight:bold; height:50px; border-left:none;" valign="top">签字/Signature:</td>
    </tr>
  </table>
</div>
<?php } ?>
</body>
</html>