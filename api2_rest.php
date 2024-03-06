<?php
require "needed/start.php";
ob_clean();
//if (!isset($_GET['stillwip'])){
//die();
//}
function getRatingAverage($vid) {
    $avg = $GLOBALS['conn']->prepare("SELECT AVG(rating) FROM ratings WHERE video = ?");
    $avg->execute([$vid]);
    $average = $avg->fetchColumn();
	if($average == NULL) {
	$average = 0;
	}
    return $average;

}
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>';
$missingparam = '<ut_response status="fail"><error><code>4</code><description>Missing required parameter.</description></error></ut_response>';
if ($_GET['method'] == "youtube.videos.list_featured" || $_GET['method'] == "kamtape.videos.list_featured" || $_GET['method'] == "youtube.users.list_favorite_videos" || $_GET['method'] == "kamtape.users.list_favorite_videos" || $_GET['method'] == "youtube.videos.list_by_tag" || $_GET['method'] == "kamtape.videos.list_by_tag" || $_GET['method'] == "youtube.videos.list_by_user" || $_GET['method'] == "kamtape.videos.list_by_user") {
switch($_GET['method']) {
case "youtube.videos.list_featured":
$featured = $conn->query(
"SELECT * FROM picks 
LEFT JOIN videos ON videos.vid = picks.video
LEFT JOIN users ON users.uid = videos.uid
WHERE (videos.converted = 1 AND videos.privacy = 1) GROUP BY picks.video
ORDER BY picks.featured DESC LIMIT 25"
);
break;
case "kamtape.videos.list_featured":
$featured = $conn->query(
"SELECT * FROM picks 
LEFT JOIN videos ON videos.vid = picks.video
LEFT JOIN users ON users.uid = videos.uid
WHERE (videos.converted = 1 AND videos.privacy = 1) GROUP BY picks.video
ORDER BY picks.featured DESC LIMIT 25"
);
break;
case "youtube.users.list_favorite_videos":
if(!isset($_GET['user'])) {
echo $missingparam;
die();
}
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
die();
} else {
$profile = $profile->fetch(PDO::FETCH_ASSOC);
}

$featured = $conn->prepare(
"SELECT * FROM favorites
LEFT JOIN videos ON favorites.vid = videos.vid
LEFT JOIN users ON users.uid = videos.uid
WHERE favorites.uid = ?
ORDER BY favorites.fid DESC LIMIT 10"
);
$featured->execute([$profile['uid']]);
break;
case "kamtape.users.list_favorite_videos":
if(!isset($_GET['user'])) {
echo $missingparam;
die();
}
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
die();
} else {
$profile = $profile->fetch(PDO::FETCH_ASSOC);
}

$featured = $conn->prepare(
"SELECT * FROM favorites
LEFT JOIN videos ON favorites.vid = videos.vid
LEFT JOIN users ON users.uid = videos.uid
WHERE favorites.uid = ?
ORDER BY favorites.fid DESC LIMIT 10"
);
$featured->execute([$profile['uid']]);
break;
case "youtube.videos.list_by_tag":
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$offs = ($page - 1) * $ppv;
if(!isset($_GET['tag'])) {
echo $missingparam;
die();
}
$search = str_replace(" ", "|", $_GET['tag']);
$featured = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 ORDER BY (INSTR(LOWER(tags), LOWER(?)) > 0) DESC LIMIT $ppv OFFSET $offs");
$featured->execute([$search, $search, $search, $search, $search]);
break;
case "kamtape.videos.list_by_tag":
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$offs = ($page - 1) * $ppv;
if(!isset($_GET['tag'])) {
echo $missingparam;
die();
}
$search = str_replace(" ", "|", $_GET['tag']);
$featured = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 ORDER BY (INSTR(LOWER(tags), LOWER(?)) > 0) DESC LIMIT $ppv OFFSET $offs");
$featured->execute([$search, $search, $search, $search, $search]);
break;
case "youtube.videos.list_by_user":
if(!isset($_GET['user'])) {
echo $missingparam;
die();
}
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
die();
} else {
$profile = $profile->fetch(PDO::FETCH_ASSOC);
}

