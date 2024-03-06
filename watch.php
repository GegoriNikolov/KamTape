<?php 
require "needed/start.php";
$_GET['v'] = substr($_GET['v'], 0, 11);
if(isset($_SERVER['HTTP_REFERER'])) {
    $vidReferer = $_SERVER['HTTP_REFERER'];
} else {
     $vidReferer = "https://www.kamtape.com";
}
if(strpos($_SERVER['HTTP_REFERER'], "www.kamtape.com") !== false){
  $vidReferer = "https://www.kamtape.com";  
}

if(strpos($_SERVER['HTTP_REFERER'], "66.33.192.247") !== false){
  $vidReferer = "https://www.kamtape.com";  
}
$video = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video->execute([$_GET['v']]);

if($video->rowCount() == 0) {
	header("Location: index.php?unavail");
	die();
} else {
	$video = $video->fetch(PDO::FETCH_ASSOC);
}
if ($video['uid'] != $session['uid'] || $session['staff'] != 1) {
if($video['reason'] == 1) { session_error_index("This video has been removed by the user.", "error"); }
if($video['reason'] == 3) { session_error_index("This video has been removed due to copyright infringement.", "error"); }
if($video['reason'] == 2) { session_error_index("This video has been removed due to terms of use violation.", "error"); }
}
if ($video['uid'] == $session['uid'] || $session['staff'] == 1) {
if($video['reason'] == 1) { session_error_index("This video has been removed by the user.", "error"); }
if($video['reason'] == 3) { alert("Your video has been removed due to copyright infringement.", "error"); }
if($video['reason'] == 2) { alert("Your video has been removed due to terms of use violation.", "error"); }
}
$uploader = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$uploader->execute([$video['uid']]);
$uploader = $uploader->fetch(PDO::FETCH_ASSOC);
    $alreadyrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :member_id AND respondent = :him AND accepted = 1");
    $alreadyrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $uploader['uid']
    ]);

    if($alreadyrelated === 1) {
	$friendswith = 1;
    }

    $newrelated = $conn->prepare("SELECT COUNT(*) FROM relationships WHERE sender = :him AND respondent = :member_id AND accepted = 1");
    $newrelated->execute([
	":member_id" => $session['uid'],
    ":him" => $uploader['uid']
    ]);

    if($newrelated === 1) {
	$friendswith = 1;
    }  
    if($session['staff'] == 1) {
    $friendswith = 1;    
    }
    if ($uploader['uid'] == $session['uid']) {
    $friendswith = 1;        
    }
    if($friendswith < 1 && $video['privacy']  == 2) {
    session_error_index("This is a private video. If you have been sent this video, please make sure you accept the sender's friend request.", "error");
    }
    
if ($uploader['uid'] == NULL) {
    redirect("/index.php");
}

if ($uploader['termination'] == 1) {
    redirect("/index.php");
}

if($video['converted'] == 0) {
	header("Location: index.php");
}
echo "<title>KamTape - ".htmlspecialchars($video['title'])."</title>";

$subscribed = $conn->prepare("SELECT subscription_id FROM subscriptions WHERE subscriber = ? AND subscribed_to = ? AND subscribed_type = 'user_uploads'");
    $subscribed->execute([
	$session['uid'],
	$uploader['uid']
    ]);

$search = preg_quote($video['tags']); // Escape special characters for regular expression
$search = str_replace(" ", "|", $search);
								$results = $conn->prepare("SELECT tags FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE videos.tags REGEXP ? AND videos.converted = 1 And videos.privacy = 1 ORDER BY videos.uploaded DESC LIMIT 200"); // Regex!
								$results->execute([$search]);
                                /*$views = $conn->prepare("SELECT views FROM videos WHERE vid = ?");
$views->execute([$video['vid']]);
$video['views'] = $views->fetchColumn();*/
$notOrganic = false;

    $playing = $conn->prepare("
        SELECT * 
        FROM videos 
        LEFT JOIN users ON users.uid = videos.uid 
        WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) 
        AND videos.privacy = 1 
        AND videos.converted = 1 
        AND users.termination = 0 
        ORDER BY (INSTR(LOWER(description), LOWER(?)) > 0)
        LIMIT 1000
    ");
    $playing->execute([$search, $search, $search, $search, $_GET['v']]);
    $playing = $playing->fetchAll(PDO::FETCH_ASSOC);
    $schedulla = array_column($playing, 'vid');
    $schedex = array_search($_GET['v'], $schedulla);
    $play_schedule = [];
    $play_schedule['before'] = ($schedex > 0) ? $schedulla[$schedex - 1] : null;
    $play_schedule['next'] = ($schedex < count($schedulla) - 1) ? $schedulla[$schedex + 1] : null;


// Check for organic views (better spam prevention)
$organ_views = $conn->prepare("SELECT COUNT(view_id) AS views FROM views WHERE vid = ? AND viewed > DATE_SUB(NOW(), INTERVAL 1 DAY)");
$organ_views->execute([$video['vid']]);
$organc = $organ_views->fetchColumn();

if ($organc > 300) {
    $notOrganic = true;
}

// Check for organic views (better spam prevention)
$organ_views = $conn->prepare("SELECT COUNT(view_id) AS views FROM views WHERE vid = ? AND viewed > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
$organ_views->execute([$video['vid']]);
$organc = $organ_views->fetchColumn();

if ($organc > 15) {
    $notOrganic = true;
}

if ($notOrganic = true) {
$already_viewed = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE vid = ? AND viewed > DATE_SUB(NOW(), INTERVAL 1 HOUR)");    
$already_viewed->execute([$video['vid']]);
} else {
$already_viewed = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE vid = ? AND sid = ? AND viewed > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$already_viewed->execute([$video['vid'], session_id()]);
}

if($already_viewed->fetchColumn() == 0) {
    if($_SESSION['uid'] != NULL) { 
	$add_view = $conn->prepare("INSERT INTO views (view_id, referer, vid, sid, uid) VALUES (?, ?, ?, ?, ?)");
	$add_view->execute([generateId(34), $vidReferer, $video['vid'], session_id(), $session['uid']]);
	$add_view_cnt = $conn->prepare("UPDATE videos SET views = views + 1 WHERE vid = ?");
	$add_view_cnt->execute([$video['vid']]);
	$add_view_vidswatched = $conn->prepare("UPDATE users SET vids_watched = vids_watched + 1 WHERE uid = ?");
	$add_view_vidswatched->execute([$session['uid']]);
    } else {
    $add_view = $conn->prepare("INSERT INTO views (view_id, referer, vid, sid, uid) VALUES (?, ?, ?, ?, NULL)");
	$add_view->execute([generateId(34), $vidReferer, $video['vid'], session_id()]);    
	$add_view_cnt = $conn->prepare("UPDATE videos SET views = views + 1 WHERE vid = ?");
	$add_view_cnt->execute([$video['vid']]);
    }
}
/*$maker_videos = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND converted = 1 AND privacy =1");
$maker_videos->execute([$video["uid"]]);
					
$maker_favorites = $conn->prepare("SELECT fid FROM favorites WHERE uid = ?");
$maker_favorites->execute([$video["uid"]]);

$maker_friends = $GLOBALS['conn']->prepare("SELECT relationship FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
$maker_friends->execute([$video['uid'], $video['uid']]);*/

$comments = $conn->prepare("SELECT * FROM comments LEFT JOIN users ON users.uid = comments.uid WHERE vidon = ? AND users.termination = 0 AND is_reply = 0 ORDER BY post_date DESC");
$comments->execute([$video['vid']]);

if($_SESSION['uid'] != NULL) { 
    // Logged in stuff
            $favorites_of_you = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY favorites.fid DESC"
);
$favorites_of_you->execute([$session['uid']]);

$videos_of_you = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1 AND videos.privacy = 1
	ORDER BY videos.uploaded DESC"
);
$videos_of_you->execute([$session['uid']]);
// End logged in stuff
}
$ACCESSES = $conn->prepare("SELECT referer FROM views WHERE vid = ? AND referer NOT LIKE '%kamtape.com%' ORDER BY viewed DESC");
$ACCESSES->execute([$video['vid']]);
$SITE_LIST = [];

