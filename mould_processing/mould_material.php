<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询加工类型
$outward_type_sql = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` ORDER BY `outward_typeid`";
$result_outward_type = $db->query($outward_type_sql);
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_number = trim($_GET['material_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  if($complete_status != NULL){
    $sql_complete_status = " AND `db_mould_material`.`complete_status` = '$complete_status'";
  }
  $sqlwhere = " AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' $sql_complete_status";
}
$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_mould_material` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`type` != 'Z' $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_material_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_material`.`material_date` DESC,`db_mould_material`.`materialid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript">
  $('#submit').live('click',function(){
    var outward_typeid = $('#outward_typeid').val();
    if(!outward_typeid){
      alert('请选择加工类型');
      return false;
    }
  })
</script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>外协物料</h4>
  <table>
    <tr>
      <td style="font-size:15px">加工类型</td>
      <td>
        <select name="outward_typeid" id="outward_typeid" form="form1" class="input_txt txt">
          <option value="">--请选择--</option>
          <?php
            if($result_outward_type->num_rows){
              if(isset($_GET['outward_typeid'])){
                $outward_typeid = $_GET['outward_typeid'];
              }
              while($row_type = $result_outward_type->fetch_assoc()){
                $is_select = $outward_typeid == $row_type['outward_typeid']?'selected':'';
                echo '<option '.$is_select.' value="'.$row_type['outward_typeid'].'">'.$row_type['outward_typename'].'</option>';
              }
            }
          ?>
        </select>
      </td>
    </tr>
  </table>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料编号：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <!-- <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_mould_material_list.php'" /></td> -->
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_id = $result_id->fetch_assoc()){
      $array_materialid .= $row_id['materialid'].',';
    }
    $array_materialid = rtrim($array_materialid,',');
    $sql_order = "SELECT `materialid` FROM `db_material_order_list` WHERE `materialid` IN ($array_materialid) GROUP BY `materialid`";
    $result_order = $db->query($sql_order);
    if($result_order->num_rows){
      while($row_order = $result_order->fetch_assoc()){
        $array_order[] = $row_order['materialid'];
      }
    }else{
      $array_order = array();
    }
  ?>
  <form action="outward_inquiry_do.php" id="form1" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="8%">料单编号</th>
        <th width="4%">料单序号</th>
        <th width="10%">物料编码</th>
        <th width="10%">物料名称</th>
        <th width="12%">规格</th>
        <th width="4%">数量</th>
        <th width="6%">材质</th>
        <th width="10%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $specification_bg = '';
      $material_name_bg = '';
      $materialid = $row['materialid'];
      $material_number_code = $row['material_number_code'];
      $specification = $row['specification'];
      if(in_array($material_number_code,array(1,2,3,4,5))){
        $tag_a = substr_count($specification,'*');
        $tag_b = substr_count($specification,'#');
        $specification_bg = ($tag_a != 2 || $tag_b != 1)?" style=\"background:orange\"":'';
      }
      $material_name_bg = $row['complete_status']?'':" style=\"background:yellow\"";
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $materialid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_no']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" disabled="disabled" />
      <input type="hidden" name="action" value="add" />
      <input type="hidden" name="query" value="<?php echo $_GET['submit'] ?>" />
      <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>