<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	 'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'maintenance' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);

if ($my_get['function'] == "view") {
	//maintenance notification
	$varMaintenance_rsMaintenanceNotif = isset($my_get['maintenance']) ? $my_get['maintenance'] : '1';
	$query_rsMaintenanceNotif = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact"
			  . ", maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%%H:%%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours"
			  . ", maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName, maintenancenotifs.status"
			  . " FROM maintenancenotifs, employees"
			  . " WHERE maintenancenotifs.maintenanceNotifsID='$varMaintenance_rsMaintenanceNotif' AND maintenancenotifs.employeeID=employees.employeeID";
	$rsMaintenanceNotif = $conn->query($query_rsMaintenanceNotif) or die($conn->error);
	$row_rsMaintenanceNotif = $rsMaintenanceNotif->fetch_assoc();
	$totalRows_rsMaintenanceNotif = $rsMaintenanceNotif->num_rows;

	//associated status reports
	$varMaintenance_rsAssociatedStatusReports = isset($my_get['maintenance']) ? $my_get['maintenance'] : '1';
	$query_rsAssociatedStatusReports = "SELECT statusreports.statusReportID, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate, statusreports.subject, statusreports.employeeID"
			  . ", employees.displayName, statusreports.maintenanceNotifID"
			  . " FROM statusreports LEFT JOIN employees ON statusreports.employeeID=employees.employeeID"
			  . " WHERE statusreports.maintenanceNotifID = $varMaintenance_rsAssociatedStatusReports ORDER BY statusreports.endDate, statusreports.endTime ASC";
	$rsAssociatedStatusReports = $conn->query($query_rsAssociatedStatusReports) or die($conn->error);
	$row_rsAssociatedStatusReports = $rsAssociatedStatusReports->fetch_assoc();
	$totalRows_rsAssociatedStatusReports = $rsAssociatedStatusReports->num_rows;
}

if (($my_get['function'] == "view") || ($my_get['function'] == "update")) {
	//find out if this maintenance notification is already tied to a project event/project
	$varModuleID_rsAnyPEforMN = "1";
	if (isset($my_get['maintenance'])) {
		$varModuleID_rsAnyPEforMN = (get_magic_quotes_gpc()) ? $my_get['maintenance'] : addslashes($my_get['maintenance']);
	}
	$query_rsAnyPEforMN = "SELECT projecttasksxmodules.projectTasksXmoduleID, projecttasksxmodules.projectID, projecttasksxmodules.projectTaskID, projecttasksxmodules.module"
			  . ", projecttasksxmodules.moduleID"
			  . " FROM projecttasksxmodules WHERE projecttasksxmodules.moduleID = $varModuleID_rsAnyPEforMN AND projecttasksxmodules.module='maintenance'";
	$rsAnyPEforMN = $conn->query($query_rsAnyPEforMN) or die($conn->error);
	$row_rsAnyPEforMN = $rsAnyPEforMN->fetch_assoc();
	$totalRows_rsAnyPEforMN = $rsAnyPEforMN->num_rows;
}

