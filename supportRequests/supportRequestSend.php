<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

$args = array(
	'MM_insert' => FILTER_SANITIZE_SPECIAL_CHARS,
	'MM_update' => FILTER_SANITIZE_SPECIAL_CHARS,
	'dateEscalated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'timeEscalated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'submittedBy' => FILTER_SANITIZE_SPECIAL_CHARS,
	'application' => FILTER_SANITIZE_SPECIAL_CHARS,
	'category' => FILTER_SANITIZE_SPECIAL_CHARS,
	'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	'description' => FILTER_SANITIZE_SPECIAL_CHARS,
	'recreateSteps' => FILTER_SANITIZE_SPECIAL_CHARS,
	'whatWasTested' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customerImpact' => FILTER_SANITIZE_SPECIAL_CHARS,
	'logs' => FILTER_SANITIZE_SPECIAL_CHARS,
	'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	'ticket' => FILTER_SANITIZE_SPECIAL_CHARS,
	'addInfo' => FILTER_SANITIZE_SPECIAL_CHARS,
	'dept' => FILTER_SANITIZE_SPECIAL_CHARS,
	'targetDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'priority' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'targetDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'assignedTo' => FILTER_SANITIZE_SPECIAL_CHARS,
	'dateUpdated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'timeUpdated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'addInfo' => FILTER_SANITIZE_SPECIAL_CHARS,
	'comments' => FILTER_SANITIZE_SPECIAL_CHARS,
	'dept' => FILTER_SANITIZE_SPECIAL_CHARS,
	'priority' => FILTER_SANITIZE_SPECIAL_CHARS,
	'supportRequestID' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_post = filter_input_array(INPUT_POST, $args, true);

$my_server = filter_input_array(INPUT_SERVER, array(
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	), true);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title>Submitting Support Request..</title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body>
		  <?php
		  if (( (isset($my_post["MM_insert"])) && ($my_post["MM_insert"] == "supportRequestAdd"))) {
			  $insertSQL = "INSERT INTO escalations (dateEscalated, timeEscalated, submittedBy, applicationID, categoryID, subject, description, recreateSteps, whatWasTested"
				  . ", customerImpact, logs, status, ticket, addInfo, deptID, targetDate, priority, customerID) VALUES"
				  . " ('{$my_post['dateEscalated']}', '{$my_post['timeEscalated']}', {$my_post['submittedBy']}, {$my_post['application']}, {$my_post['category']}"
				  . ", '{$my_post['subject']}', '{$my_post['description']}', '{$my_post['recreateSteps']}', '{$my_post['whatWasTested']}', '{$my_post['customerImpact']}'"
				  . ", '{$my_post['logs']}', '{$my_post['status']}', '{$my_post['ticket']}', '{$my_post['addInfo']}', {$my_post['dept']}, '{$my_post['targetDate']}',"
				  . " '{$my_post['priority']}', {$my_post['customer']})";

			  $Result1 = $conn->query($insertSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

			  $insertGoTo = "supportRequest.php?function=add&sent=y";
			  if (isset($my_server['QUERY_STRING'])) {
				  $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
				  $insertGoTo .= $my_server['QUERY_STRING'];
			  }
			  header("Location: $insertGoTo");
		  }

//if updating a Support Request, but not updating the target date
		  if (( (isset($my_post["MM_update"])) && ($my_post["MM_update"] == "supportRequestUpdate") ) && (!isset($my_post['targetDate']))) {
			  //$updateSQL = sprintf("UPDATE escalations SET submittedBy=%s, applicationID=%s, categoryID=%s, subject=%s, description=%s, customerImpact=%s, assignedTo=%s, status=%s, dateClosed=%s, timeClosed=%s, ticket=%s, addInfo=%s, outcome=%s, deptID=%s, priority=%s, customerID=%s, recreateSteps=%s, whatWasTested=%s, logs=%s WHERE escalationID=%s", GetSQLValueString($my_post['submittedBy'], "int"), GetSQLValueString($my_post['application'], "int"), GetSQLValueString($my_post['category'], "int"), GetSQLValueString($my_post['subject'], "text"), GetSQLValueString($my_post['description'], "text"), GetSQLValueString($my_post['customerImpact'], "text"), GetSQLValueString($my_post['assignedTo'], "int"), GetSQLValueString($my_post['status'], "text"), GetSQLValueString($my_post['dateUpdated'], "date"), GetSQLValueString($my_post['timeUpdated'], "date"), GetSQLValueString($my_post['ticket'], "text"), GetSQLValueString($my_post['addInfo'], "text"), GetSQLValueString($my_post['comments'], "text"), GetSQLValueString($my_post['dept'], "int"), GetSQLValueString($my_post['priority'], "text"), GetSQLValueString($my_post['customer'], "int"), GetSQLValueString($my_post['recreateSteps'], "text"), GetSQLValueString($my_post['whatWasTested'], "text"), GetSQLValueString($my_post['logs'], "text"), GetSQLValueString($my_post['supportRequestID'], "int"));
			  $updateSQL = "UPDATE escalations SET submittedBy={$my_post['submittedBy']}, applicationID={$my_post['application']}, categoryID={$my_post['category']}"
				  . ", subject='{$my_post['subject']}', description='{$my_post['description']}', customerImpact='{$my_post['customerImpact']}', assignedTo={$my_post['assignedTo']}"
				  . ", status='{$my_post['status']}', dateClosed='{$my_post['dateUpdated']}', timeClosed='{$my_post['timeUpdated']}', ticket='{$my_post['ticket']}'"
				  . ", addInfo='{$my_post['addInfo']}', outcome='{$my_post['comments']}', deptID={$my_post['dept']}, priority='{$my_post['priority']}'"
				  . ", customerID={$my_post['customer']}, recreateSteps='{$my_post['recreateSteps']}', whatWasTested='{$my_post['whatWasTested']}', logs='{$my_post['logs']}'"
				  . " WHERE escalationID={$my_post['supportRequestID']}";

			  $Result1 = $conn->query($updateSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

			  $updateGoTo = "supportRequest.php?function=add&sent=y";
			  if (isset($my_server['QUERY_STRING'])) {
				  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
				  $updateGoTo .= $my_server['QUERY_STRING'];
			  }
			  header("Location: $updateGoTo");
//if we're updating the target date
		  } elseif (( (isset($my_post["MM_update"])) && ($my_post["MM_update"] == "supportRequestUpdate") ) && (isset($my_post['targetDate']))) {
			  //$updateSQL = sprintf("UPDATE escalations SET submittedBy=%s, applicationID=%s, categoryID=%s, subject=%s, description=%s, customerImpact=%s, assignedTo=%s, status=%s, dateClosed=%s, timeClosed=%s, ticket=%s, addInfo=%s, outcome=%s, deptID=%s, priority=%s, customerID=%s, recreateSteps=%s, whatWasTested=%s, logs=%s, targetDate=%s WHERE escalationID=%s", GetSQLValueString($my_post['submittedBy'], "int"), GetSQLValueString($my_post['application'], "int"), GetSQLValueString($my_post['category'], "int"), GetSQLValueString($my_post['subject'], "text"), GetSQLValueString($my_post['description'], "text"), GetSQLValueString($my_post['customerImpact'], "text"), GetSQLValueString($my_post['assignedTo'], "int"), GetSQLValueString($my_post['status'], "text"), GetSQLValueString($my_post['dateUpdated'], "date"), GetSQLValueString($my_post['timeUpdated'], "date"), GetSQLValueString($my_post['ticket'], "text"), GetSQLValueString($my_post['addInfo'], "text"), GetSQLValueString($my_post['comments'], "text"), GetSQLValueString($my_post['dept'], "int"), GetSQLValueString($my_post['priority'], "text"), GetSQLValueString($my_post['customer'], "int"), GetSQLValueString($my_post['recreateSteps'], "text"), GetSQLValueString($my_post['whatWasTested'], "text"), GetSQLValueString($my_post['logs'], "text"), GetSQLValueString($my_post['targetDate'], "date"), GetSQLValueString($my_post['supportRequestID'], "int"));
			  $updateSQL = "UPDATE escalations SET submittedBy={$my_post['submittedBy']}, applicationID={$my_post['application']}, categoryID={$my_post['category']}"
				  . ", subject='{$my_post['subject']}', description='{$my_post['description']}', customerImpact='{$my_post['customerImpact']}', assignedTo={$my_post['assignedTo']}"
				  . ", status='{$my_post['status']}', dateClosed='{$my_post['dateUpdated']}', timeClosed='{$my_post['timeUpdated']}', ticket='{$my_post['ticket']}'"
				  . ", addInfo='{$my_post['addInfo']}', outcome='{$my_post['comments']}', deptID={$my_post['dept']}, priority='{$my_post['priority']}', customerID={$my_post['customer']}"
				  . ", recreateSteps='{$my_post['recreateSteps']}', whatWasTested='{$my_post['whatWasTested']}', logs='{$my_post['logs']}', targetDate='{$my_post['targetDate']}'"
				  . " WHERE escalationID={$my_post['supportRequestID']}";

			  echo $updateSQL;
			  $Result1 = $conn->query($updateSQL) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

			  $updateGoTo = "supportRequest.php?function=add&sent=y";
			  if (isset($my_server['QUERY_STRING'])) {
				  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
				  $updateGoTo .= $my_server['QUERY_STRING'];
			  }
			  header("Location: $updateGoTo");
		  }

		  global $lastID;
		  $lastID = $conn->insert_id;

		  $varSupportRequest_rsSupportRequest = "1";
		  if (isset($my_post['supportRequestID'])) {
			  $varSupportRequest_rsSupportRequest = addslashes($my_post['supportRequestID']);
		  } elseif (isset($lastID)) {
			  $varSupportRequest_rsSupportRequest = addslashes($lastID);
		  }

		  $query_rsSupportRequest = sprintf("SELECT escalations.escalationID, escalations.applicationID, applications.application, escalations.subject, escalations.description, escalations.customerImpact, escalations.assignedTo, employees.displayName, escalations.status, escalations.outcome, escalations.customerID, customers.customer, employees.workEmail, departments.email, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.escalationID = %s", GetSQLValueString($varSupportRequest_rsSupportRequest, "int"));
		  $rsSupportRequest = $conn->query($query_rsSupportRequest) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
		  $row_rsSupportRequest = $rsSupportRequest->fetch_assoc();
		  $totalRows_rsSupportRequest = $rsSupportRequest->num_rows;

		  $email = new tEmail();

		  $email->AddAddress('orlando@markssystems.com', 'RFCs - Change Management');

		  if ($my_post["MM_insert"] == "supportRequestAdd") {
			  $body = "A new Support Request has been submitted. An overview of the Support Request appears below.<br /><br />";
		  } elseif (($my_post["MM_update"] == "supportRequestUpdate") && ($row_rsSupportRequest['status'] == "Closed")) {
			  $body = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been closed. An overview of the Support Request appears below.<br />";
		  } elseif (($my_post["MM_update"] == "supportRequestUpdate") && ($row_rsSupportRequest['status'] != "Closed")) {
			  $body = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been updated. An overview of the Support Request appears below.<br /><br />";
		  }
		  $body .= "You can view this &amp; other Support Requests by visiting <a title=\"Production Operation's Support Requests\" href=\"http://" . $_SERVER['HTTP_HOST'] . "/rflow_karen/supportRequests/supportRequests.php?function=view\">http://" . $_SERVER['HTTP_HOST'] . "/rflow_karen/supportRequests/supportRequests.php?function=view</a><br />";
		  $body .= "Note: Please consult the website for information regarding the status of Support Requests.<br /><br />";
		  $body .= "**************************<br />";
		  $body .= "<b>Subject:</b> " . stripslashes($row_rsSupportRequest['subject']) . "<br />";
		  $body .= "<b>Application:</b> " . $row_rsSupportRequest['application'] . "<br />";
		  $body .= "<b>Customer:</b> " . $row_rsSupportRequest['customer'] . "<br />";
		  $body .= "<b>Customer Impact:</b> " . stripslashes($row_rsSupportRequest['customerImpact']) . "<br />";
		  $body .= "<b>Description:</b> " . stripslashes(nl2br($row_rsSupportRequest['description'])) . "<br />";
		  $body .= "**************************";

		  if ($my_post["MM_insert"] == "supportRequestAdd") {
			  $txtbody = "A new Support Request has been submitted. An overview of the Support Request appears below.<br /><br />";
		  } elseif (($my_post["MM_update"] == "supportRequestUpdate") && ($my_post['status'] == "Closed")) {
			  $txtbody = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been closed. An overview of the Support Request appears below.<br />";
		  } elseif (($my_post["MM_update"] == "supportRequestUpdate") && ($my_post['status'] != "Closed")) {
			  $txtbody = "Support Request #" . $row_rsSupportRequest['escalationID'] . " has been updated. An overview of the Support Request appears below.<br /><br />";
		  }
		  $txtbody .= "You can view this &amp; other recent Support Requests by visiting http://" . $_SERVER['HTTP_HOST'] . "/rflow_karen/supportRequests/supportRequests.php?function=view<br />";
		  $txtbody .= "Note: Please consult the website for information regarding the status of Support Requests.<br /><br />";
		  $txtbody .= "**************************<br />";
		  $txtbody .= "Subject: " . stripslashes($row_rsSupportRequest['subject']) . "<br />";
		  $txtbody .= "Application: " . $row_rsSupportRequest['application'] . "<br />";
		  $txtbody .= "Customer: " . $row_rsSupportRequest['customer'] . "<br />";
		  $txtbody .= "Customer Impact: " . stripslashes($row_rsSupportRequest['customerImpact']) . "<br />";
		  $txtbody .= "Description: " . stripslashes(nl2br($row_rsSupportRequest['description'])) . "<br />";
		  $txtbody .= "**************************";

		  if ($my_post["MM_insert"] == "supportRequestAdd") {
			  $subject = "Support Request (US): " . stripslashes($row_rsSupportRequest['subject']);
		  } elseif ($my_post["MM_update"] == "supportRequestUpdate") {
			  $subject = "Support Request (US): " . stripslashes($row_rsSupportRequest['subject']);
			  if ($row_rsSupportRequest['status'] != null) {
				  $subject .= " **" . $row_rsSupportRequest['status'] . "**";
			  }
		  }

		  $email->set_subject($subject);
		  $email->set_body($body);
		  $email->set_alt_body($txtbody);

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
				<strong>Success!</strong> RFC sent successfully! If you can see this message.
		  </div>
	 </body>
</html>