<?php
require "needed/start.php";
if($_SERVER["REQUEST_METHOD"] === "POST") {
    if(empty(trim($_POST['field_contact_email']))) {
		$contact_error = "Please enter an email.";
	} elseif(!filter_var(trim($_POST['field_contact_email']), FILTER_VALIDATE_EMAIL)) {
		$contact_error = "Please enter a valid email.";
    }
    if($_POST['field_contact_subject'] > 7 || $_POST['field_contact_subject'] < 1) {$contact_error = "What is this about?"; }
    $word_count = unique_word_count($_POST['field_contact_message']);
    if ($word_count < 8) {
    $contact_error = "Please provide enough details so we can properly process your e-mail";
    }
    if ($word_count > 620) {
    $contact_error = "Too much words.";
    }
    $cooldown = $conn->prepare(
			"SELECT * FROM tickets
			WHERE sender = ? AND submitted > DATE_SUB(NOW(), INTERVAL 3 HOUR)
			ORDER BY submitted DESC"
		);
        $cooldown->execute([$_POST['field_contact_email']]);
		if($cooldown->rowCount() > 2) {
		$contact_error = "Are you sure you meant to send that 2 times?";
		}
    if(!empty($contact_error)){
    alert($contact_error, "error");
    }
    
    if(empty($contact_error)){
    // Submit the inquiry!
    $sql = "INSERT INTO tickets (sender, subject, message) VALUES (:email, :subject, :message)";

    // Bind the parameters to the prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $_POST['field_contact_email']);
    $stmt->bindParam(":subject", $_POST['field_contact_subject']);
    $stmt->bindParam(":message", encrypt($_POST['field_contact_message']));
  
    // Execute the prepared statement
    try {
    $stmt->execute();
    session_error_index("Thank you for your message.", "success");
    } catch (PDOException $e) {
    alert("Was unable to contact.", "error");
    }
    }

    }
    
?>



<div class="pageTitle">Contact Us</div>

<div class="pageTable">

<div style="padding-left: 23px;">
			KamTape is located in the bright state of New York, USA.
			<br><br>If you have questions or comments for us, please fill out the form below.
			<br><br>If this is a product or technical question, please provide as much detail as possible.
			<li>What is your KamTape username?
			</li><li>What exactly were you trying to do? (provide full URL)
			</li><li>What is the exact page you received? (copy and paste any error messages)
			</li></div>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<form method="post" action="contact.php">
	<input type="hidden" name="field_command" value="contact_submit">
	<tbody><tr>
		<td width="200" align="right"><span class="label">Your Email:</span></td>
		<td><input type="text" size="30" maxlength="60" name="field_contact_email"></td>
	</tr>
	<tr>
		<td align="right"><span class="label">Subject:</span></td>

		
		<td><select name="field_contact_subject">
			<option value="0">---</option>
			<option value="1">Product Question</option>
			<option value="2">Business Inquiry</option>
			<option value="3">Marketing and Advertising Inquiry</option>
			<option value="4">Developer Question</option>
			<option value="5">Press Inquiry</option>
			<option value="6">Job Inquiry</option>
			<option value="7">Other</option>
			</select></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label">Message:</span></td>
		<td><textarea name="field_contact_message" cols="40" rows="4"></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Contact Now"></td></form>
	</tr>
</tbody></table>

</div>
<?php
require "needed/end.php";
?>