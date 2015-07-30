<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

session_start();
// ** Logout the current user. **
$logoutAction = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS) . "?doLogout=true";
if (filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$logoutAction .="&" . htmlentities(filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS));
}

if ((filter_input(INPUT_GET, 'doLogout', FILTER_SANITIZE_SPECIAL_CHARS) == "true")) {
//to fully log out a visitor we need to clear the session varialbles
	unset($_SESSION['MM_Username']);
	unset($_SESSION['MM_UserGroup']);

	$logoutGoTo = "index.php?loggedoff=y";
	if ($logoutGoTo) {
		header("Location: $logoutGoTo");
		exit;
	}
}

$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

$label_colors = array(
	"Open" => 'primary',
	"Analysis" => 'info',
	"Closed" => 'success',
	"In Progress" => 'default',
	"On Hold" => 'warning',
	"Returned" => 'danger'
);

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
// For security, start by assuming the visitor is NOT authorized.
	$isValid = False;

// When a visitor has logged into this site, the Session variable MM_Username set equal to their username.
// Therefore, we know that a user is NOT logged in if that Session variable is blank.
	if (!empty($UserName)) {
// Besides being logged in, you may restrict access to only certain users based on an ID established when they login.
// Parse the strings into arrays.
		$arrUsers = Explode(",", $strUsers);
		$arrGroups = Explode(",", $strGroups);
		if (in_array($UserName, $arrUsers)) {
			$isValid = true;
		}
// Or, you may restrict access to only certain users based on their username.
		if (in_array($UserGroup, $arrGroups)) {
			$isValid = true;
		}
		if (($strUsers == "") && true) {
			$isValid = true;
		}
	}
	return $isValid;
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
	$MM_qsChar = "?";
	$MM_referrer = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
	if (strpos($MM_restrictGoTo, "?")) {
		$MM_qsChar = "&";
	}
	if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) {
		$MM_referrer .= "?" . $QUERY_STRING;
	}
	$MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	header("Location: " . $MM_restrictGoTo);
	exit;
}

$varEmployee = (isset($_SESSION['employee']) ? addslashes($_SESSION['employee']) : "1");

//my projects
$query_rsMyProjects = "SELECT projectID, projectName, status, applications.application, customers.customer, organizingEngineerID"
	. ", DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate, wrm, ticket"
	. " FROM projects"
	. " LEFT JOIN applications ON projects.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON projects.primaryCustomerID=customers.customerID"
	. " WHERE organizingEngineerID = $varEmployee AND status <> 'Completed'"
	. " ORDER BY targetDate ASC";
$rsMyProjects = $conn->query($query_rsMyProjects) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsMyProjects = $rsMyProjects->fetch_assoc();
$totalRows_rsMyProjects = $rsMyProjects->num_rows;

//my support requests
$query_rsSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket"
	. ", customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON escalations.customerID=customers.customerID"
	. " WHERE assignedTo = $varEmployee AND status <> 'Closed' AND status <> 'Returned'"
	. " ORDER BY targetDate ASC";
