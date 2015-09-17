<?php
require_once('../Connections/connection.php');

$label_colors = array(
	"Open" => 'primary',
	"Analysis" => 'info',
	"Closed" => 'success',
	"In Progress" => 'default',
	"On Hold" => 'warning',
	"Returned" => 'danger',
	"Completed" => 'danger'
);

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
	$temp['subject'] = "<a href=\"../supportRequests/supportRequest.php?supportRequest={$temp['escalationID']}&amp;function=view\">{$temp['subject']}</a>";
	$temp['ticket'] = $temp['ticket'] == '0' ? '-' : $temp['ticket'];
	$temp['status'] = "<span class=\"label label-{$label_colors[$temp['status']]}\">{$temp['status']}</span>";
	$records[] = $temp;
}
echo json_encode(['records' => $rsUnassignedSupportRequests->num_rows, 'list' => $records]);
