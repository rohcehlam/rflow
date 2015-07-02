<?php require_once('../Connections/connProdOps.php'); ?>
<?php require_once('../inc/functions.php'); ?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addEmployee")) {
  $insertSQL = sprintf("INSERT INTO employees (employeeID, engineer, firstName, lastName, displayName, extension, title, cellPhone, homePhone, homeStreet, homeCity, homeStateID, homeZip, aimHandle, yahooHandle, msnHandle, jabberHandle, officeID, workEmail, personalEmail, departmentID, notes, groupID) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['employeeID'], "int"),
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
                       GetSQLValueString($_POST['group'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "employees.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsStates = "SELECT * FROM states ORDER BY `state` ASC";
$rsStates = mysql_query($query_rsStates, $connProdOps) or die(mysql_error());
$row_rsStates = mysql_fetch_assoc($rsStates);
$totalRows_rsStates = mysql_num_rows($rsStates);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDepartment = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartment = mysql_query($query_rsDepartment, $connProdOps) or die(mysql_error());
$row_rsDepartment = mysql_fetch_assoc($rsDepartment);
$totalRows_rsDepartment = mysql_num_rows($rsDepartment);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsOffice = "SELECT officeID, officeName FROM offices ORDER BY officeName ASC";
$rsOffice = mysql_query($query_rsOffice, $connProdOps) or die(mysql_error());
$row_rsOffice = mysql_fetch_assoc($rsOffice);
$totalRows_rsOffice = mysql_num_rows($rsOffice);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGroups = "SELECT groupID, `group` FROM groups ORDER BY `group` ASC";
$rsGroups = mysql_query($query_rsGroups, $connProdOps) or die(mysql_error());
$row_rsGroups = mysql_fetch_assoc($rsGroups);
$totalRows_rsGroups = mysql_num_rows($rsGroups);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<?php include_once('../inc/js/js.php'); ?>
</head>
<body>
<?php buildHeader("employee", "Employees", "employeeAdd", "Add an Employee", null); ?>
<div align="center">
<div class="casing">
<form method="post" name="addEmployee" action="<?php echo $editFormAction; ?>">
  <table class="add" align="center" cellspacing="0">
    <tr>
      <td align="right" class="contrast"><label>Name:</label></td>
      <td><input type="text" name="firstName" id="firstName" value="" size="20" maxlength="255" />
      <input type="text" name="lastName" id="lastName" value="" size="20" maxlength="255" /></td>
    </tr>
    <tr>
      <td align="right" nowrap="nowrap" class="contrast"><label for="displayName">Display Name:</label></td>
      <td><input type="text" name="displayName" id="displayName" value="" size="32" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="title">Title:</label></td>
      <td><input type="text" name="title" id="title" value="" size="32" maxlength="255" />&nbsp;<label><input type="checkbox" name="engineer" id="engineer" value="y" />&nbsp;Engineer</label></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="extension">Extension:</label></td>
      <td><input type="text" name="extension" id="extension" value="" size="8" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="cellPhone">Mobile:</label></td>
      <td><input type="text" name="cellPhone" id="cellPhone" value="" size="20" maxlength="25" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="workEmail">Email:</label></td>
      <td nowrap><input type="text" name="workEmail" id="workEmail" value="" size="32" maxlength="255" /></td>
    </tr>
    <tr class="contrast">
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td align="right"><u>Home</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="contrast">&nbsp;</td>
      <td><input type="text" name="homeStreet" id="homeStreet" value="" size="32" maxlength="255" /></td>
    </tr>
    <tr>
      <td class="contrast">&nbsp;</td>
      <td><input type="text" name="homeCity" id="homeCity" value="" size="20" maxlength="255" />,&nbsp;<select name="homeState" id="homeState">
          <?php do { ?>
          <option value="<?php echo $row_rsStates['stateID']?>" ><?php echo $row_rsStates['state']?></option>
          <?php } while ($row_rsStates = mysql_fetch_assoc($rsStates)); ?>
        </select>
          <input type="text" name="homeZip" id="homeZip" value="" size="10" maxlength="5" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="homePhone">Phone:</label></td>
      <td><input type="text" name="homePhone" id="homePhone" value="" size="20" maxlength="25" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="personalEmail">Email:</label></td>
      <td nowrap><input type="text" name="personalEmail" id="personalEmail" value="" size="32" /></td>
    </tr>
    <tr class="contrast">
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td align="right"><u>IM</u></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="aimHandle">AIM:</label></td>
      <td><input type="text" name="aimHandle" id="aimHandle" value="" size="32" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="yahooHandle">Yahoo!:</label></td>
      <td><input type="text" name="yahooHandle" id="yahooHandle" value="" size="32" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="msnHandle">MSN:</label></td>
      <td><input type="text" name="msnHandle" id="msnHandle" value="" size="32" /></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="jabberHandle">Jabber:</label></td>
      <td><input type="text" name="jabberHandle" id="jabberHandle" value="" size="32" /></td>
    </tr>
    <tr class="contrast">
      <td colspan="2">&nbsp;</td>
	</tr>
    <tr>
      <td align="right"><u>Other</u></td>
      <td>&nbsp;</td>
	</tr>
    <tr>
      <td align="right" class="contrast"><label for="office">Office:</label></td>
      <td><select name="office" id="office">
          <?php do { ?>
          <option value="<?php echo $row_rsOffice['officeID']?>" ><?php echo $row_rsOffice['officeName']?></option>
          <?php } while ($row_rsOffice = mysql_fetch_assoc($rsOffice)); ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="department">Department:</label></td>
      <td><select name="department" id="department">
          <?php do { ?>
          <option value="<?php echo $row_rsDepartment['departmentID']?>" ><?php echo $row_rsDepartment['department']?></option>
          <?php } while ($row_rsDepartment = mysql_fetch_assoc($rsDepartment)); ?>
        </select>
      </td>
    <tr>
      <td align="right" class="contrast"><label for="group">Group:</label></td>
      <td><select name="group" id="group">
      </select></td>
    </tr>
    <tr>
      <td align="right" valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><textarea name="notes" id="notes" cols="50" rows="5" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td colspan="2" class="button"><input type="submit" name="add" id="add" value="Add Employee" /></td>
    </tr>
  </table>
  <input type="hidden" name="employeeID" value="" />
  <input type="hidden" name="MM_insert" value="addEmployee" />
</form>
</div></div>
</body>
</html><?php
mysql_free_result($rsStates);
mysql_free_result($rsDepartment);
mysql_free_result($rsOffice);
mysql_free_result($rsGroups);
?>