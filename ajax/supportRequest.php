<?php

require_once('../Connections/connection.php');

session_start();

$query_rsSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket"
	. ", customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON escalations.customerID=customers.customerID"
	. " WHERE assignedTo = " . $_SESSION['employee'] . " AND status <> 'Closed' AND status <> 'Returned'"
	. " ORDER BY targetDate ASC";
$rsSupportRequests = $conn->query($query_rsSupportRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

$records = array();
while ($row = $rsSupportRequests->fetch_assoc()) {
	$temp = array();
	foreach ($row as $key => $value) {
		$temp[$key] = $value;
	}
	$temp['subject'] = "<a href=\"../supportRequests/supportRequest.php?supportRequest={$temp['escalationID']}&amp;function=view\">{$temp['subject']}</a>";
	$records[] = $temp;
}
echo json_encode(array('records' => $rsSupportRequests->num_rows, 'list' => $records));
