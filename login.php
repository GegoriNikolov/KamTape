<?php
require "needed/start.php";


if($_SESSION['uid'] != NULL) {
	header("Location: index.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
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

?>
<div class="tableSubTitle">Log In<?php
      echo '<br><small class=bold>';
	  if (isset($username_err)) {echo htmlspecialchars($username_err);}
      if (isset($password_err)) {echo htmlspecialchars($password_err);}
       if (isset($confirm_password_err)) {echo htmlspecialchars($confirm_password_err);}
      if (isset($email_err)) {echo htmlspecialchars($email_err);}
      echo '</small>';
    ?></div>
<form method="post" action="login.php">
<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;" width="510">
		
		
		<span class="highlight">What is KamTape?</span>

		KamTape is a way to get your videos to the people who matter to you. With KamTape you can:
		
		<ul>
		<li> Show off your favorite videos to the world
		<li> Blog the videos you take with your digital camera or cell phone
		<li> Securely and privately show videos to your friends and family around the world
		<li> ... and much, much more!
		</ul>

		<br><span class="highlight"><a href="signup.php">Sign up now</a> and open a free account.</span>
		<br><br><br>
		
		To learn more about our service, please see our <a href="help.php">Help</a> section.<br><br><br>
		</td>
		<td width="280">
		
		<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">

			<input type="hidden" name="field_command" value="login_submit">
				<tr>
					<td align="center" colspan="2"><div style="font-size: 14px; font-weight: bold; color:#003366; margin-bottom: 5px;">KamTape Log In</div></td>
				</tr>
				<tr>
					<td align="right"><span class="label">User Name:</span></td>
					<td><input type="text" size="20" name="field_login_username" value=""></td>
				</tr>
				<tr>
					<td align="right"><span class="label">Password:</span></td>
					<td><input type="password" size="20" name="field_login_password"></td>
				</tr>
				<tr>
					<td align="right"><span class="label">&nbsp;</span></td>
					<td><input type="submit" value="Log In"></td>
				</tr>
				<tr>
					<td align="center" colspan="2"><a href="forgot.php">Forgot your password?</a><br><br></td>
				</tr>
			</form>
			</table>
			
			</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
			
		</td>
	</tr>
</table>
<?php
require "needed/end.php";
?>
