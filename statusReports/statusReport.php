<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'statusReport' => FILTER_SANITIZE_SPECIAL_CHARS,
	'maintenance' => FILTER_SANITIZE_SPECIAL_CHARS,
	'supportRequest' => FILTER_SANITIZE_SPECIAL_CHARS,
	'module' => FILTER_SANITIZE_SPECIAL_CHARS,
	'project' => FILTER_SANITIZE_SPECIAL_CHARS,
	'projectEvent' => FILTER_SANITIZE_SPECIAL_CHARS,
	'sent' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);

if ($my_get['function'] != "add") {
	$varStatusReport_rsStatusReport = "1";
	if (isset($my_get['statusReport'])) {
		$varStatusReport_rsStatusReport = addslashes($my_get['statusReport']);
	}
	$query_rsStatusReport = "SELECT statusreports.statusReportID, statusreports.employeeID, statusreports.customerID, statusreports.subject, statusreports.applicationID"
		. ", DATE_FORMAT(startDate, '%b %e of %Y') as startDate, TIME_FORMAT(startTime,'%k:%i') as startTime, DATE_FORMAT(endDate, '%b %e of %Y') as endDate"
		. ", TIME_FORMAT(endTime,'%k:%i') as endTime, statusreports.magicTicket, statusreports.wrm, statusreports.maintenanceNotifID, statusreports.notes, statusreports.actionItems"
		. ", statusreports.reportTypeID, applications.application, customers.customer, employees.displayName, reporttypes.reportType"
		. " FROM statusreports, applications, customers, employees, reporttypes"
		. " WHERE statusReportID = $varStatusReport_rsStatusReport AND statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID"
		. "  AND statusreports.employeeID=employees.employeeID AND statusreports.reporttypeID=reporttypes.reporttypeID";
	$rsStatusReport = $conn->query($query_rsStatusReport) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsStatusReport = $rsStatusReport->fetch_assoc();
	$totalRows_rsStatusReport = $rsStatusReport->num_rows;
}

if ($my_get['function'] == "add") {
	$varMaintenance_rsMaint = "1";
	if (isset($my_get['maintenance'])) {
		$varMaintenance_rsMaint = addslashes($my_get['maintenance']);
	}
	$query_rsMaint = "SELECT TIME_FORMAT(startTime,'%k:%i') as startTime, DATE_FORMAT(startDate,'%m/%d/%Y') as startDate, startDate as startDateRaw"
		. ", maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.prodChanges, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours"
		. ", maintenancenotifs.estimatedMinutes, employees.displayName"
		. " FROM maintenancenotifs, employees WHERE maintenanceNotifsID = $varMaintenance_rsMaint AND maintenancenotifs.employeeID=employees.employeeID";
	$rsMaint = $conn->query($query_rsMaint) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsMaint = $rsMaint->fetch_assoc();
	$totalRows_rsMaint = $rsMaint->num_rows;

	if (isset($my_get['supportRequest'])) {
		$varEscalation_rsSupportRequest = addslashes($my_get['supportRequest']);
		$query_rsSupportRequest = "SELECT escalations.escalationID, escalations.applicationID, applications.application, escalations.categoryID, reporttypes.reportType"
			. ", escalations.subject, escalations.assignedTo, employees.displayName, escalations.ticket, escalations.customerID, customers.customer"
			. " FROM escalations"
			. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
			. " LEFT JOIN reportTypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID"
			. " LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE escalations.escalationID = $varEscalation_rsSupportRequest";
		$rsSupportRequest = $conn->query($query_rsSupportRequest) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
		$row_rsSupportRequest = $rsSupportRequest->fetch_assoc();
		$totalRows_rsSupportRequest = $rsSupportRequest->num_rows;
	}
}

$query_rsCustomers = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCustomers = $conn->query($query_rsCustomers) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsCustomers = $rsCustomers->fetch_assoc();
$totalRows_rsCustomers = $rsCustomers->num_rows;

$query_rsApplications = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApplications = $conn->query($query_rsApplications) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsApplications = $rsApplications->fetch_assoc();
$totalRows_rsApplications = $rsApplications->num_rows;

$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
$rsReportTypes = $conn->query($query_rsReportTypes) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsReportTypes = $rsReportTypes->fetch_assoc();
$totalRows_rsReportTypes = $rsReportTypes->num_rows;

