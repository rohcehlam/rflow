<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsNetworkTypes = 25;
$pageNum_rsNetworkTypes = 0;
if (isset($_GET['pageNum_rsNetworkTypes'])) {
  $pageNum_rsNetworkTypes = $_GET['pageNum_rsNetworkTypes'];
}
$startRow_rsNetworkTypes = $pageNum_rsNetworkTypes * $maxRows_rsNetworkTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsNetworkTypes = "SELECT networkTypeID, networkType FROM networktypes ORDER BY networkType ASC";
$query_limit_rsNetworkTypes = sprintf("%s LIMIT %d, %d", $query_rsNetworkTypes, $startRow_rsNetworkTypes, $maxRows_rsNetworkTypes);
$rsNetworkTypes = mysql_query($query_limit_rsNetworkTypes, $connProdOps) or die(mysql_error());
$row_rsNetworkTypes = mysql_fetch_assoc($rsNetworkTypes);

if (isset($_GET['totalRows_rsNetworkTypes'])) {
  $totalRows_rsNetworkTypes = $_GET['totalRows_rsNetworkTypes'];
} else {
  $all_rsNetworkTypes = mysql_query($query_rsNetworkTypes);
  $totalRows_rsNetworkTypes = mysql_num_rows($all_rsNetworkTypes);
}
$totalPages_rsNetworkTypes = ceil($totalRows_rsNetworkTypes/$maxRows_rsNetworkTypes)-1;

$queryString_rsNetworkTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsNetworkTypes") == false && 
        stristr($param, "totalRows_rsNetworkTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsNetworkTypes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsNetworkTypes = sprintf("&totalRows_rsNetworkTypes=%d%s", $totalRows_rsNetworkTypes, $queryString_rsNetworkTypes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("networkType", null, "networkTypes", "Network Types", "Add a Network Type"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" align="center" cellpadding="2" cellspacing="0">
  <tr>
    <th>Network Type</th>
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
    <td><a href="networkType.php?function=view&networkType=<?php echo $row_rsNetworkTypes['networkTypeID']; ?>"><?php echo $row_rsNetworkTypes['networkType']; ?></a></td>
	<?php sudoAuthData("networkType.php", "Network Type", "td", "edit", "function=update&amp;networkType=" . $row_rsNetworkTypes['networkTypeID']); ?>
  </tr>
  <?php } while ($row_rsNetworkTypes = mysql_fetch_assoc($rsNetworkTypes)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsNetworkTypes + 1) ?> through <?php echo min($startRow_rsNetworkTypes + $maxRows_rsNetworkTypes, $totalRows_rsNetworkTypes) ?> of <?php echo $totalRows_rsNetworkTypes ?> Network Types</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsNetworkTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsNetworkTypes=%d%s", $currentPage, 0, $queryString_rsNetworkTypes); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsNetworkTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsNetworkTypes=%d%s", $currentPage, max(0, $pageNum_rsNetworkTypes - 1), $queryString_rsNetworkTypes); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsNetworkTypes < $totalPages_rsNetworkTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsNetworkTypes=%d%s", $currentPage, min($totalPages_rsNetworkTypes, $pageNum_rsNetworkTypes + 1), $queryString_rsNetworkTypes); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsNetworkTypes < $totalPages_rsNetworkTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsNetworkTypes=%d%s", $currentPage, $totalPages_rsNetworkTypes, $queryString_rsNetworkTypes); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsNetworkTypes);
?>