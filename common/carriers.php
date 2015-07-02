<?php require_once('../Connections/connProdOps.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCarriers = 25;
$pageNum_rsCarriers = 0;
if (isset($_GET['pageNum_rsCarriers'])) {
  $pageNum_rsCarriers = $_GET['pageNum_rsCarriers'];
}
$startRow_rsCarriers = $pageNum_rsCarriers * $maxRows_rsCarriers;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCarriers = "SELECT * FROM carriers ORDER BY carrier ASC";
$query_limit_rsCarriers = sprintf("%s LIMIT %d, %d", $query_rsCarriers, $startRow_rsCarriers, $maxRows_rsCarriers);
$rsCarriers = mysql_query($query_limit_rsCarriers, $connProdOps) or die(mysql_error());
$row_rsCarriers = mysql_fetch_assoc($rsCarriers);

if (isset($_GET['totalRows_rsCarriers'])) {
  $totalRows_rsCarriers = $_GET['totalRows_rsCarriers'];
} else {
  $all_rsCarriers = mysql_query($query_rsCarriers);
  $totalRows_rsCarriers = mysql_num_rows($all_rsCarriers);
}
$totalPages_rsCarriers = ceil($totalRows_rsCarriers/$maxRows_rsCarriers)-1;

$queryString_rsCarriers = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCarriers") == false && 
        stristr($param, "totalRows_rsCarriers") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCarriers = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCarriers = sprintf("&totalRows_rsCarriers=%d%s", $totalRows_rsCarriers, $queryString_rsCarriers);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Production Operations: Reporting - Carriers</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<script src="../Connections/connProdOps.php" type="text/javascript"></script>
</head>
<body>
<?php require_once("../inc/nav.php"); ?>
<p><a href="">Home</a> &raquo; Status Reports</p>
<img src="../images/icons/add.gif" /><a title="Add a Carrier" href="carrierAdd.php">Add a carrier</a>
<table class="data">
	<tr><th>&nbsp;
		</th><th>
		Carrier</th><th colspan="2">&nbsp;
		
	</th></tr>
        <?php 
	$num=0;		
		do { 
	$num++;
	echo "<tr";
		if ($num % 2) {
			echo " class=\"odd\"";
		}
	echo ">";
	?><td>
		<a title="View all MDNs provided by <?php echo $row_rsCarriers['carrier']; ?>" href="../cellPhones/carrierXmdn.php?carrier=<?php echo $row_rsCarriers['carrierID']; ?>"><img src="../images/icons/phone.gif" alt="MDN" /></a></td><td>
		<?php echo $row_rsCarriers['carrier']; ?></td><td class="icon">
		<a title="Update Carrier Information" href="carriersUpdate.php?carrier=<?php echo $row_rsCarriers['carrierID']; ?>"><img src="../images/icons/edit.gif" /></a></td><td class="icon">
		<a title="Delete Carrier" href="carriersDelete.php?carrier=<?php echo $row_rsCarriers['carrierID']; ?>"><img src="../images/icons/delete.gif" /></a>
	</td></tr>
  <?php } while ($row_rsCarriers = mysql_fetch_assoc($rsCarriers)); ?>
</table>

<table class="pagination">
	<tr><td width="23%">
		<?php if ($pageNum_rsCarriers > 0) { // Show if not first page ?>
			<a href="<?php printf("%s?pageNum_rsCarriers=%d%s", $currentPage, 0, $queryString_rsCarriers); ?>"><img src="../images/icons/first.jpg" /></a>
		<?php } // Show if not first page ?>
	</td><td width="31%">
		<?php if ($pageNum_rsCarriers > 0) { // Show if not first page ?>
			<a href="<?php printf("%s?pageNum_rsCarriers=%d%s", $currentPage, max(0, $pageNum_rsCarriers - 1), $queryString_rsCarriers); ?>"><img src="../images/icons/prev.jpg" /></a>
		<?php } // Show if not first page ?>
	</td><td width="23%">
		<?php if ($pageNum_rsCarriers < $totalPages_rsCarriers) { // Show if not last page ?>
			<a href="<?php printf("%s?pageNum_rsCarriers=%d%s", $currentPage, min($totalPages_rsCarriers, $pageNum_rsCarriers + 1), $queryString_rsCarriers); ?>"><img src="../images/icons/next.jpg" /></a>
		<?php } // Show if not last page ?>
	</td><td width="23%">
		<?php if ($pageNum_rsCarriers < $totalPages_rsCarriers) { // Show if not last page ?>
			<a href="<?php printf("%s?pageNum_rsCarriers=%d%s", $currentPage, $totalPages_rsCarriers, $queryString_rsCarriers); ?>"><img src="../images/icons/final.jpg" /></a>
		<?php } // Show if not last page ?>
	</td></tr>
</table>

<div id="count">Viewing <?php echo ($startRow_rsCarriers + 1) ?> through <?php echo min($startRow_rsCarriers + $maxRows_rsCarriers, $totalRows_rsCarriers) ?> of <?php echo $totalRows_rsCarriers ?> carriers</div>
</body>
</html><?php
mysql_free_result($rsCarriers);
?>
