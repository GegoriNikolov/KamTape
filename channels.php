<?php 
require "needed/start.php";
$_VCHANE_DESC = [
    1 => "Artistic, Computer Graphics, Anime...",
    2 => "Cars, Boats, Airplanes...",
    3 => "Tutorials, Software Demos, Cooking Techniques...",
    4 => "Parties, Birthdays, Graduations...",
    5 => "Trailers, Commercials...",
    6 => "Babies, Holidays, Memories...",
    7 => "eBay, Craigslist...",
    8 => "Haunted Dolls, Cooking, RC Planes...",
    9 => "Funny, Bloopers, Pranks...",
    10 => "Dancing, Singing, Guitars...",
    11 => "Breaking News, Weather, Speeches...",
    12 => "Flips, Jumps, Unexplainable...",
    13 => "Celebrities, Hot Girls, Cool Guys...",
    14 => "Video Profiles, Interesting People...",
    15 => "Cats, Dogs, Fish, Zoo...",
    16 => "Gadgets, Reviews, Space Shuttle...",
    17 => "Games, Stadiums, Tailgating...",
    18 => "Self Produced, Indie Films...",
    19 => "Vacations, International, Nature...",
    20 => "Demos, Previews...",
    21 => "Blogs, Opinions, Diaries..."
    //22 => "Cooking",
];
?>
<table width="790" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
	<tbody><tr>
		<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
		<td><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		<td width="780">
		<div class="moduleTitleBar">
			<table cellpadding="0" cellspacing="0" border="0">
				<tbody><tr valign="top">
					<td width="360">

						<div class="moduleTitle">
							
							All Channels

													</div>
					</td>
				</tr>
			</tbody></table>
		</div>
				
		<div class="moduleFeatured"> 
			<table width="770" cellpadding="0" cellspacing="0" border="0">
									<tbody><tr valign="top">
						<td width="100%" align="center">
							<table width="770" cellpadding="0" cellspacing="0" border="0">
																<tbody><tr valign="top">
									<?php
    foreach ($_VCHANE as $cnum => $channel) {
    $video = $conn->prepare("SELECT vid FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND (ch1 = ? OR ch2 = ? OR ch3 = ?) ORDER BY uploaded DESC LIMIT 1");
    //if($video->rowCount() != 0) { $video->fetch(PDO::FETCH_ASSOC); }
    $video->execute([$cnum, $cnum, $cnum]);
    if($video->rowCount() != 0) { $video = $video->fetch(PDO::FETCH_ASSOC); } else { $videoisempty = 1; }
    //if(!isset($videoisempty)) {
      if (($cnum - 1) % 3 == 0) {
        echo '<tr>';
    }
    
    ?>
    <td width="33%">
        <table>
            <tbody>
                <tr>
                    <td>
                        <a href="channels_portal.php?c=<?= htmlspecialchars($cnum) ?>"><img src="/get_still?video_id=<? if(!isset($videoisempty)) { echo htmlspecialchars($video['vid']); } else { echo ""; } ?>" width="80" height="60" style="border: 5px solid #FFFFFF;"></a>&nbsp;
                    </td>
                    <td valign="top">
                        <div style="font-size: 12px; font-weight: bold;"><a href="channels_portal.php?c=<?= htmlspecialchars($cnum) ?>"><?= htmlspecialchars($_VCHANE[$cnum]) ?></a></div>
                        <div style="font-size: 11px; font-family: Arial, Helvetica, sans-serif; color: #666666;">Total: 3485 | New: 172</div>
                        <div style="padding-top: 5px;"><?= htmlspecialchars($_VCHANE_DESC[$cnum]) ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
    <?php
      if (($cnum) % 3 == 0 || $cnum == count($_VCHANE)) {
        echo '</tr>';
      }
unset($videoisempty);
    }
//}
?>

									
								</tr>
								<tr valign="top">
									<td colspan="2">&nbsp;</td>
								</tr>
															</tbody></table>
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
<?php 
require "needed/end.php";
?>
