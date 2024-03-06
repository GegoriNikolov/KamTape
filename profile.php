<?php
require "needed/start.php";
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
    if(empty($_GET['user'])) {
	redirect("index_down.php");
    } else {
    session_error_index("Invalid username", "error");
    }
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
/*
    $profile['videos'] = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND privacy = 1 AND converted = 1");
    $profile['videos']->execute([$profile["uid"]]);
    $profile['videos'] = $profile['videos']->rowCount();

    $profile['priv_videos'] = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND privacy = 2 AND converted = 1");
    $profile['priv_videos']->execute([$profile["uid"]]);
    $profile['priv_videos'] = $profile['priv_videos']->rowCount();
*/
    
    $view_profile = $conn->prepare("UPDATE users SET profile_views = profile_views + 1 WHERE uid = ?");
	$view_profile->execute([$profile['uid']]);

/*
    $profile['favorites'] = $conn->prepare("SELECT fid FROM favorites WHERE uid = ?");
    $profile['favorites']->execute([$profile["uid"]]);
    $profile['favorites'] = $profile['favorites']->rowCount();

    $profile['watched'] = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE uid = ?");
    $profile['watched']->execute([$profile['uid']]);
    $profile['watched'] = $profile['watched']->fetchColumn();

    $profile['friends'] = $conn->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
    $profile['friends']->execute([$profile["uid"],$profile["uid"]]);
    $profile['friends'] = $profile['friends']->fetchColumn();
*/

    if($session['uid'] != NULL) {
    $subscribed = $conn->prepare("SELECT subscription_id FROM subscriptions WHERE subscriber = ? AND subscribed_to = ? AND subscribed_type = 'user_uploads'");
    $subscribed->execute([
	$session['uid'],
	$profile['uid']
    ]);
    }
    
    $alreadyrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :member_id AND respondent = :him AND accepted = 1");
    $alreadyrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $profile['uid']
    ]);

    if($alreadyrelated === 1) {
	$friendswith = 1;
    }

    $newrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :him AND respondent = :member_id AND accepted = 1");
    $newrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $profile['uid']
    ]);

    if($newrelated === 1) {
	$friendswith = 1;
    }

$profile_latest_video = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1
	GROUP BY videos.vid
	ORDER BY videos.uploaded DESC LIMIT 1"
);
$profile_latest_video->execute([$profile['uid']]);

if($profile_latest_video->rowCount() == 0) {
	$profile_latest_video = false;
} else {
	$profile_latest_video = $profile_latest_video->fetch(PDO::FETCH_ASSOC);
	
	/*$profile_latest_video['views'] = $conn->prepare("SELECT COUNT(view_id) AS views FROM views WHERE vid = ?");
	$profile_latest_video['views']->execute([$profile_latest_video['vid']]);
	$profile_latest_video['views'] = $profile_latest_video['views']->fetchColumn();
	
	$profile_latest_video['comments'] = $conn->prepare("SELECT COUNT(cid) AS comments FROM comments WHERE vidon = ?");
	$profile_latest_video['comments']->execute([$profile_latest_video['vid']]);
	$profile_latest_video['comments'] = $profile_latest_video['comments']->fetchColumn();*/
    $videos = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1
	ORDER BY videos.uploaded DESC"
);
if($profile_latest_video['privacy'] !== 1) {
    $profile_latest_video = false;
}
$videos->execute([$profile['uid']]);
$favorites = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ? AND videos.converted = 1
	ORDER BY favorites.fid DESC"
);
$favorites->execute([$profile['uid']]);

