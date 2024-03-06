<?php 
if(isset($_GET['v'])) {
	header("Location: watch.php?v=".$_GET['v'], true, 303);
	die();
}
require "needed/start.php";

$tags_strings = $conn->query("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE converted = 1 AND privacy = 1 AND users.termination = 0 ORDER BY uploaded DESC LIMIT 50");
$tag_list = [];
foreach($tags_strings as $result) $tag_list = array_merge($tag_list, explode(" ", $result['tags']));
$tag_list = array_slice(array_count_values($tag_list), 0, 32);
$featured = $conn->query("SELECT * FROM picks LEFT JOIN videos ON videos.vid = picks.video LEFT JOIN users ON users.uid = videos.uid WHERE videos.converted = 1 AND videos.privacy = 1 ORDER BY picks.featured DESC LIMIT 10");
if($_GET['50'] == 50) {
$rec_viewed = $conn->query("SELECT * FROM views LEFT JOIN videos ON videos.vid = views.vid LEFT JOIN users ON users.uid = videos.uid AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 ORDER BY views.viewed DESC LIMIT 50");
} else {
$rec_viewed = $conn->query("SELECT * FROM views LEFT JOIN videos ON videos.vid = views.vid LEFT JOIN users ON users.uid = videos.uid AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 ORDER BY views.viewed DESC LIMIT 12");    
}
if ($_SESSION['uid'] != NULL) {
// ahhh!!!! logged in section
//subscriptions
$subscriptions = $conn->prepare("SELECT * FROM subscriptions WHERE subscriber = ?");
$subscriptions->execute([$session['uid']]);

$sub_check = [];

foreach ($subscriptions as $subscription) {
    if ($subscription['subscribed_type'] == 'user_uploads') {
        $subscribed_for = $conn->prepare("SELECT * FROM videos WHERE uid = ? AND converted = 1");
        $subscribed_for->execute([$subscription["subscribed_to"]]);
        $subscribed_results = $subscribed_for->fetchAll(PDO::FETCH_ASSOC);
        $sub_check = array_merge($sub_check, $subscribed_results);
        global $sub_check;
    }
}

usort($sub_check, function($a, $b) {
    return strtotime($b['uploaded']) - strtotime($a['uploaded']);
});

$sub_check = array_slice($sub_check, 0, 34);


//$y_views = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE uid = ?");
$y_views = $conn->prepare("SELECT vids_watched FROM users WHERE uid = ?");
$y_views->execute([$session['uid']]);
$y_views = $y_views->fetchColumn();
// PHP UP YOURS
/*$vids = $conn->prepare(
	"SELECT pub_vids FROM users
	WHERE uid = ?"
);
$vids->execute([$session['uid']]);*/

$fans = $conn->prepare(
	"SELECT SUM(fav_count) FROM videos
	WHERE videos.uid = ? AND videos.converted = 1"
);
$fans->execute([$session['uid']]);

$p_views = $conn->prepare(
	"SELECT SUM(views) FROM videos
	WHERE videos.uid = ? AND videos.converted = 1"
);
$p_views->execute([$session['uid']]);


/*$favs = $conn->prepare(
	"SELECT fav_count FROM users
	WHERE uid = ?"
);
$favs->execute([$session['uid']]);*/
// endingggg
}
?>

<script type="text/javascript" src="/js/video_bar.js"></script>
<?php if (empty($sub_check)) { ?>
<script language="JavaScript">
    		onLoadFunctionList.push(function() { imagesInit_recently_viewed();} );
	
		function imagesInit_recently_viewed() {
			imageBrowsers['recently_viewed'] = new ImageBrowser(4, 1, "recently_viewed");
			<?php foreach($rec_viewed as $vids_viewed) { ?>	
				
				imageBrowsers['recently_viewed'].addImage(new ktImage("/get_still.php?video_id=<?php echo htmlspecialchars($vids_viewed['vid']); ?>", 
													  "/watch.php?v=<?php echo htmlspecialchars($vids_viewed['vid']); ?>",
													  "<?php echo htmlspecialchars($vids_viewed['title']); ?>", 
													  "/watch.php?v=<?php echo htmlspecialchars($vids_viewed['vid']); ?>",
													  "<? echo timeAgo($vids_viewed['viewed']); ?>",
													  "",
													  false) );
				
				 <? } ?>
			imageBrowsers['recently_viewed'].initDisplay();
			imageBrowsers['recently_viewed'].showImages();
			images_loaded = true;
		}



</script>
<? } ?>