foreach ($ACCESSES as $WEBSITE) {
    $SITE_LIST[] = $WEBSITE['referer'];
}

function isUserOnline($lastLoginDate) {
    // Define an online threshold (e.g., 5 minutes) in seconds
    $onlineThreshold = 5 * 60;

    // Convert the last login date to a Unix timestamp
    $lastLoginTimestamp = strtotime($lastLoginDate);

    // Calculate the difference between current time and last login time
    $timeDifference = time() - $lastLoginTimestamp;

    // Check if the time difference is within the online threshold with a margin of error
    // You can adjust the margin based on how accurate you want the online status to be
    $marginOfError = 30; // 30 seconds
    return $timeDifference <= ($onlineThreshold + $marginOfError);
}

    if ($session['uid'] === null) {
    $par_link = 'signup.php';
    } else {
    $par_link = 'my_videos_upload.php';
    }

    function contest_check($tag, $title, $date) {
    global $video;
    $video_tags = $video['tags'];
    global $par_link;
    if (strpos($video_tags, $tag) !== false) {
    echo '<br><table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFCC99">';
    echo '<tr>';
    echo '<td><img src="img/box_login_tl.gif" width="5" height="5"></td>';
    echo '<td><img src="img/pixel.gif" width="1" height="5"></td>';
    echo '<td><img src="img/box_login_tr.gif" width="5" height="5"></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td><img src="img/pixel.gif" width="5" height="1"></td>';
    echo '<td width="270" style="padding: 5px; text-align: center;">';
    echo '<div style="font-weight: bold; font-size: 12px;">'.$date.' Contest Entry</div>';
    echo '<div style="font-size: 14px; font-weight: bold;"><a href="monthly_contest.php">'.$title.'</a></div>';
    echo '<br>';
    echo '<div style="font-size: 12px; font-weight: normal; margin-bottom: 10px;">';
    echo '<a href="'.$par_link.'">Join!</a> &nbsp;|&nbsp;<a href="monthly_contest.php">Learn More</a>';
    echo '</div>';
    echo '</td>';
    echo '<td><img src="img/pixel.gif" width="5" height="1"></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td><img src="img/box_login_bl.gif" width="5" height="5"></td>';
    echo '<td><img src="img/pixel.gif" width="1" height="5"></td>';
    echo '<td><img src="img/box_login_br.gif" width="5" height="5"></td>';  
    echo '</tr>';
    echo '</table><br>';
    }

    }

    $related_vid_count = $conn->prepare("
    SELECT COUNT(*) 
    FROM videos 
    LEFT JOIN users ON users.uid = videos.uid 
    WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) 
    AND videos.privacy = 1 
    AND videos.converted = 1 
    AND users.termination = 0
    ");

    $related_vid_count->execute([$search, $search, $search, $search]);

    $related_vid_count = $related_vid_count->fetchColumn();

// HONORS
    $video_honors = [];
    /* Honors: Recently Featured */
    $really_featured = $conn->prepare("SELECT * FROM picks WHERE video = :video_id");
    $really_featured->execute([
	":video_id" => $_GET['v']
    ]);

    if($really_featured->rowCount() == 1) {
        $video_honors[] = ["honor" => "Recently Featured", "url" => "s=rf&page=1"];
    }

    /* Honors: Most Viewed */
    $most_viewed = $conn->query(
    "SELECT * FROM videos
	WHERE (converted = 1 AND privacy = 1)
	ORDER BY views DESC LIMIT 50"
    );

    if ($most_viewed) {
    $most_viewed = $most_viewed->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($most_viewed, 'vid'));
    unset($most_viewed);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "#" . $where_it_is . " - Most Viewed", "url" => "s=mp&t=a&page=1"];
        }
    }
    /* Honors: Most Viewed (Today) */
    $most_viewed = $conn->query(
    "SELECT * FROM videos
	WHERE (converted = 1 AND privacy = 1 AND uploaded > DATE_SUB(NOW(), INTERVAL 2 DAY))
	ORDER BY views DESC LIMIT 50"
    );

    if ($most_viewed) {
    $most_viewed = $most_viewed->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($most_viewed, 'vid'));
    unset($most_viewed);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "#" . $where_it_is . " - Most Viewed (Today)", "url" => "s=mp&t=t&page=1"];
        }
    }
    /* Honors: Most Viewed (This Week) */
    $most_viewed = $conn->query(
    "SELECT * FROM videos
	WHERE (converted = 1 AND privacy = 1 AND uploaded > DATE_SUB(NOW(), INTERVAL 2 WEEK))
	ORDER BY views DESC LIMIT 50"
    );

    if ($most_viewed) {
    $most_viewed = $most_viewed->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($most_viewed, 'vid'));
    unset($most_viewed);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "#" . $where_it_is . " - Most Viewed (This Week)", "url" => "s=mp&t=w&page=1"];
        }
    }
    /* Honors: Most Viewed (This Month) */
    $most_viewed = $conn->query(
    "SELECT * FROM videos
	WHERE (converted = 1 AND privacy = 1 AND uploaded > DATE_SUB(NOW(), INTERVAL 1 MONTH))
	ORDER BY views DESC LIMIT 50"
    );

    if ($most_viewed) {
    $most_viewed = $most_viewed->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($most_viewed, 'vid'));
    unset($most_viewed);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "#" . $where_it_is . " - Most Viewed (This Month)", "url" => "s=mp&t=m&page=1"];
        }
    }
    /* Honors: Most Discussed */
    $most_discussed = $conn->query(
    "SELECT * FROM videos
	WHERE (converted = 1 AND privacy = 1)
	ORDER BY comm_count DESC LIMIT 50"
    );

    if ($most_discussed) {
    $most_discussed = $most_discussed->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($most_discussed, 'vid'));
    unset($most_discussed);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "#" . $where_it_is . " - Most Discussed", "url" => "s=md&page=1"];
        }
    }
    
    /* Honors: Top Favorite */
    $top_favorite = $conn->query(
    "SELECT * FROM videos
			WHERE (converted = 1 AND privacy = 1)
			ORDER BY fav_count DESC LIMIT 1"
    );

    if ($top_favorite) {
    $top_favorite = $top_favorite->fetchAll(PDO::FETCH_ASSOC);
    $honor_find = array_search($video['vid'], array_column($top_favorite, 'vid'));
    unset($top_favorite);
    if ($honor_find !== false) {
        $where_it_is = $honor_find + 1;
        $video_honors[] = ["honor" => "Top Favorite", "url" => "s=mf&page=1"];
        }
    }
