<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php'); ?><?php
session_start();
$currentPage = $_SERVER["PHP_SELF"];

$varApplication_rsEscalations = "1";
if (isset($_GET['app'])) {
  $varApplication_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['app'] : addslashes($_GET['app']);
}
$varCategory_rsEscalations = "1";
if (isset($_GET['category'])) {
  $varCategory_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['category'] : addslashes($_GET['category']);
}
$varDepartment_rsEscalations = "1";
if (isset($_GET['dept'])) {
  $varDepartment_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['dept'] : addslashes($_GET['dept']);
}
$varEmployee_rsEscalations = "1";
if (isset($_GET['employee'])) {
  $varEmployee_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
}
$varEscalation_rsEscalations = "1";
if (isset($_GET['escalation'])) {
  $varEscalation_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['escalation'] : addslashes($_GET['escalation']);
}
$varStatus_rsEscalations = "1";
if (isset($_GET['status'])) {
  $varStatus_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['status'] : addslashes($_GET['status']);
}
$varSubject_rsEscalations = "1";
if (isset($_GET['subject'])) {
  $varSubject_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['subject'] : addslashes($_GET['subject']);
}
$varTicket_rsEscalations = "1";
if (isset($_GET['ticket'])) {
  $varTicket_rsEscalations = (get_magic_quotes_gpc()) ? $_GET['ticket'] : addslashes($_GET['ticket']);
}

$maxRows_rsEscalations = 50;
$pageNum_rsEscalations = 0;
if (isset($_GET['pageNum_rsEscalations'])) {
  $pageNum_rsEscalations = $_GET['pageNum_rsEscalations'];
}
$startRow_rsEscalations = $pageNum_rsEscalations * $maxRows_rsEscalations;

