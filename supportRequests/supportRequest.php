<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();
if ($_GET['function'] != "add") {
	//Support Request
	$varEscalation_rsEscalations = "1";
	if (isset($_GET['supportRequest'])) {
	  $varEscalation_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['supportRequest'] : addslashes($_GET['supportRequest']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsEscalations = sprintf("SELECT escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') AS dateEscalated, DATE_FORMAT(targetDate, '%%m/%%d/%%Y') AS targetDate, TIME_FORMAT(timeEscalated,'%%k:%%i') AS timeEscalated, escalations.submittedBy, employees1.displayName AS escalator, escalations.applicationID, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, escalations.assignedTo, employees2.displayName AS receiver, escalations.status, escalations.ticket, escalations.priority, escalations.description, escalations.recreateSteps, escalations.whatWasTested, escalations.customerImpact, escalations.logs, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') AS dateClosed, TIME_FORMAT(timeClosed,'%%k:%%i') AS timeClosed, escalations.addInfo, escalations.outcome, escalations.deptID, departments.department, escalations.customerID, customers.customer FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN (employees AS employees1, employees AS employees2) ON (escalations.submittedBy=employees1.employeeID AND escalations.assignedTo=employees2.employeeID) LEFT JOIN departments ON escalations.deptID=departments.departmentID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE escalations.escalationID = %s", $varEscalation_rsEscalations);
	$rsEscalations = mysql_query($query_rsEscalations, $connProdOps) or die(mysql_error());
	$row_rsEscalations = mysql_fetch_assoc($rsEscalations);
	$totalRows_rsEscalations = mysql_num_rows($rsEscalations);
}

//Employees
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employees.employeeID, employees.lastName, employees.displayName FROM employees ORDER BY employees.displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);

//Departments
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDepartments = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartments = mysql_query($query_rsDepartments, $connProdOps) or die(mysql_error());
$row_rsDepartments = mysql_fetch_assoc($rsDepartments);
$totalRows_rsDepartments = mysql_num_rows($rsDepartments);

//Applications
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApplication = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApplication = mysql_query($query_rsApplication, $connProdOps) or die(mysql_error());
$row_rsApplication = mysql_fetch_assoc($rsApplication);
$totalRows_rsApplication = mysql_num_rows($rsApplication);

//Categories
if (!isset($_SESSION['MM_Username'])) {
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes WHERE reportTypeID <> 15 ORDER BY reportType ASC";
	$rsCategories = mysql_query($query_rsCategories, $connProdOps) or die(mysql_error());
	$row_rsCategories = mysql_fetch_assoc($rsCategories);
	$totalRows_rsCategories = mysql_num_rows($rsCategories);
} else {
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
	$rsCategories = mysql_query($query_rsCategories, $connProdOps) or die(mysql_error());
	$row_rsCategories = mysql_fetch_assoc($rsCategories);
	$totalRows_rsCategories = mysql_num_rows($rsCategories);
}	

//Engineers
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsAssignedTo = "SELECT employeeID, displayName FROM employees WHERE engineer='y' AND active='t' ORDER BY displayName ASC";
$rsAssignedTo = mysql_query($query_rsAssignedTo, $connProdOps) or die(mysql_error());
$row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo);
$totalRows_rsAssignedTo = mysql_num_rows($rsAssignedTo);

