<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsApps = 25;
$pageNum_rsApps = 0;
if (isset($_GET['pageNum_rsApps'])) {
  $pageNum_rsApps = $_GET['pageNum_rsApps'];
}
$startRow_rsApps = $pageNum_rsApps * $maxRows_rsApps;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApps = "SELECT * FROM applications ORDER BY application ASC";
$query_limit_rsApps = sprintf("%s LIMIT %d, %d", $query_rsApps, $startRow_rsApps, $maxRows_rsApps);
$rsApps = mysql_query($query_limit_rsApps, $connProdOps) or die(mysql_error());
$row_rsApps = mysql_fetch_assoc($rsApps);

if (isset($_GET['totalRows_rsApps'])) {
  $totalRows_rsApps = $_GET['totalRows_rsApps'];
} else {
  $all_rsApps = mysql_query($query_rsApps);
  $totalRows_rsApps = mysql_num_rows($all_rsApps);
}
$totalPages_rsApps = ceil($totalRows_rsApps/$maxRows_rsApps)-1;

$queryString_rsApps = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsApps") == false && 
        stristr($param, "totalRows_rsApps") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsApps = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsApps = sprintf("&totalRows_rsApps=%d%s", $totalRows_rsApps, $queryString_rsApps);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Applications"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("application", null, "applications", "Applications", "Add an Application"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" align="center" cellspacing="0" cellpadding="2">
  <tr>
    <th>Application</th>
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
	echo ">"; ?>
	<td><a href="../apps/app.php?app=<?php echo $row_rsApps['applicationID']; ?>"><?php echo $row_rsApps['application']; ?></a></td>
	<?php sudoAuthData("app.php", "Update application", "td", "edit", "function=update&amp;app=" . $row_rsApps['applicationID']); ?>
  </tr>
  <?php } while ($row_rsApps = mysql_fetch_assoc($rsApps)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsApps + 1) ?> through <?php echo min($startRow_rsApps + $maxRows_rsApps, $totalRows_rsApps) ?> of <?php echo $totalRows_rsApps ?> Applications</div>
<table class="pagination" align="center">
  <tr><td width="23%" align="center">
      <?php if ($pageNum_rsApps > 0) { // Show if not first page ?>
      <a href="<?php printf("../statusReports/%25s?pageNum_rsApps=%25d%25s", $currentPage, 0, $queryString_rsApps); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
    <?php if ($pageNum_rsApps > 0) { // Show if not first page ?>
      <a href="<?php printf("../statusReports/%25s?pageNum_rsApps=%25d%25s", $currentPage, max(0, $pageNum_rsApps - 1), $queryString_rsApps); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsApps < $totalPages_rsApps) { // Show if not last page ?>
      <a href="<?php printf("../statusReports/%25s?pageNum_rsApps=%25d%25s", $currentPage, min($totalPages_rsApps, $pageNum_rsApps + 1), $queryString_rsApps); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsApps < $totalPages_rsApps) { // Show if not last page ?>
      <a href="<?php printf("../statusReports/%25s?pageNum_rsApps=%25d%25s", $currentPage, $totalPages_rsApps, $queryString_rsApps); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td></tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsApps);
?>