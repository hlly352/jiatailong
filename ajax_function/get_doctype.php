<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
//获取客户状态id
$dataid = trim($_POST['dataid']);

$array_project_data_type = array(array('技术资料',array('客户项目资料','客户模具资料','客户2D图纸','模具规格书')),array('项目启动会',array('客户确认','评审记录','DFM报告','进度规划')),array('设计输出',array('设计计划','设计评审','图纸联络单')),array('加工制造',array('加工工艺','加工计划','装模前检测报告','红丹照片')),array('模具试模',array('机上红丹照片','走水板、样品照片','试模报告')),array('品质控制',array('零件检测报告','产品检测报告','出错报告')),array('模具修改',array('客户改模资料','内部改模资料')),array('模具交付及售后',array('客户确认','出厂检查表','出货放行单','送货单','装箱、装车照片','售后服务表')),array('项目总结',array('跟进流程','总结报告','设计标准')));

	echo json_encode($array_project_data_type[$dataid][1]);


?>