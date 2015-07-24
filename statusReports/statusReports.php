<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'pageNum_rsStatusReports' => FILTER_SANITIZE_SPECIAL_CHARS,
	'app' => FILTER_SANITIZE_SPECIAL_CHARS,
	'customer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
	'reporttype' => FILTER_SANITIZE_SPECIAL_CHARS,
	'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	'ticket' => FILTER_SANITIZE_SPECIAL_CHARS,
	'wrm' => FILTER_SANITIZE_SPECIAL_CHARS,
	'sortBy' => FILTER_SANITIZE_SPECIAL_CHARS,
	'sortOrder' => FILTER_SANITIZE_SPECIAL_CHARS,
	'totalRows_rsStatusReports' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS,
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS
	));

$currentPage = $my_server["PHP_SELF"];

$maxRows_rsStatusReports = 45;
$pageNum_rsStatusReports = 0;
if (isset($my_get['pageNum_rsStatusReports'])) {
	$pageNum_rsStatusReports = $my_get['pageNum_rsStatusReports'];
}
$startRow_rsStatusReports = $pageNum_rsStatusReports * $maxRows_rsStatusReports;

$query_rsStatusReports = "SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application"
	. ", customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType"
	. ", DATE_FORMAT(endDate, '%m/%d/%Y') as endDate"
	. " FROM applications, customers, statusreports, employees, reporttypes"
	. " WHERE statusreports.applicationID=applications.applicationID"
	. "  AND statusreports.customerID=customers.customerID"
	. "  AND statusreports.reportTypeID=reporttypes.reportTypeID"
	. "  AND statusreports.employeeID=employees.employeeID"
	. " ORDER BY statusreports.endDate DESC, statusreports.endTime DESC";

$query_limit_rsStatusReports = sprintf("%s LIMIT %d, %d", $query_rsStatusReports, $startRow_rsStatusReports, $maxRows_rsStatusReports);
$rsStatusReports = $conn->query($query_limit_rsStatusReports);
$row_rsStatusReports = $rsStatusReports->fetch_assoc();

if (isset($my_get['totalRows_rsStatusReports'])) {
	$totalRows_rsStatusReports = $my_get['totalRows_rsStatusReports'];
} else {
	$all_rsStatusReports = $conn->query($query_rsStatusReports);
	$totalRows_rsStatusReports = $all_rsStatusReports->num_rows;
}
$totalPages_rsStatusReports = ceil($totalRows_rsStatusReports / $maxRows_rsStatusReports) - 1;

//$queryString_rsStatusReports = "";
if (!empty($my_server['QUERY_STRING'])) {
	$params = explode("&", $my_server['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_rsStatusReports") == false &&
			stristr($param, "totalRows_rsStatusReports") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_rsStatusReports = "&amp;" . htmlentities(implode("&", $newParams));
	}
}
$queryString_rsStatusReports = sprintf("&amp;totalRows_rsStatusReports=%d%s", $totalRows_rsStatusReports, $queryString_rsStatusReports);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Status Reports"); ?></title>
		  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue layout-top-nav">

		  <div class="wrapper">
				<header class="main-header">
					 <?php build_navbar($conn, 2); ?>
				</header> 
		  </div>

		  <div class="content-wrapper">

				<div class="container-fluid">

					 <?php
					 buildNewHeader('statusReports.php', 'Status Reports', '', 'statusReport.php', 'Add a Status Report');
					 ?>

					 <div class="row">

						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <h3 class="box-title">Status Reports</h3>
								</div>
								<div class='box-body'>
									 <table id='status_reports_table' class="table table-bordered table-striped">
										  <thead>
												<tr>
													 <th>Date</th>
													 <th>ID</th>
													 <th>Subject</th>
													 <?php
													 if (!isset($my_get['app'])) {
														 echo "          <th>App</th>\n";
													 }
													 if (!isset($my_get['customer'])) {
														 echo "          <th>Customer</th>\n";
													 }
													 if (!isset($my_get['employee'])) {
														 echo "          <th>Engineer</th>\n";
													 }
													 if (!isset($my_get['reporttype'])) {
														 echo "          <th>Report Type</th>\n";
													 }
													 if (!isset($my_get['ticket'])) {
														 echo "          <th>Ticket</th>\n";
													 }
													 if (!isset($my_get['wrm'])) {
														 echo "          <th>Case</th>\n";
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
//App column
															  if (!isset($my_get['app'])) {
																  echo "          <td>" . $row_rsStatusReports['application'] . "</td>\n";
															  }
//Carrier column
															  if (!isset($my_get['customer'])) {
																  echo "          <td>" . $row_rsStatusReports['customer'] . "</td>\n";
															  }
//Employee column
															  if (!isset($my_get['employee'])) {
																  echo "          <td>" . $row_rsStatusReports['displayName'] . "</td>\n";
															  }
//Report Type column
															  if (!isset($my_get['reporttype'])) {
																  echo "          <td>" . $row_rsStatusReports['reportType'] . "</td>\n";
															  }
//Ticket column
															  if (!isset($my_get['ticket'])) {
																  echo "          <td>";
																  if ($row_rsStatusReports['magicTicket'] == "0") {
																	  echo "-";
																  } else {
																	  echo $row_rsStatusReports['magicTicket'];
																  }
																  echo "</td>\n";
															  }
//WRM column
															  if (!isset($my_get['wrm'])) {
																  echo "          <td>";
																  if ($row_rsStatusReports['wrm'] == "0") {
																	  echo "-";
																  } else {
																	  echo $row_rsStatusReports['wrm'];
																  }
																  echo "</td>\n";
															  }

															  sudoAuthData("statusReportUpdate", "Update Status Report", "td", "edit", "statusReport=" . $row_rsStatusReports['statusReportID']);
															  ?>
													</tr>
												<?php } ?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#status_reports_table').dataTable({"order": [[1, 'desc'], [3, 'asc']], "pageLength": 25});
                               });
									 </script>
								</div><!-- /.box-body -->
						  </div><!-- /.box -->

					 </div> <!-- /row -->

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>
	 </body>
</html>