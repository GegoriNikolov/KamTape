<?php
require "needed/start.php";

// Make sure the user is logged in.
if($_SESSION['uid'] == NULL) {
	header("Location: login.php", true, 401);
	die();
}
if ($_GET['friend_id'] == $session['uid']){ redirect("index.php"); }
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['friend_id']]);
if($profile->rowCount() == 0) {
	redirect("index.php");
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
    if($profile['termination'] == 1) {
    redirect("index.php");
    }
}

    $alreadyrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :member_id AND respondent = :him");
    $alreadyrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $profile['uid']
    ]);
    $alreadyrelated = $alreadyrelated->fetchColumn();

    if($alreadyrelated === 1) {
	$friendswith = 1;
    }

    $newrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :him AND respondent = :member_id");
    $newrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $profile['uid']
    ]);
    $newrelated = $newrelated->fetchColumn();

    if($newrelated === 1) {
	$friendswith = 1;
    }  

    if($friendswith > 0) {
    session_error_index("You've already sent an invitation to this user.", "error");
    die();
    }
    

if ($_POST['t'] == 2) { $closness = 2; } else { $closness = 1; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['t'])) {
// Add it to favorites!
$relate = $conn->prepare("INSERT INTO relationships (relationship, sender, respondent, status) VALUES (:relationship, :member_id, :respondent, :closness)");
$relate->execute([
    ":relationship" => generateId(),
	":member_id" => $session['uid'],
	":respondent" => $profile['uid'],
    ":closness" => $closness
]);
    session_error_index("Your request has been sent!", "success");
}
?>
<div class="tableSubTitle">Friend Invitation</div>
<div class="tableSubTitleIntro"><p>Send an invitation if you know this user wish to share private videos with each other.</p></div>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<form method="post">
	<input type="hidden" name="field_command" value="friend_add">
	<tbody><tr>
		<td width="200" align="right" valign="top"><span class="label">User Name:</span></td>
		<td><a href="/user/<? echo htmlspecialchars($profile['username']); ?>"><? echo htmlspecialchars($profile['username']); ?></a><br><br></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label">Add As:</span></td>

		
		<td><select name="t">
			<option value="1">Friends</option>
            <option value="2">Family</option>
			</select>
            <br><div class="formFieldInfo">They will be able to see the private videos you share with these groups in addition to your public videos.</div>
            </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br><input type="submit" value="Send Invite"></td></form>
	</tr>
</tbody></table>
<? require "needed/end.php"; ?>