<?php
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'html_tools.php';

/**
 * Description of alarms
 *
 * @author ojimenez
 */
class tAlarma {

	private $conn;
	private $id;
	private $server;
	private $table_name;
	private $field;
	private $cron_exp;
	private $current_state;
	private $previous_state;
	private $last_change;
	private $server_name;

	function __construct($conn = null, $id = 0, $server = '', $table_name = '', $field = '0', $cron_exp = '', $current_state = '0'
	, $previous_state = '0', $last_change = '0', $server_name = '') {
		$this->conn = $conn;
		$this->id = $id;
		$this->server = $server;
		$this->table_name = preg_replace('/yyyy/', date('Y'), preg_replace('/yyyymm/', date('Ym'), preg_replace('/yyyymmdd/', date('Ymd'), $table_name)));
		$this->field = $field;
		$this->cron_exp = $cron_exp;
		$this->current_state = $current_state;
		$this->previous_state = $previous_state;
		$this->last_change = $last_change;
		$this->server_name = $server_name;
	}

	function load() {
		$query_load = <<< EOD
SELECT ms_server.nombre as `server_name`, `server`, table_name, `field`, current_state, previous_state, last_change, cron_exp
	FROM dbalarms
	LEFT JOIN ms_server ON dbalarms.`server`=ms_server.id
	WHERE dbalarms.id=%%id%%;
EOD;
		$rs_load = $this->conn->query(str_replace('%%id%%', $this->id, $query_load)) or die("Error in the query: " . $this->conn->error);
		while ($row = $rs_load->fetch_assoc()) {
			$this->server = $row['server'];
			$this->table_name = preg_replace('/yyyy/', date('Y'), preg_replace('/yyyymm/', date('Ym'), preg_replace('/yyyymmdd/', date('Ymd'), $row['table_name'])));
			$this->field = $row['field'];
			$this->current_state = $row['current_state'];
			$this->previous_state = $row['previous_state'];
			$this->last_change = $row['last_change'];
			$this->cron_exp = $row['cron_exp'];
			$this->server_name = $row['server_name'];
		}
	}

	function form($function) {
		$rs_servers = $this->conn->query("SELECT id, nombre FROM ms_server WHERE nombre LIKE('%mf-db%') ORDER BY nombre");
		while ($row = $rs_servers->fetch_assoc()) {
			$array_servers[] = ['id' => $row['id'], 'value' => $row['nombre']];
		}
		$array_trigger = [['id' => 0, 'value' => 'Row Count'], ['id' => 1, 'value' => 'Update Time'], ['id' => 2, 'value' => 'Data Length']];
		$helper_trigger = array('Row Count', 'Update Time', 'Data Length');

		$writer = new thtml_writer($function);
		$writer->draw_select('server', 'Server', 'server', $this->server_name, $array_servers, $this->server, 'load_tablenames();');
		$writer->draw_select_plus('table_name', 'Table Name', 'table_name', $this->table_name, [['id' => $this->table_name, 'value' => $this->table_name]], $this->server_name, 'div_select_tablename');
		$writer->draw_select('field', 'Field', 'field', $helper_trigger[$this->field], $array_trigger, $this->field);
		$writer->draw_div('cron_expr', 'Cron Expression');
		?>
		<script>
			$(document).ready(function () {
				 var cron_expr = $('#cron_expr').cron({
					  customValues: {
							"5 Minutes": "*/5 * * * *",
							"15 Minutes": "*/15 * * * *",
							"30 Minutes": "*/30 * * * *",
							"30 Minutes past 7": "7,37 * * * *",
							"40 Minutes": "*/40 * * * *"
					  }
				 });
				 cron_expr.cron("value", "<?php echo $this->cron_exp; ?>");

				 $.widget("custom.catcompleteX", $.ui.autocomplete, {
					  _create: function () {
							this._super();
							this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
					  },
					  _renderMenu: function (ul, items) {
							var that = this,
									  currentCategory = "";
							$.each(items, function (index, item) {
								 var li;
								 if (item.category != currentCategory) {
									  ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
									  currentCategory = item.category;
								 }
								 li = that._renderItemData(ul, item);
								 if (item.category) {
									  li.attr("aria-label", item.category + " : " + item.label);
								 }
							});
					  }
				 });

			});
			function load_tablenames() {
				 server = document.getElementById('server').value;
				 table_name = document.getElementById('table_name').value;
				 $("#div_select_tablename_load").show();
				 $.ajax({
					  url: "../ajax/alarm_table_select.php?server=" + server + "&table_name=" + table_name,
					  success: function (data) {
							$("#div_select_tablename").html(data);
							$("#div_select_tablename_load").hide();
					  }
				 });
			}

			load_tablenames();
		</script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<style>
			 .ui-autocomplete-category {
				  font-weight: bold;
				  padding: .2em .4em;
				  margin: .8em 0 .2em;
				  line-height: 1.5;
			 }
		</style>
		<?php
	}

