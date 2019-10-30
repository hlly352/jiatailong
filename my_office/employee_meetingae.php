<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var meeting_subject = $("#meeting_subject").val();
		if(!meeting_subject){
			$("#meeting_subject").focus();
			return false;
		}
	})
})
</script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $employee_name = $_SESSION['employee_info']['employee_name'];
	  //员工的下属
	  $sql_employee = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `superior` = '$employeeid' AND `employee_status`= 1 AND `account_status` = 0 ORDER BY CONVERT(`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
	  $result_employee = $db->query($sql_employee);
  ?>
  <h4>会议申请</h4>
  <form action="employee_meetingdo.php" name="employee_express" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%"><select name="applyer" class="input_txt txt">
            <option value="<?php echo $employeeid; ?>"><?php echo $employee_name; ?></option>
            <?php
			if($result_employee->num_rows){
				while($row_employee = $result_employee->fetch_assoc()){
					echo "<option value=\"".$row_employee['employeeid']."\">".$row_employee['employee_name']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>会议室：</th>
        <td><select name="meetingroom" id="meetingroom" class="input_txt txt">
            <?php
		          foreach($array_meetingroom as $k => $meetingroom){
                echo '<option value="'.$k.'">'.$meetingroom.'</option>';
              }
			       ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>开始时间：</th>
        <td><input name="start_time" type="text" value="<?php echo date('Y-m-d H:i'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false,readOnly:true})" class="input_txt" id="" size="35" />
      </tr>
       <tr>
        <th>结束时间：</th>
        <td><input name="end_time" type="text" value="<?php echo date('Y-m-d H:i',strtotime(date('H:i').'+ 1 hours')); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false,readOnly:true})" class="input_txt" id="" size="35" />   
      </tr>
      <tr>
        <th>会议主题：</th>
        <td><input name="meeting_subject" type="text" class="input_txt" id="meeting_subject" size="35" />
         <span class="tag"> *必填</span></td>
      <tr>
        <th>备注：</th>
        <td><textarea name="meeting_remark" style="margin-left:3px" placeholder="注意事项，所需资料，提示信息..." cols="35" rows="5"></textarea>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location:javascript:history.go(-1);" />
          <input type="hidden" name="agenter" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){  
	  $meetingid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_employee_meeting` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_meeting`.`applyer` WHERE `db_employee_meeting`.`meetingid` = '$meetingid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
    <h4>会议申请修改</h4>
  <form action="employee_meetingdo.php" name="employee_express" method="post">
    <table>
      <tr>
        <th width="20%">申请人：</th>
        <td width="80%">
          <input type="text" readonly class="input_txt" value="<?php echo $array['employee_name'] ?>" />
         </td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="apply_date" value="<?php echo $array['apply_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>会议室：</th>
        <td><select name="meetingroom" id="meetingroom" class="input_txt txt">
            <?php
              foreach($array_meetingroom as $k => $meetingroom){
                $is_select = $k == $array['meetingroom'] ? 'selected':'';
                echo '<option '.$is_select.' value="'.$k.'">'.$meetingroom.'</option>';
              }
             ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>开始时间：</th>
        <td><input name="start_time" type="text" value="<?php echo date('Y-m-d H:i',strtotime($array['start_time'])); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false,readOnly:true})" class="input_txt" id="" size="35" />
      </tr>
       <tr>
        <th>结束时间：</th>
        <td><input name="end_time" type="text" value="<?php echo date('Y-m-d H:i',strtotime($array['end_time'])); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',isShowClear:false,readOnly:true})" class="input_txt" id="" size="35" />   
      </tr>
      <tr>
        <th>会议主题：</th>
        <td><input name="meeting_subject" type="text" value="<?php echo $array['meeting_subject']; ?>" class="input_txt" id="meeting_subject" size="35" />
         <span class="tag"> *必填</span></td>
      <tr>
        <th>备注：</th>
        <td><textarea name="meeting_remark" style="margin-left:3px" placeholder="注意事项，所需资料，提示信息..." cols="35" rows="5"><?php echo $array['meeting_remark']; ?></textarea>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location:javascript:history.go(-1);" />
          <input type="hidden" name="meetingid" value="<?php echo $meetingid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>