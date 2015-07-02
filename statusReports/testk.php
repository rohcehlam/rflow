<?php
require_once("../inc/class.phpmailer.php");
require_once("../inc/class.smtp.php");
require_once("../inc/phpmailer.lang-en.php");

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';

$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com";
$mail->Port = "465";
$mail->Username = "mailer@markssystems.com";
$mail->Password = "M@ilerMF";
$mail->AddReplyTo("mf-alerts@masflight.com");
$mail->From = "mf-alerts@masflight.com";
$mail->FromName = "masflight.com Reports";
//$mail->SMTPDebug = true;
$mail->AddAddress("rflow@markssystems.com", "SUI");
//$mail->AddAddress("orlando@markssystems.com", "Orlando Jimenez");
//$mail->AddAddress("karen@markssystems.com", "Karen Carvajal");
//$mail->AddReplyTo("rflow@markssystems.com", "Status Reports");
$mail->WordWrap = 75;
$mail->IsHTML(true);

$body = <<< EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur auctor erat ac mauris vulputate tincidunt. Vivamus auctor sollicitudin pellentesque. Ut ultricies urna quam, ut elementum magna dignissim quis. Cras pulvinar ipsum id quam semper accumsan. Morbi dictum leo a pretium aliquet. Etiam fringilla vel lectus vel ultricies. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam sollicitudin, neque ac varius venenatis, erat diam vehicula lectus, et finibus purus ipsum quis sem. Integer tristique odio est, eget ornare magna aliquet eget. Donec consequat risus turpis, id volutpat mauris tincidunt et. Phasellus elementum odio ac ligula volutpat finibus. Nam id enim id quam volutpat pharetra in quis neque. Aenean sit amet iaculis enim, eu tincidunt massa.

Morbi consequat massa ornare mi rutrum, tincidunt fermentum orci commodo. Morbi euismod ex at nibh rutrum, nec cursus nisl pulvinar. Praesent tincidunt sit amet ante sed iaculis. Vestibulum libero mauris, vulputate quis nibh in, gravida ullamcorper dui. Aliquam viverra nec nisi in vehicula. Nunc faucibus, urna nec fringilla sagittis, neque neque tincidunt nibh, sit amet elementum diam sem id urna. Suspendisse vel pellentesque neque, nec facilisis massa. Duis sed lobortis nulla, id cursus mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vestibulum cursus est in eros lacinia, sed blandit justo sodales. Vivamus sapien magna, luctus vestibulum dignissim a, tempor ut nulla. Aenean eget lobortis quam, eget euismod lacus. Nulla porttitor rhoncus mi ac varius. Vivamus non erat eros. Vivamus est eros, aliquam accumsan lectus eu, mattis fringilla lectus. Nam nunc eros, luctus ac venenatis at, mollis ac nibh.

Etiam vel risus nec libero mattis mattis nec vitae nunc. Etiam tincidunt laoreet enim, eu venenatis ligula facilisis et. Phasellus bibendum efficitur feugiat. Duis rhoncus in massa auctor tristique. Proin posuere nisi eget efficitur accumsan. Suspendisse dui arcu, pretium eu lectus bibendum, pharetra tempus purus. Aenean vel odio vel ipsum congue volutpat. Vestibulum porttitor quis erat quis facilisis. Pellentesque aliquet dui vel pretium gravida. Duis nec rhoncus eros, malesuada finibus sem. Etiam sed ultricies turpis.

Maecenas a luctus massa. Maecenas vel nisi pharetra dui congue tincidunt. Suspendisse non tempor sem, in pretium risus. Pellentesque vitae feugiat lacus. Fusce placerat justo id lectus aliquet, nec interdum nisl congue. Curabitur nec nisi sagittis, mollis mauris ut, malesuada mi. Donec augue augue, feugiat ac orci at, semper venenatis diam. Nulla at blandit nisi. Nam ac urna neque. Donec venenatis massa eget orci rhoncus ultricies. Aliquam ut ipsum volutpat, blandit orci eu, porttitor quam. Aenean congue ante ac auctor volutpat. Pellentesque ac ipsum faucibus, pretium magna sed, sollicitudin sapien. Sed elementum justo mauris, eget aliquam erat feugiat vel.

Suspendisse tempus metus eu dolor volutpat, vitae vehicula metus ultricies. Proin at lacus quis turpis vestibulum porttitor ornare ac orci. Nulla facilisi. Praesent tincidunt pulvinar velit in scelerisque. In ullamcorper tincidunt magna. Quisque nisl lorem, ullamcorper et turpis finibus, scelerisque dapibus enim. Aenean leo dui, faucibus vitae nibh ut, vehicula scelerisque mauris. Vestibulum pulvinar et orci ut lacinia. Curabitur at odio varius, gravida ex quis, molestie quam. Curabitur sed odio at ligula mollis vulputate. Cras a malesuada ex, nec varius turpis. Fusce bibendum, felis eu laoreet suscipit, dolor elit consectetur nunc, et hendrerit purus massa quis augue. Sed convallis cursus erat vitae sollicitudin. Quisque semper dolor at ex commodo, vel semper nunc venenatis. Pellentesque eleifend nunc id ligula congue, non rutrum sapien commodo. 
EOD;
//$body = "Some Random Text message *********************<br />";
$subject = "Status Reports (US): ";

$mail->Subject = $subject;
$mail->Body    = $body;

if($mail->Send()){
	echo "Email Sent";
} else {
	echo "This Status Report could not be sent successfully";
	echo "Mailer Error: " . $mail->ErrorInfo;
}

?>
