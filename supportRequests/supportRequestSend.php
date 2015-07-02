<?php require_once('../Connections/connProdOps.php');
	require_once('../inc/functions.php'); ?><?php  
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Support Request..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body><?php
if (( (isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "supportRequestAdd") )) {
  $insertSQL = sprintf("INSERT INTO escalations (dateEscalated, timeEscalated, submittedBy, applicationID, categoryID, subject, description, recreateSteps, whatWasTested, customerImpact, logs, status, ticket, addInfo, deptID, targetDate, priority, customerID) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['dateEscalated'], "date"),
                       GetSQLValueString($_POST['timeEscalated'], "date"),
                       GetSQLValueString($_POST['submittedBy'], "int"),
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['category'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['recreateSteps'], "text"),
                       GetSQLValueString($_POST['whatWasTested'], "text"),
                       GetSQLValueString($_POST['customerImpact'], "text"),
                       GetSQLValueString($_POST['logs'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['ticket'], "text"),
                       GetSQLValueString($_POST['addInfo'], "text"),
                       GetSQLValueString($_POST['dept'], "int"),
                       GetSQLValueString($_POST['targetDate'], "date"),
                       GetSQLValueString($_POST['priority'], "text"),
                       GetSQLValueString($_POST['customer'], "int"));


  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "supportRequest.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

//if updating a Support Request, but not updating the target date
if ( ( (isset($_POST["MM_update"])) && ($_POST["MM_update"] == "supportRequestUpdate") ) && (!isset($_POST['targetDate'])) ) {
	$updateSQL = sprintf("UPDATE escalations SET submittedBy=%s, applicationID=%s, categoryID=%s, subject=%s, description=%s, customerImpact=%s, assignedTo=%s, status=%s, dateClosed=%s, timeClosed=%s, ticket=%s, addInfo=%s, outcome=%s, deptID=%s, priority=%s, customerID=%s, recreateSteps=%s, whatWasTested=%s, logs=%s WHERE escalationID=%s",
                       GetSQLValueString($_POST['submittedBy'], "int"),
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['category'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['customerImpact'], "text"),
                       GetSQLValueString($_POST['assignedTo'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['dateUpdated'], "date"),
                       GetSQLValueString($_POST['timeUpdated'], "date"),
                       GetSQLValueString($_POST['ticket'], "text"),
                       GetSQLValueString($_POST['addInfo'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['dept'], "int"),
                       GetSQLValueString($_POST['priority'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['recreateSteps'], "text"),
                       GetSQLValueString($_POST['whatWasTested'], "text"),
                       GetSQLValueString($_POST['logs'], "text"),
                       GetSQLValueString($_POST['supportRequestID'], "int"));


  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "supportRequest.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
//if we're updating the target date
} elseif ( ( (isset($_POST["MM_update"])) && ($_POST["MM_update"] == "supportRequestUpdate") ) && (isset($_POST['targetDate'])) ) {
	$updateSQL = sprintf("UPDATE escalations SET submittedBy=%s, applicationID=%s, categoryID=%s, subject=%s, description=%s, customerImpact=%s, assignedTo=%s, status=%s, dateClosed=%s, timeClosed=%s, ticket=%s, addInfo=%s, outcome=%s, deptID=%s, priority=%s, customerID=%s, recreateSteps=%s, whatWasTested=%s, logs=%s, targetDate=%s WHERE escalationID=%s",
                       GetSQLValueString($_POST['submittedBy'], "int"),
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['category'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['customerImpact'], "text"),
                       GetSQLValueString($_POST['assignedTo'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['dateUpdated'], "date"),
                       GetSQLValueString($_POST['timeUpdated'], "date"),
                       GetSQLValueString($_POST['ticket'], "text"),
                       GetSQLValueString($_POST['addInfo'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['dept'], "int"),
                       GetSQLValueString($_POST['priority'], "text"),
                       GetSQLValueString($_POST['customer'], "int"),
                       GetSQLValueString($_POST['recreateSteps'], "text"),
                       GetSQLValueString($_POST['whatWasTested'], "text"),
                       GetSQLValueString($_POST['logs'], "text"),
                       GetSQLValueString($_POST['targetDate'], "date"),
                       GetSQLValueString($_POST['supportRequestID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "supportRequest.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

global $lastID;
$lastID = mysql_insert_id();

$varSupportRequest_rsSupportRequest = "1";
if (isset($_POST['supportRequestID'])) {
  $varSupportRequest_rsSupportRequest = (get_magic_quotes_gpc()) ? $_POST['supportRequestID'] : addslashes($_POST['supportRequestID']);
} elseif (isset($lastID)) {
  $varSupportRequest_rsSupportRequest = (get_magic_quotes_gpc()) ? $lastID : addslashes($lastID);
}

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsSupportRequest = sprintf("SELECT escalations.escalationID, escalations.applicationID, applications.application, escalations.subject, escalations.description, escalations.customerImpact, escalations.assignedTo, employees.displayName, escalations.status, escalations.outcome, escalations.customerID, customers.customer, employees.workEmail, departments.email, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.escalationID = %s", GetSQLValueString($varSupportRequest_rsSupportRequest, "int"));
$rsSupportRequest = mysql_query($query_rsSupportRequest, $connProdOps) or die(mysql_error());
$row_rsSupportRequest = mysql_fetch_assoc($rsSupportRequest);
$totalRows_rsSupportRequest = mysql_num_rows($rsSupportRequest);

require_once("../inc/class.phpmailer.php");
require_once("../inc/class.smtp.php");
require_once("../inc/phpmailer.lang-en.php");

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com";
$mail->Port = "465";
$mail->Username = "mailer@markssystems.com";
$mail->Password = "M@ilerMF";
$mail->From = "mf-alerts@masflight.com";
$mail->FromName = "Support Request Notification";
$mail->SMTPDebug = true;
$mail->AddAddress("rflow@markssystems.com", "Support");
$mail->WordWrap = 75;                                 // set word wrap to 50 characters
$mail->IsHTML(true);                                  // set email format to HTML

if ($_POST["MM_insert"] == "supportRequestAdd") {
	$body = "A new Support Request has been submitted. An overview of the Support Request appears below.<br /><br />";
} elseif (($_POST["MM_update"] == "supportRequestUpdate") && ($row_rsSupportRequest['status'] == "Closed")) {
	$body = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been closed. An overview of the Support Request appears below.<br />";
} elseif (($_POST["MM_update"] == "supportRequestUpdate") && ($row_rsSupportRequest['status'] != "Closed")) {
	$body = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been updated. An overview of the Support Request appears below.<br /><br />";
}
$body .= "You can view this &amp; other Support Requests by visiting <a title=\"Production Operation's Support Requests\" href=\"http://".$_SERVER['HTTP_HOST']."/rflow_karen/supportRequests/supportRequests.php?function=view\">http://".$_SERVER['HTTP_HOST']."/rflow_karen/supportRequests/supportRequests.php?function=view</a><br />";
$body .= "Note: Please consult the website for information regarding the status of Support Requests.<br /><br />";
$body .= "**************************<br />";
$body .= "<b>Subject:</b> " . stripslashes($row_rsSupportRequest['subject']) . "<br />";
$body .= "<b>Application:</b> " . $row_rsSupportRequest['application'] . "<br />";
$body .= "<b>Customer:</b> " . $row_rsSupportRequest['customer'] . "<br />";
$body .= "<b>Customer Impact:</b> " . stripslashes($row_rsSupportRequest['customerImpact']) . "<br />";
$body .= "<b>Description:</b> " . stripslashes(nl2br($row_rsSupportRequest['description'])) . "<br />";
$body .= "**************************";
	
if ($_POST["MM_insert"] == "supportRequestAdd") {
	$txtbody = "A new Support Request has been submitted. An overview of the Support Request appears below.<br /><br />";
} elseif (($_POST["MM_update"] == "supportRequestUpdate") && ($_POST['status'] == "Closed")) {
	$txtbody = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been closed. An overview of the Support Request appears below.<br />";
} elseif (($_POST["MM_update"] == "supportRequestUpdate") && ($_POST['status'] != "Closed")) {
	$txtbody = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been updated. An overview of the Support Request appears below.<br /><br />";
}
$txtbody .= "You can view this &amp; other recent Support Requests by visiting http://".$_SERVER['HTTP_HOST']."/rflow_karen/supportRequests/supportRequests.php?function=view<br />";
$txtbody .= "Note: Please consult the website for information regarding the status of Support Requests.<br /><br />";
$txtbody .= "**************************<br />";
$txtbody .= "Subject: " . stripslashes($row_rsSupportRequest['subject']) . "<br />";
$txtbody .= "Application: " . $row_rsSupportRequest['application'] . "<br />";
$txtbody .= "Customer: " . $row_rsSupportRequest['customer'] . "<br />";
$txtbody .= "Customer Impact: " . stripslashes($row_rsSupportRequest['customerImpact']) . "<br />";
$txtbody .= "Description: " . stripslashes(nl2br($row_rsSupportRequest['description'])) . "<br />";
$txtbody .= "**************************";

if ($_POST["MM_insert"] == "supportRequestAdd") {
	$subject = "Support Request (US): " . stripslashes($row_rsSupportRequest['subject']);
} elseif ($_POST["MM_update"] == "supportRequestUpdate") {
	$subject = "Support Request (US): " . stripslashes($row_rsSupportRequest['subject']);
	if ($row_rsSupportRequest['status'] != null) {
		$subject .= " **" . $row_rsSupportRequest['status'] . "**";
	}
}	

$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
   echo "This Support Request could not be sent successfully. Please check the errors below or contact Adam.<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Support Request sent successfully! If you can see this message, please contact Adam.";
?></body>
</html><?php
mysql_free_result($rsSupportRequest);
?>