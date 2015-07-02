<?php require_once('../Connections/connProdOps.php'); ?>
<?php require_once('../inc/functions.php'); ?><?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE employees SET engineer=%s, firstName=%s, lastName=%s, displayName=%s, extension=%s, title=%s, cellPhone=%s, homePhone=%s, homeStreet=%s, homeCity=%s, homeStateID=%s, homeZip=%s, aimHandle=%s, yahooHandle=%s, msnHandle=%s, jabberHandle=%s, officeID=%s, workEmail=%s, personalEmail=%s, departmentID=%s, notes=%s, groupID=%s, password=%s WHERE employeeID=%s",
                       GetSQLValueString(isset($_POST['engineer']) ? "true" : "", "defined","'Y'","'N'"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['displayName'], "text"),
                       GetSQLValueString($_POST['extension'], "text"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['cellPhone'], "text"),
                       GetSQLValueString($_POST['homePhone'], "text"),
                       GetSQLValueString($_POST['homeStreet'], "text"),
                       GetSQLValueString($_POST['homeCity'], "text"),
                       GetSQLValueString($_POST['homeState'], "int"),
                       GetSQLValueString($_POST['homeZip'], "text"),
                       GetSQLValueString($_POST['aimHandle'], "text"),
                       GetSQLValueString($_POST['yahooHandle'], "text"),
                       GetSQLValueString($_POST['msnHandle'], "text"),
                       GetSQLValueString($_POST['jabberHandle'], "text"),
                       GetSQLValueString($_POST['office'], "int"),
                       GetSQLValueString($_POST['workEmail'], "text"),
                       GetSQLValueString($_POST['personalEmail'], "text"),
                       GetSQLValueString($_POST['department'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['group'], "int"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['employeeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "employees.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsEmployees = "1";
if (isset($_GET['employee'])) {
  $colname_rsEmployees = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = sprintf("SELECT * FROM employees WHERE employeeID = %s", $colname_rsEmployees);
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsStates = "SELECT * FROM states ORDER BY `state` ASC";
$rsStates = mysql_query($query_rsStates, $connProdOps) or die(mysql_error());
$row_rsStates = mysql_fetch_assoc($rsStates);
$totalRows_rsStates = mysql_num_rows($rsStates);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsOffices = "SELECT officeID, officeName FROM offices ORDER BY officeName ASC";
$rsOffices = mysql_query($query_rsOffices, $connProdOps) or die(mysql_error());
$row_rsOffices = mysql_fetch_assoc($rsOffices);
$totalRows_rsOffices = mysql_num_rows($rsOffices);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDepartment = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartment = mysql_query($query_rsDepartment, $connProdOps) or die(mysql_error());
$row_rsDepartment = mysql_fetch_assoc($rsDepartment);
$totalRows_rsDepartment = mysql_num_rows($rsDepartment);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGroups = "SELECT groupID, `group` FROM groups ORDER BY `group` ASC";
$rsGroups = mysql_query($query_rsGroups, $connProdOps) or die(mysql_error());
$row_rsGroups = mysql_fetch_assoc($rsGroups);
$totalRows_rsGroups = mysql_num_rows($rsGroups);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Update an Employee</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php buildHeader("employees", "Employees", "employeesUpdate", "Update Employee Information", true, "Add an employee"); ?>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center">
    <tr valign="baseline">
      <td align="right"><label>Name:</label></td>
      <td><input name="firstName" type="text" value="<?php echo $row_rsEmployees['firstName']; ?>" size="20" maxlength="255" />
      <input name="lastName" type="text" value="<?php echo $row_rsEmployees['lastName']; ?>" size="20" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right" nowrap="nowrap"><label for="displayName">Display Name:</label></td>
      <td><input type="text" name="displayName" id="displayName" value="<?php echo $row_rsEmployees['displayName']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="title">Title:</label></td>
      <td><input type="text" name="title" id="title" value="<?php echo $row_rsEmployees['title']; ?>" size="32" maxlength="255" />&nbsp;
	  		<label><input type="checkbox" name="engineer" id="engineer" value=""  <?php if (!(strcmp($row_rsEmployees['engineer'],"y"))) {echo "checked";} ?> />&nbsp;Engineer</label></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="extension">Extension:</label></td>
      <td><input type="text" name="extension" id="extension" value="<?php echo $row_rsEmployees['extension']; ?>" size="8" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="cellPhone">Mobile:</label></td>
      <td><input type="text" name="cellPhone" id="cellPhone" value="<?php echo $row_rsEmployees['cellPhone']; ?>" size="20" maxlength="25" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="workEmail">Email:</label></td>
      <td><input type="text" name="workEmail" id="workEmail" value="<?php echo $row_rsEmployees['workEmail']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td align="right"><u>Home</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td>&nbsp;</td>
      <td><input name="homeStreet" type="text" value="<?php echo $row_rsEmployees['homeStreet']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td>&nbsp;</td>
      <td><input type="text" name="homeCity" id="homeCity" value="<?php echo $row_rsEmployees['homeCity']; ?>" size="20" maxlength="255" />,
        <select name="homeState" id="homeState">
          <?php do { ?>
          <option value="<?php echo $row_rsStates['stateID']?>" <?php if (!(strcmp($row_rsStates['stateID'], "$row_rsEmployees[homeStateID]"))) {echo "SELECTED";} ?>><?php echo $row_rsStates['state']?></option>
          <?php } while ($row_rsStates = mysql_fetch_assoc($rsStates)); ?>
        </select>
        <input type="text" name="homeZip" id="homeZip" value="<?php echo $row_rsEmployees['homeZip']; ?>" size="10" maxlength="5" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="homePhone">Phone:</label></td>
      <td><input type="text" name="homePhone" id="homePhone" value="<?php echo $row_rsEmployees['homePhone']; ?>" size="20" maxlength="25" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="personalEmail">Email:</label></td>
      <td><input type="text" name="personalEmail" id="personalEmail" value="<?php echo $row_rsEmployees['personalEmail']; ?>" size="32" maxlength="255" /></td>
	</tr>
    <tr valign="baseline">
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td align="right"><u>IM</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="aimHandle">AIM:</label></td>
      <td><input type="text" name="aimHandle" id="aimHandle" value="<?php echo $row_rsEmployees['aimHandle']; ?>" size="32" maxlength="255" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="yahooHandle">Yahoo!:</label></td>
      <td><input type="text" name="yahooHandle" id="yahooHandle" value="<?php echo $row_rsEmployees['yahooHandle']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="msnHandle">MSN:</label></td>
      <td><input type="text" name="msnHandle" id="msnHandle" value="<?php echo $row_rsEmployees['msnHandle']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="jabberHandle">Jabber:</label></td>
      <td><input type="text" name="jabberHandle" id="jabberHandle" value="<?php echo $row_rsEmployees['jabberHandle']; ?>" size="32" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
	</tr>
    <tr valign="baseline">
      <td align="right"><u>Other</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="office">Office:</label></td>
      <td><select name="office" id="office">
        <?php do { ?>
        <option value="<?php echo $row_rsOffices['officeID']?>" <?php if (!(strcmp($row_rsOffices['officeID'], "$row_rsEmployees[officeID]"))) {echo "SELECTED";} ?>><?php echo $row_rsOffices['officeName']?></option>
        <?php } while ($row_rsOffices = mysql_fetch_assoc($rsOffices)); ?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="department">Department:</label></td>
      <td><select name="department" id="department">
          <?php do { ?>
          <option value="<?php echo $row_rsDepartment['departmentID']?>" <?php if (!(strcmp($row_rsDepartment['departmentID'], "$row_rsEmployees[departmentID]"))) {echo "SELECTED";} ?>><?php echo $row_rsDepartment['department']?></option>
          <?php } while ($row_rsDepartment = mysql_fetch_assoc($rsDepartment)); ?>
        </select>
      </td>
    <tr valign="baseline">
      <td align="right"><label for="group">Group:</label></td>
      <td><select name="group" id="group">
        <?php do { ?>
        <option value="<?php echo $row_rsGroups['groupID']?>" <?php if (!(strcmp($row_rsGroups['groupID'], "$row_rsEmployees[groupID]"))) {echo "SELECTED";} ?>><?php echo $row_rsGroups['group']?></option>
        <?php } while ($row_rsGroups = mysql_fetch_assoc($rsGroups)); ?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top"><label for="notes">Notes:</label></td>
      <td><textarea name="notes" id="notes" cols="50" rows="5" wrap="VIRTUAL"><?php echo $row_rsEmployees['notes']; ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td align="right"><label for="password">Password:</label></td>
      <td><input type="password" name="password" id="password" value="<?php echo $row_rsEmployees['password']; ?>" size="20" maxlength="15" /></td>
    </tr>
    <tr valign="baseline">
      <td colspan="2" class="button"><input name="submit" type="submit" id="submit" value="Update employee"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="employeeID" value="<?php echo $row_rsEmployees['employeeID']; ?>" />
</form>
</body>
</html><?php
mysql_free_result($rsEmployees);
mysql_free_result($rsStates);
mysql_free_result($rsOffices);
mysql_free_result($rsDepartment);
mysql_free_result($rsGroups);
?>