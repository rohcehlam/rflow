<?php require_once('../../Connections/connProdOps.php');
	require_once('../../inc/functions.php'); ?><?php
// Validate request to login to this site.
session_start();

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
  session_register('PrevUrl');
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "groupID";
  $MM_redirectLoginSuccess = "employees.php";
  $MM_redirectLoginFailed = $_SERVER['PHP_SELF'];
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_connProdOps, $connProdOps);
  	
  $LoginRS__query=sprintf("SELECT workEmail, password, groupID FROM employees WHERE workEmail='%s' AND password='%s'",
  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $connProdOps) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    $loginStrGroup  = mysql_result($LoginRS,0,'groupID');

    //declare two session variables and assign them
    $GLOBALS['MM_Username'] = $loginUsername;
    $GLOBALS['MM_UserGroup'] = $loginStrGroup;	      

    //register the session variables 
    $_SESSION["MM_Username"] = $loginUsername;
    $_SESSION["MM_UserGroup"] = $loginStrGroup;

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  } else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Production Operations Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<div align="center">
<div class="casing" align="left">
<form action="<?php echo $loginFormAction; ?>" name="login" method="post">
<h3>Sign In</h3>
<table class="view" align="center" cellspacing="0" cellpadding="2">
	<tr> 
		<td class="contrast"><label for="username">Email:</label></td>
		<td><input id="username" name="username" type="text" size="25" /></td>
	</tr><tr>
		<td class="contrast"><label for="password">Password:</label></td>
		<td><input id="password" name="password" type="password" size="25" /></td>
	</tr>
	<tr class="button"><td colspan="2"><input type="submit" name="login" value="Sign in" /></td></tr>
 </table>
<input type="hidden" name="loginmein" id="loginmein" value="logmein" />
</form>
</div>
</div>
</body>
</html>