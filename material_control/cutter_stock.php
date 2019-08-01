<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
	$apply_number = rtrim($_GET['apply_number']);
	$specification = rtrim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " AND `db_cutter_apply`.`apply_number` LIKE '%$apply_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid ";
}
// $sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`listid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`employeeid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_mould`.`mould_number`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE (`db_cutter_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND `db_cutter_inout`.`dotype` = 'O' $sqlwhere";
$sql = "SELECT `db_cutter_purchase_list`.`cutterid`,`db_cutter_inout`.`dotype`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`start_quantity`,`db_cutter_inout`.`end_quantity` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_order_list` ON `db_cutter_purchase_list`.`purchase_listid`= `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` WHERE `db_cutter_inout`.`dotime` IN(SELECT max(`db_cutter_inout`.`dotime`) FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_order_list` ON `db_cutter_purchase_list`.`purchase_listid`= `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` GROUP BY `db_cutter_purchase_list`.`cutterid`) GROUP BY `db_cutter_purchase_list`.`cutterid`";

$result = $db->query($sql);
$_SESSION['cutter_inout_list_out'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_inout`.`inoutid` DESC" . $pages->limitsql;

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
<script type="text/javascript">
  $(function(){
    //点击查看时获取当前行的cutterid
    $('.show').live('click',function(){
      var cutterid = $(this).parent().prevAll('.cutterid').text();
      var index = $('.show').index(this);
    if($(this).attr('class') == 'show'){
      //通过ajax查询当前cutterid对应的出入库记录
      $.ajax({
        'url':'../ajax_function/getCutter_inout.php',
        'type':'post',
        'dataType':'json',
        'async':true,
        'data':{cutterid:cutterid},
        'success':function(data){
          //遍历得到的数据
          for(var i = 0;i < data.length; i++){
            var in_out = data[i].dotype == 'I'?'入库':'出库';
             var new_inout = '<tr class="new_tr'+index+'">      <td class="cutterid">'+data[i].inoutid+'</td>   <td>'+data[i].type+'</td>   <td>'+data[i].specification+'</td>      <td>'+data[i].hardness+'</td>      <td><?php echo $row['type']; ?></td>      <td>'+data[i].start_quantity+'</td>      <td>'+in_out+'</td>      <td>'+data[i].quantity+'</td>      <td>'+data[i].end_quantity+'</td>    </tr>';
            $('.show').eq(index).parent().parent().after(new_inout);
        }
        //更改按钮的颜色
        $('.show').eq(index).css('background','#ddd').val('收起').addClass('down');
        },
        'error':function(){
          alert('获取数据失败');
        }
      });
    }else{
        $('.new_tr'+index).remove();
        $(this).removeClass('down').val('查看').css('background','green');
    }
    })
    
  })
</script>
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>加工刀具库存管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申领编号：</th>
        <td><input type="text" name="apply_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>出库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_inout_out.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th width="">类型</th>
      <th widht="">规格</th>
      <th width="">硬度</th>
      <th width="">标准库存</th>
      <th width="">期初库存</th>
      <th width="">操作类型</th>
      <th width="">数量</th>
      <th width="">期末库存</th>
      <th width="">详情</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
      //通过cutterid查询刀具的信息
      $cutter_sql = "SELECT `db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`hardness` FROM `db_mould_cutter` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_hardness` ON `db_mould_cutter`.`hardnessid` = `db_cutter_hardness`.`hardnessid` INNER JOIN `db_cutter_type` ON `db_cutter_specification`.`typeid` = `db_cutter_type`.`typeid` WHERE `db_mould_cutter`.`cutterid`=".$row['cutterid'];
      $result_cutter = $db->query($cutter_sql);
      if($result_cutter->num_rows){
        $row_cutter = $result_cutter->fetch_assoc();

      }
      //计算每一项包含多少条出入库记录
      $inout_sql = "SELECT COUNT(`db_cutter_inout`.`dotime`) FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_order_list` ON `db_cutter_purchase_list`.`purchase_listid`= `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` WHERE `db_cutter_purchase_list`.`cutterid`= ".$row['cutterid']." GROUP BY `db_cutter_purchase_list`.`cutterid`";
     $result_inout = $db->query($inout_sql);
     if($result_inout->num_rows){
      $count = $result_inout->fetch_row()[0];
     }
    	?>
    <tr>
      <td class="cutterid"><?php echo $row['cutterid'] ?></td>
      <td><?php echo $row_cutter['type'] ?></td>
      <td><?php echo $row_cutter['specification']; ?></td>
      <td><?php echo $row_cutter['hardness']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['start_quantity']; ?></td>
      <td><?php echo $row['dotype']=='I'?'入库':'出库'; ?></td>
      <td><?php echo $row['quantity'] ?></td>
      <td><?php echo $row['end_quantity']; ?></td>
      <td><input <?php echo  $count>1?'style="background:green;cursor:pointer"':'' ?> class="show" type="button" value="查看"></td>
    </tr>
    <?php } ?>
  </table>
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