<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php'); 

//Employees
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employeeID, displayName FROM employees WHERE departmentID = 1 ORDER BY displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);

if ((isset($_GET["function"])) && ($_GET["function"] != "add")) {
$varFileUpload_rsFileUploads = "1";
if (isset($_GET['fileUpload'])) {
  $varFileUpload_rsFileUploads = (get_magic_quotes_gpc()) ? $_GET['fileUpload'] : addslashes($_GET['fileUpload']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsFileUploads = sprintf("SELECT fileuploads.fileUploadID, fileuploads.filename, fileuploads.keywords, fileuploads.description, DATE_FORMAT(fileuploads.dateUploaded, '%%m/%%d/%%Y') AS dateUploaded, fileuploads.dateUpdated, fileuploads.employeeID, employees.displayName FROM fileuploads LEFT JOIN employees ON fileuploads.employeeID=employees.employeeID WHERE fileuploads.fileUploadID = %s", $varFileUpload_rsFileUploads);
$rsFileUploads = mysql_query($query_rsFileUploads, $connProdOps) or die(mysql_error());
$row_rsFileUploads = mysql_fetch_assoc($rsFileUploads);
$totalRows_rsFileUploads = mysql_num_rows($rsFileUploads);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: File Uploads</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
	<script type="text/javascript" src="../../inc/js/calendarDateInput2.js"></script>
</head>
<body>
<?php buildHeader("fileUpload", "File Uploads", "fileUpload", "File Upload", null); ?>
<div align="center">
<div class="casing" align="left">
<form enctype="multipart/form-data" method="post" name="fileUploadForm" action="fileUploadSend.php">
<?php if ($_GET['function'] == "add") { ?>
	<div style="text-align:left; margin-left: 115px;"><dt>Instructions</dt><dd>Below is a form to upload a file to the ProdOps server. Please select your name,<br />any common keywords pertaining to this file (for searching later), and a brief<br />description of the file to assist with proper classification.<br />Thanks for sharing!</dd></div><br />
<?php } elseif ($_GET['function'] == "update") { ?>
	<div style="text-align:left; margin-left: 115px;"><dt>Instructions</dt><dd>Below is a form to update information for files previously uploaded to the ProdOps server. Please update the file location with the fully qualified file system path, as well as the keywords &amp; description, if desired. This will help keep the knowledgebase consistently upto date.<br />Thanks!</dd></div><br />
<?php } ?>
  <table align="center" width="555" class="add" cellspacing="0">
    <tr valign="middle">
      <td width="82" class="contrast"><label for="employee">Name:</label></td>
      <td width="173"><?php if ($_GET['function'] == "add") { ?><select name="employee" id="employee">
	  	<option value="">Select Name</option>
        <?php do { ?>
        <option value="<?php echo $row_rsEmployees['employeeID']?>"><?php echo $row_rsEmployees['displayName']?></option>
        <?php } while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees)); ?>
      </select><?php } else {
	  						echo $row_rsFileUploads['displayName'];
					} ?></td>
    <td width="292" align="right"><?php 
			if ($_GET['function'] == "add") { 
				?><label for="dateUploaded">Today's Date:</label><script>DateInput('dateUploaded', true, 'YYYY-MM-DD')</script><?php
			} else {
					echo "Uploaded on " . $row_rsFileUploads['dateUploaded'];
			} ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="keywords">Keywords:</label></td>
      <td<?php if ($_GET['function'] == "add") { echo " colspan=\"2\""; } ?>><?php formField("text", "keywords", $row_rsFileUploads['keywords'], "47", "255", null, null); ?></td>
<?php if (($_GET['function'] == "update") || ($_GET['function'] == "view")) {
			?><td align="right"><label for="dateUpdated">Today's Date:</label><?php 
			if (($_GET['function'] == "update") && ($row_rsFileUploads['dateUpdated'] == null)) {
				?><script>DateInput('dateUpdated', true, 'YYYY-MM-DD')</script></td><?php
			} elseif (($_GET['function'] == "view") && ($row_rsFileUploads['dateUpdated'] != null)) {
				echo $row_rsFileUploads['dateUpdated'] . "</td>";
			}
		} ?>
    </tr>
    <tr>
      <td class="contrast"><label for="filename">File:</label></td>
      <td colspan="2"><?php if ($_GET['function'] == "add") { ?><input type="file" name="filename" id="filename" size="40" /><?php 
	  						} elseif ($_GET['function'] == "update") {
									echo "<input type=\"text\" name=\"filename\" id=\"filename\" size=\"55\" value=\"" . $row_rsFileUploads['filename'] . "\" />";
							} else {
									echo "<a href=\"" . $row_rsFileUploads['filename'] . "\">" . $row_rsFileUploads['filename'] . "</a>";
							} ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="description">Description:</label></td>
      <td colspan="2"><?php formField("textarea", "description", $row_rsFileUploads['description'], "75", null, "7", "virtual"); ?></td>
    </tr>
    <tr class="button">
      <td colspan="3"><input type="submit" name="submit" id="submit" value="Submit File" /><?php 
			sentSuccessful("Thanks for contributing! Your uploaded file will be reviewed as soon as possible, <br />and you will be contacted, if necessary."); ?></td>
    </tr>
  </table>
<?php if ($_GET['function'] == "add") {
		echo "<input type=\"hidden\" name=\"MM_insert\" value=\"fileUploadAdd\" />";
	} elseif ($_GET['function'] == "update") {
		echo "<input type=\"hidden\" name=\"MM_update\" value=\"fileUploadUpdate\" />";
		echo "<input type=\"hidden\" name=\"fileUploadID\" id=\"fileUploadID\" value=\"" . $row_rsFileUploads['fileUploadID'] . "\" />"; 
	} ?>
</form><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsEmployees);
mysql_free_result($rsFileUploads);
?>