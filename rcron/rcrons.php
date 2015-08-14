<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');

session_start();
check_permission();

$cron_query = <<<EOD
SELECT Z.process, Z.datetime_event AS 'start_time', D.datetime_event AS 'end_time', NOW(), processed_rec, files
	, IF(Z.datetime_event > D.datetime_event, TRUE, FALSE) AS `running`
	, IF(Z.datetime_event > D.datetime_event, TIMEDIFF(NOW(), D.datetime_event), TIMEDIFF(D.datetime_event , Z.datetime_event)) AS `run_time`
	, TIMESTAMPDIFF(MINUTE, D.datetime_event, NOW()) AS `idle`
--	, concat(TIMESTAMPDIFF(HOUR, D.datetime_event, NOW()), 'h ', TIMESTAMPDIFF(MINUTE, D.datetime_event, NOW()), 'm') AS `idle`
FROM ( SELECT procseq, X.process, datetime_event FROM logmas_%%table_name%% AS X, 
			(SELECT PROCESS, MAX(procseq) AS procseq_ss FROM logmas_%%table_name%% WHERE type_proc='SS' GROUP BY PROCESS) AS A
		WHERE X.procseq=A.procseq_ss AND X.process = A.process ) AS Z
LEFT JOIN (
	( SELECT procseq, Y.process, datetime_event, processed_rec, files FROM logmas_%%table_name%% AS Y, 
			(SELECT PROCESS, MAX(procseq) AS procseq_es FROM logmas_%%table_name%% WHERE type_proc='ES' GROUP BY PROCESS) AS B
		WHERE Y.procseq=B.procseq_es AND Y.process = B.process ) AS D
	)
ON (Z.process = D.process);
EOD;

function get_date($format, $time) {
	$temp = DateTime::createFromFormat('Y-m-d H:i:s', $time);
	return $temp->format($format);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("rCrons"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">

				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => '', 'text' => 'rCrons']], 'rCrons', $filter_text) ?>

					 <section class="content">

						  <div class="box box-primary">

								<div class="box-body">
									 <table id="table_rcrons" class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>Process</th>
													 <th>Last Run</th>
													 <th>Start Time</th>
													 <th>End Time</th>
													 <th>Running</th>
													 <th>Run Time</th>
													 <th>Files</th>
													 <th>Records</th>
													 <th>Idle</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												$result = $conn_dbevents->query(str_replace('%%table_name%%', date('Ym'), $cron_query)) or die("<div class='callout callout-danger lead'><h4>Error!</h4><p>{$conn->error}</p></div>");
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td><a href='rcron.php?process=<?php echo $row['process']; ?>'><?php echo $row['process']; ?></a></td>
														 <td><?php echo get_date('m/d/Y', $row['start_time']); ?></td>
														 <td class='text-right'><?php echo get_date('H:i:s', $row['start_time']); ?></td>
														 <td class='text-right'><?php echo get_date('H:i:s', $row['end_time']); ?></td>
														 <td><?php echo $row['running'] ? "<span class='label label-info'>Running</span>" : "<span class='label label-success'>Finished!</span>"; ?></td>
														 <td class='text-right'><?php echo $row['running'] ? "<span class='text-blue'>{$row['run_time']}</span>" : $row['run_time']; ?></td>
														 <td class='text-right'><?php echo $row['files']; ?></td>
														 <td class='text-right'><?php echo $row['processed_rec']; ?></td>
														 <?php
														 $x = $row['idle'];
														 $h = floor($x / 60);
														 if ($h == 0) {
															 $idle = "{$x}m";
														 } else {
															 $m = $x - ($h * 60);
															 if ($h > 24) {
																 $idle = "<span class='text-red'>{$h}h {$m}m</span>";
															 } else {
																 $idle = "{$h}h {$m}m";
															 }
														 }
														 ?>
														 <td class='text-right'><?php echo $idle; ?></td>
													</tr>
													<?php
												}
												?>

										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_rcrons').dataTable({"order": [[4, "desc"], [1, "asc"], [0, "asc"]], "displayLength": 25, });
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
