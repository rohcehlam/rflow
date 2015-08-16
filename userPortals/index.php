<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

// *** Validate request to login to this site.
session_start();

$loginFormAction = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);

$GLOBALS['PrevUrl'] = filter_input(INPUT_GET, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
$_SESSION["PrevUrl"] = $GLOBALS['PrevUrl'];

if (filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$loginUsername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
	$MM_fldUserAuthorization = "";
	$MM_redirectLoginSuccess = "myPortal.php";
	$MM_redirectLoginFailed = "index.php?loginFailed=y";
	$MM_redirecttoReferrer = true;

	//$LoginRS__query = sprintf("SELECT workEmail, password FROM employees WHERE workEmail='%s' AND password='%s'", get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password));
	$LoginQuery = "SELECT count(*) AS cant FROM employees WHERE (workEmail='{$loginUsername}' OR displayName='{$loginUsername}') AND SHA('{$password}')=hash_pass";

	/*
	  //$colname_rsGetUser = (get_magic_quotes_gpc()) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) : addslashes(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
	  $colname_rsGetUser = $loginUsername;
	  $query_rsGetUser = sprintf("SELECT employeeID, workEmail, firstName, groupID, password, departmentID FROM employees WHERE workEmail='%s' AND password='%s'", get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password));
	  $rsGetUser = $conn->query($query_rsGetUser);
	  $row_rsGetUser = $rsGetUser->fetch_assoc();
	  $totalRows_rsGetUser = $rsGetUser->num_rows;
	 */
	//echo $LoginQuery;
	$result = $conn->query($LoginQuery) or die($conn->error);
	$row_rsLoginRS = $result->fetch_assoc();
	if ($row_rsLoginRS['cant'] > 0) {

		$query_rsGetUser = "SELECT employeeID, workEmail, firstName, groupID, departmentID"
			. " FROM employees"
			. " WHERE (workEmail='{$loginUsername}' OR displayName='{$loginUsername}') AND SHA('{$password}')=hash_pass";
		$rsGetUser = $conn->query($query_rsGetUser);
		$row_rsGetUser = $rsGetUser->fetch_assoc();

		//declare two session variables and assign them
		/*
		  $GLOBALS['MM_Username'] = $loginUsername;
		  $GLOBALS['MM_UserGroup'] = $loginStrGroup;
		  $GLOBALS['employee'] = $row_rsGetUser['employeeID'];
		  $GLOBALS['firstName'] = $row_rsGetUser['firstName'];
		  $GLOBALS['group'] = $row_rsGetUser['groupID'];
		  $GLOBALS['dept'] = $row_rsGetUser['deptID'];
		 */
		//register the session variables
		$_SESSION["MM_Username"] = $loginUsername;
		$_SESSION["MM_UserGroup"] = $row_rsGetUser['groupID'];
		$_SESSION["employee"] = $row_rsGetUser['employeeID'];
		$_SESSION["firstName"] = $row_rsGetUser['firstName'];
		$_SESSION["group"] = $row_rsGetUser['groupID'];
		$_SESSION["dept"] = $row_rsGetUser['departmentID'];

		if (isset($_SESSION['PrevUrl']) && true) {
			$MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
		}
		header("Location: " . $MM_redirectLoginSuccess);
	} else {
		header("Location: " . $MM_redirectLoginFailed);
	}
}
?>



<!DOCTYPE html>
<html class='no-js' lang='en'>
    <head>
        <meta charset='utf-8'>
        <meta content='chrome=1' http-equiv='X-UA-Compatible'>
        <!-- Empty IE conditional comment to prevent download blocking (http://www.phpied.com/conditional-comments-block-downloads/) -->
        <!--[if IE]>  <![endif]-->
        <!-- Force IE to use the most up to date rendering engine that it has available -->
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0' name='viewport'>
        <title></title>
        <link href='../images/favicon.png' rel='shortcut icon'>        
        <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />       

        <!-- Master stylesheet. All other stylesheets are imported from this one -->
        <link href="../css/new_login.css" media="screen" rel="stylesheet" type="text/css" />

        <!-- Modernizr allows IE to support basic HTML5 tags. This is required to be called inside the <head>, before any other script -->
        <script src="../js/modernizr.js" type="text/javascript"></script>
        <script src="../js/jQuery-2.1.4.min.js" type="text/javascript"></script>
        <!-- IE conditional comments for additional CSS needed to fix bugs (http://paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/) -->
        <!--[if lte IE 7 ]>
        <body class='ie8 ie7'></body>
        <![endif]-->
        <!--[if IE 8 ]>
        <body class='ie8'></body>
        <![endif]-->
        <!--[if (gte IE 9)|!(IE)]>  <![endif]-->
    </head>
	 <body class="login">
		  <div class='super-wrapper'>
            <!-- <![endif] -->
            <!-- Page layout -->
            <div id='wrapper'>
                <div class='left-side'><img alt='' src='../images/GEE_White_logo.png'><h3>Powering business intelligence for the global aviation industry</h3>
                    <h3>The Complete Real-Time Package</h3>
                    <p>masFlight offers the most comprehensive set of technologies to analyze aviation operations and performance to improve efficiency, identify problems, isolate under-performing assets, central data gathering and track the competition.</p>
                    <!-- %p#copyright © Copyright 2014 masFlight. Marks Systems Inc., All Rights Reserved -->
                    <!--<a class='cta-button' href='/sign_up'>Join Now</a>-->
                </div>
                <div class='right-side' id='content'>
                    <div class='form-container'>
                        <p>
                            <strong>Welcome to rflow.</strong>
                            Please Log in.
                        </p>

								<?php
								if (filter_input(INPUT_GET, 'loggedoff', FILTER_SANITIZE_SPECIAL_CHARS) == "y") {
									?>
									<div class="panel panel-success">
										 <div class="panel-heading">
											  <h3 class="panel-title">Success</h3>
										 </div>
										 <div class="panel-body">
											  <p>You have been successfully logged off</p>
										 </div>
									</div>
									<?php
								} elseif (filter_input(INPUT_GET, 'loginFailed', FILTER_SANITIZE_SPECIAL_CHARS) == "y") {
									?>
									<div class="panel panel-warning">
										 <div class="panel-heading">
											  <h3 class="panel-title">Error</h3>
										 </div>
										 <div class="panel-body">
											  <p>Please try logging in again.</p>
										 </div>
									</div>
									<?php
								}
								?>

                        <form action="<?php echo $loginFormAction; ?>" id="XloginForm" method="post">
                            <section>
                                <form action=''>
                                    <div class='input-holder'>
                                        <input autofocus='autofocus' class='text' id='username' name='username' placeholder='Your masFlight username' type='text'>
                                        <label class='placeholder' for='username'>Username</label>
                                    </div>
                                    <div class='input-holder'>
                                        <input class='text' name='password' id='password' placeholder='Your password' type='password'>
                                        <label class='placeholder' for='password'>Password</label>
                                    </div>
                                    <div class='actions-holder'>
                                        <div class='pull-left'>
														  <input class='clear' id='remember_me' name='remember_me' type='checkbox'>
														  <label for='remember_me'>Remember me</label>
                                        </div>
                                        <div class='pull-right'>
                                            <input class='btn btn-primary' id='enviar-login' type='submit' value='Sign in'>
                                        </div>
                                    </div>
                                    <!--<p class='forgot'>
                                    <a href="/password_resets" style="">Forgot your password?</a>
                                    </p>-->
                                </form>
                            </section>
                        </form>

                    </div>
                    <!--<div class='bottom-content'>
                    <p>
                    Dont have a masFlight account?
                    <a class='register' href='/sign_up'>Sign up</a>
                    </p>
                    </div>-->
                </div>
            </div>
            <!-- #content END -->
        </div>
	 </body>
</html>