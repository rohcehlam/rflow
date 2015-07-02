<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();

if ($_GET['function'] != "add") {
	$varStatusReport_rsStatusReport = "1";
	if (isset($_GET['statusReport'])) {
	  $varStatusReport_rsStatusReport = (get_magic_quotes_gpc()) ? $_GET['statusReport'] : addslashes($_GET['statusReport']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsStatusReport = sprintf("SELECT statusreports.statusReportID, statusreports.employeeID, statusreports.customerID, statusreports.subject, statusreports.applicationID, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, TIME_FORMAT(startTime,'%%k:%%i') as startTime, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate, TIME_FORMAT(endTime,'%%k:%%i') as endTime, statusreports.magicTicket, statusreports.wrm, statusreports.maintenanceNotifID, statusreports.notes, statusreports.actionItems, statusreports.reportTypeID, applications.application, customers.customer, employees.displayName, reporttypes.reportType FROM statusreports, applications, customers, employees, reporttypes WHERE statusReportID = %s AND statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.employeeID=employees.employeeID AND statusreports.reporttypeID=reporttypes.reporttypeID", GetSQLValueString($varStatusReport_rsStatusReport, "int"));
	$rsStatusReport = mysql_query($query_rsStatusReport, $connProdOps) or die(mysql_error());
	$row_rsStatusReport = mysql_fetch_assoc($rsStatusReport);
	$totalRows_rsStatusReport = mysql_num_rows($rsStatusReport);
}

if ($_GET['function'] == "add") {
	$varMaintenance_rsMaint = "1";
	if (isset($_GET['maintenance'])) {
	  $varMaintenance_rsMaint = (get_magic_quotes_gpc()) ? $_GET['maintenance'] : addslashes($_GET['maintenance']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsMaint = sprintf("SELECT TIME_FORMAT(startTime,'%%k:%%i') as startTime, DATE_FORMAT(startDate,'%%m/%%d/%%Y') as startDate, startDate as startDateRaw, maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.prodChanges, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours, maintenancenotifs.estimatedMinutes, employees.displayName FROM maintenancenotifs, employees WHERE maintenanceNotifsID = %s AND maintenancenotifs.employeeID=employees.employeeID", $varMaintenance_rsMaint);
	$rsMaint = mysql_query($query_rsMaint, $connProdOps) or die(mysql_error());
	$row_rsMaint = mysql_fetch_assoc($rsMaint);
	$totalRows_rsMaint = mysql_num_rows($rsMaint);

	if (isset($_GET['supportRequest'])) {
		$varEscalation_rsSupportRequest = "1";
		$varEscalation_rsSupportRequest = (get_magic_quotes_gpc()) ? $_GET['supportRequest'] : addslashes($_GET['supportRequest']);
		mysql_select_db($database_connProdOps, $connProdOps);
		$query_rsSupportRequest = sprintf("SELECT escalations.escalationID, escalations.applicationID, applications.application, escalations.categoryID, reporttypes.reportType, escalations.subject, escalations.assignedTo, employees.displayName, escalations.ticket, escalations.customerID, customers.customer FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reportTypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE escalations.escalationID = %s", $varEscalation_rsSupportRequest);
		$rsSupportRequest = mysql_query($query_rsSupportRequest, $connProdOps) or die(mysql_error());
		$row_rsSupportRequest = mysql_fetch_assoc($rsSupportRequest);
		$totalRows_rsSupportRequest = mysql_num_rows($rsSupportRequest);
	}
}

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCustomers = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCustomers = mysql_query($query_rsCustomers, $connProdOps) or die(mysql_error());
$row_rsCustomers = mysql_fetch_assoc($rsCustomers);
$totalRows_rsCustomers = mysql_num_rows($rsCustomers);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApplications = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApplications = mysql_query($query_rsApplications, $connProdOps) or die(mysql_error());
$row_rsApplications = mysql_fetch_assoc($rsApplications);
$totalRows_rsApplications = mysql_num_rows($rsApplications);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
$rsReportTypes = mysql_query($query_rsReportTypes, $connProdOps) or die(mysql_error());
$row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);
$totalRows_rsReportTypes = mysql_num_rows($rsReportTypes);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employeeID, engineer, displayName FROM employees WHERE engineer = 'y' AND active = 't' ORDER BY displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Status Report: ". stripslashes($row_rsStatusReport['subject'])); ?></title>
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
dolphintabs.init("menunav", 3)
</script>
<!-- <iframe src="statusReportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
<div class="casing" align="left">
<?php buildHeader("statusReport","Status Reports","statusReport","View Status Report","Add Status Report"); ?>
<form action="statusReportSend.php" method="post" name="statusReportForm" id="statusReportForm">
  <table class="viewLarge" cellpadding="2" cellspacing="0">
	<tr>
		<td width="125" class="contrast"><?php makeLabel("startDate","Start Date:"); ?></td>
		<td colspan="2" valign="top" nowrap="nowrap"><?php if ($_GET['function'] != "view") {
			if (isset($_GET['maintenance'])) {
				?><input name="userStartDate" type="text" id="userStartDate" value="<?php echo $row_rsMaint['startDate']; ?>" size="10" maxlength="10" disabled="disabled" readonly="<?php echo $row_rsMaint['startDate']; ?>" /><input type="hidden" name="startDate" id="startDate" value="<?php echo $row_rsMaint['startDateRaw']; ?>" /><?php
			} else {
				?><script>DateInput('startDate', true, 'YYYY-MM-DD')</script><?php
			}
		} else {
			echo $row_rsStatusReport['startDate'];
		} ?></td>
		<td width="102" class="contrast"><?php makeLabel("endDate","End Date:"); ?></td>
		<td valign="top"><?php if ($_GET['function'] != "view") {
			?><script>DateInput('endDate', true, 'YYYY-MM-DD')</script><?php
		} else {
			echo $row_rsStatusReport['endDate'];
		} ?></td>
	</tr>
	<tr>
		<td class="contrast"><?php makeLabel("startTime","Start Time:"); ?></td>
		<td colspan="2" valign="top"><?php if ($_GET['function'] != "view") {
			?><input type="text" name="startHour" id="startHour" size="2" maxlength="2" />:<input name="startMinute" type="text" id="startMinute" size="2" maxlength="2" /> <strong>UTC</strong><?php requiredField(); ?><?php
		} else {
			echo $row_rsStatusReport['startTime'];
		} ?></td>
		<td class="contrast"><?php makeLabel("endTime","End Time:"); ?></td>
		<td valign="top"><?php if ($_GET['function'] != "view") {
			?><input name="endHour" type="text" id="endHour" value="<?php
	$now = getdate();
	$hour = $now['hours'];
	$DST = true;
	if ($DST == true) {
		$hourAdj = $hour;
	} else {
		$hourAdj = $hour;
	}
	if ($hourAdj < 10) {
		echo "0" . $hourAdj;
	} else {
		echo $hourAdj;
	}
?>" size="2" maxlength="2" />:<input name="endMinute" type="text" id="endMinute" value="<?php
	  $time = getdate();
	  $min = $time['minutes'];
	  if ($min < 10) {
	  	echo "0" . $min;
	  } else {
	  	echo $min;
	  }
?>" size="2" maxlength="2" /> <strong>UTC</strong><?php requiredField();
		} else {
			echo $row_rsStatusReport['endTime'];
		} ?></td>
	</tr>
	<tr class="spacer"><td colspan="5"></td></tr>
	<tr>
		<td class="contrast"><?php makeLabel("subject","Subject:"); ?></td>
		<td colspan="4" valign="top"><?php if ($_GET['function'] != "view") {
			?><input type="text" name="subject" id="subject"<?php if (isset($_GET['maintenance'])) {	  																					} ?> size="100" maxlength="255" /><?php requiredField();
		} else {
			echo $row_rsStatusReport['subject'];
		} ?></td>
	</tr>
	<tr>
		<td class="contrast"><?php makeLabel("magic","Ticket #:"); ?></td>
		<td colspan="2" valign="top"><?php if ($_GET['function'] != "view") {
			?><input type="text" name="magic" id="magic"<?php if (isset($_GET['supportRequest'])) {							} ?> size="10" maxlength="20" /><?php requiredField();
		} else {
			if ($row_rsStatusReport['magicTicket'] == "0") {
				echo "-";
			} elseif ($row_rsStatusReport['magicTicket'] != "0") {
				echo $row_rsStatusReport['magicTicket'];
			}
		} ?></td>
		<td class="contrast"><?php makeLabel("wrm","Case #:"); ?></td>
		<td class="icon"><?php if ($_GET['function'] != "view") {
			?><input type="text" name="wrm" id="wrm" size="10" maxlength="20" /><?php requiredField(); ?><?php
		} else {
			if ($row_rsStatusReport['wrm'] == "0") {
				echo "-";
			} else {
				echo "<a href=\"https://na2.salesforce.com/search/SearchResults?searchType=1&amp;sbstr=" . $row_rsStatusReport['wrm'] . "&amp;search=+Go%21+\">" . $row_rsStatusReport['wrm'] . "</a>";
			}
		} ?></td>
	</tr>
	<tr class="spacer"><td colspan="5"></td></tr>
	<tr>
            <td class="contrast"><?php makeLabel("customers","Customer:"); ?></td>
            <td colspan="4" class="icon"><?php if ($_GET['function'] != "view") {
            ?><select name="customers" id="customers">
            <option value="">Select Customer</option>
            <?php
            do {
            ?>
                <option value="<?php echo $row_rsCustomers['customerID']?>"<?php if (isset($_GET['supportRequest'])) {				} ?>><?php echo $row_rsCustomers['customer']?></option>
                <?php
            } while ($row_rsCustomers = mysql_fetch_assoc($rsCustomers));
            $rows = mysql_num_rows($rsCustomers);
            if($rows > 0) {
                mysql_data_seek($rsCustomers, 0);
                $row_rsCustomers = mysql_fetch_assoc($rsCustomers);
            } ?>
            </select>
                <?php requiredField(); ?>&nbsp;<?php //adminComponent("../factSheets/customer.php", "Add a customer", "add", "825", "650");
		} else {
                    echo $row_rsStatusReport['customer'];
		} ?></td>
	</tr>
	<tr>
		<td class="contrast"><?php makeLabel("reportType","Report Type:"); ?></td>
		<td colspan="4" valign="middle"><?php if ($_GET['function'] != "view") {
			?><select name="reportType" id="reportType">
        <option value="">Select Report Type</option>
        <?php
do {
?>
        <option value="<?php echo $row_rsReportTypes['reportTypeID']?>"<?php if (isset($_GET['supportRequest'])) {
																				if (!(strcmp($row_rsReportTypes['reportTypeID'], $row_rsSupportRequest['categoryID']))) {echo " selected=\"selected\"";}
																			} ?>><?php echo $row_rsReportTypes['reportType']?></option>
        <?php
} while ($row_rsReportTypes = mysql_fetch_assoc($rsReportTypes));
  $rows = mysql_num_rows($rsReportTypes);
  if($rows > 0) {
      mysql_data_seek($rsReportTypes, 0);
	  $row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);
  }
?>
      </select><?php requiredField();
	  	sudoAuth("../common/reportTypeAdd", "Add a Report Type", "add");
	  	} else {
			echo $row_rsStatusReport['reportType'];
		} ?></td>
	</tr>
	<tr>
		<td class="contrast"><?php makeLabel("app","Application:"); ?></td>
		<td colspan="4" valign="middle"><?php if ($_GET['function'] != "view") {
			?><select name="app" id="app">
        <option value="">Select Application</option>
        <?php
do {
?>
        <option value="<?php echo $row_rsApplications['applicationID']?>"<?php if (isset($_GET['supportRequest'])) {
																					if (!(strcmp($row_rsApplications['applicationID'], $row_rsSupportRequest['applicationID']))) {echo " selected=\"selected\"";}
																				} ?>><?php echo $row_rsApplications['application']?></option>
        <?php
} while ($row_rsApplications = mysql_fetch_assoc($rsApplications));
  $rows = mysql_num_rows($rsApplications);
  if($rows > 0) {
      mysql_data_seek($rsApplications, 0);
	  $row_rsApplications = mysql_fetch_assoc($rsApplications);
  }
?>
        </select><?php requiredField();
			sudoAuth("../common/appAdd", "Add an Application", "add");
		} else {
			echo $row_rsStatusReport['application'];
		} ?></td>
	</tr>
	<tr>
		<td class="contrast"><?php makeLabel("engineer","Engineer:"); ?></td>
		<td colspan="4" valign="middle"><?php if ($_GET['function'] != "view") {
			?><select name="engineer" id="engineer">
        <option value="">Select Engineer</option>
        <?php
do {
?>
        <option value="<?php echo $row_rsEmployees['employeeID']?>"<?php if (isset($_GET['supportRequest'])) {				} ?>><?php echo $row_rsEmployees['displayName']?></option>
        <?php
} while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees));
  $rows = mysql_num_rows($rsEmployees);
  if($rows > 0) {
      mysql_data_seek($rsEmployees, 0);
	  $row_rsEmployees = mysql_fetch_assoc($rsEmployees);
  }
?>
	  </select><?php requiredField();
	  		sudoAuth("../common/employeeAdd", "Add an Employee", "add");
		} else {
			echo $row_rsStatusReport['displayName'];
		} ?></td>
	</tr>
	<tr class="spacer"><td colspan="5"></td></tr>
	<tr>
		<td valign="top" class="contrast"><?php makeLabel("notes","Notes:"); ?></td>
		<td valign="top" colspan="4"><?php if ($_GET['function'] != "view") {
			?><textarea name="notes" id="notes" cols="82" rows="8" wrap="virtual"><?php if (isset($_GET['maintenance'])) { echo stripslashes($row_rsMaint['prodChanges']); } ?></textarea><?php
		} else {
			echo stripslashes(nl2br($row_rsStatusReport['notes']));
		} ?></td>
	</tr>
	<tr>
		<td valign="top" class="contrast"><?php makeLabel("actionItems","Action Items:"); ?></td>
		<td valign="top" colspan="4"><?php if ($_GET['function'] != "view") {
			?><textarea name="actions" id="actions" cols="82" rows="8" wrap="virtual"></textarea><?php
	  	} else {
			echo stripslashes(nl2br($row_rsStatusReport['actionItems']));
		} ?></td>
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
<?php if ($_GET['function'] != "add") {
	?><input type="hidden" name="statusReport" id="statusReport" value="<?php echo $_GET['statusReport'] ?>" /><?php
} else {
	echo "<input type=\"hidden\" name=\"MM_insert\" value=\"statusReportAdd\" />";

	if (isset($_GET['project'])) {
		?><input type="hidden" name="module" value="<?php echo $_GET['module']; ?>" />
		<input type="hidden" name="project" value="<?php echo $_GET['project']; ?>" />
		<input type="hidden" name="projectEvent" value="<?php echo $_GET['projectEvent']; ?>" /><?php
	}
} ?>
</form>
<?php buildFooter("0"); ?>
</div>
</body>
</html><?php
mysql_free_result($rsStatusReport);
mysql_free_result($rsMaint);
if (isset($_GET['supportRequest'])) {
	mysql_free_result($rsSupportRequest);
}
mysql_free_result($rsCustomers);
mysql_free_result($rsApplications);
mysql_free_result($rsReportTypes);
mysql_free_result($rsEmployees);
?>