<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

session_start();

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsFileUploads = 25;
$pageNum_rsFileUploads = 0;
if (isset($_GET['pageNum_rsFileUploads'])) {
  $pageNum_rsFileUploads = $_GET['pageNum_rsFileUploads'];
}
$startRow_rsFileUploads = $pageNum_rsFileUploads * $maxRows_rsFileUploads;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsFileUploads = "SELECT fileuploads.fileUploadID, fileuploads.filename, fileuploads.keywords, fileuploads.description, DATE_FORMAT(fileuploads.dateUploaded, '%m/%d/%Y') AS dateUploaded, fileuploads.employeeID, employees.displayName FROM fileuploads LEFT JOIN employees ON fileuploads.employeeID=employees.employeeID";
$query_limit_rsFileUploads = sprintf("%s LIMIT %d, %d", $query_rsFileUploads, $startRow_rsFileUploads, $maxRows_rsFileUploads);
$rsFileUploads = mysql_query($query_limit_rsFileUploads, $connProdOps) or die(mysql_error());
$row_rsFileUploads = mysql_fetch_assoc($rsFileUploads);

if (isset($_GET['totalRows_rsFileUploads'])) {
  $totalRows_rsFileUploads = $_GET['totalRows_rsFileUploads'];
} else {
  $all_rsFileUploads = mysql_query($query_rsFileUploads);
  $totalRows_rsFileUploads = mysql_num_rows($all_rsFileUploads);
}
$totalPages_rsFileUploads = ceil($totalRows_rsFileUploads/$maxRows_rsFileUploads)-1;

$queryString_rsFileUploads = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsFileUploads") == false && 
        stristr($param, "totalRows_rsFileUploads") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsFileUploads = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsFileUploads = sprintf("&totalRows_rsFileUploads=%d%s", $totalRows_rsFileUploads, $queryString_rsFileUploads);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: File Uploads</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("fileUpload", null, "fileUploads", "File Uploads", "Upload a File"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
  <table class="data" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th width="11%">Uploaded</th>
      <th width="42%">Filename</th>
      <th width="29%">Keywords</th>
      <th width="18%">Submitter</th>
		<?php sudoAuthData(null, null, "th", "edit", null); ?>
    </tr>
<?php 
	$num=0;
		do { 
	$num++;
	echo "<tr";
		if ($num % 2) {
			echo " class=\"odd\"";
		}
	echo ">";
	?>
      <td><?php echo $row_rsFileUploads['dateUploaded']; ?></td>
      <td><a href="fileUpload.php?function=view&amp;fileUpload=<?php echo $row_rsFileUploads['fileUploadID']; ?>"><?php echo $row_rsFileUploads['filename']; ?></a></td>
      <td><?php echo $row_rsFileUploads['keywords']; ?></td>
      <td><?php echo $row_rsFileUploads['displayName']; ?></td>
		<?php sudoAuthData("fileUpload.php", "Update file information", "td", "edit", "function=update&amp;fileUpload=" . $row_rsFileUploads['fileUploadID']); ?>
    </tr>
    <?php } while ($row_rsFileUploads = mysql_fetch_assoc($rsFileUploads)); ?>
  </table>

<div id="count">Viewing <?php echo ($startRow_rsFileUploads + 1) ?> through <?php echo min($startRow_rsFileUploads + $maxRows_rsFileUploads, $totalRows_rsFileUploads) ?> of <?php echo $totalRows_rsFileUploads ?> Uploads</div>

<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsFileUploads > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsFileUploads=%d%s", $currentPage, 0, $queryString_rsFileUploads); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsFileUploads > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsFileUploads=%d%s", $currentPage, max(0, $pageNum_rsFileUploads - 1), $queryString_rsFileUploads); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsFileUploads < $totalPages_rsFileUploads) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsFileUploads=%d%s", $currentPage, min($totalPages_rsFileUploads, $pageNum_rsFileUploads + 1), $queryString_rsFileUploads); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsFileUploads < $totalPages_rsFileUploads) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsFileUploads=%d%s", $currentPage, $totalPages_rsFileUploads, $queryString_rsFileUploads); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsFileUploads);
?>