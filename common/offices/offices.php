<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsOffices = 25;
$pageNum_rsOffices = 0;
if (isset($_GET['pageNum_rsOffices'])) {
  $pageNum_rsOffices = $_GET['pageNum_rsOffices'];
}
$startRow_rsOffices = $pageNum_rsOffices * $maxRows_rsOffices;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsOffices = "SELECT officeID, officeName FROM offices ORDER BY officeName ASC";
$query_limit_rsOffices = sprintf("%s LIMIT %d, %d", $query_rsOffices, $startRow_rsOffices, $maxRows_rsOffices);
$rsOffices = mysql_query($query_limit_rsOffices, $connProdOps) or die(mysql_error());
$row_rsOffices = mysql_fetch_assoc($rsOffices);

if (isset($_GET['totalRows_rsOffices'])) {
  $totalRows_rsOffices = $_GET['totalRows_rsOffices'];
} else {
  $all_rsOffices = mysql_query($query_rsOffices);
  $totalRows_rsOffices = mysql_num_rows($all_rsOffices);
}
$totalPages_rsOffices = ceil($totalRows_rsOffices/$maxRows_rsOffices)-1;

$queryString_rsOffices = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsOffices") == false && 
        stristr($param, "totalRows_rsOffices") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsOffices = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsOffices = sprintf("&totalRows_rsOffices=%d%s", $totalRows_rsOffices, $queryString_rsOffices);
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
<?php buildHeaderNEW("office", null, "offices", "Offices", "Add an Office"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
<table class="data" align="center" cellpadding="2" cellspacing="0">
  <tr>
    <th>Office</th>
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
    <td><?php echo $row_rsOffices['officeName']; ?></td>
  </tr>
  <?php } while ($row_rsOffices = mysql_fetch_assoc($rsOffices)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsOffices + 1) ?> through <?php echo min($startRow_rsOffices + $maxRows_rsOffices, $totalRows_rsOffices) ?> of <?php echo $totalRows_rsOffices ?> Offices</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsOffices > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsOffices=%d%s", $currentPage, 0, $queryString_rsOffices); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsOffices > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsOffices=%d%s", $currentPage, max(0, $pageNum_rsOffices - 1), $queryString_rsOffices); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsOffices < $totalPages_rsOffices) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsOffices=%d%s", $currentPage, min($totalPages_rsOffices, $pageNum_rsOffices + 1), $queryString_rsOffices); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsOffices < $totalPages_rsOffices) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsOffices=%d%s", $currentPage, $totalPages_rsOffices, $queryString_rsOffices); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsOffices);
?>