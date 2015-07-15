<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

$currentPage = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
session_start();

$maxRows_rsChangeRequests = 25;
$pageNum_rsChangeRequests = filter_input(INPUT_GET, 'pageNum_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);

$startRow_rsChangeRequests = $pageNum_rsChangeRequests * $maxRows_rsChangeRequests;

$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID WHERE changerequests.status='Pending Approval' or changerequests.status='Submitted for CAB Approval' or changerequests.status='Returned' ORDER BY windowStartDate DESC, windowStartTime DESC";

$varEmployee_rsMaintenanceNotifs = filter_input(INPUT_GET, 'engineer', FILTER_SANITIZE_SPECIAL_CHARS);
$varStatus_rsMaintenanceNotifs = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
$varApp_rsMaintenanceNotifs = filter_input(INPUT_GET, 'app', FILTER_SANITIZE_SPECIAL_CHARS);
$varsummary_rsMaintenanceNotifs = filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_SPECIAL_CHARS);
$varrequestOrigin_rsMaintenanceNotifs = filter_input(INPUT_GET, 'requestOrigin', FILTER_SANITIZE_SPECIAL_CHARS);
$varsubapp_rsMaintenanceNotifs = filter_input(INPUT_GET, 'subapp', FILTER_SANITIZE_SPECIAL_CHARS);
$varorigin_rsMaintenanceNotifs = filter_input(INPUT_GET, 'origin', FILTER_SANITIZE_SPECIAL_CHARS);
$varticket_rsMaintenanceNotifs = filter_input(INPUT_GET, 'ticket', FILTER_SANITIZE_SPECIAL_CHARS);
$varEEmployee_rsMaintenanceNotifs = filter_input(INPUT_GET, 'employee', FILTER_SANITIZE_SPECIAL_CHARS);

$args = array(
	 'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'app' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'summary' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'requestOrigin' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'subapp' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'origin' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'tricket' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
//var_dump($my_get);

if (filter_input(INPUT_GET, 'app', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE changerequests.applicationID='$varApp_rsMaintenanceNotifs' 
    ORDER BY windowStartDate DESC, windowStartTime DESC
    ";
} elseif (filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE summary LIKE '%$varsummary_rsMaintenanceNotifs%'
    ORDER BY windowStartDate DESC, windowStartTime DESC ";
} elseif (filter_input(INPUT_GET, 'engineer', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE 
    employeeID=employees.employeeID
    AND changerequests.submittedBy='$varEmployee_rsMaintenanceNotifs' 
    ORDER BY windowStartDate DESC, windowStartTime DESC ";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) == "All") {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID ORDER BY windowStartDate DESC, windowStartTime DESC";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) != "All") {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate 
        FROM changerequests 
        LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
        LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
        WHERE changerequests.status='$varStatus_rsMaintenanceNotifs'  or changerequests.status='Returned' 
        ORDER BY windowStartDate DESC, windowStartTime DESC";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID WHERE changerequests.status='Pending Approval' or changerequests.status='Returned' ORDER BY windowStartDate DESC, windowStartTime DESC";
}

$query_limit_rsChangeRequests = sprintf("%s LIMIT %d, %d", $query_rsChangeRequests, $startRow_rsChangeRequests, $maxRows_rsChangeRequests);
$rsChangeRequests = $conn->query($query_limit_rsChangeRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsChangeRequests = $rsChangeRequests->fetch_assoc();

$totalRows_rsChangeRequests = filter_input(INPUT_GET, 'totalRows_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$totalRows_rsChangeRequests) {
	$all_rsChangeRequests = $conn->query($query_rsChangeRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$totalRows_rsChangeRequests = $all_rsChangeRequests->num_rows;
}
$totalPages_rsChangeRequests = ceil($totalRows_rsChangeRequests / $maxRows_rsChangeRequests) - 1;

$queryString_rsChangeRequests = "";
$temp = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS);
if (!empty($temp)) {
	$params = explode("&", $temp);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_rsChangeRequests") == false &&
				  stristr($param, "totalRows_rsChangeRequests") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_rsChangeRequests = "&" . htmlentities(implode("&", $newParams));
	}
}
$queryString_rsChangeRequests = sprintf("&totalRows_rsChangeRequests=%d%s", $totalRows_rsChangeRequests, $queryString_rsChangeRequests);

//select applications for application filter list
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = $conn->query($query_rsApps) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsApps = $rsApps->fetch_assoc();
$totalRows_rsApps = $rsApps->num_rows;

//select employees for employee filter list
$query_rsEngineers = "SELECT employeeID, displayName FROM employees WHERE employees.engineer ='y' ORDER BY displayName ASC";
$rsEngineers = $conn->query($query_rsEngineers);
$row_rsEngineers = $rsEngineers->fetch_assoc();
$totalRows_rsEngineers = $rsEngineers->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Change Requests"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php build_header(); ?>
	</head>
	<body class="skin-blue layout-top-nav">

		<div class="wrapper">
			<header class="main-header">
				<?php build_navbar(1, !isset($_SESSION['employee']) ? "<li>\n<a href=\"index.php\"><span class='glyphicon glyphicon-log-in'></span>&nbsp;Login</a>\n</li>\n" : "<li><a href='#'>Welcome, {$row_rsEmployeeInfo['firstName']}!</a></li>\n<li><a href=\"$logoutAction\"><span class='glyphicon glyphicon-log-out'></span>&nbsp;Logout</a></li>\n") ?>
			</header> 
		</div>

		<div class="content-wrapper">

			<div class="container-fluid">

				<?php
				buildNewHeader('rfas.php', 'RFCs', '', 'rfa.php', 'Add an RFC');
				?>

				<div class="row">
					<table id='rfas_table' class="showMySettings table table-bordered table-striped">
						<thead>
							<tr>
								<th>Date<br />Submitted</th>
								<th>Submitted By</th>
								<th>Summary</th>
								<th>App</th>
								<th>Status</th>
								<th>Window</th>
								<?php sudoAuthData(null, null, "th", "edit", null); ?>
							</tr>
						</thead>
						<tbody>
							<?php
							while ($row_rsChangeRequests = $rsChangeRequests->fetch_assoc()) {
								?>
								<tr>
									<td><?php echo $row_rsChangeRequests['dateSubmitted']; ?></td>
									<td><?php echo $row_rsChangeRequests['displayName']; ?></td>
									<td><?php echo "<a href=\"rfa.php?function=view&amp;rfa=" . $row_rsChangeRequests['changeRequestID'] . "\">" . $row_rsChangeRequests['summary'] . "</a>"; ?></td>
									<td><?php echo $row_rsChangeRequests['application']; ?></td>
									<td><?php echo $row_rsChangeRequests['status']; ?></td>
									<td><?php echo $row_rsChangeRequests['windowStartDate']; ?></td>
									<?php sudoAuthData("rfa.php", "Update RFA", "td", "edit", "function=update&amp;rfa=" . $row_rsChangeRequests['changeRequestID']); ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<script type="text/javascript">
						$(document).ready(function () {
							$('#rfas_table').dataTable();
						});
					</script>

				</div> <!-- /row -->

			</div> <!-- /container -->
		</div> <!-- /content-wrapper -->

		<?php build_footer(); ?>
	</body>
</html>

