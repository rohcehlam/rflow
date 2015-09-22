<?php

require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
session_start();
check_permission();
$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
	'datetimerange' => FILTER_SANITIZE_SPECIAL_CHARS,
	'groupedby' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$result = $conn_dbevents->query("SELECT process, `min`, `max`, `top` FROM rCron WHERE id={$my_get['process']}") or die($conn->error);
if ($result->num_rows < 1) {
	header('Location: rcrons.php');
}
while ($row = $result->fetch_assoc()) {
	$process = $row['process'];
	$min = $row['min'];
	$max = $row['max'];
	$top = $row['top'];
}

$rs_asi_frequency = $conn_dbevents->query("SELECT COUNT(*) FROM logmas_" . date('Ym') . " WHERE DATE(datetime_event) = DATE(NOW()) AND process='$process';");
while ($row = $rs_asi_frequency->fetch_row()) {
	$asi_frequency = $row[0];
}

$query = <<<EOD
SELECT %%period%% AS period,
IFNULL(AVG(TIMESTAMPDIFF(SECOND, A.datetime_event, B.datetime_event)), 0) AS `diff`
FROM
(
SELECT procseq, datetime_event, period_proc FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='SS' %%where%% ORDER BY datetime_event DESC
--	LIMIT 3000
) AS A
LEFT JOIN ((
SELECT procseq_father, datetime_event, processed_rec, total_rec, files FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='ES' %%where%% ORDER BY datetime_event DESC
--	LIMIT 3000
) AS B) ON (A.procseq=B.procseq_father)
GROUP BY period
order by period desc
-- limit 100
EOD;

$period = "concat(date_format(A.datetime_event, '%d/%m %H'), ':00')";
if (!$my_get['datetimerange']) {
	if ($asi_frequency < 10) {
		$my_get['datetimerange'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 7, date('Y'))) . " - " . date('Y-m-d H:i:s');
	} else {
		$my_get['datetimerange'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('Y'))) . " - " . date('Y-m-d H:i:s');
	}
}
$temp = explode(' - ', $my_get['datetimerange']);
$begin_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[0]);
$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[1]);
$where = " AND (datetime_event BETWEEN '{$begin_date->format('Y-m-d H:i:s')}' AND '{$end_date->format('Y-m-d H:i:s')}')";

if ($my_get['groupedby']) {
	switch ($my_get['groupedby']) {
		case 1:
			$period = "CONCAT(date_format(A.datetime_event, '%d/%m %H:'), floor(MINUTE(A.datetime_event) / 10), '0')";
			break;
		case 2:
			$period = "concat(date_format(A.datetime_event, '%d/%m %H'), ':00')";
			break;
		case 3:
			$period = "DATE_FORMAT(A.datetime_event, '%d/%m')";
			break;
	}
}

echo"date\tvalue\tmin\tmax\ttop\n";

$curr = $begin_date;
while ($curr->format('Ym') <= $end_date->format('Ym')) {
	//$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, str_replace('%%period%%', $period, $query))))) or die($conn_dbevents->error);
	$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', $curr->format('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, str_replace('%%period%%', $period, $query))))) or die($conn_dbevents->error);
	$n = $rs_rCrons->num_rows;
	$i = $n - 1;
	while ($row_rCron = $rs_rCrons->fetch_assoc()) {
		if ($row_rCron['period'] != '') {
			$temp = DateTime::createFromFormat('d/m H:i', $row_rCron['period']);
			$labels[$i] = $temp->format('Y-m-d H:i');
			$data[$i] = $row_rCron['diff'];
			$i--;
		}
	}

	for ($i = 0; $i < $n; $i++) {
		echo "{$labels[$i]}\t{$data[$i]}\t$min\t$max\t$top\n";
	}

	$curr = new DateTime(date('Y-m-d', mktime(0, 0, 0, $curr->format('n') + 1, 15, $curr->format('Y'))));
}

