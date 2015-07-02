<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');
session_start();

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCountries = 35;
$pageNum_rsCountries = 0;
if (isset($_GET['pageNum_rsCountries'])) {
  $pageNum_rsCountries = $_GET['pageNum_rsCountries'];
}
$startRow_rsCountries = $pageNum_rsCountries * $maxRows_rsCountries;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCountries = "SELECT countryISO, country, countryID, countries.`zone`, countries.countryCode, countries.digits FROM countries ORDER BY country ASC";
$query_limit_rsCountries = sprintf("%s LIMIT %d, %d", $query_rsCountries, $startRow_rsCountries, $maxRows_rsCountries);
$rsCountries = mysql_query($query_limit_rsCountries, $connProdOps) or die(mysql_error());
$row_rsCountries = mysql_fetch_assoc($rsCountries);

if (isset($_GET['totalRows_rsCountries'])) {
  $totalRows_rsCountries = $_GET['totalRows_rsCountries'];
} else {
  $all_rsCountries = mysql_query($query_rsCountries);
  $totalRows_rsCountries = mysql_num_rows($all_rsCountries);
}
$totalPages_rsCountries = ceil($totalRows_rsCountries/$maxRows_rsCountries)-1;

$queryString_rsCountries = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCountries") == false && 
        stristr($param, "totalRows_rsCountries") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCountries = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCountries = sprintf("&totalRows_rsCountries=%d%s", $totalRows_rsCountries, $queryString_rsCountries);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Countries"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("country", null, "countries", "Countries", "Add a Country"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
  <table cellpadding="2" cellspacing="0" class="data" align="center">
    <tr>
      <th>country ISO</th>
      <th>Country</th>
      <th>Zone</th>
      <th>Country Code</th>
      <th>Digits</th>
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
	echo ">\n";
	?>
      <td><?php echo $row_rsCountries['countryISO']; ?></td>
      <td><?php echo $row_rsCountries['country']; ?></td>
      <td><?php echo $row_rsCountries['zone']; ?></td>
      <td><?php echo $row_rsCountries['countryCode']; ?></td>
      <td><?php echo $row_rsCountries['digits']; ?></td>
	<?php sudoAuthData("country.php", "Update Country", "td", "edit", "function=update&amp;country=" . $row_rsCountries['countryID']); ?>
    </tr>
    <?php } while ($row_rsCountries = mysql_fetch_assoc($rsCountries)); ?>
  </table>
  <div id="count">Viewing <?php echo ($startRow_rsCountries + 1) ?> through <?php echo min($startRow_rsCountries + $maxRows_rsCountries, $totalRows_rsCountries) ?> of <?php echo $totalRows_rsCountries ?> Countries</div>
  <table class="pagination" align="center">
    <tr>
      <td width="23%" align="center">
        <?php if ($pageNum_rsCountries > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCountries=%d%s", $currentPage, 0, $queryString_rsCountries); ?>"><img src="../../images/icons/first.jpg" /></a>
        <?php } // Show if not first page ?>
      </td>
      <td width="31%" align="center">
        <?php if ($pageNum_rsCountries > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCountries=%d%s", $currentPage, max(0, $pageNum_rsCountries - 1), $queryString_rsCountries); ?>"><img src="../../images/icons/prev.jpg" /></a>
        <?php } // Show if not first page ?>
      </td>
      <td width="23%" align="center">
        <?php if ($pageNum_rsCountries < $totalPages_rsCountries) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCountries=%d%s", $currentPage, min($totalPages_rsCountries, $pageNum_rsCountries + 1), $queryString_rsCountries); ?>"><img src="../../images/icons/next.jpg" /></a>
        <?php } // Show if not last page ?>
      </td>
      <td width="23%" align="center">
        <?php if ($pageNum_rsCountries < $totalPages_rsCountries) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCountries=%d%s", $currentPage, $totalPages_rsCountries, $queryString_rsCountries); ?>"><img src="../../images/icons/final.jpg" /></a>
        <?php } // Show if not last page ?>
      </td>
    </tr>
  </table>
  <?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsCountries);
?>