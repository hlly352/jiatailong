<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
require_once '../function/function.php';

send('hr.04@hl.com','hr.04@hl.com','主题','内容');
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require '../class/PHPMailer/src/Exception.php';
// require '../class/PHPMailer/src/PHPMailer.php';
// require '../class/PHPMailer/src/SMTP.php';

// $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
// try {
//     //服务器配置
//     $mail->CharSet ="UTF-8";                     //设定邮件编码
//     $mail->SMTPDebug = 0;                        // 调试模式输出
//     $mail->isSMTP();                             // 使用SMTP
//     $mail->Host = '192.168.1.22';                // SMTP服务器
//     $mail->SMTPAuth = true;                      // 允许 SMTP 认证
//     $mail->Username = 'hr.04@hl.com';                // SMTP 用户名  即邮箱的用户名
//     $mail->Password = 'xierlin';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
//     $mail->SMTPSecure = '';                    // 允许 TLS 或者ssl协议
//     $mail->Port = 25;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

//     $mail->setFrom('hr.04@hl.com', 'Mailer');  //发件人
//     $mail->addAddress('hr.04@hl.com', 'Joe');  // 收件人
//     //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
//     $mail->addReplyTo('xxxx@163.com', 'info'); //回复的时候回复给哪个邮箱 建议和发件人一致
//     //$mail->addCC('cc@example.com');                    //抄送
//     //$mail->addBCC('bcc@example.com');                    //密送

//     //发送附件
//     // $mail->addAttachment('../xy.zip');         // 添加附件
//     // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

//     //Content
//     $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
//     $mail->Subject = '这里是邮件标题' . time();
//     $mail->Body    = '<h1 style="color:red">这里是邮件内容</h1>' . date('Y-m-d H:i:s');
//     $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

//     $mail->send();
//     echo '邮件发送成功';
// } catch (Exception $e) {
//     echo '邮件发送失败: ', $mail->ErrorInfo;
//}
?>