<?php if (!empty($sub_check)) { ?>
<script language="JavaScript">
    		onLoadFunctionList.push(function() { imagesInit_subscriptions();} );
	
		function imagesInit_subscriptions() {
			imageBrowsers['subscriptions'] = new ImageBrowser(4, 1, "subscriptions");

			<?php foreach($sub_check as $subscribed_for_this) { ?>	
				
				imageBrowsers['subscriptions'].addImage(new ktImage("/get_still.php?video_id=<?php echo htmlspecialchars($subscribed_for_this['vid']); ?>", 
													  "/watch.php?v=<?php echo htmlspecialchars($subscribed_for_this['vid']); ?>",
													  "placeholder", 
													  "/watch.php?v=<?php echo htmlspecialchars($subscribed_for_this['vid']); ?>",
													  "placeholder",
													  "",
													  false) );
				
				 <? } ?>
			imageBrowsers['subscriptions'].initDisplay();
			imageBrowsers['subscriptions'].showImages();
			images_loaded = true;
		}



</script>
<? } ?>


<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
					<table class="roundedTable" width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#EFEFEF">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="585">
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="browse.php">Watch</a></div>
					Instantly find and watch 1000's of fast streaming videos.
					</td>
					<td width="33%" style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="my_videos_upload.php">Upload</a></div>
					Quickly upload and tag videos in almost any video format.
					</td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;"><a href="my_friends_invite.php">Share</a></div>
					Easily share your videos with family, friends, or co-workers.
					</td>
					</tr>
				</table>

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td valign="bottom"><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td valign="bottom"><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>


			
						<table class="roundedTable" width="585" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="575">
								
		
		<?php if(!$_SESSION['uid']) { ?>
       <div style="padding-left: 10px; padding-right: 10px;">
	<table width="571" height="28" cellpadding="0" cellspacing="0" border="0" background="/img/MediumGenericTab.jpg">
		<tr>
			<td width="380px">
				<span style="padding-left: 5px; font-size: 13px; color: #6D6D6D; font-weight: bold; padding-right: 5px;">Recently Viewed</span>
				<span style="font-size: 10px; color: #999999;"><span id="counter_recently_viewed"></span>
			</td>
			<td align="left">		
					<span style="font-size: 13px; color: #6D6D6D;"><span></span>
			</td>	
			<td align="right">	
				<span style="padding-right: 10px; padding-left: 10px;"><img src="/img/icon_todo.gif" border="0" width="23" height="14" style="padding-right: 5px; vertical-align: middle;">
						<a href="/browse.php?s=mp">More Recently Viewed..</a>
				</span>
			</td>
		</tr>
	</table>
	</div>

	
		<div style="padding-left: 1px;">					
		<table width="21" height="121" cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="/img/LeftTableArrowWhite.jpg" onclick="shiftLeft('recently_viewed')" width=21 height=121 border=0></td>
				<td>
					<table width="548" height="121" style="background-color: #FFFFFF; border-bottom: 1px solid #CCCCCC;" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<div class="videobarthumbnail_block" id="div_recently_viewed_0">
								<center>
									<div><a id="href_recently_viewed_0" href=".."><img class="videobarthumbnail_gray" id="img_recently_viewed_0" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_recently_viewed_0" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_recently_viewed_0" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_0_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_1">
								<center>
									<div><a id="href_recently_viewed_1" href=".."><img class="videobarthumbnail_gray" id="img_recently_viewed_1" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_recently_viewed_1" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_recently_viewed_1" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_1_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_2">
								<center>
									<div><a id="href_recently_viewed_2" href=".."><img class="videobarthumbnail_gray" id="img_recently_viewed_2" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_recently_viewed_2" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_recently_viewed_2" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_2_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_3">
								<center>
									<div><a id="href_recently_viewed_3" href=".."><img class="videobarthumbnail_gray" id="img_recently_viewed_3" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_recently_viewed_3" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_recently_viewed_3" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_recently_viewed_3_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							</td>
						</tr>
					</table>
				</td>
				<td><img src="/img/RightTableArrowWhite.jpg" onclick="shiftRight('recently_viewed')" width=21 height=121 border=0></td>
			</tr>
		</table>
		</div>

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td valign="bottom"><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td valign="bottom"><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
			<? } ?>
		<?php if (!empty($sub_check)) { ?>
		       <div style="padding-left: 10px; padding-right: 10px;">
	<table width="571" height="28" cellpadding="0" cellspacing="0" border="0" background="/img/MediumGenericTab.jpg">
		<tr>
			<td width="380px">
				<span style="padding-left: 5px; font-size: 13px; color: #6D6D6D; font-weight: bold; padding-right: 5px;">My Subscriptions</span>
				<span style="font-size: 10px; color: #999999;"><span id="counter_subscriptions"></span>
			</td>
			<td align="left">		
					<span style="font-size: 13px; color: #6D6D6D;"><span></span>
			</td>	
			<td align="right">	
				<span style="padding-right: 10px; padding-left: 10px;"><img src="/img/icon_todo.gif" border="0" width="23" height="14" style="padding-right: 5px; vertical-align: middle;">
						<a href="/subscription_center.php">View More Subscriptions..</a>
				</span>
			</td>
		</tr>
	</table>
	</div>

	
		<div style="padding-left: 1px;">					
		<table width="21" height="121" cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="/img/LeftTableArrowWhite.jpg" onclick="shiftLeft('subscriptions')" width=21 height=121 border=0></td>
				<td>
					<table width="548" height="121" style="background-color: #FFFFFF; border-bottom: 1px solid #CCCCCC;" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							<div class="videobarthumbnail_block" id="div_subscriptions_0">
								<center>
									<div><a id="href_subscriptions_0" href=".."><img class="videobarthumbnail_gray" id="img_subscriptions_0" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_subscriptions_0" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_subscriptions_0" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_0_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_1">
								<center>
									<div><a id="href_subscriptions_1" href=".."><img class="videobarthumbnail_gray" id="img_subscriptions_1" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_subscriptions_1" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_subscriptions_1" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_2">
								<center>
									<div><a id="href_subscriptions_2" href=".."><img class="videobarthumbnail_gray" id="img_subscriptions_2" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_subscriptions_2" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_subscriptions_2" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_2_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_3">
								<center>
									<div><a id="href_subscriptions_3" href=".."><img class="videobarthumbnail_gray" id="img_subscriptions_3" src="/img/pixel.gif" width="80" height="60"></a></div>
									<div id = "title1_subscriptions_3" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">loading...</div>
									<div id = "title2_subscriptions_3" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							<div class="videobarthumbnail_block" id="div_subscriptions_3_alternate" style="display: none">
								<center>
									<div><img src="/img/pixel.gif" width="80" height="60"></div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
									<div style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-color: #666666; padding-bottom: 3px;">&nbsp;</div>
								</center>
							</div>
							</td>
						</tr>
					</table>
				</td>
				<td><img src="/img/RightTableArrowWhite.jpg" onclick="shiftRight('subscriptions')" width=21 height=121 border=0></td>
			</tr>
		</table>
		</div>

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td valign="bottom"><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td valign="bottom"><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		<? } ?>	
		
		
		<table width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="585">
				<div class="moduleTitleBar">
				<div class="moduleTitle"><div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;"><a href="browse.php">See More Videos</a></div>
				Today's Featured Videos...
				</div>
				</div>
		
				
                     <?php foreach($featured as $pick) { 
                    ?>
                        <div class="moduleEntry<? if($pick['special'] == 1) {?>Selected<? } ?>">
					<table width="565" cellpadding="0" cellspacing="0" border="0">
						<tbody><tr valign="top">
							<td><a href="index.php?v=<?php echo htmlspecialchars($pick['vid']); ?>"><img src="get_still.php?video_id=<?php echo htmlspecialchars($pick['vid']); ?>" class="moduleEntryThumb" width="120" height="90"></a></td>
							<td width="100%"><div class="moduleEntryTitle"><a href="index.php?v=<?php echo htmlspecialchars($pick['vid']); ?>"><?php echo htmlspecialchars($pick['title']); ?></a></div>
							<div class="moduleEntryDescription"><?php echo htmlspecialchars($pick['description']); ?></div>
							<div class="moduleEntryTags">
							Tags // <?php
						foreach(explode(" ", $pick['tags']) as $tag) {
							echo ' <a href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> :';
						}
						?> 							</div>
						<div class="moduleEntryTags">Channels // <? showChannels($pick['vid'], ' :'); ?></div>
							<div class="moduleEntryDetails">Added: <?php echo timeAgo($pick['uploaded']); ?> by <a href="profile.php?user=<?php echo htmlspecialchars($pick['username']); ?>"><?php echo htmlspecialchars($pick['username']); ?></a></div>
							<div class="moduleEntryDetails">Runtime: <?php echo gmdate("i:s", $pick['time']); ?> | Views: <?php echo number_format($pick['views']); ?> | Comments: <?php echo number_format($pick['comm_count']); ?></div>
                            <nobr>
			<? grabRatingsPage($pick['vid'], "SM"); ?>
	</nobr>
							</td>
						</tr>
					</tbody></table>
					</div>
<?php } ?>
					
									
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table>

		
		</td>
		<td width="180">
		
		<table width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFEEBB">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="170">
		
				<?php                 
                if($_SESSION['uid'] == NULL) { ?>				
				<div style="font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;"><a href="signup.php">Sign up for your free account!</a></div>
                <? } else if($_SESSION['uid'] != NULL && $_GET['confidence'] !== 'low') { ?>
                <div style="font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;"><a href="my_friends_invite.php">Invite your friends to join KamTape!</a></div>
               <? } else if($_SESSION['uid'] != NULL && $_GET['confidence'] == 'low') { ?>
               <div style="font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;"><a href="my_videos.php">You have no friends, so I don't know who you'd invite.</a></div>
               <? } ?>
				
								
				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		<p>
		<table class="roundedTable" width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#cccccc">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="170">
							<div style="font-size: 16px; font-weight: bold; text-align: center; padding: 5px 5px 10px 5px;">
		<div style="font-size: 13px; font-weight: bold; text-align: center; color: #444444; padding-bottom: 5px;"><img src="/img/gray_arrow.gif" width="14" height="14" broder="0" align="absmiddle">&nbsp;Explore New Features</div>
		<table style="background-color: #EAEAEA;" width="100%">
			<tr>
				<td style="text-align: center;">
				<div style="padding-top: 5px;"><a href="/channels.php">Channels</a></div>
				<div style="font-size: 11px; font-weight: normal; padding-bottom: 8px;">Find the videos you want.</div>
				<div style="padding-top: 5px;"><a href="<?php if($_SESSION['uid'] == NULL) { echo "http://www.kamtape.com/signup_login.php?next=subscription_center.php"; } else { echo "http://www.kamtape.com/subscription_center.php"; } ?>">Subscriptions</a></div>
				<div style="font-size: 11px; font-weight: normal; padding-bottom: 12px;">Subscribe to videos from your favorite users</div>		
				</td>
			</tr>
		</table></div>
		</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		
		<div style="margin: 10px 0px 5px 0px; font-size: 12px; font-weight: bold; color: #333;">Recent Tags:</div>
		<div style="font-size: 13px; color: #333333;">
		
				
			<?php foreach($tag_list as $tag => $frequency	) {
        $freqindex = $frequency*2;
        $freqindex = $freqindex+10;
        if ($freqindex > 28) {
            $freqindex = 28;
        }
					echo '<a style="font-size: '.htmlspecialchars($freqindex).'px;" href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> :'."\r\n";
				} ?>
		
					
		<div style="font-size: 14px; font-weight: bold; margin-top: 10px;"><a href="tags.php">See More Tags</a></div>
		
		</div>
        <br>
        <? if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') { ?>

     
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2537513323123758"
     crossorigin="anonymous"></script>
<!-- Sidebar -->
<ins class="adsbygoogle"
     style="display:inline-block;width:200px;height:200px"
     data-ad-client="ca-pub-2537513323123758"
     data-ad-slot="6500476197"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script><? } ?>
<div style="margin-top: 10px;">
		
		</div>
		<? whos_online(8); ?>
		
		</td>
	</tr>
</tbody></table>

		</div>
<?php 
require "needed/end.php";
?>
