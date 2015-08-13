<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'application' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'reporttype' => FILTER_SANITIZE_SPECIAL_CHARS,
	'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	'ticket' => FILTER_SANITIZE_SPECIAL_CHARS,
	'case' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS,
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS
	));

$currentPage = $my_server["PHP_SELF"];

$where = "";
$filter_text = '&nbsp;';
if (isset($my_get['application'])) {
	$where = " AND statusreports.applicationID={$my_get['application']}";
	$result = $conn->query("SELECT application FROM applications WHERE applicationID={$my_get['application']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Application: </em>{$row['application']}\n";
}
if (isset($my_get['customer'])) {
	$where = " AND statusreports.customerID={$my_get['customer']}";
	$result = $conn->query("SELECT customer FROM customers WHERE customerID={$my_get['customer']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Customer: </em>{$row['customer']}\n";
}
if (isset($my_get['engineer'])) {
	$where = " AND statusreports.employeeID={$my_get['engineer']}";
	$result = $conn->query("SELECT displayName FROM employees WHERE employeeID={$my_get['engineer']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Engineer: </em>{$row['displayName']}\n";
}
if (isset($my_get['reporttype'])) {
	$where = " AND statusreports.reportTypeID={$my_get['reporttype']}";
	$result = $conn->query("SELECT reportType FROM reporttypes WHERE reportTypeID={$my_get['reporttype']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Report Type: </em>{$row['reporttype']}\n";
}
if (isset($my_get['subject'])) {
	$where = " AND statusreports.subject like('%{$my_get['subject']}%')";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Subject like: </em>{$my_get['subject']}\n";
}
if (isset($my_get['ticket'])) {
	$where = " AND statusreports.magicTicket={$my_get['ticket']}";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Ticket=</em>{$my_get['ticket']}\n";
}
if (isset($my_get['case'])) {
	$where = " AND statusreports.wrm={$my_get['case']}";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Case#</em>{$my_get['case']}\n";
}

$query_rsStatusReports = "SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application"
	. ", customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType"
	. ", DATE_FORMAT(endDate, '%m/%d/%Y') as endDate"
	. " FROM applications, customers, statusreports, employees, reporttypes"
	. " WHERE statusreports.applicationID=applications.applicationID"
	. "  AND statusreports.customerID=customers.customerID"
	. "  AND statusreports.reportTypeID=reporttypes.reportTypeID"
	. "  AND statusreports.employeeID=employees.employeeID"
	. " %%where%%"
	. " ORDER BY statusreports.endDate DESC, statusreports.endTime DESC";

$rsStatusReports = $conn->query(str_replace('%%where%%', $where, $query_rsStatusReports)) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Status Reports"); ?></title>
		  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(3); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'statusReports.php', 'text' => 'Status Reports']], 'Status Reports', $filter_text) ?>

					 <section class="content">

						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <a class='btn btn-primary' href='statusReport.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add Status Report</a>
									 <div class="box-tools pull-right">
										  <div id="div_flt_nofilter">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li class="active"><a href="#">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="form-control">No Filter</label>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_nofilter -->
										  <div id="div_flt_application" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li class="active"><a href="#">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Application:&nbsp;</label>
														  <select id="input_div_flt_application" name="application" class="form-control">
																<?php
																$result = $conn->query("SELECT applicationID, application FROM applications ORDER BY application ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['applicationID']}'" . ($my_get['application'] == $row['applicationID'] ? " selected='selected'" : '') . ">{$row['application']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_application -->
										  <div id="div_flt_customer" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li class="active"><a href="#">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Customer:&nbsp;</label>
														  <select id="input_div_flt_customer" name="customer" class="form-control">
																<?php
																$result = $conn->query("SELECT customerID, customer FROM customers ORDER BY customer ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['customerID']}'" . ($my_get['customer'] == $row['customerID'] ? " selected='selected'" : '') . ">{$row['customer']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_customer -->
										  <div id="div_flt_engineer" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li class="active"><a href="#">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Engineer:&nbsp;</label>
														  <select id="input_div_flt_engineer" name="engineer" class="form-control">
																<?php
																$result = $conn->query("SELECT employeeID, displayName FROM employees ORDER BY displayName ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['employeeID']}'" . ($my_get['engineer'] == $row['employeeID'] ? " selected='selected'" : '') . ">{$row['displayName']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_engineer -->
										  <div id="div_flt_reporttype" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li class="active"><a href="#">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Report Type:&nbsp;</label>
														  <select id="input_div_flt_reporttype" name="reporttype" class="form-control">
																<?php
																$result = $conn->query("SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['reportTypeID']}'" . ($my_get['reporttype'] == $row['reportTypeID'] ? " selected='selected'" : '') . ">{$row['reportType']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_reporttype -->
										  <div id="div_flt_subject" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li class="active"><a href="#">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Subject:&nbsp;</label>
														  <input id="input_div_flt_subject" name="subject" value="<?php echo $my_get['subject']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_subject -->
										  <div id="div_flt_ticket" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li class="active"><a href="#">Ticket</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Ticket:&nbsp;</label>
														  <input id="input_div_flt_ticket" name="ticket" value="<?php echo $my_get['ticket']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_ticket -->
										  <div id="div_flt_case" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_customer')">Customer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_reporttype')">Report Type</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_case')">Ticket</a></li>
																	 <li class="active"><a href="#">Case</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Case:&nbsp;</label>
														  <input id="input_div_flt_case" name="case" value="<?php echo $my_get['case']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_case -->
									 </div>
								</div>
								<div class='box-body'>
									 <table id='status_reports_table' class="table table-bordered table-striped">
										  <thead>
												<tr>
													 <th>Date</th>
													 <th>ID</th>
													 <th>Subject</th>
													 <?php
													 if (!isset($my_get['application'])) {
														 echo "<th>App</th>\n";
													 }
													 if (!isset($my_get['customer'])) {
														 echo "<th>Customer</th>\n";
													 }
													 if (!isset($my_get['employee'])) {
														 echo "<th>Engineer</th>\n";
													 }
													 if (!isset($my_get['reporttype'])) {
														 echo "<th>Report Type</th>\n";
													 }
													 if (!isset($my_get['ticket'])) {
														 echo "<th>Ticket</th>\n";
													 }
													 if (!isset($my_get['case'])) {
														 echo "<th>Case</th>\n";
													 }
													 sudoAuthData(null, null, "th", null, null);
													 ?>
												</tr>
										  </thead>
										  <tbody>
												<?php
												while ($row_rsStatusReports = $rsStatusReports->fetch_assoc()) {
													?>
													<tr>
														 <td><?php echo $row_rsStatusReports['endDate']; ?></td>
														 <td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php
															  if (isset($my_get['corp'])) {
																  echo "&amp;corp=y";
															  }
															  ?>"><?php echo $row_rsStatusReports['statusReportID']; ?></a></td>
														 <td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php
															  if (isset($my_get['corp'])) {
																  echo "&amp;corp=y";
															  }
															  ?>"><?php echo stripslashes($row_rsStatusReports['subject']); ?></a></td>
															  <?php
															  if (!isset($my_get['application'])) {
																  echo "<td>{$row_rsStatusReports['application']}</td>\n";
															  }
															  if (!isset($my_get['customer'])) {
																  echo "<td>{$row_rsStatusReports['customer']}</td>\n";
															  }
															  if (!isset($my_get['employee'])) {
																  echo "<td>{$row_rsStatusReports['displayName']}</td>\n";
															  }
															  if (!isset($my_get['reporttype'])) {
																  echo "<td>{$row_rsStatusReports['reportType']}</td>\n";
															  }
															  if (!isset($my_get['ticket'])) {
																  echo "<td>" . ($row_rsStatusReports['magicTicket'] == "0" ? '-' : $row_rsStatusReports['magicTicket']) . "</td>\n";
															  }
															  if (!isset($my_get['case'])) {
																  echo "<td>" . ($row_rsStatusReports['wrm'] == "0" ? '-' : $row_rsStatusReports['wrm']) . "</td>\n";
															  }
															  // sudoAuthData("statusReportUpdate", "Update Status Report", "td", "edit", "statusReport=" . $row_rsStatusReports['statusReportID']);
															  ?>
														 <td>&nbsp;</td>
													</tr>
												<?php } ?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#status_reports_table').dataTable({"order": [[1, 'desc'], [3, 'asc']], "pageLength": 25});
                               });
                               function display_filter(filter) {
                                   $("#div_flt_nofilter").hide();
                                   $("#div_flt_application").hide();
                                   $("#div_flt_customer").hide();
                                   $("#div_flt_engineer").hide();
                                   $("#div_flt_reporttype").hide();
                                   $("#div_flt_subject").hide();
                                   $("#div_flt_ticket").hide();
                                   $("#div_flt_case").hide();
                                   $("#" + filter).show();
                                   $("#input_" + filter).focus();
                               }
<?php echo isset($my_get['application']) ? "display_filter('div_flt_application');\n" : ''; ?>
<?php echo isset($my_get['customer']) ? "display_filter('div_flt_customer');\n" : ''; ?>
<?php echo isset($my_get['engineer']) ? "display_filter('div_flt_engineer');\n" : ''; ?>
<?php echo isset($my_get['reporttype']) ? "display_filter('div_flt_reporttype');\n" : ''; ?>
<?php echo isset($my_get['subject']) ? "display_filter('div_flt_subject');\n" : ''; ?>
<?php echo isset($my_get['ticket']) ? "display_filter('div_flt_ticket');\n" : ''; ?>
<?php echo isset($my_get['case']) ? "display_filter('div_flt_case');\n" : ''; ?>
									 </script>
								</div><!-- /.box-body -->
						  </div><!-- /.box -->

					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
		  </div> <!-- /content-wrapper -->

	 </body>
</html>