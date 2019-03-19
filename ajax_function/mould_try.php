<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$mould_number = trim($_POST['mould_number']);
$sql = "SELECT `mouldid`,`mould_number` FROM `db_mould` WHERE `mould_number` LIKE '%$mould_number%' ORDER BY `mould_number` ASC";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		echo "<option value=\"".$row['mouldid']."\">".$row['mould_number']."</option>";
	}
}else{
	echo "<option value=\"\">暂无记录</option>";
}
?>