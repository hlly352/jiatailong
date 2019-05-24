<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';

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
  #main tr td input{width:60px;}
  #add_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;}
  #add_task+input{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>

</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
 
</div>
<div id="table_list">
  <form action="order_paydo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
      	<td colspan="20">
      		<?php echo $_GET['mould_dataid'] ?>
      		<input type="hidden" name="mould_id" value="<?php echo $_GET['id'] ?>" />	
      	</td>
      </tr>
      <tr>
        <th colspan="4">一期</th>
        <th colspan="4">二期</th>
        <th colspan="4">三期</th>
        <th colspan="4">四期</th>
        <th colspan="4">五期</th>
      </tr>
      <tr>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      </tr>
      <tr>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>

      </tr>
     
     <tr class="show">
         <td class="show_list"><input type="text" name="one_plan_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
         <td class="show_list"><input type="text" name="one_plan_amount"></td>
        <td class="show_list"><input type="text" name="one_reality_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>        
        <td class="show_list"><input type="text" name="one_reality_amount"></td>
        <td class="show_list"><input type="text" name="two_plan_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="two_plan_amount"></td>
        <td class="show_list"><input type="text" name="two_reality_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="two_reality_amount"></td>
        <td class="show_list"><input type="text" name="three_plan_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="three_plan_amount"></td>
        <td class="show_list"><input type="text" name="three_reality_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="three_reality_amount"></td>
        <td class="show_list"><input type="text" name="four_plan_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="four_plan_amount"></td>
        <td class="show_list"><input type="text" name="four_reality_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="four_reality_amount"></td>
        <td class="show_list"><input type="text" name="five_plan_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="five_plan_amount"></td>
        <td class="show_list"><input type="text" name="five_reality_date" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" ></td>
        <td class="show_list"><input type="text" name="five_reality_amount"></td>
      </tr> 
      <tr>
      	<td colspan="20">
      		<input type="submit" value="保存">
      	</td>
      </tr>
       </table>
</form>

</div>
 <?php include "../footer.php"; ?>
</body>
</html>