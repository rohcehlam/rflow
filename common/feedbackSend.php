<?php require_once('../Connections/connProdOps.php'); ?><?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "feedback")) {
  $insertSQL = sprintf("INSERT INTO feedback (feedbackID, feedback, employeeID, sentOn) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['feedbackID'], "int"),
                       GetSQLValueString($_POST['feedback'], "text"),
                       GetSQLValueString($_POST['employee'], "int"),
                       GetSQLValueString($_POST['sentOn'], "date"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "feedback.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if (isset($_POST['employee'])) {
  $varEngineer_rsGetUserFriendlyValues = (get_magic_quotes_gpc()) ? $_POST['employee'] : addslashes($_POST['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGetUserFriendlyValues = sprintf("SELECT employees.employeeID, employees.displayName FROM employees WHERE employees.employeeID=%s", $varEngineer_rsGetUserFriendlyValues);
$rsGetUserFriendlyValues = mysql_query($query_rsGetUserFriendlyValues, $connProdOps) or die(mysql_error());
$row_rsGetUserFriendlyValues = mysql_fetch_assoc($rsGetUserFriendlyValues);
$totalRows_rsGetUserFriendlyValues = mysql_num_rows($rsGetUserFriendlyValues);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Feedback..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body><?php
require_once("../inc/class.phpmailer.php");
require_once("../inc/class.smtp.php");
require_once("../inc/phpmailer.lang-en.php");

$mail = new PHPMailer();

$mail->IsSMTP();                           // set mailer to use SMTP
$mail->Host = "www.gmail.com";  // specify server

$mail->From = "rflow@markssystems.com.com";
$mail->FromName = "ProdOps Feedback";
$mail->AddAddress("rflow@markssystems.com", "Adam Shantz");
$mail->AddAddress("rflow@markssystems.com", "Mister Lewis");
$mail->AddAddress("rflow@markssystems.com", "Rodrigo Navarro");
$mail->AddAddress("rflow@markssystems.com", "Jim Bickings");
$mail->AddReplyTo("rflow@markssystems.com.com", "ProdOps Feedback");

$mail->WordWrap = 75;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

$body = "**************************<br />";
$body .= "<b>Request Sent:</b> " . $_POST['sentOn'] . "<br />";
$body .= "<b>Engineer:</b> " . $row_rsGetUserFriendlyValues['displayName'] . "<br /><br />";
$body .= "<b>Feedback:</b><br />";
$body .= nl2br($_POST['feedback']) . "<br />";
$body .= "**************************";

$txtbody = "**************************<br />";
$txtbody .= "<b>Request Sent:</b> " . $_POST['sentOn'] . "<br />";
$txtbody .= "<b>Engineer:</b> " . $row_rsGetUserFriendlyValues['displayName'] . "<br /><br />";
$txtbody .= "<b>Feedback:</b><br />";
$txtbody .= nl2br($_POST['feedback']) . "<br />";
$txtbody .= "**************************";

$subject = "ProdOps Website Feedback: " . $row_rsGetUserFriendlyValues['displayName'];

$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
	echo "This Feedback could not be sent successfully. Please check the errors below or contact Adam.<br />";
	echo "Mailer Error: " . $mail->ErrorInfo;
	exit;
}

echo "Feedback sent successfully! If you can see this message, please contact Adam.";
?></body>
</html><?php
mysql_free_result($rsGetUserFriendlyValues);
?>