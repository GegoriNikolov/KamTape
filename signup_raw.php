<?php
require "needed/start.php";
ob_clean();

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
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else if(!preg_match('/^[a-zA-Z0-9]+$/', trim($_POST["username"]))){
        $username_err = "Sorry, that user name contains special characters.";
    } else if (strlen(trim($_POST["field_signup_username"])) > 20) {
    $username_err = "Sorry, that user name is too long.";
    } else {
        // Prepare a select statement and bind variables to the prepared statement as parameters
        $param_username = trim($_POST["username"]);
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
    if(empty(trim($_POST["password1"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password1"])) < 3){
        $password_err = "Your password is too short.";
    } else{
        $password = trim($_POST["password1"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["password2"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["password2"]);
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
        //$location = "/signup_invite.php";
		echo "session_id=".session_id();

        //if(!empty($_POST['v'])) {
        //$location = '/watch.php?v='.$_POST['v'];
        //}
        
        //redirect($location);
    }
}

if(!empty($username_err) || !empty($password_err) || !empty($confirm_password_err) || !empty($email_err)){ 
if(!empty($username_err)) { echo "error_msg=".htmlspecialchars($username_err); }
if(!empty($password_err)) { echo "error_msg=".htmlspecialchars($password_err); }
if(!empty($confirm_password_err)) { echo "error_msg=".htmlspecialchars($confirm_password_err); }
if(!empty($email_err)) { echo "error_msg=".htmlspecialchars($email_err); }
}