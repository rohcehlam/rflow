<?php require_once('../../Connections/connProdOps.php'); 
require_once('../inc/functions.php'); 

//Initialize code for attaching requirements
$file_name = $HTTP_POST_FILES['filename']['name'];
if ($file_name != null) {
	//filename being uploaded
	$file_name = $HTTP_POST_FILES['filename']['name'];

	//randomly generated number to append to the filename
	$random_digit = rand(0000,9999);

	//set variable as global so we can reference it later
	global $new_filename;
	//combine the number generated above with the filename for a unique filename
	$new_filename = $random_digit.$file_name;

	//set where to store files
	$path = "../uploads/" . $new_filename;
	$webpath = "uploads/" . $new_filename;
	if(copy($HTTP_POST_FILES['filename']['tmp_name'], $path)) {
		echo "Successful<br />";
	} else {
		echo "Error";
	}
} //end if ($file_name != null)

//get upload details
$varUploadID_rsGetUserFriendlyValues = "1";
if (isset($_POST['fileUploadID'])) {
  $varUploadID_rsGetUserFriendlyValues = (get_magic_quotes_gpc()) ? $_POST['fileUploadID'] : addslashes($_POST['fileUploadID']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGetUserFriendlyValues = sprintf("SELECT fileuploads.employeeID, employees.displayName AS submitter, employees.workEmail, DATE_FORMAT(fileuploads.dateUploaded, '%%m/%%d/%%Y') as userDateUploaded FROM fileuploads LEFT JOIN employees ON fileuploads.employeeID=employees.employeeID WHERE fileuploads.fileUploadID=%s", $varUploadID_rsGetUserFriendlyValues);
$rsGetUserFriendlyValues = mysql_query($query_rsGetUserFriendlyValues, $connProdOps) or die(mysql_error());
$row_rsGetUserFriendlyValues = mysql_fetch_assoc($rsGetUserFriendlyValues);
$totalRows_rsGetUserFriendlyValues = mysql_num_rows($rsGetUserFriendlyValues);

//initialize email
require_once("../inc/class.phpmailer.php");
require_once("../inc/class.smtp.php");
require_once("../inc/phpmailer.lang-en.php");

$mail = new PHPMailer();

$mail->IsSMTP();                           // set mailer to use SMTP
$mail->Host = "HQMAIL01.mobile365.corp";  // specify server

$mail->From = "rflow@markssystems.com";
$mail->FromName = "ProdOps Uploads";
$mail->AddAddress("rflow@markssystems.com", "adam shantz");
//$mail->AddAddress("jim.bickings@mobile365.com", "Jim Bickings");
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "fileUploadUpdate")) {
	$mail->AddAddress("" . $row_rsGetUserFriendlyValues['workEmail'] . "", "" . $row_rsGetUserFriendlyValues['submitter'] . "");
	$mail->AddAddress("rflow@markssystems.com", "ProdOps US");
}
$mail->AddReplyTo("rflow@markssystems.com", "ProdOps Uploads");

$mail->WordWrap = 75;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

$body = "**************************<br />";

//when adding a file
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "fileUploadAdd")) {
	$insertSQL = sprintf("INSERT INTO fileuploads (filename, keywords, description, dateUploaded, employeeID) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($webpath, "text"),
                       GetSQLValueString($_POST['keywords'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['dateUploaded'], "date"),
                       GetSQLValueString($_POST['employee'], "int"));

	mysql_select_db($database_connProdOps, $connProdOps);
	$Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

	$insertGoTo = "fileUpload.php?function=add&sent=y";
	if (isset($_SERVER['QUERY_STRING'])) {
	  $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	  $insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));

	$body .= "A new file is available for your review. The GUI to manage information pertaining to this upload can be found by visiting, <a href=\"../../common/fileUploads/fileUploads.php\">../../common/fileUploads/fileUploads.php</a><br />";
}

//when updating the database
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "fileUploadUpdate")) {
	$updateSQL = sprintf("UPDATE fileuploads SET filename=%s, keywords=%s, description=%s WHERE fileUploadID=%s",
                       GetSQLValueString($_POST['filename'], "text"),
                       GetSQLValueString($_POST['keywords'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['fileUploadID'], "int"));

	mysql_select_db($database_connProdOps, $connProdOps);
	$Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

	$updateGoTo = "fileUpload.php?fileUpload=" . $_GET['fileUploadID'] . "&amp;function=view";
	if (isset($_SERVER['QUERY_STRING'])) {
	  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
	  $updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));

}

$body .= "<b>Date Uploaded:</b> " . $row_rsGetUserFriendlyValues['userDateUploaded'] . "<br />";
$body .= "<b>Submitted by:</b> " . $row_rsGetUserFriendlyValues['submitter'] . "<br />";
$body .= "<b>Keywords:</b> " . stripslashes($_POST['keywords']) . "<br />";
$body .= "<b>Description:</b><br />";
$body .= nl2br(stripslashes($_POST['description'])) . "<br />";
$body .= "**************************";

$txtbody = "**************************<br />";
$txtbody .= "<b>Date Uploaded:</b> " . $row_rsGetUserFriendlyValues['userDateUploaded'] . "<br />";
$txtbody .= "<b>Submitted by:</b> " . $row_rsGetUserFriendlyValues['submitter'] . "<br />";
$txtbody .= "<b>Keywords:</b> " . stripslashes($_POST['keywords']) . "<br />";
$txtbody .= "<b>Description:</b><br />";
$txtbody .= nl2br(stripslashes($_POST['description'])) . "<br />";
$txtbody .= "**************************";

$subject = "ProdOps File Server: " . stripslashes($_POST['keywords']);

$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
   echo "This File could not be sent successfully. Please check the errors below or contact Adam.<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "File uploaded successfully!";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting File..</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
</body>
</html><?php
mysql_free_result($rsGetUserFriendlyValues);
?>