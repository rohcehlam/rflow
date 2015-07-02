<?php require_once('../inc/functions.php'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Status Report..</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body><?php

require_once("../inc/PHPMailer/class.phpmailer.php");

$mail = new PHPMailer();
//$mail->IsSMTP();
$mail->SMTPDebug = true;
$mail->Host = "www.gmail.com";
$mail->Username = "melissa.rojano@gmail.com";
$mail->Password = "morenaza";
$mail->From = "rflow@markssystems.com";
$mail->FromName = "Status Reports";
$mail->AddAddress("rflow@markssystems.com");
//$mail->AddReplyTo("rflow@markssystems.com", "Status Reports");
$mail->WordWrap = 75;                                 // set word wrap to 50 characters
$mail->IsHTML(true);                                  // set email format to HTML
$body = "TEST MESSAGE";
$body .= "<b>Ticket #: TEST </b> <br />";
$subject = "Status Reports (US)";
$mail->Subject = $subject;
$mail->Body    = $body;

if(!$mail->Send()){
   echo "This Status Report could not be sent successfully. Please check the errors below or contact Adam.<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}
else {
	echo "Status Report sent successfully! If you can see this message, please contact Adam.";
}
?></body>
</html>
