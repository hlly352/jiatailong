<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查找客户信息
$customer_sql ="SELECT `customer_id`,`customer_code`,`customer_name` FROM `db_customer_info`";
$res = $db->query($customer_sql);
if($res->num_rows){
	$customer_list = [];
	while($customer = $res->fetch_assoc()){
		$customer_list[] = $customer; 
	}
}

if($_GET['submit']){
  $mould_name = trim($_GET['mould_name']);
  $client_name = trim($_GET['client_name']);
  $project_name = trim($_GET['project_name']);
  $sqlwhere = "  AND `client_name` LIKE '%$client_name%' AND `mould_name` LIKE '%$mould_name%' AND `project_name` LIKE '%$project_name%'";
}

//sql语句
$sql = "SELECT * FROM `db_mould_data` INNER JOIN `db_customer_info` as b ON `db_mould_data`.`client_name`=b.`customer_id` WHERE `is_approval` = '1' AND `is_deal` = '1'".$sqlwhere;
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `deal_time` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
  #main tr td input{width:120px;}
  #add_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;}
  #add_task+input{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
	
    })
</script>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
  <form action="order_taskdo.php" name="search" method="get">
    <table >

      <tr>
   
       </tr>
       <tr>
       <td>客户名称</td>
       <td><input type="text" name="client_name" class="input_txt" /></td>
       <td></td>
       <td>项目名称</td>
       <td><input type="text" name="project_name" class="input_txt"></td>
       <td></td>
        <td>客户代码</td>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <td>日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="order_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
          <?php
         if($result->num_rows){
            
        
    ?>
      <tr>
        <th style="">ID</th>
        <th style="">日期</th>
        <th style="">客户代码</th>
        <th style="">客户名称</th>
        <th style="">客户订单号</th>
        <th style="">项目名称</th>
        <th style="">模具编号</th>
        <th style="">零件图片</th>
        <th style="">合同金额</th>
        <th style="">币别</th>
        <th style="">汇率</th>
        <th style="">未税金额</th>
        <th style="">订单人民币金额</th>
        <th style="">税金</th>
        <th style="">价税合计</th>
        <th style="">收款进度</th>
        <th style="">发票进度</th>
        <th style="">付款进度</th>
        <th style="">完工比</th>
        <th style="">材料成本</th>
        <th style="">制造费用</th>
        <th style="">盈亏</th>
        <th style="">盈亏比</th>
      </tr>
      <?php 
          while($row = $result->fetch_assoc()){
          //获取图片地址
          $src = $row['upload_final_path'];
          $src = strstr($src,'$$')?substr($src,strpos($src,'$$')+2):$src;
          //获取未税金额
          if($row['currency'] == 'rmb_vat'){
              $money_vat = $row['agreement_price']*0.87;
              $order_vat = $row['agreement_price'] * 0.13;
              $order_rmb = $row['agreement_price'] * $row['mold_rate'];
               
          } else {
              $money_vat = $row['agreement_price'];
              $order_vat = 0;
              $order_rmb = $row['agreement_price'] * $row['mold_rate'];
          }
      ?>
     <tr class="show">
         <td><input type="checkbox" name="id[]" value="<?php echo $row['mould_dataid']; ?>" style="width:20px"/></td>
        <td class="show_list"><?php echo date('Y-m-d',$row['deal_time']) ?></td>
        <td class="show_list"><?php echo strstr($row['customer_code'],'$$')?substr($row['customer_code'],strrpos($row['customer_code'],'$$')+2):$row['customer_code']?></td>
        <td class="show_list"><?php echo strstr($row['customer_name'],'$$')?substr($row['customer_name'],strrpos($row['customer_name'],'$$')+2):$row['customer_name']?></td>
        <td class="show_list"></td>
        <td class="show_list"><?php echo $row['project_name']?></td>        
        <td class="show_list"><?php echo 'JTL'.$row['mold_id']?></td>
        <td class="show_list"><img src="<?php echo $src ?>" width="50" height="35"/></td>
        <td class="show_list"><?php echo $row['agreement_price']?></td>
        <td class="show_list"><?php echo $array_currency[$row['currency']]?></td>
        <td class="show_list"><?php echo $row['mold_rate']?></td>
        <td class="show_list"><?php echo $money_vat?></td>
        <td class="show_list"><?php echo $order_rmb ?></td>
        <td class="show_list"><?php echo $order_vat ?></td>
        <td class="show_list"><?php echo $order_rmb?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
      </tr> 

      <?php } ?>
       </table>
    <!--<div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
    -->
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php  } else{
     echo '<p class="tag">系统提示：暂无记录！</p>';
  } ?>
      
</div>
 <?php include "../footer.php"; ?>
</body>
</html>