<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "needed/start.php";
force_login();
if($session['em_confirmation'] == 'true') { redirect("/index.php"); }
if(isset($_POST['send_button'])) {
    $param_email = $_POST['field_send_email_to'];
    if (substr($_POST['field_send_email_to'], -strlen("kamtape.com")) === "kamtape.com") {
    $email_err = "Sorry, this email is invalid.";
    }
	if(empty(trim($_POST['field_send_email_to']))) {
		$email_err = "Please enter an email.";
	} elseif(!filter_var(trim($_POST['field_send_email_to']), FILTER_VALIDATE_EMAIL)) {
		$email_err = "Sorry, this email is invalid.";
	} else {
		$param_email = trim($_POST['field_send_email_to']);
		
		// Prepare a select statement and bind variables to the prepared statement as parameters
		$email_in_use = $conn->prepare("SELECT uid FROM users WHERE email = ? AND uid <> ?");
		$email_in_use->execute([$param_email, $session['uid']]);
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
    
    if (!empty($email_err)) {
    alert($email_err, "error");
    } else {
    $confirmation_email = generateId2();
    $datetime = new DateTime('+1day');
    $tomorrow = $datetime->format('Y-m-d H:i:s');
    $verify = $conn->prepare("UPDATE users SET confirm_expire = ?, confirm_id = ?, email = ? WHERE uid = ?");
    $verify->execute([$tomorrow, $confirmation_email, $param_email, $session['uid']]);
    if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $protocol = 'https://';
    }
    else {
    $protocol = 'http://';
    }
    if(!empty($_GET['next'])) {
    $construct_url = $protocol."www.kamtape.com/confirm_email?cid=".$confirmation_email."&next=".htmlspecialchars($_GET['next']);
    } else {
    $construct_url = $protocol."www.kamtape.com/confirm_email?cid=".$confirmation_email;    
    }
    $mail = new PHPMailer(true);                              
    try {
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $config["host"];                  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $config["email"];                // SMTP username
    $mail->Password = $config["epassword"];
    $mail->SMTPSecure = 'ssl';                            // Enable SSL encryption, TLS also accepted with port $config["emport"]
    $mail->Port = $config["emport"];                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($config["email"], 'KamTape Service');
    $mail->addAddress($param_email);     // Add a recipient  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'KamTape email confirmation';
    $mail->Body    = 'Hi '.htmlspecialchars($session['username']).',<p>Please click on the following link to confirm your email: <a href="'.$construct_url.'">'.$construct_url.'</a><p>See you back on KamTape!<p>The KamTape Team';

    $mail->send();
    } catch (Exception $e) {
    }
    alert("A confirmation email has been sent to your email address. Please check your email and click on the link provided to confirm your account. 
    If you do not receive the confirmation message within a few minutes, please check your bulk or spam folders.");
    
    }
}


?>
<div class="tableSubTitle">Please Confirm Your Email</div>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<form method="post" name="theForm" id="theForm">

	<tbody><tr>
		<td width="200" align="right"><span class="label">Send a confirmation email to:</span></td>
		<td><input type="text" size="30" maxlength="60" name="field_send_email_to" value="<?php echo (!empty($session['email'])) ? htmlspecialchars($session['email']) : ""; ?>"></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td><input name="send_button" type="submit" value="Send Email"></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		
	</tr>
</tbody></table></form>
<? require "needed/end.php"; ?>