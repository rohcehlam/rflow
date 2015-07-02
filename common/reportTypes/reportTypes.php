<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsReportTypes = 25;
$pageNum_rsReportTypes = 0;
if (isset($_GET['pageNum_rsReportTypes'])) {
  $pageNum_rsReportTypes = $_GET['pageNum_rsReportTypes'];
}
$startRow_rsReportTypes = $pageNum_rsReportTypes * $maxRows_rsReportTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reportTypes ORDER BY reportType ASC";
$query_limit_rsReportTypes = sprintf("%s LIMIT %d, %d", $query_rsReportTypes, $startRow_rsReportTypes, $maxRows_rsReportTypes);
$rsReportTypes = mysql_query($query_limit_rsReportTypes, $connProdOps) or die(mysql_error());
$row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);

if (isset($_GET['totalRows_rsReportTypes'])) {
  $totalRows_rsReportTypes = $_GET['totalRows_rsReportTypes'];
} else {
  $all_rsReportTypes = mysql_query($query_rsReportTypes);
  $totalRows_rsReportTypes = mysql_num_rows($all_rsReportTypes);
}
$totalPages_rsReportTypes = ceil($totalRows_rsReportTypes/$maxRows_rsReportTypes)-1;

$queryString_rsReportTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsReportTypes") == false && 
        stristr($param, "totalRows_rsReportTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsReportTypes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsReportTypes = sprintf("&totalRows_rsReportTypes=%d%s", $totalRows_rsReportTypes, $queryString_rsReportTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Report Types</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("reportType", null, "reportTypes", "Report Types", "Add a Report Type"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table align="center" class="data" cellpadding="2" cellspacing="0">
  <tr>
    <th>Report Type</th>
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
	<td><a href="reportType.php?reportType=<?php echo $row_rsReportTypes['reportTypeID']; ?>"><?php echo $row_rsReportTypes['reportType']; ?></a></td>
	<?php sudoAuthData("reportType", "Update Report Type", "td", "edit", "function=update&amp;reportType=" . $row_rsReportTypes['reportTypeID']); ?>
  </tr>
  <?php } while ($row_rsReportTypes = mysql_fetch_assoc($rsReportTypes)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsReportTypes + 1) ?> through <?php echo min($startRow_rsReportTypes + $maxRows_rsReportTypes, $totalRows_rsReportTypes) ?> of <?php echo $totalRows_rsReportTypes ?> Report Types</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsReportTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsReportTypes=%d%s", $currentPage, 0, $queryString_rsReportTypes); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsReportTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsReportTypes=%d%s", $currentPage, max(0, $pageNum_rsReportTypes - 1), $queryString_rsReportTypes); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsReportTypes < $totalPages_rsReportTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsReportTypes=%d%s", $currentPage, min($totalPages_rsReportTypes, $pageNum_rsReportTypes + 1), $queryString_rsReportTypes); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsReportTypes < $totalPages_rsReportTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsReportTypes=%d%s", $currentPage, $totalPages_rsReportTypes, $queryString_rsReportTypes); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsReportTypes);
?>