$featured = $conn->prepare(
"SELECT * FROM videos
LEFT JOIN users ON users.uid = videos.uid
WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
ORDER BY videos.uploaded DESC"
);
$featured->execute([$profile['uid']]);
break;
case "kamtape.videos.list_by_user":
if(!isset($_GET['user'])) {
echo $missingparam;
die();
}
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
die();
} else {
$profile = $profile->fetch(PDO::FETCH_ASSOC);
}

$featured = $conn->prepare(
"SELECT * FROM videos
LEFT JOIN users ON users.uid = videos.uid
WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
ORDER BY videos.uploaded DESC"
);
$featured->execute([$profile['uid']]);
break;
}
?>

<ut_response status="ok">
    <video_list>
<?php foreach($featured as $pick) { 
$pick['views'] = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE vid = ?");
$pick['views']->execute([$pick['vid']]);
$pick['views'] = $pick['views']->fetchColumn();
	
$pick['comments'] = $conn->prepare("SELECT COUNT(cid) FROM comments WHERE vidon = ?");
$pick['comments']->execute([$pick['vid']]);
$pick['comments'] = $pick['comments']->fetchColumn();
?>
        <video>
            <author><?php echo htmlspecialchars($pick['username']); ?></author>
            <id><?php echo htmlspecialchars($pick['vid']); ?></id>
            <title><?php echo htmlspecialchars($pick['title']); ?></title>
            <length_seconds><?php echo htmlspecialchars($pick['time']); ?></length_seconds>
            <rating_avg><? echo htmlspecialchars(getRatingAverage($pick['vid'])); ?></rating_avg>
            <rating_count><? echo htmlspecialchars(getRatingCount($pick['vid'])); ?></rating_count>
            <description><?php echo htmlspecialchars($pick['description']); ?></description>
            <view_count><?php echo htmlspecialchars($pick['views']); ?></view_count>
            <upload_time><?php echo strtotime($pick['uploaded']); ?></upload_time>
            <comment_count><?php if ($pick['comments'] != 0) { ?><?php echo htmlspecialchars($pick['comments']); ?><? } else { ?>None<? } ?></comment_count>
            <tags><?php echo htmlspecialchars($pick['tags']); ?></tags>
            <url>http://www.kamtape.com/watch.php?v=<?php echo htmlspecialchars($pick['vid']); ?></url>
            <thumbnail_url>http://www.kamtape.com/get_still.php?video_id=<?php echo htmlspecialchars($pick['vid']); ?></thumbnail_url>
        </video>
<? } ?>
    </video_list>
