<?php 
	$cavity_length = $_POST['cavity_length_sum'];
	$cavity_width  = $_POST['cavity_width_sum'];
	if(!$cavity_length || !$cavity_width){
		//echo '没有尺寸数据';
		return;
	}
	//计算模架的长和宽
	if($cavity_length < 200) {
		$cavity_len = $cavity_length + 150; 
	} elseif($cavity_length >= 200 && $cavity_length < 300) {
		$cavity_len = $cavity_length + 200;
	} else {
		$cavity_len = $cavity_length + 250;
	}

	if($cavity_width < 200) {
		$cavity_wid = $cavity_width + 150; 
	} elseif($cavity_width >= 200 && $cavity_width < 300) {
		$cavity_wid = $cavity_width + 200;
	} else {
		$cavity_wid = $cavity_width + 250;
	}
	$cavity_hei = 200;
	echo  $cavity_len.'#'.$cavity_wid.'#'.$cavity_hei;
	
 ?>