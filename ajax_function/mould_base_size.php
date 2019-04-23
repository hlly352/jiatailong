<?php 
	$cavity_length = $_POST['cavity_length_sum'];
	$cavity_width  = $_POST['cavity_width_sum'];
	$cavity_height = $_POST['max_height'];
	if(!$cavity_length || !$cavity_width){
		//echo '没有尺寸数据';
		return;
	}
	//计算模架的长
	$cavity_len = round($cavity_length * 1.15 + 100);
	//计算模架的宽
	$cavity_wid = round($cavity_width * 1.15 + 100);
	//计算模架的高
	$cavity_hei = round($cavity_height * 2.5 + 400);
	echo  $cavity_len.'#'.$cavity_wid.'#'.$cavity_hei;
	
 ?>