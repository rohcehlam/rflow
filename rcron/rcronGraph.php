<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
session_start();
check_permission();
$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
	'datetimerange' => FILTER_SANITIZE_SPECIAL_CHARS,
	'groupedby' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$result = $conn_dbevents->query("SELECT process, `min`, `max`, `top` FROM rCron WHERE id={$my_get['process']}") or die($conn->error);
if ($result->num_rows < 1) {
	header('Location: rcrons.php');
}
while ($row = $result->fetch_assoc()) {
	$process = $row['process'];
	$min = $row['min'];
	$max = $row['max'];
	$top = $row['top'];
}
$query = <<<EOD
SELECT %%period%% AS period,
IFNULL(AVG(TIMESTAMPDIFF(SECOND, A.datetime_event, B.datetime_event)), 0) AS `diff`
FROM
(
SELECT procseq, datetime_event, period_proc FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='SS' %%where%% ORDER BY datetime_event DESC
--	LIMIT 3000
) AS A
LEFT JOIN ((
SELECT procseq_father, datetime_event, processed_rec, total_rec, files FROM logmas_%%fecha%% WHERE `process`='%%process%%' AND type_proc='ES' %%where%% ORDER BY datetime_event DESC
--	LIMIT 3000
) AS B) ON (A.procseq=B.procseq_father)
GROUP BY period
order by period desc
-- limit 100
EOD;
//$where = '';
$period = "concat(date_format(A.datetime_event, '%d/%m %H'), ':00')";
if (!$my_get['datetimerange']) {
	$my_get['datetimerange'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('Y'))) . " - " . date('Y-m-d H:i:s');
}
$temp = explode(' - ', $my_get['datetimerange']);
$begin_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[0]);
$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $temp[1]);
$where = " AND (datetime_event BETWEEN '{$begin_date->format('Y-m-d H:i:s')}' AND '{$end_date->format('Y-m-d H:i:s')}')";

