<?php
require "admin_head.php";

force_login();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = $conn->prepare("SELECT * FROM tickets LEFT JOIN users ON users.email = tickets.sender WHERE ticket = ?");
$msg->execute([$_GET['n']]);

$_TYPE = [
  1 => 'Product Question',
  2 => 'Business Inquiry',
  3 => 'Marketing and Advertising Inquiry',
  4 => 'Developer Question',
  5 => 'Press Inquiry',
  6 => 'Job Inquiry',
  7 => 'Other Question'
];

if($msg->rowCount() == 0) {
	header("Location: ../admin/tickets.php");
	die();
} else {
	$msg = $msg->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['kill'])){
    $remove_video = $conn->prepare("DELETE FROM tickets WHERE ticket = :pmid");
	$remove_video->execute([
		":pmid" => $msg['ticket']
	]);
    header("Location: ../admin/tickets.php");
}
if (!empty($_POST['solution']) && $msg['resolved'] != 1) {
    $read_detect = $conn->prepare("UPDATE tickets SET resolved = 1 WHERE ticket = ?");
	$read_detect->execute([
		trim($msg['ticket'])
	]);

$mail = new PHPMailer(true);                              
try {
    //Server settings
    $mail->SMTPDebug = 0;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $config["host"];                  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $config["supmail"];                // SMTP username
    $mail->Password = $config["suppass"];
    $mail->SMTPSecure = 'ssl';                            // Enable SSL encryption, TLS also accepted with port $config["emport"]
    $mail->Port = $config["emport"];                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($config["supmail"], 'KamTape Support');
    $mail->addAddress($msg['sender']);     // Add a recipient  

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Ticket #'.$msg['ticket'].' Solved ('. $_TYPE[$msg['subject']] . ')';
    $mail->Body    = 'We have solved your issue
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="177" align="right" width="62" bgcolor="#7de2a2" style="vertical-align: top;">Solution:</td>
			<td style="vertical-align: top;">'. $_POST['solution'].'
        <p>Thanks you for using KamTape,<br>The KamTape Team</td>
		</tr>
	</tbody>
</table>';

    $mail->send();
    alert("Resolved.");
} catch (Exception $e) {
   alert("Was unable to resolve, sorry.", "error");
}
}

?>

<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="585">
				<div class="moduleTitleBar">
				<div class="moduleTitle"><div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;"><a href="tickets.php">Go Back</a></div>
				<?php echo htmlspecialchars($msg['sender']); ?>'s <? if (isset($_TYPE[$msg['subject']])) {
                echo $_TYPE[$msg['subject']];
                } else {
                echo "Unknown Subject";
                } ?>  <br><span style="font-weight:normal; font-size: 11px;">Note that once you reply to them, tickets are automatically resolved.</span>
				</div>
				</div>
                 <div class="moduleEntry">
					<table width="565" cellpadding="0" cellspacing="0" border="0">
						<tbody><tr valign="top">
                            <td width="100%"><strong>Sender</strong>: <?php echo htmlspecialchars($msg['sender']); ?>
                            <p><strong>Email Match</strong>: <?php echo htmlspecialchars($msg['username']); ?>
                            <p><strong>Initally Sent</strong>: <?php echo retroDate($msg['submitted']); ?>
							<div class="mailMessageArea"><?php echo decrypt($msg['message']); ?></div></td>
							</td>
						</tr>
					</tbody></table>
					</div>
                </td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table>
        <? if ($msg['resolved'] != 1) {?>
        <p><strong>Resolve This Ticket:</strong><p>
        <form method="POST"><textarea maxlength="350000" name="solution" cols="66" rows="6"></textarea><p><input type="submit" name="submit" value="Resolve">&nbsp;<input type="reset" name="reset" value="Start Over">&nbsp;<input type="submit" name="kill" value="Rip Ticket"></form>
        <? } ?>
<?php
require "needed/end.php";
?>