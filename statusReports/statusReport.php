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
);

$my_get = filter_input_array(INPUT_GET, $args);

if ($my_get['function'] != "add") {
	$varStatusReport_rsStatusReport = "1";
	if (isset($my_get['statusReport'])) {
		$varStatusReport_rsStatusReport = addslashes($my_get['statusReport']);
	}
	$query_rsStatusReport = "SELECT statusreports.statusReportID, statusreports.employeeID, statusreports.customerID, statusreports.subject, statusreports.applicationID, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, TIME_FORMAT(startTime,'%%k:%%i') as startTime, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate, TIME_FORMAT(endTime,'%%k:%%i') as endTime, statusreports.magicTicket, statusreports.wrm, statusreports.maintenanceNotifID, statusreports.notes, statusreports.actionItems, statusreports.reportTypeID, applications.application, customers.customer, employees.displayName, reporttypes.reportType FROM statusreports, applications, customers, employees, reporttypes WHERE statusReportID = $varStatusReport_rsStatusReport AND statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.employeeID=employees.employeeID AND statusreports.reporttypeID=reporttypes.reporttypeID";
	$rsStatusReport = $conn->query($query_rsStatusReport);
	$row_rsStatusReport = $rsStatusReport->fetch_assoc();
	$totalRows_rsStatusReport = $rsStatusReport->num_rows;
}

if ($my_get['function'] == "add") {
	$varMaintenance_rsMaint = "1";
	if (isset($my_get['maintenance'])) {
		$varMaintenance_rsMaint = addslashes($my_get['maintenance']);
	}
	$query_rsMaint = "SELECT TIME_FORMAT(startTime,'%%k:%%i') as startTime, DATE_FORMAT(startDate,'%%m/%%d/%%Y') as startDate, startDate as startDateRaw, maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.prodChanges, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours, maintenancenotifs.estimatedMinutes, employees.displayName FROM maintenancenotifs, employees WHERE maintenanceNotifsID = $varMaintenance_rsMaint AND maintenancenotifs.employeeID=employees.employeeID";
	$rsMaint = $conn->query($query_rsMaint);
	$row_rsMaint = $rsMaint->fetch_assoc();
	$totalRows_rsMaint = $rsMaint->num_rows;

	if (isset($my_get['supportRequest'])) {
		$varEscalation_rsSupportRequest = addslashes($my_get['supportRequest']);
		$query_rsSupportRequest = "SELECT escalations.escalationID, escalations.applicationID, applications.application, escalations.categoryID, reporttypes.reportType, escalations.subject, escalations.assignedTo, employees.displayName, escalations.ticket, escalations.customerID, customers.customer FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reportTypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE escalations.escalationID = $varEscalation_rsSupportRequest";
		$rsSupportRequest = $conn->query($query_rsSupportRequest);
		$row_rsSupportRequest = $rsSupportRequest->fetch_assoc();
		$totalRows_rsSupportRequest = $rsSupportRequest->num_rows;
	}
}

$query_rsCustomers = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCustomers = $conn->query($query_rsCustomers);
$row_rsCustomers = $rsCustomers->fetch_assoc();
$totalRows_rsCustomers = $rsCustomers->num_rows;

$query_rsApplications = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApplications = $conn->query($query_rsApplications);
$row_rsApplications = $rsApplications->fetch_assoc();
$totalRows_rsApplications = $rsApplications->num_rows;

$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
$rsReportTypes = $conn->query($query_rsReportTypes);
$row_rsReportTypes = $rsReportTypes->fetch_assoc();
$totalRows_rsReportTypes = $rsReportTypes->num_rows;

