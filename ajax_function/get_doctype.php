<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
//获取客户状态id
$dataid = trim($_POST['dataid']);

$array_project_data_type = array(array('技术资料',array('project_data'=>'客户项目资料','mould_data'=>'客户模具资料','drawing'=>'客户2D图纸')),array('项目启动会',array('project_review'=>'评审记录','dfm_report'=>'DFM报告','progress'=>'进度规划','customer_confirm'=>'客户确认')),array('模具试模',array('red_photo'=>'机上红丹照片','sample_photo'=>'走水板、样品照片','trial_mode'=>'试模报告')),array('模具修改',array('customer_modify'=>'客户改模资料','own_modify'=>'内部改模资料')),array('模具交付及售后',array('after_sale_confirm'=>'客户确认','out_factory'=>'出厂检查表','car_photo'=>'装箱、装车照片','delivery_note'=>'放行单、送货单','service'=>'售后服务表','customer_indication'=>'客户交付指示')),array('项目总结',array('project_sum'=>'总结报告')));


	echo json_encode($array_project_data_type[$dataid][1]);


?>