?>

<link rel="stylesheet" href="/viewfinder/player.css">
<iframe id="invisible" name="invisible" src="" scrolling="yes" width="0" height="0" frameborder="0" marginheight="0" marginwidth="0"></iframe>   

<script src="js/AJAX.js" language="javascript"></script>
<script src="js/ui.js" language="javascript"></script>
<script src="swfobject.js" language="javascript"></script>
<script src="js/components.js" language="javascript"></script>
<script>
function CheckLogin()
{
	
		<?php if($_SESSION['uid'] == NULL) { ?>
	window.location="signup_login.php?r=c&v=<?php echo htmlspecialchars($video['vid']); ?>&next=watch.php";
	return false;
	<?php } ?>
		
	return true;
}

function commentResponse(xmlHttpRequest)
{
	response_str = xmlHttpRequest.responseText;
	if(response_str == "ERROR") {
		alert("An error occured while posting the comment.");
	} else {
		var comment_form_id = response_str;
		var dstDiv = document.getElementById('comment_button_' + comment_form_id);
		dstDiv.value = "Thanks for the comment!";
		dstDiv.disabled = true;

		//var dstDiv = document.getElementById("container_" + comment_form_id);
		//dstDiv.innerHTML = "Thank you for the comment"
		//toggleVisibility("div_" + comment_form_id, false);
		//toggleVisibility("reply_" + comment_form_id, false);
		//dstDiv.style.display = "block";
	}
}
function commentRemoved(xmlHttpRequest)
{
	response_str = xmlHttpRequest.responseText;
	if(response_str == "ERROR") {
		alert("An error occured while removing the comment.");
	} else {
		comment_id = response_str;
		var comment_container = document.getElementById("container_comment_form_id_" + comment_id);

		var remove_btn = document.getElementById('remove_button_' + comment_id);
		remove_btn.value = "Comment Removed";
		return true;
	}
}

function showCommentReplyForm(form_id, reply_type)
{
	if (CheckLogin() == false)
		return false;
	var div_id = "div_" + form_id;
	var comment_form = document.getElementById(form_id);
	var reply_parent_id;
	if (reply_type == 'current_post')  {
		reply_parent_id = comment_form.comment_id.value;
	} else if (reply_type == 'parent_post') {
		reply_parent_id = comment_form.comment_parent_id.value;
	} else {
		// This is a main post
		reply_parent_id = "";
	}

	comment_form.reply_parent_id.value = reply_parent_id;
	toggleVisibility("div_" + form_id, true);
	toggleVisibility("reply_" + form_id, false);
}
		

function postThreadedComment(comment_form_id) 
{
	if (CheckLogin() == false)
		return false;

	var comment_form = document.getElementById(comment_form_id);
	if (ThreadedCommentHandler(comment_form)) {
		var add_btn = document.getElementById('comment_button_' + comment_form_id);
		add_btn.value = "Adding comment...";
		add_btn.disabled = true;

		var discard_btn = document.getElementById('discard_button_' + comment_form_id);
		discard_btn.disabled = true;
		discard_btn.style.display  = "none";

	} 
}


function ThreadedCommentHandler(comment_form)
{
        var comment = comment_form.comment;
        var comment_button = comment_form.comment_button;

        if (comment.value.length == 0 || comment.value == null)
        {
                alert("You must enter a comment!");
                comment.focus();
                return false;
        }

        if (comment.value.length > 500)
        {
                alert("Your comment must be shorter than 500 characters!");
                comment.focus();
                return false;
        }

        var formVars = new Array();
        for (var i = 0; i < comment_form.elements.length; i++)
        {
                var formElement = comment_form.elements[i];
                formVars[formElement.name] = formElement.value;
        }

        postUrl("comment_servlet", urlEncodeDict(formVars), true, execOnSuccess(commentResponse));

        return true;
}


function removeComment(form)
{
	if (CheckLogin() == false)
		return false;

	if (!confirm("Really remove comment?"))
		return true;

	var formVars = new Array();
	for (var i = 0; i < form.elements.length; i++)
	{
		var formElement = form.elements[i];
		formVars[formElement.name] = formElement.value;
	}

	postUrl("comment_servlet",  urlEncodeDict(formVars), true, execOnSuccess(commentRemoved));

	var remove_btn = document.getElementById('remove_button_' + form.comment_id.value);
	remove_btn.value = "Removing comment...";
	remove_btn.disabled = true
	return true;
}

function toggleVisibility(whichForm, setVisible)
{
	var newstate="none"
	if(setVisible == true) 
		newstate = ""
	
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById(whichForm).style;
		style2.display = newstate;
	}
	else if (document.all)
	{
		// this is the way old msie versions work
		var style2 = document.all[whichForm].style;
		style2.display = newstate;
	}
	else if (document.layers)
	{
		// this is the way nn4 works
		var style2 = document.layers[whichForm].style;
		style2.display = newstate;
	}
}
	

function addFavorite()
{
	getUrl("/add_favorites.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>", true, execOnSuccess(function() { alert("This video has been added to your favorites."); }));
}

function openFull()
{
  var fs = window.open( "/watch_fullscreen?video_id=<?php echo htmlspecialchars($video['vid']); ?>&l=<?php echo ceil($video['time']); ?>&fs=1&title=" + "<?php echo htmlspecialchars($video['title']); ?>" ,
           "FullScreenVideo", "toolbar=no,width=" + screen.availWidth  + ",height=" + screen.availHeight 
         + ",status=no,resizable=yes,fullscreen=yes,scrollbars=no");
  fs.focus();
}

</script>


<? if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') { ?>
<div align="center" style="padding-bottom: 10px;">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2537513323123758"
     crossorigin="anonymous"></script>
<!-- Watch Page -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-2537513323123758"
     data-ad-slot="3705019363"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>
     <? } ?>
