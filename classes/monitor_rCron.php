<?php

class trCrons {

	const FILENAME = './rCron.data';
	const CRON_QUERY = <<<EOD
SELECT id, `process`, `begin`, `end`, 
IF(ISNULL(`end`)
-- unfinished
, IF (NOW() > DATE_ADD(`begin`, INTERVAL `top` SECOND), 'Top Exceeded'
, IF (NOW() > DATE_ADD(`begin`, INTERVAL `max` SECOND), 'Max Exceeded', 'Ok') )
-- finished
, IF (`end` < DATE_ADD(`begin`, INTERVAL `min` SECOND), 'Ended Prematurely'
, IF (`end` > DATE_ADD(`begin`, INTERVAL `top` SECOND), 'Top Exceeded'
, IF (`end` > DATE_ADD(`begin`, INTERVAL `max` SECOND), 'Max Exceeded', 'Ok') ) )
) AS `Error`,
 NOW(),
 IF(ISNULL(`end`), TIMEDIFF(NOW(), `begin`), TIMEDIFF(`end`, `begin`)) AS diff,
 DATE_ADD(`begin`, INTERVAL `min` SECOND) AS `min_date`,
 DATE_ADD(`begin`, INTERVAL `max` SECOND) AS `max_date`,
 DATE_ADD(`begin`, INTERVAL `top` SECOND) AS `top_date`,
 `min`, `max`, db,
 IF(NOT ISNULL(`end`), TIMEDIFF(NOW(), `end`), 0) AS `Idle`
FROM rCron
EOD;

	private $lista;
	private $conn;
	private $log;
	private $email;
	private $body;

	function __construct($conn, $log, $email) {
		$this->lista = array();
		$this->conn = $conn;
		$this->log = $log;
		$this->email = $email;
		$this->body = '';
		if (file_exists(self::FILENAME)) {
			$this->log->info("Openning file: " . self::FILENAME);
			$f = fopen(self::FILENAME, 'r');
			$this->lista = unserialize(fread($f, filesize(self::FILENAME)));
			fclose($f);
		}
		/*
		  echo "<pre>";
		  print_r($this->lista);
		  echo "</pre>";
		 */
	}

	private function send_email($id, $begin, $body = '') {
		$this->lista[$id][$begin] = time();
		$this->body .= $body;
	}

	function check() {
		//Delete older records
		$older = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 3, date('Y'));
		//$older = mktime(date('H'), date('i')-3, date('s'), date('m'), date('d'), date('Y'));
		foreach ($this->lista as $key0 => $data0) {
			foreach ($data0 as $key1 => $data1) {
				if ($data1 < $older) {
					$this->log->trace("Deleting older record. Process Id:($key0) begin of execution time:($key1) value:($data1)");
					unset($this->lista[$key0][$key1]);
				}
			}
		}
		//Query Database for anomalus executions
		$result = $this->conn->query(self::CRON_QUERY);
		while ($row = $result->fetch_assoc()) {
			if ($row['Error'] != 'Ok') {
				$body = "<p><fieldset>\n";
				$body .= "<legend>&nbsp;<b>Process:</b>{$row['process']}&nbsp;</legend>\n";
				$body .= "<b>Status Message:</b>&nbsp;<em>{$row['Error']}</em><br/>\n";
				$body .= "<b>Beginning of execution:</b>&nbsp;{$row['begin']}<br/>\n";
				$body .= "<b>Current Time:</b>&nbsp;{$row['end']}<br/>\n";
				$body .= "<b>Lower Runnig Expected Time:</b>&nbsp;{$row['min_date']}<br/>\n";
				$body .= "<b>Higher Runnig Expected Time:</b>&nbsp;{$row['max_date']}<br/>\n";
				$body .= "<b>Maximum Runnig Expected Time:</b>&nbsp;{$row['top_date']}<br/>\n";
				$body .= "</fieldset></p>\n";
				if (!isset($this->lista[$row['id']])) {
					$this->log->trace("New Process.  Process id:({$row['id']}), begin of execution time: ({$row['begin']})");
					$this->send_email($row['id'], $row['begin'], $body);
				} else {
					if (!isset($this->lista[$row['id']][$row['begin']])) {
						$this->log->trace("New Begin.  Process id:({$row['id']}), begin of execution time: ({$row['begin']})");
						$this->send_email($row['id'], $row['begin'], $body);
					} else {
						$this->log->trace("Already Registered.  Process id:({$row['id']}), begin of execution time: ({$row['begin']})");
					}
				}
			}
		}
	}

	function save() {
		$f = fopen(self::FILENAME, 'c');
		fwrite($f, serialize($this->lista));
		fclose($f);
		if ($this->body != '') {
			//$this->log->trace("email body:<br/>\n" . $this->body);
			$this->email->Body = $this->body;
			if ($this->email->Send()) {
				$this->log->info("Email with warning messages succesfully sent.");
			} else {
				$this->log->info("There were errros and the Email with warning messages could not be sent." . $this->email->ErrorInfo);
			}
		}
	}

}
