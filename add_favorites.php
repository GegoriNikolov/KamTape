<?php
require "needed/start.php";
ob_get_clean();

force_login();

// Check if the video in question exists.
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_GET['video_id']
]);

if($video_exists->rowCount() == 0) {
	header("Location: index.php", true, 401);
	die();
}

// Check if the user has already favorited this video.
$favorite_exists = $conn->prepare("SELECT fid FROM favorites WHERE uid = :member_id AND vid = :video_id");
$favorite_exists->execute([
	":member_id" => $session['uid'],
	":video_id" => $_GET['video_id']
]);

if($favorite_exists->rowCount() > 0) {
	die();
}
$privacsy = $conn->prepare("SELECT privacy FROM videos WHERE vid = :video_id AND converted = 1");
$privacsy->execute([
	":video_id" => $_GET['video_id']
]);
$privacsy = $privacsy->fetch(PDO::FETCH_ASSOC);
if($privacsy['privacy'] == 2) {
 die();
}
// Add it to favorites!
$add_to_favorites = $conn->prepare("INSERT INTO favorites (fid, uid, vid) VALUES (:favorite_id, :member_id, :video_id)");
$add_to_favorites->execute([
    ":favorite_id" => generateId(),
	":member_id" => $session['uid'],
	":video_id" => $_GET['video_id']
]);
$remove_fav = $conn->prepare("UPDATE videos SET fav_count = fav_count + 1 WHERE vid = ?");
$remove_fav->execute([$_GET['video_id']]);
$remove_fav = $conn->prepare("UPDATE users SET fav_count = fav_count + 1 WHERE uid = ?");
$remove_fav->execute([$session['uid']]);

?>