$friends = $conn->prepare(
	"SELECT * FROM relationships LEFT JOIN users ON users.uid = relationships.sender WHERE (respondent = ? OR sender = ?) AND accepted = 1 ORDER BY sent DESC"
);
$friends->execute([$profile['uid'], $profile['uid']]);
}

}
if($profile['closure'] == 1) { $term_text = 'This user account is closed.'; } else { $term_text = 'This user account is suspended.'; } if($profile['termination'] == 1) { session_error_index($term_text, "error"); } else { ?>
<div style="padding-bottom: 15px;">
<table align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><strong>Profile</strong></td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Public Videos</a> (<?php echo $profile['pub_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos_private.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Private Videos</a> (<?php echo $profile['priv_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_favorites.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Favorites</a> (<?php echo $profile['fav_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_friends.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Friends</a> (<?php echo $profile['friends_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_play_list?user=<?php echo htmlspecialchars($profile['username']) ?>">Playlists</a> (0)</td>
	</tr>
</table>
</div>

<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		
		<td width="595" style="padding-right: 15px;">
		<div style="border: 1px solid #CCCCCC; padding: 15px 15px 30px 15px;">
		<div style="font-size: 18px; font-weight: bold; color: #CC6633; margin-bottom: 2px;">Hello. I'm <?php echo htmlspecialchars($profile['username']) ?>.</div>
				
		<div style="font-size: 11px; font-weight: bold; padding-left: 15px; color: #999999;">
				I have <a href="<?php if($_SESSION['uid'] == NULL) { echo "signup_login.php?next=subscription_center"; } else { echo "subscription_center"; } ?>"><?php echo number_format($profile['subscribers']) ?> subscribers</a>!<br>
			I have watched <?php echo number_format($profile['vids_watched']) ?> videos!&nbsp; 
			<br>
			My profile has been viewed <?php echo number_format($profile['profile_views']) ?> times!<br>
			</div>

			<!-- Personal Information: -->
			
			<div class="profileLabel">Last Login:</div>
			<? echo timeAgo($profile['lastlogin']) ?>			
			<div class="profileLabel">Signed up:</div>
			<? echo timeAgo($profile['joined']) ?>
						
				<? if (!empty($profile['name'])) { ?><div class="profileLabel">Name:</div>
				<?php echo htmlspecialchars($profile['name']) ?>	
                 <? } ?>		
					
			
				<? if ($profile['birthday'] != '0000-00-00' && $profile['birthday'] != NULL) { ?><div class="profileLabel">Age:</div>
				<?php echo str_replace('ago', 'old', timeAgo($profile['birthday'])); ?><? } ?>			
					
						
				<? if(!empty($profile['gender']) && $profile['gender'] !== 0) { ?><div class="profileLabel">Gender:</div>
				<?php
					switch($profile['gender']) {
						case '0':
							break;
						case '1':
							echo "Male";
							break;
						case '2':
							echo "Female";
							break;
                        case '3':
						echo "Other";
						break;
                        default:
                        echo "Prefer not to say";
                        break;
					}
				?><? } ?>			
					
						
				<?php if(!empty($profile['relationship']) && $profile['relationship'] !== 0) { ?><div class="profileLabel">Relationship Status:</div>
				        <?php
					    switch($profile['relationship']) {
						case '0':
							break;
						case '1':
							echo "Single";
							break;
						case '2':
							echo "Taken";
							break; 
                            default:
                        echo "Prefer not to say";
                         break; }?><? } ?>			
					
					
						
				<? if (!empty($profile['about'])) { ?><div class="profileLabel">About Me:</div>
				<?php echo nl2br(htmlspecialchars($profile['about'])); ?><? } ?>			
					
						
				<? if (!empty($profile['website'])) { ?><div class="profileLabel">Personal Website:</div>
				<a href="<?php echo htmlspecialchars($profile['website']) ?>" target="_blank"><?php echo htmlspecialchars($profile['website']) ?></a><? } ?>
			
						
			<!-- Location Information -->
		
				<? if (!empty($profile['hometown'])) { ?>		
				<div class="profileLabel">Hometown:</div>
				<?php echo htmlspecialchars($profile['hometown']) ?><? } ?>				
					
						
				<? if (!empty($profile['city'])) { ?><div class="profileLabel">Current City:</div>
				<?php echo htmlspecialchars($profile['city']) ?><? } ?>			
					
						
				<? if (!empty($profile['country'])) { ?><div class="profileLabel">Current Country:</div>
				<? echo htmlspecialchars(getCountryName($profile['country'])) ?><? } ?>			
					
						
				<? if (!empty($profile['occupations'])) { ?><div class="profileLabel">Occupations:</div>
				<?php echo htmlspecialchars($profile['occupations']) ?><? } ?>			
					
						
				<? if (!empty($profile['companies'])) { ?><div class="profileLabel">Companies:</div>
				<?php echo htmlspecialchars($profile['companies']) ?><? } ?>			
					
						
				<? if (!empty($profile['schools'])) { ?><div class="profileLabel">Schools:</div>
				<?php echo htmlspecialchars($profile['schools']) ?><? } ?>			
					
						
				<? if (!empty($profile['hobbies'])) { ?><div class="profileLabel">Interests &amp; Hobbies:</div>
				<?php echo htmlspecialchars($profile['hobbies']) ?><? } ?>			
					
						
				<? if (!empty($profile['fav_media'])) { ?><div class="profileLabel">Favorite Movies &amp; Shows:</div>
				<?php echo htmlspecialchars($profile['fav_media']) ?><? } ?>			
					
						
				<? if (!empty($profile['music'])) { ?><div class="profileLabel">Favorite Music:</div>
				<?php echo htmlspecialchars($profile['music']) ?><? } ?>				
					
						
				<? if (!empty($profile['books'])) { ?><div class="profileLabel">Favorite Books:</div>
				<?php echo htmlspecialchars($profile['books']) ?><? } ?>			
					
		</div>
		</td>
		<? $blitz = false; if($session['username'] == $profile['username'] && empty($profile_latest_video)) {$blitz = true;} ?>
		<td width="180">
		<? if ($blitz == false) { ?>
		<table width="180" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#E5ECF9">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="170"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td align="center" style="padding: 5px;">
				
				<div style="padding: 5px; text-align: center;">
					
				<? if($profile_latest_video) { ?>				
				<div style="font-size: 14px; font-weight: bold; color: #003366;">Latest Video Added</div>
			
				<div style="padding-bottom: 10px;">
				<a href="watch.php?v=<?php echo htmlspecialchars($profile_latest_video['vid']) ?>"><img src="get_still.php?video_id=<?php echo htmlspecialchars($profile_latest_video['vid']) ?>" class="moduleFeaturedThumb" width="120" height="90"></a>
				<div class="moduleFeaturedTitle"><a href="watch.php?v=<?php echo htmlspecialchars($profile_latest_video['vid']) ?>"><?php echo shorten(htmlspecialchars($profile_latest_video['title']), 15); ?></a></div>
				<div class="moduleFeaturedDetails">Added: <?php echo retroDate($profile_latest_video['uploaded'], "F j, Y, h:i a") ?></div>
				
				</div>
                <? } ?>
				<?php                 
                if($_SESSION['uid'] != $profile['uid']) { ?>				
				<?php                 
                if($_SESSION['uid'] == NULL) { ?>
				<div style="padding-bottom: 10px;"><a href="signup.php">Sign up</a> or <a href="login.php">log in</a> to add <?php echo htmlspecialchars($profile['username']) ?> as a friend.</div>
                <? } elseif ($friendswith != 1){ ?>
                <form method="post" action="my_friends_invite_user.php?friend_id=<?php echo htmlspecialchars($profile['username']) ?>">
				<input type="submit" value="Add to Friends"></form>
                <? } elseif ($friendswith == 1){ ?>
                <form method="post" action="my_friends_remove_user.php?friend_id=<?php echo htmlspecialchars($profile['username']) ?>">
				<input type="submit" value="Remove from Friends"></form>
                <? } ?>
				<? if ($session['staff'] == 1 && $profile['staff'] != 1) {?>
                <form method="post" action="/admin/manage_account.php?user=<?php echo htmlspecialchars($profile['username']) ?>">
				<input type="submit" value="Moderate <?php echo htmlspecialchars($profile['username']) ?>"></form>
                <? } ?>
								
				<form method="post" action="outbox.php?user=<?php echo htmlspecialchars($profile['username']) ?>">
				<input type="submit" value="Send Message"></form>
				<form method="post" action="/subscription_center?<? if(isset($session['uid']) && $subscribed->rowCount() > 0) { echo "remove_user"; } else { echo "add_user"; } ?>=<?php echo htmlspecialchars($profile['username']) ?>">
						<input type="submit" value="<? if(isset($session['uid']) && $subscribed->rowCount() > 0) { echo "Unsubscribe from"; } else { echo "Subscribe to"; } ?> <?php echo htmlspecialchars($profile['username']) ?>'s Videos">
						</form>
				 <? } ?>
				</div>
				
				</td>
				</form>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
			</table>
            
			<? } ?>
			<? whos_online(); ?>
		</tr>


		</table>
	
		</td>
	</tr>
</table>

		</div>
		</td>
	</tr>
</table>
<? } ?>
<?php
require "needed/end.php";
