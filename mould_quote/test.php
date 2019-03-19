<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#button").click(function(){
							 
		$("#aa").html('aa');
	})
	$("#aa").bind("span propertychange",function(){
							 
		alert('ddd');
	})
})
</script>
<title>无标题文档</title>
</head>

<body>
<span id="aa">aa</span>
<input type="button" name="button" id="button" value="button" />
<?php
$a = '17.24%';
echo (float)$a/100;
?>
</body>
</html>