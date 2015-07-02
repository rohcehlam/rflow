<?php require_once('../Connections/connProdOps.php'); ?>
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

if ((isset($_GET['function'])) && ($_GET['function'] == "add")) {
	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "leaveTypeAdd")) {
	  $insertSQL = sprintf("INSERT INTO leavetypes (leaveTypeID, leaveType, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['leaveTypeID'], "int"),
                       GetSQLValueString($_POST['leaveType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

	  mysql_select_db($database_connProdOps, $connProdOps);
	  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

	  $insertGoTo = "leaveTypeAdd.php?success=y";
	  if (isset($_SERVER['QUERY_STRING'])) {
	    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
	    $insertGoTo .= $_SERVER['QUERY_STRING'];
	  }
	  header(sprintf("Location: %s", $insertGoTo));
	}
} elseif ((isset($_GET['function'])) && ($_GET['function'] == "update")) {
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "leaveTypeAdd")) {
	  $updateSQL = sprintf("UPDATE leavetypes SET leaveType=%s, notes=%s WHERE leaveTypeID=%s",
                       GetSQLValueString($_POST['leaveType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['leaveTypeID'], "int"));

	  mysql_select_db($database_connProdOps, $connProdOps);
	  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

	  $updateGoTo = "leaveTypeAdd.php?leaveType=" . $row_rsLeaveTypes['leaveTypeID'] . "";
	  if (isset($_SERVER['QUERY_STRING'])) {
	    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
	    $updateGoTo .= $_SERVER['QUERY_STRING'];
	  }
	  header(sprintf("Location: %s", $updateGoTo));
	}
}

$colname_rsLeaveTypes = "1";
if (isset($_GET['leaveType'])) {
  $colname_rsLeaveTypes = (get_magic_quotes_gpc()) ? $_GET['leaveType'] : addslashes($_GET['leaveType']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsLeaveTypes = sprintf("SELECT leaveTypeID, leaveType, notes FROM leavetypes WHERE leaveTypeID = %s", $colname_rsLeaveTypes);
$rsLeaveTypes = mysql_query($query_rsLeaveTypes, $connProdOps) or die(mysql_error());
$row_rsLeaveTypes = mysql_fetch_assoc($rsLeaveTypes);
$totalRows_rsLeaveTypes = mysql_num_rows($rsLeaveTypes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Add a Leave Type</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<?php require_once('../inc/functions.php'); ?>
	<?php require_once('../inc/js/js.php'); ?>
</head>
<body>
<?php 
if ((isset($_GET['function'])) && ($_GET['function'] == "add")) {
	buildHeader("leaveType", "Leave Types", "leaveType", "Add Leave Type", null);
} elseif ((isset($_GET['function'])) && ($_GET['function'] == "update")) {
	buildHeader("leaveType", "Leave Types", "leaveType", "Update Leave Type", "Add Leave Type");
} elseif ((isset($_GET['function'])) && ($_GET['function'] == "view")) {
	buildHeader("leaveType", "Leave Types", "leaveType", "View Leave Type", "Add Leave Type");
}
?>
<div align="center">
<div class="casing"><br />
<form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="leaveTypeAdd" id="leaveTypeAdd">
  <table class="<?php if ((isset($_GET['function'])) && ($_GET['function'] == "add")) {
				echo "add";
			} elseif ((isset($_GET['function'])) && ($_GET['function'] == "update")) {
				echo "update";
			} else {
				echo "view";
			} ?>" align="center" cellspacing="0">
    <tr>
      <td align="right" valign="middle" nowrap class="contrast"><label for="leaveType">Leave Type:</label></td>
      <td><?php if ((isset($_GET['function'])) && (($_GET['function'] == "add") || ($_GET['function'] == "update"))) {
			echo "<input type=\"text\" name=\"leaveType\" id=\"leaveType\" value=\"";
		}
		if ((isset($_GET['function'])) && ($_GET['function'] != "add")) {
			echo $row_rsLeaveTypes['leaveType'];
		}
		if ((isset($_GET['function'])) && (($_GET['function'] == "add") || ($_GET['function'] == "update"))) {
			echo "\" size=\"32\" />";
		}
		?></td>
    </tr>
    <tr>
      <td align="right" valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php if ((isset($_GET['function'])) && (($_GET['function'] == "add") || ($_GET['function'] == "update"))) {
	  		echo "<textarea name=\"notes\" id=\"notes\" cols=\"50\" rows=\"5\" wrap=\"VIRTUAL\">";
		}
		echo $row_rsLeaveTypes['notes'];
		if ((isset($_GET['function'])) && (($_GET['function'] == "add") || ($_GET['function'] == "update"))) {
			echo "</textarea>";
		} ?></td>
    </tr>
    <tr>
      <td colspan="2" class="button"><?php 
		if ((isset($_GET['function'])) && (($_GET['function'] == "add") || ($_GET['function'] == "update"))) { ?>
			<input name="submit" type="submit" id="submit" value="<?php if ((isset($_GET['function'])) && ($_GET['function'] == "add")) {
				echo "Add ";
			} elseif ((isset($_GET['function'])) && ($_GET['function'] == "update")) {
				echo "Update ";
			} 
			echo "Leave Type";
			?>" />
		<?php } ?><?php 
		if ((isset($_GET['function'])) && ($_GET['function'] == "add")) {
			sentSuccessful("Leave Type added successfully!");
		} elseif ((isset($_GET['function'])) && ($_GET['function'] == "update")) {
			sentSuccessful("Leave Type updated successfully!");
		}
		?></td>
    </tr>
  </table><?php if ((isset($_GET['function'])) && ($_GET['function'] != "add")) {
		echo "<input type=\"hidden\" name=\"leaveTypeID\" value=\"" . $row_rsLeaveTypes['leaveTypeID'] . "\" />";
	} ?>
  <input type="hidden" name="MM_insert" value="leaveTypeAdd" />
  <input type="hidden" name="MM_update" value="leaveTypeAdd">
</form><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsLeaveTypes);
?>