<?php 
	$cavity_length = $_POST['cavity_length_sum'];
	$cavity_width  = $_POST['cavity_width_sum'];
	$cavity_height = $_POST['max_height'];
	if(!$cavity_length || !$cavity_width){
		//echo '没有尺寸数据';
		return;
	}
	//计算模架的长
	if($cavity_length < 200) {
		$cavity_len = $cavity_length + 150; 
	} elseif($cavity_length >= 200 && $cavity_length < 300) {
		$cavity_len = $cavity_length + 200;
	} else {
		$cavity_len = $cavity_length + 250;
	}
	//计算模架的宽
	if($cavity_width < 200) {
		$cavity_wid = $cavity_width + 150; 
	} elseif($cavity_width >= 200 && $cavity_width < 300) {
		$cavity_wid = $cavity_width + 200;
	} else {
		$cavity_wid = $cavity_width + 250;
	}
	//计算模架的高
	if($cavity_height < 50){
		 $cavity_hei = $cavity_height + 400;
	} elseif($cavity_height >= 50 && $cavity_height < 100) {
		$cavity_hei = $cavity_height + 500;
	} elseif($cavity_height >= 100 && $cavity_height < 150) {
		$cavity_hei = $cavity_height + 600;
	} elseif($cavity_height >= 150) {
		$cavity_hei = $cavity_height + 700;
	}
	echo  $cavity_len.'#'.$cavity_wid.'#'.$cavity_hei;
	
 ?>