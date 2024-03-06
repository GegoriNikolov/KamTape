<?php
require "needed/start.php";
force_login();
$member = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$member->execute([$session['uid']]);
$member = $member->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$_POST['birthday'] = date("Y-m-d", strtotime($_POST['birthday']));
    if ($_POST['website'] != NULL){
   if (filter_var($_POST['website'], FILTER_VALIDATE_URL) === FALSE) {
    $profile_err = "This URL doesn't look right.";
}
    }
    if ($_POST['email'] == NULL){
    $profile_err = "Please enter an email, and then try again.";
    } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === FALSE) {
    $profile_err = "This email doesn't look right.";
    }
if (!empty(trim($_POST['country'])) && !array_key_exists(trim($_POST['country']),$_COUNTRIES)) {
    $profile_err = 'Not a country.';
}
if($_POST['birthday_mon'] != '---' && $_POST['birthday_day'] != '---' && $_POST['birthday_yr'] != '---') {
    $birthday_mon = $_POST['birthday_mon'];
$birthday_day = $_POST['birthday_day'];
$birthday_yr = $_POST['birthday_yr'];
$currentDate = new DateTime();
$birthday = $birthday_yr . '-' . $birthday_mon . '-' . $birthday_day;
$dateTime = DateTime::createFromFormat('Y-m-d', $birthday);
$errors = DateTime::getLastErrors();

if ($dateTime === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
    // Date format is invalid
   $profile_err = "Invalid age.";
}
$diff = $currentDate->diff($dateTime);

if ($diff->y < 13) {
    $profile_err = "Invalid age.";
}
if ($diff->y > 123) {
    $profile_err = "Invalid age.";
}
}
if(strlen($_POST['about']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['website']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['name']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['hometown']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['city']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['occupations']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['companies']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['schools']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['hobbies']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['fav_media']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['music']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if(strlen($_POST['books']) > 500) {
    $profile_err = "Please shorten some of your entered information and try again.";
}
if (!empty($profile_err)) {
   alert($profile_err, "error");
}
	if (empty($profile_err)) {
	if($_POST['birthday_mon'] === '---' && $_POST['birthday_day'] === '---' && $_POST['birthday_yr'] === '---') {
		$update_video = $conn->prepare("UPDATE users SET name = ?, email = ?, birthday = NULL, gender = ?, relationship = ?, about = ?, website = ?, hometown = ?, city = ?, country = ?, occupations = ?, companies = ?, schools = ?, hobbies = ?, fav_media = ?, music = ?, books = ? WHERE uid = ?");
		$update_video->execute([
			trim($_POST['name']),
            trim($_POST['email']),
			trim($_POST['gender']),
			trim($_POST['relationship']),
			trim($_POST['about']),
			trim($_POST['website']),
			trim($_POST['hometown']),
			trim($_POST['city']),
			trim($_POST['country']),
			trim($_POST['occupations']),
			trim($_POST['companies']),
			trim($_POST['schools']),
			trim($_POST['hobbies']),
			trim($_POST['fav_media']),
			trim($_POST['music']),
			trim($_POST['books']),
			$session['uid']
		]);
        alert("Profile has been successfully updated.");
	} else {
		$update_video = $conn->prepare("UPDATE users SET name = ?, email = ?, birthday = ?, gender = ?, relationship = ?, about = ?, website = ?, hometown = ?, city = ?, country = ?, occupations = ?, companies = ?, schools = ?, hobbies = ?, fav_media = ?, music = ?, books = ? WHERE uid = ?");
		$update_video->execute([
			trim($_POST['name']),
            trim($_POST['email']),
			trim($birthday),
			trim($_POST['gender']),
			trim($_POST['relationship']),
			trim($_POST['about']),
			trim($_POST['website']),
			trim($_POST['hometown']),
			trim($_POST['city']),
			trim($_POST['country']),
			trim($_POST['occupations']),
			trim($_POST['companies']),
			trim($_POST['schools']),
			trim($_POST['hobbies']),
			trim($_POST['fav_media']),
			trim($_POST['music']),
			trim($_POST['books']),
			$session['uid']
		]);
        alert("Profile has been successfully updated.");
	}
    }
}

?>
<table width="45%" align="center" cellpadding="5" cellspacing="0" border="0">
         <tr align="center">
		 <td align="center" colspan="3">
                <span class="bold">My Profile</span> | <a href="/my_profile_email.php" >My Email Preferences</a>
            </td></tr>
            </table>
<div class="formTable">
    <form method="post" action="my_profile.php">
        <table cellpadding="5" width="700" cellspacing="0" border="0" align="center">
               <tr valign="top">
					<td colspan="2"><div class="tableSubTitle"><span style="float:right; font-size: 12px; font-weight: normal;"><a href="/user/<?= htmlspecialchars($member['username']) ?>">View Your Profile Page</a></span>Account Information</div><div class="tableSubTitleInfo">* Indicates required field. <span style="float:right; font-size: 12px;"><a style="font-weight: bold; color:#f22b33;" href="my_account_delete.php">Delete Your Account</a></span></div></td>
				</tr>
                <tr valign="top">
					<td align="right"><span class="label">User Name:</span></td>
					<td><?= htmlspecialchars($member['username']) ?></td>
                </tr>
                <tr valign="top">
                <td align="right"><span class="label">Email:</span></td>
				<td><input type="text" size="20" maxlength="500" name="email" value="<?php echo (!empty($member['email'])) ? htmlspecialchars($member['email']) : ""; ?>">*</td>
                </tr>
				<tr valign="top">
					<td colspan="2"><div class="tableSubTitle">Personal Information</div></td>
				</tr>
				<tr valign="top">
					<td align="right"><span class="label">Name</span></td>
					<td><input type="text" size="20" maxlength="500" name="name" value="<?php echo (!empty($member['name'])) ? htmlspecialchars($member['name']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Birthday:</span></td>
					<td>
					<select name="birthday_mon">
                             <option value="---" <?php if (strtotime($member['birthday']) == strtotime('1969-12-31')) echo "selected"; ?>>---</option>
                            <option value="1" <?php if (date('m', strtotime($member['birthday'])) === '01') echo "selected"; ?>>Jan</option>
                            <option value="2" <?php if(date('m', strtotime($member['birthday'])) === '02') { echo "selected"; } ?>> Feb  </option>
							<option value="3" <?php if(date('m', strtotime($member['birthday'])) === '03') { echo "selected"; } ?>> Mar  </option>
							<option value="4" <?php if(date('m', strtotime($member['birthday'])) === '04') { echo "selected"; } ?>> Apr  </option>
							<option value="5" <?php if(date('m', strtotime($member['birthday'])) === '05') { echo "selected"; }?>> May  </option>
							<option value="6" <?php if(date('m', strtotime($member['birthday'])) === '06') { echo "selected"; }?>> Jun  </option>
							<option value="7" <?php if(date('m', strtotime($member['birthday'])) === '07') { echo "selected"; }?>> Jul  </option>
							<option value="8" <?php if(date('m', strtotime($member['birthday'])) === '08') { echo "selected"; }?>> Aug  </option>
							<option value="9" <?php if(date('m', strtotime($member['birthday'])) === '09') { echo "selected"; }?>> Sep  </option>
							<option value="10" <?php if(date('m', strtotime($member['birthday'])) === '10') { echo "selected";} ?>> Oct  </option>
							<option value="11" <?php if(date('m', strtotime($member['birthday'])) === '11') { echo "selected"; }?>> Nov  </option>
							<option value="12" <?php if(date('m', strtotime($member['birthday'])) === '12' && date('Y', strtotime($member['birthday'])) != '1969') { echo "selected"; }?>> Dec  </option>
</select>

<select name="birthday_day">
    <option value="---" <?php if (strtotime($member['birthday']) == strtotime('1969-12-31')) echo "selected"; ?>>---</option>
    <?php
    for ($day = 1; $day <= 31; $day++) {
        $selected = (date('d', strtotime($member['birthday'])) == $day && date('Y', strtotime($member['birthday'])) != '1969') ? "selected" : "";
        echo '<option ' . $selected . '>' . $day . '</option>';
    }
    ?>
</select>

<select name="birthday_yr">
    <option value="---" <?php if (strtotime($member['birthday']) == strtotime('1969-12-31')) echo "selected"; ?>>---</option>
    <?php
    $selectedYear = date('Y', strtotime($member['birthday']));
    $years = range(1900, 2010);
    foreach ($years as $year) {
		if($selectedYear == '1969') {
			$selected = "";
		} else {
			$selected = ($year == $selectedYear) ? "selected" : "";
		}
        
        echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
    }
    ?>
</select>

				</td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Gender:</span></td>
					<td>
						<select name="gender">
							<option value="0" <?php echo ($member['gender'] == 0) ? "selected" : ""; ?>>Prefer not to say</option>
							<option value="1" <?php echo ($member['gender'] == 1) ? "selected" : ""; ?>>Male</option>
							<option value="2" <?php echo ($member['gender'] == 2) ? "selected" : ""; ?>>Female</option>
                            <option value="3" <?php echo ($member['gender'] == 3) ? "selected" : ""; ?>>Other</option>
						</select>
					</td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Relationship Status:</span></td>
					<td>
						<select name="relationship">
							<option value="0" <?php echo ($member['relationship'] == 0) ? "selected" : ""; ?>>Prefer not to say</option>
							<option value="1" <?php echo ($member['relationship'] == 1) ? "selected" : ""; ?>>Single</option>
							<option value="2" <?php echo ($member['relationship'] == 2) ? "selected" : ""; ?>>Taken</option>
						</select>
					</td>
                </tr>
				<tr valign="top">
					<td align="right" valign="top"><span class="label">About Me:</span><br><span class="formFieldInfo">(Describe Yourself)</span></td>
					<td><textarea maxlength="500" name="about" rows="5" cols="45"><?php echo (!empty($member['about'])) ? htmlspecialchars($member['about']) : ""; ?></textarea></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Personal Website:</span></td>
					<td><input type="text" size="20" maxlength="500" name="website" value="<?php echo (!empty($member['website'])) ? htmlspecialchars($member['website']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td colspan="2"><br><div class="tableSubTitle">Location Information</div></td>
				</tr>
				<tr valign="top">
					<td align="right"><span class="label">Hometown:</span></td>
					<td><input type="text" size="50" maxlength="500" name="hometown" value="<?php echo (!empty($member['hometown'])) ? htmlspecialchars($member['hometown']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Current City:</span></td>
					<td><input type="text" size="50" maxlength="500" name="city" value="<?php echo (!empty($member['city'])) ? htmlspecialchars($member['city']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Current Country:</span></td>
					<td><?php echo '<select name="country">';
foreach ($_COUNTRIES as $code => $name) {
    echo '<option value="' . $code . '"';
    echo ($member['country'] == $code) ? ' selected' : '';
    echo '>' . $name . '</option>';
}
echo '</select>';?></td>
                </tr>
				<tr valign="top">
					<td colspan="2"><br><div class="tableSubTitle">Random Information</div><div class="tableSubTitleInfo">Separate items with commas.</div></td>
                    
				</tr>
				<tr valign="top">
					<td align="right"><span class="label">Occupations:</span></td>
					<td><input maxlength="500" size="40" name="occupations" value="<?php echo (!empty($member['occupations'])) ? htmlspecialchars($member['occupations']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Companies:</span></td>
					<td><input maxlength="500" size="40" name="companies" value="<?php echo (!empty($member['companies'])) ? htmlspecialchars($member['companies']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Schools:</span></td>
					<td><input maxlength="500" size="40" name="schools" value="<?php echo (!empty($member['schools'])) ? htmlspecialchars($member['schools']) : ""; ?>"></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Interests & Hobbies:</span></td>
					<td><textarea maxlength="500" name="hobbies" rows="5" cols="45"><?php echo (!empty($member['hobbies'])) ? htmlspecialchars($member['hobbies']) : ""; ?></textarea></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Favorite Movies & Shows:</span></td>
					<td><textarea maxlength="500" name="fav_media" rows="5" cols="45"><?php echo (!empty($member['fav_media'])) ? htmlspecialchars($member['fav_media']) : ""; ?></textarea></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Favorite Music:</span></td>
					<td><textarea maxlength="500" name="music" rows="5" cols="45"><?php echo (!empty($member['music'])) ? htmlspecialchars($member['music']) : ""; ?></textarea></td>
                </tr>
				<tr valign="top">
					<td align="right"><span class="label">Favorite Books:</span></td>
					<td><textarea maxlength="500" name="books" rows="5" cols="45"><?php echo (!empty($member['books'])) ? htmlspecialchars($member['books']) : ""; ?></textarea></td>
                </tr>
				<tr valign="top">
                    <td></td>
                    <td><input type="submit" id="save" name="save" value="Update Profile"></td>
                </tr>
        </table>
    </form>
</div>

<?php
unset($_SESSION['alert']);
require "needed/end.php";
?>