//Customers
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCustomers = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCustomers = mysql_query($query_rsCustomers, $connProdOps) or die(mysql_error());
$row_rsCustomers = mysql_fetch_assoc($rsCustomers);
$totalRows_rsCustomers = mysql_num_rows($rsCustomers);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("a Support Request"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link href="../inc/menu.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/menu.js"></script>
	<script type="text/javascript" src="../inc/js/js.js"></script>
	<script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>
	<?php include_once("../inc/js/escalationsTabs.php"); ?>
</head>
<body>
<?php buildMenu(); ?>
<script type="text/javascript">
dolphintabs.init("menunav", 2)
</script>
<!-- <iframe src="supportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
<div class="casing" align="left">
<?php if ($_GET['function'] == "add") {
	buildHeader("supportRequest", "Support Requests", "supportRequest", "Add a Support Request", null);
} elseif ($_GET['function'] == "update") {
	buildHeader("supportRequest", "Support Requests", "supportRequest", "Update a Support Request", "Add a Support Request");
} else {
	buildHeader("supportRequest", "Support Requests", "supportRequest", "View a Support Request", "Add a Support Request");
} ?>
<form action="supportRequestSend.php" method="post" enctype="multipart/form-data" name="supportRequestForm" id="supportRequestForm" onsubmit="validateForm('submittedBy','Requested by','R','dept','Department','R','ticket','Ticket','R','subject','Subject','R','priority','Priority','R','customer','Customer','R','application','Application','R','category','Category','R','description','Description','R');return document.MM_returnValue">
<table class="viewLarge" align="center" cellspacing="0" cellpadding="2">
<?php if (($_GET['function'] == "update") || ($_GET['function'] == "view")) { ?>
    <tr valign="middle"><td colspan="4" class="title">Update Support Request</td></tr>	
    <tr>
	  <td class="contrast"><label>Updated on:</label></td>
	  <td colspan="2"><?php if ($_GET['function'] == "update") {
								?><script>DateInput('dateUpdated', true, 'YYYY-MM-DD')</script>at&nbsp;<input type="text" name="timeUpdated" id="timeUpdated" value="<?php 
	$now = getdate();
	$hour = $now['hours'];
	$min = $now['minutes'];
	$DST = true;
	//hours
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
	echo ":";
	//minutes
	if ($min < 10) {
		echo "0" . $min;
	} else {
		echo $min;
	}
?>" size="4" maxlength="5" /> UTC to <select name="status" id="status">
	      <option value="">Select Status</option>
	      <option value="Open"<?php if (!(strcmp("Open", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
	      <option value="Analysis"<?php if (!(strcmp("Analysis", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
	      <option value="Closed"<?php if (!(strcmp("Closed", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
	      <option value="In Progress"<?php if (!(strcmp("In Progress", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
	      <option value="On Hold"<?php if (!(strcmp("On Hold", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
	      <option value="Returned"<?php if (!(strcmp("Returned", $row_rsEscalations['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
	      </select>
					<?php } else {
							echo $row_rsEscalations['dateClosed'] . " at " . $row_rsEscalations['timeClosed'] . " UTC to " . $row_rsEscalations['status'];
							if (isset($_SESSION['MM_Username'])) {
								echo " <a class='btn btn-app' href=\"supportRequest.php?supportRequest=" . $_GET['supportRequest'] . "&amp;function=update\" class=\"update\"> Add Comment </a>";
							}
						} ?></td>
                <!--<td style="font-weight: bold; text-align:right;">Support Request&nbsp;#<?php //echo $row_rsEscalations['escalationID']; ?></td>-->
	</tr><tr>
		<td class="contrast"><label for="assignedTo">Engineer:</label></td>
		<td colspan="3"><?php if ($_GET['function'] == "update") { ?><select name="assignedTo" id="assignedTo">
		  <option value="">Select Engineer</option>
		  <?php
do {  
?>
		  <option value="<?php echo $row_rsAssignedTo['employeeID']?>"<?php if (!(strcmp($row_rsAssignedTo['employeeID'], $row_rsEscalations['assignedTo']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsAssignedTo['displayName']?></option>
		  <?php
} while ($row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo));
  $rows = mysql_num_rows($rsAssignedTo);
  if($rows > 0) {
      mysql_data_seek($rsAssignedTo, 0);
	  $row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo);
  }
?>
        </select><?php } else {
			echo $row_rsEscalations['receiver'];
		} ?></td>
	</tr>
	<tr>
	    <td valign="top" class="contrast"><label for="comments">Comments:</label></td>
	    <td colspan="3"><?php formField("textarea", "comments", $row_rsEscalations['outcome'], "77", null, "5", "virtual"); ?></td>
	</tr>
<?php if ($_GET['function'] == "update") { ?>
	<tr class="button"><td colspan="4"><input type="submit" name="update" id="update" value="Update Support Request" /></td></tr>
<?php } elseif(isset($_GET['sent'])) { ?>
	<tr class="button"><td colspan="4"><?php sentSuccessful("Support Request updated successfully!"); ?></td></tr>
<?php } ?>
	<tr class="spacer"><td colspan="4"></td></tr>
	<tr class="spacer"><td colspan="4"></td></tr>
	<tr valign="middle"><td colspan="4" class="title">Support Request #<?php echo $row_rsEscalations['escalationID']."  :  ".$row_rsEscalations['subject']; ?></td></tr>
<?php } //end if ($_GET['function'] == "update") ?>
	<tr valign="middle">
		<td width="18%" nowrap="nowrap" class="contrast"><label for="submittedBy">Requested by:</label></td>
		<td colspan="3"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="submittedBy" id="submittedBy">
		  <option value="">Select Employee</option>
		  <?php
do {  
?>
		  <option value="<?php echo $row_rsEmployees['employeeID']?>"<?php if (!(strcmp($row_rsEmployees['employeeID'], $row_rsEscalations['submittedBy']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsEmployees['displayName']?></option>
		  <?php
} while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees));
  $rows = mysql_num_rows($rsEmployees);
  if($rows > 0) {
      mysql_data_seek($rsEmployees, 0);
	  $row_rsEmployees = mysql_fetch_assoc($rsEmployees);
  }
?>
		    </select><?php 
							} else {
								echo stripslashes($row_rsEscalations['escalator']);
							} ?>&nbsp;on&nbsp;<?php 
				if ($_GET['function'] != "add") { //display this only when viewing/updating (i.e. don't let the user change these values unless in 'add' mode)
					echo $row_rsEscalations['dateEscalated'] . " at " . $row_rsEscalations['timeEscalated'] . " UTC";
				} else { ?>
					<script>DateInput('dateEscalated', true, 'YYYY-MM-DD')</script>at&nbsp;<input type="text" name="timeEscalated" id="timeEscalated" value="<?php 
	$now = getdate();
	$hour = $now['hours'];
	$min = $now['minutes'];
	$DST = true;
	//hours
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
	echo ":";
	//minutes
	if ($min < 10) {
		echo "0" . $min;
	} else {
		echo $min;
	}
?>" size="3" maxlength="5" />&nbsp;UTC<?php 
				} //end date/time else ?></td>
	</tr><tr>
		<td class="contrast"><label for="dept">Assign to Department:</label></td>
		<td width="39%"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="dept" id="dept">
          <option value="">Select Department</option>
		  <?php
do {  
?>
		  <option value="<?php echo $row_rsDepartments['departmentID']?>"<?php if (!(strcmp($row_rsDepartments['departmentID'], $row_rsEscalations['deptID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsDepartments['department']?></option>
		  <?php
} while ($row_rsDepartments = mysql_fetch_assoc($rsDepartments));
  $rows = mysql_num_rows($rsDepartments);
  if($rows > 0) {
      mysql_data_seek($rsDepartments, 0);
	  $row_rsDepartments = mysql_fetch_assoc($rsDepartments);
  }
?>
        </select><?php requiredField(); ?><?php 
							} else {
								echo $row_rsEscalations['department'];
							} ?></td>
		<td width="13%" class="contrast"><label for="ticket">Ticket:</label></td>
		<td width="30%"><?php formField("text", "ticket", $row_rsEscalations['ticket'], "10", "255", null, null, "7", "y"); ?></td>
	</tr><tr>
		<td class="contrast"><label for="subject">Subject:</label></td>
                <td colspan="3" style="font-weight:bold;"><?php formField("text", "subject", $row_rsEscalations['subject'], "85", "255", null, null, "8", "y"); ?></td>
	</tr>
	<tr>
	  <td class="contrast"><label for="targetDate">Target Date:</label></td>
	  <td><?php if ((isset($_GET['modTarget']) == "y") || ($_GET['function'] == "add")) {
												?><script>DateInput('targetDate', true, 'YYYY-MM-DD')</script>
	  <?php 
										} else {
												echo $row_rsEscalations['targetDate'] . "&nbsp;&nbsp;";
											if (isset($_SESSION['MM_Username'])) {
												echo "<a title=\"Update Target Date\" href=\"supportRequest.php?function=update&amp;modTarget=y&amp;";
												if ($_GET['category'] == "internal") {
													echo "category=internal&amp;";
												}
												echo "supportRequest=" . $_GET['supportRequest'] . "\">Update</a>";
											}
										} ?></td>
	  <td class="contrast" ><label for="priority">Priority:</label></td>
	  <td><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="priority" id="priority">
	    <option value="">Select Priority</option>
	    <option value="Fire"<?php if (!(strcmp("Fire", $row_rsEscalations['priority']))) {echo " selected=\"selected\"";} ?>>Fire</option>
	    <option value="Hot"<?php if (!(strcmp("Hot", $row_rsEscalations['priority']))) {echo " selected=\"selected\"";} ?>>Hot</option>
	    <option value="Medium"<?php if (!(strcmp("Medium", $row_rsEscalations['priority']))) {echo " selected=\"selected\"";} ?>>Medium</option>
	    <option value="Low"<?php if (!(strcmp("Low", $row_rsEscalations['priority']))) {echo " selected=\"selected\"";} ?>>Low</option>
	    </select><?php requiredField(); ?><?php 
							} else {
								echo $row_rsEscalations['priority'];
							} ?></td>
	</tr><tr>
		<td class="contrast"><label for="customerImpact">Customer Impact:</label></td>
		<td><?php formField("text", "customerImpact", $row_rsEscalations['customerImpact'], "45", "255", null, null); ?></td>
		<td class="contrast"><label for="customer">Customer:</label></td>
	    <td><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="customer" id="customer">
				<option value="">Select Customer</option>
	      <?php
do {  
?>
	      <option value="<?php echo $row_rsCustomers['customerID']?>"<?php if (!(strcmp($row_rsCustomers['customerID'], $row_rsEscalations['customerID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsCustomers['customer']?></option>
	      <?php
} while ($row_rsCustomers = mysql_fetch_assoc($rsCustomers));
  $rows = mysql_num_rows($rsCustomers);
  if($rows > 0) {
      mysql_data_seek($rsCustomers, 0);
	  $row_rsCustomers = mysql_fetch_assoc($rsCustomers);
  }
?>
	      </select><?php requiredField(); ?><?php 
							} else {
								echo $row_rsEscalations['customer'];
							} ?></td>
	</tr><tr>
		<td class="contrast"><label for="application">Application:</label></td>
		<td><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="application" id="application">
              <option value="">Select Application</option>
		  <?php
do {  
?>
		  <option value="<?php echo $row_rsApplication['applicationID']?>"<?php if (!(strcmp($row_rsApplication['applicationID'], $row_rsEscalations['applicationID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsApplication['application']?></option>
		  <?php
} while ($row_rsApplication = mysql_fetch_assoc($rsApplication));
  $rows = mysql_num_rows($rsApplication);
  if($rows > 0) {
      mysql_data_seek($rsApplication, 0);
	  $row_rsApplication = mysql_fetch_assoc($rsApplication);
  }
?>
		</select><?php requiredField(); ?><?php 
							} else {
								echo $row_rsEscalations['application'];
							} ?></td>
		<td class="contrast"><label for="category">Category:</label></td>
		<td><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
								?><select name="category" id="category">
              <option value="">Select Category</option>
		  <?php
do {  
?>
		  <option value="<?php echo $row_rsCategories['reportTypeID']; ?>"<?php if (!(strcmp($row_rsCategories['reportTypeID'], $row_rsEscalations['categoryID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsCategories['reportType']?></option>
		  <?php
} while ($row_rsCategories = mysql_fetch_assoc($rsCategories));
  $rows = mysql_num_rows($rsCategories);
  if($rows > 0) {
      mysql_data_seek($rsCategories, 0);
	  $row_rsCategories = mysql_fetch_assoc($rsCategories);
  }
?>
		</select><?php requiredField(); ?><?php 
							} else {
								echo $row_rsEscalations['category'];
							} ?></td>
	</tr><tr>
		<td class="contrast" valign="top"><label for="description">Description:</label></td>
		<td colspan="3"><?php formField("textarea", "description", $row_rsEscalations['description'], "77", null, "5", "virtual"); ?></td>
	</tr>
	<tr class="spacer"><td colspan="4"></td></tr>
<?php if (isset($_GET['category']) != "internal") { ?>
	<tr>
		<td valign="top" class="contrast"><label for="whatWasTested">Tests Performed:</label></td>
		<td colspan="3"><?php formField("textarea", "whatWasTested", $row_rsEscalations['whatWasTested'], "77", null, "5", "virtual"); ?></td>
	</tr><tr>
		<td class="contrast" valign="top"><label for="recreateSteps">Verification Steps:</label></td>
		<td colspan="3"><?php formField("textarea", "recreateSteps", $row_rsEscalations['recreateSteps'], "77", null, "5", "virtual"); ?></td>
	</tr><tr>
		<td valign="top" class="contrast"><label for="logs">Logs:</label></td>
		<td colspan="3"><?php formField("textarea", "logs", $row_rsEscalations['logs'], "77", null, "5", "virtual"); ?></td>
	</tr><tr>
		<td class="contrast" valign="top"><label for="addInfo">Additional Info:</label></td>
		<td colspan="3"><?php formField("textarea", "addInfo", $row_rsEscalations['addInfo'], "77", null, "5", "virtual"); ?></td>
	</tr>
<?php } else { ?>
	<tr>
		<td class="contrast" valign="top"><label for="addInfo">Additional Info:</label></td>
		<td colspan="3"><?php formField("textarea", "addInfo", $row_rsEscalations['addInfo'], "77", null, "5", "virtual"); ?></td>
	</tr>
<?php } ?>
<?php if ($_GET['function'] == "add") { ?>
	<tr class="button"><td colspan="4"><input type="submit" name="add" id="add" value="Submit Support Request" /><?php sentSuccessful("Support Request submitted successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if ($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="supportRequestAdd" />
	<input type="hidden" name="status" id="status" value="Open" />
<?php } elseif ($_GET['function'] == "update") { ?>
	<input type="hidden" name="MM_update" value="supportRequestUpdate" />
	<input type="hidden" name="supportRequestID" id="supportRequestID" value="<?php echo $_GET['supportRequest']; ?>" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsEmployees);
if ($_GET['function'] != "add") {
	mysql_free_result($rsEscalations);
}
mysql_free_result($rsApplication);
mysql_free_result($rsCategories);
mysql_free_result($rsAssignedTo);
mysql_free_result($rsDepartments);
mysql_free_result($rsCustomers);
?>