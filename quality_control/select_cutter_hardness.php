<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_SERVER['HTTP_REFERER']){
	$texture = $_POST['texture'];
	$sql = "SELECT `hardnessid`,`hardness` FROM `db_cutter_hardness` WHERE `texture` = '$texture' ORDER BY `texture` ASC,`hardnessid` DESC";
	$result = $db->query($sql);
	echo "<option value=\"\">请选择</option>";
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			echo "<option value=\"".$row['hardnessid']."\">".$row['hardness']."</option>";
		}
	}
}
?>