if ($my_get['groupedby']) {
	switch ($my_get['groupedby']) {
		case 1:
			//$period = "CONCAT(MID(A.period_proc, 7, 2), '/',MID(A.period_proc, 5, 2),' ',MID(A.period_proc, 10, 2),':',MID(A.period_proc, 13, 1),'0'  )";
			//$period = "CONCAT(DAY(A.datetime_event), '/', MONTH(A.datetime_event), ' ', HOUR(A.datetime_event), ':', LEFT(MINUTE(A.datetime_event), 1), '0')";
			$period = "CONCAT(date_format(A.datetime_event, '%d/%m %H:'), floor(MINUTE(A.datetime_event) / 10), '0')";
			break;
		case 2:
			$period = "concat(date_format(A.datetime_event, '%d/%m %H'), ':00')";
			break;
		case 3:
			$period = "DATE_FORMAT(A.datetime_event, '%d/%m')";
			break;
	}
}
$labels = array();
$data = array();
//echo str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, str_replace('%%period%%', $period, $query))));
$rs_rCrons = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $process, str_replace('%%where%%', $where, str_replace('%%period%%', $period, $query))))) or die($conn_dbevents->error);
while ($row_rCron = $rs_rCrons->fetch_assoc()) {
	if ($row_rCron['period'] != '') {
		$labels[] = "'{$row_rCron['period']}'";
		$data[] = $row_rCron['diff'];
		$vmin[] = $min;
		$vmax[] = $max;
		$vtop[] = $top;
	}
}
$labels = array_reverse($labels);
$data = array_reverse($data);
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

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'rCron Graphic']], 'rCron Graphic', 'Process:&nbsp;' . $process) ?>

					 <section class="content">

						  <div class="box box-primary">
								<div class="box-header with-border">
									 <form class="form-horizontal">
										  <input type="hidden" name="process" value="<?php echo $my_get['process']; ?>"/>
										  <div class="form-group">
												<label class="control-label col-xs-2">Date &amp; Time Range</label>
												<div class="col-xs-4">
													 <input type="text" id="sinceuntil" value="<?php echo $my_get['datetimerange']; ?>" name="datetimerange" class="form-control"></input>
												</div>
												<label class="control-label col-xs-1">Grouped By</label>
												<div class="col-xs-2">
													 <select name="groupedby" class="form-control">
														  <option value="1"<?php echo $my_get['groupedby'] == '1' ? ' selected="selected"' : '' ?>>10 Minutes</option>
														  <option value="2"<?php echo (!$my_get['groupedby'] || $my_get['groupedby'] == '2') ? ' selected="selected"' : '' ?>>Hour</option>
														  <option value="3"<?php echo $my_get['groupedby'] == '3' ? ' selected="selected"' : '' ?>>Day</option>
													 </select>
												</div>
												<div class="col-xs-2">
													 <button class="btn btn-primary" type="submit">Render Graphic</button>
												</div>
										  </div>
									 </form>
									 <div class="pull-right box-tools">
										  <button class="btn btn-default btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
									 </div>
								</div>
								<div class="box-body">
									 <div class = "chart">
										  <canvas id = "areaChart" style = "height:240px"></canvas>
									 </div>
									 <script type="text/javascript">
                               var areaChart;
                                       var areaChartData;
                                       var areaChartOptions;
                                       var areaChartCanvas;
                                       $(function () {
                                       areaChartCanvas = $("#areaChart").get(0).getContext("2d");
                                               // This will get the first returned node in the jQuery collection.

                                               areaChartData = {
                                               labels: [<?php echo implode(', ', $labels); ?>],
                                                       datasets: [
<?php if ($top) { ?>
	                                                       {
	                                                       label: "Ceiling",
	                                                               fillColor: "#f56954",
	                                                               strokeColor: "#f56954",
	                                                               pointColor: "#f56954",
	                                                               pointStrokeColor: "#f56954",
	                                                               pointHighlightFill: "#f56954",
	                                                               pointHighlightStroke: "#f56954",
	                                                               data: [<?php echo implode(', ', $vtop); ?>]
	                                                       },
<?php } ?>
<?php if ($max) { ?>
	                                                       {
	                                                       label: "Maximum",
	                                                               fillColor: "#3c8dbc",
	                                                               strokeColor: "#3c8dbc",
	                                                               pointColor: "#3c8dbc",
	                                                               pointStrokeColor: "#3c8dbc",
	                                                               pointHighlightFill: "#3c8dbc",
	                                                               pointHighlightStroke: "#3c8dbc",
	                                                               data: [<?php echo implode(', ', $vmax); ?>]
	                                                       },
<?php } ?>
                                                       {
                                                       label: "<?php echo $process; ?>",
                                                               fillColor: "rgba(210, 214, 222, 1)",
                                                               strokeColor: "rgba(210, 214, 222, 1)",
                                                               pointColor: "rgba(210, 214, 222, 1)",
                                                               pointStrokeColor: "#c1c7d1",
                                                               pointHighlightFill: "#fff",
                                                               pointHighlightStroke: "rgba(220,220,220,1)",
                                                               data: [<?php echo implode(', ', $data); ?>]
                                                       }
<?php if ($min) { ?>
	                                                       , {
	                                                       label: "Minimum",
	                                                               fillColor: "#39CCCC",
	                                                               strokeColor: "#39CCCC",
	                                                               pointColor: "#39CCCC",
	                                                               pointStrokeColor: "#39CCCC",
	                                                               pointHighlightFill: "#39CCCC",
	                                                               pointHighlightStroke: "#39CCCC",
	                                                               data: [<?php echo implode(', ', $vmin); ?>]
	                                                       }
<?php } ?>
                                                       ]
                                               };
                                               areaChartOptions = {
                                               showScale: true, //Boolean - If we should show the scale at all
                                                       scaleShowGridLines: true, //Boolean - Whether grid lines are shown across the chart
                                                       scaleGridLineColor: "rgba(0,0,0,.05)", //String - Colour of the grid lines
                                                       scaleGridLineWidth: 1, //Number - Width of the grid lines
                                                       scaleShowHorizontalLines: true, //Boolean - Whether to show horizontal lines (except X axis)
                                                       scaleShowVerticalLines: false, //Boolean - Whether to show vertical lines (except Y axis)
                                                       bezierCurve: true, //Boolean - Whether the line is curved between points
                                                       bezierCurveTension: 0.3, //Number - Tension of the bezier curve between points
                                                       pointDot: false, //Boolean - Whether to show a dot for each point
                                                       pointDotRadius: 4, //Number - Radius of each point dot in pixels
                                                       pointDotStrokeWidth: 1, //Number - Pixel width of point dot stroke
                                                       pointHitDetectionRadius: 20, //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                                                       datasetStroke: true, //Boolean - Whether to show a stroke for datasets
                                                       datasetStrokeWidth: 2, //Number - Pixel width of dataset stroke
                                                       datasetFill: false, //Boolean - Whether to fill the dataset with a color
                                                       legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                                                       maintainAspectRatio: true, //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                                                       responsive: true//Boolean - whether to make the chart responsive to window resizing
                                               };
                                               areaChart = new Chart(areaChartCanvas).Line(areaChartData, areaChartOptions);
                                       });
                                       $(document).ready(function () {
                               $('#sinceuntil').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD HH:mm:ss'});
                               });</script>
									 <script src="../js/Chart.min.js" type="text/javascript"></script>
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

