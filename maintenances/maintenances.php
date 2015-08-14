<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();
check_permission();

$args = array(
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
$where = " AND (maintenancenotifs.status='Open' OR maintenancenotifs.status='Extended')";
if (isset($my_get['employee'])) {
	$where = " AND maintenancenotifs.employeeID={$my_get['employee']}";
	$result = $conn->query("SELECT displayName FROM employees WHERE employeeID={$my_get['employee']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Engineer: </em>{$row['displayName']}\n";
}
if (isset($my_get['status'])) {
	$where = " AND maintenancenotifs.status='{$my_get['status']}'";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Status: </em>{$my_get['status']}\n";
}

$varEmployee_rsMaintenanceNotifs = (isset($my_get['employee']) ? addslashes($my_get['employee']) : "1");
$varStatus_rsMaintenanceNotifs = (isset($my_get['status']) ? addslashes($my_get['status']) : "1");


$query_rsMaintenanceNotifs = str_replace('%%where%%', $where, "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort"
	. ", startDate AS startDateSort, TIME_FORMAT(startTime, '%H:%i') as startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName"
	. ", maintenancenotifs.status"
	. " FROM maintenancenotifs, employees"
	. " WHERE maintenancenotifs.employeeID=employees.employeeID"
	. " %%where%%"
	. " ORDER BY startDateSort DESC, startTimeSort DESC");

$rsMaintenanceNotifs = $conn->query($query_rsMaintenanceNotifs) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Maintenance Notifications"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(4); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'maintenances.php', 'text' => 'Maintenances']], 'Maintenances', $filter_text) ?>

					 <section class="content">

						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <a class='btn btn-primary' href='maintenance.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add Maintenance</a>
									 <div class="box-tools pull-right">
										  <div id="div_flt_nofilter">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li class="active"><a href="#">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																</ul>
														  </div>
														  <label class="form-control">No Filter</label>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_nofilter -->
										  <div id="div_flt_engineer" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li class="active"><a href="#">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
														  </div>
														  <label class="input-group-addon">Engineer:&nbsp;</label>
														  <select id="input_div_flt_engineer" name="employee" class="form-control">
																<?php
																$result = $conn->query("SELECT employeeID, displayName FROM employees ORDER BY displayName ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['employeeID']}'" . ($my_get['employee'] == $row['employeeID'] ? " selected='selected'" : '') . ">{$row['displayName']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_engineer -->
										  <div id="div_flt_status" style="display: none;">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li class="active"><a href="#">Status</a></li>
														  </div>
														  <label class="input-group-addon">Status:&nbsp;</label>
														  <select id="input_div_flt_status" name="status" class="form-control">
																<?php
																foreach (['Open', 'Closed', 'Canceled', 'Extended'] as $data) {
																	echo "<option value='$data'" . ($data == $my_get['status'] ? " selected='selected'" : '') . ">$data</option>";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_status -->
									 </div>
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
                               function display_filter(filter) {
                                   $("#div_flt_nofilter").hide();
                                   $("#div_flt_engineer").hide();
                                   $("#div_flt_status").hide();
                                   $("#" + filter).show();
                                   $("#input_" + filter).focus();
                               }
<?php echo isset($my_get['employee']) ? "display_filter('div_flt_engineer');\n" : ''; ?>
<?php echo isset($my_get['status']) ? "display_filter('div_flt_status');\n" : ''; ?>
									 </script>
								</div><!-- /.box-body -->
						  </div><!-- /.box -->

					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
		  </div> <!-- /content-wrapper -->


	 </body>
</html>