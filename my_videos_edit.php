<?php 
require "needed/start.php";
force_login();

if(!isset($_GET['video_id'])) {
	die(header("Location: my_videos.php"));
}

$video = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND uid = ?");
$video->execute([$_GET['video_id'], $session['uid']]);

if($video->rowCount() == 0) {
	die(header("Location: my_videos.php"));
} else {
	$video = $video->fetch(PDO::FETCH_ASSOC);
}
if ($_POST['private'] == 2) {
             $privacy = 2;
         } else {
             $privacy = 1;
         }

if ($_POST['comms_allow'] == 0) {
             $allow_comms = 0;
         } else if ($_POST['comms_allow'] == 3) {
             $allow_comms = 3;
         } else {
             $allow_comms = 1;
         }

if ($_POST['allow_votes'] == 0) {
             $allow_votes = 0;
         } else {
             $allow_votes = 1;
         }
if($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['field_command'] === "update_video") {
    $word_count = unique_word_count($_POST['field_upload_tags']);
    
    $checked_channels = [];
    if (isset($_POST['chlist'])) {
    $checked_channels = $_POST['chlist'];
    $checked_channels = array_slice($checked_channels, 0,3);
    } else {
    $nochannels = "yes";
    }
    if ($word_count < 1 || $nochannels == "yes" || empty($_POST['field_upload_title']) || empty($_POST['field_upload_description'])) {
    alert("Couldn't update video.", "error");
    } else {
	$update_video = $conn->prepare("UPDATE videos SET title = ?, description = ?, tags = ?, updated = CURRENT_TIMESTAMP, address = ?, addrcountry = ?, ch1 = ?, ch2 = ?, ch3 = ?, privacy = ?, allow_votes = ?, comms_allow = ? WHERE vid = ? AND uid = ?");
	$update_video->execute([
		trim($_POST['field_upload_title']),
		trim($_POST['field_upload_description']),
		trim($_POST['field_upload_tags']),
        isset($_POST['field_upload_address']) ? strip_tags($_POST['field_upload_address']) : null,
        isset($_POST['field_upload_country']) ? strip_tags($_POST['field_upload_country']) : null,
        isset($checked_channels[0]) ? $checked_channels[0] : 5,
        isset($checked_channels[1]) ? $checked_channels[1] : null,
        isset($checked_channels[2]) ? $checked_channels[2] : null,
        $privacy,
        $allow_votes,
        $allow_comms,
		$video['vid'],
		$session['uid']
	]);
     // force html to also update
    $video = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND uid = ?");
    $video->execute([$_GET['video_id'], $session['uid']]);
    $video = $video->fetch(PDO::FETCH_ASSOC);
	alert("Video has been updated!");
     }
}
?>

<table width="100%" align="center" bgcolor="#333333" cellpadding="6" cellspacing="3" border="0">
	<div class="tableSubTitle">Video Details <span style="float:right; font-size: 12px; font-weight: normal;"><a href="/my_videos.php">Back to "my videos"</a></span></div>
	<div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<form method="post">
			<input type="hidden" name="field_command" value="update_video">
			<tbody>
				<tr>
					<td width="200" align="right"><span class="label">Title:</span></td>
					<td><input type="text" size="30" maxlength="60" name="field_upload_title" value="<?php echo htmlspecialchars($video['title']); ?>"></td>
				</tr>
				<tr>
					<td align="right" valign="top"><span class="label">Description:</span></td>
					<td><textarea name="field_upload_description" cols="40" rows="4"><?php echo htmlspecialchars($video['description']); ?></textarea></td>
				</tr>
				<tr>
					<td width="200" align="right"><span class="label">Tags:</span></td>
					<td><input type="text" size="30" maxlength="60" name="field_upload_tags" value="<?php echo htmlspecialchars($video['tags']); ?>"></td>
                    
				</tr>
                </table>
                <span style="margin-left: 214px" class="formFieldInfo"><b> 	