</ut_response>
<? } elseif ($_GET['method'] == "youtube.videos.get_details" || $_GET['method'] == "kamtape.videos.get_details") { 
if(!isset($_GET['video_id'])) {
echo $missingparam;
die();
}
$video = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video->execute([$_GET['video_id']]);

if($video->rowCount() == 0) {
	echo '<ut_response status="fail"><error><code>102</code><description>No video was found with the specified ID.</description></error></ut_response>';
	die();
} else {
	$video = $video->fetch(PDO::FETCH_ASSOC);
}
$uploader = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$uploader->execute([$video['uid']]);
$uploader = $uploader->fetch(PDO::FETCH_ASSOC);
$video['views'] = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE vid = ?");
$video['views']->execute([$video['vid']]);
$video['views'] = $video['views']->fetchColumn();
	
//$video['comments'] = $conn->prepare("SELECT COUNT(cid) FROM comments WHERE vidon = ?");
//$video['comments']->execute([$video['vid']]);
//$video['comments'] = $video['comments']->fetchColumn();
$comments = $conn->prepare("SELECT * FROM comments LEFT JOIN users ON users.uid = comments.uid WHERE vidon = ? AND users.termination = 0 AND is_reply = 0 ORDER BY post_date DESC");
$comments->execute([$video['vid']]);
$comments = $comments->fetchAll(PDO::FETCH_ASSOC);
function showChannelsXML($vid) {
$video2chan = $GLOBALS['conn']->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video2chan->execute([$vid]);

if($video2chan->rowCount() == 0) {
 echo "";
} else {
    global $_VCHANE;
	$video2chan = $video2chan->fetch(PDO::FETCH_ASSOC);
    $ch1 = $video2chan['ch1'];
    $ch2 = $video2chan['ch2'];
    $ch3 = $video2chan['ch3'];
    if ($ch1 != NULL && $ch2 != NULL) {
     echo '<channel>'.$_VCHANE[$ch1].'</channel>

						, ';
    } elseif ($ch1 != NULL && $ch2 == NULL && $ch3 == NULL) {
     echo '<channel>'.$_VCHANE[$ch1].'</channel>

						';
    }
    
    if ($ch2 != NULL && $ch3 != NULL) {
     echo '<channel>'.$_VCHANE[$ch2].'</channel>

						, ';
    } elseif ($ch2 != NULL && $ch3 == NULL) {
     echo '<channel>'.$_VCHANE[$ch2].'</channel>

						';
    }

  if ($ch3 != NULL) {
     echo '<channel>'.$_VCHANE[$ch3].'</channel>

						';
    }
  }
}
?>

<ut_response status="ok">
    <video_details>
        <author><?php echo htmlspecialchars($uploader['username']); ?></author>
        <title><?php echo htmlspecialchars($video['title']); ?></title>
        <rating_avg><? echo htmlspecialchars(getRatingAverage($video['vid'])); ?></rating_avg>
        <rating_count><? echo htmlspecialchars(getRatingCount($video['vid'])); ?></rating_count>
        <tags><?php echo htmlspecialchars($video['tags']); ?></tags>
        <description><?php echo htmlspecialchars($video['description']); ?></description>
        <update_time><?php echo strtotime($video['updated']); ?></update_time>
        <view_count><?php echo htmlspecialchars($video['views']); ?></view_count>
        <upload_time><?php echo strtotime($video['uploaded']); ?></upload_time>
        <length_seconds><?php echo htmlspecialchars($video['time']); ?></length_seconds>
        <recording_date<?php if ($video['recorddate'] != NULL) { ?>><?php echo htmlspecialchars($video['recorddate']); ?></recording_date><? } else { ?> /><? } ?>

        <recording_location<?php if ($video['address'] != NULL) { ?>><?php echo htmlspecialchars($video['address']); ?></recording_location><? } else { ?> /><? } ?>

        <recording_country<?php if ($video['addrcountry'] != NULL) { ?>><?php echo htmlspecialchars($video['addrcountry']); ?></recording_country><? } else { ?> /><? } ?>

        <comment_list>
<?php if($comments !== false) {
foreach($comments as $comment) {
?>
            <comment>
                <author><?php echo htmlspecialchars($comment['username']); ?></author>
                <text><?php echo htmlspecialchars($comment['body']); ?></text>
                <time><?php echo strtotime($comment['post_date']); ?></time>
            </comment>
<? } } ?>
        </comment_list>
        <channel_list>
            <?php showChannelsXML($video['vid']); ?>
        </channel_list>
        <thumbnail_url>http://www.kamtape.com/get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?></thumbnail_url>
    </video_details>
</ut_response>
<? } elseif ($_GET['method'] == "youtube.users.get_profile" || $_GET['method'] == "kamtape.users.get_profile") { 

if(!isset($_GET['user'])) {
echo $missingparam;
die();
}

$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
	echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
	die();
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
}
    $profile['videos'] = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND privacy = 1 AND converted = 1");
    $profile['videos']->execute([$profile["uid"]]);
    $profile['videos'] = $profile['videos']->rowCount();

    $profile['favorites'] = $conn->prepare("SELECT fid FROM favorites WHERE uid = ?");
    $profile['favorites']->execute([$profile["uid"]]);
    $profile['favorites'] = $profile['favorites']->rowCount();

    $profile['watched'] = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE uid = ?");
    $profile['watched']->execute([$profile['uid']]);
    $profile['watched'] = $profile['watched']->fetchColumn();

    $profile['friends'] = $conn->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
    $profile['friends']->execute([$profile["uid"],$profile["uid"]]);
    $profile['friends'] = $profile['friends']->fetchColumn();
