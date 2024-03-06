<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "needed/start.php";

force_login();
    $favorites_of_you = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY favorites.fid DESC"
);
$favorites_of_you->execute([$session['uid']]);

$videos_of_you = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY videos.uploaded DESC"
);
$videos_of_you->execute([$session['uid']]);

if (empty($_GET['user'])) {
    if (isset($_GET['thanks'])){
    alert("Message has been sent.");
    }
    $msgcount = $conn->prepare("SELECT * FROM messages WHERE sender = ? ORDER BY created DESC");
    $msgcount->execute([$session['uid']]);
    $msgcount = $msgcount->rowCount();
    $nocompose = 'yes';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $ppv = 35;
    $offs = ($page - 1) * $ppv;
    $inbox = $conn->prepare("SELECT * FROM messages LEFT JOIN users ON users.uid = messages.receiver WHERE sender = ? ORDER BY created DESC LIMIT $ppv OFFSET $offs");
$inbox->execute([$session['uid']]);
} else {
    $nocompose = 'no';
    $profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
    $profile->execute([$_GET['user']]);
    if ($profile->rowCount() == 0) {
        header("Location: outbox.php");
    } else {
        $profile = $profile->fetch(PDO::FETCH_ASSOC);
    }
     if ($session['username'] == $profile['username']) {
        header("Location: my_messages.php");
        die();
     }
     // uh
   if ((isset($_POST['title']) || isset($_POST['comment'])) && strlen($_POST['title']) < 75 && strlen($_POST['comment']) < 50000 && strlen($_POST['title']) > 2 && strlen($_POST['comment']) > 2) {
       $pmid = generateId();
$message = $conn->prepare("INSERT INTO messages (pmid, subject, attached, sender, receiver, body) VALUES (:pmid, :subject, :attached, :sender, :receiver, :body)");
$message->execute([
	":pmid" => trim($pmid),
	":subject" => encrypt($_POST['title']),
    ":attached" => trim($_POST['field_reference_video']),
	":sender" => $session['uid'],
    ":receiver" => $profile['uid'],
	":body" => encrypt($_POST['comment'])
]);
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
    $mail->addAddress($profile['email']);     // Add a recipient  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'KamTape Message: '.htmlspecialchars($_POST['title']);
    /*$mail->Body    = '<link href="http://www.kamtape.com/styles.css" rel="stylesheet" type="text/css"><img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>KamTape Message: '.htmlspecialchars($_POST['title']).'<p>
            <a href="http://www.kamtape.com/user/'.htmlspecialchars($session['username']).'">'.htmlspecialchars($session['username']).'</a> has sent you this message at <a href="http://www.kamtape.com">KamTape</a>:<p>'. shorten($_POST['comment'], 350) .' <br><br><br><br>Click <a href="http://www.kamtape.com/read_msg.php?id='.htmlspecialchars($pmid).'&s=">here</a> to go directly to the message, or go to <a href="http://www.kamtape.com/my_messages.php">My Messages</a> on KamTape to view all your messages.<p>Thank you for using KamTape,<br>KamTape Team<p><i>KamTape - '. invokethConfig("slogan") .'</i><p><small>To change or cancel your email notifications, go to your <a href="http://www.kamtape.com/my_profile_email.php">email options</a>.</small><br><br><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright © '. retroDate(date("Y"), "Y") .' KamTape, LLC';*/
    $mail->Body    = '<img src="http://www.kamtape.com/img/logo.gif" width="147" height="50" hspace="12" vspace="12" alt="KamTape"><br>KamTape Message: '.htmlspecialchars($_POST['title']).'<p>
            <a href="http://www.kamtape.com/user/'.htmlspecialchars($session['username']).'">'.htmlspecialchars($session['username']).'</a> has sent you this message at <a href="http://www.kamtape.com">KamTape</a>:<p>'. shorten($_POST['comment'], 350) .' <br><br><br><br>Click <a href="http://www.kamtape.com/read_msg.php?id='.htmlspecialchars($pmid).'&s=">here</a> to go directly to the message, or go to <a href="http://www.kamtape.com/my_messages.php">My Messages</a> on KamTape to view all your messages.<p>Thank you for using KamTape,<br>KamTape Team<p><i>KamTape - '. invokethConfig("slogan") .'</i><p><small>To change or cancel your email notifications, go to your <a href="http://www.kamtape.com/my_profile_email.php">email options</a>.</small><br><br><br><br><center><div style="padding: 2px; padding-left: 7px; padding-top: 0px; margin-top: 10px; background-color: #E5ECF9; border-top: 1px dashed #3366CC; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold;">&nbsp;</div><br>Copyright &copy; '. retroDate(date("Y"), "Y") .' KamTape, LLC';
    $mail->AltBody    = 'KamTape Message: '.$_POST['title'].'

'.$session['username'].' has sent you this message at KamTape:

'. shorten($_POST['comment'], 350) .' 



Click here (http://www.kamtape.com/read_msg.php?id='.$pmid.'&s=) to go directly to the message, or go to My Messages on KamTape to view all your messages:
http://www.kamtape.com/my_messages.php

Thank you for using KamTape,
KamTape Team

To change or cancel your email notifications, go to your email options:
http://www.kamtape.com/my_profile_email.php';

    $mail->addReplyTo($config["email"], 'KamTape Service');
    $mail->send();
} catch (Exception $e) {
   
}
header("Location: outbox.php?thanks");
    }

// uh oh

}

