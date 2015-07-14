<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
require_once("../inc/class.email.php");

$args = array(
	 'MM_insert' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'comments' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'submittedBy' => FILTER_VALIDATE_INT,
	 'dateSubmitted' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'timeSubmitted' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'summary' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'description' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'application' => FILTER_VALIDATE_INT,
	 'subapplication' => FILTER_VALIDATE_INT,
	 'layer' => FILTER_VALIDATE_INT,
	 'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'requestOrigin' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'requestOriginID' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'risk' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'windowStartDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'windowEndDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'windowEndTime' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'reviewedBy' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'flagged' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_post = filter_input_array(INPUT_POST, $args, true);
var_dump($my_post);
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
	</head>
	<body>		
		<?php
		if ($my_post["MM_insert"] == "rfaAdd") {
			$temp = array();
			$helper_insert = array('comments', 'submittedBy', 'dateSubmitted', 'timeSubmitted', 'summary', 'description', 'application', 'subapplication', 'layer', 'status'
				 , 'requestOrigin', 'requestOriginID', 'risk', 'windowStartDate', ' windowStartTime', 'windowEndDate', 'windowEndTime');
			foreach ($helper_insert as $data) {
				$temp[$data] = (isset($my_post[$data]) && $my_post[$data] != '' && $my_post[$data]) ? "'{$my_post[$data]}'" : 'NULL';
			}
			$insertSQL = sprintf("INSERT INTO changerequests (comments, submittedBy, dateSubmitted, timeSubmitted, summary, `description`, applicationID, subapplicationID, layerID, status"
					  . ", requestOrigin, requestOriginID, risk, windowStartDate, windowStartTime, windowEndDate, windowEndTime) VALUES (" . implode(', ', $temp) . ")");
			$Result1 = $conn->query($insertSQL);
			if ($conn->error) {
				echo $conn->error;
			}
			//echo $insertSQL;
			//exit(0);
			$insertGoTo = "rfa.php?function=add&sent=y";
			if (isset($my_server['QUERY_STRING'])) {
				$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
				$insertGoTo .= $my_server['QUERY_STRING'];
			}
			header(sprintf("Location: %s", $insertGoTo));
		}

		if ((isset($my_post["MM_update"])) && ($my_post["MM_update"] == "rfaUpdate") && (!isset($my_post['windowStartDate']))) {
			$temp = array();
			$helper_update = array('summary', 'description', 'applicationID', 'subapplicationID', 'layerID', 'status', 'comments', 'requestOrigin', 'requestOriginID', 'flagged', 'risk', 'reviewedBy');
			foreach ($helper_update as $data) {
				$temp[$data] = (isset($my_post[$data]) && $my_post[$data] != '' && $my_post[$data]) ? "$data='{$my_post[$data]}'" : 'NULL';
			}
			$updateSQL = "UPDATE changerequests SET " . implode(', ', $temp) . " WHERE changeRequestID='{$my_post['changeRequestID']}'";
			$Result1 = $conn->query($updateSQL);
		} elseif ((isset($my_post["MM_update"])) && ($my_post["MM_update"] == "rfaUpdate") && (isset($my_post['windowStartDate']))) {
			$temp = array();
			$helper_update = array('summary', 'description', 'applicationID', 'subapplicationID', 'layerID', 'status', 'comments', 'requestOrigin', 'requestOriginID', 'flagged', 'windowStartDate', 'windowStartTime', 'windowEndDate', 'windowEndTime', 'risk', 'reviewedBy');
			foreach ($helper_update as $data) {
				$temp[$data] = (isset($my_post[$data]) && $my_post[$data] != '' && $my_post[$data]) ? "$data='{$my_post[$data]}'" : 'NULL';
			}
			$updateSQL = "UPDATE changerequests SET " . implode(', ', $temp) . " WHERE changeRequestID='{$my_post['changeRequestID']}'";
			$Result1 = $conn->query($updateSQL);
			$updateGoTo = "rfa.php?function=view&rfa=" . $my_post['changeRequestID'] . "&sent=y";
			if (isset($my_server['QUERY_STRING'])) {
				$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
				$updateGoTo .= $my_server['QUERY_STRING'];
			}
			header(sprintf("Location: %s", $updateGoTo));
		}

		global $lastID;
		$lastID = $conn->insert_id();

		if (isset($my_post['changeRequestID'])) {
			$varRFA_rsRFA = addslashes($my_post['changeRequestID']);
		} else {
			$varRFA_rsRFA = $lastID;
		}
		$query_rsRFA = "SELECT changerequests.changeRequestID, employees.displayName as submittedBy, DATE_FORMAT(dateSubmitted, '%%m/%%d/%%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%%k:%%i') AS timeSubmitted, changerequests.summary, changerequests.description, changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%%m/%%d/%%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%%k:%%i') AS windowStartTime, DATE_FORMAT(windowEndDate, '%%m/%%d/%%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%%k:%%i') AS windowEndTime, changerequests.applicationID, applications.application, changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk, employees.workEmail FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = $varRFA_rsRFA";
		$rsRFA = $conn->query($query_rsRFA);
		$row_rsRFA = $rsRFA->fetch_assoc();
		$totalRows_rsRFA = $rsRFA->num_rows;

		$email = new tEmail();

		if ($my_post["MM_insert"] == "rfaAdd") {
			$body = "An RFC has been submitted, and is awaiting review. An overview of the RFC appears below.<br /><br />";
		} elseif ($my_post["MM_update"] == "rfaUpdate") {
			$body = "RFC #" . $row_rsRFA['changeRequestID'] . " has been <b>" . $row_rsRFA['status'] . "</b>. An overview of the RFC appears below.<br />";
		}
		$body .= "You can view the RFC by visiting <a title=\"Production Operation's RFCs\" href=\"http://" . $my_server['HTTP_HOST'] . "/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . "\">http://" . $my_server['HTTP_HOST'] . "/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . "</a>.<br /><br />";
		$body .= "<b>Submitted By:</b> " . $row_rsRFA['submittedBy'] . "<br />";
		$body .= "<b>Subject:</b> " . stripslashes($row_rsRFA['summary']) . "<br />";
		$body .= "<b>Application:</b> " . $row_rsRFA['application'] . "<br />";
		$body .= "<b>Subapplication:</b> " . $row_rsRFA['subapplication'] . "<br />";
		$body .= "<b>Layer:</b> " . $row_rsRFA['layer'] . "<br />";
		$body .= "<b>Request Origin:</b> ";
		if ($row_rsRFA['requestOrigin'] == "Support Request") {
			$body .= " <a href=\"http://" . $my_server['HTTP_HOST'] . "/rflow_karen/supportRequests/supportRequest.php?function=view&amp;supportRequest=" . $row_rsRFA['requestOriginID'] . "\">";
		}
		$body .= "" . $row_rsRFA['requestOrigin'] . " #" . $row_rsRFA['requestOriginID'] . "</a><br />";
		$body .= "<b>Window:</b>";
		$body .= "<div style=\"margin-left: 15px;\">Starting: " . $row_rsRFA['windowStartDate'] . " at " . $row_rsRFA['windowStartTime'] . "<br />";
		$body .= "Ending: " . $row_rsRFA['windowEndDate'] . " at " . $row_rsRFA['windowEndTime'] . "</div>";
		$body .= "<b>Description:</b> " . stripslashes(nl2br($row_rsRFA['description'])) . "<br />";
		$body .= "<b>Risk:</b> " . stripslashes(nl2br($row_rsRFA['risk'])) . "<br />";
		if ($my_post['comments'] != null) {
			$body .= "<b>Comments:</b> " . stripslashes(nl2br($row_rsRFA['comments'])) . "<br />";
		}
		if ($my_post["MM_insert"] == "rfaAdd") {
			$txtbody = "An RFC has been submitted, and is awaiting review. An overview of the RFC appears below.<br /><br />";
		} elseif ($my_post["MM_update"] == "rfaUpdate") {
			$txtbody = "RFC #" . $row_rsRFA['changeRequestID'] . " has been " . $row_rsRFA['status'] . ". An overview of the RFA appears below.<br />";
		}
		$txtbody .= "You can view the RFC by visiting http://" . $my_server['HTTP_HOST'] . "/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . ".<br />";
		$txtbody .= "You can login by visiting http://" . $my_server['HTTP_HOST'] . "/rflow_karen/userPortals/index.php?ref=" . $my_server['HTTP_HOST'] . "/rflow_karen/rfas/rfas.php.<br />";
		$txtbody .= "You can also view the ProdOps US Calendar by visiting http://" . $my_server['HTTP_HOST'] . "/rflow_karen/calendar/month.php.<br /><br />";
		$txtbody .= "*********************";
		$txtbody .= "Subject: " . stripslashes($row_rsRFA['summary']) . "<br />";
		$txtbody .= "Application: " . $row_rsRFA['application'] . "<br />";
		$txtbody .= "Subapplication: " . $row_rsRFA['subapplication'] . "<br />";
		$txtbody .= "Layer: " . $row_rsRFA['layer'] . "<br />";
		$txtbody .= "Request Origin: " . $row_rsRFA['requestOrigin'] . " #" . $row_rsRFA['requestOriginID'] . "<br />";
		$txtbody .= "Window:";
		$txtbody .= "Starting: " . $row_rsRFA['windowStartDate'] . " at " . $row_rsRFA['windowStartTime'] . "<br />";
		$txtbody .= "Ending: " . $row_rsRFA['windowEndDate'] . " at " . $row_rsRFA['windowEndTime'] . "";
		$txtbody .= "Description: " . stripslashes(nl2br($row_rsRFA['description'])) . "<br />";
		$txtbody .= "Risk: " . stripslashes(nl2br($row_rsRFA['risk'])) . "<br />";
		if ($my_post['comments'] != null) {
			$txtbody .= "Comments: " . stripslashes(nl2br($row_rsRFA['comments'])) . "<br />";
		}
		$subject = "RFC #" . $row_rsRFA['changeRequestID'] . " - " . stripslashes($row_rsRFA['summary']);
		if ($row_rsRFA['status'] != "Pending Approval") {
			$subject .= " **" . $row_rsRFA['status'] . "**";
		}
		$email->set_subject($subject);
		$email->set_body($body);
		$email->set_alt_body($txtbody);
		if (!$email->send()) {
			echo "This RFC could not be sent successfully. Please check the errors below .<br />";
			echo "Mailer Error: " . $email->get_error();
			exit;
		} else {
			echo "RFC sent successfully! If you can see this message.";
			$goTo = "rfa.php?function=add&sent=y";
			header(sprintf("Location: %s", $goTo));
		}
		?></body>
</html>
