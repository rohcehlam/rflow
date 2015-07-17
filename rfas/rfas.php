<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

$currentPage = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
session_start();

$maxRows_rsChangeRequests = 25;
$pageNum_rsChangeRequests = filter_input(INPUT_GET, 'pageNum_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);

$startRow_rsChangeRequests = $pageNum_rsChangeRequests * $maxRows_rsChangeRequests;

$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted"
		  .", changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID"
		  .", changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate"
		  ." FROM changerequests"
		  ." LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID"
		  ." LEFT JOIN applications ON changerequests.applicationID=applications.applicationID"
		  ." WHERE changerequests.status='Pending Approval' OR changerequests.status='Submitted for CAB Approval' OR changerequests.status='Returned'"
		  ." ORDER BY windowStartDate DESC, windowStartTime DESC";

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
				<?php build_navbar($conn, 1) ?>
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
								<th>Date Submitted</th>
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

