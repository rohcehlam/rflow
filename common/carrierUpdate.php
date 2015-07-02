<?php require_once('../Connections/connProdOps.php'); ?>
<?php
session_start();
$MM_authorizedUsers = "Admin";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateCarrier")) {
  $updateSQL = sprintf("UPDATE carriers SET carrier=%s, notes=%s WHERE carrierID=%s",
                       GetSQLValueString($_POST['carrier'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['carrierID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "carriers.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsCarriers = "1";
if (isset($_GET['carrier'])) {
  $colname_rsCarriers = (get_magic_quotes_gpc()) ? $_GET['carrier'] : addslashes($_GET['carrier']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCarriers = sprintf("SELECT * FROM carriers WHERE carrierID = %s ORDER BY carrier ASC", $colname_rsCarriers);
$rsCarriers = mysql_query($query_rsCarriers, $connProdOps) or die(mysql_error());
$row_rsCarriers = mysql_fetch_assoc($rsCarriers);
$totalRows_rsCarriers = mysql_num_rows($rsCarriers);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Update a Carrier</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once("../inc/nav.php"); ?>
<p><a href="../images/logos/mobile_365.gif">Home</a> &raquo; Update Carrier </p>
<form method="post" name="updateCarrier" action="<?php echo $editFormAction; ?>">
  <table align="center">
    <tr valign="baseline">
      <td align="right">Carrier:</td>
      <td><input type="text" name="carrier" value="<?php echo $row_rsCarriers['carrier']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top">Notes:</td>
      <td>
        <textarea name="notes" cols="50" rows="5" wrap="VIRTUAL"><?php echo $row_rsCarriers['notes']; ?></textarea>
      </td>
    </tr>
    <tr valign="baseline" class="button">
      <td colspan="2"><input type="submit" value="Update Carrier" /></td>
    </tr>
  </table>
  <input type="hidden" name="carrierID" value="<?php echo $row_rsCarriers['carrierID']; ?>" />
  <input type="hidden" name="MM_update" value="updateCarrier" />
</form>
</body>
</html><?php
mysql_free_result($rsCarriers);
?>