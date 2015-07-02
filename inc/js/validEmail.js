function emailcheck(cur) {
	var string1=cur.email.value
	if (string1.indexOf("@")==-1) {
		alert("Please input a valid email address!")
		return false
	}
}

<form onsubmit="return emailcheck(this)">
<input type="text" size="20" name="email">
<input type="submit" value="Submit!">
</form>