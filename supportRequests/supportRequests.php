<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	 'pageNum_rsMaintenanceNotifs' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'status' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	 'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS,
		  ), true);

$currentPage = $my_server["PHP_SELF"];

$varApplication_rsEscalations = (isset($my_get['app']) ? addslashes($my_get['app']) : "1");
$varCategory_rsEscalations = (isset($my_get['category']) ? addslashes($my_get['category']) : "1");
$varDepartment_rsEscalations = (isset($my_get['dept']) ? addslashes($my_get['dept']) : "1");
$varEmployee_rsEscalations = (isset($my_get['employee']) ? addslashes($my_get['employee']) : "1");
$varEscalation_rsEscalations = (isset($my_get['escalation']) ? addslashes($my_get['escalation']) : "1");
$varStatus_rsEscalations = (isset($my_get['status']) ? addslashes($my_get['status']) : "1");
$varSubject_rsEscalations = (isset($my_get['subject']) ? addslashes($my_get['subject']) : "1");
$varTicket_rsEscalations = (isset($my_get['ticket']) ? addslashes($my_get['ticket']) : "1");

$maxRows_rsEscalations = 50;
$pageNum_rsEscalations = (isset($my_get['pageNum_rsEscalations']) ? $my_get['pageNum_rsEscalations'] : 0);
$startRow_rsEscalations = $pageNum_rsEscalations * $maxRows_rsEscalations;

if (isset($my_get['app'])) {
	if (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, escalations.applicationID, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.applicationID=%s AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varApplication_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, escalations.applicationID, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.applicationID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varApplication_rsEscalations);
	}
} elseif (isset($my_get['category'])) {
	if (isset($my_get['status']) && ($my_get['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	} elseif (!isset($my_get['status']) || ($my_get['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.categoryID='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varCategory_rsEscalations);
	}
} elseif (isset($my_get['dept'])) {
	if (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.deptID=%s AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varDepartment_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh, departments.department FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID LEFT JOIN departments ON escalations.deptID=departments.departmentID WHERE escalations.deptID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varDepartment_rsEscalations);
	}
} elseif (isset($my_get['employee'])) {
	if (isset($my_get['status']) && ($my_get['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.assignedTo='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEmployee_rsEscalations);
	}
} elseif (isset($my_get['escalation'])) {
	$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.escalationID=%s ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varEscalation_rsEscalations);
} elseif (isset($my_get['subject'])) {
	if (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.subject REGEXP '%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varSubject_rsEscalations, $varStatus_rsEscalations);
	} else {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.subject REGEXP '%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varSubject_rsEscalations);
	}
} elseif (isset($my_get['ticket'])) {
	if (isset($my_get['status']) && ($my_get['status'] == "Not Closed")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] != "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations, $varStatus_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] == "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	} elseif (isset($my_get['status']) && ($my_get['status'] == "none")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priorityHigh FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.ticket='%s' AND (escalations.status<>'Returned' AND escalations.status<>'Closed') ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varTicket_rsEscalations);
	}
} elseif (isset($my_get['status'])) {
	if (isset($my_get['status']) && ($my_get['status'] == "Any")) {
		$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
	} elseif (isset($my_get['status']) && ($my_get['status'] == "Not Closed")) {
		$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE escalations.status <> 'Closed' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
	} elseif (isset($my_get['status']) && ($my_get['status'] != "Any")) {
		$query_rsEscalations = sprintf("SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, DATE_FORMAT(dateClosed, '%%m/%%d/%%Y') as dateUpdated, applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, employees.displayName AS receiver, escalations.status, escalations.ticket FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID LEFT JOIN employees ON escalations.assignedTo=employees.employeeID WHERE  escalations.status='%s' ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC", $varStatus_rsEscalations);
	}
} else {
	$query_rsEscalations = "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated"
			  . ", DATE_FORMAT(dateClosed, '%m/%d/%Y') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject"
			  . ", employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priority"
			  . " FROM escalations"
			  . " LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes"
			  . " ON escalations.categoryID=reporttypes.reportTypeID"
			  . " LEFT JOIN employees"
			  . " ON escalations.assignedTo=employees.employeeID"
			  . " WHERE (escalations.categoryID<>'15' AND escalations.categoryID<>'16') AND ( escalations.status<>'Returned' AND escalations.status<>'Closed')"
			  . " ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC";
}
$query_limit_rsEscalations = sprintf("%s LIMIT %d, %d", $query_rsEscalations, $startRow_rsEscalations, $maxRows_rsEscalations);
$rsEscalations = $conn->query($query_limit_rsEscalations) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsEscalations = $rsEscalations->fetch_assoc();

//select applications for application filter list
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = $conn->query($query_rsApps) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsApps = $rsApps->fetch_assoc();
$totalRows_rsApps = $rsApps->num_rows;

//Categories
if (isset($_SESSION['MM_Username'])) {
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
	$rsCategories = $conn->query($query_rsCategories) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsCategories = $rsCategories->fetch_assoc();
	$totalRows_rsCategories = $rsCategories->num_rows;
} else {
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes WHERE reportTypeID <> 15 ORDER BY reportType ASC";
	$rsCategories = $conn->query($query_rsCategories) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsCategories = $rsCategories->fetch_assoc();
	$totalRows_rsCategories = $rsCategories->num_rows;
}

//Departments
$query_rsDepartments = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartments = $conn->query($query_rsDepartments) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsDepartments = $rsDepartments->fetch_assoc();
$totalRows_rsDepartments = $rsDepartments->num_rows;

