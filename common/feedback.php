<?php require_once('../Connections/connProdOps.php'); 
require_once('../inc/functions.php'); 

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employeeID, displayName FROM employees ORDER BY displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Feedback</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/js.js"></script>
</head>
<body>
<?php buildHeader(null, null, "feedback", "Feedback", null); ?>
<div align="center">
<div class="casing" align="left">
<form method="post" name="feedback" action="feedbackSend.php">
<div style="text-align:left; margin-left: 115px;"><dt>Instructions</dt><dd>Below is a form to report a bug, request a feature, as well as share ideas,<br />suggestions, and recommendations. If you wish to remain anonymous, simply<br />select "Anonymous" from the drop-down box below. 
Thanks for your input!</dd></div><br />
  <table align="center" width="555" class="add" cellspacing="0">
    <tr valign="middle">
      <td width="71" class="contrast"><label for="employee">Name:</label></td>
      <td width="195"><select name="employee" id="employee">
	  	<option value="">Select Name</option>
        <?php do { ?>
        <option value="<?php echo $row_rsEmployees['employeeID']?>"><?php echo $row_rsEmployees['displayName']?></option>
        <?php } while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees)); ?>
      </select></td>
    <td width="273" align="right"><label for="sentOn">Today's Date: </label><input type="text" name="userSentOn" id="userSentOn" value="<?php
	$date = getdate();
	$month = $date['mon'];
	if ($month < 10) {
		echo "0" . $month;
	} else {
		echo $month;
	}
	echo "/";
	$day = $date['mday'];
	if ($day < 10) {
		echo "0" . $day;
	} else {
		echo $day;
	}
	echo "/";
	$year = $date['year'];
	echo $year;
?>" size="8" disabled="disabled" /><input type="hidden" name="sentOn" id="sentOn" value="<?php
	$date = getdate();
	$year = $date['year'];
	echo $year . "-";
	$month = $date['mon'];
	if ($month < 10) {
		echo "0" . $month;
	} else {
		echo $month;
	}
	echo "-";
	$day = $date['mday'];
	if ($day < 10) {
		echo "0" . $day;
	} else {
		echo $day;
	}
?>" /></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="feedback">Feedback:</label></td>
      <td colspan="2"><textarea name="feedback" id="feedback" cols="75" rows="15" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr class="button">
      <td colspan="3"><input type="submit" name="submit" id="submit" value="Send Feedback" /><?php 
			sentSuccessful("Thanks for your input! Your feedback will be reviewed as soon as possible, <br />and you will be contacted, if necessary."); ?></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="feedback" />
</form><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsFeedback);
mysql_free_result($rsEmployees);
?>