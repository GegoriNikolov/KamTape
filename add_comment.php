<?php
require "needed/start.php";
ob_get_clean();

// Make sure the user is logged in.
force_login();
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die();
}
// Make sure variables are set
if(!isset($_POST['video_id']) || !isset($_POST['comment'])) {
	die();
}

// Check if the video in question exists.
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['video_id']
]);

if($video_exists->rowCount() == 0) {
	die();
}

// Check if the video in question exists.
if(!empty($_POST['field_reference_video'])) {
    
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['field_reference_video']
]);

if($video_exists->rowCount() == 0) {
	die();
}
}
// Check if the user has already commented on this video within the past 5 minutes.
$comment_exists = $conn->prepare("SELECT cid FROM comments WHERE uid = :uid AND vid = :video_id AND post_date > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$comment_exists->execute([
	":uid" => $session['uid'],
	":video_id" => $_POST['video_id']
]);

if($comment_exists->rowCount() > 4) {
	die();
}
$comments_disabled = $conn->prepare("SELECT * FROM videos WHERE vid = :video_id AND converted = 1");
$comments_disabled->execute([
	":video_id" => $_POST['video_id']
]);

if($comments_disabled->rowCount() == 0) {
	die();
} else {
	$comments_disabled = $comments_disabled ->fetch(PDO::FETCH_ASSOC);
}
$author = $comments_disabled['uid'];
$comments_disabled = $comments_disabled['comms_allow'];

if($comments_disabled < 1 && $session['uid'] != $author) { die(); }
// Post that comment!
$post_comment = $conn->prepare("INSERT INTO comments (cid, vidon, vid, uid, body) VALUES (:comment_id, :video_id, :referenced, :uid, :body)");
$post_comment->execute([
	":comment_id" => generateId(),
	":video_id" => $_POST['video_id'],
    ":referenced" => $_POST['field_reference_video'],
	":uid" => $session['uid'],
	":body" => trim($_POST['comment'])
]);
?>