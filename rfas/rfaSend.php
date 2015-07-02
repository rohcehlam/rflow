<?php require_once('../Connections/connProdOps.php');
        require_once('../inc/functions.php'); ?><?php
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Support Request..</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body><?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "rfaAdd")) {
  $insertSQL = sprintf("INSERT INTO changerequests (comments,submittedBy, dateSubmitted, timeSubmitted, summary, `description`, applicationID, subapplicationID, layerID, status, requestOrigin, requestOriginID, risk, windowStartDate, windowStartTime, windowEndDate, windowEndTime) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['submittedBy'], "int"),
                       GetSQLValueString($_POST['dateSubmitted'], "date"),
                       GetSQLValueString($_POST['timeSubmitted'], "date"),
                       GetSQLValueString($_POST['summary'], "text"),
                       GetSQLValueString($_POST['description'], "text"),                       
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['subapplication'], "int"),
                       GetSQLValueString($_POST['layer'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['requestOrigin'], "text"),
                       GetSQLValueString($_POST['requestOriginID'], "text"),
                       GetSQLValueString($_POST['risk'], "text"),
                       GetSQLValueString($_POST['windowStartDate'], "date"),
                       GetSQLValueString($_POST['windowStartTime'], "date"),
                       GetSQLValueString($_POST['windowEndDate'], "date"),
                       GetSQLValueString($_POST['windowEndTime'], "date"));  
  echo $insertSQL;
  var_dump($_REQUEST);
  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "rfa.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "rfaUpdate") && (!isset($_POST['windowStartDate']))) {
  $updateSQL = sprintf("UPDATE changerequests SET summary=%s, `description`=%s, applicationID=%s, subapplicationID=%s, layerID=%s, status=%s, comments=%s, requestOrigin=%s, requestOriginID=%s, flagged=%s, risk=%s, reviewedBy=%s WHERE changeRequestID=%s",
                      GetSQLValueString($_POST['summary'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['subapplication'], "int"),
                       GetSQLValueString($_POST['layer'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['requestOrigin'], "text"),
                       GetSQLValueString($_POST['requestOriginID'], "text"),
                       GetSQLValueString($_POST['flagged'], "text"),
                       GetSQLValueString($_POST['risk'], "text"),
                       GetSQLValueString($_POST['reviewedBy'], "int"),
                       GetSQLValueString($_POST['changeRequestID'], "int"));
  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());
} elseif ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "rfaUpdate") && (isset($_POST['windowStartDate']))) {
  $updateSQL = sprintf("UPDATE changerequests SET summary=%s, `description`=%s, applicationID=%s, subapplicationID=%s, layerID=%s, status=%s, comments=%s, requestOrigin=%s, requestOriginID=%s, flagged=%s, windowStartDate=%s, windowStartTime=%s, windowEndDate=%s, windowEndTime=%s, risk=%s, reviewedBy=%s WHERE changeRequestID=%s",
                       GetSQLValueString($_POST['summary'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['application'], "int"),
                       GetSQLValueString($_POST['subapplication'], "int"),
                       GetSQLValueString($_POST['layer'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['requestOrigin'], "text"),
                       GetSQLValueString($_POST['requestOriginID'], "text"),
                       GetSQLValueString($_POST['flagged'], "text"),
                       GetSQLValueString($_POST['windowStartDate'], "date"),
                       GetSQLValueString($_POST['windowStartTime'], "date"),
                       GetSQLValueString($_POST['windowEndDate'], "date"),
                       GetSQLValueString($_POST['windowEndTime'], "date"),
                       GetSQLValueString($_POST['risk'], "text"),
                       GetSQLValueString($_POST['reviewedBy'], "int"),
                       GetSQLValueString($_POST['changeRequestID'], "int"));
  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());
  $updateGoTo = "rfa.php?function=view&rfa=" . $_POST['changeRequestID'] . "&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

global $lastID;
$lastID = mysql_insert_id();

if (isset($_POST['changeRequestID'])) {
  $varRFA_rsRFA = (get_magic_quotes_gpc()) ? $_POST['changeRequestID'] : addslashes($_POST['changeRequestID']);
} else {
        $varRFA_rsRFA = $lastID;
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsRFA = sprintf("SELECT changerequests.changeRequestID, employees.displayName as submittedBy, DATE_FORMAT(dateSubmitted, '%%m/%%d/%%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%%k:%%i') AS timeSubmitted, changerequests.summary, changerequests.description, changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%%m/%%d/%%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%%k:%%i') AS windowStartTime, DATE_FORMAT(windowEndDate, '%%m/%%d/%%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%%k:%%i') AS windowEndTime, changerequests.applicationID, applications.application, changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk, employees.workEmail FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = %s", GetSQLValueString($varRFA_rsRFA, "int"));
$rsRFA = mysql_query($query_rsRFA, $connProdOps) or die(mysql_error());
$row_rsRFA = mysql_fetch_assoc($rsRFA);
$totalRows_rsRFA = mysql_num_rows($rsRFA);

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
$mail->From = "mailer@markssystems.com";
$mail->FromName = "RFC Notification";
$mail->AddAddress("karen@markssystems.com", "RFCs - Change Management");
$mail->WordWrap = 75;                              
$mail->IsHTML(true);                                  

if ($_POST["MM_insert"] == "rfaAdd"){
    $body = "An RFC has been submitted, and is awaiting review. An overview of the RFC appears below.<br /><br />";
} elseif ($_POST["MM_update"] == "rfaUpdate"){
    $body = "RFC #" . $row_rsRFA['changeRequestID'] . " has been <b>" . $row_rsRFA['status'] . "</b>. An overview of the RFC appears below.<br />";
}
$body .= "You can view the RFC by visiting <a title=\"Production Operation's RFCs\" href=\"http://".$_SERVER['HTTP_HOST']."/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . "\">http://".$_SERVER['HTTP_HOST']."/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . "</a>.<br /><br />";
$body .= "<b>Submitted By:</b> ".$row_rsRFA['submittedBy']."<br />";
$body .= "<b>Subject:</b> " . stripslashes($row_rsRFA['summary']) . "<br />";
$body .= "<b>Application:</b> " . $row_rsRFA['application'] . "<br />";
$body .= "<b>Subapplication:</b> " . $row_rsRFA['subapplication'] . "<br />";
$body .= "<b>Layer:</b> " . $row_rsRFA['layer'] . "<br />";
$body .= "<b>Request Origin:</b> ";
if ($row_rsRFA['requestOrigin'] == "Support Request") {
    $body .= " <a href=\"http://".$_SERVER['HTTP_HOST']."/rflow_karen/supportRequests/supportRequest.php?function=view&amp;supportRequest=" . $row_rsRFA['requestOriginID'] . "\">";
}
$body .= "" . $row_rsRFA['requestOrigin']." #" . $row_rsRFA['requestOriginID'] . "</a><br />";
$body .= "<b>Window:</b>";
$body .= "<div style=\"margin-left: 15px;\">Starting: " . $row_rsRFA['windowStartDate'] . " at " . $row_rsRFA['windowStartTime'] . "<br />";
$body .= "Ending: " . $row_rsRFA['windowEndDate'] . " at " . $row_rsRFA['windowEndTime'] . "</div>";
$body .= "<b>Description:</b> " . stripslashes(nl2br($row_rsRFA['description'])) . "<br />";
$body .= "<b>Risk:</b> " . stripslashes(nl2br($row_rsRFA['risk'])) . "<br />";
if ($_POST['comments'] != null) {
    $body .= "<b>Comments:</b> " . stripslashes(nl2br($row_rsRFA['comments'])) . "<br />";
}
if ($_POST["MM_insert"] == "rfaAdd") {
    $txtbody = "An RFC has been submitted, and is awaiting review. An overview of the RFC appears below.<br /><br />";
} elseif ($_POST["MM_update"] == "rfaUpdate") {
    $txtbody = "RFC #" . $row_rsRFA['changeRequestID'] . " has been " . $row_rsRFA['status'] . ". An overview of the RFA appears below.<br />";
}
$txtbody .= "You can view the RFC by visiting http://".$_SERVER['HTTP_HOST']."/rflow_karen/rfas/rfa.php?function=view&amp;rfa=" . $row_rsRFA['changeRequestID'] . ".<br />";
$txtbody .= "You can login by visiting http://".$_SERVER['HTTP_HOST']."/rflow_karen/userPortals/index.php?ref=".$_SERVER['HTTP_HOST']."/rflow_karen/rfas/rfas.php.<br />";
$txtbody .= "You can also view the ProdOps US Calendar by visiting http://".$_SERVER['HTTP_HOST']."/rflow_karen/calendar/month.php.<br /><br />";
$txtbody .= "*********************";
$txtbody .= "Subject: " . stripslashes($row_rsRFA['summary']) . "<br />";
$txtbody .= "Application: " . $row_rsRFA['application'] . "<br />";
$txtbody .= "Subapplication: " . $row_rsRFA['subapplication'] . "<br />";
$txtbody .= "Layer: " . $row_rsRFA['layer'] . "<br />";
$txtbody .= "Request Origin: " . $row_rsRFA['requestOrigin'] . " #" . $row_rsRFA['requestOriginID'] . "<br />";
$txtbody .= "Window:";
$txtbody .= "Starting: " . $row_rsRFA['windowStartDate'] . " at " . $row_rsRFA['windowStartTime'] . "<br />";
$txtbody .= "Ending: " . $row_rsRFA['windowEndDate'] . " at " . $row_rsRFA['windowEndTime'] . "";
$txtbody .= "Description: " . stripslashes(nl2br($row_rsRFA['description'])) . "<br />";
$txtbody .= "Risk: " . stripslashes(nl2br($row_rsRFA['risk'])) . "<br />";
if ($_POST['comments'] != null) {
    $txtbody .= "Comments: " . stripslashes(nl2br($row_rsRFA['comments'])) . "<br />";
}
$subject = "RFC #".$row_rsRFA['changeRequestID']." - ".stripslashes($row_rsRFA['summary']);
if ($row_rsRFA['status'] != "Pending Approval") {
    $subject .= " **" . $row_rsRFA['status'] . "**";
}
$mail->Subject = $subject;
$mail->Body    = $body;
$mail->AltBody = $txtbody;

if(!$mail->Send()) {
   echo "This RFC could not be sent successfully. Please check the errors below .<br />";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
} else {
        echo "RFC sent successfully! If you can see this message.";
        $goTo = "rfa.php?function=add&sent=y";
        header(sprintf("Location: %s", $goTo));
}
?></body>
</html><?php
mysql_free_result($rsRFA);
?>
