<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php'); ?><?php

// *** Validate request to login to this site.
session_start();

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['ref'])) {
  $GLOBALS['PrevUrl'] = $_GET['ref'];
  $_SESSION["PrevUrl"] = $_GET['ref'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "myPortal.php";
  $MM_redirectLoginFailed = "index.php?loginFailed=y";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_connProdOps, $connProdOps);

  $LoginRS__query=sprintf("SELECT workEmail, password FROM employees WHERE workEmail='%s' AND password='%s'",
    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password));

	$colname_rsGetUser = (get_magic_quotes_gpc()) ? $_POST['username'] : addslashes($_POST['username']);
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsGetUser = sprintf("SELECT employeeID, workEmail, firstName, groupID, password, departmentID FROM employees WHERE workEmail='%s' AND password='%s'",
		get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password));
	$rsGetUser = mysql_query($query_rsGetUser, $connProdOps) or die(mysql_error());
	$row_rsGetUser = mysql_fetch_assoc($rsGetUser);
	$totalRows_rsGetUser = mysql_num_rows($rsGetUser);

  $LoginRS = mysql_query($LoginRS__query, $connProdOps) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";

    //declare two session variables and assign them
    $GLOBALS['MM_Username'] = $loginUsername;
    $GLOBALS['MM_UserGroup'] = $loginStrGroup;
    $GLOBALS['employee'] = $row_rsGetUser['employeeID'];
    $GLOBALS['firstName'] = $row_rsGetUser['firstName'];
    $GLOBALS['group'] = $row_rsGetUser['groupID'];
    $GLOBALS['dept'] = $row_rsGetUser['deptID'];

    //register the session variables
    $_SESSION["MM_Username"] = $loginUsername;
    $_SESSION["MM_UserGroup"] = $loginStrGroup;
    $_SESSION["employee"] = $row_rsGetUser['employeeID'];
    $_SESSION["firstName"] = $row_rsGetUser['firstName'];
    $_SESSION["group"] = $row_rsGetUser['groupID'];
    $_SESSION["dept"] = $row_rsGetUser['deptID'];
    
    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>masFlight - Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link href="../inc/menu.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/menu.js"></script>
	<script type="text/javascript" src="../inc/js/js.js"></script>
</head>
<body>
<?php buildMenu(); ?>
<script type="text/javascript">
dolphintabs.init("menunav", 1)
</script>
<div class="casing" align="left">
<?php buildHeader("myPortal", null, "myPortal", "My Portal", null); ?>
<form action="<?php echo $loginFormAction; ?>" name="loginForm" id="loginForm" method="post">
<?php if($_GET['loggedoff'] == "y") {
			echo "<div class=\"successful\" style=\"text-align: center;\">You have been successfully logged off.</div>";
		} elseif ($_GET['loginFailed'] == "y") {
			echo "<div class=\"problem\" style=\"text-align: center;\">Please try logging in again.</div>";
		} ?>
<table class="login" align="center" cellpadding="2" cellspacing="0">
	<tr class="title"><td colspan="2"><h3>Sign In</h3></td></tr>
	<tr>
		<td class="contrast"><label for="username">Email:</label></td>
		<td><input type="text" name="username" id="username" tabindex="1" size="25" value="" /></td>
	</tr>
	<tr>
		<td class="contrast"><label for="password">Password:</label></td>
		<td><input type="password" name="password" id="password" tabindex="2" size="25" /></td>
	</tr>
	<tr class="button"><td colspan="2"><input type="submit" name="login" id="login" tabindex="3" value="Log in" /></td></tr>
 </table>
</form>
<?php buildFooter("0"); ?>
</div>
</body>
</html><?php
if (isset($_POST['username'])) {
	mysql_free_result($rsGetUser);
}?>