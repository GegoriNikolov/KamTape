<?php
//if(1 == 1){
//die();
//}
require "needed/start.php";
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1);
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
	die('Profile was not found.');
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
/*
    $profile['videos'] = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND converted = 1");
    $profile['videos']->execute([$profile["uid"]]);
    $profile['videos'] = $profile['videos']->rowCount();

    $profile['priv_videos'] = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND privacy = 2 AND converted = 1");
    $profile['priv_videos']->execute([$profile["uid"]]);
    $profile['priv_videos'] = $profile['priv_videos']->rowCount();

$profile['favorites'] = $conn->prepare("SELECT fid FROM favorites WHERE uid = ?");
$profile['favorites']->execute([$profile["uid"]]);
$profile['favorites'] = $profile['favorites']->rowCount();

$profile['friends'] = $conn->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
$profile['friends']->execute([$profile["uid"],$profile["uid"]]);
$profile['friends'] = $profile['friends']->fetchColumn();*/
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = 10;
$offs = ($page - 1) * $ppv;

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
	
	$profile_latest_video['comments'] = $conn->prepare("SELECT COUNT(cid) AS comments FROM comments WHERE vid = ?");
	$profile_latest_video['comments']->execute([$profile_latest_video['vid']]);
	$profile_latest_video['comments'] = $profile_latest_video['comments']->fetchColumn();*/
    /*$vidocount = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY videos.uploaded DESC"
);
$vidocount->execute([$profile['uid']]);
$vidocount = $vidocount->rowCount();*/
}
/*
    $videos = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY videos.uploaded DESC LIMIT $ppv OFFSET $offs"
);
$videos->execute([$profile['uid']]);
$favorites = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ? AND videos.converted = 1
	ORDER BY favorites.fid"
);
$favorites->execute([$profile['uid']]);*/

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array, SORT_STRING | SORT_FLAG_CASE);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

//$friends = $conn->prepare("SELECT * FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
//$friends->execute([$profile['uid'], $profile['uid']]);
$friends = $conn->prepare("SELECT * FROM relationships LEFT JOIN users ON users.uid = relationships.respondent WHERE relationships.sender = ? AND relationships.accepted = 1");
$friends->execute([$profile['uid']]);
$friends = $friends->fetchAll(PDO::FETCH_ASSOC);

$friends2 = $conn->prepare("SELECT * FROM relationships LEFT JOIN users ON users.uid = relationships.sender WHERE relationships.respondent = ? AND relationships.accepted = 1");
$friends2->execute([$profile['uid']]);
$friends2 = $friends2->fetchAll(PDO::FETCH_ASSOC);
$friends = array_merge($friends, $friends2);

$friends = array_sort($friends, "username", SORT_ASC);

}

?>
<div style="padding-bottom: 15px;">
<table align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><a href="profile?user=<?php echo htmlspecialchars($profile['username']) ?>">Profile</a></td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Public Videos</a> (<?php echo $profile['pub_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos_private.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Private Videos</a> (<?php echo $profile['priv_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_favorites.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Favorites</a> (<?php echo $profile['fav_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><strong>Friends</strong> (<?php echo $profile['friends_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_play_list?user=<?php echo htmlspecialchars($profile['username']) ?>">Playlists</a> (0)</td>
	</tr>
</table>
</div>

<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td>
				
				<div class="watchTitleBar">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr valign="top">
							<td><div class="moduleTitle">Friends // <span style="text-transform: capitalize;"><?php echo htmlspecialchars($profile['username']) ?></span></div></td>
 							<td align="right">
								<div style="font-weight: bold; color: #444; margin-right: 5px;">
								</div>
							</td>
						</tr>
					</table>
				</div>
				
				<?php foreach($friends as $friend) {
				
				/*
				if($friend['sender'] == $profile['uid']){
				$frienduid = $friend['respondent'];
				} else {
				$frienduid = $friend['sender'];
				}
				*/
				
				$friend_latest_video = $conn->prepare(
				"SELECT * FROM videos
				LEFT JOIN users ON users.uid = videos.uid
				WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
				GROUP BY videos.vid
				ORDER BY videos.uploaded DESC LIMIT 1"
				);
				$friend_latest_video->execute([$friend['uid']]);
				
				if($friend_latest_video->rowCount() == 0) {
				$friend_latest_video = false;
				} else {
				$friend_latest_video = $friend_latest_video->fetch(PDO::FETCH_ASSOC);
				}
				if($friend_latest_video['privacy'] !== 1) {
				$friend_latest_video = false;
				}
				
/*
				$friend['vids'] = $conn->prepare("SELECT COUNT(vid) FROM videos WHERE uid = ? AND converted = 1");
				$friend['vids']->execute([$friend['uid']]);
				$friend['vids'] = $friend['vids']->fetchColumn();

				$friend['favs'] = $conn->prepare("SELECT COUNT(fid) FROM favorites WHERE uid = ?");
				$friend['favs']->execute([$friend['uid']]);
				$friend['favs'] = $friend['favs']->fetchColumn();

				$friend['friends'] = $conn->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
				$friend['friends']->execute([$friend['uid'], $friend['uid']]);
				$friend['friends'] = $friend['friends']->fetchColumn();*/
				?>
                <div class="moduleEntry"> 
				<table width="565" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
						<td align="center"><a href="">
						
<? if($friend_latest_video) { ?>
							<a href="watch.php?v=<?= htmlspecialchars($friend_latest_video['vid']) ?>"><img src="/get_still.php?video_id=<?= htmlspecialchars($friend_latest_video['vid']) ?>" class="moduleEntryThumb" width="120" height="90"></a>
							<div class="moduleFeaturedTitle"><a href="watch.php?v=<?= htmlspecialchars($friend_latest_video['vid']) ?>"><?= htmlspecialchars($friend_latest_video['title']) ?></a></div>
							
<? } ?>
						<td width="100%">
						<div class="moduleEntryTitle" style="margin-bottom: 5px;">

						<a href="/profile?user=<?= htmlspecialchars($friend['username']) ?>"><?= htmlspecialchars($friend['username']) ?></a>

						</div>
						<div class="moduleEntryDescription"><a href="profile_videos.php?user=<?= htmlspecialchars($friend['username']) ?>">Videos</a> (<?= $friend['pub_vids'] ?>) | <a href="profile_favorites.php?user=<?= htmlspecialchars($friend['username']) ?>">Favorites</a> (<?= $friend['fav_count'] ?>) | <a href="profile_friends.php?user=<?= htmlspecialchars($friend['username']) ?>">Friends</a> (<?= $friend['friends_count'] ?>)</div>
						<div class="moduleEntryDetails"></div>
						<div class="moduleEntryDetails"></div>
						</td>
					</tr>
				</table>
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
		</table>
		
		</td>
		<td width="180">
		</td>
	</tr>
</table>

		</div>
		</td>
	</tr>
</table>
<?php
require "needed/end.php";
?>