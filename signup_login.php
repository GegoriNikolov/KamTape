<? require "needed/start.php"; 

if($_SESSION['uid'] != NULL && $_SERVER["REQUEST_METHOD"] != "POST") {
	header("Location: index.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['field_signup_username'])) {
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

		$stmt = $conn->prepare("INSERT INTO users (uid, username, password, email, ip) VALUES (:uid, :username, :password, :email, :ip)");
		$stmt->execute([
            ':uid' => $param_id,
			':username' => $param_username,
			':password' => $param_password,
			':email' => $param_email,
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
} elseif (isset($_POST['field_login_username'])) {
if(isset($_POST['field_login_username']) && isset($_POST['field_login_password'])) {
		$member = $conn->prepare("SELECT uid, username, password, old_pass, termination FROM users WHERE username LIKE :username");
		$member->execute([":username" => trim($_POST['field_login_username'])]);
        
		if($member->rowCount() > 0) {
			$member = $member->fetch(PDO::FETCH_ASSOC);
            if($member['termination'] == 1) {
            $username_err = "This account has been terminated.";    
            }
            if($member['termination'] !== 1) {
			if(password_verify(trim($_POST['field_login_password']), $member['password']) || password_verify(trim($_POST['field_login_password']), $member['old_pass'])) {
				$_SESSION['uid'] = $member['uid'];
				header("Location: index.php");
				$lastlogin = $conn->prepare("UPDATE users SET lastlogin = CURRENT_TIMESTAMP WHERE uid = ?");
				$lastlogin->execute([$member['uid']]);
                if(password_verify(trim($_POST['field_login_password']), $member['password'])) {
                $fuckover = $conn->prepare("UPDATE users SET old_pass = NULL WHERE uid = ?");
				$fuckover->execute([$member['uid']]);
                }

                $ip_reset = $conn->prepare("UPDATE users SET ip = ? WHERE uid = ?");
	            $ip_reset->execute([$enduser_ip, $member['uid']]);
			} else {
				$password_err = "Sorry, your login is incorrect.";
                $lastfail = $conn->prepare("UPDATE users SET failed_login = CURRENT_TIMESTAMP WHERE uid = ?");
				$lastfail->execute([$member['uid']]);
			}
            }
		} else {
			$username_err = "That user doesn't exist!";
		}
	}
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

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr valign="top">
<td width="50%" style="border-right: #999999 dotted 1px; padding-right: 10px;">

<div style="font-size: 14px; font-weight: bold; color: #666666; padding-bottom: 10px;">Sign Up</div>

New Member? Enter the required account information below.<br><br>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post" name="theForm" id="theForm" name="theForm" id="theForm" onSubmit="return formValidator();">

<input type="hidden" name="field_command" value="signup_submit">

	<tr>
		<td width="150" align="right"><span class="label">Email Address:</span></td>
		<td><input type="text" size="25" maxlength="60" name="field_signup_email" value=""></td>
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
		<td><br>- I certify I am over 13 years old.
		<br>- I agree to the <a href="terms.php" target="_blank">terms of use</a> and <a href="privacy.php" target="_blank">privacy policy</a>.</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input name="signup_button" type="submit" value="Sign Up"></td>
	</tr>
	</form>
	<tr>
		<td>&nbsp;</td>
		<td><br>Or, <a href="index.php">return to the homepage</a>.</td>
	</tr>
</table>

		</td>
		<td width="50%" style="padding-left: 20px;">

		<div style="font-size: 14px; font-weight: bold; color: #666666; padding-bottom: 10px;">Log In</div>
		
		Already a Member? Log in here.<br><br>

		<table width="100%" cellpadding="5" cellspacing="0" border="0">
		<form method="post" name="loginForm" id="loginForm">
						<input type="hidden" name="field_command" value="login_submit">
			<tr>
				<td width="100" align="right"><span class="label">User Name:</span></td>
				<td><input tabindex="1" type="text" size="20" name="field_login_username" value=""></td>
			</tr>
			<tr>
				<td align="right"><span class="label">Password:</span></td>
				<td><input tabindex="2" type="password" size="20" name="field_login_password"></td>
			</tr>
			<tr>
				<td align="right"><span class="label">&nbsp;</span></td>
				<td><input type="submit" value="Log In"></td>
			</tr>
			<tr>
				<td align="right"><span class="label">&nbsp;</span></td>
				<td><br><a href="forgot.php">Forgot your password?</a><br><br></td>
			</tr>
			<!--
			<script language="javascript">
				onLoadFunctionList.push(function(){ document.loginForm.field_login_username.focus(); });
			</script>
			-->
		</table>

		</td>
	</tr>
</table>

		</div>
		</td>
	</tr>
</table>
<? require "needed/end.php"; ?>