//Engineers
$query_rsAssignedTo = "SELECT employeeID, displayName FROM employees WHERE departmentID = 1 ORDER BY displayName ASC";
$rsAssignedTo = $conn->query($query_rsAssignedTo) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsAssignedTo = $rsAssignedTo->fetch_assoc();
$totalRows_rsAssignedTo = $rsAssignedTo->num_rows;

if (isset($my_get['totalRows_rsEscalations'])) {
	$totalRows_rsEscalations = $my_get['totalRows_rsEscalations'];
} else {
	$all_rsEscalations = $conn->query($query_rsEscalations) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$totalRows_rsEscalations = $all_rsEscalations->num_rows;
}
$totalPages_rsEscalations = ceil($totalRows_rsEscalations / $maxRows_rsEscalations) - 1;

$queryString_rsEscalations = "";
if (!empty($my_server['QUERY_STRING'])) {
	$params = explode("&", $my_server['QUERY_STRING']);
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
	if ($colorme == "Closed") {
		echo " class=\"escalationCleared\"";
	} else {
		if (($colorme >= "20")) {
			echo " class=\"escalationDegreeFour\"";
		} elseif (($colorme >= 12) && ($colorme < 20)) {
			echo " class=\"escalationDegreeThree\"";
		} elseif (($colorme >= 2) && ($colorme < 12)) {
			echo " class=\"escalationDegreeTwo\"";
		} elseif (($colorme >= 0) && ($colorme < 2)) {
			echo " class=\"escalationDegreeOne\"";
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Support Requests"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php build_header(); ?>
	</head>
	<body class="skin-blue layout-top-nav">

		<div class="wrapper">
			<header class="main-header">
				<?php build_navbar(2, !isset($_SESSION['employee']) ? "<li>\n<a href=\"index.php\"><span class='glyphicon glyphicon-log-in'></span>&nbsp;Login</a>\n</li>\n" : "<li><a href='#'>Welcome, {$row_rsEmployeeInfo['firstName']}!</a></li>\n<li><a href=\"$logoutAction\"><span class='glyphicon glyphicon-log-out'></span>&nbsp;Logout</a></li>\n") ?>
			</header> 
		</div>

		<div class="content-wrapper">

			<div class="container-fluid">

				<?php
				buildNewHeader('supportRequests.php', 'Support Requests', '', 'supportRequest.php', 'Add a Support Request');
				?>

				<div class="row">
					<table id='supportRequests_table' class="showMySettings table table-bordered table-striped">
						<thead>
							<tr>
								<th width="7%">Date<br />Requested</th>
								<th width="7%">Last<br />Updated</th>
								<th>ID</th>
								<th>Subject</th>
								<?php
								if (!isset($my_get['app'])) {
									echo "		<th>App</th>\n";
								}
								if (!isset($my_get['ticket'])) {
									echo "		<th>Ticket</th>\n";
								}
								if (!isset($my_get['employee'])) {
									echo "		<th>Engineer</th>\n";
								}
								if (!isset($my_get['status']) || ($my_get['status'] == "none") || ($my_get['status'] == "Any")) {
									echo "		<th>Status</th>\n";
								}
								?>
								<?php sudoAuthData(null, null, "th", "edit", null); ?>
							</tr>
						</thead>
						<tbody>
							<?php while ($row_rsEscalations = $rsEscalations->fetch_assoc()) { ?>
								<tr<?php
								if ($row_rsEscalations['status'] == "Closed") {
									colorcode($row_rsEscalations['status']);
								} else {
									colorcode($row_rsEscalations['rowcolor']);
								}
								?>>
									<td><?php echo $row_rsEscalations['dateEscalated']; ?></td>
									<td><?php echo $row_rsEscalations['dateUpdated']; ?></td>
									<td><a href="supportRequest.php?supportRequest=<?php echo $row_rsEscalations['escalationID']; ?>&amp;function=view<?php
										if (isset($row_rsEscalations['categoryID']) == "15") {
											echo "&amp;category=internal";
										}
										?>"><?php echo $row_rsEscalations['escalationID']; ?></a></td>
										<?php
										echo "		<td><a href=\"supportRequest.php?supportRequest=" . $row_rsEscalations['escalationID'] . "&amp;function=view";
										if (isset($row_rsEscalations['categoryID']) == "15") {
											echo "&amp;category=internal";
										}
										echo "\">" . stripslashes($row_rsEscalations['subject']) . "</a></td>\n";
										if (!isset($my_get['app'])) {
											echo "		<td>" . $row_rsEscalations['application'] . "</td>\n";
										}
										if (!isset($my_get['ticket'])) {
											echo "		<td>";
											if ($row_rsEscalations['ticket'] == "0") {
												echo "-";
											} else {
												echo $row_rsEscalations['ticket'];
											}
											echo "</td>\n";
										}
										if (!isset($my_get['employee'])) {
											echo "		<td nowrap=\"nowrap\">" . $row_rsEscalations['receiver'] . "</td>\n";
										}
										if (!isset($my_get['status']) || ($my_get['status'] == "none") || ($my_get['status'] == "Any")) {
											echo "		<td nowrap=\"nowrap\">" . $row_rsEscalations['status'] . "</td>\n";
										}
										?>
										<?php sudoAuthData("supportRequest.php", "Update Support Request", "td", "edit", "function=update&amp;supportRequest=" . $row_rsEscalations['escalationID']); ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<script type="text/javascript">
						$(document).ready(function () {
							$('#supportRequests_table').dataTable();
						});
					</script>

				</div> <!-- /row -->

			</div> <!-- /container -->
		</div> <!-- /content-wrapper -->

		<?php build_footer(); ?>
	</body>
</html>