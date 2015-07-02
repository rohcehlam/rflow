<?php require_once('../../Connections/connProdOps.php');
	require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "employeeAdd")) {
  $insertSQL = sprintf("INSERT INTO employees (engineer, firstName, lastName, displayName, extension, title, cellPhone, aimHandle, officeID, workEmail, departmentID, notes, groupID, password) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString(isset($_POST['engineer']) ? "true" : "", "defined","'Y'","'N'"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['displayName'], "text"),
                       GetSQLValueString($_POST['extension'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['cellPhone'], "text"),
                       GetSQLValueString($_POST['aimHandle'], "text"),
                       GetSQLValueString($_POST['office'], "int"),
                       GetSQLValueString($_POST['workEmail'], "text"),
                       GetSQLValueString($_POST['department'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['group'], "int"),
                       GetSQLValueString($_POST['password'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "employee.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "employeeUpdate")) {
  $updateSQL = sprintf("UPDATE employees SET engineer=%s, firstName=%s, lastName=%s, displayName=%s, extension=%s, title=%s, cellPhone=%s, aimHandle=%s, officeID=%s, workEmail=%s, departmentID=%s, notes=%s, groupID=%s, password=%s WHERE employeeID=%s",
                       GetSQLValueString(isset($_POST['engineer']) ? "true" : "", "defined","'Y'","'N'"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['displayName'], "text"),
                       GetSQLValueString($_POST['extension'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['cellPhone'], "text"),
                       GetSQLValueString($_POST['aimHandle'], "text"),
                       GetSQLValueString($_POST['office'], "int"),
                       GetSQLValueString($_POST['workEmail'], "text"),
                       GetSQLValueString($_POST['department'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['group'], "int"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['employeeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "employee.php?function=view&employee=" . $_POST['employeeID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ($_GET['function'] != "add") {
	//employee details
	$varEmployee_rsEmployees = "1";
	if (isset($_SESSION['employee'])) {
	  $varEmployee_rsEmployees = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
	} elseif (isset($_GET['employee'])) {
	  $varEmployee_rsEmployees = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsEmployees = sprintf("SELECT employees.employeeID, employees.engineer, employees.firstName, employees.lastName, employees.displayName, employees.extension, employees.title, employees.cellPhone, employees.homePhone, employees.aimHandle, employees.officeID, offices.officeName, employees.workEmail, employees.departmentID, departments.department, employees.notes, employees.groupID, groups.group, employees.password, employees.manager, employees.onCall, employees.hireDate FROM employees LEFT JOIN offices ON employees.officeID=offices.officeID LEFT JOIN departments ON employees.departmentID=departments.departmentID LEFT JOIN groups ON employees.groupID=groups.groupID WHERE employees.employeeID = %s", $varEmployee_rsEmployees);
	$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
	$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
	$totalRows_rsEmployees = mysql_num_rows($rsEmployees);
}

//offices
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsOffices = "SELECT officeID, officeName FROM offices ORDER BY officeName ASC";
$rsOffices = mysql_query($query_rsOffices, $connProdOps) or die(mysql_error());
$row_rsOffices = mysql_fetch_assoc($rsOffices);
$totalRows_rsOffices = mysql_num_rows($rsOffices);

//departments
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDepartment = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartment = mysql_query($query_rsDepartment, $connProdOps) or die(mysql_error());
$row_rsDepartment = mysql_fetch_assoc($rsDepartment);
$totalRows_rsDepartment = mysql_num_rows($rsDepartment);

//groups
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGroups = "SELECT groupID, `group` FROM groups ORDER BY `group` ASC";
$rsGroups = mysql_query($query_rsGroups, $connProdOps) or die(mysql_error());
$row_rsGroups = mysql_fetch_assoc($rsGroups);
$totalRows_rsGroups = mysql_num_rows($rsGroups);

if ($_GET['function'] == "view") {
	//maintenance notifications associated with this employee
	$varEmployee_rsEmployeeXMNs = "1";
	if (isset($_GET['employee'])) {
	  $varEmployee_rsEmployeeXMNs = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsEmployeeXMNs = sprintf("SELECT maintenanceNotifsID, reason, startTime, employeeID, startDate, status FROM maintenancenotifs WHERE employeeID = %s ORDER BY startDate ASC", $varEmployee_rsEmployeeXMNs);
	$rsEmployeeXMNs = mysql_query($query_rsEmployeeXMNs, $connProdOps) or die(mysql_error());
	$row_rsEmployeeXMNs = mysql_fetch_assoc($rsEmployeeXMNs);
	$totalRows_rsEmployeeXMNs = mysql_num_rows($rsEmployeeXMNs);
	
	//status reports associated with this employee
	$varEmployee_rsEmployeeXSRs = "1";
	if (isset($_GET['employee'])) {
	  $varEmployee_rsEmployeeXSRs = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsEmployeeXSRs = sprintf("SELECT statusReportID, subject, endTime, employeeID, endDate, endTime, maintenanceNotifID FROM statusreports WHERE employeeID=%s ORDER BY endDate, endTime ASC", $varEmployee_rsEmployeeXSRs);
	$rsEmployeeXSRs = mysql_query($query_rsEmployeeXSRs, $connProdOps) or die(mysql_error());
	$row_rsEmployeeXSRs = mysql_fetch_assoc($rsEmployeeXSRs);
	$totalRows_rsEmployeeXSRs = mysql_num_rows($rsEmployeeXSRs);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("an Employee"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
			buildHeaderNEW("employee", "Employees", "employee", "Add an Employee", null);
	} elseif ($_GET['function'] == "update") {
			buildHeaderNEW("employee", "Employees", "employee", "Update Employee Information", "Add an Employee");
	} else {
			buildHeaderNEW("employee", "Employees", "employee", "View an Employee", "Add an Employee");
	} ?>
<div align="center">
<div class="casing" align="left">
<form method="post" name="employeeForm" action="employee.php">
  <table class="<?php echo $_GET['function']; ?>" align="center" cellspacing="0" cellpadding="2">
    <tr>
      <td width="98" class="contrast"><label for="firstName">Name:</label></td>
      <td width="163"><?php formField("text", "firstName", $row_rsEmployees['firstName'], "15", "255", null, null); ?>&nbsp;<?php formField("text", "lastName", $row_rsEmployees['lastName'], "15", "255", null, null); ?></td>
    </tr>
    <tr>
      <td nowrap="nowrap" class="contrast"><label for="displayName">Display Name:</label></td>
      <td><?php formField("text", "displayName", $row_rsEmployees['displayName'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="title">Title:</label></td>
      <td nowrap="nowrap"><?php formField("text", "title", $row_rsEmployees['title'], "32", "255", null, null); ?>&nbsp;<?php if ($_GET['function'] == "update") { ?>
	  				<label><input type="checkbox" name="engineer" id="engineer" value=""<?php if (!(strcmp($row_rsEmployees['engineer'],"y"))) {echo " checked=\"checked\"";} ?> />&nbsp;Engineer</label>
			<?php } elseif (($_GET['function'] == "view") && ($row_rsEmployees['engineer'] == "Y")) {
					echo "Engineer";
				} ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="extension">Extension:</label></td>
      <td><?php formField("text", "extension", $row_rsEmployees['extension'], "8", "8", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="cellPhone">Mobile:</label></td>
      <td><?php formField("text", "cellPhone", $row_rsEmployees['cellPhone'], "20", "25", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="workEmail">Email:</label></td>
      <td><?php formField("text", "workEmail", $row_rsEmployees['workEmail'], "32", "255", null, null); ?></td>
    </tr>
    <tr class="spacer"><td colspan="2"></td></tr>
    <tr>
      <td align="right"><u>IM</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="contrast"><label for="aimHandle">AIM:</label></td>
      <td><?php formField("text", "aimHandle", $row_rsEmployees['aimHandle'], "32", "255", null, null); ?></td>
    </tr>
    <tr class="spacer"><td colspan="2"></td></tr>
    <tr>
      <td align="right"><u>Other</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="contrast"><label for="office">Office:</label></td>
      <td><?php if(($_GET['function'] == "update") || ($_GET['function'] == "add")) { ?><select name="office" id="office">
			<option value="">Select Office</option>
        	<?php do { ?>
        		<option value="<?php echo $row_rsOffices['officeID']; ?>"<?php if (!(strcmp($row_rsOffices['officeID'], "$row_rsEmployees[officeID]"))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsOffices['officeName']; ?></option>
        	<?php } while ($row_rsOffices = mysql_fetch_assoc($rsOffices)); ?>
		</select>
		<?php } else {
				echo $row_rsEmployees['officeName'];
			} ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="department">Department:</label></td>
      <td><?php if(($_GET['function'] == "update") || ($_GET['function'] == "add")) { ?><select name="department" id="department">
				<option value="">Select Department</option>
          <?php do { ?>
          <option value="<?php echo $row_rsDepartment['departmentID']?>"<?php if (!(strcmp($row_rsDepartment['departmentID'], "$row_rsEmployees[departmentID]"))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsDepartment['department']?></option>
          <?php } while ($row_rsDepartment = mysql_fetch_assoc($rsDepartment)); ?>
        </select>
		<?php } else {
				echo $row_rsEmployees['department'];
			} ?>
      </td>
<?php if ($row_rsEmployees['groupID'] == "1") { ?>    <tr>
      <td class="contrast"><label for="group">Group:</label></td>
      <td><?php if(($_GET['function'] == "update") || ($_GET['function'] == "add")) { ?><select name="group" id="group">
		<option value="">Select Group</option>
        <?php do { ?>
        <option value="<?php echo $row_rsGroups['groupID']?>"<?php if (!(strcmp($row_rsGroups['groupID'], "$row_rsEmployees[groupID]"))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsGroups['group']?></option>
        <?php } while ($row_rsGroups = mysql_fetch_assoc($rsGroups)); ?>
      </select>
  		<?php } else {
				echo $row_rsEmployees['group'];
			} ?></td>
    </tr>
<?php } ?>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsEmployees['notes'], "30", null, "5", "virtual"); ?></td>
    </tr>
<?php if (($_GET['function'] == "update") || ($_GET['function'] == "add")) { ?>
    <tr>
      <td class="contrast"><label for="password">Password:</label></td>
      <td><input type="password" name="password" id="password" value="<?php echo $row_rsEmployees['password']; ?>" size="20" maxlength="15" /></td>
    </tr>
<?php } ?>
<?php if($_GET['function'] == "add") { ?>
	<tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Employee" /></td></tr>
<?php } elseif($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Employee" /></td></tr>
<?php } ?>

  </table>
<?php if ($_GET['function'] == "add") { ?>
  <input type="hidden" name="MM_insert" value="employeeAdd" />
<?php } elseif ($_GET['function'] == "update") { ?>
  <input type="hidden" name="MM_update" value="employeeUpdate" />
  <input type="hidden" name="employeeID" value="<?php echo $row_rsEmployees['employeeID']; ?>" />
<?php } ?>
</form>
<?php if (($_GET['function'] == "view") && ($row_rsEmployeeXMNs > 0)) { ?>
<br />
<div align="center"><a href="../../maintenances/maintenances.php?function=view&amp;employee=<?php echo $row_rsEmployees['employeeID']; ?>">View Maintenance Notifications sent by <?php echo $row_rsEmployees['displayName']; ?></a></div>
<?php } ?>

<?php if (($_GET['function'] == "view") && ($row_rsEmployeeXSRs > 0)) { ?>
<br />
<div align="center"><a href="../../statusReports/statusReports.php?function=view&amp;employee=<?php echo $row_rsEmployees['employeeID']; ?>">Status Reports written by <?php echo $row_rsEmployees['displayName']; ?></a></div>
<?php } ?>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsEmployees);
mysql_free_result($rsOffices);
mysql_free_result($rsDepartment);
mysql_free_result($rsGroups);
mysql_free_result($rsEmployeeXMNs);
mysql_free_result($rsEmployeeXSRs);
?>