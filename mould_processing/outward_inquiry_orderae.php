<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
//查询加工类型
$sql_outward_type = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` ORDER BY `outward_typeid` ASC";
$result_outward = $db->query($sql_outward_type);
//查询组别
$sql_workteam = "SELECT `workteamid`,`workteam_name` FROM `db_mould_workteam` ORDER BY `workteamid` ASC";
$result_workteam = $db->query($sql_workteam);
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

<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <script type="text/javascript">
    $(function(){
      $('#submit').live('click',function(){
          var supplierid = $("#supplierid").val();
          if(!supplierid){
            $("#supplierid").focus();
            return false;
          }
          var workteamid = $('#workteamid').val();
          var delivery_cycle = $("#delivery_cycle").val();
          if(!ri_b.test(delivery_cycle)){
            $("#delivery_cycle").focus();
            return false;
          }
          if(!workteamid){
             $('#workteamid').focus();
             return false;
     }
      })
    })
  </script>
  <h4>外协加工订单添加</h4>
  <form action="outward_inquiry_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="20%">询价单号：</th>
        <td width="80%">系统生成</td>
      </tr>
      <tr>
        <th>订单日期：</th>
        <td><input type="text" name="order_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid" id="supplierid" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>申请组别：</th>
        <td>
         <select class="input_txt txt" name="workteamid" id="workteamid">
           <option value="">请选择</option>
           <?php
            if($result_workteam->num_rows){
              while($row_workteam = $result_workteam->fetch_assoc()){
                echo '<option value="'.$row_workteam['workteamid'].'">'.$row_workteam['workteam_name'].'</option>';
              }
            }
           ?>
         </select>
        </td>
      </tr>
      <tr>
        <th>交货周期：</th>
        <td><input type="text" name="delivery_cycle" id="delivery_cycle" value="5" class="input_txt" />
          天</td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == 'show'){
  ?>
  <h4>外协加工回厂时间</h4>
  <form action="outward_inquiry_orderdo.php" name="material_order" method="post">

    <table>
      <tr>
        <th width="20%">回厂时间：</th>
        <td width="80%">
          <input type="text" name="back_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" />
          <input type="hidden" value="<?php echo $_GET['listid'] ?>" name="listid" />
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $inquiry_orderid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_outward_inquiry_order` WHERE `inquiry_orderid` = '$inquiry_orderid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $rows = $result->fetch_assoc();
  ?>
    <h4>外协加工询价单修改</h4>
  <form action="outward_inquiry_orderdo.php" name="material_order" method="post">
    <script language="javascript" type="text/javascript">
    $(function(){
      $("#submit").click(function(){
        var supplierid = $("#supplierid").val();
        if(!supplierid){
          $("#supplierid").focus();
          return false;
        }
      })
    })
    </script>
    <table>
      <tr>
        <th width="20%">询价单号：</th>
        <td width="80%"><?php echo $rows['inquiry_number'] ?></td>
      </tr>
      <tr>
        <th>订单日期：</th>
        <td><input type="text" name="inquiry_date" value="<?php echo $rows['inquiry_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><select name="supplierid" id="supplierid" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          $is_select = $rows['supplierid'] == $row_supplier['supplierid']?'selected':'';
          echo "<option {$is_select} value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
      </tr>
      <tr>
        <th>申请组别：</th>
        <td>
         <select class="input_txt txt" name="workteamid" id="workteamid">
           <option value="">请选择</option>
           <?php
            if($result_workteam->num_rows){
              while($row_workteam = $result_workteam->fetch_assoc()){
                $is_select = $row_workteam['workteamid'] == $rows['workteamid']?'selected':'';
                echo '<option '.$is_select.' value="'.$row_workteam['workteamid'].'">'.$row_workteam['workteam_name'].'</option>';
              }
            }
           ?>
         </select>
        </td>
      </tr>
      <tr>
        <th>订单状态：</th>
        <td>
          <select name="order_status" class="input_txt txt">
            <option value="0" <?php echo $rows['inquiry_order_status'] == 0?'selected':'' ?>>未下单</option>
            <option value="1" <?php echo $rows['inquiry_order_status'] == 1?'selected':'' ?>>已下单</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
          <input type="hidden" name="inquiry_orderid" value="<?php echo $inquiry_orderid; ?>" />
        </td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>