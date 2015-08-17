<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
session_start();
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

$query = <<<EOD
SELECT  type_proc, datetime_event, period_proc, processed_rec, total_rec, files
FROM (
 SELECT type_proc, datetime_event, period_proc, processed_rec, total_rec, files
 FROM logmas_%%fecha%%
 WHERE PROCESS='%%process%%'
 ORDER BY period_proc desc
	 LIMIT 1000
	) AS A
ORDER BY period_proc ASC
		 ;
EOD;

$data = new stdClass();
$data->list = array();
$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $my_get['process'], $query)));
while ($row_rCron = $rs_rCrons->fetch_assoc()) {
	switch ($row_rCron['type_proc']) {
		case 'SS':
			if (isset($temp)) {
				$temp->end = '';
				$data->list[] = $temp;
				unset($temp);
			}
			$temp = new stdClass();
			$temp->begin = $row_rCron['datetime_event'];
			$temp->period = $row_rCron['period_proc'];
			break;
		case 'ES':
			if (isset($temp)) {
				$temp->end = $row_rCron['datetime_event'];
				$temp->processed_rec = $row_rCron['processed_rec'];
				$temp->total_rec = $row_rCron['total_rec'];
				$temp->files = $row_rCron['files'];
				$data->list[] = $temp;
				unset($temp);
			} else {
				$temp = new stdClass();
				$temp->begin = '';
				$temp->period = $row_rCron['period_proc'];
				$temp->end = $row_rCron['datetime_event'];
				$temp->processed_rec = $row_rCron['processed_rec'];
				$temp->total_rec = $row_rCron['total_rec'];
				$temp->files = $row_rCron['files'];
				$data->list[] = $temp;
				unset($temp);
			}
			break;
	}
}
$data->avg = 0;
$data->cont = 0;
$data->sum = 0;
$data->since = $data->list[0]->begin;
if (isset($temp)){
	$data->list[] = $temp;
	unset($temp);
}
for ($i = 0; $i <= sizeof($data->list) - 1; $i++) {
	if ($data->list[$i]->begin != '' && $data->list[$i]->end != '') {
		$start = DateTime::createFromFormat('Y-m-d H:i:s', $data->list[$i]->begin);
		$end = DateTime::createFromFormat('Y-m-d H:i:s', $data->list[$i]->end);
		$interval = $end->diff($start);
		$data->list[$i]->diff = $interval->format('%s') + ($interval->format('%i') * 60) + ($interval->format('%h') * 60 * 60);
		$data->list[$i]->str_diff = '';
		if ($interval->format('%h') != 0) {
			$data->list[$i]->str_diff .= $interval->format('%hh');
		}
		if ($interval->format('%i') != 0) {
			$data->list[$i]->str_diff .= $interval->format('%im');
		}
		if ($interval->format('%s') != 0) {
			$data->list[$i]->str_diff .= $interval->format('%ss');
		}
		//$data->list[$i]->str_diff = $interval->format('%hH %iM %sS');
		if (!isset($data->min)) {
			$data->min = $data->list[$i]->diff;
		}
		if (!isset($data->max)) {
			$data->max = $data->list[$i]->diff;
		}
		if ($data->list[$i]->diff < $data->min) {
			$data->min = $data->list[$i]->diff;
		}
		if ($data->list[$i]->diff > $data->max) {
			$data->max = $data->list[$i]->diff;
		}
		$data->cont++;
		$data->sum += $data->list[$i]->diff;
	}
}
$data->until = $data->list[sizeof($data->list) - 1]->end;
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

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'View a rCron']], 'View a rCron', 'Process:&nbsp;' . $my_get['process']) ?>

					 <section class="content">

						  <div class="box box-primary">
								<div class="box-header with-border">
									 <h3 class="box-title">View a rCron:&nbsp;<?php echo $my_get['process']; ?></h3>
									 <div class="row">
										  <div class="col-xs-2">Since:</div>
										  <div class="col-xs-4"><?php echo $data->since; ?></div>
										  <div class="col-xs-2">Until:</div>
										  <div class="col-xs-4"><?php echo $data->until; ?></div>
									 </div>
									 <div class="row">
										  <div class="col-xs-2">Minimum:</div>
										  <div class="col-xs-4"><?php echo $data->min; ?> Seconds</div>
										  <div class="col-xs-2">Maximum:</div>
										  <div class="col-xs-4"><?php echo $data->max; ?> Seconds</div>
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