	function get_id() {
		return $this->id;
	}

	function to_array() {
		global $helper_trigger;
		if ($this->cron_exp != '') {
			$temp = "<a href='#' title='Next Scheduled Run: " . date('l j \o\f M \a\t H:i:s', $this->next_run()) . "'>{$this->cron_exp}</a>";
		} else {
			$temp = $this->cron_exp;
		}
		$temp = array(
			'id' => $this->id,
			'server_name' => $this->server_name,
			'table_name' => $this->table_name,
			'field' => $helper_trigger[$this->field],
			'cron_exp' => $temp,
			'details' => "<a href='#' title='Current State: {$this->current_state}\nPrevious State: {$this->previous_state}\nLast Change: {$this->last_change}'>Details</a>",
		);
		return $temp;
	}

	private function get_server_list($conn) {
		$result = $conn->query("SELECT id, nombre FROM mfdbserver WHERE nombre LIKE('%mf-db%') ORDER BY nombre");
		while ($row = $result->fetch_assoc()) {
			if ($row['id'] == $this->server) {
				echo "<option value='{$row['id']}' selected='selected'>{$row['nombre']}</option>\n";
			} else {
				echo "<option value='{$row['id']}'>{$row['nombre']}</option>\n";
			}
		}
	}

	function del_dialog() {
		global $helper_trigger;
		?>
		<fieldset>
			 <input type='hidden' name='del_alarm_id' value='<?php echo $this->id; ?>' id='del_alarm_id'>
			 <strong>Server: </strong><?php echo $this->server_name; ?><br/>
			 <strong>Table: </strong><?php echo $this->table_name; ?><br/>
			 <strong>Field: </strong><?php echo $helper_trigger[$this->field]; ?><br/>
			 <strong>Cron Expression: </strong><?php echo $this->cron_exp; ?><br/>
		</fieldset>
		<?php
	}

	private function valid_cron() {
		$sw = true;
		foreach (explode(' ', $this->cron_exp) as $data) {
			$sw = $sw and preg_match('/^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/', $data);
		}
		return $sw;
	}

	private function lista_minus($cadena = '') {
		$temp = array();
		$z = explode('-', $cadena);
		if (isset($z[1])) {
			if ($z[0] < $z[1]) {
				$from = $z[0];
				$to = $z[1];
			} else {
				$from = $z[1];
				$to = $z[0];
			}
			for ($i = $from; $i <= $to; $i++) {
				$temp[] = $i;
			}
		} else {
			$temp[] = $z[0];
		}
		return $temp;
	}

	private function lista_comma($cadena = '') {
		$temp = array();
		foreach (explode(',', $cadena) as $data) {
			foreach ($this->lista_minus($data) as $x) {
				$temp[] = $x;
			}
		}
		return $temp;
	}

	private function lista_slash($cadena = '') {
		$temp = array();
		@list ($one, $two) = explode('/', $cadena);
		if (isset($two)) {
			foreach ($this->lista_comma($two) as $x) {
				for ($i = 0; $i <= 59; $i += $x) {
					$temp[] = $i;
				}
			}
		} else {
			foreach ($this->lista_comma($one) as $x) {
				$temp[] = $x;
			}
		}
		return $temp;
	}

