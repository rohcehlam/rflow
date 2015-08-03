<?php
require_once('../Connections/connection.php');

$query_rsUnassignedSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status"
	. ", ticket, customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON escalations.customerID=customers.customerID"
	. " WHERE assignedTo='48' AND status <> 'Closed' AND status <> 'Returned'"
	. " ORDER BY targetDate ASC";
$rsUnassignedSupportRequests = $conn->query($query_rsUnassignedSupportRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

$records = array();
while ($row = $rsUnassignedSupportRequests->fetch_assoc()) {
	$temp = array();
	foreach ($row as $key => $value) {
		$temp[$key] = $value;
	}
	$records[] = $temp;
}
echo json_encode($records);
/*
$f = fopen('unassigned_supportRequest.json', 'w');
fwrite($f, json_encode($records));
fclose($f);
*/