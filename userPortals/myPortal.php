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
/*
  //my projects
  $query_rsMyProjects = "SELECT projectID, projectName, status, applications.application, customers.customer, organizingEngineerID"
  . ", DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate, wrm, ticket"
  . " FROM projects"
  . " LEFT JOIN applications ON projects.applicationID=applications.applicationID"
  . " LEFT JOIN customers ON projects.primaryCustomerID=customers.customerID"
  . " WHERE organizingEngineerID = $varEmployee AND status <> 'Completed'"
  . " ORDER BY targetDate ASC";
  $rsMyProjects = $conn->query($query_rsMyProjects) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
  $totalRows_rsMyProjects = $rsMyProjects->num_rows;
 */
//my support requests
$query_rsSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket"
	. ", customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
	. " LEFT JOIN customers ON escalations.customerID=customers.customerID"
	. " WHERE assignedTo = $varEmployee AND status <> 'Closed' AND status <> 'Returned'"
	. " ORDER BY targetDate ASC";
$rsSupportRequests = $conn->query($query_rsSupportRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

//pending maintenances
$query_rsPendingMaintenances = "SELECT maintenanceNotifsID, reason, TIME_FORMAT(startTime,'%k:%i') AS startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate"
	. " FROM maintenancenotifs"
	. " WHERE status = 'Open' OR status='Extended'"
	. " ORDER BY startDate DESC, startTime DESC";
$rsPendingMaintenances = $conn->query($query_rsPendingMaintenances) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

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
/*
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
 */
$conn_skybot = new mysqli('50.19.240.104', 'serviceuser', 'H!ghZ3cret', 'masflightdb');
$rsAlarms = $conn_skybot->query("SELECT comment FROM alarms WHERE active=TRUE");

$labels = array();
$data0 = array();
$data1 = array();
$conn0 = new mysqli("23.23.213.234", "mfroot", "Mfroo7", "masflightdb");
$result = $conn0->query("SELECT PROCESS, LEFT(datetime_event, 15) AS hora, AVG(processed_rec) as records"
	. " FROM logmas_" . date('Ym')
	. " WHERE (PROCESS='oag_global' OR PROCESS='pinkfroot_demo')"
	. "  AND type_proc='ES'"
	. "  AND (datetime_event BETWEEN DATE_SUB(NOW(), INTERVAL 3 HOUR) AND NOW())"
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

$result = $conn_skybot->query("SELECT LEFT(TIMESTAMP, 15) AS hora, AVG(ROWS) AS total_rows FROM table_status_plus WHERE NAME ='air_nav_" . date('Ym') . "' AND SERVER=41 GROUP BY hora ORDER BY id ASC;");
$data2 = array();
$prev = -1;
$max_an = 0;
while ($row = $result->fetch_assoc()) {
	if ($prev == -1) {
		$prev = $row['total_rows'];
	} else {
		$data2[$row['hora']] = $row['total_rows'] - $prev;
		$prev = $row['total_rows'];
		if ($data2[$row['hora']] > $max_an) {
			$max_an = $data2[$row['hora']];
		}
	}
}
$avg_an = $max_an != 0 ? floor(end($data2) / $max_an * 100) : 0;
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
						  <section class="col-lg-8 connectedSortable">

								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title">Graph Area</h3>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" onclick="refresh_graph();"><span class="glyphicon glyphicon-refresh"></span></button>
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <div id="graph_data"></div>
										  <div id="graph_data_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
									 </div> <!-- /.box-body -->
								</div>


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

						  </section>
						  <section class="col-lg-4 connectedSortable">

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
								</div> <!-- /.box-danger -->

								<div class="box box-danger">
									 <div class="box-header with-border">
										  <h4 class="panel-title"><span class="badge"><?php echo $rsAlarms->num_rows; ?></span>&nbsp;<strong>Active Alarms</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>DataBase</th>
														  <th>Table</th>
														  <th>Last Check</th>
													 </tr>
												</thead>
												<tbody>
													 <?php
													 while ($row = $rsAlarms->fetch_assoc()) {
														 $temp = explode('strong', preg_replace('/<|\/|>/', '', $row['comment']));
														 echo "<tr>\n";
														 echo "<td>{$temp[3]}</td>\n";
														 echo "<td>{$temp[1]}</td>\n";
														 echo "<td>{$temp[5]}</td>\n";
														 echo "</tr>\n";
													 }
													 ?>
												</tbody>
										  </table>
									 </div>
								</div> <!-- /.box-danger -->

						  </section>
						  <script>
                       $(function () {
                           //Make the dashboard widgets sortable Using jquery UI
                           $(".connectedSortable").sortable({
                               placeholder: "sort-highlight",
                               connectWith: ".connectedSortable",
                               handle: ".box-header, .nav-tabs",
                               forcePlaceholderSize: true,
                               zIndex: 999999
                           });
                           $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");
                       });
                       function refresh_graph() {
                           $('#graph_data_load').show();
                           $.ajax({
                               url: '../ajax/dashboard_graph.php',
                               success: function (data) {
                                   //$('#graph_data').show();
                                   $('#graph_data').html(data);
                                   $('#graph_data_load').hide();
                               }
                           });
                       }
                       refresh_graph();
						  </script>
						  <script src="../js/Chart.min.js" type="text/javascript"></script>
						  <script src="../js/jquery.knob.js" type="text/javascript"></script>
					 </div> <!-- /.row -->

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>

	 </body>
</html>

