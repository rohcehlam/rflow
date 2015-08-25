<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'maintenance' => FILTER_SANITIZE_SPECIAL_CHARS,
	'module' => FILTER_SANITIZE_SPECIAL_CHARS,
	'project' => FILTER_SANITIZE_SPECIAL_CHARS,
	'projectEvent' => FILTER_SANITIZE_SPECIAL_CHARS,
	'rfa' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);

if ($my_get['function'] == "view") {
	//maintenance notification
	$varMaintenance_rsMaintenanceNotif = isset($my_get['maintenance']) ? $my_get['maintenance'] : '1';
	$query_rsMaintenanceNotif = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact"
		. ", maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%H:%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours"
		. ", maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName, maintenancenotifs.status"
		. " FROM maintenancenotifs, employees"
		. " WHERE maintenancenotifs.maintenanceNotifsID='$varMaintenance_rsMaintenanceNotif' AND maintenancenotifs.employeeID=employees.employeeID";
	$rsMaintenanceNotif = $conn->query($query_rsMaintenanceNotif) or die($conn->error);
	$row_rsMaintenanceNotif = $rsMaintenanceNotif->fetch_assoc();
	$totalRows_rsMaintenanceNotif = $rsMaintenanceNotif->num_rows;

	//associated status reports
	$varMaintenance_rsAssociatedStatusReports = isset($my_get['maintenance']) ? $my_get['maintenance'] : '1';
	$query_rsAssociatedStatusReports = "SELECT statusreports.statusReportID, DATE_FORMAT(endDate, '%m/%d/%Y') as endDate, statusreports.subject, statusreports.employeeID"
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

	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(4); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'maintenances.php', 'text' => 'RFCs'], ['url' => '#', 'text' => ucwords($my_get['function']) . ' a Maintenance']], ucwords($my_get['function']) . ' a Maintenance') ?>

					 <section class="content">


						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> a Maintenance Notification</h3>
								</div>
								<div class='box-body'>

									 <form class="form-horizontal" action="maintenanceSend.php" method="post" enctype="multipart/form-data" name="maintenanceUpdate" data-toggle="validator">
										  <div class="form-group">
												<label for='startdate' class="control-label col-xs-2">Start Date:</label>
												<div class="col-xs-4">
													 <?php
													 if ($my_get['function'] == 'view') {
														 ?>
														 <div class="input-group">
															  <input type="text" name='startDate' class="form-control" value="<?php echo $row_rsMaintenanceNotif['startDate']; ?>" readonly />
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
														 <?php
														 //echo "<p class=\"form-control-static\">{$row_rsMaintenanceNotif['startDate']}</p>\n";
													 } else {
														 ?>
														 <div class="input-group">
															  <span class="input-group-addon" onclick='opendatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
															  <input type="text" id="startDate" name='startDate' class="form-control" value="<?php echo date('Y-m-d') ?>" required/>
														 </div>
														 <?php
													 }
													 ?>
												</div>
												<label for='status' class="control-label col-xs-2">Status:</label>
												<div class="col-xs-4">
													 <select<?php echo (($row_rsMaintenanceNotif['status'] == "Closed") || ($row_rsMaintenanceNotif['status'] == "Canceled")) ? " name=\"userStatus\" id=\"userStatus\" disabled=\"disabled\"" : " name=\"status\" id=\"status\""; ?> class='form-control'>
														  <?php
														  foreach (['Open', 'Closed', 'Canceled', 'Extended'] as $data) {
															  echo "<option value='$data'" . ($data === $row_rsMaintenanceNotif['status'] ? " selected='selected'" : '') . ">$data</option>\n";
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
												<?php if ($my_get['function'] != "view") { ?>
													<div class='col-xs-2'>
														 <div class="input-group">
															  <input type="text" name="startHour" id="startHour" maxlength="2" class='form-control' value='<?php echo date('H'); ?>' required/>
															  <span class="input-group-addon">&nbsp;<strong>:</strong>&nbsp;</span>
														 </div>
													</div>
													<div class='col-xs-2'>
														 <div class="input-group">
															  <input type="text" name="startMinute" id="startMinute" maxlength="2" class='form-control' value='<?php echo date('i'); ?>' required/>
															  <span class="input-group-addon">UTC</span>
														 </div>
													</div>
													<?php
												} else {
													?>
													<div class='col-xs-4'>
														 <div class="input-group">
															  <input type="text" name='startTime' class="form-control" value="<?php echo $row_rsMaintenanceNotif['startTime']; ?>" readonly />
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													</div>
													<?php
													//echo "<div class='col-xs-4'>\n<p class=\"form-control-static\">{$row_rsMaintenanceNotif['startTime']}&nbsp;</p></div>\n";
												}
												?>
												<label for='estHours' class="control-label col-xs-2">Estimated Duration:</label>
												<?php if ($my_get['function'] != "view") { ?>
													<div class='col-xs-2'>
														 <div class="input-group">
															  <span class="input-group-addon">Hour(s)</span>
															  <input type="text" name="estHours" id="estHours" maxlength="2"  class='form-control' value='00' required/>
														 </div>
													</div>
													<div class='col-xs-2'>
														 <div class="input-group">
															  <span class="input-group-addon">Minute(s)</span>
															  <input type="text" name="estMins" id="estMins" maxlength="2" tabindex="2" class='form-control' value='30' required/>
														 </div>
													</div>
													<?php
												} else {
													?>
													<div class='col-xs-4'>
														 <div class="input-group">
															  <input type="text" name='estimated' class="form-control" value="<?php echo "{$row_rsMaintenanceNotif['estimatedHours']} Hours(s)&nbsp;{$row_rsMaintenanceNotif['estimatedMinutes']} Minute(s)"; ?>" readonly />
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													</div>
													<?php
													//echo "<div class='col-xs-4'>\n<p class=\"form-control-static\">{$row_rsMaintenanceNotif['estimatedHours']} Hours(s)&nbsp;{$row_rsMaintenanceNotif['estimatedMinutes']} Minute(s)</p></div>\n";
												}
												?>
										  </div>

										  <div class='form-group'>
												<label for='reason' class="control-label col-xs-2">Reason:</label>
												<div class="col-xs-10">
													 <?php if ($my_get['function'] != "view") { ?>
														 <input id='reason' name='reason' value='<?php echo $row_rsMaintenanceNotif['reason']; ?>' class='form-control' placeholder='Reason' required/>
													 <?php } else { ?>
														 <div class='input-group'>
															  <input id='reason' name='reason' value='<?php echo $row_rsMaintenanceNotif['reason']; ?>' class='form-control' readonly/>
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													 <?php } ?>
												</div>
										  </div>
										  <div class='form-group'>
												<label for='customerImpact' class="control-label col-xs-2">Customer Impact:</label>
												<div class="col-xs-10">
													 <?php if ($my_get['function'] != "view") { ?>
														 <input id='customerImpact' name='customerImpact' value='<?php echo $row_rsMaintenanceNotif['customerImpact']; ?>' class='form-control' placeholder='Customer Impact' required/>
													 <?php } else { ?>
														 <div class='input-group'>
															  <input id='customerImpact' name='customerImpact' value='<?php echo $row_rsMaintenanceNotif['customerImpact']; ?>' class='form-control' readonly/>
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													 <?php } ?>
												</div>
										  </div>
										  <div class='form-group'>
												<label for='nocImpact' class="control-label col-xs-2">NOC Impact:</label>
												<div class="col-xs-10">
													 <?php if ($my_get['function'] != "view") { ?>
														 <input id='nocImpact' name='nocImpact' value='<?php echo $row_rsMaintenanceNotif['nocImpact']; ?>' class='form-control' placeholder='NOC Impact' required/>
													 <?php } else { ?>
														 <div class='input-group'>
															  <input id='nocImpact' name='nocImpact' value='<?php echo $row_rsMaintenanceNotif['nocImpact']; ?>' class='form-control' readonly/>
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													 <?php } ?>
												</div>
										  </div>

										  <div class='form-group'>
												<label for='engineer' class='control-label col-xs-2'>Engineer</label>
												<div class="col-xs-4">
													 <?php if ($my_get['function'] != "view") { ?>
														 <div class="input-group">
															  <select name="engineer" id="engineer" class='form-control'>
																	<?php
																	while ($row_rsEngineers = $rsEngineers->fetch_assoc()) {
																		echo "<option value='{$row_rsEngineers['employeeID']}'" . (($row_rsEngineers['employeeID'] == $_SESSION['employee']) ? "selected ='selected'" : '') . ">{$row_rsEngineers['displayName']}</option>\n";
																	}
																	?>
															  </select>
															  <?php sudoAuth("../common/employee.php?function=add", "Add an Engineer", "add"); ?>
														 </div>
													 <?php } else { ?>
														 <div class="input-group">
															  <input type="text" name='engineer' class="form-control" value="<?php echo $row_rsMaintenanceNotif['displayName']; ?>" readonly />
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													 <!-- echo "<p class=\"form-control-static\">{$row_rsMaintenanceNotif['displayName']}</p>\n"; -->
													 <?php } ?>
												</div>
										  </div>

										  <div class='form-group'>
												<label for='prodChanges' class="control-label col-xs-2">Production Changes:</label>
												<div class="col-xs-10">
													 <?php if ($my_get['function'] != "view") { ?>
														 <textarea name='prodChanges' id='prodChanges' class='form-control' rows="5" required><?php echo $row_rsMaintenanceNotif['prodChanges']; ?></textarea>
													 <?php } else { ?>
														 <div class='input-group'>
															  <textarea name='prodChanges' id='prodChanges' class='form-control' rows="5" readonly><?php echo $row_rsMaintenanceNotif['prodChanges']; ?></textarea>
															  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														 </div>
													 <?php } ?>
												</div>
										  </div>

										  <div class='form-group'>
												<label for='cc' class='control-label col-xs-2'>Email Recipients</label>
												<div class="col-xs-5">
													 <div class="btn-group btn-group-justified" data-toggle="buttons">
														  <label class="btn btn-default active">
																<input type="checkbox" name="prodOps" id="prodOps" value="y" checked='checked'/>Tech Support
														  </label>
														  <label class="btn btn-default">
																<input type="checkbox" name="noc" id="noc" value="y"/>Product Dev
														  </label>
														  <label class="btn btn-default">
																<input type="checkbox" name="syseng" id="syseng" value="y"/>Sales
														  </label>
														  <label class="btn btn-default">
																<input type="checkbox" name="neteng" id="neteng" value="y"/>Projects
														  </label>
													 </div>
												</div>
												<div class="col-xs-5">
													 <div class="input-group">
														  <span class="input-group-addon">CC:</span>
														  <input type="text" class="form-control" value="" name='cc' id='cc' placeholder="Carbon Copy"/>
													 </div>
												</div>
										  </div>

								</div>
								<div class="box-footer">
									 <div class="form-group">
										  <div class="col-xs-offset-2 col-xs-10">
												<?php if (($row_rsMaintenanceNotif['status'] != "Closed") && ($row_rsMaintenanceNotif['status'] != "Canceled")) { ?>
													<button type="submit" class="btn btn-primary"><span class='glyphicon glyphicon-save'></span>&nbsp;Send Maintenance Notification</button>
												<?php } ?>
												<?php if ($my_get['function'] != 'add') { ?>
													<a class="btn btn-default" href="../statusReports/statusReport.php?function=add&amp;maintenance=<?php
													echo $row_rsMaintenanceNotif['maintenanceNotifsID'];
													if ($row_rsAnyPEforMN > 0) {
														echo "&amp;project=" . $row_rsAnyPEforMN['projectID'] . "&amp;module=statusReport&amp;projectEvent=" . $row_rsAnyPEforMN['projectTaskID'] . "&amp;function=add";
													}
													?>"><span class="glyphicon glyphicon-new-window"></span>&nbsp;Generate Status Report</a>
													<?php } ?>
										  </div>
									 </div>

									 <?php if ($my_get['function'] != "add") { ?>
										 <input type="hidden" name="maintenance" id="maintenance" value="<?php echo $my_get['maintenance']; ?>" />
										 <input type="hidden" name="MM_update" id="MM_update" value="maintenanceUpdate" />
									 <?php } else { ?>
										 <input type="hidden" name="MM_insert" value="maintenanceNotif1" />
										 <input type="hidden" name="status" value="Open" />
										 <?php
										 if (isset($my_get['module'])) {
											 echo "<input type=\"hidden\" name=\"module\" value=\"" . $my_get['module'] . "\" />";
											 if (isset($my_get['project'])) {
												 echo "<input type=\"hidden\" name=\"project\" value=\"" . $my_get['project'] . "\" />";
											 }
											 if (isset($my_get['projectEvent'])) {
												 echo "<input type=\"hidden\" name=\"projectEvent\" value=\"" . $my_get['projectEvent'] . "\" />";
											 }
											 if (isset($my_get['rfa'])) {
												 echo "<input type=\"hidden\" name=\"rfa\" value=\"" . $my_get['rfa'] . "\" />";
											 }
										 }
									 }
									 ?>

									 </form>
									 <script>
                               $(function () {
                                   $("#startDate").datepicker();
                               });
                               function opendatepicker() {
                                   $("#startDate").datepicker("show");
                               }
									 </script>
								</div> <!-- /.box-body -->
						  </div><!-- /.box -->

					 </section>
				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>
		  <script src="../js/bootstrap-datepicker.js"></script>
		  <link rel="stylesheet" href="../css/datepicker.css"/>

	 </body>
</html>