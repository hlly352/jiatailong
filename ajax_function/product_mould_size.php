<?php
if($_SERVER['HTTP_REFERER']){
	$cavity_type = $_POST['cavity_type'];
	$p_length = $_POST['p_length'];
	$p_width = $_POST['p_width'];
	$p_height = $_POST['p_height'];
	if(in_array($cavity_type,array('A','C','E'))){
		$m_length = round(($p_length*1.6)+250,1);
		$m_width = round($p_width+($p_length*0.6)+250,1);
		$m_height = round($p_height+600,1);			
	}elseif(in_array($cavity_type,array('B','D'))){
		$m_length = round($p_length+250,1);
		$m_width = round($p_width+($p_length*0.6)+250,1);
		$m_height = round($p_height+600,1);
	}
	$m_weight = round($m_length*$m_width*$m_height*0.00000785,2);
	echo number_format($m_length,1,'.','').'#'.number_format($m_width,1,'.','').'#'.number_format($m_height,1,'.','').'#'.number_format($m_weight,2,'.',''); 
}
?>