//Select engineers
$query_rsEngineers = "SELECT employeeID, engineer, lastName, displayName FROM employees WHERE employees.engineer='y' AND employees.active='t' ORDER BY displayName ASC";
$rsEngineers = $conn->query($query_rsEngineers) or die($conn->error);
$row_rsEngineers = $rsEngineers->fetch_assoc();
$totalRows_rsEngineers = $rsEngineers->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Maintenance Notification"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<?php build_header(); ?>

		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"/>

	</head>
	<body class="skin-blue layout-top-nav">

		<div class="wrapper">
			<header class="main-header">
				<?php build_navbar(3, !isset($_SESSION['employee']) ? "<li>\n<a href=\"index.php\">Login</a>\n</li>\n" : "<li><a href='#'>Welcome, {$row_rsEmployeeInfo['firstName']}!</a></li>\n<li><a href=\"$logoutAction\">Logout</a></li>\n") ?>
			</header> 
		</div>

		<div class="content-wrapper">

			<div class="container-fluid">

				<div class="page-header">
					<h3>Maintenance</h3>
					<!--
					<p class="lead">&nbsp;</p>
					-->
				</div>

				<div class='row'>
					<div class='col-md-2'></div>
					<div class='col-md-8'>
						<form class="form-horizontal" action="maintenanceSend.php" method="post" enctype="multipart/form-data" name="maintenanceUpdate">
							<div class="form-group">
								<label for='startdate' class="control-label col-xs-2">Start Date:</label>
								<div class="col-xs-4">
									<?php
									if ($my_get['function'] == 'view') {
										echo "<p class=\"form-control-static\">{$row_rsMaintenanceNotif['startDate']}</p>\n";
									} else {
										?>
										<script>
											$(function () {
												$("#startDate").datepicker();
											});
										</script>
										<input type="text" id="startDate" class="form-control" placeholder="<?php echo date('m/d/Y') ?>"/>
										<?php
									}
									?>
								</div>
								<label for='status' class="control-label col-xs-2">Status:</label>
								<div class="col-xs-4">
									<select<?php echo (($row_rsMaintenanceNotif['status'] == "Closed") || ($row_rsMaintenanceNotif['status'] == "Canceled")) ? " name=\"userStatus\" id=\"userStatus\" disabled=\"disabled\"" : " name=\"status\" id=\"status\""; ?> class='form-control'>
										<?php
										$options = array(
											 'Open' => 'Open',
											 'Closed' => 'Closed',
											 'Canceled' => 'Canceled',
											 'Extended' => 'Extended',
										);
										foreach ($options as $key => $data) {
											echo "<option value='$key'" . ($key === $row_rsMaintenanceNotif['status'] ? " selected='selected'" : '') . ">$data</option>\n";
										}
										?>
									</select>
								</div>
								<?php
								if ($my_get['function'] != "add") {
									if (($row_rsMaintenanceNotif['status'] == "Closed") || ($row_rsMaintenanceNotif['status'] == "Canceled")) {
										echo "<input type=\"hidden\" value=\"" . $row_rsMaintenanceNotif['status'] . "\" name=\"status\" id=\"status\" />";
									}
								}
								?>
							</div>

							<div class='form-group'>
								<label for='' class="control-label col-xs-2">Start Time:</label>
								<div class="col-xs-4">
									<?php if ($my_get['function'] != "view") { ?>
										<div class='col-xs-4'>
											<input type="text" name="startHour" id="startHour" value="" maxlength="2" class='form-control' placeholder='<?php echo date('H'); ?>'/>
										</div>
										<div class='col-xs-4'>
											<input type="text" name="startMinute" id="startMinute" value="" maxlength="2" class='form-control' placeholder='<?php echo date('i'); ?>'/>
										</div>
										<?php
									} else {
										echo "<div class='col-xs-8'>\n<p class=\"form-control-static\">{$row_rsMaintenanceNotif['startTime']}&nbsp</p></div>\n";
									}
									?>
									<div class='col-xs-4'><strong>UTC</strong></div>
								</div>
								<label for='estHours' class="control-label col-xs-2">Estimated Duration:</label>
								<div class="col-xs-4">
									<?php if ($my_get['function'] != "view") { ?>
										<div class='col-xs-3'>
											<input type="text" name="estHours" id="estHours" maxlength="2"  class='form-control' placeholder='00'/>
										</div>
										<div class='col-xs-3'>
											<label for="estHours">&nbsp;hour(s)</label>
										</div>
										<div class='col-xs-3'>
											<input type="text" name="estMins" id="estMins" maxlength="2" tabindex="2" class='form-control' placeholder='30'/>
										</div>
										<div class='col-xs-3'>
											<label for="estMins">&nbsp;minute(s)</label>
										</div>
										<?php
									} else {
										if ($row_rsMaintenanceNotif['estimatedHours'] > "1") {
											echo $row_rsMaintenanceNotif['estimatedHours'] . " hours";
										} elseif ($row_rsMaintenanceNotif['estimatedHours'] == "1") {
											echo $row_rsMaintenanceNotif['estimatedHours'] . " hour";
										} else {
											echo "";
										}

										//start estimated minutes
										if ($row_rsMaintenanceNotif['estimatedMinutes'] > "1") {
											echo " " . $row_rsMaintenanceNotif['estimatedMinutes'] . " minutes";
										} elseif ($row_rsMaintenanceNotif['estimatedMinutes'] == "1") {
											echo " " . $row_rsMaintenanceNotif['estimatedMinutes'] . " minute";
										} else {
											echo "";
										}
									}
									?>
								</div>
							</div>

							<div class='form-group'>
								<label for='reason' class="control-label col-xs-2">Reason:</label>
								<div class="col-xs-10">
									<input id='reason' name='reason' value='<?php echo $row_rsMaintenanceNotif['reason']; ?>' class='form-control' placeholder='Reason'/>
								</div>
							</div>
							<div class='form-group'>
								<label for='customerImpact' class="control-label col-xs-2">Customer Impact:</label>
								<div class="col-xs-10">
									<input id='customerImpact' name='customerImpact' value='<?php echo $row_rsMaintenanceNotif['customerImpact']; ?>' class='form-control' placeholder='Customer Impact'/>
								</div>
							</div>
							<div class='form-group'>
								<label for='nocImpact' class="control-label col-xs-2">NOC Impact:</label>
								<div class="col-xs-10">
									<input id='nocImpact' name='nocImpact' value='<?php echo $row_rsMaintenanceNotif['nocImpact']; ?>' class='form-control' placeholder='NOC Impact'/>
								</div>
							</div>

							<div class='form-group'>
								<label for='engineer' class='control-label col-xs-2'>Engineer</label>
								<div class="col-xs-4">
									<?php if ($my_get['function'] != "view") { ?>
										<select name="engineer" id="engineer" class='form-control'>
											<option value=""<?php ($_SESSION['employee'] == '' ? '' : ' selected="selected"') ?> >Select Engineer</option>
											<?php
											while ($row_rsEngineers = $rsEngineers->fetch_assoc()) {
												echo "<option value='{$row_rsEngineers['employeeID']}'" . (($row_rsEngineers['employeeID'] == $_SESSION['employee']) ? "selected ='selected'" : '') . ">{$row_rsEngineers['displayName']}</option>\n";
											}
											?>
										</select><?php
										sudoAuth("../common/employee.php?function=add", "Add an Engineer", "add");
									} else {
										echo "<p class=\"form-control-static\">{$row_rsMaintenanceNotif['displayName']}</p>\n";
									}
									?>
								</div>
							</div>

							<div class='form-group'>
								<label for='prodChange' class="control-label col-xs-2">Production Changes:</label>
								<div class="col-xs-10">
									<textarea name='prodChange' id='prodChange' class='form-control' rows="5" ><?php echo $row_rsMaintenanceNotif['prodChanges']; ?></textarea>
								</div>
							</div>

							<div class='form-group'>
								<label for='cc' class='control-label col-xs-2'>Email Recipients</label>
								<div class='col-xs-10'>
									<div class="row">
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">
													<input type="checkbox" name="prodOps" id="prodOps" value="y" checked="checked"/>
												</span>
												<input type="text" class="form-control" value="US ProdOps" disabled="disabled"/>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">
													<input type="checkbox" name="noc" id="noc" value="y" checked="checked"/>
												</span>
												<input type="text" class="form-control" value="&nbsp;NOC &amp; SUI" disabled="disabled"/>
											</div>
										</div>
										<div class="col-xs-6">
											<div class="input-group">
												<span class="input-group-addon">CC:</span>
												<input type="text" class="form-control" value="" name='cc' id='cc' placeholder="Carbon Copy"/>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">
													<input type="checkbox" name="syseng" id="syseng" value="y"/>
												</span>
												<input type="text" class="form-control" value="&nbsp;SysEng" disabled="disabled"/>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">
													<input type="checkbox" name="neteng" id="neteng" value="y"/>
												</span>
												<input type="text" class="form-control" value="&nbsp;NetEng" disabled="disabled"/>
											</div>
										</div>
										<div class="col-xs-6">
											&nbsp;
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-xs-offset-2 col-xs-10">
									<button type="submit" class="btn btn-primary">Send Maintenance Notification</button>
									<?php if ($my_get['function'] != 'add') { ?>
										<a style="font-weight: bold;" href="../statusReports/statusReport.php?function=add&amp;maintenance=<?php
										echo $row_rsMaintenanceNotif['maintenanceNotifsID'];
										if ($row_rsAnyPEforMN > 0) {
											echo "&amp;project=" . $row_rsAnyPEforMN['projectID'] . "&amp;module=statusReport&amp;projectEvent=" . $row_rsAnyPEforMN['projectTaskID'] . "&amp;function=add";
										}
										?>">Generate Status Report</a>
										<?php } ?>
								</div>
							</div>
						</form>
					</div>
					<div class='col-md-2'></div>
				</div>

			</div> <!-- /container -->
		</div> <!-- /content-wrapper -->

		<?php build_footer(); ?>

	</body>
</html>