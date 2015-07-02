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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO carriers (carrierID, carrier, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['carrierID'], "int"),
                       GetSQLValueString($_POST['carrier'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "carrierAdd.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Production Operations: Reporting - Add a Carrier</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<script src="../Connections/connProdOps.php" type="text/javascript"></script>
    <style type="text/css">
<!--
.style10 {font-family: Arial, Helvetica, sans-serif}
.style6 {color: #7F9358}
body,td,th {
	color: #666666;
}
body {
	background-color: #E0E0E0;
	margin-left: 0px;
	margin-top: 3px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style11 {
	color: #000000;
	font-weight: bold;
}
-->
    </style>
</head>
<body>
<table width="795" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="69" colspan="2" bgcolor="#6B6F7A"><div align="left"><img src="../images/logos/M365.jpg" alt="Mobile 365" width="795" height="170" /></div></td>
  </tr>
  <tr bgcolor="#D5EDB3">
    <script language="JavaScript" type="text/javascript">
//--------------- LOCALIZEABLE GLOBALS ---------------
var d=new Date();
var monthname=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
//Ensure correct for language. English is "January 1, 2004"
var TODAY = monthname[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();
//---------------   END LOCALIZEABLE   ---------------
</script>
    <td width="131" bordercolor="#D5EDB3"><span class="style10">
      <script language="JavaScript" type="text/javascript">
      document.write(TODAY);	</script>
    </span></span></td>
    <td width="684" height="37" bordercolor="#D5EDB3"><span class="style6"> <strong><a href="/index.html">Home</a> &raquo; <a href="../images/logos/M365.jpg">Status Reports</a> &raquo; Add a New Customer </strong></span></td>
  </tr>
</table>
<table width="795" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="806" bgcolor="#FFFFFF"><p>&nbsp;</p>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table align="center">
          <tr valign="baseline">
            <td width="91" align="right"><span class="style11">Carrier:</span></td>
            <td width="320" class="style6"><input type="text" name="carrier" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" valign="top"><span class="style11">Notes:</span></td>
            <td class="style6"><textarea name="notes" cols="50" rows="5" wrap="virtual"></textarea>            </td>
          </tr>
          <tr valign="baseline">
            <td colspan="2" class="button style6"><input name="add" type="submit" id="add" value="Add Carrier" /></td>
          </tr>
        </table>
        <input type="hidden" name="carrierID" value="" />
        <input type="hidden" name="MM_insert" value="form1" />
      </form></td>
  </tr>
  <tr>
    <td bgcolor="#7F9358">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>