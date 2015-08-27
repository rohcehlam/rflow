<?php
error_reporting(FALSE);
require_once('../Connections/conn_dbevents.php');

$server = filter_input(INPUT_GET, 'server', FILTER_SANITIZE_SPECIAL_CHARS);
$table_name = filter_input(INPUT_GET, 'table_name', FILTER_SANITIZE_SPECIAL_CHARS);

global $table_list;
global $groups;
$groups = array(
	 0 => array('key' => 'regular', 'name' => 'Regular Tables', 'flag' => false),
	 1 => array('key' => 'yearly', 'name' => 'Yearly Tables', 'flag' => true),
	 2 => array('key' => 'monthly', 'name' => 'Monthly Tables', 'flag' => true),
	 3 => array('key' => 'daily', 'name' => 'Daily Tables', 'flag' => true),
	 4 => array('key' => 'temporal', 'name' => 'Temporal Tables', 'flag' => false),
);

function assign($cat, $str, $pat, $size, $replacement) {
	global $table_list;
	$name = preg_replace($pat, $replacement, $str);
	//$fecha = substr($str, strlen($str) - $size, $size);
	$temp = preg_replace_callback($pat, function ($matches) {
		return (",{$matches[0]},");
	}
			  , $str);
	$temp = explode(',', $temp);
	$fecha = $temp[1];
	if (isset($table_list[$cat][$name])) {
		if ($table_list[$cat][$name]['since'] > $fecha) {
			$table_list[$cat][$name]['since'] = $fecha;
		}
		if ($table_list[$cat][$name]['until'] < $fecha) {
			$table_list[$cat][$name]['until'] = $fecha;
		}
	} else {
		$table_list[$cat][$name] = array('since' => $fecha, 'until' => $fecha);
	}
}

function write_select() {
	global $table_list;
	global $groups;
	foreach ($groups as $group) {
		echo "<optgroup label='{$group['name']}'>\n";
		foreach ($table_list[$group['key']] as $key => $data) {
			if ($group['flag']) {
				echo "<option value='$key'>$key</option>\n";
			} else {
				echo "<option value='$data'>$data</option>\n";
			}
		}
		echo "</optgroup>\n";
	}
}

function write_array() {
	global $table_list;
	global $groups;
	$temp = array();
	$cont = 0;
	foreach ($groups as $group) {
		foreach ($table_list[$group['key']] as $key => $data) {
			if ($group['flag']) {
				$temp[] = "{ label: \"$key\", category: \"{$group['name']}\" }\n";
			} else {
				$temp[] = "{ label: \"$data\", category: \"{$group['name']}\" }\n";
			}
			$cont++;
		}
	}
	return implode(',', $temp);
}

$pattern3 = "/[0-9]{8}/"; // Daily
$pattern0 = "/[0-9]{6}/"; // Monthly
$pattern1 = "/[0-9]{4}/"; // Yearly
$pattern2 = "/buf|temp|tmp|old|aaa/"; // Temporal
$table_list = array();

$query_tables = <<<EOD
SELECT `name` FROM ms_table_status_plus_%%table_name%% WHERE server=%%server%% AND timestamp=(SELECT MAX(timestamp)
	FROM ms_table_status_plus_%%table_name%%
	WHERE server=%%server%%)
	ORDER BY name;
EOD;
$result = $conn_dbevents->query(str_replace('%%table_name%%', date('Ym'), str_replace('%%server%%', $server, $query_tables))) or die ($conn_dbevents->error);
while ($row = $result->fetch_assoc()) {
	if (preg_match($pattern3, $row['name'])) {
		assign('daily', $row['name'], $pattern3, 8, 'yyyymmdd');
	} else {
		if (preg_match($pattern0, $row['name'])) {
			assign('monthly', $row['name'], $pattern0, 6, 'yyyymm');
		} else {
			if (preg_match($pattern1, $row['name'])) {
				assign('yearly', $row['name'], $pattern1, 8, 'yyyy');
			} else {
				if (preg_match($pattern2, $row['name'])) {
					$table_list['temporal'][] = $row['name'];
				} else {
					$table_list['regular'][] = $row['name'];
				}
			}
		}
	}
}
?>

<input type="text" id="table_name" value="<?php echo $table_name; ?>" class="form-control">
<div id="div_select_tablename_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
<script>
	$(function () {
		var data_tables = [
<?php echo write_array(); ?>
		];
		$("#table_name").catcompleteX({
			delay: 0,
			source: data_tables
		});
	});
</script>

