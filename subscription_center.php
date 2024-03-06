<?php
require "needed/start.php";
force_login();
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

$sub_check = array_slice($sub_check, 0, 70);
// What The Fuck
if(isset($_GET['add_user']) && isset($_GET['remove_user'])) {
alert("What the fuck?", "error");
$d0_not_ececute = 1;
}
// Ass User Upload Subscription
if(isset($_GET['add_user']) && $d0_not_ececute !== 1) {
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['add_user']]);

if($profile->rowCount() == 0) {
	alert("Channel doesn't exist!", "error");
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
	// Check if the user has already subscribed to this channel.
    $subscribed = $conn->prepare("SELECT subscription_id FROM subscriptions WHERE subscriber = ? AND subscribed_to = ? AND subscribed_type = 'user_uploads' ");
    $subscribed->execute([
	$session['uid'],
	$profile['uid']
    ]);
    
    // Deny subscription if already added
    if($subscribed->rowCount() > 0) {
	    alert("You're already subscribed to this channel!", "error");
	    $already_subscribed = 1;
    }
    if($profile['uid'] == $session['uid']) {
        alert("You cannot subscribe to yourself!", "error");
	    $already_subscribed = 1;
    }
    if ($already_subscribed != 1) {
    // Now, subscribe.
    $subscription_add = $conn->prepare("INSERT INTO subscriptions (subscription_id, subscriber, subscribed_to, subscribed_type) VALUES (?, ?, ?, 'user_uploads')");
    $subscription_add->execute([
        generateId(),
	    $session['uid'],
	    $profile['uid']
    ]);
    $add_subscribers_cnt = $conn->prepare("UPDATE users SET subscribers = subscribers + 1 WHERE uid = ?");
	$add_subscribers_cnt->execute([$profile['uid']]);
	
	$add_subscriptions_cnt = $conn->prepare("UPDATE users SET subscriptions = subscriptions + 1 WHERE uid = ?");
	$add_subscriptions_cnt->execute([$session['uid']]);
	
     alert("Your subscription to ".$profile['username']." has been added.", "success");
    }
}
}

// Remove User Upload Subscription
if(isset($_GET['remove_user']) && $d0_not_ececute !== 1) {
$profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
$profile->execute([$_GET['remove_user']]);

if($profile->rowCount() == 0) {
	alert("Channel doesn't exist!", "error");
} else {
	$profile = $profile->fetch(PDO::FETCH_ASSOC);
	// Check if the user has already subscribed to this channel.
    $subscribed = $conn->prepare("SELECT subscription_id FROM subscriptions WHERE subscriber = ? AND subscribed_to = ? AND subscribed_type = 'user_uploads' ");
    $subscribed->execute([
	$session['uid'],
	$profile['uid']
    ]);
    
    // Deny subscription if already added
    if($subscribed->rowCount() < 1) {
	    alert("You're not subscribed to this channel!", "error");
	    $not_subscribed = 1;
    } else {
    $subscribed = $subscribed->fetch(PDO::FETCH_ASSOC);
    }
    
    if($profile['uid'] == $session['uid']) {
        alert("You cannot subscribe to yourself!", "error");
	    $not_subscribed = 1;
    }
    
    if ($not_subscribed != 1) {
    // Now, unsubscribe.
    $subscription_add = $conn->prepare("DELETE FROM subscriptions WHERE subscription_id = ?");
    $subscription_add->execute([
        $subscribed['subscription_id']
    ]);
    $add_subscribers_cnt = $conn->prepare("UPDATE users SET subscribers = subscribers - 1 WHERE uid = ?");
	$add_subscribers_cnt->execute([$profile['uid']]);
	
	$add_subscriptions_cnt = $conn->prepare("UPDATE users SET subscriptions = subscriptions - 1 WHERE uid = ?");
	$add_subscriptions_cnt->execute([$session['uid']]);
	
     alert("Your subscription to ".$profile['username']." has been removed.", "success");
    }
}
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
							Active Subscriptions						</div>
					</td>
		
				</tr>
			</tbody></table>
		</div>
				
		<div class="moduleFeatured"> 
			<table width="770" cellpadding="0" cellspacing="0" border="0">
				<tbody><?php $i = 0;
        foreach($sub_check as $video) { ?>
				<?php			
                $i = $i + 1;
						if($i == 1) {
							echo '<tr valign="top">';
						}
				?><td width="20%" align="center"><a href="watch.php?v=<?php echo $video['vid']; ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>" width="120" height="90" class="moduleFeaturedThumb"></a><div class="moduleFeaturedTitle"><a href="watch.php?v=<?php echo $video['vid']; ?>" <? if (strlen($video['title']) > 29) { ?> title="<?= htmlspecialchars($video['title']) ?>" <? } ?>><?php echo shorten($video['title'], 29); ?></a></div><div class="moduleFeaturedDetails">Added: <?php echo timeAgo($video['uploaded']); ?><br>by <a href="profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo shorten($video['username'], 13); ?></a></div><div class="moduleFeaturedDetails" style="padding-bottom: 5px;">Runtime: <?php echo gmdate("i:s", $video['time']); ?><br>Views: <?php echo number_format($video['views']); ?> | Comments: <?php echo number_format($video['comm_count']); ?></div><? grabRatingsPage($video['vid'], "SM", 0); ?></td><? if($i == 5) { echo '</tr>'; $i = 0; } } ?>
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