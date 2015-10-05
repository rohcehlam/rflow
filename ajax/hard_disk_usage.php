<?php

include_once '../Connections/conn_dbevents.php';

$query_hard_disk = <<< EOD
SELECT S.nombre AS `server`, mounted_on, size, xinuse
FROM (
	SELECT id, max_id, `server` AS `server_id`, mounted_on, size, CONVERT(inuse, UNSIGNED INTEGER) AS `xinuse`
	FROM 
	(
		SELECT MAX(id) AS max_id
		FROM ms_diskusage_%%table_name%%
		GROUP BY `server`, mounted_on
	) AS A
	LEFT JOIN (
		(
			SELECT id, `server`, mounted_on, size, used, inuse
			FROM ms_diskusage_%%table_name%%
		)AS B
	)
	ON (B.id=A.max_id)
	WHERE CONVERT(inuse, UNSIGNED INTEGER) > 80
) D
LEFT JOIN ms_server AS S
ON (D.server_id = S.id)
ORDER BY xinuse DESC
EOD;

$result = $conn_dbevents->query(str_replace('%%table_name%%', date('Ym'), $query_hard_disk));
$records = array();
while ($row = $result->fetch_assoc()){
	$temp = array();
	foreach ($row as $key => $value) {
		$temp[$key] = $value;
	}
	$records[] = $temp;
}
echo json_encode(['records' => $result->num_rows, 'list' => $records]);