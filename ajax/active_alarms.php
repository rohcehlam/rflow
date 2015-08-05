<?php
require_once('../Connections/conn_skybot.php');

$rsAlarms = $conn_skybot->query("SELECT comment FROM alarms WHERE active=TRUE");

$records = array();
while ($row = $rsAlarms->fetch_assoc()) {
	$temp0 = explode('strong', preg_replace('/<|\/|>/', '', $row['comment']));
	$temp = array();
	$temp['database'] = $temp0[3];
	$temp['table'] = $temp0[1];
	$temp['lastcheck'] = $temp0[5];
	$records[] = $temp;
}
echo json_encode(['records' => $rsAlarms->num_rows, 'list' => $records]);