Enter one or more tags, separated by spaces.</b></span><br>
<span style="margin-left: 214px" class="formFieldInfo">Tags are simply keywords used to describe
your video so they are easily searched and organized. For example,
        if you have a surfing video, you might tag it: surfing beach
waves.</span>
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
            <tr>
					<td width="200" align="right" valign="top"><span class="label">Video Channels:</span></td><td align="left" style="float: left" valign="top">
					<? foreach ($_VCHANE as $index => $channel) {
 				unset($isset);
 				if($video['ch1'] == $index || $video['ch2'] == $index || $video['ch3'] == $index) { $isset = 'checked'; }
 				if ($index == 11) { echo "</td><td style=\"float: left\" align=\"left\" valign=\"top\">"; } elseif ($index != 1){ echo "<br>"; }
    echo "<input type=\"checkbox\" name=\"chlist[]\" value=\"" . $index . "\"" . $isset . "><label for=\"ch" . $index . "\">" . $channel . "</label>\n";
    } ?>

    
                    
                    </td>
                    
				</tr>
          </table>  <p><div class="tableSubTitle">Date & Address Details</div>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td width="200" align="right"><span class="label">Date Recorded:</span><br><span class="formFieldInfo">(Doesn't work yet)</span></td>
					<td><select name="addr_month" tabindex="13">
						<option value="---"<?php if ($video['recorddate'] == null) {echo " selected";} ?>>---</option>
							<option value="1"<?php if (date('m', strtotime($video['recorddate'])) === '1') {echo " selected";} ?>> Jan  </option>
							<option value="2"<?php if (date('m', strtotime($video['recorddate'])) === '2') {echo " selected";} ?>> Feb  </option>
							<option value="3"<?php if (date('m', strtotime($video['recorddate'])) === '3') {echo " selected";} ?>> Mar  </option>
							<option value="4"<?php if (date('m', strtotime($video['recorddate'])) === '4') {echo " selected";} ?>> Apr  </option>
							<option value="5"<?php if (date('m', strtotime($video['recorddate'])) === '5') {echo " selected";} ?>> May  </option>
							<option value="6"<?php if (date('m', strtotime($video['recorddate'])) === '6') {echo " selected";} ?>> Jun  </option>
							<option value="7"<?php if (date('m', strtotime($video['recorddate'])) === '7') {echo " selected";} ?>> Jul  </option>
							<option value="8"<?php if (date('m', strtotime($video['recorddate'])) === '8') {echo " selected";} ?>> Aug  </option>
							<option value="9"<?php if (date('m', strtotime($video['recorddate'])) === '9') {echo " selected";} ?>> Sep  </option>
							<option value="10"<?php if (date('m', strtotime($video['recorddate'])) === '10') {echo " selected";} ?>> Oct  </option>
							<option value="11"<?php if (date('m', strtotime($video['recorddate'])) === '11') {echo " selected";} ?>> Nov  </option>
							<option value="12"<?php if (date('m', strtotime($video['recorddate'])) === '12') {echo " selected";} ?>> Dec  </option>
					</select>
					<select name="addr_day" tabindex="14">
						<option value="---"<?php if ($video['recorddate'] == null) {echo " selected";} ?>>---</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '1') {echo "selected";} ?>>1</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '2') {echo "selected";} ?>>2</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '3') {echo "selected";} ?>>3</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '4') {echo "selected";} ?>>4</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '5') {echo "selected";} ?>>5</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '6') {echo "selected";} ?>>6</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '7') {echo "selected";} ?>>7</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '8') {echo "selected";} ?>>8</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '9') {echo "selected";} ?>>9</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '10') {echo "selected";} ?>>10</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '11') {echo "selected";} ?>>11</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '12') {echo "selected";} ?>>12</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '13') {echo "selected";} ?>>13</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '14') {echo "selected";} ?>>14</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '15') {echo "selected";} ?>>15</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '16') {echo "selected";} ?>>16</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '17') {echo "selected";} ?>>17</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '18') {echo "selected";} ?>>18</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '19') {echo "selected";} ?>>19</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '20') {echo "selected";} ?>>20</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '21') {echo "selected";} ?>>21</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '22') {echo "selected";} ?>>22</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '23') {echo "selected";} ?>>23</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '24') {echo "selected";} ?>>24</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '25') {echo "selected";} ?>>25</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '26') {echo "selected";} ?>>26</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '27') {echo "selected";} ?>>27</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '28') {echo "selected";} ?>>28</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '29') {echo "selected";} ?>>29</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '30') {echo "selected";} ?>>30</option>
							<option <?php if (date('d', strtotime($video['recorddate'])) === '31') {echo "selected";} ?>>31</option>
					</select>					
					<select name="addr_yr" tabindex="15">
						<option value="---"<?php if ($video['recorddate'] == null) {echo " selected";} ?>>---</option>
							<?php
							$selectedYear = date('Y', strtotime($video['recorddate']));
							$years = range(1900, 2010);
							foreach ($years as $year) {
								$selected = ($year == $selectedYear) ? " selected" : "";
								echo '<option' . $selected . '>' . $year . '</option>';
							}
							?>
							<?php /* just for now
								<option>2023</option>
								<option>2022</option>
								<option>2021</option>
								<option>2020</option>
								<option>2019</option>
								<option>2018</option>
								<option>2017</option>
								<option>2016</option>
								<option>2015</option>
								<option>2014</option>
								<option>2013</option>
								<option>2012</option>
								<option>2011</option>
								<option>2010</option>
								<option>2009</option>
								<option>2008</option>
								<option>2007</option>
								<option>2006</option>
								<option>2005</option>
								<option>2004</option>
								<option>2003</option>
								<option>2002</option>
								<option>2001</option>
								<option>2000</option>
								<option>1999</option>
								<option>1998</option>
								<option>1997</option>
								<option>1996</option>
								<option>1995</option>
								<option>1994</option>
								<option>1993</option>
								<option>1992</option>
								<option>1991</option>
								<option>1990</option>
								<option>1989</option>
								<option>1988</option>
								<option>1987</option>
								<option>1986</option>
								<option>1985</option>
								<option>1984</option>
								<option>1983</option>
								<option>1982</option>
								<option>1981</option>
								<option>1980</option>
								<option>1979</option>
								<option>1978</option>
								<option>1977</option>
								<option>1976</option>
								<option>1975</option>
								<option>1974</option>
								<option>1973</option>
								<option>1972</option>
								<option>1971</option>
								<option>1970</option>
								<option>1969</option>
								<option>1968</option>
								<option>1967</option>
								<option>1966</option>
								<option>1965</option>
								<option>1964</option>
								<option>1963</option>
								<option>1962</option>
								<option>1961</option>
								<option>1960</option>
								<option>1959</option>
								<option>1958</option>
								<option>1957</option>
								<option>1956</option>
								<option>1955</option>
								<option>1954</option>
								<option>1953</option>
								<option>1952</option>
								<option>1951</option>
								<option>1950</option>
								<option>1949</option>
								<option>1948</option>
								<option>1947</option>
								<option>1946</option>
								<option>1945</option>
								<option>1944</option>
								<option>1943</option>
								<option>1942</option>
								<option>1941</option>
								<option>1940</option>
								<option>1939</option>
								<option>1938</option>
								<option>1937</option>
								<option>1936</option>
								<option>1935</option>
								<option>1934</option>
								<option>1933</option>
								<option>1932</option>
								<option>1931</option>
								<option>1930</option>
								<option>1929</option>
								<option>1928</option>
								<option>1927</option>
								<option>1926</option>
								<option>1925</option>
								<option>1924</option>
								<option>1923</option>
								<option>1922</option>
								<option>1921</option>
								<option>1920</option>
								<option>1919</option>
								<option>1918</option>
								<option>1917</option>
								<option>1916</option>
								<option>1915</option>
								<option>1914</option>
								<option>1913</option>
								<option>1912</option>
								<option>1911</option>
								<option>1910</option>
								<option>1909</option>
								<option>1908</option>
								<option>1907</option>
								<option>1906</option>
								<option>1905</option>
								<option>1904</option>
								<option>1903</option>
								<option>1902</option>
								<option>1901</option>
								<option>1900</option>
								*/?>
					</select></td>
				</tr>

				<tr>
					<td width="200" align="right"><span class="label">Address Recorded:</span><br><span class="formFieldInfo">(Optional)</span></td>
					<td><input type="text" size="30" maxlength="160" name="field_upload_address" value="<?php echo htmlspecialchars($video['address']); ?>"></td>
				</tr>
                </table><span style="margin-left: 214px" class="formFieldInfo">It helps to use relevant keywords so that others can find your video!</span><div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td width="200" align="right"><span class="label">Country:</span><br><span class="formFieldInfo">(Optional)</span></td>
					<td><?php echo '<select name="field_upload_country" tabindex="5">';
                        foreach ($_COUNTRIES as $code => $name) {
                        echo '<option ';
                        echo ($video['addrcountry'] == $name) ? ' selected' : '';
                        echo '>' . $name . '</option>';
                        }
                    echo '</select>';?></td>
                    </tr></table><div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0"><div class="tableSubTitle">Sharing:</div><tr>
        <tr>
                   <td width="200" align="right" valign="top"><span class="label">Video URL:</span></td>
        <td>
									<input name="video_link" type="text" onClick="document.linkForm_<?php echo htmlspecialchars($video['vid']); ?>.video_link.focus();document.linkForm_<?php echo htmlspecialchars($video['vid']); ?>.video_link.select();" value="http://www.kamtape.com/?v=<?php echo htmlspecialchars($video['vid']); ?>" size="50" readonly="true" style="font-size: 10px; text-align: center;">

        </td>
        </tr>
        <tr>
                   <td width="200" align="right" valign="top"><span class="label">Broadcast:</span></td>
		<td>

                <input type="radio" name="private" value="1" <?php if ($video['privacy'] == 1) { echo 'checked="true"'; } ?>> 
                <label for="1"><strong>Public</strong>: Share your video with the world! (Reccomended)</label><br>
                <input type="radio" name="private" value="2" <?php if ($video['privacy'] == 2) { echo 'checked="true"'; } ?>>
                <label for="2"><strong>Private</strong>: Only viewable by you and the people you specify.</label><br>
		</td>
        </tr>
        <tr>
                   <td width="200" align="right" valign="top"><span class="label">Allow Comments:</span></td>
		<td>

                <input type="radio" name="comms_allow" value="1" <?php if ($video['comms_allow'] == 1) { echo 'checked="true"'; } ?>> 
                <label for="1"><strong>Yes</strong>: Allow comments to be added to your video.</label><br>
                <input type="radio" name="comms_allow" value="0" <?php if ($video['comms_allow'] == 0) { echo 'checked="true"'; } ?>>
                <label for="0"><strong>No</strong>: Disallow comments to be added to your video.</label><br>
		</td>
        </tr>
        <tr>
                   <td width="200" align="right" valign="top"><span class="label">Allow Ratings:</span></td>
		<td>

                <input type="radio" name="allow_votes" value="1" <?php if ($video['allow_votes'] == 1) { echo 'checked="true"'; } ?>> 
                <label for="1"><strong>Yes</strong>: Allow people to rate your video.</label><br>
                <input type="radio" name="allow_votes" value="0" <?php if ($video['allow_votes'] == 0) { echo 'checked="true"'; } ?>>
                <label for="0"><strong>No</strong>: Disallow people to rate your video.</label><br><span class="formFieldInfo">If you disable ratings, this video will no longer be eligible to appear on the list of "Top Rated" videos.</span>
		</td>
        </tr>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Update Video"></td></form>
				</tr>
				<tr>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php 
require "needed/end.php";
?>