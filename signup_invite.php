<?php // since this is doing the exact same thing for bunches of post forms, this script will probably look like total dogshit 
// TODO: rewrite with post arrays.... now there is no excuse for this hideous code
require "needed/start.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
force_login();
if($session['em_confirmation'] == 'false' && $_SERVER['HTTP_REFERER'] == 'signup.php' || $_SERVER['HTTP_REFERER'] == 'signup' || $_SERVER['HTTP_REFERER'] == 'signup_login') {
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
    $mail->Subject = 'Welcome to KamTape';
    $mail->Body    = '<h1>Thank You for Signing Up!</h1>
You\'ve taken the next step in becoming part of the KamTape community. Now that you\'re a 
member, you can rate videos, leave comments*, and upload your own videos to the site.
Please take a look at the <a href="http://www.kamtape.com/terms.php">Terms of Use</a> before uploading so that you understand what\'s allowed on the site.<p>
You will need to confirm your email address before uploading or commenting on a video.
Go to the <a href="http://www.kamtape.com/my_videos_upload.php">Upload</a> tab, enter your email address, and you will receive a new email with the link where you can confirm.<p>
To get you started, here are some of the fun things you can do with KamTape:
<p><ul>
<li><a href="http://www.kamtape.com/my_videos_upload.php">Upload</a>* and share your videos worldwide</li>
<li><a href="http://www.kamtape.com/browse.php">Browse</a> millions of original videos uploaded by community members</li>
<li>Customize your experience with playlists and subscriptions</li>
<li>Integrate KamTape with your website using video embeds or<a href="http://www.kamtape.com/developers">APIs</a></li>
</ul>
There\'s a lot more to explore, and more features are always in the works. Thanks for signing up, and we hope you enjoy the site!<p>
-- The KamTape Team';

    $mail->send();
    } catch (Exception $e) {
    }
}
if (!empty($_POST['friends_email1']) && empty($_POST['friends_fname1'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email2']) && empty($_POST['friends_fname2'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email3']) && empty($_POST['friends_fname3'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email4']) && empty($_POST['friends_fname4'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email5']) && empty($_POST['friends_fname5'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email6']) && empty($_POST['friends_fname6'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email7']) && empty($_POST['friends_fname7'])) { $send_err = "You've forgotten their names!"; }
if (!empty($_POST['friends_email']) && empty($_POST['friends_fname'])) { $send_err = "You've forgotten their names!"; }
// now 4 emails -- nobody is left behind!
if (empty($_POST['friends_email1']) && !empty($_POST['friends_fname1'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email2']) && !empty($_POST['friends_fname2'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email3']) && !empty($_POST['friends_fname3'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email4']) && !empty($_POST['friends_fname4'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email5']) && !empty($_POST['friends_fname5'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email6']) && !empty($_POST['friends_fname6'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email7']) && !empty($_POST['friends_fname7'])) { $send_err = "How will we send without an e-mail?"; }
if (empty($_POST['friends_email']) && !empty($_POST['friends_fname'])) { $send_err = "How will we send without an e-mail?"; }
// we don't want people sending emails to addresses like "jake"
if (!filter_var($_POST['friends_email1'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email1'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email2'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email2'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email3'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email3'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email4'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email4'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email5'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email5'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email6'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email6'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email7'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email7'])) { $send_err = "This is not a real e-mail address!"; }
if (!filter_var($_POST['friends_email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['friends_email'])) { $send_err = "This is not a real e-mail address!"; }
// AHAHAHAH! I WAS RIGHT! THIS LOOKS LIKE CATSHIT ON A PLATE!


// seperate email sending and error checking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($send_err)) {
    alert($send_err, "error");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($send_err)) {


if (!empty($_POST['friends_email1'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email1'], $_POST['friends_fname1']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}
if (!empty($_POST['friends_email2'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email2'], $_POST['friends_fname2']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email3'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email3'], $_POST['friends_fname3']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email4'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email4'], $_POST['friends_fname4']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email5'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email5'], $_POST['friends_fname5']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email6'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email6'], $_POST['friends_fname6']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email7'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email7'], $_POST['friends_fname7']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

if (!empty($_POST['friends_email'])) {
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
    $mail->setFrom($config["email"], 'KamTape');
    $mail->addAddress($_POST['friends_email'], $_POST['friends_fname']);  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $session['username'].' has invited you to join KamTape';
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>
            KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><a href="http://www.kamtape.com/invite_signup.php?u='. htmlspecialchars($session['username']) .'">'. htmlspecialchars($_POST['message']) .'</a><p><i>KamTape - '. invokethConfig("slogan") .'</i><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';

    $mail->send();
    session_error_index("Your invitations have been sent!", "success");
} catch (Exception $e) {
    alert("We were unable to send the email.", "error");
}
}

// HOLY SHIT. R.I.P ME IF ANY BUG EVER HAPPENS WITH THESE.

}
?>
<h1 class="tableSubTitle">Welcome to KamTape, <?php echo htmlspecialchars($session['username']); ?>!</h1>
<p></p>

        <p>We hope you enjoy your experience. Write anytime to let us know how we
        can serve you better.<br>- <i>The KamTape Team</i> </p>

        <table class="roundedTable" cellspacing="0" cellpadding="0" width="777" align="center" bgcolor="#e5ecf9" border="0" id="table3">

        <tbody>

        <tr>

        <td><img height="1" src="img/pixel.gif" width="5"></td>

        <td width="767">

            <table cellspacing="0" cellpadding="0" width="100%" border="0" id="table4">

              <tbody>

              <tr valign="top">

                <td class="highLight" style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-WEIGHT: bold; PADDING-BOTTOM: 13px; PADDING-TOP: 3px; BORDER-RIGHT-STYLE: none">What would you like to do next?</td>

                <td style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 0px; PADDING-TOP: 0px">&nbsp;</td></tr>

              <tr valign="top">

                <td style="BORDER-RIGHT: #369 1px dashed; PADDING-RIGHT: 10px; PADDING-LEFT: 0px; PADDING-BOTTOM: 0px; PADDING-TOP: 0px" width="50%">

                  <div style="FONT-SIZE: 13px; MARGIN-BOTTOM: 5px">

                  <ul>
                  	
                  	

                    <li><a href="/my_profile.php"><strong>Complete your profile page </strong></a>

                    <div style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 15px; PADDING-TOP: 4px">The KamTape community wants to know about you.</div>
                    
                    </li>
                    <li><a href="/my_videos_upload.php"><strong>Upload your videos</strong></a><div style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 15px; PADDING-TOP: 4px">Share
                    your experiences with the world.</div></li></ul>
                  </div></td>

                <td style="PADDING-RIGHT: 10px; PADDING-LEFT: 0px; PADDING-BOTTOM: 0px; PADDING-TOP: 0px" width="50%">

                  <div style="FONT-SIZE: 13px; MARGIN-BOTTOM: 0px">

                  <ul>
                  	

                    <li><a href="/channels.php"><strong>Browse the Categories</strong></a>

                    <div style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 15px; PADDING-TOP: 4px">Watch videos organized into categories.</div>
                    </li> <li><a href="/browse.php"><strong>Start watching videos </strong></a>

                    <div style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 15px; PADDING-TOP: 4px">Search
                    and browse 1000's of streaming
              videos.</div></li></ul>
                  </div></td></tr></tbody></table></td>

          <td><img height="1" src="img/pixel.gif" width="5"></td></tr>

        </tbody></table>



        <p></p><h1 class="tableSubTitle">KamTape is...</h1>
        <br><span class="highLight"><i>For
        Families:</i></span> <i>Have family members that you wish to share baby or
        event videos with? Invite them to join!</i> <br>
        <br>

        <form method="post">

        <table cellspacing="5" cellpadding="0" border="0" id="table5">

        <tbody>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Family">
			<input maxlength="60" size="30" name="friends_email1">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>            </span>
          <input maxlength="30" name="friends_fname1" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Family">
			<input maxlength="60" size="30" name="friends_email2">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>            </span>
          <input maxlength="30" name="friends_fname2" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Family">
			<input maxlength="60" size="30" name="friends_email3">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>            </span>
          <input maxlength="30" name="friends_fname3" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Family">
			<input maxlength="60" size="30" name="friends_email4">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>            </span>
          <input maxlength="30" name="friends_fname4" size="20"></td></tr>

        <tr>

          <td colspan="2">&nbsp;</td></tr>

        <tr>

          <td colspan="2"><span class="highLight"><i>For Friends and
            Co-Workers:</i></span> <i>Have co-workers and friends you want to
            share funny videos with? Invite them to join!</i></td>
        </tr>

        <tr>

          <td align="right" colspan="2">&nbsp;</td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Friends">
			<input maxlength="60" size="30" name="friends_email5">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>

            </span><input maxlength="30" name="friends_fname5" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Friends">
			<input maxlength="60" size="30" name="friends_email6">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>

            </span><input maxlength="30" name="friends_fname6" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Friends">
			<input maxlength="60" size="30" name="friends_email7">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>

            </span><input maxlength="30" name="friends_fname7" size="20"></td></tr>

        <tr>

          <td align="right"><span class="label"><nobr>Email Address:</nobr>

          </span></td>

          <td><input type="hidden" name="type" value="Friends"><input maxlength="60" size="30" name="friends_email">

          <span class="label" style="MARGIN-LEFT: 3em"><nobr>First Name:</nobr>

            </span><input maxlength="30" name="friends_fname"></td></tr>

        <tr>

          <td style="FONT-SIZE: 1px" colspan="2">&nbsp;</td></tr>

        <tr valign="top">

          <td align="right"><span class="label">Your Message:</span></td>

          <td>

            <div class="formHighlight">Hello, <br><br>KamTape is a
            great site for sharing and hosting personal videos. I have been
            <br>using KamTape to share videos with my friends and family. I
            would like to add <br>you to the list of people I may share videos
            with. <br><br>Your personal message:  <br>
              <br>

            <textarea name="message" rows="5" cols="45">Have you heard about KamTape? I love this site.</textarea>

            <br><br>
            	Thanks, <br>
            <?php echo htmlspecialchars($session['username']); ?> <br><br></div></td></tr>

        <tr>

          <td>&nbsp;</td>

          <td><input type="submit" value="Send Invite" name="invite_signup">

          <input onclick="document.location='/email_confirm.php';" type="button" value="Skip" name="cancel">

          </td>

        </tr>

        </tbody>

        </table>

        </form>

		
		</td>
	</tr>
</tbody></table>
<?php 
require "needed/end.php";
?>
