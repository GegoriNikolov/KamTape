<?php
require "needed/start.php";
// It's just old code made compatible with kamtape because i was tired and lazy :P
// If it doesn't work too well development wise I'll rewrite it later
if(isset($_GET['s']) && in_array($_GET['s'], ["mr", "mp", "md", "mf", "r", "rf", "tr"])) {
	$browse_sort = $_GET['s'];
} else {
	$browse_sort = "mr";
}

if(isset($_GET['t']) && in_array($_GET['t'], ["t", "w", "m", "a"])) {
	$time = $_GET['t'];
} else {
	$time = "t";
}
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = 20;
$offs = ($page - 1) * $ppv;

if($browse_sort == "rf") {
	$videos = $conn->query("SELECT * FROM picks LEFT JOIN videos ON videos.vid = picks.video LEFT JOIN users ON users.uid = videos.uid WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) ORDER BY picks.featured DESC LIMIT $ppv OFFSET $offs");
} elseif($browse_sort == "mr") {
	$videos = $conn->query("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) ORDER BY uploaded DESC LIMIT $ppv OFFSET $offs");
} elseif($browse_sort == "mp") {
	if($time == "t") {
		$videos = $conn->query(
			"SELECT * FROM videos
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 2 DAY)
			ORDER BY views DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "w") {
		$videos = $conn->query(
			"SELECT * FROM videos
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 WEEK)
			ORDER BY views DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "m") {
		$videos = $conn->query(
			"SELECT * FROM videos
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 MONTH)
			ORDER BY views DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "a") {
		$videos = $conn->query(
			"SELECT * FROM videos
			LEFT JOIN users ON users.uid = videos.uid
			WHERE videos.converted = 1
			ORDER BY views DESC LIMIT $ppv OFFSET $offs"
		);
	}
} elseif($browse_sort == "tr") {
	if($time == "t") {
		$videos = $conn->query(
			"SELECT * FROM ratings
			LEFT JOIN videos ON videos.vid = ratings.video
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND videos.allow_votes = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY ratings.video
			ORDER BY AVG(ratings.rating) DESC, COUNT(ratings.rating) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "w") {
		$videos = $conn->query(
			"SELECT * FROM ratings
			LEFT JOIN videos ON videos.vid = ratings.video
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 WEEK) GROUP BY ratings.video
			ORDER BY AVG(ratings.rating) DESC, COUNT(ratings.rating) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "m") {
		$videos = $conn->query(
			"SELECT * FROM ratings
			LEFT JOIN videos ON videos.vid = ratings.video
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP BY ratings.video
			ORDER BY AVG(ratings.rating) DESC, COUNT(ratings.rating) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "a") {
		$videos = $conn->query(
			"SELECT * FROM ratings
			LEFT JOIN videos ON videos.vid = ratings.video
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) GROUP BY ratings.video
			ORDER BY AVG(ratings.rating) DESC, COUNT(ratings.rating) DESC LIMIT $ppv OFFSET $offs"
		);
	}    
} elseif($browse_sort == "md") {
    // Looks like it was pretty much timeless until early 2006
	        $videos = $conn->query(
			"SELECT * FROM comments
			LEFT JOIN videos ON videos.vid = comments.vidon
			LEFT JOIN users ON users.uid = videos.uid
			WHERE ((videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND users.termination = 0) GROUP BY comments.vidon
			ORDER BY COUNT(comments.cid) DESC LIMIT $ppv OFFSET $offs"
		);
} elseif($browse_sort == "mf") {
	if($time == "t") {
		$videos = $conn->query(
			"SELECT * FROM favorites
			LEFT JOIN videos ON videos.vid = favorites.vid
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY favorites.vid
			ORDER BY COUNT(favorites.fid) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "w") {
		$videos = $conn->query(
			"SELECT * FROM favorites
			LEFT JOIN videos ON videos.vid = favorites.vid
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 WEEK) GROUP BY favorites.vid
			ORDER BY COUNT(favorites.fid) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "m") {
		$videos = $conn->query(
			"SELECT * FROM favorites
			LEFT JOIN videos ON videos.vid = favorites.vid
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) AND videos.privacy = 1  AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP BY favorites.vid
			ORDER BY COUNT(favorites.fid) DESC LIMIT $ppv OFFSET $offs"
		);
	} elseif($time == "a") {
		$videos = $conn->query(
			"SELECT * FROM favorites
			LEFT JOIN videos ON videos.vid = favorites.vid
			LEFT JOIN users ON users.uid = videos.uid
			WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) GROUP BY favorites.vid
			ORDER BY COUNT(favorites.fid) DESC LIMIT $ppv OFFSET $offs"
		);
	}
} elseif($browse_sort == "r") {
	$videos = $conn->query("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0) ORDER BY RAND() DESC LIMIT $ppv OFFSET $offs");
}
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
					<td width="260">
						<div class="moduleTitle">
							<?php
				switch($browse_sort) {
					case 'mp':
						echo "Most Viewed";
						break;
					case 'md':
						echo "Most Discussed";
						break;
					case 'mf':
						echo "Top Favorites";
						break;
					case 'r':
						echo "Random";
						break;
				    case 'rf':
						echo "Recently Featured";
						break;
                    case 'tr':
						echo "Top Rated";
						break;
					default:
						echo "Most Recent";
				}
				?>						</div>
					</td>
					<td width="260" align="center">
						<div style="font-weight: normal; font-size: 11px; color: #444444">
                        <?php // if($browse_sort !== "mr" && $browse_sort !== "r" && $browse_sort !== "rf") {
                if($browse_sort !== "mr" && $browse_sort !== "r" && $browse_sort !== "md" && $browse_sort !== "rf") { ?>
					<div style="float: right; font-weight: normal; font-size: 11px; color: #444444; padding-right: 5px;">
						<?php echo ($time == "t") ? '<strong>Today</strong>' : '<a href="browse.php?s='.$browse_sort.'&t=t">Today</a>'; ?>
						|
						<?php echo ($time == "w") ? '<strong>This Week</strong>' : '<a href="browse.php?s='.$browse_sort.'&t=w">This Week</a>'; ?>
						|
						<?php echo ($time == "m") ? '<strong>This Month</strong>' : '<a href="browse.php?s='.$browse_sort.'&t=m">This Month</a>'; ?>
						|
						<?php echo ($time == "a") ? '<strong>All Time</strong>' : '<a href="browse.php?s='.$browse_sort.'&t=a">All Time</a>'; ?>
					</div>
				<?php } else { if ($_GET['f'] == 'v') { ?>
				<div style="float: center; padding: 1px 5px 0px 0px; font-size: 12px;">
								<a href="/browse.php?s=<?= $browse_sort ?>&amp;f=b&amp;page=<?php echo ($_GET['page']) ? $_GET['page'] : '1'; ?>&amp;t=<?php echo ($_GET['t']) ? $_GET['t'] : 't'; ?>">Basic View</a>
								| 
								<strong>Detailed View</strong>
							</div>
				<? } else { ?>
				<strong>Basic View</strong>
								| 
								<a href="/browse.php?s=<?= $browse_sort ?>&amp;f=v&amp;page=<?php echo ($_GET['page']) ? $_GET['page'] : '1'; ?>&amp;t=<?php echo ($_GET['t']) ? $_GET['t'] : 't'; ?>">Detailed View</a>
				<? } ?>
				        	<? } ?>
												</div>
					</td>
					<td width="260" align="right">
					<div style="font-weight: bold; color: #444444; margin-right: 5px;">
					
						Videos <?php if ($offs > 0) { echo htmlspecialchars(trim($offs)); } else { echo "1"; } ?>-<? $nextynexty = $offs + $ppv; echo htmlspecialchars($nextynexty); ?> of 300							
					</div>
					</td>
				</tr>
			</tbody></table>
		</div>
				
		<div class="moduleFeatured"> 
			<table width="770" cellpadding="0" cellspacing="0" border="0">
				<tbody><?php $i = 0;
        foreach($videos as $video) { ?>
        <? if ($_GET['f'] == 'v') { ?>
        <div class="moduleEntry">
		<table>
			<tbody><tr>
				<td>
					<table style="border-right: 1px dashed #999999;">
						<tbody><tr>
							<td>
							</td><td>
								<a href="/watch.php?v=<?php echo $video['vid']; ?>"><img src="/get_still.php?video_id=<?php echo $video['vid']; ?>&amp;still_id=1" class="moduleFeaturedThumb" width="100" height="75"></a>
							</td>
							<td>
								<a href="/watch.php?v=<?php echo $video['vid']; ?>"><img src="/get_still.php?video_id=<?php echo $video['vid']; ?>&amp;still_id=2" class="moduleFeaturedThumb" width="100" height="75"></a>
							</td>
							<td>
								<a href="/watch.php?v=<?php echo $video['vid']; ?>"><img src="/get_still.php?video_id=<?php echo $video['vid']; ?>&amp;still_id=3" class="moduleFeaturedThumb" width="100" height="75"></a>
							</td>
						</tr>
					</tbody></table>
				</td>
				<td width="10px">&nbsp;</td>
					<td width="100%"><div class="moduleEntryTitle" style="word-break: break-all;"><a href="watch.php?v=<?php echo $video['vid']; ?>"><?php echo htmlspecialchars($video['title']); ?></a></div>
							<div class="moduleEntryDescription"><?php
$description = htmlspecialchars($video['description']);
$description = (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
echo $description;
?></div>
					<div class="moduleEntryTags">
						Tags // <?php foreach(explode(" ", $video['tags']) as $tag) echo '<a href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> : '; ?>
					</div>
						<div class="moduleEntryDetails">Channels // <? showChannels($video['vid']); ?>
						</div>
					<div class="moduleEntryDetails">Added: <?php echo retroDate($video['uploaded']); ?> by <a href="profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo htmlspecialchars($video['username']); ?></a></div>
							<div class="moduleEntryDetails">Runtime: <?php echo gmdate("i:s", $video['time']); ?> | Views: <?php echo $video['views']; ?> | Comments: <?php echo $video['comm_count']; ?></div>
					
				</td>
			
		</tr></tbody></table>
	</div>
        <? } else { ?>
				<?php			
                $i = $i + 1;
						if($i == 1) {
							echo '<tr valign="top">';
						}
				?><td width="20%" align="center"><a href="watch.php?v=<?php echo $video['vid']; ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>" width="120" height="90" class="moduleFeaturedThumb"></a><div class="moduleFeaturedTitle"><a href="watch.php?v=<?php echo $video['vid']; ?>" ><?php echo htmlspecialchars($video['title']); ?></a></div><div class="moduleFeaturedDetails">Added: <?php echo timeAgo($video['uploaded']); ?><br>by <a href="profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo htmlspecialchars($video['username']); ?></a></div><div class="moduleFeaturedDetails" style="padding-bottom: 5px;">Runtime: <?php echo gmdate("i:s", $video['time']); ?><br>Views: <?php echo number_format($video['views']); ?> | Comments: <?php echo number_format($video['comm_count']); ?></div><? grabRatingsPage($video['vid'], "SM", 0); ?></td><? if($i == 5) { echo '</tr>'; $i = 0; } } ?><? } ?>
			</tbody></table>
		</div>
				<!-- begin paging -->
				<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Browse Pages:
				
					<?php
					if(empty($_GET['f'])) { $GET['f'] == 'b'; }
    $totalPages = ceil(300 / $ppv);
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $page) {
            echo '<span style="color: #444; background-color: #FFFFFF; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;">' . $i . '</span>';
        } else {
            echo '<span style="background-color: #CCC; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><a href="browse.php?page=' . $i . '&s=' . $browse_sort . '&t=' . $time . '&f=' . $_GET['f'] . '">' . $i . '</a></span>';
        }
    }
    ?>					
				</div>
				<!-- end paging -->
		
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