$rsSupportRequests = $conn->query($query_rsSupportRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsSupportRequests = $rsSupportRequests->fetch_assoc();
//$totalRows_rsSupportRequests = $rsSupportRequests->num_rows;
//pending maintenances
$query_rsPendingMaintenances = "SELECT maintenanceNotifsID, reason, TIME_FORMAT(startTime,'%k:%i') AS startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate"
	. " FROM maintenancenotifs"
	. " WHERE status = 'Open' OR status='Extended'"
	. " ORDER BY startDate DESC, startTime DESC";
$rsPendingMaintenances = $conn->query($query_rsPendingMaintenances) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsPendingMaintenances = $rsPendingMaintenances->fetch_assoc();
//$totalRows_rsPendingMaintenances = $rsPendingMaintenances->num_rows;

$query_rsEmployeeInfo = "SELECT employeeID, firstName, displayName FROM employees WHERE employeeID = $varEmployee";
$rsEmployeeInfo = $conn->query($query_rsEmployeeInfo) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsEmployeeInfo = $rsEmployeeInfo->fetch_assoc();
$totalRows_rsEmployeeInfo = $rsEmployeeInfo->num_rows;

//unassigned support requests
$query_rsUnassignedSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status"
	. ", ticket, customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON escalations.customerID=customers.customerID"
	. " WHERE assignedTo='48' AND status <> 'Closed' AND status <> 'Returned'"
	. " ORDER BY targetDate ASC";
$rsUnassignedSupportRequests = $conn->query($query_rsUnassignedSupportRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->fetch_assoc();
//$totalRows_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->num_rows;
//my project tasks
$query_rsMyProjectTasks = "SELECT projectevents.projectEventID, projectevents.projectEvent, projectevents.projectID, projects.projectName, projectevents.order"
	. ", projectevents.engineerID, employees.displayName, DATE_FORMAT(projectevents.targetDate, '%m/%d/%Y') as targetDate, projectevents.status"
	. " FROM projectevents"
	. " LEFT JOIN projects ON projects.projectID=projectevents.projectID"
	. " LEFT JOIN employees ON employees.employeeID=projectevents.engineerID"
	. " WHERE projectevents.engineerID = '$varEmployee' AND projectevents.status <> 'Complete'"
	. " ORDER BY projectevents.projectID ASC, projectevents.order";
$rsMyProjectTasks = $conn->query($query_rsMyProjectTasks) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsMyProjectTasks = $rsMyProjectTasks->fetch_assoc();
$totalRows_rsMyProjectTasks = $rsMyProjectTasks->num_rows;
$labels = array();
$data0 = array();
$data1 = array();
$conn0 = new mysqli("23.23.213.234", "mfroot", "Mfroo7", "masflightdb");
$result = $conn0->query("SELECT PROCESS, LEFT(datetime_event, 15) AS hora, AVG(processed_rec) as records"
	. " FROM logmas_201507"
	. " WHERE (PROCESS='oag_global' OR PROCESS='pinkfroot_demo')"
	. "  AND type_proc='ES'"
	. "  AND (datetime_event BETWEEN DATE_SUB(NOW(), INTERVAL 1 HOUR) AND NOW())"
	. " GROUP BY PROCESS, hora"
	. " ORDER BY hora ASC;");
$max_oag = 0;
$max_pf = 0;
while ($row = $result->fetch_assoc()) {
	//if (!array_search($row['hora'], $labels)) {
	if (!isset($labels[$row['hora']])) {
		$labels[$row['hora']] = "'" . substr($row['hora'], 8, 7) . "0'";
		$data0[$row['hora']] = 0;
		$data1[$row['hora']] = 0;
	}
	if ($row['PROCESS'] == 'oag_global') {
		$data0[$row['hora']] = $row['records'];
		if ($row['records'] > $max_oag) {
			$max_oag = $row['records'];
		}
	} else {
		$data1[$row['hora']] = $row['records'];
		if ($row['records'] > $max_pf) {
			$max_pf = $row['records'];
		}
	}
}
$avg_oag = $max_oag != 0 ? floor(end($data0) / $max_oag * 100) : 0;
$avg_pf = $max_pf != 0 ? floor(end($data1) / $max_pf * 100) : 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <meta http-equiv="refresh" content="30; URL=../userPortals/myPortal.php"/>
		  <title><?php buildTitle("My Portal"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue layout-top-nav">
		  <div class="wrapper">
				<header class="main-header">
					 <?php build_navbar($conn, 0) ?>
				</header> 
		  </div>

		  <div class="content-wrapper">

				<div class="container-fluid">

					 <div class="page-header">
						  <div class='row'>
								<div class='col-xs-10'>
									 <small>
										  <ul style='margin-top: 8px;' class='breadcrumb'>
												<li class="active">Home</li>
										  </ul>
									 </small>
								</div>
								<div class='col-xs-2'>&nbsp;</div>
						  </div>
					 </div>

					 <div class="row">
						  <div class="col-md-8">

								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title">Graph Area</h3>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <div class="chart">
												<canvas id="areaChart" style="height:120px"></canvas>
										  </div>
										  <div class="row row-border">
												<div class="col-md-2">
													 <div class="description-block">
														  <input type="text" class="knob" value="<?php echo $avg_oag; ?>" data-width="90" data-height="90" data-fgColor="rgba(210, 214, 222, 1)" readonly/>
													 </div><!-- /.description-block -->
												</div>
												<div class="col-md-2 border-right">
													 <div class="description-block">
														  <span class="description-header">OAG global</span><br/>
														  <span class="description-text">Max: <?php echo number_format($max_oag, 2); ?></span><br/>
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
										  </div>
									 </div>
								</div>
								<script src="../js/Chart.min.js" type="text/javascript"></script>
								<script src="../js/jquery.knob.js" type="text/javascript"></script>
								<script>
                           $(function () {
                               var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
                               // This will get the first returned node in the jQuery collection.
                               var areaChart = new Chart(areaChartCanvas);

                               var areaChartData = {
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
                                       }
                                   ]
                               };

                               var areaChartOptions = {
                                   //Boolean - If we should show the scale at all
                                   showScale: true,
                                   //Boolean - Whether grid lines are shown across the chart
                                   scaleShowGridLines: false,
                                   //String - Colour of the grid lines
                                   scaleGridLineColor: "rgba(0,0,0,.05)",
                                   //Number - Width of the grid lines
                                   scaleGridLineWidth: 1,
                                   //Boolean - Whether to show horizontal lines (except X axis)
                                   scaleShowHorizontalLines: true,
                                   //Boolean - Whether to show vertical lines (except Y axis)
                                   scaleShowVerticalLines: true,
                                   //Boolean - Whether the line is curved between points
                                   bezierCurve: true,
                                   //Number - Tension of the bezier curve between points
                                   bezierCurveTension: 0.3,
                                   //Boolean - Whether to show a dot for each point
                                   pointDot: false,
                                   //Number - Radius of each point dot in pixels
                                   pointDotRadius: 4,
                                   //Number - Pixel width of point dot stroke
                                   pointDotStrokeWidth: 1,
                                   //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                                   pointHitDetectionRadius: 20,
                                   //Boolean - Whether to show a stroke for datasets
                                   datasetStroke: true,
                                   //Number - Pixel width of dataset stroke
                                   datasetStrokeWidth: 2,
                                   //Boolean - Whether to fill the dataset with a color
                                   datasetFill: false,
                                   //String - A legend template
                                   legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                                   //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                                   maintainAspectRatio: true,
                                   //Boolean - whether to make the chart responsive to window resizing
                                   responsive: true
                               };
                               areaChart.Line(areaChartData, areaChartOptions);

                               $(".knob").knob();
                           });
								</script>

								<div class="box box-success">
									 <div class="box-header with-border">
										  <h3 class="box-title"><span class="badge"><?php echo $rsSupportRequests->num_rows; ?></span>&nbsp;<strong>Requests for my support</strong></h3>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>Date Requested</th>
														  <th>Target Date</th>
														  <th>Subject</th>
														  <th>Customer</th>
														  <th>App</th>
														  <th>Ticket</th>
														  <th>Status</th>
													 </tr>
												</thead>
												<tbody>
													 <?php
													 while ($row_rsSupportRequests = $rsSupportRequests->fetch_assoc()) {
														 ?>
														 <tr>
															  <td><?php echo $row_rsSupportRequests['dateEscalated']; ?></td>
															  <td><?php echo $row_rsSupportRequests['targetDate']; ?></td>
															  <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsSupportRequests['subject']); ?></a></td>
															  <td><?php echo $row_rsSupportRequests['customer']; ?></td>
															  <td><?php echo $row_rsSupportRequests['application']; ?></td>
															  <td><?php echo ($row_rsSupportRequests['ticket'] == "0") ? '-' : $row_rsSupportRequests['ticket']; ?></td>
															  <td><span class="label label-<?php echo $label_colors[$row_rsSupportRequests['status']]; ?>"><?php echo $row_rsSupportRequests['status']; ?></span></td>
														 </tr>
													 <?php } ?>
												</tbody>
										  </table>
									 </div> <!-- /.box-body -->
								</div>

								<div class="box box-info">
									 <div class="box-header with-border">
										  <h4 class="box-title"><span class="badge"><?php echo $rsUnassignedSupportRequests->num_rows; ?></span>&nbsp;<strong>Unassigned support requests</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>Date Requested</th>
														  <th>Target Date</th>
														  <th>Subject</th>
														  <th>Customer</th>
														  <th>App</th>
														  <th>Ticket</th>
														  <th>Status</th>
													 </tr>
												</thead>
												<tbody>
													 <?php
													 while ($row_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->fetch_assoc()) {
														 ?>
														 <tr>
															  <td><?php echo $row_rsUnassignedSupportRequests['dateEscalated']; ?></td>
															  <td><?php echo $row_rsUnassignedSupportRequests['targetDate']; ?></td>
															  <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsUnassignedSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsUnassignedSupportRequests['subject']); ?></a></td>
															  <td><?php echo $row_rsUnassignedSupportRequests['customer']; ?></td>
															  <td><?php echo $row_rsUnassignedSupportRequests['application']; ?></td>
															  <td><?php echo ($row_rsUnassignedSupportRequests['ticket'] == "0") ? '-' : $row_rsUnassignedSupportRequests['ticket']; ?></td>
															  <td><span class="label label-<?php echo $label_colors[$row_rsUnassignedSupportRequests['status']]; ?>"><?php echo $row_rsUnassignedSupportRequests['status']; ?></span></td>
														 </tr>
													 <?php } ?>
												</tbody>
										  </table>
									 </div>
								</div>

						  </div>
						  <div class="col-md-4">

								<div class="box box-danger">
									 <div class="box-header with-border">
										  <h4 class="panel-title"><span class="badge"><?php echo $rsPendingMaintenances->num_rows; ?></span>&nbsp;<strong>Open Maintenances</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">

										  <table class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>Start Date</th>
														  <th>Start Time</th>
														  <th>Reason</th>
													 </tr>
												</thead>
												<tbody>
													 <?php while ($row_rsPendingMaintenances = $rsPendingMaintenances->fetch_assoc()) { ?>
														 <tr>
															  <td><?php echo $row_rsPendingMaintenances['startDate']; ?></td>
															  <td align="right"><?php echo $row_rsPendingMaintenances['startTime']; ?></td>
															  <td><a href="../maintenances/maintenance.php?maintenance=<?php echo $row_rsPendingMaintenances['maintenanceNotifsID']; ?>&amp;function=view"><?php echo $row_rsPendingMaintenances['reason']; ?></a></td>
														 </tr>
													 <?php } ?>
												</tbody>
										  </table>
									 </div>
								</div> <!-- /panel -->
						  </div>
					 </div>

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>

	 </body>
</html>