?><div class="tableSubTitle">Sent Messages</div>
<table width="45%" align="center" cellpadding="5" cellspacing="0" border="0">
         <tr align="center">
		 <td align="center" colspan="3">
                <a href="/my_messages.php" >Inbox Messages</a> | <a href="/outbox.php" class="bold">Sent Messages</a>
            </td></tr>
            </table>
<?php if($nocompose == 'yes') { ?>

<table width="91%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
	<tbody>
	<tr>
		<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
		<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		<td>
		<div class="moduleTitleBar">
		<div class="moduleTitle"><? if($msgcount > 0) { ?><div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;">Messages <?php if ($offs > 0) { echo htmlspecialchars(trim($offs)); } else { echo "1"; } ?>-<? if($msgcount > $ppv) { $nextynexty = $offs + $ppv; } else {$nextynexty = $msgcount; } echo htmlspecialchars($nextynexty); ?> of <?php echo $inbox->rowCount(); ?></div><? } ?>
		Messages // Outbox
		</div>
		</div>
				
	<table width="100%" cellpadding="3" cellspacing="0" table="table" align="center">
                <tbody><tr><td colspan="5" height="10"></td></tr>
                                <tr>
                        <td width="20">&nbsp;</td>
                        <td><b>Subject</b></td>
                        <td width="70"><b>To<b></b></b></td>
                        <td width="160"><b>Date</b></td>
                        <td width="20">&nbsp;</td>
                </tr>
                <?php if($inbox->rowCount() > 0) {
				foreach($inbox as $message) { ?>
                 <tr bgcolor="<?php if($message['isRead'] == '0') { echo'#FFCC66'; } else { echo '#eeeeee'; } ?>">
                        <td width="5"><img src="/img/mail<?php if($message['isRead'] == '0') { echo'_unread'; } ?>.gif"></td>
                        <td><a href="/out_msg.php?id=<?php echo htmlspecialchars($message['pmid']);?>" <?php if($message['isRead'] == '0') { echo'class=bold'; } ?>><?php echo decrypt($message['subject']); ?></a></td>
                        <td><a href="/user/<?php echo htmlspecialchars($message['username']);?>"><?php echo htmlspecialchars($message['username']);?></a></td>
                        <td><?php echo gmailStyleTime($message['created']); ?>
                        <td width="5"><?php if(!empty($message['attached'])) { ?><img src="/img/tv_icon.gif"><? } ?></td>
                </tr>
                <? } ?>
                <? } ?>
                                                </tbody></table><!-- begin paging -->
				<?php if($msgcount > $ppv) { ?><div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Browse Pages:
				
					<?php
    $totalPages = ceil($msgcount / $ppv);
    if (empty($_GET['page'])) { $_GET['page'] = 1; }
    $pagesPerSet = 10; // Set the number of pages per group
    $startPage = floor(($page - 1) / $pagesPerSet) * $pagesPerSet + 1;
    $endPage = min($startPage + $pagesPerSet - 1, $totalPages); ?>
    <?php if ($startPage < $totalPages && $page !== 1) { ?>
    <a href="outbox.php?page=<?php echo $_GET['page'] - 1; ?>"> < Previous</a>
    <?php } ?>

    <?php 
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $page) {
            echo '<span style="color: #444; background-color: #FFFFFF; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;">' . $i . '</span>';
        } else {
            echo '<span style="background-color: #CCC; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><a href="outbox.php?page=' . $i . '">' . $i . '</a></span>';
        }
    }
    ?>
    <!-- Add "Next" link if there are more pages -->
    <?php if ($endPage < $totalPages) { ?>
            <a href="outbox.php?page=<?php echo $_GET['page'] + 1; ?>">Next ></a>
    <?php } ?>
</div>
<?php } ?>
				<!-- end paging -->
		
		</td>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
	</tr>
	<tr>
		<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
		<td><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_br.gif" width="5" height="5"></td>
	</tr>