<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="510" style="padding-right: 15px;">
				
		<div style="font-size: 16px; font-weight: bold; color: #333333; padding: 5px; border-top: 1px dotted #CCCCCC;">
		<?php echo htmlspecialchars($video['title']); ?>		</div>
		
		<div style="text-align: center; padding-bottom: 8px;">
		<div id="flashcontent">
		<? if ($_SESSION['js'] == 1) { ?>
		<!-- player HTML begins here -->
        <div class="player" id="playerBox">
            <div class="mainContainer">
                <div class="playerScreen">
                    <div class="playbackArea">
                        <div class="videoContainer">
                            <video class="videoObject" id="video">
                                <source src="https://v<?= $video['cdn'] ?>.kamtape.com/get_video.mp4?video_id=<?php echo htmlspecialchars($video['vid']); ?>&format=webm"> 
                             </video>
                        </div>
                    </div>
                  <div class="watermark">
                        <img src="/viewfinder/resource/watermark.png" height="35px">
                    </div>
                </div>
                <div class="controlBackground">
                    <div class="controlContainer">
                        <div class="lBtnContainer">
                            <div class="button" id="playButton">
                                <img src="/viewfinder/resource/play.png" id="playIcon">
                                <img src="/viewfinder/resource/pause.png" class="hidden" id="pauseIcon">
                            </div>
                        </div>
                        <div class="centerContainer">
                            <div class="seekbarElementContainer">
                                <progress class="seekProgress" id="seekProgress" value="0" min="0"></progress>
                            </div>
                            <div class="seekbarElementContainer">
                                <input class="seekHandle" id="seekHandle" value="0" min="0" step="1" type="range">
                            </div>
                        </div>
                        <div class="rBtnContainer">
                            <div class="button" id="muteButton">
                                <img src="/viewfinder/resource/mute.png" id="muteIcon">
                                <img src="/viewfinder/resource/unmute.png" class="hidden" id="unmuteIcon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aboutBox hidden" id="aboutBox">
                <div class="aboutBoxContent">
                <div class="aboutHeader">Viewfinder</div>
                <div class="aboutBody">
                    <div>Version 1.0<br>
                    <br>
                    2005-Style HTML5 player<br>
                    <br>
                    by purpleblaze<br>
                    for KamTape.com
                </div>
                </div>
                <button id="aboutCloseBtn">Close</button>
                </div>
            </div>
            <div class="contextMenu hidden" id="playerContextMenu">
                <div class="contextItem" id="contextMute">
                    <span>Mute</span>
                    <div id="muteTick" class="tick hidden">    
                    </div>
                </div>
                <div class="contextItem" id="contextLoop">
                    <span>Loop</span>
                    <div id="loopTick" class="tick hidden">
                    </div>
                </div>
                <div class="contextSeparator"></div>
                <div class="contextItem" id="contextAbout">About</div>
            </div>
        </div>
        
        <!-- here lies purple -->
        <script src="/viewfinder/player.js"></script>
		<? } else { ?>
		<div style="padding: 20px; font-size:14px; font-weight: bold;">
			Hello, you either have JavaScript turned off or an old version of Macromedia's Flash Player, <a href="/help#flash_help">click here</a> to get the latest flash player.
		</div>
		<? } ?>
		</div>
		</div>
		<? if ($_SESSION['flash'] == 1) { ?>
		<script type="text/javascript">
				swfobject.embedSWF("/player.swf?video_id=<?php echo $video['vid']; ?>&l=<?php echo ceil($video['time']); ?>&c=<?= $video['cdn'] ?><?php if($_SESSION['uid'] != NULL) { echo "&s=".session_id(); }?>", "flashcontent", "425", "350", "6");
            
            </script>
         <? } else if($_SESSION['js'] != 1 && $_SESSION['flash'] != 1) { ?>   
        <script type="text/javascript">
			if(swfobject.hasFlashPlayerVersion("6")) {	
				swfobject.embedSWF("/player.swf?video_id=<?php echo $video['vid']; ?>&l=<?php echo ceil($video['time']); ?>&c=<?= $video['cdn'] ?><?php if($_SESSION['uid'] != NULL) { echo "&s=".session_id(); }?>", "flashcontent", "425", "350", "6");
			}  
            
            </script><script type="text/javascript">
            if(typeof(document.createElement('video').canPlayType) != 'undefined' && document.createElement('video').canPlayType('video/webm;codecs="vp8,opus"') == "probably") {
				document.getElementById('flashcontent').innerHTML = `<!-- player HTML begins here -->
        <div class="player" id="playerBox">
            <div class="mainContainer">
                <div class="playerScreen">
                    <div class="playbackArea">
                        <div class="videoContainer">
                            <video class="videoObject" id="video">
                                <source src="//v<?= $video['cdn'] ?>.kamtape.com/get_video.mp4?video_id=<?php echo htmlspecialchars($video['vid']); ?>&format=webm"> 
                             </video>
                        </div>
                    </div>
                  <div class="watermark">
                        <img src="/viewfinder/resource/watermark.png" height="35px">
                    </div>
                </div>
                <div class="controlBackground">
                    <div class="controlContainer">
                        <div class="lBtnContainer">
                            <div class="button" id="playButton">
                                <img src="/viewfinder/resource/play.png" id="playIcon">
                                <img src="/viewfinder/resource/pause.png" class="hidden" id="pauseIcon">
                            </div>
                        </div>
                        <div class="centerContainer">
                            <div class="seekbarElementContainer">
                                <progress class="seekProgress" id="seekProgress" value="0" min="0"></progress>
                            </div>
                            <div class="seekbarElementContainer">
                                <input class="seekHandle" id="seekHandle" value="0" min="0" step="1" type="range">
                            </div>
                        </div>
                        <div class="rBtnContainer">
                            <div class="button" id="muteButton">
                                <img src="/viewfinder/resource/mute.png" id="muteIcon">
                                <img src="/viewfinder/resource/unmute.png" class="hidden" id="unmuteIcon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aboutBox hidden" id="aboutBox">
                <div class="aboutBoxContent">
                <div class="aboutHeader">Viewfinder</div>
                <div class="aboutBody">
                    <div>Version 1.0<br>
                    <br>
                    2005-Style HTML5 player<br>
                    <br>
                    by purpleblaze<br>
                    for KamTape.com
                </div>
                </div>
                <button id="aboutCloseBtn">Close</button>
                </div>
            </div>
            <div class="contextMenu hidden" id="playerContextMenu">
                <div class="contextItem" id="contextMute">
                    <span>Mute</span>
                    <div id="muteTick" class="tick hidden">    
                    </div>
                </div>
                <div class="contextItem" id="contextLoop">
                    <span>Loop</span>
                    <div id="loopTick" class="tick hidden">
                    </div>
                </div>
                <div class="contextSeparator"></div>
                <div class="contextItem" id="contextAbout">About</div>
            </div>
        </div>
        
        <!-- here lies purple -->
				`;
			}
			</script>
         <script src="/viewfinder/player.js"></script>
         <? } ?>
		
		
		<div style="font-size: 12px; font-weight: bold; text-align: center; padding-bottom: 10px;">
		
				<a <? if($_SESSION['uid'] == NULL) {?>href="signup.php?r=c&v=<?php echo htmlspecialchars($video['vid']); ?>"<? } else {?> href="#comment" <? } ?>>Post Comments</a>
		&nbsp;&nbsp;//&nbsp;&nbsp; <a <? if($_SESSION['uid'] == NULL) {?>href="signup.php?r=a&v=<?php echo htmlspecialchars($video['vid']); ?>"<? } else {?> href="#" onClick="return addFavorite();" <? } ?>>Add to Favorites</a>
		&nbsp;&nbsp;//&nbsp;&nbsp; <a <? if($_SESSION['uid'] == NULL) {?>href="signup.php?r=o&v=<?php echo htmlspecialchars($video['vid']); ?>"<? } else {?> href="#flag" <? } ?>>Flag This Video</a>
        <?php if ($uploader['uid'] == $session['uid']) { ?><p>Video Owner Options: <a href="/my_videos_edit.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>">Edit Your Video Here</a><? } ?>
        <?php if ($session['staff'] == 1 && $session['uid'] != $video['uid']) { ?><p>ManagerTape Options: <a style="color:#f22b33;" href="/admin/mod_video.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>">Moderate This Video</a>&nbsp;&nbsp;//&nbsp;&nbsp;<a style="color:#f22b33;" href="/admin/mod_user.php?user=<?php echo htmlspecialchars($uploader['username']); ?>">Moderate Uploader</a>
        <? } ?>
				</div>
                

		
		<table width="400" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td style="padding-bottom: 15px;">
								<? if (getRatingCount($video['vid']) > 0) { ?>
										<div style="float:left; margin-left:5em; padding-right: 18px;">
							<span>Average (<? echo htmlspecialchars(getRatingCount($video['vid'])); ?> <? echo (getRatingCount($video['vid'])  == 1) ? 'vote' : 'votes' ?>)</span><br />
                            <!-- these were called vote counts for some reason -->
								
	<nobr>
                            <? grabRatings($video['vid'], "L", 'style="border:0px; padding:0px; margin:0px; vertical-align:middle;"'); ?>
							</nobr>
		
						</div><? } ?>
                        <? $ratingMessage = "Rate this video!"; 

                        if($session['uid'] == $video['uid']) { $rating_error = "You cannot rate your own video."; }
                        if($_SESSION['uid'] == NULL) { $rating_error = "Please sign up and login to rate this video."; }
                        if($video['allow_votes'] == 0) { $rating_error = "Ratings have been disabled for this video."; }
                        if (getRatingCount($video['vid']) == 0) { $ratingMessage = "Be the first to rate this video!"; }
                       ?>
														<div id="ratingDiv" style="<? if (getRatingCount($video['vid']) == 0) { ?> text-align:center; margin-right:5em; <? } else { ?> float:right; margin-right:5em; <? } ?>">
							<span id="ratingMessage"><?= htmlspecialchars($ratingMessage) ?></span><br />
                            <?  if(isset($rating_error)) { ?>
                            <a href="<? if($_SESSION['uid'] == NULL) { ?>signup.php<? } else { ?>#<? } ?>" style="text-decoration:none;" title="<?= htmlspecialchars($rating_error) ?>">	
	<nobr>
									<img style="border:0px; padding:0px; margin:0px; vertical-align:middle;" src="img/star_bg.gif">
														<img style="border:0px; padding:0px; margin:0px; vertical-align:middle;" src="img/star_bg.gif">
														<img style="border:0px; padding:0px; margin:0px; vertical-align:middle;" src="img/star_bg.gif">
														<img style="border:0px; padding:0px; margin:0px; vertical-align:middle;" src="img/star_bg.gif">
														<img style="border:0px; padding:0px; margin:0px; vertical-align:middle;" src="img/star_bg.gif">
							</nobr>
		
</a>
<? } else { 
    $already_rated = $conn->prepare("SELECT * FROM ratings WHERE user = :uid AND video = :video_id");
    $already_rated->execute([
	":uid" => $session['uid'],
	":video_id" => htmlspecialchars($video['vid'])
    ]);
    if ($already_rated->rowCount() == 0) {
    $ur_rating = 0;    
    } else {
    $already_rated = $already_rated->fetch(PDO::FETCH_ASSOC);
    $ur_rating = $already_rated['rating'];
    }
    ?>
														<form style="display:none;" name="ratingForm" action="rating.php" method="POST">
	<input type="hidden" name="action_add_rating" value="1" />
	<input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['vid']); ?>">
	<input type="hidden" name="user_id" value="<?php echo htmlspecialchars($session['uid']); ?>">
	<input type="hidden" name="rating" id="rating" value="">
	<input type="hidden" name="size" value="L">
