<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';

//查找是否有数据
$pay_sql = "SELECT * FROM `db_order_bill` WHERE `mould_id`=".$_GET['id'];
$res = $db->query($pay_sql);
if($res->num_rows){
	$bill_info = [];
	$bill_info = $res->fetch_assoc();
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>

<title>订单管理-嘉泰隆</title>
<style type="text/css">
  #main{table-layout:fixed;width:1350px;}
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:100px;}
  #save_bill{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;margin-top:20px;}
</style>

</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
 
</div>
<div id="table_list">
  <form action="order_billdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
      	<td colspan="12">
      		<?php echo $_GET['mould_no'] ?>
      		<input type="hidden" name="mould_id" value="<?php echo $_GET['id'] ?>" />	
      	</td>
      </tr>
      <tr>
        <th colspan="3">一期</th>
        <th colspan="3">二期</th>
        <th colspan="3">三期</th>
        <th colspan="3">四期</th>
      </tr>
      <tr>
      	<th>日期</th>
      	<th>金额</th>
      	<th>发票号码</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>发票号码</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>发票号码</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>发票号码</th>
      </tr>
     <?php 
     	if(!is_null($bill_info)){
     		echo '<input type="hidden" name="bill_id" value='.$bill_info['bill_id'].' />';
     	}
     ?>
     <tr class="show">
         <td class="show_list"><input type="text" name="one_date" value="<?php echo $bill_info['one_date'] ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
         <td class="show_list"><input type="text" name="one_amount" value="<?php echo $bill_info['one_amount'] ?>"></td>
        <td class="show_list"><input type="text" name="one_no" value="<?php echo $bill_info['one_no'] ?>" ></td>        
        <td class="show_list"><input type="text" name="two_date" value="<?php echo $bill_info['two_date']?>"  onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})"></td>
        <td class="show_list"><input type="text" name="two_amount"  value="<?php echo $bill_info['two_amount'] ?>" ></td>
        <td class="show_list"><input type="text" name="two_no" value="<?php echo $bill_info['two_no'] ?>"></td>
        <td class="show_list"><input type="text" name="three_date" value="<?php echo $bill_info['three_date'] ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="three_amount" value="<?php echo $bill_info['three_amount'] ?>"></td>
        <td class="show_list"><input type="text" name="three_no" value="<?php echo $bill_info['three_no'] ?>"></td>
        <td class="show_list"><input type="text" name="four_date" value="<?php echo $bill_info['four_date'] ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="four_amount" value="<?php echo $bill_info['four_amount'] ?>"></td>
        <td class="show_list"><input type="text" name="four_no" value="<?php echo $bill_info['four_no'] ?>"  ></td>
      </tr> 
      <tr>
      	<td colspan="12">
      		<input id="save_bill" type="submit" value="保存">
      	</td>
      </tr>
       </table>
</form>

</div>
 <?php include "../footer.php"; ?>
</body>
</html>