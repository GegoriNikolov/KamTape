<?php 
require "needed/start.php";

if($_SESSION['uid'] != NULL) {
	header("Location: index.php");
}
// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
   $param_ip = $enduser_ip;
        $stmt = $conn->prepare("SELECT uid FROM users WHERE ip = :address");
        $stmt->execute([
            ':address' => $param_ip,
        ]);
        if($stmt->rowCount() > 15){
            $username_err = "Sorry, you have too many accounts.";
        }
    // Validate username
    if(empty(trim($_POST["field_signup_username"]))){
        $username_err = "Please enter a username.";
    } else if(!preg_match('/^[a-zA-Z0-9]+$/', trim($_POST["field_signup_username"]))){
        $username_err = "Sorry, that user name contains special characters.";
    } else if (strlen(trim($_POST["field_signup_username"])) > 20) {
    $username_err = "Sorry, that user name is too long.";
    } else {
        // Prepare a select statement and bind variables to the prepared statement as parameters
        $param_username = trim($_POST["field_signup_username"]);
        $stmt = $conn->prepare("SELECT uid FROM users WHERE username = :username");
        $stmt->execute([
            ':username' => $param_username,
        ]);
        if($stmt->rowCount() > 0){
            $username_err = "Sorry, that user name has already been taken.";
        }
    }
    // if (isset($_POST["field_signup_username"]) && stripos($_POST["field_signup_username"], "kamtape") !== false) {
    //        $username_err = 'Sorry, a user name can not contain the word "kamtape".';
    // }
    // Validate password
    if(empty(trim($_POST["field_signup_password_1"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["field_signup_password_1"])) < 3){
        $password_err = "Your password is too short.";
    } else{
        $password = trim($_POST["field_signup_password_1"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["field_signup_password_2"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["field_signup_password_2"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Your passwords didn't match; try retyping them.";
        }
    }
	
	// Validate email
    if (substr($_POST['field_signup_email'], -strlen("kamtape.com")) === "kamtape.com") {
    $email_err = "Sorry, this email is invalid.";
    }
	if(empty(trim($_POST['field_signup_email']))) {
		$email_err = "Please enter an email.";
	} elseif(!filter_var(trim($_POST['field_signup_email']), FILTER_VALIDATE_EMAIL)) {
		$email_err = "Sorry, this email is invalid.";
	} else {
		$param_email = trim($_POST['field_signup_email']);
		
		// Prepare a select statement and bind variables to the prepared statement as parameters
		$email_in_use = $conn->prepare("SELECT uid FROM users WHERE email = ?");
		$email_in_use->execute([$param_email]);
		if($email_in_use->rowCount() > 0) {
			$email_err = "Sorry, somebody is already using this e mail.";
		}
	}
    $emailValidator = new \enricodias\EmailValidator\EmailValidator();
    $validate = $emailValidator->validate($param_email);
    if ($validate) {
    if ($emailValidator->isDisposable()) {
       $email_err = "Sorry, somebody is already using this e mail.";
    }
    } else {
    $email_err = "Sorry, somebody is already using this e mail.";
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){ 
		// Set parameters
        $param_id = generateId();
		$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

		$stmt = $conn->prepare("INSERT INTO users (uid, username, password, email, emailprefs_wklytape, ip) VALUES (:uid, :username, :password, :email, :emailprefs_wklytape, :ip)");
		$stmt->execute([
            ':uid' => $param_id,
			':username' => $param_username,
			':password' => $param_password,
			':email' => $param_email,
			':emailprefs_wklytape' => isset($_POST['flag_weekly_tape']) ? 1 : 0,
			':ip' => $param_ip 
		]);
        $_SESSION['uid'] = $param_id;
		// Redirect to the intended page
        $location = "/signup_invite.php";

        if(!empty($_POST['v'])) {
        $location = '/watch.php?v='.$_POST['v'];
        }
        
        redirect($location);
    }
}
?>
<? if(!empty($_GET['v']) && !empty($_GET['r'])) { 
    if($_GET['r'] == "o") {
    $blahblah = "contact the author of this video";
    } elseif($_GET['r'] == "c") {
    $blahblah = "comment on this video";
    } elseif($_GET['r'] == "a") {
    $blahblah = "add this videos to your favorites";
    } else {
    // what the fuck?
    $blahblah = "interact to this video";
    }
  }
    ?>

<script>
function formValidator()
{
	/*
	var field_signup_email = document.theForm.field_signup_email;
	var field_signup_username = document.theForm.field_signup_username;
	var field_signup_password_1 = document.theForm.field_signup_password_1;
	var field_signup_password_2 = document.theForm.field_signup_password_2;
	*/

	var signup_button = document.theForm.signup_button;

	signup_button.disabled='true';
	signup_button.value='Please wait...';
}
</script>

<?php if(!empty($username_err) || !empty($password_err) || !empty($confirm_password_err) || !empty($email_err)){ 
      if(!empty($username_err)) { alert(htmlspecialchars($username_err), "error"); }
      if(!empty($password_err)) { alert(htmlspecialchars($password_err), "error"); }
      if(!empty($confirm_password_err)) { alert(htmlspecialchars($confirm_password_err), "error"); }
      if(!empty($email_err)) { alert(htmlspecialchars($email_err), "error"); }
  } ?>	
  
<div class="tableSubTitle">Sign Up</div>
<? if(!empty($_GET['v']) && !empty($_GET['r'])) { ?>
<table width="80%" cellpadding="5" cellspacing="0" border="0"><tbody><tr><td><b>In order to <?php echo htmlspecialchars($blahblah); ?>, you must first create an account.</b>
		<br><br>Already have an account? <a href="login.php">Please log in</a>.
		<br><br>The registration process takes less than 10 seconds. After registering, you will be able to:
		<br><ul><li>Upload your own videos</li><li>Add videos you like to 'My Favorites'</li><li>Message other KamTape users</li><li>Share videos with your friends and family</li><li>Participate in <a href="monthly_contest.php">monthly contests</a></li><li>Create software and web applications on top of KamTape using our <a href="developers_intro.php">Developer Tools</a></li></ul></td></tr></tbody></table>
        <? } else { echo "Please enter your account information below. All fields are required."; } ?>	
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post" name="theForm" id="theForm" onsubmit="return formValidator();" action="signup.php">

<? if(!empty($_GET['v']) && !empty($_GET['r'])) { ?><input type="hidden" name="v" value="<? echo htmlspecialchars($_GET['v']); ?>"><? } ?>
<input type="hidden" name="field_command" value="signup_submit">

	<tbody><tr>
		<td width="200" align="right"><span class="label">Email Address:</span></td>
		<td><input type="text" size="30" maxlength="60" name="field_signup_email" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">User Name:</span></td>
		<td><input type="text" size="20" maxlength="20" name="field_signup_username" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Password:</span></td>
		<td><input type="password" size="20" maxlength="20" name="field_signup_password_1" value=""></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Retype Password:</span></td>
		<td><input type="password" size="20" maxlength="20" name="field_signup_password_2" value=""></td>
	</tr>
    <tr>
		<td>&nbsp;</td>
		<td align="left">
			<input name="flag_weekly_tape" type="checkbox" checked="">Sign me up for "The Weekly Tape" e-mail.
			<br>
			<span class="footer">Get the best KamTape videos delivered to you via e-mail each week.</span>
		</td> 
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br>- I certify I am over 13 years old.
		<br>- I agree to the <a href="/terms" target="_blank">terms of use</a> and <a href="/privacy" target="_blank">privacy policy</a>.</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input name="signup_button" type="submit" value="Sign Up"></td>
	</tr>
	</form>
	<tr>
		<td>&nbsp;</td>
		<td><br>Or, <a href="/index">return to the homepage</a>.</td>
	</tr>
</tbody></table>
<?php 
require "needed/end.php";
?>