?>

<ut_response status="ok">
    <user_profile>
        <first_name><?php echo htmlspecialchars($profile['name']); ?></first_name>
        <last_name>Jones</last_name>
        <about_me><?php echo htmlspecialchars($profile['about']); ?></about_me>
        <age><?php if ($profile['birthday'] != '0000-00-00' && $profile['birthday'] != NULL) { echo str_replace(' years ago', '', timeAgo($profile['birthday'])); } ?></age>
        <video_upload_count><?php echo htmlspecialchars($profile['videos']); ?></video_upload_count>
        <video_watch_count><?php echo htmlspecialchars($profile['watched']); ?></video_watch_count>
        <homepage><?php echo htmlspecialchars($profile['website']); ?></homepage>
        <hometown><?php echo htmlspecialchars($profile['hometown']); ?></hometown>
        <gender><?php
					switch($profile['gender']) {
						case '0':
							break;
						case '1':
							echo "m";
							break;
						case '2':
							echo "f";
							break;
                        case '3':
						echo "o";
						break;
                        default:
                        echo "o";
                        break;
					}
				?></gender>
        <occupations><?php echo htmlspecialchars($profile['occupations']); ?></occupations>
        <companies><?php echo htmlspecialchars($profile['companies']); ?></companies>
        <city><?php echo htmlspecialchars($profile['city']); ?></city>
        <country><?php echo htmlspecialchars($profile['country']); ?></country>
        <books><?php echo htmlspecialchars($profile['books']); ?></books>
        <hobbies><?php echo htmlspecialchars($profile['hobbies']); ?></hobbies>
        <movies><?php echo htmlspecialchars($profile['fav_media']); ?></movies>
        <relationship><?php
					    switch($profile['relationship']) {
						case '0':
							echo "open";
							break;
						case '1':
							echo "single";
							break;
						case '2':
							echo "taken";
							break; 
                            default:
                        echo "open";
                         break; }?></relationship>
        <friend_count><?php echo htmlspecialchars($profile['friends']); ?></friend_count>
        <favorite_video_count><?php echo htmlspecialchars($profile['favorites']); ?></favorite_video_count>
        <currently_on>false</currently_on>
    </user_profile>
</ut_response>
<? } elseif ($_GET['method'] == "youtube.users.list_friends" || $_GET['method'] == "kamtape.users.list_friends") {  
if(!isset($_GET['user'])) {
echo $missingparam;
die();
}

$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['user']]);

if($profile->rowCount() == 0) {
	echo '<ut_response status="fail"><error><code>101</code><description>No user was found with the specified username.</description></error></ut_response>';
	die();
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
}

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
?>

<ut_response status="ok">
    <friend_list>
<?php foreach($friends as $friend) {

$friend['vids'] = $conn->prepare("SELECT COUNT(vid) FROM videos WHERE uid = ? AND converted = 1");
$friend['vids']->execute([$friend['uid']]);
$friend['vids'] = $friend['vids']->fetchColumn();

$friend['favs'] = $conn->prepare("SELECT COUNT(fid) FROM favorites WHERE uid = ?");
$friend['favs']->execute([$friend['uid']]);
$friend['favs'] = $friend['favs']->fetchColumn();

$friend['friends'] = $conn->prepare("SELECT COUNT(relationship) FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
$friend['friends']->execute([$friend['uid'], $friend['uid']]);
$friend['friends'] = $friend['friends']->fetchColumn();
?>
        <friend>
            <user><?= htmlspecialchars($friend['username']) ?></user>
            <video_upload_count><?= $friend['vids'] ?></video_upload_count>
            <favorite_count><?= $friend['favs'] ?></favorite_count>
            <friend_count><?= $friend['friends'] ?></friend_count>
        </friend>
<? } ?>
    </friend_list>
</ut_response>
<?
} elseif (empty($_GET['method']) || !isset($_GET['method'])) { 
echo '<ut_response status="fail"><error><code>5</code><description>No method specified.</description></error></ut_response>';
} else {
echo '<ut_response status="fail"><error><code>6</code><description>Unknown method specified.</description></error></ut_response>';
}
?>