</form>

<script language="javascript">
	ratingComponent = new UTRating('ratingDiv', 5, 'ratingComponent', 'ratingForm', 'ratingMessage');
	ratingComponent.starCount=<?php echo htmlspecialchars($ur_rating); ?>;
			onLoadFunctionList.push(function() { ratingComponent.drawStars(<?php echo htmlspecialchars($ur_rating); ?>, true); });
            
</script>
	
<div>
		<nobr>
			<a href="#" onclick="ratingComponent.setStars(1); return false;" onmouseover="ratingComponent.showStars(1);" onmouseout="ratingComponent.clearStars();"><img src="/img/star_bg.gif" id="star_1" class="rating" style="border: 0px" ></a>
			<a href="#" onclick="ratingComponent.setStars(2); return false;" onmouseover="ratingComponent.showStars(2);" onmouseout="ratingComponent.clearStars();"><img src="/img/star_bg.gif" id="star_2" class="rating" style="border: 0px" ></a>
			<a href="#" onclick="ratingComponent.setStars(3); return false;" onmouseover="ratingComponent.showStars(3);" onmouseout="ratingComponent.clearStars();"><img src="/img/star_bg.gif" id="star_3" class="rating" style="border: 0px" ></a>
			<a href="#" onclick="ratingComponent.setStars(4); return false;" onmouseover="ratingComponent.showStars(4);" onmouseout="ratingComponent.clearStars();"><img src="/img/star_bg.gif" id="star_4" class="rating" style="border: 0px" ></a>
			<a href="#" onclick="ratingComponent.setStars(5); return false;" onmouseover="ratingComponent.showStars(5);" onmouseout="ratingComponent.clearStars();"><img src="/img/star_bg.gif" id="star_5" class="rating" style="border: 0px" ></a>
	</nobr>
									</div>
                                    <? } ?>
					<!-- <br clear="all" />
				</div> -->
						</td>
		</tr>
	</table>
			
	<table width="485" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
			<td>
	
			<div class="watchDescription">
			<?php $real_desc = nl2br(htmlspecialchars($video['description']));
            $real_desc = AutoLinkUrls($real_desc);
            echo $real_desc; ?>			</div>
			
			<div style="font-size: 11px; padding-bottom: 18px;">
			Added on <?php echo retroDate($video['uploaded'], "F j, Y, h:i a"); ?> by <a href="profile.php?user=<?php echo htmlspecialchars($uploader['username']); ?>"><?php echo htmlspecialchars($uploader['username']); ?></a>
			</div>
			
			</td>
		</tr>
	</table>
			
	<table width="485" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr valign="top">
			<td width="245" style="border-right: 1px dotted #AAAAAA; padding-right: 5px;">
			<div style="font-weight: bold; color:#003399; padding-bottom: 7px;">Video Details //</div>
			
			<div style="font-size: 11px; padding-bottom: 10px;">
			Runtime: <?php echo gmdate("i:s", $video['time']); ?> | Views: <?php echo htmlspecialchars($video['views']); ?> | <a href="#comment">Comments</a>: <?php echo number_format($video['comm_count']); ?>			</div>
			
			<div style="padding-bottom: 10px;"><span style="background-color: #FFFFAA; padding: 2px;">Tags:</span>&nbsp; <?php $tags = explode(" ", $video['tags']); $tagCount = count($tags); foreach ($tags as $index => $tag) { echo '<a      href="results.php?search=' . htmlspecialchars($tag) . '">' . htmlspecialchars($tag) . '</a>';
    if ($index < $tagCount - 1) {
        echo ', ';
    }
}
?>
			</div>

			<div style="padding-bottom: 10px;"><span style="background-color: #FFFFAA; padding: 2px;">Channels:</span>&nbsp; 
						<? showChannels($video['vid']); ?>

						</div>			
			<div style="font-size: 11px; padding-bottom: 10px;">
						</div>
			
			</td>
			<td width="240" style="padding-left: 10px;">
			<div style="font-weight: bold; font-size: 12px; color:#003399; padding-bottom: 7px;">User Details //</div>
			
						
			<div style="font-size: 11px; padding-bottom: 10px;">
			<a href="profile_videos.php?user=<?php echo htmlspecialchars($uploader['username']); ?>">Videos</a>: <?php echo $uploader['pub_vids']; ?> | <a href="profile_favorites.php?user=<?php echo htmlspecialchars($uploader['username']); ?>">Favorites</a>: <?php echo $uploader['fav_count']; ?> | Friends: <?php echo $uploader['friends_count']; ?>
					</div>
			
			<div style="padding-bottom: 5px;">
			<span style="background-color: #FFFFAA; padding: 2px;">User Name:</span>&nbsp; <a href="profile.php?user=<?php echo htmlspecialchars($uploader['username']); ?>"><?php echo htmlspecialchars($uploader['username']); ?></a>
			</div>
			
			<div style="padding-bottom: 10px;">
									<div style="padding-bottom: 10px;"><?php $isOnline = isUserOnline($uploader['last_act']);

