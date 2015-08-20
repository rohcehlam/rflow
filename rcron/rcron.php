<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
session_start();
check_permission();
$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
	'datetimerange' => FILTER_SANITIZE_SPECIAL_CHARS,
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
SELECT procseq, datetime_event, period_proc FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='SS' %%where%% ORDER BY datetime_event DESC LIMIT 500
) AS A
LEFT JOIN ((
SELECT procseq_father, datetime_event, processed_rec, total_rec, files FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='ES' %%where%% ORDER BY datetime_event DESC LIMIT 500
) AS B) ON (A.procseq=B.procseq_father)
EOD;
$where = '';
if ($my_get['datetimerange']) {
	$temp = explode(' - ', $my_get['datetimerange']);
	$begin_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[0]);
	$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[1]);
	$where = " AND (datetime_event BETWEEN '{$begin_date->format('Y-m-d H:i:s')}' AND '{$end_date->format('Y-m-d H:i:s')}')";
}
$data = new stdClass();
$data->list = array();
$data->cont = 0;
$data->sum = 0;
//echo str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, $query)));
$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, $query)))) or die($conn_dbevents->error);
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
		  <link rel="stylesheet" href="../js/daterangepicker/daterangepicker-bs3.css"/>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'View a rCron']], 'View a rCron', 'Process:&nbsp;' . $process) ?>

					 <section class="content">

						  <div class="box box-default">
								<div class="box-header with-border">
									 <h3 class="box-title">Summary</h3>
									 <div class="pull-right box-tools">
										  <button class="btn btn-default btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
									 </div>

								</div>
								<div class="box-body">
									 <form class="form-horizontal">
										  <div class="form-group">
												<label class="control-label col-xs-2">Minimum: </label>
												<div class="col-xs-4">
													 <input class="form-control text-right" value="<?php echo $data->str_min; ?>" readonly/>
												</div>
												<label class="control-label col-xs-2">Maximum: </label>
												<div class="col-xs-4">
													 <input class="form-control text-right" value="<?php echo $data->str_max; ?>" readonly/>
												</div>
										  </div>
										  <div class="form-group">
												<label class="control-label col-xs-2">Complete Correct Runs: </label>
												<div class="col-xs-4">
													 <input class="form-control text-right" value="<?php echo $data->cont; ?>" readonly/>
												</div>
												<label class="control-label col-xs-2">Total in Seconds: </label>
												<div class="col-xs-4">
													 <input class="form-control text-right" value="<?php echo $data->sum; ?>" readonly/>
												</div>
										  </div>
										  <div class="form-group">
												<label class="control-label col-xs-2">Average: </label>
												<div class="col-xs-4">
													 <input class="form-control text-right" value="<?php echo number_format($data->avg, 2); ?>" readonly/>
												</div>
										  </div>
									 </form>
								</div>
						  </div>
						  <div class="box box-primary">
								<div class="box-header with-border">
									 <form class="form-horizontal">
										  <div class="form-group">
												<label class="control-label col-xs-2">Date &amp; Time Range</label>
												<div class="col-xs-6">
													 <div class="input-group">
														  <input type="text" id="sinceuntil" value="<?php echo $data->until; ?> - <?php echo $data->since; ?>" name="datetimerange" class="form-control"></input>
														  <input type="hidden" name="process" value="<?php echo $my_get['process']; ?>"/>
														  <span class="input-group-btn">
																<button class="btn btn-primary" type="submit">Go!</button>
														  </span>
													 </div>
												</div>
												<div class="col-xs-4">

												</div>
										  </div>
									 </form>
									 <div class="pull-right box-tools">
										  <button class="btn btn-default btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
									 </div>
								</div>
								<div class="box-body">
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
                                   $('#sinceuntil').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD HH:mm:ss'});
                                   $('#table_rcron').dataTable({"order": [[0, "desc"], [1, "desc"]], "displayLength": 25, });
                               });
									 </script>
									 <script src="../js/daterangepicker/moment.min.js"></script>
									 <script src="../js/daterangepicker/daterangepicker.js"></script>
								</div>
						  </div>
					 </section>
				</div>
				<?php build_footer(); ?>
		  </div>
	 </body>
</html>

