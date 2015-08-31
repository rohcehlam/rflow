<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'html_tools.php';

/**
 * Description of alarms
 *
 * @author ojimenez
 */
class tServer {

	private $conn;
	private $id;
	private $nombre;
	private $host;
	private $uptime;
	private $t_total;
	private $t_running;
	private $t_sleeping;
	private $t_stopped;
	private $t_zombie;
	private $m_total;
	private $m_used;
	private $m_free;
	private $cpuus;
	private $cpuwa;
	private $cpusy;
	private $cpuid;

	function __construct($conn = null, $id = 0, $nombre = '', $host = '', $uptime = '', $t_total = '', $t_running = '', $t_sleeping = '', $t_stopped = '', $t_zombie = ''
	, $m_total = '', $m_used = '', $m_free = '', $cpuus = '', $cpuwa = '', $cpusy = '', $cpuid = '') {
		$this->conn = $conn;
		$this->id = $id;
		$this->nombre = $nombre;
		$this->host = $host;
		$this->uptime = $uptime;
		$this->t_total = $t_total;
		$this->t_running = $t_running;
		$this->t_sleeping = $t_sleeping;
		$this->t_stopped = $t_stopped;
		$this->t_zombie = $t_zombie;
		$this->m_total = $m_total;
		$this->m_used = $m_used;
		$this->m_free = $m_free;
		$this->cpuus = $cpuus;
		$this->cpuwa = $cpuwa;
		$this->cpusy = $cpusy;
		$this->cpuid = $cpuid;
	}

	function load() {
		$rs_load = $this->conn->query("SELECT nombre, `host` FROM ms_server WHERE id={$this->id}") or die("Error in the query: " . $this->conn->error);
		while ($row = $rs_load->fetch_assoc()) {
			$this->nombre = $row['nombre'];
			$this->host = $row['host'];
		}
	}

	function load_plus() {
		$query_load = <<< EOD
SELECT uptime, t_total, t_running, t_sleeping, t_stopped, t_zombie, m_total, m_used, m_free, cpuus, cpuwa, cpusy, cpuid,
			TIMEDIFF(NOW(), last_report) AS `since`
FROM (
SELECT MAX(id) AS `id` FROM ms_server_%%table_name%% GROUP BY SERVER
) AS A
LEFT JOIN (ms_server_%%table_name%% AS B) ON (A.id = B.id)
WHERE `server`=%%id%%
EOD;
		$rs_load = $this->conn->query(str_replace('%%id%%', $this->id, str_replace('%%table_name%%', date('Ym'), $query_load))) or die("Error in the query: " . $this->conn->error);
		while ($row = $rs_load->fetch_assoc()) {
			$this->uptime = $row['uptime'];
			$this->t_total = $row['t_total'];
			$this->t_running = $row['t_running'];
			$this->t_sleeping = $row['t_sleeping'];
			$this->t_stopped = $row['t_stopped'];
			$this->t_zombie = $row['t_zombie'];
			$this->m_total = $row['m_total'];
			$this->m_used = $row['m_used'];
			$this->m_free = $row['m_free'];
			$this->cpuus = $row['cpuus'];
			$this->cpuwa = $row['cpuwa'];
			$this->cpusy = $row['cpusy'];
			$this->cpuid = $row['cpuid'];
		}
	}

	function form($function) {

		$writer = new thtml_writer($function);

		if (($function == "add") || ($function == "update") || ($function == "delete")) {
			$writer->draw_input('id', 'Server ID', 'id', $this->id, 'Server ID', true);
			$writer->draw_input('nombre', 'Server Name', 'nombre', $this->nombre, 'Server Name');
			$writer->draw_input('host', 'Host Name', 'host', $this->host, 'Host Name');
		} else {
			
		}
	}

	function get_id() {
		return $this->id;
	}

	function get_description() {
		if ($this->nombre) {
			return "{$this->nombre}&nbsp;<i class='fa fa-angle-right'></i>&nbsp;{$this->host}";
		}
	}

	function add() {
		$result = $this->conn->query("SELECT count(*) FROM ms_server WHERE id={$this->id}");
		$row = $result->fetch_row;
		if ($row[0] > 0) {
			$this->error = "Id <strong>{$this->id}</strong> belongs to existing server.";
			return false;
		} else {
			$strquery = "INSERT INTO ms_server (id, nombre, host) VALUES ({$this->id}, '{$this->nombre}', '{$this->host}')";
			$this->conn->query($strquery);
			return true;
		}
	}

	function update() {
		$this->conn->query("UPDATE ms_server SET nombre='{$this->nombre}', host='{$this->host}' WHERE id='{$this->id}';");
	}

	function delete() {
		$this->conn->query("DELETE FROM ms_server WHERE id='{$this->id}'");
	}

}