if ($isOnline) {
    echo '<img src="/img/Little_Man.gif" width="15" height="20" align="absmiddle">&nbsp;I am online now!';
} else {
    echo "I was on the site ". timeAgo($uploader['last_act']) .".";
} ?>
							</div>
			
			<div style="font-weight: bold; padding-bottom: 10px;">
						<a href="<? if($_SESSION['uid'] == NULL) {?>signup.php?r=o&v=<?php echo htmlspecialchars($video['vid']); } else { ?>outbox.php?user=<?php echo htmlspecialchars($uploader['username']); } ?>">Send Me a Private Message!</a>
						</div>
			<div style="padding-bottom: 5px;">	
				<img src="/img/SubscribeIcon.gif" align="absmiddle">&nbsp;<a href="/subscription_center?<? if($subscribed->rowCount() > 0) { echo "remove_user"; } else { echo "add_user"; } ?>=<?php echo htmlspecialchars($uploader['username']) ?>"><? if($subscribed->rowCount() > 0) { echo "unsubscribe"; } else { echo "subscribe"; } ?></a> <? if($subscribed->rowCount() > 0) { echo "from"; } else { echo "to"; } ?> <?php echo htmlspecialchars($uploader['username']) ?>'s videos
			</div>
						

		</td>
	</tr>
</table>
<br />
		<!-- watchTable -->

		<table width="485" cellpadding="0" cellspacing="0" border="0" align="center" style="table-layout: fixed;">
          <tr>
            <td>
				<form name="linkForm" id="linkForm">
				  <table width="485"  border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
                    <tr>
                      <td width="33%">
					  <div align="left" style="font-weight: bold; font-size: 12px; color:#003399; padding-bottom: 7px;">
					  	Share Details // &nbsp;<a href="sharing.php">Help? </a>
					  </div>					  </td>
                      <td width="67%">&nbsp;</td>
                    </tr>
                    <tr>
                      <td valign="top"><span style="background-color: #FFFFAA; padding: 2px;">Video URL (Permalink):</span><font style="color: #000000;">&nbsp;&nbsp;</font> </td>
                      <td valign="top"><input name="video_link" type="text" onClick="javascript:document.linkForm.video_link.focus();document.linkForm.video_link.select();" value="http://www.kamtape.com/?v=<?php echo htmlspecialchars($video['vid']); ?>" style="width: 300px;" readonly="true" style="font-size: 10px;">
                        
                        <div style="font-size: 11px;">(E-mail or link it)<br>
                          <br>
                        </div>                      
					  </td>
                    </tr>
                    <tr>
                      <td valign="top"><span style="background-color: #FFFFAA; padding: 2px;">Embeddable Player:</span><font style="color: #000000;">&nbsp;&nbsp;</font> </td>
                      <td valign="top"><input name="video_play" type="text" onClick="javascript:document.linkForm.video_play.focus();document.linkForm.video_play.select();" value="&lt;iframe src=&quot;//www.kamtape.com/v/<?php echo htmlspecialchars($_GET['v']); ?>&quot; width=&quot;460&quot; height=&quot;357&quot; allowfullscreen scrolling=&quot;off&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;"" style="width: 300px;" readonly="true" style="font-size: 10px; text-align: center;">
                      <div style="font-size: 11px;">(Put this video on your website. Works on Friendster, eBay, Blogger, FriendProject!)<br>
                        <br>
                      </div></td>
                    </tr>
					
					
													<? if ($SITE_LIST != NULL) { ?><tr>
								<td colspan="2" valign="top">
								<span style="background-color: #FFFFAA; padding: 2px;">Sites linking to this video:</span>
									<div style="font-size: 11px; padding-bottom: 7px;"></div>
								
								<?php foreach($SITE_LIST as $frequency => $referer	) {
                                    if ($frequency < 1) {
                                    $frequency = 1;
                                    }
                                    if(empty($referer)) {
                                        echo '&#187; '.htmlspecialchars($frequency).' click from somewhere...<br>'."\r\n";
                                        } else {
					echo '&#187; <b>'.htmlspecialchars($frequency).' clicks from </b><a href="r.php?u='.htmlspecialchars($referer).'">'.htmlspecialchars($referer).'</a><br>'."\r\n"; }
				} ?>
							</td>
								</tr><? } ?>
								
              </table>
			</form>
		    </td>
          </tr>
        </table>

		<br>

		             <a name="comment"></a>
</p>
		<? if($video['comms_allow'] < 1){ ?><div style="padding-bottom: 5px; font-weight: bold; color: #444;">Comments have been disabled for this video.</div><? } else { ?><div style="padding-bottom: 5px; font-weight: bold; color: #444;">Comment on this video:</div>
				<div id='container_new_comment_thread'>
		
				<div id="div_new_comment_thread">
			<div style="padding-bottom: 5px; font-weight: bold; color: #444; display: none;">Comment on this video:</div>
				<form name="comment_form" id="new_comment_thread" method="post" action="#">
				<input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['vid']); ?>">
				<input type="hidden" name="add_comment" value="">
				<input type="hidden" name="form_id" value="new_comment_thread">
				<input type="hidden" name="comment_parent_id" value="">
				<input type="hidden" name="comment_id" value="">
				<input type="hidden" name="reply_parent_id" value="">
				<textarea tabindex="2" name="comment" cols="55" rows="3"></textarea>
				<br>

				<?php // attach video on utube has existed since july but only became visible in december
        //how? this is the only possible explanation
        // actually it probably just took links and turned them into html, but this has been here so long i'm keeping it
        if($_SESSION['uid'] != NULL) { ?>Attach a video: <select name="field_reference_video">				<option value="">- Your Videos -</option>				<?php if($videos_of_you !== false) { ?>
			<?php foreach($videos_of_you as $myvideo) { ?><option value="<?php echo htmlspecialchars($myvideo['vid']);?>"><?php echo htmlspecialchars($myvideo['title']);?></option> 				<?php } } ?><option value="">- Your Favorite Videos -</option>			<?php if($favorites_of_you !== false) { ?>
			<?php foreach($favorites_of_you as $myfavorites) { ?><option value="<?php echo htmlspecialchars($myfavorites['vid']);?>"><?php echo htmlspecialchars($myfavorites['title']);?></option> 				<?php } } ?></select><?php } ?>
				<input type="button" name="comment_button" id="comment_button_new_comment_thread" value="Post Comment" onclick="postThreadedComment('new_comment_thread');">
				<input type="button" name="discard_comment" style="display: none" id="discard_button_new_comment_thread" value="Discard" onclick="toggleVisibility('div_new_comment_thread',false); toggleVisibility('reply_new_comment_thread', true);">
				</form>
				</div>
				</div>
 			<? } ?>
				<br>

