<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once "../class/class.phpmailer.php";
$sql = "SELECT `emailid`,`email_name`,`email_subject`,`email_content` FROM `db_email` ORDER BY `emailid` ASC LIMIT 0,1";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$emailid = $row['emailid'];
		$email_name = $row['email_name'];
		$email_subject = $row['email_subject'];
		$email_content = $row['email_content'];
		if($email_name){
			$array_email = explode('@',$email_name);
			$email_type = $array_email[1];
			if($email_type == 'jotylong.com'){
				$email_host = 'mail.jotylong.com';
				$email_username = 'webmaster@jotylong.com';
				$email_password = '1qaz2wsx';
				$email_from = 'webmaster@jotylong.com';
			}elseif($email_type = 'jtl.com'){
				$email_host = '192.168.1.2';
				$email_username = 'webmaster@jtl.com';
				$email_password = '1qaz2wsx';
				$email_from = 'webmaster@jtl.com';
			}
			$mail = new PHPMailer();
			$mail->IsSMTP();					// 启用SMTP
			$mail->Host = $email_host;			//SMTP服务器
			$mail->SMTPAuth = true;					//开启SMTP认证
			$mail->Username = $email_username;			// SMTP用户名
			$mail->Password = $email_password;				// SMTP密码
			$mail->From = $email_from;			//发件人地址
			$mail->FromName = "网站管理员";				//发件人
			$mail->CharSet ="utf-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置
			$mail->Encoding = "base64";
			$mail->WordWrap = 50;					//设置每行字符长度
			$mail->IsHTML(true);					// 是否HTML格式邮件
			$mail->AddAddress($email_name);
			$mail->Subject = $email_subject;
			$mail->Body = "<p style=\"font-size:13px; font-family:微软雅黑, 宋体\">内网信息提醒：".$email_content."【信息来源内网,请勿回复】"."</p>";
			/*
			if(!$mail->Send()){
				echo "Message could not be sent. <p>";echo "Mailer Error: " . $mail->ErrorInfo;
				exit;
			}
			*/
		}
		$sql_del = "DELETE FROM `db_email` WHERE `emailid` = '$emailid'";
		$db->query($sql_del);
	}
}
?>