$query_rsEmployees = "SELECT employeeID, engineer, displayName FROM employees WHERE engineer = 'y' AND active = 't' ORDER BY displayName ASC";
$rsEmployees = $conn->query($query_rsEmployees);
$row_rsEmployees = $rsEmployees->fetch_assoc();
$totalRows_rsEmployees = $rsEmployees->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Status Report: " . stripslashes($row_rsStatusReport['subject'])); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="../inc/global.css" rel="stylesheet" type="text/css" />
		<link href="../inc/menu.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
		<script type="text/javascript" src="../inc/js/menu.js"></script>
		<script type="text/javascript" src="../inc/js/js.js"></script>
		<script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>
	</head>
	<body>
		<?php buildMenu(); ?>
		<script type="text/javascript">
			dolphintabs.init("menunav", 3);
		</script>
		<!-- <iframe src="statusReportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
		<div class="casing" align="left">
			<?php buildHeader("statusReport", "Status Reports", "statusReport", "View Status Report", "Add Status Report"); ?>
			<form action="statusReportSend.php" method="post" name="statusReportForm" id="statusReportForm">
				<table class="viewLarge" cellpadding="2" cellspacing="0">
					<tr>
						<td width="125" class="contrast"><?php makeLabel("startDate", "Start Date:"); ?></td>
						<td colspan="2" valign="top" nowrap="nowrap"><?php
							if ($my_get['function'] != "view") {
								echo (isset($my_get['maintenance'])) ? "<input name=\"userStartDate\" type=\"text\" id=\"userStartDate\" value=\"{$row_rsMaint['startDate']}\" "
										  . "size=\"10\" maxlength=\"10\" disabled=\"disabled\" readonly=\"{$row_rsMaint['startDate']}\" /><input type=\"hidden\" name=\"startDate\""
										  . " id=\"startDate\" value=\"{$row_rsMaint['startDateRaw']}\" />" : "<script>\nDateInput('startDate', true, 'YYYY-MM-DD');\n</script>";
							} else {
								echo $row_rsStatusReport['startDate'];
							}
							?></td>
						<td width="102" class="contrast"><?php makeLabel("endDate", "End Date:"); ?></td>
						<td valign="top">							
							<?php
							echo ($my_get['function'] != "view") ? "<script>\nDateInput('endDate', true, 'YYYY-MM-DD');\n</script>" : $row_rsStatusReport['endDate'];
							?>
						</td>
					</tr>
					<tr>
						<td class="contrast"><?php makeLabel("startTime", "Start Time:"); ?></td>
						<td colspan="2" valign="top">
							<?php
							echo ($my_get['function'] != "view") ? "<input type=\"text\" name=\"startHour\" id=\"startHour\" size=\"2\" maxlength=\"2\" />:<input name=\"startMinute\" type=\"text\" id=\"startMinute\" size=\"2\" maxlength=\"2\" /> <strong>UTC</strong>\n" : $row_rsStatusReport['startTime'];
							?>
						</td>
						<td class="contrast"><?php makeLabel("endTime", "End Time:"); ?></td>
						<td valign="top">
							<?php if ($my_get['function'] != "view") { ?>
								<input name="endHour" type="text" id="endHour" value="<?php echo date('H'); ?>" size="4" maxlength="5" />:<input name="endMinute" type="text" id="endMinute" value="<?php echo date('i'); ?>" size="2" maxlength="2" /> <strong>UTC</strong>
								<?php
								requiredField();
							} else {
								echo $row_rsStatusReport['endTime'];
							}
							?>
						</td>
					</tr>
					<tr class="spacer"><td colspan="5"></td></tr>
					<tr>
						<td class="contrast"><?php makeLabel("subject", "Subject:"); ?></td>
						<td colspan="4" valign="top">							
							<?php
							echo ($my_get['function'] != "view") ? "<input type=\"text\" name=\"subject\" id=\"subject\" size=\"100\" maxlength=\"255\" />\n" : $row_rsStatusReport['subject'];
							?>
						</td>
					</tr>
					<tr>
						<td class="contrast"><?php makeLabel("magic", "Ticket #:"); ?></td>
						<td colspan="2" valign="top">
							<?php
							if ($my_get['function'] != "view") {
								?>
								<input type="text" name="magic" id="magic" size="10" maxlength="20" />
								<?php
								requiredField();
							} else {
								if ($row_rsStatusReport['magicTicket'] == "0") {
									echo "-";
								} elseif ($row_rsStatusReport['magicTicket'] != "0") {
									echo $row_rsStatusReport['magicTicket'];
								}
							}
							?>
						</td>
						<td class="contrast"><?php makeLabel("wrm", "Case #:"); ?></td>
						<td class="icon">
							<?php
							if ($my_get['function'] != "view") {
								?>
								<input type="text" name="wrm" id="wrm" size="10" maxlength="20" />
								<?php
								requiredField();
							} else {
								if ($row_rsStatusReport['wrm'] == "0") {
									echo "-";
								} else {
									echo "<a href=\"https://na2.salesforce.com/search/SearchResults?searchType=1&amp;sbstr=" . $row_rsStatusReport['wrm'] . "&amp;search=+Go%21+\">" . $row_rsStatusReport['wrm'] . "</a>";
								}
							}
							?></td>
					</tr>
					<tr class="spacer"><td colspan="5"></td></tr>
					<tr>
						<td class="contrast"><?php makeLabel("customers", "Customer:"); ?></td>
						<td colspan="4" class="icon">
							<?php
							if ($my_get['function'] != "view") {
								?>
								<select name="customers" id="customers">
									<option value="">Select Customer</option>
									<?php
									while ($row_rsCustomers = $rsCustomers->fetch_assoc()) {
										echo "<option value='{$row_rsCustomers['customerID']}'>{$row_rsCustomers['customer']}</option>\n";
									}
									?>
								</select>
								<?php requiredField(); ?>&nbsp;<?php
								//adminComponent("../factSheets/customer.php", "Add a customer", "add", "825", "650");
							} else {
								echo $row_rsStatusReport['customer'];
							}
							?></td>
					</tr>
					<tr>
						<td class="contrast"><?php makeLabel("reportType", "Report Type:"); ?></td>
						<td colspan="4" valign="middle">
							<?php if ($my_get['function'] != "view") { ?>
								<select name="reportType" id="reportType">
									<option value="">Select Report Type</option>
									<?php
									while ($row_rsReportTypes = $rsReportTypes->fetch_assoc()) {
										echo "<option value='{$row_rsReportTypes['reportTypeID']}'" . ((isset($my_get['supportRequest']) && !(strcmp($row_rsReportTypes['reportTypeID'], $row_rsSupportRequest['categoryID']))) ? ' selected="selected"' : '' ) . ">{$row_rsReportTypes['reportType']}</option>\n";
									}
									?>
								</select>
								<?php
								requiredField();
								sudoAuth("../common/reportTypeAdd", "Add a Report Type", "add");
							} else {
								echo $row_rsStatusReport['reportType'];
							}
							?></td>
					</tr>
					<tr>
						<td class="contrast"><?php makeLabel("app", "Application:"); ?></td>
						<td colspan="4" valign="middle">
							<?php if ($my_get['function'] != "view") { ?>
								<select name="app" id="app">
									<option value="">Select Application</option>
									<?php
									while ($row_rsApplications = $rsApplications->fetch_assoc()) {
										echo "<option value='{$row_rsApplications['applicationID']}'" . ((isset($my_get['supportRequest'])) && (!(strcmp($row_rsApplications['applicationID'], $row_rsSupportRequest['applicationID']))) ? ' selected="selected"' : '') . ">{$row_rsApplications['application']}</option>\n";
									}
									?>
								</select>
								<?php
								requiredField();
								sudoAuth("../common/appAdd", "Add an Application", "add");
							} else {
								echo $row_rsStatusReport['application'];
							}
							?></td>
					</tr>
					<tr>
						<td class="contrast"><?php makeLabel("engineer", "Engineer:"); ?></td>
						<td colspan="4" valign="middle">
							<?php if ($my_get['function'] != "view") { ?>
								<select name="engineer" id="engineer">
									<option value="">Select Engineer</option>
									<?php
									while ($row_rsEmployees = $rsEmployees->fetch_assoc()) {
										echo "<option value='{$row_rsEmployees['employeeID']}'>{$row_rsEmployees['displayName']}</option>\n";
									}
									?>
								</select><?php
								requiredField();
								sudoAuth("../common/employeeAdd", "Add an Employee", "add");
							} else {
								echo $row_rsStatusReport['displayName'];
							}
							?></td>
					</tr>
					<tr class="spacer"><td colspan="5"></td></tr>
					<tr>
						<td valign="top" class="contrast"><?php makeLabel("notes", "Notes:"); ?></td>
						<td valign="top" colspan="4">
							<?php if ($my_get['function'] != "view") { ?>
								<textarea name="notes" id="notes" cols="82" rows="8" wrap="virtual"><?php echo (isset($my_get['maintenance'])) ? stripslashes($row_rsMaint['prodChanges']) : ''; ?></textarea>
								<?php
							} else {
								echo stripslashes(nl2br($row_rsStatusReport['notes']));
							}
							?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="contrast"><?php makeLabel("actionItems", "Action Items:"); ?></td>
						<td valign="top" colspan="4">
							<?php if ($my_get['function'] != "view") { ?>
								<textarea name="actions" id="actions" cols="82" rows="8" wrap="virtual"></textarea><?php
							} else {
								echo stripslashes(nl2br($row_rsStatusReport['actionItems']));
							}
							?></td>
					</tr>
					<tr><td colspan="5" class="recipients"><br />Email Recipients</td></tr>
					<tr>
						<td valign="middle" class="contrast"><strong>To:</strong></td>
						<td width="200" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="prodOps" id="prodOps" value="y" checked="checked"/>&nbsp;US ProdOps</label></td>
						<td width="200" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="noc" id="noc" value="y"/>&nbsp;US NOC</label></td>
						<td valign="middle" align="right"><label for="cc">CC:</label></td>
						<td valign="middle"><input type="text" name="cc" id="cc" size="40" /></td>
					</tr>
					<tr>
						<td rowspan="3" bgcolor="#7EABCD"></td>
						<td valign="middle" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="syseng" id="syseng" value="y"/>&nbsp;SysEng</label></td>
						<td valign="middle" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="projMan" id="projMan" value="y" />&nbsp;Project Management</label></td>
					</tr>
					<tr>
						<td valign="middle"><label title="rflow@markssystems.com"><input type="checkbox" name="neteng" id="neteng" value="y" />&nbsp;NetEng</label></td>
						<td valign="middle" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="dev" id="dev" value="y" />&nbsp;US Development</label></td>
					</tr>
					<tr>
						<td valign="middle" nowrap="nowrap"><label title="rflow@markssystems.com"><input type="checkbox" name="newLaunch" id="newLaunch" value="y" />&nbsp;Carrier Launch</label></td>
						<td valign="middle"><label title="rflow@markssystems.com"><input type="checkbox" name="sui" id="sui" value="y" />&nbsp;SUI</label></td>
					</tr>
					<tr class="button"><td colspan="5"><br /><input type="submit" name="submit" id="submit" value="Send Status Report" /><?php sentSuccessful("Status Report sent successfully!"); ?></td></tr>
				</table>
				<?php if ($my_get['function'] != "add") {
					?><input type="hidden" name="statusReport" id="statusReport" value="<?php echo $my_get['statusReport'] ?>" /><?php
				} else {
					echo "<input type=\"hidden\" name=\"MM_insert\" value=\"statusReportAdd\" />";

					if (isset($my_get['project'])) {
						?><input type="hidden" name="module" value="<?php echo $my_get['module']; ?>" />
						<input type="hidden" name="project" value="<?php echo $my_get['project']; ?>" />
						<input type="hidden" name="projectEvent" value="<?php echo $my_get['projectEvent']; ?>" /><?php
					}
				}
				?>
			</form>
			<?php buildFooter("0"); ?>
		</div>
	</body>
</html>