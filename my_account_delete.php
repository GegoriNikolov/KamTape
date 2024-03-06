<?php
require "needed/start.php";

force_login();
if($session['staff'] == 1){
    redirect("my_profile.php");
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
if(password_verify(trim($_POST['field_login_password']), $session['password'])) {
    $remove_account = $conn->prepare("UPDATE users SET termination = 1, closure = 1 WHERE uid = ?");
    $remove_account->execute([$session['uid']]);
    session_destroy();
    session_error_index("Account closed successfully.", "success");
} else {
alert("Failure to close your account.<br>You have entered the wrong password.", "error");
}
}
?>
<div class="formTable">
<table cellpadding="5" width="700" cellspacing="0" border="0" align="center">
		<div class="tableSubTitle"><span style="float:right; font-size: 12px; font-weight: normal;"><a href="/my_profile.php">Go back</a></span> Delete my account</div>
		<div class="tableSubTitleInfo"><b><font color="#FF0000">Closing your KamTape account will permanently remove your complete profile information (Videos, Comments, Favorites etc.) from KamTape. This cannot be undone.</font></b></div>
		<p>
    		</p><form id="tosForm" name="tosForm" method="post" onsubmit="return confirm('This process is irreversible. Are you sure you want to delete your account?');">
		Please enter your password: <input type="password" name="field_login_password" size="20"><p></p>
		<p><input type="submit" value="Delete Account" name="action_close" ></p>
        </form><p>&nbsp;</p></table>
<?php
require "needed/end.php"; ?>