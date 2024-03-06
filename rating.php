<?php
require "needed/start.php";
ob_get_clean();

force_login();

// Make sure variables are set
if($_POST['action_add_rating'] != 1) {
	die();
}

// Check if the video in question exists.
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['video_id']
]);

if($video_exists->rowCount() == 0) {
	die();
} else {
$video = $video_exists->fetch(PDO::FETCH_ASSOC);
}

if($session['uid'] == $video['uid']) {
    die();
}
// Check if the user has already rated this video.
$already_rated = $conn->prepare("SELECT rating_id FROM ratings WHERE user = :uid AND video = :video_id");
$already_rated->execute([
	":uid" => $session['uid'],
	":video_id" => $_POST['video_id']
]);

if($already_rated->rowCount() > 0) {
	$already_rated = $conn->prepare("UPDATE ratings SET rating = ? WHERE user = ? AND video = ?");
    $already_rated->execute([
    $_POST['rating'],
	$session['uid'],
	$_POST['video_id']
    ]);
	?>

	

				<div class="label">Thanks for rating!</div>
		<div class="spacer"></div>
							<nobr>
			<? drawStars($_POST['rating'], $_POST['size'], 'class="rating"'); ?>
	</nobr>
		<div class="smallText"><? echo htmlspecialchars(getRatingCount($_POST['video_id'])); ?> rating<? if (htmlspecialchars(getRatingCount($_POST['video_id'])) != 1) { ?>s<? } ?></div>



			
		<div class="spacer"></div>
    <? die();
}



// Rate it!
$rate_it = $conn->prepare("INSERT INTO ratings (rating_id, rating, user, video) VALUES (:rating_id, :rating, :user, :video)");
$rate_it->execute([
	":rating_id" => generateId(),
    ":rating" => $_POST['rating'],
    ":user" => $session['uid'],
	":video" => $_POST['video_id'],
]);
?>

	

				<div class="label">Thanks for rating!</div>
		<div class="spacer"></div>
							<nobr>
			<? drawStars($_POST['rating'], $_POST['size'], 'class="rating"'); ?>
	</nobr>
		<div class="smallText"><? echo htmlspecialchars(getRatingCount($_POST['video_id'])); ?> rating<? if (htmlspecialchars(getRatingCount($_POST['video_id'])) != 1) { ?>s<? } ?></div>



			
		<div class="spacer"></div>