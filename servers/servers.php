<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');

session_start();
check_permission();

$args = array(
	'added' => FILTER_SANITIZE_SPECIAL_CHARS,
	'updated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'deleted' => FILTER_SANITIZE_SPECIAL_CHARS,
	'error' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$query = <<< EOD
SELECT S.id, nombre, `host`, uptime, t_total, t_running, t_sleeping, t_stopped, t_zombie, m_total, m_used, m_free, cpuus, cpuwa, cpusy, cpuid,
TIMEDIFF(NOW(), last_report) AS `since`
FROM ms_server AS S
LEFT JOIN (
SELECT A.id, `server`, uptime, t_total, t_running, t_sleeping, t_stopped, t_zombie, m_total, m_used, m_free, cpuus, cpuwa, cpusy, cpuid, last_report
FROM (
SELECT MAX(id) AS `id` FROM ms_server_201508 GROUP BY SERVER
) AS A
LEFT JOIN (ms_server_201508 AS B) ON (A.id = B.id)
) AS C 
ON (S.id=C.server)
EOD;
$result = $conn_dbevents->query($query) or die($conn_dbevents->error);

function beautify_uptime($text) {
	if ($text) {
		$temp = explode(',', $text);
		list($dummy, $days) = explode('up', $temp[0]);
		return "$days, {$temp[1]}";
	} else {
		return "<span class='label label-default'>No Data Available</span>\n";
	}
}

function color_bar($value) {
	if ($value > 90) {
		$color = 'danger';
	} elseif ($value > 75) {
		$color = 'warning';
	} else {
		$color = 'success';
	}
	return $color;
}

function progress_task($t_total, $t_running, $t_sleeping, $t_stopped, $t_zombie) {
	if ($t_total != '') {
		$curr = ceil(($t_running + $t_stopped + $t_zombie) / $t_total * 100);
		?>
		<div class="progress progress-sm">
			 <div class="progress-bar progress-bar-<?php echo color_bar($curr); ?>" role="progressbar" aria-valuenow="<?php echo $curr; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $curr; ?>%">
				  <span class="sr-only"><?php echo $curr; ?>% Complete</span>
			 </div>
		</div>
		<?php
	}
}

function progress_memory($m_total, $m_used, $m_free) {
	if ($m_total != '') {
		$curr = ceil(($m_used) / $m_total * 100);
		?>
		<div class="progress progress-sm">
			 <div class="progress-bar progress-bar-<?php echo color_bar($curr); ?>" role="progressbar" aria-valuenow="<?php echo $curr; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $curr; ?>%">
				  <span class="sr-only"><?php echo $curr; ?>% Complete</span>
			 </div>
		</div>
		<?php
	}
}

function progress_cpu($cpuus, $cpuwa, $cpusy, $cpuid) {
	if ($cpuus) {
		$curr = ceil($cpuus + $cpusy + $cpuwa);
		?>
		<div class="progress progress-sm">
			 <div class="progress-bar progress-bar-<?php echo color_bar($curr); ?>" role="progressbar" aria-valuenow="<?php echo $curr; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $curr; ?>%">
				  <span class="sr-only"><?php echo $curr; ?>% Complete</span>
			 </div>
		</div>
		<?php
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Servers"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">

				<?php build_navbar(); ?>
				<?php build_sidebar(8); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => '', 'text' => 'Servers']], 'Servers', $filter_text) ?>

					 <section class="content">

						  <?php
						  if (isset($my_get['added'])) {
							  draw_message('success', 'Success!', 'New Server Succesfully Created');
						  }
						  if (isset($my_get['updated'])) {
							  draw_message('info', 'Success!', 'Server Succesfully Updated');
						  }
						  if (isset($my_get['deleted'])) {
							  draw_message('warning', 'Success!', 'Server Succesfully Deleted');
						  }
						  if (isset($my_get['error'])) {
							  draw_message('danger', 'Error!', str_replace('\n', '<br/>', $my_get['error']));
						  }
						  ?>

						  <div class="box box-primary">
								<div class='box-header with-border'>
									 <div class='col-xs-6'>
										  <a class='btn btn-primary' href='server.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add a Server</a>
									 </div>
								</div>
								<div class="box-body">
									 <table id="table_servers" class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>ID</th>
													 <th>Server</th>
													 <th>Host</th>
													 <th>Uptime</th>
													 <th>Last report</th>
													 <th>Tasks</th>
													 <th>Memory</th>
													 <th>CPU</th>
													 <th>&nbsp;</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td><?php echo $row['id']; ?></td>
														 <td>
															  <a href='server.php?function=view&id=<?php echo $row['id']; ?>'><?php echo $row['nombre']; ?></a>
															  <?php
															  if (isset($my_get['added']) && $my_get['added'] == $row['id']) {
																  echo "<span class='label label-success pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;New</span>\n";
															  }
															  ?>
															  <?php
															  if (isset($my_get['updated']) && $my_get['updated'] == $row['id']) {
																  echo "<span class='label label-info pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;Updated</span>\n";
															  }
															  ?>
														 </td>
														 <td><?php echo $row['host']; ?></td>
														 <td><?php echo beautify_uptime($row['uptime']); ?></td>
														 <td><?php echo $row['since']; ?></td>

														 <td><?php echo progress_task($row['t_total'], $row['t_running'], $row['t_sleeping'], $row['t_stopped'], $row['t_zombie']); ?></td>
														 <td><?php echo progress_memory($row['m_total'], $row['m_used'], $row['m_free']); ?></td>
														 <td><?php echo progress_cpu($row['cpuus'], $row['cpuwa'], $row['cpusy'], $row['cpuid']); ?></td>

														 <td>
															  <a href='server.php?function=view&id=<?php echo $row['id']; ?>'><i class='fa fa-eye'></i></a>
															  &nbsp;<a href='server.php?function=update&id=<?php echo $row['id']; ?>'><i class='fa fa-pencil'></i></a>
															  &nbsp;<a href='server.php?function=delete&id=<?php echo $row['id']; ?>'><i class='fa fa-remove'></i></a>
														 </td>

													</tr>
													<?php
												}
												?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_servers').dataTable({"order": [[1, "asc"]], "displayLength": 25, });
                               });
									 </script>
								</div>
						  </div>

					 </section>
				</div>
				<?php build_footer(); ?>
		  </div>
	 </body>
</html>
