<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$supplier_name = trim($_GET['supplier_name']);
	$supplier_typeid = $_GET['supplier_typeid'];
	if(strlen($supplier_typeid) != 0){
		$sql_suppliertype = " AND FIND_IN_SET($supplier_typeid,`supplier_type`) > 0";
	}
	
	$sqlwhere = " WHERE (`supplier_name` LIKE '%$supplier_name%' OR `supplier_ename` LIKE '%$supplier_name%') $sql_suppliertype";
}
$sql = "SELECT * FROM `db_other_supplier` $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `supplier_code` ASC" . $pages->limitsql;
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
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>供应商</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>供应商：</th>
        <td><input type="text" name="supplier_name" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="supplier_typeid">
            <option value="">所有</option>
            <?php
				foreach($array_mould_other_material as $k=>$v){
					echo '<option value="'.$k.'"">'.$v.'</option>';
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='other_supplierae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  
  <form action="other_supplierae.php" name="supplier_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">中文名</th>
        <th width="">英文名</th>
        <th width="">全称</th>
        <th width="">类型</th>
        <th width="">地址</th>
        <th width="">Edit</th>
      </tr>
      <?php
      if($result->num_rows){
	      while($row = $result->fetch_assoc()){
			  $supplierid = $row['other_supplier_id'];
		  ?>
	      <tr>
	        <td><input type="checkbox" name="id[]" value="<?php echo $supplierid; ?>"<?php if(in_array($supplierid,$array_supplier)) echo " disabled=\"disabled\""; ?> /></td>
	        <td><?php echo $row['supplier_code'].'-'.$row['supplier_cname']; ?></td>
	        <td><?php echo $row['supplier_ename']; ?></td>
	        <td><?php echo $row['supplier_name']; ?></td>
	        <td>
	        	<?php   
	        		$supplier_type = explode(',',$row['supplier_type']); 
	        		$supplier_types = '';
	        		foreach($supplier_type as $k=>$v){

	        			$supplier_types .= $array_mould_other_material[$v].',';
	        		}
	        		echo substr($supplier_types,0,strlen($suppliers_type)-1);

	        	?>
	        	
	        </td>
	        <td><?php echo $row['supplier_address']; ?></td>
	        <td width="4%"><a href="other_supplierae.php?id=<?php echo $supplierid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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