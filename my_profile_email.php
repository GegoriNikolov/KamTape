<?php
require "needed/start.php";
if(empty($_SESSION['uid'])) {
	header("Location: index.php");
}
$member = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$member->execute([$session['uid']]);
$member = $member->fetch(PDO::FETCH_ASSOC);
if(isset($_POST['emailprefs_vdocomments']) && 
$_POST['emailprefs_vdocomments'] == 'true') {
$vdo_comm = 1;
} else {
$vdo_comm = 0;
}
if(isset($_POST['emailprefs_privatem']) && 
$_POST['emailprefs_privatem'] == 'true') {
$privatememmies= 1;
} else {
$privatememmies = 0;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
        echo $vdo_comm;
        echo $privatememmies;
		$update_video = $conn->prepare("UPDATE users SET emailprefs_privatem = ?, emailprefs_vdocomments = ?  WHERE uid = ?");
		$update_video->execute([
			$vdo_comm,
 		    $privatememmies,
			$session['uid']
		]);
        alert("Your preferences have been updated.");

}

?>
<table width="45%" align="center" cellpadding="5" cellspacing="0" border="0">
         <tr align="center">
		 <td align="center" colspan="3">
                <a href="/my_profile.php">My Profile</a> | <span class="bold">My Email Preferences</span>
            </td></tr>
            </table>
<div class="formTable">
    <form method="post" action="my_profile_email.php">
        <table cellpadding="5" width="700" cellspacing="0" border="0" align="center">
               <tr valign="top">
					<td colspan="2"><div class="highlight">Email Preferences</div><div class="tableSubTitleInfo">Here you can choose which types of emails you'd like to get from KamTape.</div></td>
				</tr>
                <tr valign="top">
					<td><input type="checkbox" name="emailprefs_privatem" value="true" <? if($member['emailprefs_privatem'] == 1) { echo 'checked'; } ?>><label for="true">Email me on Private Messages</label></td>
                </tr>
                <tr valign="top">
					<td><input type="checkbox" name="emailprefs_vdocomments" value="true" <? if($member['emailprefs_vdocomments'] == 1) { echo 'checked'; } ?>><label for="true">Email me on Video Comments</label></td>
                </tr>
				<tr valign="top">
                    <td><input type="submit" id="save" name="save" value="Update Preferences"></td>
                </tr>
        </table>
    </form>
</div>

<?php
unset($_SESSION['alert']);
require "needed/end.php";
?>