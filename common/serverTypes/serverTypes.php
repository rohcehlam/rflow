<?php require_once('../../Connections/connProdOps.php');
	require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsServerTypes = 17;
$pageNum_rsServerTypes = 0;
if (isset($_GET['pageNum_rsServerTypes'])) {
  $pageNum_rsServerTypes = $_GET['pageNum_rsServerTypes'];
}
$startRow_rsServerTypes = $pageNum_rsServerTypes * $maxRows_rsServerTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsServerTypes = "SELECT serverTypeID, serverType FROM servertypes ORDER BY serverType ASC";
$query_limit_rsServerTypes = sprintf("%s LIMIT %d, %d", $query_rsServerTypes, $startRow_rsServerTypes, $maxRows_rsServerTypes);
$rsServerTypes = mysql_query($query_limit_rsServerTypes, $connProdOps) or die(mysql_error());
$row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);

if (isset($_GET['totalRows_rsServerTypes'])) {
  $totalRows_rsServerTypes = $_GET['totalRows_rsServerTypes'];
} else {
  $all_rsServerTypes = mysql_query($query_rsServerTypes);
  $totalRows_rsServerTypes = mysql_num_rows($all_rsServerTypes);
}
$totalPages_rsServerTypes = ceil($totalRows_rsServerTypes/$maxRows_rsServerTypes)-1;

$queryString_rsServerTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&amp;", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsServerTypes") == false && 
        stristr($param, "totalRows_rsServerTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsServerTypes = "&amp;" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsServerTypes = sprintf("&amp;totalRows_rsServerTypes=%d%s", $totalRows_rsServerTypes, $queryString_rsServerTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Server Types</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeader("serverType",null,"serverTypes","Server Types","Add a Server Type"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" cellspacing="0">
  <tr>
    <th>Server Type</th>
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
    <td><a href="serverType.php?serverType=<?php echo $row_rsServerTypes['serverTypeID']; ?>&amp;function=view"><?php echo $row_rsServerTypes['serverType']; ?></a></td>
	<?php sudoAuthData("serverType", "Update Server Type", "td", "edit", "function=update&amp;serverType=" . $row_rsServerTypes['serverTypeID']); ?>
  </tr>
  <?php } while ($row_rsServerTypes = mysql_fetch_assoc($rsServerTypes)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsServerTypes + 1) ?> through <?php echo min($startRow_rsServerTypes + $maxRows_rsServerTypes, $totalRows_rsServerTypes) ?> of <?php echo $totalRows_rsServerTypes ?> Server Types</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsServerTypes > 0) { // Show if not first page ?>
      <a title="Go to first page" href="<?php printf("%s?pageNum_rsServerTypes=%d%s", $currentPage, 0, $queryString_rsServerTypes); ?>"><img src="../../images/icons/first.jpg" alt="First" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsServerTypes > 0) { // Show if not first page ?>
      <a title="Go to previous page" href="<?php printf("%s?pageNum_rsServerTypes=%d%s", $currentPage, max(0, $pageNum_rsServerTypes - 1), $queryString_rsServerTypes); ?>"><img src="../../images/icons/prev.jpg" alt="Previous" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsServerTypes < $totalPages_rsServerTypes) { // Show if not last page ?>
      <a title="Go to next page" href="<?php printf("%s?pageNum_rsServerTypes=%d%s", $currentPage, min($totalPages_rsServerTypes, $pageNum_rsServerTypes + 1), $queryString_rsServerTypes); ?>"><img src="../../images/icons/next.jpg" alt="Next" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsServerTypes < $totalPages_rsServerTypes) { // Show if not last page ?>
      <a title="Go to final page" href="<?php printf("%s?pageNum_rsServerTypes=%d%s", $currentPage, $totalPages_rsServerTypes, $queryString_rsServerTypes); ?>"><img src="../../images/icons/final.jpg" alt="Final" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsServerTypes);
?>