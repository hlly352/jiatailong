<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$quoteid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould_quote`.`quote_date`,`db_mould_quote`.`ver_num`,`db_mould_quote`.`total_price`,`db_mould_quote`.`total_price_usd`,`db_mould_quote`.`total_price_vat`,`db_mould_quote`.`total_price_txn`,`db_mould_quote`.`quote_status`,`db_mould_data`.`mould_name`,`db_mould_data`.`client_name`,`db_mould_data`.`project_name` FROM `db_mould_quote` INNER JOIN `db_mould_data` ON `db_mould_data`.`mould_dataid` = `db_mould_quote`.`mould_dataid` WHERE `db_mould_quote`.`quoteid` = '$quoteid' AND `db_mould_quote`.`quote_status` = 0";
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
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var quote_status = $("#quote_status").val();						
		var total_price_txn = $("#total_price_txn").val();
		if(quote_status == 1 && !rf_b.test(total_price_txn)){
			$("#total_price_txn").focus();
			return false;
		}
	})
	$("#quote_status").change(function(){
		var quote_status = $(this).val();
		if(quote_status == 1){
			$("#total_price_txn,#submit").attr('disabled',false);
		}else{
			$("#total_price_txn,#submit").attr('disabled',true);
		}
	})
})
</script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$quote_status = $array['quote_status'];
?>
<div id="table_sheet">
  <h4>模具报价确认</h4>
  <form action="mould_quote_confirmdo.php" name="mould_quote_confirm" method="post">
    <table>
      <tr>
        <th width="10%">模具名称：</th>
        <td width="15%"><?php echo $array['mould_name']; ?></td>
        <th width="10%">客户名称：</th>
        <td width="15%"><?php echo $array['client_name']; ?></td>
        <th width="10%">项目名称：</th>
        <td width="15%"><?php echo $array['priject_name']; ?></td>
        <th width="10%">报价日期：</th>
        <td width="15%"><?php echo $array['quote_date']; ?></td>
      </tr>
      <tr>
        <th>版本：</th>
        <td><?php echo $array['ver_num']; ?></td>
        <th>价格(不含税)：</th>
        <td><?php echo number_format($array['total_price'],2); ?></td>
        <th>价格(USD)：</th>
        <td><?php echo number_format($array['total_price_usd'],2); ?></td>
        <th>价格(含税)：</th>
        <td><?php echo number_format($array['total_price_vat'],2); ?></td>
      </tr>
      <tr>
        <th>状态：</th>
        <td><select name="quote_status" id="quote_status">
            <?php foreach($array_quote_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($quote_status == $status_key) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <th>优惠价(含税)：</th>
        <td colspan="5"><input type="text" name="total_price_txn" id="total_price_txn" value="<?php echo $array['total_price_txn']; ?>" class="input_txt"<?php if(!$quote_status) echo " disabled=\"disabled\""; ?> /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button"<?php if(!$quote_status) echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="返回" class="button" onclick="javaascript:history.go(-1);" />
          <input type="hidden" name="pre_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
          <input type="hidden" name="quoteid" value="<?php echo $quoteid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>