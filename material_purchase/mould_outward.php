<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$array_system_shell = $_SESSION['system_shell'][$system_dir];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//类型
$sql_outward_type = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` ORDER BY `outward_typeid` ASC";
$result_outward_type = $db->query($sql_outward_type);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  if($mould_number){
    $sql_mould_number = " AND `db_mould`.`mould_number` LIKE '%$mould_number%'";
  }
  $order_number = trim($_GET['order_number']);
  $date_type = $_GET['date_type'];
  if ($date_type == 'A') {
    $sql_date_type = " AND (`db_mould_outward`.`order_date` BETWEEN '$sdate' AND '$edate')";
  } elseif ($date_type == 'B') {
    $sql_date_type = " AND (`db_mould_outward`.`actual_date` BETWEEN '$sdate' AND '$edate')";
  }
  $cost_from = $_GET['cost_from'];
  $cost_to = $_GET['cost_to'];
  if ($cost_from != NULL && $cost_to != NULL) {
    $sql_cost = " AND (`db_mould_outward`.`cost` BETWEEN '$cost_from' AND '$cost_to')";
  }
  $outward_typeid = $_GET['outward_typeid'];
  if($outward_typeid){
    $sql_outward_typeid = " AND `db_mould_outward`.`outward_typeid` = '$outward_typeid'";
  }
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_mould_outward`.`supplierid` = '$supplierid'";
  }
  $inout_status = $_GET['inout_status'];
  if($inout_status != NULL){
    $sql_inout_status = " AND `db_mould_outward`.`inout_status` = '$inout_status'";
  }
  $outward_status = $_GET['outward_status'];
  if($outward_status != NULL){
    $sql_outward_status = " AND `db_mould_outward`.`outward_status` = '$outward_status'";
  }
  $sqlwhere = " WHERE `db_mould_outward`.`order_number` LIKE '%$order_number%' $sql_date_type $sql_cost $sql_mould_number $sql_outward_typeid $sql_supplierid $sql_inout_status $sql_outward_status";
}else{
  $date_type == 'A';
  $outward_status = 1;
  $sqlwhere = " WHERE (`db_mould_outward`.`order_date` BETWEEN '$sdate' AND '$edate') AND `db_mould_outward`.`outward_status` = '$outward_status'";
}
$sql = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`supplierid`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,IF(`db_mould_outward`.`inout_status` = 1,DATEDIFF(`db_mould_outward`.`actual_date`,`db_mould_outward`.`plan_date`),DATEDIFF(`db_mould_outward`.`plan_date`,CURDATE())) AS `diff_date` FROM `db_mould_outward` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` $sqlwhere";
$result = $db->query($sql);
$result_allid =$db->query($sql); 
$_SESSION['mould_outward'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_outward`.`plan_date` DESC,`db_mould_outward`.`outwardid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" type="text/css" href="../images/logo/jtl.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>外协加工</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>外协单号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>
          <select name="date_type">
            <option value="A"<?php if ($date_type == 'A') echo ' selected'; ?>>外协时间</option>
            <option value="B"<?php if ($date_type == 'B') echo ' selected'; ?>>回厂时间</option>
          </select>：
        </th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="outward_typeid">
            <option value="">所有</option>
            <?php
            if($result_outward_type->num_rows){
        while($row_outward_type = $result_outward_type->fetch_assoc()){
          echo "<option value=\"".$row_outward_type['outward_typeid']."\">".$row_outward_type['outward_typename']."</option>";
        }
      }
      ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
        <th>进度状态：</th>
        <td><select name="inout_status">
            <option value="">所有</option>
            <?php
      foreach($array_mould_inout_status as $inout_status_key=>$inout_status_value){
        echo "<option value=\"".$inout_status_key."\">".$inout_status_value."</option>";
      }
      ?>
          </select></td>
        <th>金额：</th>
        <td><input type="text" name="cost_from" class="input_txt" />
          --
          <input type="text" name="cost_to" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="outward_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $outward_status && $outward_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td>
        <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_moult_outward.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_allid = $result_allid->fetch_assoc()){
      $array_all_outwardid .= $row_allid['outwardid'].',';
    }
    $array_all_outwardid = rtrim($array_all_outwardid,',');
    while($row_id = $result_id->fetch_assoc()){
      $array_outwardid .= $row_id['outwardid'].',';
    }
    $array_outwardid = rtrim($array_outwardid,',');
    //支付金额
    $sql_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_outwardid) AND `data_type` = 'MO' GROUP BY `linkid`";
    $result_pay_amount = $db->query($sql_pay_amount);
    if($result_pay_amount->num_rows){
      while($row_pay_amount = $result_pay_amount->fetch_assoc()){
        $array_pay_amount[$row_pay_amount['linkid']] = $row_pay_amount['total_pay_amount'];
      }
    }else{
      $array_pay_amount = array();
    }
    //print_r($array_pay_amount);
    //计算总费用
    $sql_cost = "SELECT SUM(`cost`) AS `total_cost` FROM `db_mould_outward` WHERE `outwardid` IN ($array_all_outwardid)";
    $result_cost = $db->query($sql_cost);
    $array_cost = $result_cost->fetch_assoc();
    //统计是否有附件
    $sql_file = "SELECT `linkid`,COUNT(*) AS `count` FROM `db_upload_file` WHERE `linkcode` = 'MO' AND `linkid` IN ($array_outwardid) GROUP BY `linkid`";
    $result_file = $db->query($sql_file);
    if($result_file->num_rows){
      while($row_file = $result_file->fetch_assoc()){
        $array_file[$row_file['linkid']] = $row_file['count'];
      }
    }else{
      $array_file = array();
    }
  ?>
  <form action="mould_outwarddo.php" name="mould_outward_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="14%">零件编号</th>
        <th width="6%">外协时间</th>
        <th width="6%">申请组别</th>
        <th width="5%">外协单号</th>
        <th width="4%">数量</th>
        <th width="6%">供应商</th>
        <th width="6%">类型</th>
        <th width="5%">金额</th>
        <th width="5%">现金</th>
        <th width="5%">申请人</th>
        <th width="6%">计划回厂</th>
        <th width="6%">实际回厂</th>
        <th width="4%">进度状态</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $outwardid = $row['outwardid'];
      $mould_number = $row['mouldid']?$row['mould_number']:'--';
      $supplier_cname = $row['supplierid']?$row['supplier_cname']:'--';
      $iscash = $row['iscash'];
      $part_number = strlen_sub($row['part_number'],28,28);
      $part_number_title = (mb_strlen($row['part_number'],'utf8')>28)?" title=\"".$row['part_number']."\"":'';
      $pay_amount = ($iscash)?array_key_exists($outwardid,$array_pay_amount)?$array_pay_amount[$outwardid]:0:'--';
      $inout_status = $row['inout_status'];
      $actual_date = $inout_status?$row['actual_date']:'--';
      $diff_date = $row['diff_date'];
      if($inout_status && $diff_date>0){
        $actual_date = "<font color=red>".$actual_date."</font>";
      }else{
        $actual_date = $actual_date;
      }
      if(!$inout_status && $diff_date <=1){
        $plan_date_bg = " style=\"background:#F00;\"";
      }elseif(!$inout_status && ($diff_date == 2 || $diff_date == 3)){
        $plan_date_bg = " style=\"background:#FF0;\"";
      }else{
        $plan_date_bg = "";
      }
      $file_count = array_key_exists($outwardid,$array_file)?$array_file[$outwardid]:0;
      $file_image = ($file_count>0)?"<img src=\"../images/system_ico/file_10_10.png\" width=\"10\" height=\"10\" />":"<img src=\"../images/system_ico/upload_10_10.png\" width=\"10\" height=\"10\" />";
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $outwardid; ?>"<?php if(array_key_exists($outwardid,$array_pay_amount) || !$array_system_shell['isconfirm']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $mould_number; ?></td>
        <td<?php echo $part_number_title; ?>><?php echo $part_number; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['workteam_name']; ?></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $supplier_cname; ?></td>
        <td><?php echo $row['outward_typename']; ?></td>
        <td><?php echo $row['cost']; ?></td>
        <td><?php echo $pay_amount; ?></td>
        <td><?php echo $row['applyer']; ?></td>
        <td<?php echo $plan_date_bg; ?>><?php echo $row['plan_date']; ?></td>
        <td><?php echo $actual_date; ?></td>
        <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <!--   <td><a href="upload_file.php?id=<?php echo $outwardid; ?>&type=MO"><?php echo $file_image; ?></a></td>
        <td><?php if ($array_system_shell['isconfirm']) { ?><a href="mould_outwardae.php?id=<?php echo $outwardid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>-->
        <td><a href="mould_outward_info.php?id=<?php echo $outwardid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="9">Total</td>
        <td><?php echo $array_cost['total_cost']; ?></td>
        <td colspan="8">&nbsp;</td>
      </tr>
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