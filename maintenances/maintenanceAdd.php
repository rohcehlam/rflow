<?php require_once('../Connections/connProdOps.php'); ?>
<?php
function GetSQLValueString2($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEngineers = "SELECT employeeID, engineer, lastName, displayName FROM employees WHERE employees.engineer='y' ORDER BY displayName ASC";
$rsEngineers = mysql_query($query_rsEngineers, $connProdOps) or die(mysql_error());
$row_rsEngineers = mysql_fetch_assoc($rsEngineers);
$totalRows_rsEngineers = mysql_num_rows($rsEngineers);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Add a Maintenance Notification</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<?php require_once('../inc/js/js.php'); ?>
	<?php require_once('../inc/functions.php'); ?>
	<script type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
	  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
	    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
	  if(!x && d.getElementById) x=d.getElementById(n); return x;
	}
function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required (Use N/A if necessary) .\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
    <style type="text/css">
<!--
.style4 {
	color: #D5EDB3;
	background-color: #7F9358;
	vertical-align: top;
}
.style5 {
	font-size: 12px;
	color: #7F9358;
	font-weight: bold;
}
.style6 {color: #7F9358}
.style9 {color: #D5EDB3; font-weight: bold; }
td,th {
	color: #666666;
}
-->
    </style>
</head>
<body>
<?php buildHeader("maintenance", "Maintenance Notifications", "maintenanceAdd", "Add a Notification", null); ?>
<form action="maintenanceSend.php" method="POST" enctype="multipart/form-data" name="maintenanceNotif1" id="maintenanceNotif1">
  <table width="795" border="1" align="center" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
    <tr bgcolor="#D5EDB3"><td colspan="5"></td></tr>
    <tr>
      <td width="131" align="right" class="contrast"><label>Start Date:</label></td>
      <td colspan="4" valign="top" nowrap="nowrap"><input name="startMonth" type="text" id="startMonth" value="<?php
	$date = getdate();
	$month = $date['mon'];
	if ($month < 10) {
		echo "0" . $month;
	} else {
		echo $month;
	}
?>" size="2" maxlength="2" />/<input name="startDay" type="text" id="startDay" value="<?php
	$date = getdate();
	$day = $date['mday'];
	if ($day < 10) {
		echo "0" . $day;
	} else {
		echo $day;
	}
?>" size="2" maxlength="2" />/<input name="startYear" type="text" id="startYear" value="<?php
	$date = getdate();
	$year = $date['year'];
	echo $year;
?>" size="4" maxlength="4" /> <label for="startMonth">(mm/</label><label for="startDay">dd/</label><label for="startYear">yyyy)</label> <abbr class="required" title="Required">*</abbr></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label>Start Time:</label></td>
      <td colspan="2"><input name="startHour" type="text" id="startHour" value="<?php 
	  $now = getdate();
	  $hour = $now['hours'];
	  if ($hour < 5) {
	  	$gmt = $hour + 24;
		$eastern = $gmt - 5;
	  } else {
	  	$eastern = $hour - 5;
	  }
	  if ($eastern < 10) {
	  	echo "0" . $eastern;
	  } else {
	  	echo $eastern;
	  }
	  ?>" size="2" maxlength="2" />:<input name="startMinute" type="text" id="startMinute" value="<?php 
	  $time = getdate();
	  $min = $time['minutes'];
	  if ($min < 10) {
	  	echo "0" . $min;
	  } else {
	  	echo $min;
	  }
	  ?>" size="2" maxlength="2" />&nbsp;<strong>EST</strong>  <abbr class="required" title="Required">*</abbr></td>
	<td width="146" align="right" nowrap="nowrap" class="contrast"><label>Estimated Duration:</label></td>
      <td width="278"><label for="estHours"><input type="text" name="estHours" id="estHours" size="2" maxlength="2" /> hour(s)</label>&nbsp;<label for="estMins"><input type="text" name="estMins" id="estMins" size="2" maxlength="2" /> minute(s) <abbr class="required" title="Required">*</abbr></label></td>
    </tr>
    <tr>
      <td height="5" colspan="5" bgcolor="#E0E0E0"></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="reason">Reason:</label></td>
      <td colspan="4"><input type="text" name="reason" id="reason" size="100" maxlength="255" /> <abbr class="required" title="Required">*</abbr></td>
    </tr>
    <tr bgcolor="#E0E0E0">
      <td height="5" colspan="5"></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="custImpact">Customer Impact:</label></td>
      <td colspan="4"><input type="text" name="custImpact" id="custImpact" size="100" maxlength="255" /> <abbr class="required" title="Required">*</abbr></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="nocImpact">Noc Impact: </label></td>
      <td colspan="4"><input type="text" name="nocImpact" id="nocImpact" size="100" maxlength="255" /> <abbr class="required" title="Required">*</abbr></td>
    </tr>
    <tr>
      <td align="right" class="contrast"><label for="engineer">Engineer:</label></td>
      <td colspan="4"><select name="engineer" id="engineer">
          <option value="" selected="selected">Select Engineer</option>
          <?php do { ?>
          <option value="<?php echo $row_rsEngineers['employeeID']?>"><?php echo $row_rsEngineers['displayName']?></option>
          <?php } while ($row_rsEngineers = mysql_fetch_assoc($rsEngineers));
  $rows = mysql_num_rows($rsEngineers);
  if($rows > 0) {
      mysql_data_seek($rsEngineers, 0);
	  $row_rsEngineers = mysql_fetch_assoc($rsEngineers);
  }
?>
      </select> <abbr class="required" title="Required">*</abbr></td>
    </tr>
    <tr>
      <td height="5" colspan="5" bgcolor="#E0E0E0"></td>
    </tr>
    <tr>
      <td height="177" align="right" valign="top" class="contrast"><label for="prodChanges">Production Changes:</label></td>
      <td valign="top" colspan="4"><textarea name="prodChanges" id="prodChanges" cols="82" rows="10" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr>
      <td height="31" colspan="5" valign="top" class="recipients"><br />Email Recipients</td>
    </tr>
    <tr>
      <td class="contrast" align="right"><strong>To:</strong></td>
      <td width="95" align="left"><label><input name="prodOps" type="checkbox" id="prodOps" value="y" checked="checked" />&nbsp;ProdOps</label></td>
      <td width="149" align="left"><label><input type="checkbox" name="noc" id="noc" value="y" checked="checked" />&nbsp;NOC</label></td>
      <td valign="middle" bgcolor="#D5EDB3" align="right"><label for="cc">CC:</label></td>
      <td valign="middle" align="left"><input name="cc" type="text" id="cc" size="40" maxlength="255" /></td>
    </tr>
    <tr>
      <td bgcolor="#7F9358"></td>
      <td align="left" valign="middle"><label><input name="syseng" type="checkbox" id="syseng" value="y" checked="checked" />&nbsp;SysEng</label></td>
      <td align="left" valign="middle"><label><input name="neteng" type="checkbox" id="neteng" value="y" checked="checked" />&nbsp;NetEng</label></td>
      <td colspan="2" rowspan="2" bgcolor="#D5EDB3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" class="button"><br /><input type="submit" name="submit" id="submit" value="Submit Maintenance Notification" /><br /><?php
	if ((isset($_GET["sent"])) && ($_GET["sent"] == 'y')) {
		echo "<span style=\"background-color: yellow; color: #000000;\">Maintenance Notification submitted successfully!</span>";
	}
?>
      </td>
    </tr>
<?php buildFooter("5"); ?>
</table>
  <input type="hidden" name="MM_insert" value="maintenanceNotif1">
</form></body>
</html><?php
mysql_free_result($rsEngineers);
?>