<?php require_once('../Connections/connProdOps.php'); ?>
<?php require_once('../inc/functions.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsEmployees = 25;
$pageNum_rsEmployees = 0;
if (isset($_GET['pageNum_rsEmployees'])) {
  $pageNum_rsEmployees = $_GET['pageNum_rsEmployees'];
}
$startRow_rsEmployees = $pageNum_rsEmployees * $maxRows_rsEmployees;

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT * FROM employees ORDER BY employees.displayName ASC";
$query_limit_rsEmployees = sprintf("%s LIMIT %d, %d", $query_rsEmployees, $startRow_rsEmployees, $maxRows_rsEmployees);
$rsEmployees = mysql_query($query_limit_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);

if (isset($_GET['totalRows_rsEmployees'])) {
  $totalRows_rsEmployees = $_GET['totalRows_rsEmployees'];
} else {
  $all_rsEmployees = mysql_query($query_rsEmployees);
  $totalRows_rsEmployees = mysql_num_rows($all_rsEmployees);
}
$totalPages_rsEmployees = ceil($totalRows_rsEmployees/$maxRows_rsEmployees)-1;

$queryString_rsEmployees = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsEmployees") == false && 
        stristr($param, "totalRows_rsEmployees") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsEmployees = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsEmployees = sprintf("&totalRows_rsEmployees=%d%s", $totalRows_rsEmployees, $queryString_rsEmployees);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Employees</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
<?php include_once('../inc/js/js.php'); ?>
</head>
<body>
<?php buildHeader("employee", null, "employees", "Employees", "Add an Employee"); ?>
<div align="center">
<div class="casing"><br />
	<table class="data" align="center" cellspacing="0">
	<tr><th>
		Employee</th><th colspan="2">
		&nbsp;
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
	  <a title="View all phones checked out to <?php echo $row_rsEmployees['displayName']; ?>" href="phonesXemployee.php?employee=<?php echo $row_rsEmployees['employeeID']; ?>"><?php echo $row_rsEmployees['displayName']; ?></a></td><td class="icon">
	  <a title="Edit details for <?php echo $row_rsEmployees['displayName']; ?>" href="employeesUpdate.php?employee=<?php echo $row_rsEmployees['employeeID']; ?>"><img src="../images/icons/edit.gif" /></a></td><td class="icon">
	  <img src="../images/icons/delete.gif" />
	</td></tr>
  <?php } while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsEmployees + 1) ?> through <?php echo min($startRow_rsEmployees + $maxRows_rsEmployees, $totalRows_rsEmployees) ?> of <?php echo $totalRows_rsEmployees ?> employees</div><br />
<table border="0" width="50%" align="center">
	<tr><td align="center" class="icon">
		<?php if ($pageNum_rsEmployees > 0) { // Show if not first page ?>
			<a href="<?php printf("%s?pageNum_rsEmployees=%d%s", $currentPage, 0, $queryString_rsEmployees); ?>"><img src="../images/icons/first.jpg" /></a>
		<?php } // Show if not first page ?>
	</td><td align="center" class="icon">
		<?php if ($pageNum_rsEmployees > 0) { // Show if not first page ?>
			<a href="<?php printf("%s?pageNum_rsEmployees=%d%s", $currentPage, max(0, $pageNum_rsEmployees - 1), $queryString_rsEmployees); ?>"><img src="../images/icons/prev.jpg" /></a>
		<?php } // Show if not first page ?>
	</td><td align="center" class="icon">
		<?php if ($pageNum_rsEmployees < $totalPages_rsEmployees) { // Show if not last page ?>
			<a href="<?php printf("%s?pageNum_rsEmployees=%d%s", $currentPage, min($totalPages_rsEmployees, $pageNum_rsEmployees + 1), $queryString_rsEmployees); ?>"><img src="../images/icons/next.jpg" /></a>
		<?php } // Show if not last page ?>
	</td><td align="center" class="icon">
		<?php if ($pageNum_rsEmployees < $totalPages_rsEmployees) { // Show if not last page ?>
			<a href="<?php printf("%s?pageNum_rsEmployees=%d%s", $currentPage, $totalPages_rsEmployees, $queryString_rsEmployees); ?>"><img src="../images/icons/final.jpg" /></a>
		<?php } // Show if not last page ?>
	</td></tr>
</table><?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsEmployees);
?>
