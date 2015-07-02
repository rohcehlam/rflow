<?php  date_default_timezone_set('America/New_York');
//require("PHPMailer/class.phpmailer.php");
include_once("PHPMailer/class.phpmailer.php");
include_once("PHPMailer/class.smtp.php");

$mail=new phpmailer();
$mensaje="Hola mundo";
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true; // enable SMTP authentication
$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
$mail->Port = 587; // set the SMTP port for the GMAIL server
$mail->Username = "melissa.rojano@gmail.com"; // SMTP account username
$mail->Password = "morenaza"; // SMTP account password
$mail->SetFrom('melissa.rojano@gmail.com', 'List manager');
//$mail->AddReplyTo('info@sitioweb.es', 'List manager');
$mail->AddAddress("rflow@markssystems.com");
$mail->Subject="asunto";//en espass cambiar
$mail->AltBody=$mensaje;
$mail->Wordwrap=50;//numero de lineas
$mail->MsgHTML($mensaje);//formato html para el mensaje


if (!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Sent";
}


?>
