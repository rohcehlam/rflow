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

if ((isset($_GET['carrier'])) && ($_GET['carrier'] != "") && (isset($_GET['confirm']))) {
  $deleteSQL = sprintf("DELETE FROM carriers WHERE carrierID=%s",
                       GetSQLValueString($_GET['carrier'], "int"));

  mysql_select_db($database_connCellPhones, $connCellPhones);
  $Result1 = mysql_query($deleteSQL, $connCellPhones) or die(mysql_error());

  $deleteGoTo = "carriers.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsCarriers = "1";
if (isset($_GET['carrier'])) {
  $colname_rsCarriers = (get_magic_quotes_gpc()) ? $_GET['carrier'] : addslashes($_GET['carrier']);
}
mysql_select_db($database_connCellPhones, $connCellPhones);
$query_rsCarriers = sprintf("SELECT * FROM carriers WHERE carrierID = %s", $colname_rsCarriers);
$rsCarriers = mysql_query($query_rsCarriers, $connCellPhones) or die(mysql_error());
$row_rsCarriers = mysql_fetch_assoc($rsCarriers);
$totalRows_rsCarriers = mysql_num_rows($rsCarriers);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Delete a Carrier</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once("../inc/nav.php"); ?>
<p><a href="../images/logos/mobile_365.gif">Home</a> &raquo; Delete a carrier</p>
<p>Are you sure you want to delete this carrier?</p>
<p><a title="Yes, delete this carrier" href="carriersDelete.php?carrier=<?php echo $row_rsCarriers['carrierID']; ?>&amp;confirm=y">Yes, delete it!</a></p>
<p><a title="No, do NOT delete this carrier" href="carriers.php">No, do NOT delete it!</a></p>
</body>
</html><?php
mysql_free_result($rsCarriers);
?>