<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
require_once("../inc/class.email.php");

$args = array(
	'MM_insert' => FILTER_SANITIZE_SPECIAL_CHARS,
	'MM_update' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'reason' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customerImpact' => FILTER_SANITIZE_SPECIAL_CHARS,
	'nocImpact' => FILTER_SANITIZE_SPECIAL_CHARS,
	'prodChanges' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startHour' => FILTER_SANITIZE_SPECIAL_CHARS,
	'startMinute' => FILTER_SANITIZE_SPECIAL_CHARS,
	'estHours' => FILTER_SANITIZE_SPECIAL_CHARS,
	'estMins' => FILTER_SANITIZE_SPECIAL_CHARS,
	'project' => FILTER_SANITIZE_SPECIAL_CHARS,
	'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	'maintenance' => FILTER_SANITIZE_SPECIAL_CHARS,
	'projectEvent' => FILTER_SANITIZE_SPECIAL_CHARS,
	'module' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_post = filter_input_array(INPUT_POST, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	), true);

if ((isset($my_post["MM_insert"])) && ($my_post["MM_insert"] == "maintenanceNotif1")) {
	$insertSQL = "INSERT INTO maintenancenotifs (startDate, reason, customerImpact, nocImpact, prodChanges, employeeID, startTime, estimatedHours, estimatedMinutes) VALUES"
		. " ('{$my_post['startDate']}', '{$my_post['reason']}', '{$my_post['customerImpact']}', '{$my_post['nocImpact']}', '{$my_post['prodChanges']}', {$my_post['engineer']}"
		. ", {$my_post['startHour']}{$my_post['startMinute']}00, {$my_post['estHours']}, {$my_post['estMins']})";
	//TODO Default Values?
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

	global $lastID;
	$lastID = $conn->insert_id;

	$insertGoTo = "maintenance.php?sent=y&function=view&maintenance=" . $lastID;
	if (isset($my_server['QUERY_STRING'])) {
		$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
		$insertGoTo .= $my_server['QUERY_STRING'];
	}
	header("Location: $insertGoTo");
}

if ((isset($my_post["MM_update"])) && ($my_post["MM_update"] == "maintenanceUpdate")) {
	$updateSQL = "UPDATE maintenancenotifs SET maintenancenotifs.status='{$my_post['status']}' WHERE maintenancenotifs.maintenanceNotifsID={$my_post['maintenance']}";
	$Result1 = $conn->query($updateSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

	$updateGoTo = "../statusReports/statusReport.php?function=add&maintenance=" . $my_post['maintenance'];
	if (isset($my_server['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $my_server['QUERY_STRING'];
	}
	header("Location: $updateGoTo");
}

//populate projects xreference table
if (isset($my_post['project'])) {
	$insertSQL = "INSERT INTO projecttasksxmodules (projectID, projectTaskID, `module`, moduleID) VALUES"
		. " ({$my_post['project']}, {$my_post['projectEvent']}, '{$my_post['module']}', LAST_INSERT_ID())";
	$Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
}

//maintenance notification
$query_rsMaintenanceNotif = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact"
	. ", maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%H:%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours"
	. ", maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName, maintenancenotifs.status"
	. " FROM maintenancenotifs, employees"
	. " WHERE maintenancenotifs.maintenanceNotifsID = $lastID AND maintenancenotifs.employeeID=employees.employeeID";
$rsMaintenanceNotif = $conn->query($query_rsMaintenanceNotif) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsMaintenanceNotif = $rsMaintenanceNotif->fetch_assoc();
$totalRows_rsMaintenanceNotif = $rsMaintenanceNotif->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title>Submitting Maintenance Notification..</title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	 </head>
	 <body>
		  <?php
		  $email = new tEmail();

		  if (($my_post['prodOps'] == 'y')) {
			  $email->AddAddress("techsupport@markssystems.com", "Tech Support");
		  }
		  if (($my_post['noc'] == 'y')) {
			  $email->AddAddress("product@markssystems.com", "Product Dev");
		  }
		  if (($my_post['neteng'] == 'y')) {
			  $email->AddAddress("sales@markssystems.com", "Sales");
		  }
		  if (($my_post['syseng'] == 'y')) {
			  $email->AddAddress("projects@markssystems.com", "Projects");
		  }
		  if ((isset($my_post['cc'])) && ($my_post['cc'] != null)) {
			  $email->AddAddress("" . $my_post['cc'] . "", "cc");
		  }
		  $email->AddReplyTo("MaintenanceNotifications@sap.com", "Maintenance Notifications");

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

		  $email->set_subject($subject);
		  $email->set_body($body);
		  $email->set_alt_body($txtbody);

		  if (!$email->send()) {
			  ?>
			  <div class='alert alert-danger' role='alert'>
					<strong>Error!</strong> This Maintenance Notification could not be sent successfully. Please check the errors below or contact El Chapulin Colorado.<br />
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
