<?php
$mail = new PHPMailer();
$mail->IsSMTP();					// 启用SMTP
$mail->Host = "210.51.52.136";			//SMTP服务器
$mail->SMTPAuth = true;					//开启SMTP认证
$mail->Username = "webmaster@su.shuanglin.com";			// SMTP用户名
$mail->Password = "fugeone";				// SMTP密码
$mail->From = "webmaster@su.shuanglin.com";			//发件人地址
$mail->FromName = "网站管理员";				//发件人
$mail->CharSet ="utf-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置
$mail->Encoding = "base64";
$mail->WordWrap = 50;					//设置每行字符长度
$mail->IsHTML(true);					// 是否HTML格式邮件
?>