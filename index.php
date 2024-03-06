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

$rec_viewed = $conn->query("SELECT * FROM views LEFT JOIN videos ON videos.vid = views.vid LEFT JOIN users ON users.uid = videos.uid AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 ORDER BY views.viewed DESC LIMIT 4");
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

$sub_check = array_slice($sub_check, 0, 5);


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


<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tbody><tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
                <?php                 
                if($_SESSION['uid'] == NULL) { ?>
				<td style="padding: 5px 0px 5px 0px;">
				
								
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
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
				</tbody></table>

									
				</td>

									
				</td>
                <? } else { ?>
                <td style="padding: 5px 0px 5px 0px;">
				
								
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
					
					<td style="border-right: 1px dashed #369; padding: 0px 10px 10px 10px; color: #444;" width="33%">
					<span class="title">My Account Overview</span>
<table width="100%" cellpadding="1" cellspacing="0" border="0">
	
	
	<tbody><tr>
		
		<td align="left">
<strong>User Name</strong>: <a href="profile.php?user=<?php echo htmlspecialchars($session['username']) ?>"><?php echo htmlspecialchars($session['username']) ?></a><br><strong>Email</strong>: <?php echo htmlspecialchars($session['email']) ?><br>
<? if ($profile['watched'] !== 0) { ?><strong>Videos watched</strong>: <?php echo htmlspecialchars($y_views); ?><? } else { ?><strong>You should view something.</strong><? } ?><p><table cellspacing="3"><td style="width: 113px;height: 44px;background: #FFF;padding: 6px 12px;text-align: center;"><span style="font-size:14px"><a href="/my_videos.php">Videos: <?php echo $session['pub_vids']; ?></a></span><br><span style="font-size:10px">Views: <?php echo $p_views->fetchColumn(); ?><br>* Fans: <?php echo $fans->fetchColumn(); ?></span></td>
<td style="width: 113px;height: 44px;background: #FFF;padding: 6px 12px;text-align: center;"><span style="font-size:14px"><a href="/my_favorites.php">Favorites: <?php echo $session['fav_count']; ?></a></span><br><span style="font-size:10px"><br><br></span></td>
<td style="width: 113px;height: 44px;background: #FFF;padding: 6px 12px;text-align: center;"><span style="font-size:14px"><a href="/my_friends.php">Friends: <?php echo $session['friends_count']; ?></a></span><br><span style="font-size:10px"><a href="my_friends_videos.php">Their Vids</a> (0)<br><a href="my_friends_favorites.php">Their Favs</a> (0)</span></td>
	</table>
    <br><span style="font-size:10px">* Number of users that added your videos as a favorite.</span>
	
	
	
</tbody></table></td>
					<td width="33%" style="padding: 0px 10px 10px 10px; color: #444;">
					<img src="../img/mail<? if($inbox > 0) { echo '_unread'; } ?>.gif" id="mailico" border="0"> You have <a href="/my_messages.php"><? if($inbox == 0) { $new_m = 'no'; } else { $new_m = $inbox; } echo htmlspecialchars($new_m) ?> new messages.</a><p><b>ToDo List...</b><br><img src="/img/icon_todo.gif" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"><a href="my_friends_invite.php">Invite Your Friends</a><br><img src="/img/icon_todo.gif" style="vertical-align: text-bottom; padding-left: 2px; padding-right: 1px;"><a href="my_profile.php">Update Your Profile</a>
					</td>
					</tr>
				</tbody></table>

									
				</td>
                <?php } ?>

				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table><br>
		<?php if (empty($sub_check)) { ?>
        <!-- begin recently viewed -->
					<table class="roundedTable" width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#eeeedd">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="585">
					<div class="sunkenTitleBar">
						<div class="sunkenTitle">
							<div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;"><a href="/browse.php?s=mp">More Recently Viewed</a></div>
							<span style="color:#666633;">Recently Viewed...</span>
						</div>
					</div>
									<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
						<?php foreach($rec_viewed as $vids_viewed) { ?>	
						<td width="20%" align="center">
		
						<a href="index.php?v=<?php echo htmlspecialchars($vids_viewed['vid']); ?>"  title="<?php echo htmlspecialchars($vids_viewed['title']); ?>"><img src="get_still.php?video_id=<?php echo htmlspecialchars($vids_viewed['vid']); ?>" width="80" height="60" style="border: 5px solid #FFFFFF; margin-top: 10px;"></a>
						<div class="moduleFeaturedDetails" style="padding-top: 2px;"><? echo timeAgo($vids_viewed['viewed']); ?></div>
		
						</td>
                        <? } ?>
				</tr>
				</table>

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>

			<!-- end recently viewed -->
			<? } ?>
		<?php if (!empty($sub_check)) { ?>
		<!-- begin subscriptions -->
					<table class="roundedTable" width="595" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#eeeedd">
			<tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="585">
					<div class="sunkenTitleBar">
						<div class="sunkenTitle">
							<div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;"><a href="/subscription_center.php">Subscriptions Center</a></div>
							<span style="color:#666633;">From Subscriptions</span>
						</div>
					</div>
									<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
				
						<?php foreach($sub_check as $subscribed_for_this) { 
						?>	
						<td width="20%" align="center">
		
						<a href="index.php?v=<?php echo htmlspecialchars($subscribed_for_this['vid']); ?>"><img src="get_still.php?video_id=<?php echo htmlspecialchars($subscribed_for_this['vid']); ?>" width="80" height="60" style="border: 5px solid #FFFFFF; margin-top: 10px;"></a>
						<div class="moduleFeaturedDetails" style="padding-top: 2px;"><? echo timeAgo($subscribed_for_this['uploaded']); ?></div>
		
						</td>
                        <? } ?>
				</table>

				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>

			<!-- end subscriptions -->
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
							<td><a href="index.php?v=<?php echo htmlspecialchars($pick['vid']); ?>"><img src="<?= getStillUrl($pick['vid']); ?>" class="moduleEntryThumb" width="120" height="90"></a></td>
							<td width="100%"><div class="moduleEntryTitle"><a href="index.php?v=<?php echo htmlspecialchars($pick['vid']); ?>"><?php echo htmlspecialchars($pick['title']); ?></a></div>
							<div class="moduleEntryDescription"><?php echo htmlspecialchars($pick['description']); ?></div>
							<div class="moduleEntryTags">
							Tags // <?php
						foreach(explode(" ", $pick['tags']) as $tag) {
							echo ' <a href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> :';
						}
						?> 							</div>
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
