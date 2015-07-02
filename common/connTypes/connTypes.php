<?php require_once('../../Connections/connProdOps.php');
	require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsConnTypes = 17;
$pageNum_rsConnTypes = 0;
if (isset($_GET['pageNum_rsConnTypes'])) {
  $pageNum_rsConnTypes = $_GET['pageNum_rsConnTypes'];
}
$startRow_rsConnTypes = $pageNum_rsConnTypes * $maxRows_rsConnTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsConnTypes = "SELECT connTypeID, connType FROM conntypes ORDER BY connType ASC";
$query_limit_rsConnTypes = sprintf("%s LIMIT %d, %d", $query_rsConnTypes, $startRow_rsConnTypes, $maxRows_rsConnTypes);
$rsConnTypes = mysql_query($query_limit_rsConnTypes, $connProdOps) or die(mysql_error());
$row_rsConnTypes = mysql_fetch_assoc($rsConnTypes);

if (isset($_GET['totalRows_rsConnTypes'])) {
  $totalRows_rsConnTypes = $_GET['totalRows_rsConnTypes'];
} else {
  $all_rsConnTypes = mysql_query($query_rsConnTypes);
  $totalRows_rsConnTypes = mysql_num_rows($all_rsConnTypes);
}
$totalPages_rsConnTypes = ceil($totalRows_rsConnTypes/$maxRows_rsConnTypes)-1;

$queryString_rsConnTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsConnTypes") == false && 
        stristr($param, "totalRows_rsConnTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsConnTypes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsConnTypes = sprintf("&totalRows_rsConnTypes=%d%s", $totalRows_rsConnTypes, $queryString_rsConnTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Connection Types"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeader("connType", null, "connTypes", "Connection Types", "Add a Connection Type"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" cellspacing="0" align="center">
  <tr>
    <th>Connection Type</th>
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
    <td><a href="connType.php?connType=<?php echo $row_rsConnTypes['connTypeID']; ?>&amp;function=view"><?php echo $row_rsConnTypes['connType']; ?></a></td>
	<?php sudoAuthData("connType.php", "Update Connection Type", "td", "edit", "function=update&amp;app=" . $row_rsConnTypes['connTypeID']); ?>
  </tr>
  <?php } while ($row_rsConnTypes = mysql_fetch_assoc($rsConnTypes)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsConnTypes + 1) ?> through <?php echo min($startRow_rsConnTypes + $maxRows_rsConnTypes, $totalRows_rsConnTypes) ?> of <?php echo $totalRows_rsConnTypes ?> Connection Types</div>
<table class="pagination" align="center">
  <tr><td width="23%" align="center">
      <?php if ($pageNum_rsConnTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsConnTypes=%d%s", $currentPage, 0, $queryString_rsConnTypes); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsConnTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsConnTypes=%d%s", $currentPage, max(0, $pageNum_rsConnTypes - 1), $queryString_rsConnTypes); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsConnTypes < $totalPages_rsConnTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsConnTypes=%d%s", $currentPage, min($totalPages_rsConnTypes, $pageNum_rsConnTypes + 1), $queryString_rsConnTypes); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsConnTypes < $totalPages_rsConnTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsConnTypes=%d%s", $currentPage, $totalPages_rsConnTypes, $queryString_rsConnTypes); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td></tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsConnTypes);
?>