	private function next_run() {
		if ($this->last_change == 0) {
			//$start = time();
			$start = mktime(date('H'), date('i') - 6, date('s'), date('m'), date('d'), date('Y'));
		} else {
			$start = strtotime($this->last_change);
		}
		$dates = array($start);
		@list ($minute, $hour, $day, $month, $dayofweek) = explode(' ', $this->cron_exp);
		if (!isset($hour)) {
			return false;
		}

		$temp = array();
		foreach ($this->lista_slash($minute) as $x) {
			foreach ($dates as $data) {
				if ($x === '*') {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = " minute *";
					$temp[@mktime(date('H', $data), date('i', $data) + 1, date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = " minute *+1";
				} else {
					$temp[@mktime(date('H', $data), $x, date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = " minute $x";
					$temp[@mktime(date('H', $data) + 1, $x, date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = " hour " . (date('H', $data) . " +1");
				}
			}
		}
		$dates = $temp;

		$temp = array();
		foreach ($this->lista_slash($hour) as $x) {
			foreach ($dates as $data => $key) {
				if ($x === '*') {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = "$key hour *";
					$temp[@mktime(date('H', $data) + 1, date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = "$key hour *+1";
				} else {
					$temp[@mktime($x, date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = "$key hour $x";
					$temp[@mktime($x, date('i', $data), date('s', $data), date('m', $data), date('d', $data) + 1, date('Y', $data))] = "$key day " . (date('d', $data) . " +1");
				}
			}
		}
		$dates = $temp;

		$temp = array();
		foreach ($this->lista_slash($day) as $x) {
			foreach ($dates as $data => $key) {
				if ($x === '*') {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = "$key day *";
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + 1, date('Y', $data))] = "$key day *+1";
				} else {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), $x, date('Y', $data))] = "$key day $x";
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data) + 1, $x, date('Y', $data))] = "$key month " . date('m', $data) . " + 1";
				}
			}
		}
		$dates = $temp;

		$temp = array();
		foreach ($this->lista_slash($month) as $x) {
			foreach ($dates as $data => $key) {
				if ($x === '*') {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data))] = "$key month *";
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data) + 1, date('d', $data), date('Y', $data))] = "$key month * +1";
				} else {
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), $x, date('d', $data), date('Y', $data))] = "$key month $x";
					$temp[@mktime(date('H', $data), date('i', $data), date('s', $data), $x, date('d', $data), date('Y', $data) + 1)] = "$key year " . date('Y', $data) . " + 1";
				}
			}
		}
		$dates = $temp;

		$temp = array();
		foreach ($this->lista_slash($dayofweek) as $x) {
			foreach ($dates as $data => $key) {
				$y = date('w', $data);
				if ($x === '*') {
					$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data), date('Y', $data)))] = "$key this_day";
					$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + 7, date('Y', $data)))] = "$key next week";
				} else {
					if ($x > $y) {
						$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + ($x - $y), date('Y', $data)))] = "$key week_day $x case one";
						$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + ($x - $y + 7), date('Y', $data)))] = "$key week_day $x+7 case one";
					} else {
						$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + (-$y + $x), date('Y', $data)))] = "$key week_day $x case two";
						$temp[date(@mktime(date('H', $data), date('i', $data), date('s', $data), date('m', $data), date('d', $data) + (7 - $y + $x), date('Y', $data)))] = "$key week_day $x+7 case two";
					}
				}
			}
		}
		$dates = $temp;

