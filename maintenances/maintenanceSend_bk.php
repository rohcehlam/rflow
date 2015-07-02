<?php require_once('../Connections/connProdOps.php'); ?>
<?php
//var_dump($_REQUEST);

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "maintenanceNotif1")) {
  $insertSQL = sprintf("INSERT INTO maintenancenotifs (startDate, reason, customerImpact, nocImpact, prodChanges, employeeID, startTime, estimatedHours, estimatedMinutes) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       //GetSQLValueString2($_POST['startYear'] . $_POST['startMonth'] . $_POST['startDay'], "date"),
                       GetSQLValueString2($_POST['startDate'], "text"),
                       GetSQLValueString2($_POST['reason'], "text"),
                       GetSQLValueString2($_POST['customerImpact'], "text"),
                       GetSQLValueString2($_POST['nocImpact'], "text"),
                       GetSQLValueString2($_POST['prodChanges'], "text"),
                       GetSQLValueString2($_POST['engineer'], "int"),
                       GetSQLValueString2($_POST['startHour'] . $_POST['startMinute'] . "00", "int"),
                       GetSQLValueString2($_POST['estHours'], "int"),
                       GetSQLValueString2($_POST['estMins'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  ?>
    <script>
        alert("Maintenance Notification sent successfully!");
        setTimeout(function(){
            window.location.href = "maintenances.php";
        },0000);
    </script>    
  <?php 
  echo "Maintenance Notification sent successfully!";
  /*$insertGoTo = "maintenanceAdd.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
  
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Maintenance Notification..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
$varEngineer_rsGetUserFriendlyValues = "1";
if (isset($_POST['engineer'])) {
  $varEngineer_rsGetUserFriendlyValues = (get_magic_quotes_gpc()) ? $_POST['engineer'] : addslashes($_POST['engineer']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsGetUserFriendlyValues = sprintf("SELECT employees.displayName FROM employees WHERE employees.employeeID=%s", $varEngineer_rsGetUserFriendlyValues);
$rsGetUserFriendlyValues = mysql_query($query_rsGetUserFriendlyValues, $connProdOps) or die(mysql_error());
$row_rsGetUserFriendlyValues = mysql_fetch_assoc($rsGetUserFriendlyValues);
$totalRows_rsGetUserFriendlyValues = mysql_num_rows($rsGetUserFriendlyValues);

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

//$mail->SMTPDebug = true;
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
//$mail->AddReplyTo("karen@markssystems.com", "Maintenance Notifications");

$mail->WordWrap = 75;                                 // set word wrap to 50 characters
//$mail->AddAttachment("/var/tmp/file.tar.gz");         // add attachments
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // optional name
$mail->IsHTML(true);                                  // set email format to HTML

$body = "**************************<br />";
//$body .= "<b>Start Date:</b> " . $_POST['startMonth'] . "/" . $_POST['startDay'] . "/" . $_POST['startYear'] . "<br />";
$body .= "<b>Start Date:</b> " . $_POST['startDate'] . "<br />";
$body .= "<b>Start Time:</b> " . $_POST['startHour'] . ":" . $_POST['startMinute'] . "&nbsp;EST<br />";
$body .= "<b>Estimated Duration:</b> ";
				if ($_POST['estHours'] > 0) { 
					$body .= $_POST['estHours'] . "&nbsp;hour";
					if (($_POST['estHours'] != 01) || ($_POST['estHours'] != 1)) {
						$body .= "s";
					} 
					$body .= "&nbsp;";
				}
				if ($_POST['estMins'] > 0) { 
					$body .= $_POST['estMins'] . "&nbsp;minute";
					if ($_POST['estMins'] != 1) {
						$body .= "s";
					} 
					$body .= "&nbsp;";
				}
				$body .= "<br />";
$body .= "<b>Reason:</b> " . $_POST['reason'] . "<br />";
$body .= "<b>Customer Impact:</b> " . $_POST['custImpact'] . "<br />";
$body .= "<b>NOC Impact:</b> " . $_POST['nocImpact'] . "<br />";
$body .= "<b>Engineer:</b> " . $row_rsGetUserFriendlyValues['displayName'] . "<br />";
$body .= "<b>Production Changes:</b><br />";
$body .= nl2br($_POST['prodChanges']) . "<br />";
$body .= "**************************";

$txtbody = "**************************<br />";

$txtbody .= "<b>Start Date:</b> " . $_POST['startDate'] . "<br />";
//$txtbody .= "Start Date: " . $_POST['startMonth'] . "/" . $_POST['startDay'] . "/" . $_POST['startYear'] . "<br />";
$txtbody .= "Start Time: " . $_POST['startHour'] . ":" . $_POST['startMinute'] . "&nbsp;EST<br />";
$txtbody .= "Estimated Duration: ";
				if ($_POST['estHours'] > 0) { 
					$txtbody .= $_POST['estHours'] . "&nbsp;hour";
					if (($_POST['estHours'] != 01) || ($_POST['estHours'] != 1)) {
						$txtbody .= "s";
					} 
					$txtbody .= "&nbsp;";
				}
				if ($_POST['estMins'] > 0) { 
					$txtbody .= $_POST['estMins'] . "&nbsp;minute";
					if ($_POST['estMins'] != 1) {
						$txtbody .= "s";
					} 
					$txtbody .= "&nbsp;";
				}
				$txtbody .= "<br />";
$txtbody .= "Reason: " . $_POST['reason'] . "<br />";
$txtbody .= "Customer Impact: " . $_POST['custImpact'] . "<br />";
$txtbody .= "NOC Impact: " . $_POST['nocImpact'] . "<br />";
$txtbody .= "Engineer: " . $row_rsGetUserFriendlyValues['displayName'] . "<br />";
$txtbody .= "Production Changes:</b><br />";
$txtbody .= nl2br($_POST['prodChanges']) . "<br />";
$txtbody .= "**************************";

$subject = "Maintenance Notification (Internal): " . $_POST['reason'];

$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
   echo "This Maintenance Notification could not be sent successfully. Please check the errors below.<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

 echo "Maintenance Notification sent successfully!";
?>
</body>
</html><?php
mysql_free_result($rsGetUserFriendlyValues);
?>