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
  #main{table-layout:fixed;width:100%}
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:120px;}
  #add_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;}
  #add_task+input{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
	//点击每一行跳转到启动项目
      $('.show_list').live('click',function(){
        $('.show').each(function(){
          window.open('order_start.php','_self');
        })
      })
      //计算合计
        function getSubtotal(className,subName){
          var number = $(className).size();
          var subtotal = 0;
          for(var i=0;i<number;i++){
            if($(className).eq(i).text()){
            subtotal += parseFloat($(className).eq(i).text());
            }
          
          }
          
          subtotal = subtotal.toFixed(2);
          $(subName).text(subtotal);
        }
        getSubtotal('.agreement_price','#agreement_price');
        getSubtotal('.deal_price','#deal_price');
        getSubtotal('.order_vat','#order_vat');
        getSubtotal('.order_total_rmb','#order_total_rmb');
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
        <th style="width:20px"  rowspan="2">ID</th>
        <th  rowspan="2">日期</th>
        <th  rowspan="2">客户代码</th>
        <th  rowspan="2">客户名称</th>
        <th  rowspan="2">客户订单号</th>
        <th  rowspan="2">项目名称</th>
        <th  rowspan="2">模具编号</th>
        <th  rowspan="2">零件图片</th>
        <th  colspan="5">合同内容</th>
        <th  colspan="3">人民币计价</th>
        <th  colspan="4">进 度</th>
        <th  colspan="2">成 本</th>
        <th  colspan="2">盈 亏</th>
        <th  rowspan="2">损益/扣款</th>
        <th  rowspan="2">状 态</th>
      </tr>
      <tr>
        <th>数量</th>
         <th>单价</th>
        <th>币别</th>
        <th>汇率</th>
        <th>金额</th>
        <th>未税金额</th>
        <th>税金<br>(13%)</th>
        <th>价税合计</th>
        <th>收款比</th>
        <th>发票比</th>
        <th>付款比</th>
        <th>完工比</th>
        <th>材料成本</th>
        <th>制造费用</th>
        <th>金额</th>
        <th>盈亏比</th>
      </tr>
      <?php 
          while($row = $result->fetch_assoc()){
          //查询收款计划
        
         $pay_sql = "SELECT * FROM `db_order_pay` WHERE `mould_id` =".$row['mould_dataid'];
         $res = $db->query($pay_sql);
         $paylist = [];
         if($res->num_rows){
            $paylist = $res->fetch_assoc();
            }
        //查询发票情况
        
         $bill_sql = "SELECT * FROM `db_order_bill` WHERE `mould_id` =".$row['mould_dataid'];
         $res = $db->query($bill_sql);
         $bill_list = [];
         if($res->num_rows){
            $bill_list = $res->fetch_assoc();
            }
        //计算发票比
        $total_bill = intval($bill_list['one_amount']) + intval($bill_list['two_amount']) + intval($bill_list['three_amount']) + intval($bill_list['four_amount']);
        if($row['agreement_price'] != 0){
          $bill_percent = floatval($total_bill / $row['agreement_price']) * 100;
          $bill_percent = number_format($bill_percent,2,'.','').'%';
        }
        //计算收款进度
        $total_pay = intval($paylist['one_reality_amount'] + $paylist['two_reality_amount'] + $paylist['three_reality_amount'] + $paylist['four_reality_amount'] + $paylist['five_reality_amount']);
        if($row['agreement_price'] != 0){
          $pay_percent = $total_pay / $row['agreement_price'] * 100;
        }
        $pay_percent = number_format($pay_percent,2,'.','').'%';
          //获取图片地址
          $src = $row['upload_final_path'];
          $src = $src?strstr($src,'$$')?substr($src,strpos($src,'$$')+2):$src:' ';
          //获取税金
          $order_vat = Floatval($row['deal_price'] * 0.13);
          $order_vat = number_format($order_vat,2,'.','');
          //计算价税合计
          if($row['currency'] == 'rmb_vat'){
                
              $order_total_rmb = $row['agreement_price'];
          }else{
              $order_total_rmb = $row['deal_price'] + $order_vat;
              $order_total_rmb = number_format($order_total_rmb,2,'.','');
                
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
        <td class="show_list"><?php echo strstr($src,'/')?'<img src='.$src.' width="50" height="35"/>': $src ?></td>
        <td class="show_list"><?php echo $row['number']?$row['number']:1 ?></td>
        <td class="show_list"><?php echo $row['unit_price'] == 0?$row['agreement_price']:$row['unit_price']?></td>
        <td class="show_list"><?php echo $array_currency[$row['currency']]?></td>
        <td class="show_list"><?php echo $row['mold_rate']?></td>
        <td class="show_list agreement_price"><?php echo $row['agreement_price']?></td>
        <td class="show_list deal_price"><?php echo number_format($row['deal_price'],2,'.','')?></td>
        <td class="show_list order_vat"><?php echo $order_vat ?></td>
        <td class="show_list order_total_rmb"><?php echo $order_total_rmb?></td>
        <td class="show_list"><?php echo $pay_percent ?></td>
        <td class="show_list"><?php echo $bill_percent?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"></td>
        <td class="show_list"><span>待启动</span></td>
      </tr> 

      <?php } ?>
       <tr>
        <td colspan="12">合计</td>
        <td id="agreement_price"></td>
        <td id="deal_price"></td>
        <td id="order_vat"></td>
        <td id="order_total_rmb"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
       </table>
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