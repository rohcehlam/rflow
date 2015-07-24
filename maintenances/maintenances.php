<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'pageNum_rsMaintenanceNotifs' => FILTER_SANITIZE_SPECIAL_CHARS,
	'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
	'status' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS
	), true);

$currentPage = $my_server["PHP_SELF"];

$maxRows_rsMaintenanceNotifs = 25;
$pageNum_rsMaintenanceNotifs = (isset($my_get['pageNum_rsMaintenanceNotifs']) ? $my_get['pageNum_rsMaintenanceNotifs'] : 0);
$startRow_rsMaintenanceNotifs = $pageNum_rsMaintenanceNotifs * $maxRows_rsMaintenanceNotifs;

$varEmployee_rsMaintenanceNotifs = (isset($my_get['employee']) ? addslashes($my_get['employee']) : "1");
$varStatus_rsMaintenanceNotifs = (isset($my_get['status']) ? addslashes($my_get['status']) : "1");


$query_rsMaintenanceNotifs = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort"
	. ", startDate AS startDateSort, TIME_FORMAT(startTime, '%H:%i') as startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName"
	. ", maintenancenotifs.status"
	. " FROM maintenancenotifs, employees"
	. " WHERE maintenancenotifs.employeeID=employees.employeeID AND (maintenancenotifs.status='Open' OR maintenancenotifs.status='Extended')"
	. " ORDER BY startDateSort DESC, startTimeSort DESC";

$query_limit_rsMaintenanceNotifs = sprintf("%s LIMIT %d, %d", $query_rsMaintenanceNotifs, $startRow_rsMaintenanceNotifs, $maxRows_rsMaintenanceNotifs);
$rsMaintenanceNotifs = $conn->query($query_limit_rsMaintenanceNotifs) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsMaintenanceNotifs = $rsMaintenanceNotifs->fetch_assoc();

if (isset($my_get['totalRows_rsMaintenanceNotifs'])) {
	$totalRows_rsMaintenanceNotifs = $my_get['totalRows_rsMaintenanceNotifs'];
} else {
	$all_rsMaintenanceNotifs = $conn->query($query_rsMaintenanceNotifs) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$totalRows_rsMaintenanceNotifs = $all_rsMaintenanceNotifs->num_rows;
}
$totalPages_rsMaintenanceNotifs = ceil($totalRows_rsMaintenanceNotifs / $maxRows_rsMaintenanceNotifs) - 1;

$queryString_rsMaintenanceNotifs = "";
if (!empty($my_server['QUERY_STRING'])) {
	$params = explode("&", $my_server['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_rsMaintenanceNotifs") == false &&
			stristr($param, "totalRows_rsMaintenanceNotifs") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_rsMaintenanceNotifs = "&" . htmlentities(implode("&", $newParams));
	}
}
$queryString_rsMaintenanceNotifs = sprintf("&totalRows_rsMaintenanceNotifs=%d%s", $totalRows_rsMaintenanceNotifs, $queryString_rsMaintenanceNotifs);

//employee filter list
$query_rsEmployees = "SELECT employeeID, engineer, displayName FROM employees WHERE engineer = 'y' ORDER BY displayName ASC";
$rsEmployees = $conn->query($query_rsEmployees) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsEmployees = $rsEmployees->fetch_assoc();
$totalRows_rsEmployees = $rsEmployees->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Maintenance Notifications"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue layout-top-nav">

		  <div class="wrapper">
				<header class="main-header">
					 <?php build_navbar($conn, 3); ?>
				</header> 
		  </div>

		  <div class="content-wrapper">

				<div class="container-fluid">

					 <?php
					 buildNewHeader('maintenances.php', 'Maintenance Notifications', '', 'maintenance.php', 'Add a Maintenance Notification');
					 ?>

					 <div class="row">
						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <h3 class="box-title">Maintenance Notifications</h3>
								</div>
								<div class='box-body'>
									 <table id="table_maintenance_notificacions" class="table table-bordered table-striped">
										  <thead>
												<tr>
													 <th width="6%">Date</th>
													 <th width="6%">Start<br />Time</th>
													 <th width="6%">ID</th>
													 <th>Reason</th>
													 <?php
													 if (!(isset($my_get['employee']))) {
														 echo "<th>Engineer</th>";
													 }
													 if ((!isset($my_get['status'])) || ($my_get['status'] == "All")) {
														 echo "<th>Status</th>";
													 }
													 ?>
												</tr>
										  </thead>
										  <tbody>
												<?php
												while ($row_rsMaintenanceNotifs = $rsMaintenanceNotifs->fetch_assoc()) {
													?>
													<tr>
														 <td><?php echo $row_rsMaintenanceNotifs['startDate']; ?></td>
														 <td><?php echo $row_rsMaintenanceNotifs['startTime']; ?></td>
														 <td><a title="View Maintenance Notification" href="maintenance.php?function=view&amp;maintenance=<?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?>"><?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?></a></td>
														 <td><a title="View Maintenance Notification" href="maintenance.php?function=view&amp;maintenance=<?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?>"><?php echo stripslashes($row_rsMaintenanceNotifs['reason']); ?></a></td>
														 <?php
														 if (!(isset($my_get['employee']))) {
															 echo "<td>" . $row_rsMaintenanceNotifs['displayName'] . "</td>";
														 }
														 if ((!isset($my_get['status'])) || ($my_get['status'] == "All")) {
															 echo "<td>" . $row_rsMaintenanceNotifs['status'] . "</td>";
														 }
														 ?>
													</tr>
												<?php } ?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_maintenance_notificacions').dataTable({"order": [[2, 'desc']], "pageLength": 25});
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