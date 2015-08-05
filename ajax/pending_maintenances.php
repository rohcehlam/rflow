<?php
require_once('../Connections/connection.php');

$query_rsPendingMaintenances = "SELECT maintenanceNotifsID, reason, TIME_FORMAT(startTime,'%k:%i') AS startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate"
	. " FROM maintenancenotifs"
	. " WHERE status = 'Open' OR status='Extended'"
	. " ORDER BY startDate DESC, startTime DESC";
$rsPendingMaintenances = $conn->query($query_rsPendingMaintenances) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

$records = array();
while ($row = $rsPendingMaintenances->fetch_assoc()) {
	$temp = array();
	foreach ($row as $key => $value) {
		$temp[$key] = $value;
	}
	$temp['reason'] = "<a href=\"../maintenances/maintenance.php?maintenance={$temp['maintenanceNotifsID']}&amp;function=view\">{$temp['reason']}</a>";
	$records[] = $temp;
}
echo json_encode(['records' => $rsPendingMaintenances->num_rows, 'list' => $records]);
