<?php
require "needed/start.php";
ob_clean();

if($_SESSION['uid'] != NULL && $session['username'] == trim($_POST['username'])) {
	echo "session_id=".session_id();
	die();
} elseif($_SESSION['uid'] != NULL && $session['username'] != trim($_POST['username'])) {
	echo "error_msg=You are currently logged into a different account.";
	die();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$member = $conn->prepare("SELECT uid, username, password, old_pass, termination FROM users WHERE username LIKE :username");
		$member->execute([":username" => trim($_POST['username'])]);
        
		if($member->rowCount() > 0) {
			$member = $member->fetch(PDO::FETCH_ASSOC);
            if($member['termination'] == 1) {
            $username_err = "This account has been terminated.";    
            }
            if($member['termination'] !== 1) {
			if(password_verify(trim($_POST['password']), $member['password']) || password_verify(trim($_POST['password']), $member['old_pass'])) {
				$_SESSION['uid'] = $member['uid'];
				echo "session_id=".session_id();
				$lastlogin = $conn->prepare("UPDATE users SET lastlogin = CURRENT_TIMESTAMP WHERE uid = ?");
				$lastlogin->execute([$member['uid']]);
                if(password_verify(trim($_POST['password']), $member['password'])) {
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

if (isset($username_err)) {echo "error_msg=".htmlspecialchars($username_err);}
if (isset($password_err)) {echo "error_msg=".htmlspecialchars($password_err);}
if (isset($confirm_password_err)) {echo "error_msg=".htmlspecialchars($confirm_password_err);}
if (isset($email_err)) {echo "error_msg=".htmlspecialchars($email_err);}