$query_rsEmployees = "SELECT employeeID, engineer, displayName FROM employees WHERE engineer = 'y' AND active = 't' ORDER BY displayName ASC";
$rsEmployees = $conn->query($query_rsEmployees) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsEmployees = $rsEmployees->fetch_assoc();
$totalRows_rsEmployees = $rsEmployees->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Status Report: " . stripslashes($row_rsStatusReport['subject'])); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		  <?php build_header(); ?>

		  <script src="../js/bootstrap-datepicker.js"></script>
		  <link rel="stylesheet" href="../css/datepicker.css"/>
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
					 buildNewHeader('statusReports.php', 'Status Reports', "{$my_get['function']} a Status Report");
					 ?>

					 <div class='row'>
						  <div class='col-md-2'></div>
						  <div class='col-md-8'>
								<div class='box box-primary'>
									 <div class='box-header with-border'>
										  <h3 class="box-title"><?php echo $my_get['function']; ?> a Status Report</h3>
									 </div>
									 <div class='box-body'>
										  <form class="form-horizontal" action="statusReportSend.php" method="post" name="statusReportForm" id="statusReportForm">
												<div class="form-group">
													 <label for='startdate' class="control-label col-xs-2">Start Date:</label>
													 <div class="col-xs-4">
														  <?php
														  if ($my_get['function'] != "view") {
															  if (isset($my_get['maintenance'])) {
																  ?>
																  <div class="input-group">
																		<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
																		<input name="userStartDate" type="text" id="userstartDate" value="<?php echo $row_rsMaint['startDate']; ?>" class="form-control" readonly="<?php echo $row_rsMaint['startDate']; ?>"/>
																		<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
																		<input type="hidden" name="startDate" id="startDate" value="<?php echo $row_rsMaint['startDateRaw']; ?>" />
																  </div>											
															  <?php } else { ?>
																  <div class="input-group">
																		<span class="input-group-addon" onclick='openstartdatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
																		<input type="text" name="startDate" id="startDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required/>
																  </div>
															  <?php } ?>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['startDate']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsStatusReport['startDate'];                         ?></p> -->
														  <?php } ?>
													 </div>
													 <label for='enddate' class="control-label col-xs-2">End Date:</label>
													 <div class="col-xs-4">
														  <?php if ($my_get['function'] != "view") { ?>
															  <div class="input-group">
																	<span class="input-group-addon" onclick='openenddatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
																	<input type="text" name='endDate' id="endDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required/>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['endDate']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsStatusReport['endDate'];                        ?></p> -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='subject' class="control-label col-xs-2">Subject:</label>
													 <div class="col-xs-10">
														  <?php if ($my_get['function'] != "view") { ?>
															  <input type="text" name="subject" id="subject" maxlength="255" placeholder="Subject" class="form-control" required/>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['subject']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsStatusReport['subject'];                        ?></p> -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='startHour' class="control-label col-xs-2">Start Time:</label>
													 <?php if ($my_get['function'] != "view") { ?>
														 <div class='col-xs-2'>
															  <div class="input-group">
																	<input type="text" name="startHour" id="startHour" value="" maxlength="2" class='form-control' placeholder='<?php echo date('H'); ?>'/>
																	<span class="input-group-addon">&nbsp;<strong>:</strong>&nbsp;</span>
															  </div>
														 </div>
														 <div class='col-xs-2'>
															  <div class="input-group">
																	<input type="text" name="startMinute" id="startMinute" value="" maxlength="2" class='form-control' placeholder='<?php echo date('i'); ?>'/>
																	<span class="input-group-addon">UTC</span>
															  </div>
														 </div>
													 <?php } else { ?>
														 <div class='col-xs-4'>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['startTime']; ?>" readonly />
																	<span class="input-group-addon"><strong>UTC</strong></span>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- <p class="form-control-static">{$row_rsStatusReport['startTime']} -->
														 </div>
													 <?php } ?>
													 <label for='endHour' class="control-label col-xs-2">End Time:</label>
													 <?php if ($my_get['function'] != "view") { ?>
														 <div class='col-xs-2'>
															  <div class="input-group">
																	<input type="text" name="endHour" id="endHour" value="" maxlength="2" class='form-control' placeholder='<?php echo date('H'); ?>'/>
																	<span class="input-group-addon">&nbsp;<strong>:</strong>&nbsp;</span>
															  </div>
														 </div>
														 <div class='col-xs-2'>
															  <div class="input-group">
																	<input type="text" name="endMinute" id="endMinute" value="" maxlength="2" class='form-control' placeholder='<?php echo date('i'); ?>'/>
																	<span class="input-group-addon">UTC</span>
															  </div>
														 </div>
													 <?php } else { ?>
														 <div class='col-xs-4'>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['endTime']; ?>" readonly />
																	<span class="input-group-addon"><strong>UTC</strong></span>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- <p class=\"form-control-static\">{$row_rsStatusReport['endTime']} -->
														 </div>
													 <?php } ?>
												</div>

												<div class='form-group'>
													 <label for='magic' class="control-label col-xs-2">Ticket #:</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <input type="text" name="magic" id="magic" value="" maxlength="20" class='form-control' placeholder='Ticket #' required/>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['magicTicket'] == "0" ? '-' : $row_rsStatusReport['magicTicket']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- echo "<p class=\"form-control-static\">" . ($row_rsStatusReport['magicTicket'] == "0" ? '-' : $row_rsStatusReport['magicTicket']) . "&nbsp;</p>"; -->
														  <?php } ?>
													 </div>
													 <label for='wrm' class="control-label col-xs-2">Case #:</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <input type="text" name="wrm" id="wrm" value="" class='form-control' placeholder='Case #' required/>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['wrm'] == "0" ? '-' : $row_rsStatusReport['wrm']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!--echo "<p class=\"form-control-static\">" . ($row_rsStatusReport['wrm'] == "0" ? '-' : $row_rsStatusReport['wrm']) . "&nbsp;</p>"; -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='customers' class="control-label col-xs-2">Customer :</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <select name="customers" id="customers" class="form-control" required>
																	<?php
																	while ($row_rsCustomers = $rsCustomers->fetch_assoc()) {
																		echo "<option value='{$row_rsCustomers['customerID']}'>{$row_rsCustomers['customer']}</option>\n";
																	}
																	?>
															  </select>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['customer']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- echo "<div class='col-xs-4'>\n<p class=\"form-control-static\">{$row_rsStatusReport['customer']}&nbsp;</p></div>\n"; -->
														  <?php } ?>
													 </div>
													 <label for='reportType' class="control-label col-xs-2">Report Type:</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <div class="input-group">
																	<select name="reportType" id="reportType" class="form-control" required>
																		 <?php
																		 while ($row_rsReportTypes = $rsReportTypes->fetch_assoc()) {
																			 echo "<option value='{$row_rsReportTypes['reportTypeID']}'" . ((isset($my_get['supportRequest']) && !(strcmp($row_rsReportTypes['reportTypeID'], $row_rsSupportRequest['categoryID']))) ? ' selected="selected"' : '' ) . ">{$row_rsReportTypes['reportType']}</option>\n";
																		 }
																		 ?>
																	</select>
																	<?php sudoAuth("../common/reportTypeAdd", "Add a Report Type", "glyphicon-plus-sign"); ?>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['reportType']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- echo "<p class=\"form-control-static\">{$row_rsStatusReport['reportType']}&nbsp;</p>"; -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='app' class="control-label col-xs-2">Application :</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <div class="input-group">
																	<select name="app" id="app" class="form-control">
																		 <?php
																		 while ($row_rsApplications = $rsApplications->fetch_assoc()) {
																			 echo "<option value='{$row_rsApplications['applicationID']}'" . ((isset($my_get['supportRequest'])) && (!(strcmp($row_rsApplications['applicationID'], $row_rsSupportRequest['applicationID']))) ? ' selected="selected"' : '') . ">{$row_rsApplications['application']}</option>\n";
																		 }
																		 ?>
																	</select>
																	<?php sudoAuth("../common/appAdd", "Add an Application", "glyphicon-plus-sign"); ?>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['application']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- <p class=\"form-control-static\">{$row_rsStatusReport['application']}&nbsp;</p> -->
														  <?php } ?>
													 </div>
													 <label for='engineer' class="control-label col-xs-2">Engineer:</label>
													 <div class='col-xs-4'>
														  <?php if ($my_get['function'] != "view") { ?>
															  <div class="input-group">
																	<select name="engineer" id="engineer" class="form-control" required>
																		 <?php
																		 while ($row_rsEmployees = $rsEmployees->fetch_assoc()) {
																			 //echo "<option value='{$row_rsEmployees['employeeID']}'>{$row_rsEmployees['displayName']}</option>\n";
																			 echo "<option value='{$row_rsEmployees['employeeID']}'" . (($row_rsEmployees['employeeID'] == $_SESSION['employee']) ? "selected ='selected'" : '') . ">{$row_rsEmployees['displayName']}</option>\n";
																		 }
																		 ?>
																	</select>
																	<?php sudoAuth("../common/employeeAdd", "Add an Employee", "glyphicon-plus-sign"); ?>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" class="form-control" value="<?php echo $row_rsStatusReport['displayName']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 <!-- <p class=\"form-control-static\">{$row_rsStatusReport['displayName']}&nbsp;</p> -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='notes' class="control-label col-xs-2">Notes:</label>
													 <div class="col-xs-10">
														  <?php if ($my_get['function'] != "view") { ?>
															  <textarea name='notes' id='notes' class='form-control' rows="5" required><?php echo (isset($my_get['maintenance']) ? $row_rsMaint['prodChanges'] : ''); ?></textarea>
														  <?php } else { ?>
															  <div class="input-group">
																	<textarea rows="5" class="form-control" readonly><?php echo nl2br($row_rsStatusReport['notes']); ?></textarea>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo nl2br($row_rsStatusReport['notes']);    ?></p> -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='actionItems' class="control-label col-xs-2">Action Items:</label>
													 <div class="col-xs-10">
														  <?php if ($my_get['function'] != "view") { ?>
															  <textarea name='actions' id='actionItems' class='form-control' rows="5" ></textarea>
														  <?php } else { ?>
															  <div class="input-group">
																	<textarea rows="5" class="form-control" readonly><?php echo nl2br($row_rsStatusReport['actionItems']); ?></textarea>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo nl2br($row_rsStatusReport['actionItems']);   ?></p> -->
														  <?php } ?>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='cc' class='control-label col-xs-2'>Email Recipients</label>
													 <div class="col-xs-5">
														  <div class="btn-group btn-group-justified" data-toggle="buttons">
																<label class="btn btn-default active">
																	 <input type="checkbox" value='y' name="prodOps" id="prodOps" checked='checked' />Tech Support
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" value='y' name="noc" id="noc"/>Product Dev
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" value='y' name="syseng" id="syseng"/>Sales
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" value='y' name="neteng" id="neteng"/>Projects
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
													 <?php if ($my_get['function'] != "view") { ?>
														 <button type="submit" class="btn btn-primary"><span class='glyphicon glyphicon-save'></span>&nbsp;Send Status Report</button>
													 <?php } ?>
													 <?php if ($my_get['function'] != "add") { ?>
														 <input type="hidden" name="statusReport" id="statusReport" value="<?php echo $my_get['statusReport'] ?>" /><?php
													 } else {
														 echo "<input type=\"hidden\" name=\"MM_insert\" value=\"statusReportAdd\" />";
														 if (isset($my_get['project'])) {
															 ?>
															 <input type="hidden" name="module" value="<?php echo $my_get['module']; ?>" />
															 <input type="hidden" name="project" value="<?php echo $my_get['project']; ?>" />
															 <input type="hidden" name="projectEvent" value="<?php echo $my_get['projectEvent']; ?>" />
															 <?php
														 }
													 }
													 sentSuccessful("Status Report sent successfully!");
													 ?>
												</div>
										  </div>

										  </form>
										  <script>
                                   $(function () {
                                       $("#startDate").datepicker();
                                       $("#endDate").datepicker();
                                   });
                                   function openstartdatepicker() {
                                       $("#startDate").datepicker("show");
                                   }
                                   function openenddatepicker() {
                                       $("#endDate").datepicker("show");
                                   }
										  </script>
									 </div><!-- /.box-body -->
								</div><!-- /.box -->
						  </div>
						  <div class='col-md-2'></div>
					 </div>

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>
	 </body>
</html>