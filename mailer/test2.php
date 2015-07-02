<?php 
date_default_timezone_set('America/New_York');
require("PHPMailer/class.phpmailer.php");

$mail = new PHPMailer();
$mail->IsMAIL();
$mail->SMTPDebug = true;
$mail->Host = "www.gmail.com";
$mail->Username = "melissa.rojano@gmail.com";
$mail->Password = "morenaza";
$mail->FromName = "TEST";
$mail->AddAddress("karen@markssystems.com", "KAREN");
$mail->IsHTML(true);
$mail->Subject = "masFlight" ;
$mail->Body = "mtnesage";

if (!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Sent";
}
?>
