<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');
session_start();

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCustomerTypes = 25;
$pageNum_rsCustomerTypes = 0;
if (isset($_GET['pageNum_rsCustomerTypes'])) {
  $pageNum_rsCustomerTypes = $_GET['pageNum_rsCustomerTypes'];
}
$startRow_rsCustomerTypes = $pageNum_rsCustomerTypes * $maxRows_rsCustomerTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCustomerTypes = "SELECT customerTypeID, customerType FROM customertypes ORDER BY customerType ASC";
$query_limit_rsCustomerTypes = sprintf("%s LIMIT %d, %d", $query_rsCustomerTypes, $startRow_rsCustomerTypes, $maxRows_rsCustomerTypes);
$rsCustomerTypes = mysql_query($query_limit_rsCustomerTypes, $connProdOps) or die(mysql_error());
$row_rsCustomerTypes = mysql_fetch_assoc($rsCustomerTypes);

if (isset($_GET['totalRows_rsCustomerTypes'])) {
  $totalRows_rsCustomerTypes = $_GET['totalRows_rsCustomerTypes'];
} else {
  $all_rsCustomerTypes = mysql_query($query_rsCustomerTypes);
  $totalRows_rsCustomerTypes = mysql_num_rows($all_rsCustomerTypes);
}
$totalPages_rsCustomerTypes = ceil($totalRows_rsCustomerTypes/$maxRows_rsCustomerTypes)-1;

$queryString_rsCustomerTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCustomerTypes") == false && 
        stristr($param, "totalRows_rsCustomerTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCustomerTypes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCustomerTypes = sprintf("&totalRows_rsCustomerTypes=%d%s", $totalRows_rsCustomerTypes, $queryString_rsCustomerTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Customer Types"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("customerType", null, "customerTypes", "Customer Types", "Add a Customer Type"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" cellpadding="2" cellspacing="0" align="center">
  <tr>
    <th>Customer Type</th>
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
    <td><a href="customerType.php?function=view&amp;customerType=<?php echo $row_rsCustomerTypes['customerTypeID']; ?>"><?php echo $row_rsCustomerTypes['customerType']; ?></a></td>
	<?php sudoAuthData("customerType.php", "Customer Type", "td", "edit", "function=update&amp;customerType=" . $row_rsCustomerTypes['customerTypeID']); ?>
  </tr>
  <?php } while ($row_rsCustomerTypes = mysql_fetch_assoc($rsCustomerTypes)); ?>
</table>

<div id="count">Viewing <?php echo ($startRow_rsCustomerTypes + 1) ?> through <?php echo min($startRow_rsCustomerTypes + $maxRows_rsCustomerTypes, $totalRows_rsCustomerTypes) ?> of <?php echo $totalRows_rsCustomerTypes ?> Customer Types</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsCustomerTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsCustomerTypes=%d%s", $currentPage, 0, $queryString_rsCustomerTypes); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsCustomerTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsCustomerTypes=%d%s", $currentPage, max(0, $pageNum_rsCustomerTypes - 1), $queryString_rsCustomerTypes); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsCustomerTypes < $totalPages_rsCustomerTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsCustomerTypes=%d%s", $currentPage, min($totalPages_rsCustomerTypes, $pageNum_rsCustomerTypes + 1), $queryString_rsCustomerTypes); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsCustomerTypes < $totalPages_rsCustomerTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsCustomerTypes=%d%s", $currentPage, $totalPages_rsCustomerTypes, $queryString_rsCustomerTypes); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsCustomerTypes);
?>