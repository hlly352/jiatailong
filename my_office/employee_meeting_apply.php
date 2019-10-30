<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
  $express_num = trim($_GET['express_num']);
  $approve_status = $_GET['approve_status'];
  if($approve_status){
    $sql_approve_status = " AND `db_employee_express`.`approve_status` = '$approve_status'";
  }
  $express_status = $_GET['express_status'];
  if($express_status != NULL){
    $sql_express_status = " AND `db_employee_express`.`express_status` = '$express_status'";
  }
  $sqlwhere = " AND `db_employee_express`.`express_num` LIKE '%$express_num%' $sql_approve_status $sql_express_status";
}
$sql = "SELECT * FROM `db_employee_meeting` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_meeting`.`applyer` WHERE (`db_employee_meeting`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_meeting`.`apply_date` DESC,`db_employee_meeting`.`start_time` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>我申请的会议</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>审批状态：</th>
        <td><select name="approve_status">
            <option value="">所有</option>
            <?php
      foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
        echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
      }
      ?>
          </select></td>
        <th>状态：</th>
        <td><select name="express_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $express_status && $express_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="申请" class="button" onclick="location.href='employee_meetingae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="employee_expressdo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">会议室</th>
        <th width="10%">申请人</th>
        <th width="10%">申请日期</th>
        <th width="10%">开始时间</th>
        <th width="14%">结束时间</th>
        <th width="10%">会议主题</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
    while($row = $result->fetch_assoc()){
      $meetingid = $row['meetingid'];
      $meeting_status = $row['meeting_status'];
      $meetingroom = $array_meetingroom[$row['meetingroom']];
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $meetingid; ?>"<?php if($approve_status != "C") echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $meetingroom; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['apply_date']; ?></td>
        <td><?php echo $row['start_time']; ?></td>
        <td><?php echo $row['end_time']; ?></td>
        <td><?php echo $row['meeting_subject']; ?></td>
        <td><?php echo $meeting_status; ?></td>
        <td><?php if($meeting_status == 1 && $row['applyer'] == $employeeid){ ?>
          <a href="employee_meetingae.php?id=<?php echo $meetingid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
      </tr>
      <?php } ?>
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