mysql_select_db($database_connProdOps, $connProdOps);
if (isset($_GET['app'])) {
	if (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, escalations.applicationID, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.applicationID=%s AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varApplication_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, escalations.applicationID, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.applicationID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varApplication_rsEscalations);
	}
} elseif (isset($_GET['category'])) {
	if (isset($_GET['status']) && ($_GET['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	} elseif (!isset($_GET['status']) || ($_GET['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	}
} elseif (isset($_GET['dept'])) {
	if (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.deptID=%s AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varDepartment_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.deptID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varDepartment_rsEscalations);
	}
} elseif (isset($_GET['employee'])) {
	if (isset($_GET['status']) && ($_GET['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	}
} elseif (isset($_GET['escalation'])) {
	$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.escalationID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEscalation_rsEscalations);
} elseif (isset($_GET['subject'])) {
	if (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.subject REGEXP '%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varSubject_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.subject REGEXP '%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varSubject_rsEscalations);
	}
} elseif (isset($_GET['ticket'])) {
	if (isset($_GET['status']) && ($_GET['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	} elseif (isset($_GET['status']) && ($_GET['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	}
} elseif (isset($_GET['status'])) {
	if (isset($_GET['status']) && ($_GET['status'] == "Any")) {
		$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
	} elseif (isset($_GET['status']) && ($_GET['status'] == "Not Closed")) {
		$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
	} elseif (isset($_GET['status']) && ($_GET['status'] != "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE  escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varStatus_rsEscalations);
	}
} else {
	$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priority FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE (escalations.categoryID<>'15' AND escalations.categoryID<>'16') AND ( escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
}
$query_limit_rsEscalations = sprintf("%s LIMIT %d, %d", $query_rsEscalations, $startRow_rsEscalations, $maxRows_rsEscalations);
$rsEscalations = mysql_query($query_limit_rsEscalations, $connProdOps) or die(mysql_error());
$row_rsEscalations = mysql_fetch_assoc($rsEscalations);

//select applications for application filter list
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = mysql_query($query_rsApps, $connProdOps) or die(mysql_error());
$row_rsApps = mysql_fetch_assoc($rsApps);
$totalRows_rsApps = mysql_num_rows($rsApps);

//Categories
if (isset($_SESSION['MM_Username'])) {
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
	$rsCategories = mysql_query($query_rsCategories, $connProdOps) or die(mysql_error());
	$row_rsCategories = mysql_fetch_assoc($rsCategories);
	$totalRows_rsCategories = mysql_num_rows($rsCategories);
} else {
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes WHERE reportTypeID <> 15 ORDER BY reportType ASC";
	$rsCategories = mysql_query($query_rsCategories, $connProdOps) or die(mysql_error());
	$row_rsCategories = mysql_fetch_assoc($rsCategories);
	$totalRows_rsCategories = mysql_num_rows($rsCategories);
}	

//Departments
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDepartments = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartments = mysql_query($query_rsDepartments, $connProdOps) or die(mysql_error());
$row_rsDepartments = mysql_fetch_assoc($rsDepartments);
$totalRows_rsDepartments = mysql_num_rows($rsDepartments);

//Engineers
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsAssignedTo = "SELECT employeeID, displayName FROM employees WHERE departmentID = 1 ORDER BY displayName ASC";
$rsAssignedTo = mysql_query($query_rsAssignedTo, $connProdOps) or die(mysql_error());
$row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo);
$totalRows_rsAssignedTo = mysql_num_rows($rsAssignedTo);

if (isset($_GET['totalRows_rsEscalations'])) {
  $totalRows_rsEscalations = $_GET['totalRows_rsEscalations'];
} else {
  $all_rsEscalations = mysql_query($query_rsEscalations);
  $totalRows_rsEscalations = mysql_num_rows($all_rsEscalations);
}
$totalPages_rsEscalations = ceil($totalRows_rsEscalations/$maxRows_rsEscalations)-1;

$queryString_rsEscalations = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsEscalations") == false && 
        stristr($param, "totalRows_rsEscalations") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsEscalations = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsEscalations = sprintf("&totalRows_rsEscalations=%d%s", $totalRows_rsEscalations, $queryString_rsEscalations);

function colorCode($colorme) {
	if($colorme == "Closed") {
		echo " class=\"escalationCleared\"";
	} else {
		if(($colorme >= "20")) {
			echo " class=\"escalationDegreeFour\"";
		} elseif(($colorme >= 12) && ($colorme < 20)) {
			echo " class=\"escalationDegreeThree\"";
		} elseif(($colorme >= 2) && ($colorme < 12)) {
			echo " class=\"escalationDegreeTwo\"";
		} elseif(($colorme >= 0) && ($colorme < 2)) {
			echo " class=\"escalationDegreeOne\"";
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Support Requests"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link href="../inc/menu.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/menu.js"></script>
	<script type="text/javascript" src="../inc/js/js.js"></script>
	<script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>
	<?php include_once("../inc/js/escalationsTabs.php"); ?>
</head>
<body>
<?php buildMenu(); ?>
<script type="text/javascript">
dolphintabs.init("menunav", 2)
</script>
<!-- <iframe src="supportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
<div class="casing" align="left">
<?php buildHeader("supportRequest", null, "supportRequest", "Support Requests", "Add a Support Request"); ?>
      <div id="tabs">
	  	<span class="<?php if ((isset($_GET['app'])) || (isset($_GET['category'])) || (isset($_GET['department'])) || (isset($_GET['employee'])) || (isset($_GET['escalation'])) || (isset($_GET['status'])) || (isset($_GET['subject'])) || (isset($_GET['ticket']))) { echo "tabbak"; } else { echo "tabfor"; } ?>" id="tab_none"><a href="supportRequests.php?filter=none">No Filter</a></span>
		<?php tab("app","Application"); ?>
		<?php tab("category","Category"); ?>
		<?php tab("department","Department"); ?>
		<?php tab("engineer","Engineer"); ?>
		<?php tab("escalation","ID"); ?>
		<span class="<?php if ((isset($_GET['status'])) && (!isset($_GET['app'])) && (!isset($_GET['category'])) && (!isset($_GET['department'])) && (!isset($_GET['employee'])) && (!isset($_GET['escalation'])) && (!isset($_GET['subject'])) && (!isset($_GET['ticket']))) { echo "tabfor"; } else { echo "tabbak"; } ?>" id="tab_status"><a href="#tabstatus" onclick="return showTab('status')">Status</a></span>
		<?php tab("subject","Subject"); ?>
		<?php tab("ticket","Ticket"); ?>
      <!-- TABS BODY -->
      <div id="tabscontent" style="width: 95%;">
        <!-- DETAILS -->
        <a name="tabnone" id="tabnone"></a>
        <div id="tabscontent_none"<?php if ((isset($_GET['app'])) || (isset($_GET['category'])) || (isset($_GET['department'])) || (isset($_GET['employee'])) || (isset($_GET['escalation'])) || (isset($_GET['status'])) || (isset($_GET['subject'])) || (isset($_GET['ticket']))) { echo " style=\"display: none;\""; } ?>>Select a tab to filter the available Support Requests</div>
        <a name="tabapp" id="tabapp"></a>
        <div id="tabscontent_app"<?php if (!isset($_GET['app'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterApp" id="filterApp">
            Display Support Requests for
              <select name="app" id="app">
              <option value="">Select Application</option>
              <?php do { ?>
              <option value="<?php echo $row_rsApps['applicationID']?>"<?php if (isset($_GET['app']) && ($row_rsApps['applicationID'] == $_GET['app'])) { echo " selected=\"selected\"";} ?>><?php echo $row_rsApps['application']?></option>
              <?php } while ($row_rsApps = mysql_fetch_assoc($rsApps));
  $rows = mysql_num_rows($rsApps);
  if($rows > 0) {
      mysql_data_seek($rsApps, 0);
	  $row_rsApps = mysql_fetch_assoc($rsApps);
  }
?>
            </select> that are <select name="status">
              <option value="none">Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabcategory" id="tabcategory"></a>
        <div id="tabscontent_category"<?php if (!isset($_GET['category'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterCategory" id="filterCategory">
            Display Support Requests for 
            <select name="category" id="category">
              <option value="">Select Category</option>
              <?php
do {  
?>
              <option value="<?php echo $row_rsCategories['reportTypeID']?>"<?php if (isset($_GET['category']) && ($row_rsCategories['reportTypeID'] == $_GET['category'])) { echo " selected=\"selected\"";} ?>><?php echo $row_rsCategories['reportType']?></option>
              <?php
} while ($row_rsCategories = mysql_fetch_assoc($rsCategories));
  $rows = mysql_num_rows($rsCategories);
  if($rows > 0) {
      mysql_data_seek($rsCategories, 0);
	  $row_rsCategories = mysql_fetch_assoc($rsCategories);
  }
?>
            </select> that are <select name="status">
              <option value="none"<?php if (!(strcmp("none", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabdepartment" id="tabdepartment"></a>
        <div id="tabscontent_department"<?php if (!isset($_GET['department'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterDepartment" id="filterDepartment">
            Display Support Requests for 
            <select name="department" id="department">
              <option value="">Select Department</option>
              <?php
do {  
?>
              <option value="<?php echo $row_rsDepartments['departmentID']?>"<?php if (isset($_GET['department']) && ($row_rsDepartments['departmentID'] == $_GET['department'])) { echo " selected=\"selected\"";} ?>><?php echo $row_rsDepartments['department']?></option>
              <?php
} while ($row_rsDepartments = mysql_fetch_assoc($rsDepartments));
  $rows = mysql_num_rows($rsDepartments);
  if($rows > 0) {
      mysql_data_seek($rsDepartments, 0);
	  $row_rsDepartments = mysql_fetch_assoc($rsDepartments);
  }
?>
            </select> that are <select name="status">
              <option value="none"<?php if (!(strcmp("none", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabengineer" id="tabengineer"></a>
        <div id="tabscontent_engineer"<?php if (!isset($_GET['engineer'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterEngineer" id="filterEngineer">
            Display Support Requests assigned to
              <select name="engineer" id="engineer">
                <option value="">Select Engineer</option>
                <?php
do {  
?>
                <option value="<?php echo $row_rsAssignedTo['employeeID']?>"<?php if (isset($_GET['engineer']) && ($row_rsAssignedTo['employeeID'] == $_GET['engineer'])) { echo " selected=\"selected\"";} ?>><?php echo $row_rsAssignedTo['displayName']?></option>
                <?php
} while ($row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo));
  $rows = mysql_num_rows($rsAssignedTo);
  if($rows > 0) {
      mysql_data_seek($rsAssignedTo, 0);
	  $row_rsAssignedTo = mysql_fetch_assoc($rsAssignedTo);
  }
?>
              </select> that are <select name="status">
              <option value="none"<?php if (!(strcmp("none", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabescalation" id="tabescalation"></a>
        <div id="tabscontent_escalation"<?php if (!isset($_GET['escalation'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterEscalation" id="filterEscalation">
            Display Support Request # <input type="text" name="escalation" id="escalation"<?php if (isset($_GET['escalation'])) { echo " value=\"" . $_GET['escalation'] . "\""; } ?> size="10" />
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabstatus" id="tabstatus"></a>
        <div id="tabscontent_status"<?php if ((!isset($_GET['status'])) || (isset($_GET['app'])) || (isset($_GET['category'])) || (isset($_GET['department'])) || (isset($_GET['employee'])) || (isset($_GET['subject'])) || (isset($_GET['ticket']))) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterStatus" id="filterStatus">
            Display Support Requests that are <select name="status">
              <option value=""<?php if (!(strcmp("", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Any"<?php if (!(strcmp("Any", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Any</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabsubject" id="tabsubject"></a>
        <div id="tabscontent_subject"<?php if (!isset($_GET['subject'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterSubject" id="filterSubject">
				Display Support Requests with <input type="text" name="subject" id="subject"<?php if (isset($_GET['subject'])) { echo " value=\"" . $_GET['subject'] . "\""; } ?> size="10" /> in the Subject, and that are <select name="status">
              <option value="none"<?php if (!(strcmp("none", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
				<input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabticket" id="tabticket"></a>
        <div id="tabscontent_ticket"<?php if (!isset($_GET['ticket'])) { echo " style=\"display: none;\""; } ?>>
          <form action="supportRequests.php" method="get" name="filterTicket" id="filterTicket">
            Display Support Requests for Ticket # <input type="text" name="ticket" id="ticket"<?php if (isset($_GET['ticket'])) { echo " value=\"" . $_GET['ticket'] . "\""; } ?> size="10" /> that are <select name="status">
              <option value="none"<?php if (!(strcmp("none", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Select Status</option>
              <option value="Open"<?php if (!(strcmp("Open", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Open</option>
			  <option value="Analysis"<?php if (!(strcmp("Analysis", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Analysis</option>
              <option value="Closed"<?php if (!(strcmp("Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Closed</option>
              <option value="In Progress"<?php if (!(strcmp("In Progress", $_GET['status']))) {echo " selected=\"selected\"";} ?>>In Progress</option>
              <option value="Not Closed"<?php if (!(strcmp("Not Closed", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Not Closed</option>
              <option value="On Hold"<?php if (!(strcmp("On Hold", $_GET['status']))) {echo " selected=\"selected\"";} ?>>On Hold</option>
              <option value="Returned"<?php if (!(strcmp("Returned", $_GET['status']))) {echo " selected=\"selected\"";} ?>>Returned</option>
			</select>&nbsp;(optional)
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
      </div>
	</div>

<br /><?php
//Start filter headers
if (isset($_GET['app'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests for <em>" . $row_rsEscalations['application'] . "</em></h3>\n";
} elseif (isset($_GET['category'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests in the <em>" . $row_rsEscalations['category'] . "</em> category</h3>\n";
} elseif (isset($_GET['department'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests for <em>" . $row_rsEscalations['department'] . "</em></h3>\n";
} elseif (isset($_GET['employee'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests assigned to <em>" . $row_rsEscalations['receiver'] . "</em></h3>\n";
} elseif (isset($_GET['escalation'])) {
	echo "<h3><em>Support Requests #" . $_GET['escalation'] . "</em></h3>\n";
} elseif ((isset($_GET['status'])) && (!isset($_GET['app'])) && (!isset($_GET['category'])) && (!isset($_GET['department'])) && (!isset($_GET['employee'])) && (!isset($_GET['subject'])) && (!isset($_GET['ticket']))) {
	echo "<h3>Support Requests that are <em>" . $_GET['status'] . "</em></h3>\n";
} elseif (isset($_GET['subject'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests with the Subject containing <em>" . $_GET['subject'] . "</em></h3>\n";
} elseif (isset($_GET['ticket'])) {
	echo "<h3>";
	if(isset($_GET['status']) && ($_GET['status'] != "none")) {
		echo "<em>" . $_GET['status'] . "</em> ";
	}
	echo "Support Requests with the <em>Ticket #" . $_GET['ticket'] . "</em></h3>\n";
} ?>

<table align="center" cellpadding="2" cellspacing="0" class="data">
	<tr>
		<th width="7%">Date<br />Requested</th>
		<th width="7%">Last<br />Updated</th>
		<th>ID</th>
		<th>Subject</th>
<?php if (!isset($_GET['app'])) {
	echo "		<th>App</th>\n";
}
if (!isset($_GET['ticket'])) {
	echo "		<th>Ticket</th>\n";
}
if (!isset($_GET['employee'])) {
	echo "		<th>Engineer</th>\n";
}
if (!isset($_GET['status']) || ($_GET['status'] == "none") || ($_GET['status'] == "Any")) {
	echo "		<th>Status</th>\n";
} ?>
	<?php sudoAuthData(null, null, "th", "edit", null); ?>
	</tr>
  <?php do { ?>
	<tr<?php if ($row_rsEscalations['status'] == "Closed") {
				colorcode($row_rsEscalations['status']);
			} else {
				colorcode($row_rsEscalations['rowcolor']);
			} ?>>
		<td><?php echo $row_rsEscalations['dateEscalated']; ?></td>
		<td><?php echo $row_rsEscalations['dateUpdated']; ?></td>
		<td><a href="supportRequest.php?supportRequest=<?php echo $row_rsEscalations['escalationID']; ?>&amp;function=view<?php if (isset($row_rsEscalations['categoryID']) == "15") { echo "&amp;category=internal"; } ?>"><?php echo $row_rsEscalations['escalationID']; ?></a></td>
<?php echo "		<td><a href=\"supportRequest.php?supportRequest=" . $row_rsEscalations['escalationID'] . "&amp;function=view";
		if (isset($row_rsEscalations['categoryID']) == "15") { echo "&amp;category=internal"; } 
		echo "\">" . stripslashes($row_rsEscalations['subject']) . "</a></td>\n";
if (!isset($_GET['app'])) {
	echo "		<td>" . $row_rsEscalations['application'] . "</td>\n";
}
if (!isset($_GET['ticket'])) {
	echo "		<td>";
	if ($row_rsEscalations['ticket'] == "0") {
		echo "-";
	} else {
		echo $row_rsEscalations['ticket'];
	}
	echo "</td>\n";
}
if (!isset($_GET['employee'])) {
	echo "		<td nowrap=\"nowrap\">" . $row_rsEscalations['receiver'] . "</td>\n";
}
if (!isset($_GET['status']) || ($_GET['status'] == "none") || ($_GET['status'] == "Any")) {
	echo "		<td nowrap=\"nowrap\">" . $row_rsEscalations['status'] . "</td>\n";
} ?>
	<?php sudoAuthData("supportRequest.php", "Update Support Request", "td", "edit", "function=update&amp;supportRequest=" . $row_rsEscalations['escalationID']); ?>
	</tr>
  <?php } while ($row_rsEscalations = mysql_fetch_assoc($rsEscalations)); ?>
</table>
<div id="count">Viewing <?php echo ($startRow_rsEscalations + 1) ?> through <?php echo min($startRow_rsEscalations + $maxRows_rsEscalations, $totalRows_rsEscalations) ?> of <?php echo $totalRows_rsEscalations ?> Support Requests</div>
<?php if ($totalRows_rsEscalations > 50) { 
	?><table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsEscalations > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsEscalations=%d%s", $currentPage, 0, $queryString_rsEscalations); ?>"><img src="../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?></td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsEscalations > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsEscalations=%d%s", $currentPage, max(0, $pageNum_rsEscalations - 1), $queryString_rsEscalations); ?>"><img src="../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?></td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsEscalations < $totalPages_rsEscalations) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsEscalations=%d%s", $currentPage, min($totalPages_rsEscalations, $pageNum_rsEscalations + 1), $queryString_rsEscalations); ?>"><img src="../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?></td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsEscalations < $totalPages_rsEscalations) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsEscalations=%d%s", $currentPage, $totalPages_rsEscalations, $queryString_rsEscalations); ?>"><img src="../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php } ?>
<?php buildFooter("0"); ?>
</div>
</body>
</html><?php
mysql_free_result($rsEscalations);
mysql_free_result($rsApps);
mysql_free_result($rsCategories);
mysql_free_result($rsDepartments);
mysql_free_result($rsAssignedTo);
?>