<?php 
date_default_timezone_set('America/New_York');
require("PHPMailer/class.phpmailer.php");

$send_to_name = "RFLOW";
$mensajeHtml = "MENSAJE TEST";
$email = "rflow@markssystems.com";

$mail = new PHPMailer();
$mail->SMTPDebug = true;
$mail->Host = "www.gmail.com";
$mail->Username = "melissa.rojano@gmail.com";
$mail->Password = "morenaza";
$mail->FromName = "Status Report";
$mail->AddAddress($email, $send_to_name);
$mail->IsHTML(true);
$mail->Subject = "Status Report 1";
$mail->Body = $mensajeHtml;

if (!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Sent";
}

?>