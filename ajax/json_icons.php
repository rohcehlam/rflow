<?php

require_once('../Connections/connection.php');
require_once('../Connections/conn_skybot.php');
session_start();

$rsAlarms = $conn_skybot->query("SELECT count(*) FROM alarms WHERE active=TRUE");
$row_Alarms = $rsAlarms->fetch_row();
$rsPendingMaintenances = $conn->query("SELECT count(*) FROM maintenancenotifs WHERE status = 'Open' OR status='Extended'");
$row_Maintenances = $rsPendingMaintenances->fetch_row();
$rs_SupportRequests = $conn->query("SELECT count(*) FROM escalations WHERE assignedTo = " . $_SESSION['employee'] . " AND status <> 'Closed' AND status <> 'Returned'");
$row_SupportRequests = $rs_SupportRequests->fetch_row();
$rs_UnassignedSupportRequests = $conn->query("SELECT count(*) FROM escalations WHERE (assignedTo='48' or isnull(assignedTo)) AND status <> 'Closed' AND status <> 'Returned'");
$row_UnassignedSupportRequests = $rs_UnassignedSupportRequests->fetch_row();

echo json_encode([
	'alarms' => $row_Alarms[0],
	'maintenances' => $row_Maintenances[0],
	'my_requests' => $row_SupportRequests[0],
	'support_requests' => $row_UnassignedSupportRequests[0],
]);
