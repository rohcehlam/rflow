<?php require_once('../../Connections/connProdOps.php');
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsDatacenters = 17;
$pageNum_rsDatacenters = 0;
if (isset($_GET['pageNum_rsDatacenters'])) {
  $pageNum_rsDatacenters = $_GET['pageNum_rsDatacenters'];
}
$startRow_rsDatacenters = $pageNum_rsDatacenters * $maxRows_rsDatacenters;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDatacenters = "SELECT datacenterID, datacenter FROM datacenters ORDER BY datacenter ASC";
$query_limit_rsDatacenters = sprintf("%s LIMIT %d, %d", $query_rsDatacenters, $startRow_rsDatacenters, $maxRows_rsDatacenters);
$rsDatacenters = mysql_query($query_limit_rsDatacenters, $connProdOps) or die(mysql_error());
$row_rsDatacenters = mysql_fetch_assoc($rsDatacenters);

if (isset($_GET['totalRows_rsDatacenters'])) {
  $totalRows_rsDatacenters = $_GET['totalRows_rsDatacenters'];
} else {
  $all_rsDatacenters = mysql_query($query_rsDatacenters);
  $totalRows_rsDatacenters = mysql_num_rows($all_rsDatacenters);
}
$totalPages_rsDatacenters = ceil($totalRows_rsDatacenters/$maxRows_rsDatacenters)-1;

$queryString_rsDatacenters = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsDatacenters") == false && 
        stristr($param, "totalRows_rsDatacenters") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsDatacenters = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsDatacenters = sprintf("&totalRows_rsDatacenters=%d%s", $totalRows_rsDatacenters, $queryString_rsDatacenters);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Datacenters"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("datacenter", null, "datacenters", "Datacenters", "Add a Datacenter"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" align="center" cellspacing="0">
  <tr>
    <th>Datacenter</th>
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
    <td><a title="View additional information about <?php echo $row_rsDatacenters['datacenter']; ?>" href="datacenter.php?datacenter=<?php echo $row_rsDatacenters['datacenterID']; ?>&amp;function=view"><?php echo $row_rsDatacenters['datacenter']; ?></a></td>
	<?php sudoAuthData("datacenter.php", "Update datacenter", "td", "edit", "function=update&amp;datacenter=" . $row_rsDatacenters['datacenterID']); ?>
  </tr>
  <?php } while ($row_rsDatacenters = mysql_fetch_assoc($rsDatacenters)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsDatacenters + 1) ?> through <?php echo min($startRow_rsDatacenters + $maxRows_rsDatacenters, $totalRows_rsDatacenters) ?> of <?php echo $totalRows_rsDatacenters ?> Datacenters</div>
<table class="pagination" align="center">
  <tr><td width="23%" align="center">
      <?php if ($pageNum_rsDatacenters > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsDatacenters=%d%s", $currentPage, 0, $queryString_rsDatacenters); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsDatacenters > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsDatacenters=%d%s", $currentPage, max(0, $pageNum_rsDatacenters - 1), $queryString_rsDatacenters); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsDatacenters < $totalPages_rsDatacenters) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsDatacenters=%d%s", $currentPage, min($totalPages_rsDatacenters, $pageNum_rsDatacenters + 1), $queryString_rsDatacenters); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsDatacenters < $totalPages_rsDatacenters) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsDatacenters=%d%s", $currentPage, $totalPages_rsDatacenters, $queryString_rsDatacenters); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td></tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsDatacenters);
?>