<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sdate_time = strtotime($sdate);
$edate_time = strtotime($edate);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_suppliers = $db->query($sql_supplier);
if($_GET['submit']){
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_material_funds_list`.`supplierid` = '$supplierid'";
  }
  $sqlwhere = "$sql_supplierid";
}
$sql = "SELECT GROUP_CONCAT(`db_material_funds_list`.`fundsid`) AS `fundsid`,`db_supplier`.`supplier_cname`,`db_material_funds_list`.`supplierid` FROM `db_material_funds_list` INNER JOIN `db_supplier` ON `db_material_funds_list`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_funds_list`.`approval_status` = 'Z' $sqlwhere GROUP BY `db_material_funds_list`.`supplierid`";
        //数组用于接收到对应的月份
        $year = date('Y-');
        $date = array();
        for($i=1;$i<13;$i++){
          $i = $i<10?'0'.$i:$i;
          $date[] = $year.$i;
        }
      $result = $db->query($sql);
        while($row = $result->fetch_assoc()){
          $fundsid = $row['fundsid'];
   
          //根据供应商查询应付款，实付款及发票数据
         $supplier_sql = "SELECT LEFT(`approval_date`,7) AS `date`,`apply_amount`,`amount`,`invoice_no` FROM `db_material_funds_list` WHERE `fundsid` IN($fundsid)";
          $result_supplier = $db->query($supplier_sql);
          if($result_supplier->num_rows){
            $info = array();
            while($row_supplier = $result_supplier->fetch_assoc()){
             foreach($date as $v){
              if($v == $row_supplier['date']){
                $info[$v]['date'] = $row_supplier['date'];
                $info[$v]['amount'] = $row_supplier['amount'];
                $info[$v]['apply_amount'] = $row_supplier['apply_amount'];
                $info[$v]['invoice_no'] = $row_supplier['invoice_no'];
                ${'amount_tot'.$row['supplierid']} += $row_supplier['amount'];
                ${'apply_amount_tot'.$row['supplierid']} += $row_supplier['apply_amount'];
              }
             }
            }
          foreach($date as $v){
            $mon_amount_tot[$v] += $info[$v]['amount'];
            $mon_apply_amount_tot[$v] += $info[$v]['apply_amount'];
          }
          $tot_should_pay += number_format(${'amount_tot'.$row['supplierid']},2,'.','');
          $tot_actual_pay += number_format(${'apply_amount_tot'.$row['supplierid']},2,'.','');
          $tot_no_pay += number_format((${'amount_tot'.$row['supplierid']} - ${'apply_amount_tot'.$row['supplierid']}),2,'.','');

          }}

$pages = new Page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_funds_list`.`approval_date` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>

<title>订单管理-嘉泰隆</title>

</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
  <form action="" name="search" method="get">
    <table >
       <tr>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_suppliers->num_rows){
            while($row_supplier = $result_suppliers->fetch_assoc()){
             echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
         }
        }
      ?>
      </select>
      </td>
       <!--  <td>日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_tx" />
          --
          &nbsp;&nbsp;
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_tx" /></td> -->
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
        <th rowspan="2">ID</th>
        <th rowspan="2">供应商</th>
        <!-- <th rowspan="2">上期结余</th> -->
        <th colspan="3">一月</th>
        <th colspan="3">二月</th>
        <th colspan="3">三月</th>
        <th colspan="3">四月</th>
        <th colspan="3">五月</th>
        <th colspan="3">六月</th>
        <th colspan="3">七月</th>
        <th colspan="3">八月</th>
        <th colspan="3">九月</th>
        <th colspan="3">十月</th>
        <th colspan="3">十一月</th>
        <th colspan="3">十二月</th>
        <th rowspan="2"><?php echo date('Y') ?>应付<br />总 计</th>
        <th rowspan="2"><?php echo date('Y') ?>实付<br />总 计</th>
        <th rowspan="2">未付款<br />总 计</th>
        
      </tr>
      <tr>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
         <th>应付</th>
         <th>实付</th>
         <th>发票号</th>
      </tr>
      <?php
        //数组用于接收到对应的月份
        $year = date('Y-');
        $date = array();
        for($i=1;$i<13;$i++){
          $i = $i<10?'0'.$i:$i;
          $date[] = $year.$i;
        }

        while($row = $result->fetch_assoc()){
          $fundsid = $row['fundsid'];
   
          //根据供应商查询数据
         $supplier_sql = "SELECT LEFT(`approval_date`,7) AS `date`,`apply_amount`,`amount`,`invoice_no` FROM `db_material_funds_list` WHERE `fundsid` IN($fundsid)";
          $result_supplier = $db->query($supplier_sql);
          if($result_supplier->num_rows){
            $info = array();
            while($row_supplier = $result_supplier->fetch_assoc()){
             foreach($date as $v){
              if($v == $row_supplier['date']){
                $info[$v]['date'] = $row_supplier['date'];
                $info[$v]['amount'] += $row_supplier['amount'];
                $info[$v]['apply_amount'] += $row_supplier['apply_amount'];
                $info[$v]['invoice_no'][] = $row_supplier['invoice_no'];
              //  ${'amount_tot'.$row['supplierid']} += $row_supplier['amount'];
               // ${'apply_amount_tot'.$row['supplierid']} += $row_supplier['apply_amount'];
              }
             }
            }
            
         // var_dump($info);exit;
          }
      
       ?>
      <tr> 
        <td>
          <?php echo $row['supplierid']?>
        </td>
        <td>
          <?php echo $row['supplier_cname'] ?>
        </td>
        <!-- <td></td> -->
        <?php
          foreach($date as $v){
          //  $mon_amount_tot[$v] += $info[$v]['amount'];
          //  $mon_apply_amount_tot[$v] += $info[$v]['apply_amount'];
            if($info[$v]['date'] == $v){
              $invoice_no_str = '';
              foreach($info[$v]['invoice_no'] as $value){
                if(!empty($value)){
                  $invoice_no_str .= $value.',';
                }
              }
              $invoice_no_str = rtrim($invoice_no_str,',');
              echo '<td>'.$info[$v]['amount'].'</td><td>'.$info[$v]['apply_amount'].'</td><td>'.$invoice_no_str.'</td>';
            } else {
              echo '<td></td><td></td><td></td>';
            }
          }
         ?>
         <td>
           <?php 
              echo number_format(${'amount_tot'.$row['supplierid']},2,'.','');
            //  $tot_should_pay += number_format(${'amount_tot'.$row['supplierid']},2,'.','');
            ?>
         </td>
         <td>
            <?php
              echo number_format(${'apply_amount_tot'.$row['supplierid']},2,'.','');
           //   $tot_actual_pay += number_format(${'apply_amount_tot'.$row['supplierid']},2,'.','');
            ?>
         </td>
         <td>
           <?php 
              echo number_format((${'amount_tot'.$row['supplierid']} - ${'apply_amount_tot'.$row['supplierid']}),2,'.','');
           //   $tot_no_pay += number_format((${'amount_tot'.$row['supplierid']} - ${'apply_amount_tot'.$row['supplierid']}),2,'.','');
           ?>
         </td>
      </tr>
      <?php } ?>
        <tr>
          <td></td>
          <td></td>
      <?php
        for($i=0;$i<12;$i++){
          list($key,$value) = each($mon_amount_tot);
          echo '<td>'.$value.'</td>';
          list($k,$v) = each($mon_apply_amount_tot);
          echo '<td>'.$v.'</td>';
          echo '<td></td>';
        }
      ?>
        <td><?php echo $tot_should_pay; ?></td>
        <td><?php echo $tot_actual_pay; ?></td>
        <td><?php echo $tot_no_pay; ?></td>
      </tr>
       </table>
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