</tbody></table>
<?php } else if($nocompose == 'no') { ?>
<table width="100%" align="center" cellpadding="1" cellspacing="1" border="0" bgcolor="#EEEEEE">
	<tbody>
	<tr>
		
		<td width="100%"><img src="img/pixel.gif" width="1" height="5"> </td>
	</tr>
	<tr>
		
		<td>
				<form method="POST">
	<table width="75%" cellpadding="4" cellspacing="9" table="table" align="center">
                <tbody><tr><td colspan="1" height="10" width="35" align="right"></td></tr>
                                <tr valign="top">
					<td align="right" valign="top"><span class="label">To:</span></td>
					<td><input type="text" size="50" maxlength="75" name="user" value="<?php echo htmlspecialchars($profile['username']); ?>" disabled></td></tr>
                    <tr valign="top">
                </tr>
                <tr valign="top">
					<td align="right" valign="top"><span class="label">Sent:</span></td>
					<td><?php echo retroDate("now", "F j, Y, H:i A"); ?></td>
                </tr>
                <tr valign="top">
					<td align="right" valign="top"><span class="label">Attach a video:</span></td>
					<td><select name="field_reference_video">				<option value="">- Your Videos -</option>
			<?php foreach($videos_of_you as $myvideo) { ?><option value="<?php echo htmlspecialchars($myvideo['vid']);?>"><?php echo htmlspecialchars($myvideo['title']);?></option> 				<?php } ?><option value="">- Your Favorite Videos -</option>			
			<?php foreach($favorites_of_you as $myfavorites) { ?><option value="<?php echo htmlspecialchars($myfavorites['vid']);?>"><?php echo htmlspecialchars($myfavorites['title']);?></option> 				<?php } ?></select></td>
                </tr>
                <tr valign="top">
					<td align="right" valign="top"><span class="label">Subject:</span></td>
					<td><input type="text" size="50" maxlength="75" name="title" value="<?php if(isset($_GET['subject'])) { echo htmlspecialchars($_GET['subject']); } ?>"></td>
                </tr>
                <tr valign="top">
					<td align="right" valign="top"><span class="label">Message:</span></td>
					<td><textarea maxlength="50000" name="comment" cols="66" rows="6"></textarea></td>
                    
                </tr>
                <tr valign="top">
					<td align="right" valign="top"></td>
					<td><input type="submit" name="message" value="Send Message"></td>
                    
                </tr></form>
                                                </tbody></table>
		
		</td>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
	</tr>
	<tr>
		
		<td><img src="img/pixel.gif" width="1" height="5"></td>
		
	</tr>
</tbody></table>
<?php
}
require "needed/end.php";
?>