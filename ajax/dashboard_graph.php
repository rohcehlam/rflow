<?php
require_once('../Connections/conn_skybot.php');
require_once('../Connections/conn_dbevents.php');

$labels = array();
$data0 = array();
$data1 = array();
$result_dbevents = $conn_dbevents->query("SELECT PROCESS, LEFT(datetime_event, 15) AS hora, AVG(processed_rec) as records"
	. " FROM logmas_" . date('Ym')
	. " WHERE (PROCESS='oag_global' OR PROCESS='pinkfroot_demo')"
	. "  AND type_proc='ES'"
	. "  AND (datetime_event BETWEEN DATE_SUB(NOW(), INTERVAL 3 HOUR) AND NOW())"
	. " GROUP BY PROCESS, hora"
	. " ORDER BY hora ASC;");
$max_oag = 0;
$max_pf = 0;
while ($row = $result_dbevents->fetch_assoc()) {
	if (!isset($labels[$row['hora']])) {
		$labels[$row['hora']] = "'" . substr($row['hora'], 8, 7) . "0'";
		$data0[$row['hora']] = 0;
		$data1[$row['hora']] = 0;
	}
	if ($row['PROCESS'] == 'oag_global') {
		$data0[$row['hora']] = $row['records'];
		//$data0[] = $row['records'];
		if ($row['records'] > $max_oag) {
			$max_oag = $row['records'];
		}
	} else {
		$data1[$row['hora']] = $row['records'];
		//$data1[] = $row['records'];
		if ($row['records'] > $max_pf) {
			$max_pf = $row['records'];
		}
	}
}
$avg_oag = $max_oag != 0 ? floor(end($data0) / $max_oag * 100) : 0;
$avg_pf = $max_pf != 0 ? floor(end($data1) / $max_pf * 100) : 0;

$result_skybot = $conn_skybot->query("SELECT LEFT(TIMESTAMP, 15) AS hora, AVG(ROWS) AS total_rows FROM table_status_plus WHERE NAME ='air_nav_" . date('Ym') . "' AND SERVER=41 GROUP BY hora ORDER BY id ASC;");
$data2 = array();
$prev = -1;
$max_an = 0;
while ($row = $result_skybot->fetch_assoc()) {
	if ($prev == -1) {
		$prev = $row['total_rows'];
	} else {
		$data2[$row['hora']] = $row['total_rows'] - $prev;
		//$data2[] = $row['total_rows'] - $prev;
		$prev = $row['total_rows'];
		if ($data2[$row['hora']] > $max_an) {
			$max_an = $data2[$row['hora']];
		}
	}
}
$avg_an = $max_an != 0 ? floor(end($data2) / $max_an * 100) : 0;
?>

<div class = "chart">
	 <canvas id = "areaChart" style = "height:120px"></canvas>
</div>
<div class = "row row-border">
	 <div class = "col-md-2">
		  <div class = "description-block">
				<input type = "text" class = "knob" value = "<?php echo $avg_oag; ?>" data-width = "90" data-height = "90" data-fgColor = "rgba(210, 214, 222, 1)" readonly/>
		  </div><!--/.description-block -->
	 </div>
	 <div class = "col-md-2 border-right">
		  <div class = "description-block">
				<span class = "description-header">OAG global</span><br/>
				<span class = "description-text">Max: <?php echo number_format($max_oag, 2); ?></span><br/>
				<span class="description-text">Current: <?php echo number_format(end($data0), 2); ?></span>
				<span class="description-text">Percent: <?php echo $avg_oag; ?>%</span>
		  </div>
	 </div>
	 <div class="col-md-2">
		  <div class="description-block">
				<input type="text" class="knob" value="<?php echo $avg_pf; ?>" data-width="90" data-height="90" data-fgColor="rgba(60,141,188,0.9)" readonly/>
		  </div><!-- /.description-block -->
	 </div>
	 <div class="col-md-2 border-right">
		  <div class="description-block">
				<span class="description-header">Pinkfroot Demo</span><br/>
				<span class="description-text">Max: <?php echo number_format($max_pf, 2); ?></span><br/>
				<span class="description-text">Current: <?php echo number_format(end($data1), 2); ?></span>
				<span class="description-text">Percent: <?php echo $avg_pf; ?>%</span>
		  </div>
	 </div>
	 <div class="col-md-2">
		  <div class="description-block">
				<input type="text" class="knob" value="<?php echo $avg_an; ?>" data-width="90" data-height="90" data-fgColor="Pink" readonly/>
		  </div><!-- /.description-block -->
	 </div>
	 <div class="col-md-2">
		  <div class="description-block">
				<span class="description-header">AirNav</span><br/>
				<span class="description-text">Max: <?php echo number_format($max_an, 2); ?></span><br/>
				<span class="description-text">Current: <?php echo number_format(end($data2), 2); ?></span>
				<span class="description-text">Percent: <?php echo $avg_an; ?>%</span>
		  </div>
	 </div>
</div>
<script>
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
               {
                   label: "OAG Global",
                   fillColor: "rgba(210, 214, 222, 1)",
                   strokeColor: "rgba(210, 214, 222, 1)",
                   pointColor: "rgba(210, 214, 222, 1)",
                   pointStrokeColor: "#c1c7d1",
                   pointHighlightFill: "#fff",
                   pointHighlightStroke: "rgba(220,220,220,1)",
                   data: [<?php echo implode(', ', $data0); ?>]
               },
               {
                   label: "Pinkfroot Demo",
                   fillColor: "rgba(60,141,188,0.9)",
                   strokeColor: "rgba(60,141,188,0.8)",
                   pointColor: "#3b8bba",
                   pointStrokeColor: "rgba(60,141,188,1)",
                   pointHighlightFill: "#fff",
                   pointHighlightStroke: "rgba(60,141,188,1)",
                   data: [<?php echo implode(', ', $data1); ?>]
               },
               {
                   label: "Airnav",
                   fillColor: "Pink",
                   strokeColor: "Pink",
                   pointColor: "Pink",
                   pointStrokeColor: "Pink",
                   pointHighlightFill: "Pink",
                   pointHighlightStroke: "Pink",
                   data: [<?php echo implode(', ', $data2); ?>]
               }
           ]
       };

       areaChartOptions = {
           showScale: true, //Boolean - If we should show the scale at all
           scaleShowGridLines: false, //Boolean - Whether grid lines are shown across the chart
           scaleGridLineColor: "rgba(0,0,0,.05)", //String - Colour of the grid lines
           scaleGridLineWidth: 1, //Number - Width of the grid lines
           scaleShowHorizontalLines: true, //Boolean - Whether to show horizontal lines (except X axis)
           scaleShowVerticalLines: true, //Boolean - Whether to show vertical lines (except Y axis)
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

       $(".knob").knob();
   });
</script>