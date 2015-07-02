<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Cross-Browser Rich Text Editor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta name="PageURL" content="http://www.kevinroth.com/rte/demo.htm" />
	<meta name="PageTitle" content="Cross-Browser Rich Text Editor" />
	<!-- To decrease bandwidth, change the src to richtext_compressed.js //-->
	<script language="JavaScript" type="text/javascript" src="richtext.js"></script>
</head>
<body>

<h2>Cross-Browser Rich Text Editor</h2>
<p><a href="http://www.planetsourcecode.com/vb/scripts/ShowCode.asp?txtCodeId=3508&amp;lngWId=2" target="_blank"><img src="/images/PscContestWinner.jpg" height="88" width="409" alt="PscContestWinner" border="0"></a></p>
<p>The cross-browser rich-text editor implements the <a href="http://www.mozilla.org/editor/midas-spec.html" target="_blank">Mozilla Rich Text Editing API</a> included with Mozilla 1.3+.  There is <b>NO LICENSE</b>, so just take the code and use it for any purpose.  This code is 100% free.  Enjoy!</p>
<p>For frequently asked question and support, please visit <a href="http://www.kevinroth.com/forums/index.php?c=2">http://www.kevinroth.com/forums/index.php?c=2</a></p>
<p><b>Requires:</b> IE5+/<a href="http://www.mozilla.org/products/mozilla1.x/">Mozilla</a> 1.3+/<a href="http://www.mozilla.org/products/firefox/" target="_blank">Mozilla Firebird/Firefox</a> 0.6.1+/<a href="http://channels.netscape.com/ns/browsers/download.jsp" target="_blank">Netscape</a> 7.1+ for all rich-text features to function properly.  If browser does not support rich-text, it should display a standard textarea box.</p>
<p><b>Source:</b> <a href="../../n3xt/inc/rt/rte.zip">rte.zip</a>, <a href="../../n3xt/inc/rt/rte.tar.gz">rte.tar.gz</a><br>
Included in the zip are <a href="demo.htm">HTML</a>, <a href="../../n3xt/inc/rt/demo.asp">ASP</a>, and <a href="demo.php">PHP</a> demos.  Also, here is an html demo showing <a href="multi.htm">multiple RTEs</a> on one page.</p>
<p><b>Change Log:</b> <a href="../../n3xt/inc/rt/changelog.txt">changelog.txt</a></p>

<p><b>If you feel that the work I've done has value to you,</b> I would greatly appreciate a paypal donation (click button below).  Another way you can help me out is to <a href="http://www.FreeFlatScreens.com/default.aspx?referer=11055453" target="_blank">sign up for a free flat screen</a>, to help me get mine.  Again, I am very grateful for any and all contributions.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="kevin@kevinroth.com">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="US">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>

<form name="RTEDemo" action="demo.htm" method="post" onSubmit="return submitForm();">

<script language="JavaScript" type="text/javascript">
<!--
function submitForm() {
	//make sure hidden and iframe values are in sync before submitting form
	//to sync only 1 rte, use updateRTE(rte)
	//to sync all rtes, use updateRTEs
	//updateRTE('rte1');
	updateRTEs();
	alert("rte1 = " + document.RTEDemo.rte1.value);
	alert("rte2 = " + document.RTEDemo.rte2.value);
	alert("rte3 = " + document.RTEDemo.rte3.value);
	
	//change the following line to true to submit form
	return false;
}

//Usage: initRTE(imagesPath, includesPath, cssFile)
initRTE("images/", "", "");
//-->
</script>
<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>

<script language="JavaScript" type="text/javascript">
<!--
//Usage: writeRichText(fieldname, html, width, height, buttons, readOnly)
writeRichText('rte1', 'here&#39;s the "<em>preloaded</em> <b>content</b>"', 520, 200, true, false);

document.writeln('<br><br>');
writeRichText('rte2', 'preloaded <b>text</b>', 560, 100, true, false);

document.writeln('<br><br>');
writeRichText('rte3', 'preloaded <b>text</b>', 450, 100, true, true);
//-->
</script>

<p>Click submit to show the value of the text box.</p>
<p><input type="submit" name="submit" value="Submit"></p>
</form>

</body>
</html>ing = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);
	
	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = str_replace("\"", "\"", $tmpString);
	
	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);
	
	return $tmpString;
}
?>
