<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
require_once("../inc/class.email.php");

//error_reporting(E_ALL);

$args = array(
	'MM_insert' => FILTER_SANITIZE_SPECIAL_CHARS,
	'link' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customers' => FILTER_SANITIZE_SPECIAL_CHARS,
	'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	'app' => FILTER_SANITIZE_SPECIAL_CHARS,
	'magic' => FILTER_SANITIZE_SPECIAL_CHARS,
	'wrm' => FILTER_SANITIZE_SPECIAL_CHARS,
	'notes' => FILTER_SANITIZE_SPECIAL_CHARS,
	'actions' => FILTER_SANITIZE_SPECIAL_CHARS,
	'reportType' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'endDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startTime' => FILTER_SANITIZE_SPECIAL_CHARS,
	'endHour' => FILTER_SANITIZE_SPECIAL_CHARS,
	'maintenance' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startHour' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startMinute' => FILTER_SANITIZE_SPECIAL_CHARS,
	'endHour' => FILTER_SANITIZE_SPECIAL_CHARS,
	'endMinute' => FILTER_SANITIZE_SPECIAL_CHARS,
	'project' => FILTER_SANITIZE_SPECIAL_CHARS,
	'projectEvent' => FILTER_SANITIZE_SPECIAL_CHARS,
	'module' => FILTER_SANITIZE_SPECIAL_CHARS,
	'statusReport' => FILTER_SANITIZE_SPECIAL_CHARS,
	'prodOps' => FILTER_SANITIZE_SPECIAL_CHARS,
	'noc' => FILTER_SANITIZE_SPECIAL_CHARS,
	'syseng' => FILTER_SANITIZE_SPECIAL_CHARS,
	'neteng' => FILTER_SANITIZE_SPECIAL_CHARS,
	'cc' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_post = filter_input_array(INPUT_POST, $args);

$my_server = filter_input_array(INPUT_SERVER, array(
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	)
);

/*
  echo "<pre>";
  print_r($my_post);
  echo "</pre>\n";
 */

//insert statement for when the user wants to link the Status Report to the Maintenance Notification
if ((isset($my_post["MM_insert"])) && ($my_post["MM_insert"] == "statusReportAdd") && ($my_post['link'] == "y")) {
	$insertSQL = "INSERT INTO statusreports (employeeID, customerID, subject, applicationID, magicTicket, wrm, notes, actionItems, reportTypeID, startDate, endDate"
		. ", startTime, endTime, maintenanceNotifID) VALUES ({$my_post['engineer']}, {$my_post['customers']}, '{$my_post['subject']}', {$my_post['app']}"
		. ", {$my_post['magic']}, {$my_post['wrm']}, '{$my_post['notes']}', '{$my_post['actions']}', {$my_post['reportType']}, '{$my_post['startDate']}'"
		. ", '{$my_post['endDate']}', '{$my_post['startTime']}', '{$my_post['endHour']}{$my_post['endMinute']}', {$my_post['maintenance']})";
	//echo $insertSQL;
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//insert for when the user doesn't want to link this Status Report to a Maintenance Notification (we still force them to use the startDate from the Maintenance Notification)
} elseif ((isset($my_post["MM_insert"])) && ($my_post["MM_insert"] == "statusReportAdd") && (isset($my_post['maintenance'])) && ($my_post['link'] != "y")) {
	$insertSQL = "INSERT INTO statusreports (employeeID, customerID, subject, applicationID, magicTicket, wrm, notes, actionItems, reportTypeID, startDate, endDate"
		. ", startTime, endTime) VALUES ({$my_post['engineer']}, {$my_post['customers']}, '{$my_post['subject']}', {$my_post['app']}"
		. ", {$my_post['magic']}, {$my_post['wrm']}, '{$my_post['notes']}', '{$my_post['actions']}', {$my_post['reportType']}, '{$my_post['startDate']}'"
		. ", '{$my_post['endDate']}', '{$my_post['startTime']}', '{$my_post['endHour']}{$my_post['endMinute']}')";
	//echo $insertSQL;
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//insert for when the user is writing a Status Report without originating from a Maintenance Notification
} elseif ((isset($my_post["MM_insert"])) && ($my_post["MM_insert"] == "statusReportAdd") && (!isset($my_post['maintenance']))) {
	$my_post['startTime'] = (isset($my_post['startHour']) && $my_post['startHour'] != '' ? $my_post['startHour'] : '00') . ':' . (isset($my_post['startMinute']) && $my_post['startMinute'] != '' ? $my_post['startMinute'] : '00') . ':00';
	$my_post['endTime'] = (isset($my_post['endHour']) && $my_post['endHour'] != '' ? $my_post['endHour'] : '00') . ':' . (isset($my_post['endMinute']) && $my_post['endMinute'] != '' ? $my_post['endMinute'] : '00') . ':00';
	$helper_insert = array('engineer', 'customers', 'subject', 'app', 'startDate', 'startTime', 'endDate', 'endTime', 'magic', 'wrm', 'notes', 'actions', 'reportType');
	foreach ($helper_insert as $data) {
		$temp[$data] = (isset($my_post[$data]) && $my_post[$data] != '' && $my_post[$data]) ? "'{$my_post[$data]}'" : 'NULL';
	}
	$insertSQL = "INSERT INTO statusreports (employeeID, customerID, subject, applicationID, startDate, startTime, endDate, endTime, magicTicket, wrm, notes"
		. ", actionItems, reportTypeID) VALUES (" . implode(', ', $temp) . ")";
	//echo $insertSQL;
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
}

//when we're linking this Status Report to a Project Task, populate projects xreference table
if (isset($my_post["project"])) {
	$insertSQL = "INSERT INTO projecttasksxmodules (projectID, projectTaskID, `module`, moduleID) VALUES ({$my_post['project']}, {$my_post['projectEvent']}"
		. ", '{$my_post['module']}', LAST_INSERT_ID())";
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
}

global $lastID;
if (!isset($my_post['statusReport'])) {
	$lastID = $conn->insert_id;
} else {
	$lastID = $my_post['statusReport'];
}

$insertGoTo = "statusReport.php?function=view&statusReport=" . $lastID . "&sent=y";
if (isset($my_server['QUERY_STRING'])) {
	$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	$insertGoTo .= $my_server['QUERY_STRING'];
}
header(sprintf("Location: %s", $insertGoTo));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title>Submitting Status Report..</title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body><?php
		  $varStatusReport_rsStatusReport = "97";
		  if (isset($lastID)) {
			  $varStatusReport_rsStatusReport = addslashes($lastID);
		  }
		  $query_rsStatusReport = "SELECT statusreports.statusReportID, statusreports.employeeID, statusreports.customerID, statusreports.subject, statusreports.applicationID"
			  . ", DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, TIME_FORMAT(startTime,'%k:%i') as startTime, DATE_FORMAT(endDate, '%m/%d/%Y') as endDate"
			  . ", TIME_FORMAT(endTime,'%k:%i') as endTime, statusreports.magicTicket, statusreports.wrm, statusreports.maintenanceNotifID, statusreports.notes"
			  . ", statusreports.actionItems, statusreports.reportTypeID, applications.application, customers.customer, employees.displayName, reporttypes.reportType"
			  . " FROM statusreports, applications, customers, employees, reporttypes"
			  . " WHERE statusReportID = {$varStatusReport_rsStatusReport} AND statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID"
			  . "  AND statusreports.employeeID=employees.employeeID AND statusreports.reporttypeID=reporttypes.reporttypeID";
		  $rsStatusReport = $conn->query($query_rsStatusReport) or die($conn->error);
		  $row_rsStatusReport = $rsStatusReport->fetch_assoc();
		  $totalRows_rsStatusReport = $rsStatusReport->num_rows;

		  $email = new tEmail('Status Report');

		  if (($my_post['prodOps'] == 'y')) {
			  $email->AddAddress("karen@markssystems.com", "Tech Support");
		  }
		  if (($my_post['noc'] == 'y')) {
			  $email->AddAddress("karen@markssystems.com", "Product Dev");
		  }
		  if (($my_post['neteng'] == 'y')) {
			  $email->AddAddress("karen@markssystems.com", "Sales");
		  }
		  if (($my_post['syseng'] == 'y')) {
			  $email->AddAddress("karen@markssystems.com", "Projects");
		  }
		  if ((isset($my_post['cc'])) && ($my_post['cc'] != null)) {
			  $email->AddAddress("" . $my_post['cc'] . "", "cc");
		  }

		  $body = "*********************<br />";
		  if (!isset($my_post['maintenance'])) {
			  $body .= "<b>Start Date:</b> " . $row_rsStatusReport['startDate'] . "<br />";
		  } else {
			  $body .= "<b>Start Date:</b> " . $my_post['hiddenUserStartDate'] . "<br />";
		  }
		  $body .= "<b>End Date:</b> " . $row_rsStatusReport['endDate'] . "<br />";
		  if (!isset($my_post['maintenance'])) {
			  $body .= "<b>Start Time:</b> " . $row_rsStatusReport['startTime'] . "&nbsp;UTC<br />";
		  } else {
			  $body .= "<b>Start Time:</b> " . $my_post['startHour'] . ":" . $my_post['startMinute'] . "&nbsp;UTC<br />";
		  }
		  $body .= "<b>End Time:</b> " . $row_rsStatusReport['endTime'] . "&nbsp;UTC<br /><br />";
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
			  $subject = "Status Reports #" . $row_rsStatusReport['statusReportID'] . " " . $row_rsStatusReport['subject'];
		  } else {
			  $subject = "Status Reports #" . $row_rsStatusReport['statusReportID'] . " " . $row_rsStatusReport['subject'] . " Case #" . $row_rsStatusReport['wrm'];
		  }

		  $email->set_subject($subject);
		  $email->set_body($body);

		  if (!$email->send()) {
			  ?>
			  <div class='alert alert-danger' role='alert'>
					<strong>Error!</strong> This Status Report could not be sent successfully. Please check the errors below or contact El Chapulin Colorado.<br />
					<strong>Mailer Error:</strong> <?php echo $email->get_error(); ?>
			  </div>
			  <?php
			  exit;
		  }
		  ?>
		  <div class="alert alert-success" role="alert">
				<strong>Success!</strong> Maintenance Notification sent successfully!
		  </div>
	 </body>
</html>