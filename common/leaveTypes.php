<?php require_once('../Connections/connProdOps.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsLeaveTypes = 17;
$pageNum_rsLeaveTypes = 0;
if (isset($_GET['pageNum_rsLeaveTypes'])) {
  $pageNum_rsLeaveTypes = $_GET['pageNum_rsLeaveTypes'];
}
$startRow_rsLeaveTypes = $pageNum_rsLeaveTypes * $maxRows_rsLeaveTypes;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsLeaveTypes = "SELECT leaveTypeID, leaveType FROM leavetypes ORDER BY leaveType ASC";
$query_limit_rsLeaveTypes = sprintf("%s LIMIT %d, %d", $query_rsLeaveTypes, $startRow_rsLeaveTypes, $maxRows_rsLeaveTypes);
$rsLeaveTypes = mysql_query($query_limit_rsLeaveTypes, $connProdOps) or die(mysql_error());
$row_rsLeaveTypes = mysql_fetch_assoc($rsLeaveTypes);

if (isset($_GET['totalRows_rsLeaveTypes'])) {
  $totalRows_rsLeaveTypes = $_GET['totalRows_rsLeaveTypes'];
} else {
  $all_rsLeaveTypes = mysql_query($query_rsLeaveTypes);
  $totalRows_rsLeaveTypes = mysql_num_rows($all_rsLeaveTypes);
}
$totalPages_rsLeaveTypes = ceil($totalRows_rsLeaveTypes/$maxRows_rsLeaveTypes)-1;

$queryString_rsLeaveTypes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsLeaveTypes") == false && 
        stristr($param, "totalRows_rsLeaveTypes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsLeaveTypes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsLeaveTypes = sprintf("&totalRows_rsLeaveTypes=%d%s", $totalRows_rsLeaveTypes, $queryString_rsLeaveTypes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Leave Types</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<?php require_once('../inc/js/js.php'); ?>
<?php require_once('../inc/functions.php'); ?>
</head>
<body>
<?php buildHeader("leaveType", null, "leaveTypes", "Leave Types", "Add a Leave Type"); ?>
<div align="center">
<div class="casing"><br />
<table class="data" align="center" cellspacing="0">
  <tr>
	<th>Leave Type</th>
	<th>&nbsp;</th>
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
	<td><a href="leaveTypeAdd.php?function=view&leaveType=<?php echo $row_rsLeaveTypes['leaveTypeID']; ?>"><?php echo $row_rsLeaveTypes['leaveType']; ?></a></td>
	<td><a href="leaveTypeAdd.php?function=update&leaveType=<?php echo $row_rsLeaveTypes['leaveTypeID']; ?>"><img src="../images/icons/edit.gif" border="0" /></a></td>
    </tr>
  <?php } while ($row_rsLeaveTypes = mysql_fetch_assoc($rsLeaveTypes)); ?>
</table>
<br /> 
<div id="count">Viewing <?php echo ($startRow_rsLeaveTypes + 1) ?> through <?php echo min($startRow_rsLeaveTypes + $maxRows_rsLeaveTypes, $totalRows_rsLeaveTypes) ?> of <?php echo $totalRows_rsLeaveTypes ?> Leave Types</div><br />
<table border="0" width="50%" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsLeaveTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsLeaveTypes=%d%s", $currentPage, 0, $queryString_rsLeaveTypes); ?>"><img src="../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsLeaveTypes > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsLeaveTypes=%d%s", $currentPage, max(0, $pageNum_rsLeaveTypes - 1), $queryString_rsLeaveTypes); ?>"><img src="../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsLeaveTypes < $totalPages_rsLeaveTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsLeaveTypes=%d%s", $currentPage, min($totalPages_rsLeaveTypes, $pageNum_rsLeaveTypes + 1), $queryString_rsLeaveTypes); ?>"><img src="../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsLeaveTypes < $totalPages_rsLeaveTypes) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsLeaveTypes=%d%s", $currentPage, $totalPages_rsLeaveTypes, $queryString_rsLeaveTypes); ?>"><img src="../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsLeaveTypes);
?>