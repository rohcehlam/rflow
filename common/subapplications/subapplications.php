<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php'); 

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsSubapplications = 25;
$pageNum_rsSubapplications = 0;
if (isset($_GET['pageNum_rsSubapplications'])) {
  $pageNum_rsSubapplications = $_GET['pageNum_rsSubapplications'];
}
$startRow_rsSubapplications = $pageNum_rsSubapplications * $maxRows_rsSubapplications;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsSubapplications = "SELECT subapplicationID, subapplication FROM subapplications ORDER BY subapplication ASC";
$query_limit_rsSubapplications = sprintf("%s LIMIT %d, %d", $query_rsSubapplications, $startRow_rsSubapplications, $maxRows_rsSubapplications);
$rsSubapplications = mysql_query($query_limit_rsSubapplications, $connProdOps) or die(mysql_error());
$row_rsSubapplications = mysql_fetch_assoc($rsSubapplications);

if (isset($_GET['totalRows_rsSubapplications'])) {
  $totalRows_rsSubapplications = $_GET['totalRows_rsSubapplications'];
} else {
  $all_rsSubapplications = mysql_query($query_rsSubapplications);
  $totalRows_rsSubapplications = mysql_num_rows($all_rsSubapplications);
}
$totalPages_rsSubapplications = ceil($totalRows_rsSubapplications/$maxRows_rsSubapplications)-1;

$queryString_rsSubapplications = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsSubapplications") == false && 
        stristr($param, "totalRows_rsSubapplications") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsSubapplications = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsSubapplications = sprintf("&totalRows_rsSubapplications=%d%s", $totalRows_rsSubapplications, $queryString_rsSubapplications);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php buildHeaderNEW("subapplication", null, "subapplications", "Subapplications", "Add a Subapplication"); ?>
<div align="center">
<div class="casing" align="left">
<?php login(); ?>
  <table class="data" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th>Subapplication</th>
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
		<td><?php echo $row_rsSubapplications['subapplication']; ?></td>
		<?php sudoAuthData("subapplication.php", "Sub-application", "td", "edit", "function=update&amp;subapp=" . $row_rsSubapplications['subapplicationID']); ?>
    </tr>
    <?php } while ($row_rsSubapplications = mysql_fetch_assoc($rsSubapplications)); ?>
  </table>
<div id="count">Viewing <?php echo ($startRow_rsSubapplications + 1) ?> through <?php echo min($startRow_rsSubapplications + $maxRows_rsSubapplications, $totalRows_rsSubapplications) ?> of <?php echo $totalRows_rsSubapplications ?> Subapplications</div>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsSubapplications > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsSubapplications=%d%s", $currentPage, 0, $queryString_rsSubapplications); ?>"><img src="../../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsSubapplications > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsSubapplications=%d%s", $currentPage, max(0, $pageNum_rsSubapplications - 1), $queryString_rsSubapplications); ?>"><img src="../../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsSubapplications < $totalPages_rsSubapplications) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsSubapplications=%d%s", $currentPage, min($totalPages_rsSubapplications, $pageNum_rsSubapplications + 1), $queryString_rsSubapplications); ?>"><img src="../../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsSubapplications < $totalPages_rsSubapplications) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsSubapplications=%d%s", $currentPage, $totalPages_rsSubapplications, $queryString_rsSubapplications); ?>"><img src="../../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsSubapplications);
?>