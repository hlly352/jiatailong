<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_SERVER['HTTP_REFERER']){
  $check_status_do = $_POST['check_status_do'];
  $inoutid = $_POST['inoutid'];
  if($check_status_do == 'n'){
    $sql = "UPDATE `db_material_inout` SET `check_status` = 1 WHERE `inoutid` = '$inoutid'";
    $check_status = 'check_y';
  }elseif($check_status_do == 'y'){
    $sql = "UPDATE `db_material_inout` SET `check_status` = 0 WHERE `inoutid` = '$inoutid'";
    $check_status = 'check_n';
  }
  $db->query($sql);
  if($db->affected_rows){
    echo "<img src=\"images/".$check_status.".png\" id=\"".$check_status.'_'.$inoutid."\" style=\"cursor:pointer;\">";
  }
}
?>
