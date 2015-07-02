<?php require_once('../Connections/connProdOps.php');
	require_once('../inc/functions.php'); ?><?php

//insert statement for when the user wants to link the Status Report to the Maintenance Notification
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "statusReportAdd") && ($_POST['link'] == "y")) {
  $insertSQL = sprintf("INSERT INTO statusreports (employeeID, customerID, subject, applicationID, magicTicket, wrm, notes, actionItems, reportTypeID, startDate, endDate, startTime, endTime, maintenanceNotifID) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['engineer'], "int"),
                       GetSQLValueString($_POST['customers'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['app'], "int"),
                       GetSQLValueString($_POST['magic'], "int"),
                       GetSQLValueString($_POST['wrm'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['actions'], "text"),
                       GetSQLValueString($_POST['reportType'], "int"),
                       GetSQLValueString($_POST['startDate'], "date"),
                       GetSQLValueString($_POST['endDate'], "date"),
                       GetSQLValueString($_POST['startTime'], "date"),
                       GetSQLValueString($_POST['endHour'] . $_POST['endMinute'] . "00", "date"),
                       GetSQLValueString($_POST['maintenance'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());
//insert for when the user doesn't want to link this Status Report to a Maintenance Notification (we still force them to use the startDate from the Maintenance Notification)
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "statusReportAdd") && (isset($_POST['maintenance'])) && ($_POST['link'] != "y")) {
  $insertSQL = sprintf("INSERT INTO statusreports (employeeID, customerID, subject, applicationID, magicTicket, wrm, notes, actionItems, reportTypeID, startDate, endDate, startTime, endTime) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['engineer'], "int"),
                       GetSQLValueString($_POST['customers'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['app'], "int"),
                       GetSQLValueString($_POST['magic'], "int"),
                       GetSQLValueString($_POST['wrm'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['actions'], "text"),
                       GetSQLValueString($_POST['reportType'], "int"),
                       GetSQLValueString($_POST['startDate'], "date"),
                       GetSQLValueString($_POST['endDate'], "date"),
                       GetSQLValueString($_POST['startTime'], "date"),
                       GetSQLValueString($_POST['endHour'] . $_POST['endMinute'] . "00", "date"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());
//insert for when the user is writing a Status Report without originating from a Maintenance Notification
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "statusReportAdd") && (!isset($_POST['maintenance']))) {
  $insertSQL = sprintf("INSERT INTO statusreports (employeeID, customerID, subject, applicationID, startDate, startTime, endDate, endTime, magicTicket, wrm, notes, actionItems, reportTypeID) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['engineer'], "int"),
                       GetSQLValueString($_POST['customers'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['app'], "int"),
                       GetSQLValueString($_POST['startDate'], "date"),
                       GetSQLValueString($_POST['startHour'] . $_POST['startMinute'] . "00", "date"),
                       GetSQLValueString($_POST['endDate'], "date"),
                       GetSQLValueString($_POST['endHour'] . $_POST['endMinute'] . "00", "date"),
                       GetSQLValueString($_POST['magic'], "int"),
                       GetSQLValueString($_POST['wrm'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['actions'], "text"),
                       GetSQLValueString($_POST['reportType'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());
}

//when we're linking this Status Report to a Project Task, populate projects xreference table
if (isset($_POST["project"])) {
  $insertSQL = sprintf("INSERT INTO projecttasksxmodules (projectID, projectTaskID, `module`, moduleID) VALUES (%s, %s, %s, LAST_INSERT_ID())",
                       GetSQLValueString($_POST['project'], "int"),
                       GetSQLValueString($_POST['projectEvent'], "int"),
                       GetSQLValueString($_POST['module'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());
}

	global $lastID;
	if (!isset($_POST['statusReport'])) {
		$lastID = mysql_insert_id();
	} else {
		$lastID = $_POST['statusReport'];
	}

  $insertGoTo = "statusReport.php?function=view&statusReport=" . $lastID . "&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Status Report..</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body><?php
$varStatusReport_rsStatusReport = "97";
if (isset($lastID)) {
  $varStatusReport_rsStatusReport = (get_magic_quotes_gpc()) ? $lastID : addslashes($lastID);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsStatusReport = sprintf("SELECT statusreports.statusReportID, statusreports.employeeID, statusreports.customerID, statusreports.subject, statusreports.applicationID, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, TIME_FORMAT(startTime,'%%k:%%i') as startTime, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate, TIME_FORMAT(endTime,'%%k:%%i') as endTime, statusreports.magicTicket, statusreports.wrm, statusreports.maintenanceNotifID, statusreports.notes, statusreports.actionItems, statusreports.reportTypeID, applications.application, customers.customer, employees.displayName, reporttypes.reportType FROM statusreports, applications, customers, employees, reporttypes WHERE statusReportID = %s AND statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.employeeID=employees.employeeID AND statusreports.reporttypeID=reporttypes.reporttypeID", $varStatusReport_rsStatusReport);
$rsStatusReport = mysql_query($query_rsStatusReport, $connProdOps) or die(mysql_error());
$row_rsStatusReport = mysql_fetch_assoc($rsStatusReport);
$totalRows_rsStatusReport = mysql_num_rows($rsStatusReport);

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
$mail->AddReplyTo("mf-alerts@masflight.com");
$mail->From = "mf-alerts@masflight.com";
$mail->FromName = "Status Report Notification";

$mail->SMTPDebug = true;

if (($_POST['prodOps']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "ProdOps US");
}
if (($_POST['noc']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "Sybase365 NOC");
}
if (($_POST['neteng']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "NetEng");
}
if (($_POST['syseng']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "SysEng");
}
if (($_POST['projMan']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "Project Management");
}
if (($_POST['dev']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "US Software Development");
}
if (($_POST['newLaunch']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "Carrier Launch");
}
if (($_POST['sui']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "SUI");
}
if ((isset($_POST['cc'])) && ($_POST['cc']!=null)) {
	$mail->AddAddress("" . $_POST['cc'] . "", "cc");
}
//$mail->AddReplyTo("rflow@markssystems.com", "Status Reports");
$mail->WordWrap = 75;                                 // set word wrap to 50 characters
$mail->IsHTML(true);                                  // set email format to HTML

$body = "*********************<br />";
if (!isset($_POST['maintenance'])) {
	$body .= "<b>Start Date:</b> " . $row_rsStatusReport['startDate'] . "<br />";
} else {
	$body .= "<b>Start Date:</b> " . $_POST['hiddenUserStartDate'] . "<br />";
}
$body .= "<b>End Date:</b> " . $row_rsStatusReport['endDate'] . "<br />";
if (!isset($_POST['maintenance'])) {
	$body .= "<b>Start Time:</b> " . $row_rsStatusReport['startTime'] . "&nbsp;EST<br />";
} else {
	$body .= "<b>Start Time:</b> " . $_POST['startHour'] . ":" . $_POST['startMinute'] . "&nbsp;EST<br />";
}
$body .= "<b>End Time:</b> " . $row_rsStatusReport['endTime'] . "&nbsp;EST<br /><br />";
if (($row_rsStatusReport['magicTicket'] == "0") || ($row_rsStatusReport['magicTicket'] == "n/a") || ($row_rsStatusReport['magicTicket'] == "N/A")) {
	$body .= "<b>Ticket #:</b> -<br />";
} else {
	$body .= "<b>Ticket #:</b> " . $row_rsStatusReport['magicTicket'] . "<br />";
}
if (($row_rsStatusReport['wrm'] == "0") || ($row_rsStatusReport['wrm'] == "n/a") || ($row_rsStatusReport['wrm'] == "N/A") || ($row_rsStatusReport['wrm'] == "NA")) {
	$body .= "<b>Case #:</b> -<br /><br />";
} else {
	$body .= "<b>Case #:</b> " . $row_rsStatusReport['wrm'] . "<br /><br />";
}
$body .= "<b>Report Type:</b> " . $row_rsStatusReport['reportType'] . "<br />";
$body .= "<b>Application:</b> " . $row_rsStatusReport['application'] . "<br /><br />";
$body .= "<b>Customer:</b> " . $row_rsStatusReport['customer'] . "<br />";
$body .= "<b>Engineer:</b> " . $row_rsStatusReport['displayName'] . "<br /><br />";
$body .= "<b>Notes:</b><br />";
$body .= nl2br($row_rsStatusReport['notes']) . "<br /><br />";
$body .= "<b>Action Items:</b><br />";
$body .= nl2br($row_rsStatusReport['actionItems']) . "<br />";
$body .= "*********************";

if (($row_rsStatusReport['wrm'] == "0") || ($row_rsStatusReport['wrm'] == "n/a") || ($row_rsStatusReport['wrm'] == "N/A") || ($row_rsStatusReport['wrm'] == "NA")) {
	$subject = "Status Reports (US): #" . $row_rsStatusReport['statusReportID'] . " " . $row_rsStatusReport['subject'];
} else {
	$subject = "Status Reports (US): #" . $row_rsStatusReport['statusReportID'] . " " . $row_rsStatusReport['subject'] . " Case #" . $row_rsStatusReport['wrm'];
}

$mail->Subject = $subject;
$mail->Body    = $body;

if(!$mail->Send()){
   echo "This Status Report could not be sent successfully. Please check the errors below .<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Status Report sent successfully! If you can see this message, please contact Adam.";
?></body>
</html><?php
mysql_free_result($rsStatusReport);
?>
