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
        <link href='new_login/images/favicon.png' rel='shortcut icon'>
        <!-- Master stylesheet. All other stylesheets are imported from this one -->
        <link href="new_login/css/new_login.css" media="screen" rel="stylesheet" type="text/css" />
        <!-- Modernizr allows IE to support basic HTML5 tags. This is required to be called inside the <head>, before any other script -->
        <script src="new_login/js/modernizr.js" type="text/javascript"></script>
        <script src="new_login/js/jquery.min.js" type="text/javascript"></script>
    </head>
    <!-- IE conditional comments for additional CSS needed to fix bugs (http://paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/) -->
    <!--[if lte IE 7 ]>
    <body class='ie8 ie7'></body>
    <![endif]-->
    <!--[if IE 8 ]>
    <body class='ie8'></body>
    <![endif]-->
    <!--[if (gte IE 9)|!(IE)]>  <![endif]-->
    <body class='login'>
        <div class='super-wrapper'>
            <!-- <![endif] -->
            <!-- Page layout -->
            <div id='wrapper'>
                <div class='left-side'><img alt='' src='new_login/images/login-logo.png'><h2>Powering business intelligence for the global aviation industry</h2>
                    <h3>The Complete Real-Time Package</h3>
                    <p>masFlight offers the most comprehensive set of technologies to analyze aviation operations and performance to improve efficiency, identify problems, isolate under-performing assets, central data gathering and track the competition.</p>
                    <!-- %p#copyright © Copyright 2014 masFlight. Marks Systems Inc., All Rights Reserved -->
                    <!--<a class='cta-button' href='/sign_up'>Join Now</a>-->
                </div>
                <div class='right-side' id='content'>
                    <div class='form-container'>
                        <p>
                            <strong>Welcome to masFlight.</strong>
                            Please Log in.
                        </p>
                        <form accept-charset="UTF-8" action="/sessions" method="post"><div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="&#x2713;" /><input name="authenticity_token" type="hidden" value="Ktyi+BNXJZ1yZ4nIBVph9e1gP0iHzjnl/CnTU5XDJyE=" /></div>
                            <section>
                                <form action=''>
                                    <div class='input-holder'>
                                        <input autofocus='autofocus' class='text' name='login' placeholder='Your masFlight username' type='text'>
                                        <label class='placeholder' for='login'>Username</label>
                                    </div>
                                    <div class='input-holder'>
                                        <input class='text' name='password' placeholder='Your password' type='password'>
                                        <label class='placeholder' for='password'>Password</label>
                                    </div>
                                    <div class='actions-holder'>
                                        <!--<div class='pull-left'>
                                        <input class='clear' id='remember_me' name='remember_me' type='checkbox'>
                                        <label for='remember_me'>Remember me</label>
                                        </div>-->
                                        <div class='pull-right'>
                                            <input class='login-btn' type='submit' value='Sign in'>
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
        <!-- #wrapper END -->
        <!-- jQuery is first requested from googleapis. It falls back to a local file -->
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
        <script>
            //<![CDATA[
            window.jQuery || document.write('<script src="javascripts/jquery.js">\x3C/script>')
            //]]>
        </script>
        <!-- Javascript main file for presentational behavior -->
    </body>
</html>

