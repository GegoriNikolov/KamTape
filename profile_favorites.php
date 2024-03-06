<?php
require "needed/start.php";
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
$profile['friends'] = $profile['friends']->fetchColumn();
*/
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
}
    $vidocount = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ?
	ORDER BY favorites.fid DESC"
);
$vidocount->execute([$profile['uid']]);
$vidocount = $vidocount->rowCount();
$videos = $conn->prepare(
    "SELECT * FROM favorites
    INNER JOIN videos ON favorites.vid = videos.vid
    INNER JOIN users ON users.uid = videos.uid
    WHERE favorites.uid = ? AND videos.converted = 1 AND videos.privacy = 1 AND videos.rejected = 0
    ORDER BY favorites.fid DESC LIMIT $ppv OFFSET $offs"
);
$videos->execute([$profile['uid']]);
/*$favorites = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ?
	ORDER BY favorites.fid DESC LIMIT $ppv OFFSET $offs"
);
$favorites->execute([$profile['uid']]);*/
}
$related_tags = [];
?>

<table align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><a href="profile.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Profile</a></td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Public Videos</a> (<?php echo $profile['pub_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_videos_private.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Private Videos</a> (<?php echo $profile['priv_vids']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><strong>Favorites</strong> (<?php echo $profile['fav_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_friends.php?user=<?php echo htmlspecialchars($profile['username']) ?>">Friends</a> (<?php echo $profile['friends_count']; ?>)</td>
		<td style="padding: 0px 5px 0px 5px;">|</td>
		<td><a href="profile_play_list?user=<?php echo htmlspecialchars($profile['username']) ?>">Playlists</a> (0)</td>
	</tr>
</table><p>
<? //if($vidocount > 0) {?>
<div class="pageTable">

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
				<div class="moduleTitleBar">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
						<td><div class="moduleTitle">Favorites // <?php echo htmlspecialchars($profile['username']) ?></div></td>
						<td align="right">
						<? if($vidocount > 0) { ?><div style="font-weight: color: #444; margin-right: 5px;"><b>Favorites <?php if ($offs > 0) { echo htmlspecialchars(trim($offs)); } else { echo "1"; } ?>-<? if($vidocount > $ppv) { $nextynexty = $offs + $ppv; } else {$nextynexty = $vidocount; } echo htmlspecialchars($nextynexty); ?> of <?php echo $vidocount; ?></b></div><? } ?>
						</td>
					</tr>
				</tbody></table>
				</div>
				<?php foreach($videos as $video) { ?>
				<?php
				$related_tags = array_merge($related_tags, explode(" ", $video['tags']));
				
				/*$video['views'] = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE vid = ?");
				$video['views']->execute([$video['vid']]);
				$video['views'] = $video['views']->fetchColumn();
						
				$video['comments'] = $conn->prepare("SELECT COUNT(cid) FROM comments WHERE vidon = ?");
				$video['comments']->execute([$video['vid']]);
				$video['comments'] = $video['comments']->fetchColumn();*/
				?>
                <div class="moduleEntry"> 
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tbody><tr valign="top">
							<td>
							<table cellpadding="0" cellspacing="0" border="0">
								<tbody><tr>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<? echo htmlspecialchars($profile['username']); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=1" class="moduleEntryThumb" width="100" height="75"></a></td>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<? echo htmlspecialchars($profile['username']); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=2" class="moduleEntryThumb" width="100" height="75"></a></td>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<? echo htmlspecialchars($profile['username']); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=3" class="moduleEntryThumb" width="100" height="75"></a></td>
								</tr>
							</table>
							
							</td>
							<td width="100%"><div class="moduleEntryTitle" style="word-break: break-all;"><a href="watch.php?v=<?php echo $video['vid']; ?>"><?php echo htmlspecialchars($video['title']); ?></a></div>
							<div class="moduleEntryDescription"><?php
$description = htmlspecialchars($video['description']);
$description = (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
echo $description;
?></div>
							<div class="moduleEntryTags">Tags // <?php foreach(explode(" ", $video['tags']) as $tag) echo '<a href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> : '; ?></div>
							<div class="moduleEntryDetails">Added: <?php echo retroDate($video['uploaded']); ?> by <a href="profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo htmlspecialchars($video['username']); ?></a></div>
							<div class="moduleEntryDetails">Runtime: <?php echo gmdate("i:s", $video['time']); ?> | Views: <?php echo $video['views']; ?> | Comments: <?php echo $video['comm_count']; ?></div>
								
	<nobr>
							</nobr>
		
							</td>
						</tr>
					</tbody></table>
				</div>
                <?php } ?> <!-- begin paging -->
<?php if($vidocount > $ppv) { ?>
<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">My Videos Page:

    <?php
    $totalPages = ceil($vidocount / $ppv);
    if (empty($_GET['page'])) { $_GET['page'] = 1; }
    $pagesPerSet = 10; // Set the number of pages per group
    $startPage = floor(($page - 1) / $pagesPerSet) * $pagesPerSet + 1;
    $endPage = min($startPage + $pagesPerSet - 1, $totalPages); ?>
    <?php if ($startPage < $totalPages && $page !== 1) { ?>
    <a href="profile_favorites.php?user=<? echo htmlspecialchars($profile['username']); ?>&page=<?php echo $_GET['page'] - 1; ?>"> < Previous</a>
    <?php } ?>

    <?php 
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $page) {
            echo '<span style="color: #444; background-color: #FFFFFF; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;">' . $i . '</span>';
        } else {
            echo '<span style="background-color: #CCC; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><a href="profile_favorites.php?user=' . htmlspecialchars($profile['username']) . '&page=' . $i . '">' . $i . '</a></span>';
        }
    }
    ?>
    <!-- Add "Next" link if there are more pages -->
    <?php if ($endPage < $totalPages) { ?>
            <a href="profile_favorites.php?user=<? echo htmlspecialchars($profile['username']); ?>&page=<?php echo $_GET['page'] + 1; ?>">Next ></a>
    <?php } ?>
</div>
<?php } ?>
				<!-- end paging -->

								
								
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
        <!-- if u ever need a laugh just look at this -->
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Related Tags:</div>
		<?php $related_tags = array_unique($related_tags); ?>
		<?php foreach($related_tags as $tag) { ?>
		<div style="padding: 0px 0px 5px 0px; color: #999;">&#187; <a href="profile_favorites.php?user=<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></a></div>
		<?php } ?>
				
		</td>
		
	</tr>
</table>
</div>
<? //} if($vidocount == 0) { alert("This user has no videos at this time!"); } ?>
<?php
require "needed/end.php";
?>