<?php
require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
?><?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "maintenanceNotif1")) {
	$insertSQL = sprintf("INSERT INTO maintenancenotifs (startDate, reason, customerImpact, nocImpact, prodChanges, employeeID, startTime, estimatedHours, estimatedMinutes) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)", GetSQLValueString($_POST['startDate'], "date"), GetSQLValueString($_POST['reason'], "text"), GetSQLValueString($_POST['customerImpact'], "text"), GetSQLValueString($_POST['nocImpact'], "text"), GetSQLValueString($_POST['prodChanges'], "text"), GetSQLValueString($_POST['engineer'], "int"), GetSQLValueString($_POST['startHour'] . $_POST['startMinute'] . "00", "int"), GetSQLValueString($_POST['estHours'], "int"), GetSQLValueString($_POST['estMins'], "int"));

	mysql_select_db($database_connProdOps, $connProdOps);
	$Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

	global $lastID;
	$lastID = mysql_insert_id();

	$insertGoTo = "maintenance.php?sent=y&function=view&maintenance=" . $lastID;
	if (isset($_SERVER['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "maintenanceUpdate")) {
	$updateSQL = sprintf("UPDATE maintenancenotifs SET maintenancenotifs.status=%s WHERE maintenancenotifs.maintenanceNotifsID=%s", GetSQLValueString($_POST['status'], "text"), GetSQLValueString($_POST['maintenance'], "int"));

	mysql_select_db($database_connProdOps, $connProdOps);
	$Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

	$updateGoTo = "../statusReports/statusReport.php?function=add&maintenance=" . $_POST['maintenance'];
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}

//populate projects xreference table
if (isset($_POST['project'])) {
	$insertSQL = sprintf("INSERT INTO projecttasksxmodules (projectID, projectTaskID, `module`, moduleID) VALUES (%s, %s, %s, LAST_INSERT_ID())", GetSQLValueString($_POST['project'], "int"), GetSQLValueString($_POST['projectEvent'], "int"), GetSQLValueString($_POST['module'], "text"));

	mysql_select_db($database_connProdOps, $connProdOps);
	$Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());
}

//maintenance notification
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsMaintenanceNotif = sprintf("SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact, maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%%H:%%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours, maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.maintenanceNotifsID = %s AND maintenancenotifs.employeeID=employees.employeeID", $lastID);
$rsMaintenanceNotif = mysql_query($query_rsMaintenanceNotif, $connProdOps) or die(mysql_error());
$row_rsMaintenanceNotif = mysql_fetch_assoc($rsMaintenanceNotif);
$totalRows_rsMaintenanceNotif = mysql_num_rows($rsMaintenanceNotif);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Submitting Maintenance Notification..</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<?php
		require_once("../inc/class.phpmailer.php");
		require_once("../inc/class.smtp.php");
		require_once("../inc/phpmailer.lang-en.php");

		$mail = new PHPMailer();

		$mail->IsSMTP();			// set mailer to use SMTP
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.gmail.com";
		$mail->Port = "465";
		$mail->Username = "mailer@markssystems.com";
		$mail->Password = "M@ilerMF";
		$mail->AddReplyTo("mailer²markssystems.com");
		$mail->From = "mailer@markssystems.com";
		$mail->FromName = "Maintenance Notification";

		if (($_POST['prodOps'] == 'y')) {
			$mail->AddAddress("rflow@markssystems.com", "masFlight Operations US");
		}
		if (($_POST['noc'] == 'y')) {
			$mail->AddAddress("rflow@markssystems.com", "NOC");
		}
		if (($_POST['neteng'] == 'y')) {
			$mail->AddAddress("rflow@markssystems.com", "NetEng");
		}
		if (($_POST['syseng'] == 'y')) {
			$mail->AddAddress("rflow@markssystems.com", "SysEng");
		}
		if ((isset($_POST['cc'])) && ($_POST['cc'] != null)) {
			$mail->AddAddress("" . $_POST['cc'] . "", "cc");
		}
		$mail->AddReplyTo("MaintenanceNotifications@sap.com", "Maintenance Notifications");

		$mail->WordWrap = 75;			  // set word wrap to 50 characters
		$mail->IsHTML(true);				// set email format to HTML
		$body = "*********************<br />";
		$body .= "<b>Start Date:</b> " . $row_rsMaintenanceNotif['startDate'] . "<br />";
		$body .= "<b>Start Time:</b> " . $row_rsMaintenanceNotif['startTime'] . "&nbsp;EST<br />";
		$body .= "<b>Estimated Duration:</b> ";
		if ($row_rsMaintenanceNotif['estimatedHours'] > 0) {
			$body .= $row_rsMaintenanceNotif['estimatedHours'] . "&nbsp;hour";
			if (($row_rsMaintenanceNotif['estimatedHours'] != 01) || ($row_rsMaintenanceNotif['estimatedHours'] != 1)) {
				$body .= "s";
			}
			$body .= "&nbsp;";
		}
		if ($row_rsMaintenanceNotif['estimatedMinutes'] > 0) {
			$body .= $row_rsMaintenanceNotif['estimatedMinutes'] . "&nbsp;minute";
			if ($row_rsMaintenanceNotif['estimatedMinutes'] != 1) {
				$body .= "s";
			}
			$body .= "&nbsp;";
		}
		$body .= "<br />";
		$body .= "<b>Reason:</b> " . $row_rsMaintenanceNotif['reason'] . "<br />";
		$body .= "<b>Customer Impact:</b> " . $row_rsMaintenanceNotif['customerImpact'] . "<br />";
		$body .= "<b>NOC Impact:</b> " . $row_rsMaintenanceNotif['nocImpact'] . "<br />";
		$body .= "<b>Engineer:</b> " . $row_rsMaintenanceNotif['displayName'] . "<br />";
		$body .= "<b>Production Changes:</b><br />";
		$body .= nl2br($row_rsMaintenanceNotif['prodChanges']) . "<br />";
		$body .= "*********************";

		$txtbody = "*********************<br />";
		$txtbody .= "Start Date: " . $row_rsMaintenanceNotif['startDate'] . "<br />";
		$txtbody .= "Start Time: " . $row_rsMaintenanceNotif['startTime'] . "&nbsp;EST<br />";
		$txtbody .= "Estimated Duration: ";
		if ($row_rsMaintenanceNotif['estimatedHours'] > 0) {
			$txtbody .= $row_rsMaintenanceNotif['estimatedHours'] . "&nbsp;hour";
			if (($row_rsMaintenanceNotif['estimatedHours'] != 01) || ($row_rsMaintenanceNotif['estimatedHours'] != 1)) {
				$txtbody .= "s";
			}
			$txtbody .= "&nbsp;";
		}
		if ($row_rsMaintenanceNotif['estimatedMinutes'] > 0) {
			$txtbody .= $row_rsMaintenanceNotif['estimatedMinutes'] . "&nbsp;minute";
			if ($row_rsMaintenanceNotif['estimatedMinutes'] != 1) {
				$txtbody .= "s";
			}
			$txtbody .= "&nbsp;";
		}
		$txtbody .= "<br />";
		$txtbody .= "Reason: " . $row_rsMaintenanceNotif['reason'] . "<br />";
		$txtbody .= "Customer Impact: " . $row_rsMaintenanceNotif['customerImpact'] . "<br />";
		$txtbody .= "NOC Impact: " . $row_rsMaintenanceNotif['nocImpact'] . "<br />";
		$txtbody .= "Engineer: " . $row_rsMaintenanceNotif['displayName'] . "<br />";
		$txtbody .= "Production Changes:<br />";
		$txtbody .= nl2br($row_rsMaintenanceNotif['prodChanges']) . "<br />";
		$txtbody .= "*********************";

		$subject = "Maintenance Notification (Internal): " . $row_rsMaintenanceNotif['reason'];

		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = $txtbody;

		if (!$mail->Send()) {
			echo "This Maintenance Notification could not be sent successfully. Please check the errors below or contact Adam.<br />";
			echo "Mailer Error: " . $mail->ErrorInfo;
			exit;
		}

		echo "Maintenance Notification sent successfully!";
		?></body>
</html>
<?php
mysql_free_result($rsGetUserFriendlyValues);
