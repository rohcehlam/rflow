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
/*
$varEmployee = (isset($_SESSION['employee']) ? addslashes($_SESSION['employee']) : "1");

$query_rsEmployeeInfo = "SELECT employeeID, firstName, displayName FROM employees WHERE employeeID = $varEmployee";
$rsEmployeeInfo = $conn->query($query_rsEmployeeInfo) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsEmployeeInfo = $rsEmployeeInfo->fetch_assoc();
$totalRows_rsEmployeeInfo = $rsEmployeeInfo->num_rows;
*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <!--
		  <meta http-equiv="refresh" content="30; URL=../userPortals/myPortal.php"/>
		  -->
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
										  <h3 class="box-title"><span id="mysupport_requests_count" class="badge">0</span>&nbsp;<strong>Requests for my support</strong></h3>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" onclick="refresh_supportrequest();"><span class="glyphicon glyphicon-refresh"></span></button>
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table id="mysupport_requests" class="table table-striped table-condensed">
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
												</tbody>
										  </table>
										  <div id="mysupport_requests_nodata" style="display: none;">There are no <em>Requests for my support</em> at the moment</div>
										  <div id="mysupport_requests_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
									 </div> <!-- /.box-body -->
								</div>

								<div class="box box-info">
									 <div class="box-header with-border">
										  <h4 class="box-title"><span id="myunassigned_support_requests_count" class="badge">0</span>&nbsp;<strong>Unassigned support requests</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" onclick="refresh_unassigned_supportrequest();"><span class="glyphicon glyphicon-refresh"></span></button>
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table id="myunassigned_support_requests" class="table table-striped table-condensed">
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
												</tbody>
										  </table>
										  <div id="myunassigned_support_requests_nodata" style="display: none;">There are no <em>unassigned support requests</em> at the moment</div>
										  <div id="myunassigned_support_requests_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
									 </div>
								</div>

						  </section>
						  <section class="col-lg-4 connectedSortable">

								<div class="box box-danger">
									 <div class="box-header with-border">
										  <h4 class="panel-title"><span id="mypending_maintenances_count" class="badge">0</span>&nbsp;<strong>Open Maintenances</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" onclick="refresh_pending_maintenances();"><span class="glyphicon glyphicon-refresh"></span></button>
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table id="mypending_maintenances" class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>Start Date</th>
														  <th>Start Time</th>
														  <th>Reason</th>
													 </tr>
												</thead>
												<tbody>
												</tbody>
										  </table>
										  <div id="mypending_maintenances_nodata" style="display: none;">There are no <em>Open Maintenances</em> at the moment</div>
										  <div id="mypending_maintenances_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
									 </div>
								</div> <!-- /.box-danger -->

								<div class="box box-danger">
									 <div class="box-header with-border">
										  <h4 class="panel-title"><span id="active_alarms_count" class="badge">0</span>&nbsp;<strong>Active Alarms</strong></h4>
										  <div class="pull-right box-tools">
												<button class="btn btn-sm" onclick="refresh_active_alarms();"><span class="glyphicon glyphicon-refresh"></span></button>
												<button class="btn btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
										  </div>
									 </div>
									 <div class="box-body">
										  <table id="active_alarms" class="table table-striped table-condensed">
												<thead>
													 <tr>
														  <th>DataBase</th>
														  <th>Table</th>
														  <th>Last Check</th>
													 </tr>
												</thead>
												<tbody>
												</tbody>
										  </table>
										  <div id="active_alarms_nodata" style="display: none;">There are no <em>Active Alarms</em> at the moment</div>
										  <div id="active_alarms_load" style="display: none;" class="overlay"><i class="fa fa-refresh fa-spin"></i></div>
									 </div>
								</div> <!-- /.box-danger -->

						  </section>
						  <script>
							  var to_refresh_graph;
							  var to_support_request;
							  var to_unassigned_support_request;
							  var to_pending_maintenances;
							  var to_active_alarms;
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
                                   $('#graph_data').html(data);
                                   $('#graph_data_load').hide();
											  clearTimeout(to_refresh_graph);
											  to_refresh_graph = setTimeout(refresh_graph, 30000);
                               }
                           });
                       }
                       function refresh_supportrequest() {
                           $('#mysupport_requests_load').show();
                           $.getJSON('../ajax/supportRequest.php', function (data) {
                               $("#mysupport_requests_count").html(data.records);
										 if (data.records === 0) {
											 $("#mysupport_requests").hide();
											 $("#mysupport_requests_nodata").show();
										 } else {
											 $("#mysupport_requests").show();
											 $("#mysupport_requests_nodata").hide();
										 }
                               var c = [];
                               $.each(data.list, function (i, item) {
                                   c.push("<tr><td>" + item.dateEscalated + "</td>");
                                   c.push("<td>" + item.targetDate + "</td>");
                                   c.push("<td>" + item.subject + "</td>");
                                   c.push("<td>" + item.customer + "</td>");
                                   c.push("<td>" + item.application + "</td>");
                                   c.push("<td>" + item.ticket + "</td>");
                                   c.push("<td>" + item.status + "</td></tr>");
                               });
                               $('#mysupport_requests tbody').html(c.join(""));
                               $('#mysupport_requests_load').hide();
										 clearTimeout(to_support_request);
										 to_support_request = setTimeout(refresh_supportrequest, 30000);
                           });
                       }
                       function refresh_unassigned_supportrequest() {
                           $('#myunassigned_support_requests_load').show();
                           $.getJSON('../ajax/unassigned_supportRequest.php', function (data) {
                               $("#myunassigned_support_requests_count").html(data.records);
										 if (data.records === 0) {
											 $("#myunassigned_support_requests").hide();
											 $("#myunassigned_support_requests_nodata").show();
										 } else {
											 $("#myunassigned_support_requests").show();
											 $("#myunassigned_support_requests_nodata").hide();
										 }
                               var c = [];
                               $.each(data.list, function (i, item) {
                                   c.push("<tr><td>" + item.dateEscalated + "</td>");
                                   c.push("<td>" + item.targetDate + "</td>");
                                   c.push("<td>" + item.subject + "</td>");
                                   c.push("<td>" + item.customer + "</td>");
                                   c.push("<td>" + item.application + "</td>");
                                   c.push("<td>" + item.ticket + "</td>");
                                   c.push("<td>" + item.status + "</td></tr>");
                               });
                               $('#myunassigned_support_requests tbody').html(c.join(""));
                               $('#myunassigned_support_requests_load').hide();
										 clearTimeout(to_unassigned_support_request);
										 to_unassigned_support_request = setTimeout(refresh_unassigned_supportrequest, 30000);
                           });
                       }
                       function refresh_pending_maintenances() {
                           $('#mypending_maintenances_load').show();
                           $.getJSON('../ajax/pending_maintenances.php', function (data) {
                               $("#mypending_maintenances_count").html(data.records);
										 if (data.records === 0) {
											 $("#mypending_maintenances").hide();
											 $("#mypending_maintenances_nodata").show();
										 } else {
											 $("#mypending_maintenances").show();
											 $("#mypending_maintenances_nodata").hide();
										 }
                               var c = [];
                               $.each(data.list, function (i, item) {
                                   c.push("<tr><td>" + item.startDate + "</td>");
                                   c.push("<td>" + item.startTime + "</td>");
                                   c.push("<td>" + item.reason + "</td></tr>");
                               });
                               $('#mypending_maintenances tbody').html(c.join(""));
                               $('#mypending_maintenances_load').hide();
										 clearTimeout(to_pending_maintenances);
										 to_pending_maintenances = setTimeout(refresh_pending_maintenances, 30000);
                           });
                       }
                       function refresh_active_alarms() {
                           $('#active_alarms_load').show();
                           $.getJSON('../ajax/active_alarms.php', function (data) {
                               $("#active_alarms_count").html(data.records);
										 if (data.records === 0) {
											 $("#active_alarms").hide();
											 $("#active_alarms_nodata").show();
										 } else {
											 $("#active_alarms").show();
											 $("#active_alarms_nodata").hide();
										 }
                               var c = [];
                               $.each(data.list, function (i, item) {
                                   c.push("<tr><td>" + item.database + "</td>");
                                   c.push("<td>" + item.table + "</td>");
                                   c.push("<td>" + item.lastcheck + "</td></tr>");
                               });
                               $('#active_alarms tbody').html(c.join(""));
                               $('#active_alarms_load').hide();
										 clearTimeout(to_active_alarms);
										 to_active_alarms = setTimeout(refresh_active_alarms, 30000);
                           });
                       }
                       refresh_graph();
                       refresh_supportrequest();
                       refresh_unassigned_supportrequest();
                       refresh_pending_maintenances();
							  refresh_active_alarms();
						  </script>
						  <script src="../js/Chart.min.js" type="text/javascript"></script>
						  <script src="../js/jquery.knob.js" type="text/javascript"></script>
					 </div> <!-- /.row -->

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>

	 </body>
</html>

