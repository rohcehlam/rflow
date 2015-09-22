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

if (!$my_get['datetimerange']) {
	if ($asi_frequency < 10) {
		$my_get['datetimerange'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 7, date('Y'))) . " - " . date('Y-m-d H:i:s');
	} else {
		$my_get['datetimerange'] = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 1, date('Y'))) . " - " . date('Y-m-d H:i:s');
	}
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
									 <div id="graphic_area"></div>
									 <style>

										  body {
												font: 10px sans-serif;
										  }

										  .axis path,
										  .axis line {
												fill: none;
												stroke: #000;
												shape-rendering: crispEdges;
										  }

										  .x.axis path {
												display: none;
										  }

										  .line {
												fill: none;
												stroke: steelblue;
												stroke-width: 1.5px;
										  }
										  .top_line {
												fill: none;
												stroke: red;
												stroke-width: 1.5px;
										  }

									 </style>
									 <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
									 <script>

                               var margin = {top: 20, right: 20, bottom: 30, left: 50},
                               width = 960 - margin.left - margin.right,
                                       height = 500 - margin.top - margin.bottom;

                               var parseDate = d3.time.format("%Y-%m-%d %H:%M").parse;

                               var x = d3.time.scale()
                                       .range([0, width]);

                               var y = d3.scale.linear()
                                       .range([height, 0]);

                               var xAxis = d3.svg.axis()
                                       .scale(x)
                                       .orient("bottom");

                               var yAxis = d3.svg.axis()
                                       .scale(y)
                                       .orient("left");

                               var line = d3.svg.line()
                                       .x(function (d) {
                                           return x(d.date);
                                       })
                                       .y(function (d) {
                                           return y(d.value);
                                       });

                               var svg = d3.select("#graphic_area").append("svg")
                                       .attr("width", width + margin.left + margin.right)
                                       .attr("height", height + margin.top + margin.bottom)
                                       .append("g")
                                       .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

                               d3.tsv("rcronGraph.tsv.php?process=<?php echo $my_get['process']; ?>&datetimerange=<?php echo $my_get['datetimerange']; ?>&groupedby=<?php echo $my_get['groupedby']; ?>", function (error, data) {
                                   if (error)
                                       throw error;

                                   data.forEach(function (d) {
                                       d.date = parseDate(d.date);
                                       d.value = +d.value;
                                   });

                                   x.domain(d3.extent(data, function (d) {
                                       return d.date;
                                   }));
                                   y.domain(d3.extent(data, function (d) {
                                       return d.value;
                                   }));

                                   svg.append("g")
                                           .attr("class", "x axis")
                                           .attr("transform", "translate(0," + height + ")")
                                           .call(xAxis);

                                   svg.append("g")
                                           .attr("class", "y axis")
                                           .call(yAxis)
                                           .append("text")
                                           .attr("transform", "rotate(-90)")
                                           .attr("y", 6)
                                           .attr("dy", ".71em")
                                           .style("text-anchor", "end")
                                           .text("Time taken in Seconds");

                                   svg.append("path")
                                           .datum(data)
                                           .attr("class", "line")
                                           .attr("d", line);
                               });
                               $(document).ready(function () {
                                   $('#sinceuntil').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD HH:mm:ss'});
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