//echo "Dates<br/>\n";
		foreach ($dates as $key => $data) {
//echo date('Y-m-d H:i:s', $key) . "&nbsp;$data<br/>\n";
			if ($key > $start) {
//if ($key > date('U')) {
				if (!isset($candidate)) {
					$candidate = $key;
				} else {
					if ($candidate > $key) {
						$candidate = $key;
					}
				}
			}
		}

		/*
		  echo "<pre>\n";
		  print_r($this->lista_slash($minute));
		  echo "</pre>\n";
		  echo "Last change: <strong>{$this->last_change}</strong><br/>\n";
		  echo "Crontab Expression: <strong>{$this->cron_exp}</strong><br/>\n";
		  echo "Candidate: <strong>" . date('Y-m-d H:i:s', $candidate) . "</strong><br/>\n";
		 */
		return $candidate;
	}

	function save($conn = null) {
		global $helper_trigger;
		$temp = '';
		if ($this->server == '') {
			$temp = 'You must select a server first.';
		} else {
			if (($this->table_name == '') or ( $this->table_name == 'null')) {
				$temp = 'You must select a table first';
			} else {
				if (!$this->valid_cron()) {
					$temp = "Syntax error in the crontab expression ({$this->cron_exp})";
				} else {
					$strquery = "INSERT INTO dbalarms (server, table_name, field, current_state, previous_state, last_change, cron_exp)"
						. " VALUES ('{$this->server}', '{$this->table_name}', '{$this->field}', '{$this->current_state}', "
						. "'{$this->previous_state}', '{$this->last_change}', '{$this->cron_exp}');";
					$conn->query($strquery);
					$result = $conn->query("SELECT nombre FROM mfdbserver WHERE id='{$this->server}'");
					while ($row = mysqli_fetch_row($result)) {
						$server = $row[0];
					}
				}
			}
		}
		if ($temp != '') {
			?>
			<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
				 <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
					  <strong>Error: </strong><br/><?php echo $temp; ?>
				 </p>
			</div><br/>
			<?php
		} else {
			?>
			<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
				 <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
					  <strong>Information:</strong> New Alarm successfully created.<br/><?php
					  echo "Table to be monitored: <strong>{$this->table_name}</strong><br/>\n";
					  echo "In the server: <strong>$server</strong><br/>\n";
					  echo "Cron defined: <strong>{$this->cron_exp}</strong><br/>\n";
					  echo "Field to be Monitored: <strong>{$helper_trigger[$this->field]}</strong><br/>\n";
					  echo "Scheduled candidate for next run: <strong>" . date('Y-m-d H:i:s', $this->next_run()) . "</strong>";
					  ?>
				 </p>
			</div><br/>
			<?php
		}
	}

	function update($conn = null) {
		global $helper_trigger;
		$temp = '';
		if ($this->server == '') {
			$temp = 'You must select a server first.';
		} else {
			if (($this->table_name == '') or ( $this->table_name == 'null')) {
				$temp = 'You must select a table first';
			} else {
				if (!$this->valid_cron()) {
					$temp = "Syntax error in the crontab expression ({$this->cron_exp})";
				} else {
					$strquery = "UPDATE dbalarms SET server='{$this->server}', table_name='{$this->table_name}',"
						. " field='{$this->field}', cron_exp='{$this->cron_exp}' WHERE id='{$this->id}';";
//echo "$strquery<br/>\n";
					$conn->query($strquery);
					$result = $conn->query("SELECT nombre FROM mfdbserver WHERE id='{$this->server}'");
					while ($row = mysqli_fetch_row($result)) {
						$server = $row[0];
					}
				}
			}
		}
		if ($temp != '') {
			?>
			<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
				 <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
					  <strong>Error: </strong><br/><?php echo $temp; ?>
				 </p>
			</div><br/>
			<?php
		} else {
			?>
			<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
				 <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
					  <strong>Information:</strong> Alarm successfully updated.<br/><?php
					  echo "Table to be monitored: <strong>{$this->table_name}</strong><br/>\n";
					  echo "In the server: <strong>$server</strong><br/>\n";
					  echo "Cron defined: <strong>{$this->cron_exp}</strong><br/>\n";
					  echo "Field to be Monitored: <strong>{$helper_trigger[$this->field]}</strong><br/>\n";
					  echo "Scheduled candidate for next run: <strong>" . date('Y-m-d H:i:s', $this->next_run()) . "</strong>";
					  ?>
				 </p>
			</div><br/>
			<?php
		}
	}

	function delete($conn = null, $id = 0) {
		global $helper_trigger;
//echo "UPDATE alarms SET active=FALSE, until_timestamp=" . time() . " WHERE server='{$this->server}' AND table_name='{$this->table_name}'<br/>\n";
//echo "DELETE FROM dbalarms WHERE id='$id'<br/>\n";
		$conn->query("UPDATE alarms SET active=FALSE, until_timestamp=" . time() . " WHERE server='{$this->server}' AND table_name='{$this->table_name}'");
		$conn->query("DELETE FROM dbalarms WHERE id='$id'");
		?>
		<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
			 <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				  <strong>Information:</strong> Alarm successfully deleted.<br/>
				  <strong>Server: </strong><?php echo $this->server_name; ?><br/>
				  <strong>Table: </strong><?php echo $this->table_name; ?><br/>
				  <strong>Field: </strong><?php echo $helper_trigger[$this->field]; ?><br/>
				  <strong>Cron Expression: </strong><?php echo $this->cron_exp; ?><br/>
			 </p>
		</div><br/>
		<?php
	}

	function check($conn = null, $debug = false) {
//$this->last_change = '2014-10-14 12:30:26';
//$this->cron_exp = '30 10 * * *';
		$status = '';
		$next_run = date('Y-m-d H:i:s', $this->next_run());
		$last_change = $this->last_change;
		if (substr($next_run, 0, 4) == '1969')
			return 0;
		if (time() >= $this->next_run()) {
			$result = $conn->query("SELECT rows, update_time, data_length, a.id, timestamp"
				. " FROM ("
				. " SELECT MAX(id) AS id"
				. " FROM table_status_plus"
				. " WHERE server={$this->server} AND name='{$this->table_name}') AS a"
				. " LEFT JOIN (table_status_plus as b)"
				. " ON (a.id=b.id);");
			while ($row = mysqli_fetch_row($result)) {
				global $helper_trigger;
				$status = 'Alarm triggered';
//echo "Alarm triggered<br/>\nField: " . $helper_trigger[$this->field] . "<br/>\nValue Lost: {$this->previous_state}<br/>\n";
				$this->previous_state = $this->current_state;
				$this->current_state = $row[$this->field];
				$this->last_change = date('Y-m-d H:i:s', time());
//echo "Previous State: {$this->previous_state}<br/>\nCurrent State: {$this->current_state}<br/>\n";
				if ($this->current_state == $this->previous_state) {
					$status .= ' Table unchanged';
					if ($result = $conn->query("SELECT count(*) FROM alarms WHERE server='$this->server' AND table_name='$this->table_name' AND active=TRUE")) {
						$row = $result->fetch_row();
						if ($row[0] <= 0) {
							$comment = "The table <strong>$this->table_name</strong> in the server <strong>{$this->server_name}</strong> has not updated or is delayed. Last Check: <strong>$this->last_change</strong>";
							$subject = "[mf-Alarm]. Server:{$this->server_name} Table:{$this->table_name} Server Time:(" . date('Y-m-d H:i:s T') . ")";
//echo "$comment<br/>\n";
							$conn->query("INSERT INTO alarms (server, table_name, active, since_timestamp, comment) VALUES ('$this->server', '$this->table_name', TRUE, '" . time() . "', '$comment')");

							//sendEmail("<fieldset><legend>&nbsp;<strong>Error</strong> Active Alarms.&nbsp;</legend>$comment</fieldset>");
							sendEmail("<fieldset><legend>&nbsp;<strong>Error</strong> Active Alarms.&nbsp;</legend>$comment Server Timestamp: <strong>" . date('r')
								. "</strong> Field monitored: <strong>{$helper_trigger[$this->field]}</strong> Previous value: <strong>{$this->previous_state}</strong>"
								. " Current value: <strong>{$this->current_state}</strong></fieldset>", $subject);

							sendSMS('7037861502', strip_tags($comment), 'mqpons'); // Rodrigo
							sendSMS('5162256936', strip_tags($comment), 'mqpons'); // Edmundo
							//sendSMS('5714842943', strip_tags($comment), 'mqpons'); // Jhon L.
							$status .= ' <strong>Sending Alarm</strong>';
						} else {
							$status .= ' Alarm already sent';
						}
					}
				} else {
					$status .= ' Everything is <strong>Ok.</strong>';
					$conn->query("UPDATE alarms SET active=FALSE WHERE server='$this->server' AND table_name='$this->table_name'");
				}
				$status .= ' Record saved with new values';
				$conn->query("UPDATE dbalarms SET previous_state='{$this->previous_state}', current_state='{$this->current_state}', last_change='{$this->last_change}' WHERE id='{$this->id}'");
			}
		} else {
			$status = "No time to run <strong>yet!</strong>";
		}
		if ($debug) {
			?>
			<tr>
				 <td><?php echo $this->server_name; ?></td>
				 <td><?php echo $this->table_name; ?></td>
				 <td><?php echo $this->cron_exp; ?></td>
				 <td><?php echo $last_change; ?></td>
				 <td><?php echo date('Y-m-d H:i:s', time()); ?></td>
				 <td><?php echo $next_run; ?></td>
				 <td><?php echo $status; ?></td>
				 <td><?php echo date('Y-m-d H:i:s', $this->next_run()); ?></td>
			</tr>
			<?php
		}
	}

}

