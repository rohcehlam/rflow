<?php require_once('../Connections/connProdOps.php'); ?>
<?php
function GetSQLValueString2($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "maintenanceUpdate")) {
  $updateSQL = sprintf("UPDATE maintenancenotifs SET maintenancenotifs.status=%s WHERE maintenancenotifs.maintenanceNotifsID=%s",
                       GetSQLValueString2($_POST['status'], "text"),
                       GetSQLValueString2($_POST['maintenance'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "maintenance.php?maintenance=" . $_POST['maintenance'] . "&amp;sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsMaintenanceNotif = "1";
if (isset($_POST['maintenance'])) {
  $colname_rsMaintenanceNotif = (get_magic_quotes_gpc()) ? $_POST['maintenance'] : addslashes($_POST['maintenance']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsMaintenanceNotif = sprintf("SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact, maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%%k:%%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours, maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName FROM maintenancenotifs, employees WHERE maintenanceNotifsID = %s AND maintenancenotifs.employeeID=employees.employeeID", $colname_rsMaintenanceNotif);
$rsMaintenanceNotif = mysql_query($query_rsMaintenanceNotif, $connProdOps) or die(mysql_error());
$row_rsMaintenanceNotif = mysql_fetch_assoc($rsMaintenanceNotif);
$totalRows_rsMaintenanceNotif = mysql_num_rows($rsMaintenanceNotif);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Maintenance Notification..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
</head>
<body><?php
require_once("../inc/class.phpmailer.php");
require_once("../inc/class.smtp.php");
require_once("../inc/phpmailer.lang-en.php");

$mail = new PHPMailer();
$mail->IsSMTP();

$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com";
$mail->Port = "465";
$mail->Username = "mailer@markssystems.com";
$mail->Password = "M@ilerMF";
//$mail->AddReplyTo("mf-alerts@masflight.com");
$mail->From = "mailer@markssystems.com";
$mail->FromName = "Maintenance Notification";

if (($_POST['prodOps']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "ProdOps US");
}
if (($_POST['noc']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "Mobile365 NOC");
}
if (($_POST['neteng']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "NetEng");
}
if (($_POST['syseng']=='y')) {
	$mail->AddAddress("rflow@markssystems.com", "SysEng");
}
if ((isset($_POST['cc'])) && ($_POST['cc']!=null)) {
	$mail->AddAddress("" . $_POST['cc'] . "", "cc");
}
//$mail->AddAddress("ellen@example.com");                  // name is optional
//$mail->AddReplyTo("rflow@markssystems.com", "Maintenance Notifications");

$mail->WordWrap = 75;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

if ($_POST['status'] == "c") {
	$body = "This maintenance is complete. Please refer to the corresponding Status Report for additional details. If there are any questions or concerns, please contact the Maintenance Engineer or ProdOps On Call. Thank you.<br /><br />";
	$body .= "**************************<br />";
} elseif ($_POST['status'] == 'x') {
	$body = "This maintenance has been canceled. If there are any questions or concerns, please contact the Maintenance Engineer or ProdOps On Call. Thank you.<br /><br />";
	$body .= "**************************<br />";
} else {
	$body = "**************************<br />";
}
	$body .= "<b>Start Date:</b> " . $row_rsMaintenanceNotif['startDate'] . "<br />";
	$body .= "<b>Start Time:</b> " . $row_rsMaintenanceNotif['startTime'] . "&nbsp;EST<br />";
	$body .= "<b>Estimated Duration:</b> ";
				if ($row_rsMaintenanceNotif['estimatedHours'] > 0) { 
					$body .= $row_rsMaintenanceNotif['estimatedHours'] . "&nbsp;hour";
					if (($row_rsMaintenanceNotif['estimatedHours'] != 01) || ($row_rsMaintenanceNotif['estimatedHours'] != 1)) {
						$body .= "s";
					} 
					$body .= "&nbsp;";
				}
				if ($row_rsMaintenanceNotif['estimatedMinutes'] > 0) { 
					$body .= $row_rsMaintenanceNotif['estimatedMinutes'] . "&nbsp;minute";
					if ($row_rsMaintenanceNotif['estimatedMinutes']==1) {
						$body .= "s";
					} 
					$body .= "&nbsp;";
				}
				$body .= "<br />";
	$body .= "<b>Reason:</b> " . $row_rsMaintenanceNotif['reason'] . "<br />";
	$body .= "<b>Customer Impact:</b> " . $row_rsMaintenanceNotif['customerImpact'] . "<br />";
	$body .= "<b>NOC Impact:</b> " . $row_rsMaintenanceNotif['nocImpact'] . "<br />";
	$body .= "<b>Engineer:</b> " . $row_rsMaintenanceNotif['displayName'] . "<br />";
	$body .= "<b>Production Changes:</b><br />";
	$body .= nl2br($row_rsMaintenanceNotif['prodChanges']) . "<br />";
	$body .= "**************************";

if ($_POST['status'] == "c") {
	$txtbody = "This maintenance is complete. Please refer to the corresponding Status Report for additional details. If there are any questions or concerns, please contact the Maintenance Engineer or ProdOps On Call. Thank you.<br /><br />";	$txtbody .= "**************************<br />";
} elseif ($_POST['status'] == 'x') {
	$txtbody = "This maintenance has been canceled. If there are any questions or concerns, please contact the Maintenance Engineer or ProdOps On Call. Thank you.<br /><br />";
	$txtbody .= "**************************<br />";
} else {
	$txtbody = "**************************<br />";
}
	$txtbody .= "<b>Start Date:</b> " . $row_rsMaintenanceNotif['startDate'] . "<br />";
	$txtbody .= "<b>Start Time:</b> " . $row_rsMaintenanceNotif['startTime'] . "&nbsp;EST<br />";
	$txtbody .= "<b>Estimated Duration:</b> ";
				if ($row_rsMaintenanceNotif['estimatedHours'] > 0) { 
					$txtbody .= $row_rsMaintenanceNotif['estimatedHours'] . "&nbsp;hour";
					if (($row_rsMaintenanceNotif['estimatedHours'] != 01) || ($row_rsMaintenanceNotif['estimatedHours'] != 1)) {
						$txtbody .= "s";
					} 
					$txtbody .= "&nbsp;";
				}
				if ($row_rsMaintenanceNotif['estimatedMinutes'] > 0) { 
					$txtbody .= $row_rsMaintenanceNotif['estimatedMinutes'] . "&nbsp;minute";
					if ($row_rsMaintenanceNotif['estimatedMinutes']==1) {
						$txtbody .= "s";
					} 
					$txtbody .= "&nbsp;";
				}
				$txtbody .= "<br />";
	$txtbody .= "<b>Reason:</b> " . $row_rsMaintenanceNotif['reason'] . "<br />";
	$txtbody .= "<b>Customer Impact:</b> " . $row_rsMaintenanceNotif['customerImpact'] . "<br />";
	$txtbody .= "<b>NOC Impact:</b> " . $row_rsMaintenanceNotif['nocImpact'] . "<br />";
	$txtbody .= "<b>Engineer:</b> " . $row_rsMaintenanceNotif['displayName'] . "<br />";
	$txtbody .= "<b>Production Changes:</b><br />";
	$txtbody .= nl2br($row_rsMaintenanceNotif['prodChanges']) . "<br />";
	$txtbody .= "**************************";

if ($_POST['status'] == "c") {
	$subject = "Maintenance Notification (Internal): " . $row_rsMaintenanceNotif['reason'] . " **COMPLETE**";
} elseif ($_POST['status'] == "x") {
	$subject = "Maintenance Notification (Internal): " . $row_rsMaintenanceNotif['reason'] . " **CANCELED**";
} elseif ($_POST['status'] == "o") {
	$subject = "Maintenance Notification (Internal): " . $row_rsMaintenanceNotif['reason'];
}

$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
   echo "This Maintenance Notification could not be sent successfully. Please check the errors below .<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Maintenance Notification sent successfully!";
?>
</body>
</html><?php
mysql_free_result($rsMaintenanceNotif);
?>