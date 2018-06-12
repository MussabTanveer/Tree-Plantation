document.addEventListener('OnClick', deleteAccount, false);

function deleteAccount(name) {
	var msg = "Are you sure you want to delete this account: " + name + "?";
	var verify = confirm(msg);
	if (verify) {
		window.location.href = "login.php?username=" + name;
	}
	else {
		return false;
	}
}