class tAlarmas {

	public $list;
	public $dbconn;

	function __construct($dbconn) {
		$this->dbconn = $dbconn;
		$result = $this->dbconn->query("SELECT a.id AS alarm_id, server, table_name, field, cron_exp, current_state, previous_state, last_change, b.nombre AS server_name"
			. " FROM dbalarms AS a"
			. " LEFT JOIN (mfdbserver AS b)"
			. " ON (a.server = b.id)") or die('Error in the query: ' . $dbconn->error);
		while ($row = mysqli_fetch_assoc($result)) {
			$this->list[$row['alarm_id']] = new tAlarma($row['alarm_id'], $row['server'], $row['table_name'], $row['field'], $row['cron_exp'], $row['current_state']
				, $row['previous_state'], $row['last_change'], $row['server_name']);
		}
	}

	function frame() {
		?>
		<script type="text/javascript" src="js/alarms.js"></script>
		<p>
			 <button id="btn_new_alarm">New Alarm</button>
			 <button id="btn_refresh_alarm">Refresh</button>
		</p>

		<div id="dlg_new_alarm" title="New Alarm"></div>
		<div id="dlg_edit_alarm" title="Edit Alarm"></div>
		<div id="dlg_delete_alarm" title="Delete Alarm"></div>

		<div id='div_alarm' style="margin: 10px 10px 10px 10px"></div>
		<div id='div_alarm_load' style='display:none;'><img src='img/loading.gif' alt='Loading'/> Loading...</div>
		<?php
	}

	function nueva_alarma($id, $alarma) {
		if (!isset($this->list[$id])) {
			$this->list[$id] = $alarma;
		}
	}

	function nuevo($server, $table_name, $trigger_at, $field, $cron_exp, $activate_at, $activate_time) {
		$this->list[] = new tAlarma($server, $table_name, $trigger_at, $field, $cron_exp, $activate_at, $activate_time);
	}

	function eliminar($id) {
		$dato = $this->list[$id];
		$this->dbconn->query("UPDATE alarms SET active=FALSE, until_timestamp=" . time() . " WHERE server='$dato->server' AND table_name='$dato->table_name'");
		unset($this->list[$id]);
	}

	function mostrar($divid = '') {
		$data['header'] = array('Server', 'Table Name', 'Field', 'Cron Expression', 'Details');
		$data['orientacion'] = array('Izq', 'Izq', 'Izq', 'Izq', 'Izq', 'Izq', 'Izq', 'Izq', 'Izq');
		foreach ($this->list as $temp) {
			$data['body'][$temp->get_id()] = $temp->to_array();
		}
		draw_table($divid, $data, true, 'frm_edit_alarm', 'frm_del_alarm');
	}

}
