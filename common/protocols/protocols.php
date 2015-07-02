<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_fsProtocols = 25;
$pageNum_fsProtocols = 0;
if (isset($_GET['pageNum_fsProtocols'])) {
  $pageNum_fsProtocols = $_GET['pageNum_fsProtocols'];
}
$startRow_fsProtocols = $pageNum_fsProtocols * $maxRows_fsProtocols;

mysql_select_db($database_connProdOps, $connProdOps);
$query_fsProtocols = "SELECT protocolID, protocol FROM protocols ORDER BY protocol ASC";
$query_limit_fsProtocols = sprintf("%s LIMIT %d, %d", $query_fsProtocols, $startRow_fsProtocols, $maxRows_fsProtocols);
$fsProtocols = mysql_query($query_limit_fsProtocols, $connProdOps) or die(mysql_error());
$row_fsProtocols = mysql_fetch_assoc($fsProtocols);

if (isset($_GET['totalRows_fsProtocols'])) {
  $totalRows_fsProtocols = $_GET['totalRows_fsProtocols'];
} else {
  $all_fsProtocols = mysql_query($query_fsProtocols);
  $totalRows_fsProtocols = mysql_num_rows($all_fsProtocols);
}
$totalPages_fsProtocols = ceil($totalRows_fsProtocols/$maxRows_fsProtocols)-1;

$queryString_fsProtocols = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_fsProtocols") == false && 
        stristr($param, "totalRows_fsProtocols") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_fsProtocols = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_fsProtocols = sprintf("&totalRows_fsProtocols=%d%s", $totalRows_fsProtocols, $queryString_fsProtocols);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Protocols"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("protocol", null, "protocols", "Protocols", "Add a Protocol"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
  <table align="center" class="data" cellpadding="2" cellspacing="0">
    <tr>
		<th>Protocol</th>
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
      <td><a href="protocol.php?function=view&amp;protocol=<?php echo $row_fsProtocols['protocolID']; ?>"><?php echo $row_fsProtocols['protocol']; ?></a></td>
	<?php sudoAuthData("project.php", "Update Protocol", "td", "edit", "function=update&amp;app=" . $row_rsConnTypes['connTypeID']); ?>
    </tr>
    <?php } while ($row_fsProtocols = mysql_fetch_assoc($fsProtocols)); ?>
  </table>
<div id="count">Viewing <?php echo ($startRow_fsProtocols + 1) ?> through <?php echo min($startRow_fsProtocols + $maxRows_fsProtocols, $totalRows_fsProtocols) ?> of <?php echo $totalRows_fsProtocols ?> Protocols </div>
<table class="pagination" width="50%" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_fsProtocols > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_fsProtocols=%d%s", $currentPage, 0, $queryString_fsProtocols); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_fsProtocols > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_fsProtocols=%d%s", $currentPage, max(0, $pageNum_fsProtocols - 1), $queryString_fsProtocols); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_fsProtocols < $totalPages_fsProtocols) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_fsProtocols=%d%s", $currentPage, min($totalPages_fsProtocols, $pageNum_fsProtocols + 1), $queryString_fsProtocols); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_fsProtocols < $totalPages_fsProtocols) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_fsProtocols=%d%s", $currentPage, $totalPages_fsProtocols, $queryString_fsProtocols); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($fsProtocols);
?>