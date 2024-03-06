<?php
require __DIR__ . "/../needed/scripts.php";

if($_SERVER['REQUEST_URI'] == "/rss/global/recently_added.rss" || $_SERVER['REQUEST_URI'] == "/rss/") {
	$feed_title = $feed_description = "Recently Added Videos";
	$feed_link = "http://www.kamtape.com/rss/global/recently_added.rss";
	$videos = $conn->query("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0 ORDER BY videos.uploaded DESC LIMIT 15");
} elseif (preg_match_all('/^\/rss\/user\/(\w+)\/my_videos\.rss$/m', $_SERVER['REQUEST_URI'], $matches, PREG_SET_ORDER, 0)) {
	$user = $conn->prepare("SELECT * FROM users WHERE username = ?");
	$user->execute([$matches['0']['1']]);
	if($user->rowCount() > 0) {
		$user = $user->fetch(PDO::FETCH_ASSOC);
		$feed_title = "Videos by ".htmlspecialchars($user['username']);
		$feed_description = "Videos uploaded by ".htmlspecialchars($user['username'])." hosted at http://www.kamtape.com.";
		$feed_link = "http://kamtape.com/rss/user/".htmlspecialchars($user['username'])."/my_videos.rss";
		$videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0  ORDER BY videos.uploaded DESC LIMIT 15");
		$videos->execute([$user['uid']]);
	} else {
		require __DIR__ . "/../errors/404.php";
	}
} elseif (preg_match_all('/^\/rss\/tag\/(.*).rss$/m', $_SERVER['REQUEST_URI'], $matches, PREG_SET_ORDER, 0)) {
	$matches['0']['1'] = str_replace("+", " ", $matches['0']['1']);
	$search = str_replace(" ", "|", $matches['0']['1']);
	$videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE videos.tags REGEXP ? AND videos.converted = 1 AND videos.privacy = 1 AND users.termination = 0  ORDER BY videos.uploaded DESC"); // Regex!
	$videos->execute([$search]);
	
	if(strlen($matches['0']['1']) < 2) {
		class videos { // stupid
			function rowCount() {
				return 0;
			}
		}
		$videos = new videos;
	}
	
	$feed_title = "Tag // ".htmlspecialchars($matches['0']['1']);
	$feed_description = "Videos tagged with ".htmlspecialchars($matches['0']['1']);
	$feed_link = "http://kamtape.com/rss/tag/".urlencode($matches['0']['1']).".rss";
} else {
	require __DIR__ . "/../err/unfound.php";
    die();
}

header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="utf-8"?>'; // Have to echo this or else dreamhost throws a fit...
?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss">
	<channel>
		<title>KamTape :: <?php echo $feed_title; ?></title>
		<link><?php echo $feed_link; ?></link>
		<description><?php echo $feed_description; ?></description>
		<?php foreach ($videos as $video) { ?>
		<item>
			<author>rss@kamtape.com (<?php echo htmlspecialchars($video['username']); ?>)</author>
			<title><?php echo htmlspecialchars($video['title']); ?></title>
			<link>http://www.kamtape.com/?v=<?php echo htmlspecialchars($video['vid']); ?></link>
			<description><![CDATA[
				<img src="http://www.kamtape.com/get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>" align="right" border="0" width="120" height="90" vspace="4" hspace="4" />
				<p><?php echo htmlspecialchars($video['description']); ?></p>
				<p>
					Author: <a href="http://www.kamtape.com/profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo htmlspecialchars($video['username']); ?></a><br/>
					Keywords: <?php foreach(explode(" ", $video['tags']) as $tag) echo '<a href="http://www.kamtape.com/results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> '; ?><br/>
					Added: <?php echo retroDate($video['uploaded']); ?><br/>
				</p>
			]]></description>
			<guid isPermaLink="true">http://www.kamtape.com/?v=<?php echo htmlspecialchars($video['vid']); ?></guid>
			<pubDate><?php echo retroDate($video['uploaded'], "r"); ?></pubDate>

			<media:player url="http://www.kamtape.com/?v=<?php echo htmlspecialchars($video['vid']); ?>" />
			<media:thumbnail url="http://www.kamtape.com/get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>" width="120" height="90" />
			<media:title><?php echo htmlspecialchars($video['title']); ?></media:title>
			<media:category label="Tags"><?php echo htmlspecialchars($video['tags']); ?></media:category>
			<media:credit><?php echo htmlspecialchars($video['username']); ?></media:credit>
			<enclosure url="http://www.kamtape.com/v/<?php echo htmlspecialchars($video['vid']); ?>.swf" length="<?php echo htmlspecialchars($video['time']); ?>" type="application/x-shockwave-flash" />
		</item>
		<?php } ?>
	</channel>
</rss>