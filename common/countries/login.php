<?php require_once('../../Connections/connProdOps.php'); ?><?php
// *** Validate request to login to this site.
session_start();

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
  session_register('PrevUrl');
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "countries.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = true;
  mysql_select_db($database_connProdOps, $connProdOps);
  
  $LoginRS__query=sprintf("SELECT workEmail, password FROM employees WHERE workEmail='%s' AND password='%s'",
    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $connProdOps) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $GLOBALS['MM_Username'] = $loginUsername;
    $GLOBALS['MM_UserGroup'] = $loginStrGroup;	      

    //register the session variables
    session_register("MM_Username");
    session_register("MM_UserGroup");

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
<title>Mobile365: Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<div align="center">
<div class="casing" align="left">
<form action="<?php echo $loginFormAction; ?>" name="loginForm" id="loginForm" method="post">
<table class="login" align="center" cellpadding="2" cellspacing="0">
	<tr class="title"><td colspan="2"><h3>Sign In</h3></td></tr>
	<tr> 
		<td class="contrast"><label for="username">Email</label></td>
		<td><input type="text" name="username" id="username" size="25" /></td>
	</tr>
	<tr>
		<td class="contrast"><label for="password">Password</label></td>
		<td><input type="password" name="password" id="password" size="25" /></td>
	</tr>
	<tr class="button"><td colspan="2"><input type="submit" name="login" id="login" value="Log in" /></td></tr>
 </table>
</form>
</div>
</div>
</body>
</html>