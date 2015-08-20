<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
session_start();
check_permission();
$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$helper = array(
	'SS' => '<span class="label label-primary">Start Script</span>',
	'SI' => '<span class="label label-primary">Start Insertion in BD</span>',
	'EI' => '<span class="label label-default">End Insertion in BD</span>',
	'ES' => '<span class="label label-default">End Script</span>',
	'W' => '<span class="label label-warning">Warning</span>',
	'E' => '<span class="label label-danger">Error</span>',
	'I' => '<span class="label label-info">Info</span>',
);
$result = $conn_dbevents->query("SELECT process, min, max FROM rCron WHERE id={$my_get['process']}") or die($conn->error);
if ($result->num_rows < 1) {
	header('Location: rcrons.php');
}
while ($row = $result->fetch_assoc()) {
	$process = $row['process'];
}
$query = <<<EOD
SELECT A.datetime_event AS `begin`, B.datetime_event AS `end`, A.period_proc, B.processed_rec, B.total_rec, B.files,
IF(NOT ISNULL(B.datetime_event), TIMEDIFF(B.datetime_event, A.datetime_event), NULL) AS `str_diff`,
IF(NOT ISNULL(B.datetime_event), TIMESTAMPDIFF(SECOND, A.datetime_event, B.datetime_event), NULL) AS `diff`
FROM
(
SELECT procseq, datetime_event, period_proc FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='SS' ORDER BY datetime_event DESC LIMIT 500
) AS A
LEFT JOIN ((
SELECT procseq_father, datetime_event, processed_rec, total_rec, files FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='ES' ORDER BY datetime_event DESC LIMIT 500
) AS B) ON (A.procseq=B.procseq_father)
EOD;

$data = new stdClass();
$data->list = array();
$data->cont = 0;
$data->sum = 0;
$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, $query)));
while ($row_rCron = $rs_rCrons->fetch_assoc()) {
	$temp = new stdClass();
	$temp->begin = $row_rCron['begin'];
	$temp->end = $row_rCron['end'];
	$temp->period = $row_rCron['period_proc'];
	$temp->processed_rec = $row_rCron['processed_rec'];
	$temp->total_rec = $row_rCron['total_rec'];
	$temp->files = $row_rCron['files'];
	$temp->diff = $row_rCron['diff'];
	$temp->str_diff = $row_rCron['str_diff'];

	if ($temp->diff) {

		$data->cont++;
		$data->sum += $temp->diff;

		if (!isset($data->min)) {
			$data->min = $temp->diff;
			$data->str_min = $temp->str_diff;
		}
		if (!isset($data->max)) {
			$data->max = $temp->diff;
			$data->str_max = $temp->str_diff;
		}
		if ($temp->diff < $data->min) {
			$data->min = $temp->diff;
			$data->str_min = $temp->str_diff;
		}
		if ($temp->diff > $data->max) {
			$data->max = $temp->diff;
			$data->str_max = $temp->str_diff;
		}
	}

	$data->list[] = $temp;
}
$data->avg = 0;
$data->since = $data->list[0]->begin;
$data->until = $data->list[sizeof($data->list) - 1]->begin;
if ($data->cont != 0) {
	$data->avg = $data->sum / $data->cont;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("an rCron"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'View a rCron']], 'View a rCron', 'Process:&nbsp;' . $process) ?>

					 <section class="content">

						  <div class="box box-primary">
								<div class="box-header with-border">
									 <h3 class="box-title">View a rCron:&nbsp;<?php echo $process; ?></h3>
									 <div class="row">
										  <div class="col-xs-2">Since:</div>
										  <div class="col-xs-4"><?php echo $data->since; ?></div>
										  <div class="col-xs-2">Until:</div>
										  <div class="col-xs-4"><?php echo $data->until; ?></div>
									 </div>
									 <div class="row">
										  <div class="col-xs-2">Minimum:</div>
										  <div class="col-xs-4"><?php echo $data->str_min; ?></div>
										  <div class="col-xs-2">Maximum:</div>
										  <div class="col-xs-4"><?php echo $data->str_max; ?></div>
									 </div>
									 <div class="row">
										  <div class="col-xs-2">Complete Correct Runs:</div>
										  <div class="col-xs-4"><?php echo $data->cont; ?></div>
										  <div class="col-xs-2">Total:</div>
										  <div class="col-xs-4"><?php echo $data->sum; ?> Seconds</div>
									 </div>
									 <div class="row">
										  <div class="col-xs-2">Average:</div>
										  <div class="col-xs-4"><?php echo number_format($data->avg, 2); ?></div>
									 </div>

									 <div class="pull-right box-tools">
										  <button class="btn btn-default btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
									 </div>

								</div>
								<div class="box-body">
									 <!--
									 <pre>
									 <?php //print_r($data);  ?>
									 </pre>
									 -->
									 <table id="table_rcron" class='table table-striped table-bordered'>
										  <thead>
												<tr>
													 <th>Period</th>
													 <th>Begin</th>
													 <th>End</th>
													 <th>Difference</th>
													 <th>processed_rec</th>
													 <th>total_rec</th>
													 <th>files</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												foreach ($data->list as $record) {
													?>
													<tr>
														 <td><?php echo $record->period; ?></td>
														 <td><?php echo $record->begin == '' ? '<span class="label label-warning">Undefined</span>' : $record->begin; ?></td>
														 <td><?php echo $record->end == '' ? '<span class="label label-warning">Unfinished</span>' : $record->end; ?></td>
														 <td class="text-right"><?php echo $record->str_diff; ?></td>
														 <td class="text-right"><?php echo $record->processed_rec; ?></td>
														 <td class="text-right"><?php echo $record->total_rec; ?></td>
														 <td class="text-right"><?php echo $record->files; ?></td>
													</tr>
													<?php
												}
												?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_rcron').dataTable({"order": [[0, "desc"], [1, "desc"]], "displayLength": 25, });
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

