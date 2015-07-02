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
<div align="center">Your new carrier has been created 
</div>
<div id="count"></div>
</body>
</html><?php
mysql_free_result($rsCarriers);
?>
