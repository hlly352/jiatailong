<?php
//check id 为正整数
function fun_check_int($str){
	if(!preg_match("/^[1-9]\d*$/", $str)){
		echo "Note:Parameters Error!";
	}else{
		return $str;
	}
}
//获取当前时间
function fun_gettime(){
	return date("Y-m-d H:i:s");
}
//获取当前日期
function fun_getdate(){
	return date("Y-m-d");
}
//检测action值并返回值
function fun_check_action(){
	$array_action = array('add','edit','del','approval','approval_edit','show','mould_excel');
	$action = $_GET['action'];
	if(!in_array($_GET['action'],$array_action,true)){
		echo "System Prompt:Parameter Error!";
		exit();
	}
	return $action;
}
//转换Checkbox值
function fun_convert_checkbox($array_checkbox){
	foreach($array_checkbox as $key=>$value){
		if($key != (count($array_checkbox)-1)){
			$id .= $value.",";
		}else{
			$id .= $value;
		}
	}
	return $id;
}
//删除文件
function fun_delfile($filepath){
	if(is_file($filepath)){
		@unlink($filepath);
	}
}
//文本内容转换html
function htmlcode($str){
	$str = nl2br(str_replace(" ","&nbsp;",htmlspecialchars($str,ENT_QUOTES)));
	return $str;
}
//html转换成textarea
function codetextarea($str){
	$str =htmlspecialchars_decode(str_replace("&nbsp;"," ",str_replace("<br />","",$str)),ENT_QUOTES);
	return $str;
}
//转换字节=>兆
function fun_sizeformat($bytesize){
	$i = 0;
	while(abs($bytesize) >= 1024){
		$bytesize = $bytesize/1024;
		$i++;
		if($i == 4) break;
	}
	$array_unit =array("Bytes","KB","MB","GB","TB");
	$newsize = round($bytesize,2);
	return $newsize . $array_unit[$i];
}
//获取IP地址
function fun_getip(){
	if($_SERVER["HTTP_X_FORWARDED_FOR"]){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif($_SERVER["HTTP_CLIENT_IP"]){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}elseif($_SERVER["REMOTE_ADDR"]){
		$ip = $_SERVER["REMOTE_ADDR"];
	}elseif(getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
    }elseif(getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}elseif(getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
	}else{
		$ip = "Unknown";
	}
	return $ip;
}
//定长数字
function strtolen($num,$str_len){
	$num_len = strlen($num);
	for($i=$num_len;$i<$str_len;$i++){
		$zero .= "0";
	}
	return $zero;
}
//字符串长度截取
function strlen_sub($str,$strlen,$strsub){
	return $str = (mb_strlen($str,'utf8')>$strlen)?mb_substr($str,0,$strsub,'utf-8')."...":$str;
}
//全角空格替换
function str_replace_null($str){
	$array = array('　',' ');
    return str_replace($array,'',$str);
}
//全角括弧替换
function str_replace_array($str){
	$array1 = array("（", "）","　");
    $array2 = array("(", ")"," ");
    return str_replace($array1, $array2, $str);
}
//或者中文首字母
function getfirstchar($s0){
	$firstchar_ord = ord(strtoupper($s0{0})); 
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return strtoupper($s0{0}); 
	$s = iconv("UTF-8","gb2312", $s0); 
	$asc = ord($s{0})*256+ord($s{1})-65536; 
	if($asc>=-20319 and $asc<=-20284)return "A"; 
	if($asc>=-20283 and $asc<=-19776)return "B"; 
	if($asc>=-19775 and $asc<=-19219)return "C"; 
	if($asc>=-19218 and $asc<=-18711)return "D"; 
	if($asc>=-18710 and $asc<=-18527)return "E"; 
	if($asc>=-18526 and $asc<=-18240)return "F"; 
	if($asc>=-18239 and $asc<=-17923)return "G"; 
	if($asc>=-17922 and $asc<=-17418)return "H"; 
	if($asc>=-17417 and $asc<=-16475)return "J"; 
	if($asc>=-16474 and $asc<=-16213)return "K"; 
	if($asc>=-16212 and $asc<=-15641)return "L"; 
	if($asc>=-15640 and $asc<=-15166)return "M"; 
	if($asc>=-15165 and $asc<=-14923)return "N"; 
	if($asc>=-14922 and $asc<=-14915)return "O"; 
	if($asc>=-14914 and $asc<=-14631)return "P"; 
	if($asc>=-14630 and $asc<=-14150)return "Q"; 
	if($asc>=-14149 and $asc<=-14091)return "R"; 
	if($asc>=-14090 and $asc<=-13319)return "S"; 
	if($asc>=-13318 and $asc<=-12839)return "T"; 
	if($asc>=-12838 and $asc<=-12557)return "W"; 
	if($asc>=-12556 and $asc<=-11848)return "X"; 
	if($asc>=-11847 and $asc<=-11056)return "Y"; 
	if($asc>=-11055 and $asc<=-10247)return "Z"; 
	return null; 
}
//把从数据库拿出的字符串转换为数组
  function turn_arr($arr){
		  	$new_arr = explode("$$",$arr);
		  	return $new_arr;
		  }
//取两个数组键名相同的数组,组成一个新的二维数组
function arr_merge($arr1,$arr2){
		 	foreach($arr1 as $key=>$value){
		 		foreach($arr2 as $ks=>$vs){
		 			if(is_array($value)){

		 				if($key == $ks){
			        		$arr1[$key][] = $vs;
			        		$new_arr = $arr1;
			        			}
		 			} else {
		 				if(is_array($vs)){
		 					if($key == $ks){
		 						$arr2[$key][] = $value;
		 						$new_arr = $arr2;
		 					}
		 				} else {
			 				if($key == $ks){
			 					$new_arr[$key][] = $value;
			 					$new_arr[$key][] = $vs;
			 				}
			 			}
		 			}
		 		}
		 	}
		 	return $new_arr;	
		 }
//得到最终的数据
	function getdata($arrs){
		 $exp_arr = [];
		 $final_arr = [];
		for($x = 0; $x<count($arrs);$x++){
			$exp_arr[$x] = explode("$$",$arrs[$x]);
			if($x == 1){
			$final_arr = arr_merge($exp_arr[0],$exp_arr[1]);
			} elseif($x > 1 ){
			$final_arr = arr_merge($final_arr,$exp_arr[$x]);
			}
		}
		 return $final_arr;
			}
//处理多模穴数的数据
	function getin($data){
				if(is_array($data)){
					foreach($data as $v){
						echo $v.'<br/>';
					}
				} else {
					echo $data;
				}
			}
//处理编辑项目信息里的多选框
function doCheckbox($arr,$str,$info,$num = -1){
            foreach($arr as $k=>$v){
            	if(isset($info[$str])){
	            	if(in_array($k,$info[$str])){
	            		$is_check = 'checked';
	            	}else{
	            		$is_check = '';
	            	}
           		 }
                //输出多选框
                echo '<label><input type="checkbox" name="'.$str.'[]" '.$is_check.'  value="'.$k.'">'.$v.'</label>';
                echo $k == $num?'<br>':'';
            }
		}


?>