<table width="495">
<tr>
<td>
	<table class="commentsTitle" width="100%">
	<tr>
		<td>Comments (<?php echo number_format($video['comm_count']); ?>): </td>
	</table>
</td>
</tr>
<tr>
<td>
	<?php if($comments !== false) {
				foreach($comments as $comment) {
					/*$comment_videos = $conn->prepare("SELECT vid FROM videos WHERE uid = ? AND converted = 1");
					$comment_videos->execute([$comment["uid"]]);
					
					$comment_favorites = $conn->prepare("SELECT fid FROM favorites WHERE uid = ?");
					$comment_favorites->execute([$comment["uid"]]);

					$comment_friends = $GLOBALS['conn']->prepare("SELECT relationship FROM relationships WHERE (sender = ? OR respondent = ?) AND accepted = 1");
					$comment_friends->execute([$comment['uid'], $comment['uid']]);*/
                    if ($comment['vid'] == NULL) { ?>
<a name="<?php echo htmlspecialchars($comment['cid']); ?>">
					<table class="parentSection" id="comment_<?php echo htmlspecialchars($comment['cid']); ?>" width="100%" style="margin-left: 0px">
					<tbody><tr valign="top">
						<td>
						<? if ($comment['removed'] == 1) { echo '----- Comment deleted by user -----</td>'; } else { ?>
		<?= nl2br(htmlspecialsomechars($comment['body'], ['b', 'i', 'big'])) ?> 
							<div class="userStats">
								<? if($comment['termination'] != 1) {?><a href="profile?user=<?php echo htmlspecialchars($comment['username']); ?>"><?php echo htmlspecialchars($comment['username']); ?></a> // <a href="profile_videos.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Videos</a> (<?php echo $comment['pub_vids']; ?>) | <a href="profile_favorites.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Favorites</a> (<?php echo $comment['fav_count']; ?>) | <a href="profile_friends.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Friends</a> (<?php echo $comment['friends_count']; ?>)<? } ?>
								 - (<?= timeAgo($comment['post_date']); ?>)
							</div>
							
	<div class="userStats" id="container_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" style="display: none"></div>
    <div class="userStats" id="reply_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', 'current_post');">Reply to this</a>) &nbsp; 
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', 'main_thread');">Create new thread</a>) &nbsp; 
				  <?php if ($uploader['uid'] == $session['uid'] || $session['staff'] == 1 && $comment['uid'] != NULL) { ?><input type="button" name="remove_comment" id="remove_button_<?php echo htmlspecialchars($comment['cid']); ?>" value="Remove Comment" onclick="removeComment(document.getElementById('remove_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>'));"> &nbsp; 
	<form name="remove_comment_form" id="remove_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="deleter_user_id" value="<?php echo htmlspecialchars($session['uid']); ?>">
		<input type="hidden" name="remove_comment" value="">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="comment_type" value="V">
	</form>
<? } ?>
				  

	</div>

	
		<div id="div_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" style="display: none">
	<div style="padding-bottom: 5px; font-weight: bold; color: #444; display: none;">Comment on this video:</div>
	<form name="comment_form" id="comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" method="post" action="#">
		<input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['vid']); ?>">
		<input type="hidden" name="add_comment" value="">
		<input type="hidden" name="form_id" value="comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
			<input type="hidden" name="comment_parent_id" value="">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="reply_parent_id" value="">
		<textarea tabindex="2" name="comment" cols="55" rows="3"></textarea>
		<br>
		<input type="button" name="comment_button" id="comment_button_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" value="Post Comment" onclick="postThreadedComment('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>');">
		<input type="button" name="discard_comment" style="" id="discard_button_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" value="Discard" onclick="toggleVisibility('div_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>',false); toggleVisibility('reply_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', true);">
	</form>
	</div>


							</td>
					</tr>
				</tbody></table>


</a>
<? } ?>
<?php } else { ?>
<a name="<?php echo htmlspecialchars($comment['cid']); ?>">
					<table class="parentSection" id="comment_<?php echo htmlspecialchars($comment['cid']); ?>" width="100%" style="margin-left: 0px">
					<tbody><tr valign="top">
					
						<td><? if ($comment['removed'] == 1) { echo '----- Comment deleted by user -----'; } else { ?>
							<a href="watch.php?v=<?php echo htmlspecialchars($comment['vid']); ?>"><img src="/get_still.php?video_id=<?php echo htmlspecialchars($comment['vid']); ?>" class="commentsThumb" width="60" height="45"></a>
							<div class="commentSpecifics">
								<a href="watch.php?v=<?php echo htmlspecialchars($comment['vid']); ?>">Related Video</a>
							</div>
						</td>

						<td>
			<?= nl2br(htmlspecialsomechars($comment['body'], ['b', 'i', 'big'])) ?> 
							<div class="userStats">
									<? if($comment['termination'] != 1) {?><a href="profile?user=<?php echo htmlspecialchars($comment['username']); ?>"><?php echo htmlspecialchars($comment['username']); ?></a> // <a href="profile_videos.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Videos</a> (<?php echo $comment['pub_vids']; ?>) | <a href="profile_favorites.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Favorites</a> (<?php echo $comment['fav_count']; ?>) | <a href="profile_friends.php?user=<?php echo htmlspecialchars($comment['username']); ?>">Friends</a> (<?php echo $comment['friends_count']; ?>)<? } ?>
								 - (<?= timeAgo($comment['post_date']); ?>)
							</div>
							
		<div class="userStats" id="container_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" style="display: none"></div>
    <div class="userStats" id="reply_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', 'current_post');">Reply to this</a>) &nbsp; 
				  (<a href="javascript:showCommentReplyForm('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', 'main_thread');">Create new thread</a>) &nbsp; 
				  <?php if ($uploader['uid'] == $session['uid'] || $session['staff'] == 1 && $comment['uid'] != NULL) { ?><input type="button" name="remove_comment" id="remove_button_<?php echo htmlspecialchars($comment['cid']); ?>" value="Remove Comment" onclick="removeComment(document.getElementById('remove_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>'));"> &nbsp; 
	<form name="remove_comment_form" id="remove_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="deleter_user_id" value="<?php echo htmlspecialchars($session['uid']); ?>">
		<input type="hidden" name="remove_comment" value="">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="comment_type" value="V">
	</form>
	<? } ?>
<? } ?>
				  

	</div>

	
		<div id="div_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" style="display: none">
	<div style="padding-bottom: 5px; font-weight: bold; color: #444; display: none;">Comment on this video:</div>
	<form name="comment_form" id="comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" method="post" action="#">
		<input type="hidden" name="video_id" value="<?php echo htmlspecialchars($video['vid']); ?>">
		<input type="hidden" name="add_comment" value="">
		<input type="hidden" name="form_id" value="comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>">
			<input type="hidden" name="comment_parent_id" value="">
			<input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['cid']); ?>">
		<input type="hidden" name="reply_parent_id" value="">
		<textarea tabindex="2" name="comment" cols="55" rows="3"></textarea>
		<br>
		<input type="button" name="comment_button" id="comment_button_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" value="Post Comment" onclick="postThreadedComment('comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>');">
		<input type="button" name="discard_comment" style="" id="discard_button_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>" value="Discard" onclick="toggleVisibility('div_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>',false); toggleVisibility('reply_comment_form_id_<?php echo htmlspecialchars($comment['cid']); ?>', true);">
	</form>
	</div>


							</td>
					</tr>
				</tbody></table>

			</a>
	
<? } getReplies($comment['cid'], $video['vid'], $uploader['uid']); } } ?>
</td>
</tr>
</table>

		
		<a name="flag"></a>
		<table width="495" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFEEBB" style="margin-top: 10px;">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="485" style="padding: 5px 5px 10px 5px; text-align: center;">
				<div style="font-size: 14px; padding-bottom: 5px;">
				Please help keep this site <strong>FUN</strong>, <strong>CLEAN</strong>, and <strong>REAL</strong>.
				</div>
				
				<div style="font-size: 12px;">
				Flag this video:&nbsp;
				<a href="flag_video.php?v=<?php echo htmlspecialchars($video['vid']); ?>&flag=I">Inappropriate</a> &nbsp; 
				<a href="flag_video.php?v=<?php echo htmlspecialchars($video['vid']); ?>&flag=M">Miscategorized</a>
				</div>
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
		<td width="280">
		
									<div style="padding-bottom: 10px;">
						<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#EEEEEE">
							<tr>
								<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
								<td><img src="img/pixel.gif" width="1" height="5"></td>
								<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
							</tr>
							<tr>
								<td><img src="img/pixel.gif" width="5" height="1"></td>
								<td width="270" style="padding: 5px 0px 5px 0px;">
							
								<table width="270" cellpadding="0" cellspacing="0" border="0">
									<tr>
																				<td align="center"><? if ($play_schedule['before'] != null) { ?><a href="watch.php?v=<? echo htmlspecialchars($play_schedule['before']) ?>"><? } ?><img src="<? echo ($play_schedule['before'] === null) ? 'img/no_prev.gif' : '/get_still.php?video_id=' . $play_schedule['before'] ?>" width="60" height="45" style="border: 5px solid #FFFFFF;"><? if ($play_schedule['before'] != null) { ?></a><? } ?>
										<div style="font-size: 10px; font-weight: bold; padding-top: 3px;"><? if ($play_schedule['before'] != null) { ?><a href="watch.php?v=<? echo htmlspecialchars($play_schedule['before']) ?>"><? } ?>&lt; PREV<? if ($play_schedule['before'] != null) { ?></a><? } ?></div></td>
																				<td align="center"><img src="get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>" width="80" height="60" style="border: 5px solid #FFFFFF;">
										<div style="font-size: 10px; font-weight: bold; padding-top: 3px;">NOW PLAYING</div></td>
										<td align="center"><? if ($play_schedule['next'] != null) { ?><a href="watch.php?v=<? echo htmlspecialchars($play_schedule['next']) ?>"><? } ?><img src="<? if ($play_schedule['next'] === null && $play_schedule['before'] === null) {echo 'img/no_prev.gif'; } else { echo ($play_schedule['next'] === null) ? 'img/no_next.gif' : '/get_still.php?video_id=' . $play_schedule['next']; } ?>" width="60" height="45" style="border: 5px solid #FFFFFF;"><? if ($play_schedule['next'] != null) { ?></a><? } ?>
										<div style="font-size: 10px; font-weight: bold; padding-top: 3px;"><? if ($play_schedule['next'] != null) { ?><a href="watch.php?v=<? echo htmlspecialchars($play_schedule['next']) ?>"><? } ?>NEXT &gt;<? if ($play_schedule['next'] != null) { ?></a><? } ?></div></td>
									</tr>
								</table>
								
								</td>
								<td><img src="img/pixel.gif" width="5" height="1"></td>
							</tr>
							<tr>
								<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
								<td><img src="img/pixel.gif" width="1" height="5"></td>
								<td><img src="img/box_login_br.gif" width="5" height="5"></td>
							</tr>
						</table>
						</div>
							
		<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="270">
				<div class="moduleTitleBar">
				<table width="270" cellpadding="0" cellspacing="0" border="0">
					<tr valign="top">
											<td><div class="moduleFrameBarTitle">Related Videos (<? if ($related_vid_count > 10) { echo "10 of "; } else { echo htmlspecialchars($related_vid_count)." of "; } echo htmlspecialchars($related_vid_count); ?>)</div></td>
						<td align="right"><div style="font-size: 11px; margin-right: 5px;"><a href="results.php?related=<?php echo urlencode(htmlspecialchars($video['tags'])); ?>" target="_parent">See All Results</a></div></td>
										</tr>
				</table>
				</div>

				<iframe id="side_results" name="side_results" src="/api/include_results?v=<?php echo htmlspecialchars($video['vid']); ?>&search=<?php echo urlencode(htmlspecialchars($video['tags'])); ?>#selected" scrolling="auto" 
				 width="270" height="350" frameborder="0" marginheight="0" marginwidth="0">
				 [Content for browsers that don't support iframes goes here]
				</iframe>
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		
		<? if(!empty($video_honors)) { ?>
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Honors:</div><? foreach ($video_honors as $honor) {
        echo '<div style="padding: 0px 0px 5px 0px; color: #999;">» <a href="/browse.php?' . htmlspecialchars($honor["url"]) . '">' . htmlspecialchars($honor["honor"]) . '</a></div>';
        } ?>		
        <? } ?>
						
		<? contest_check("mymorning", "What's your daily routine?","August 2005"); ?>

		
		<div style="font-weight: bold; color: #333; margin: 10px 0px 5px 0px;">Related Tags:</div>
		<?php
			$related_tags = [];
			foreach($results as $result) $related_tags = array_merge($related_tags, explode(" ", $result['tags']));
			$related_tags = array_unique($related_tags);
			?>
			<?php foreach($related_tags as $tag) { ?>
			<div style="padding: 0px 0px 5px 0px; color: #999;">&#187; <a href="results.php?search=<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></a></div>
			<?php } ?>	

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