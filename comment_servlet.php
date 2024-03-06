<?php
require "needed/start.php";
ob_get_clean();
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
if(empty($_POST['reply_parent_id'])) {
ob_get_clean();
// Make sure the user is logged in.
if($_SESSION['uid'] == NULL) {
	echo "ERROR";
	die();
}
if($_SESSION['em_confirmation'] == 'false') { die("ERROR"); }

// Make sure variables are set
if(!isset($_POST['video_id']) || !isset($_POST['comment'])) {
	die("ERROR");
}

// Check if the video in question exists.
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['video_id']
]);

if($video_exists->rowCount() == 0) {
	die("ERROR");
}

// Check if the video in question exists.
if(!empty($_POST['field_reference_video'])) {
    
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['field_reference_video']
]);

if($video_exists->rowCount() == 0) {
	die("ERROR");
}
}
// Check if the user has already commented on this video within the past 5 minutes.
$comment_exists = $conn->prepare("SELECT cid FROM comments WHERE uid = :uid AND vidon = :video_id AND post_date > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$comment_exists->execute([
	":uid" => $session['uid'],
	":video_id" => $_POST['video_id']
]);

if($comment_exists->rowCount() > 4) {
	die("ERROR");
}
$comments_disabled = $conn->prepare("SELECT * FROM videos WHERE vid = :video_id AND converted = 1");
$comments_disabled->execute([
	":video_id" => $_POST['video_id']
]);

if($comments_disabled->rowCount() == 0) {
	die("ERROR");
} else {
	$comments_disabled = $comments_disabled ->fetch(PDO::FETCH_ASSOC);
}
$author = $comments_disabled['uid'];
$comments_disabled = $comments_disabled['comms_allow'];

if($comments_disabled < 1 && $session['uid'] != $author) { die("ERROR"); }
// Post that comment!
$post_comment = $conn->prepare("INSERT INTO comments (cid, vidon, vid, uid, body) VALUES (:comment_id, :video_id, :referenced, :uid, :body)");
$post_comment->execute([
	":comment_id" => generateId(),
	":video_id" => $_POST['video_id'],
    ":referenced" => $_POST['field_reference_video'],
	":uid" => $session['uid'],
	":body" => trim($_POST['comment'])
]);
$add_cnt = $conn->prepare("UPDATE videos SET comm_count = comm_count + 1 WHERE vid = ?");
$add_cnt->execute([$_POST['video_id']]);

echo htmlspecialchars($_POST["form_id"]);
} elseif(!empty($_POST['reply_parent_id'])) {
ob_get_clean();
// Make sure the user is logged in.
if($_SESSION['uid'] == NULL) {
	echo "ERROR";
	die();
}
// Make sure variables are set
if(!isset($_POST['video_id']) || !isset($_POST['comment'])) {
	die("ERROR");
}

// Check if the video in question exists.
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['video_id']
]);

if($video_exists->rowCount() == 0) {
	die("ERROR");
}

// Check if the video in question exists.
if(!empty($_POST['field_reference_video'])) {
    
$video_exists = $conn->prepare("SELECT vid FROM videos WHERE vid = :video_id AND converted = 1");
$video_exists->execute([
	":video_id" => $_POST['field_reference_video']
]);

if($video_exists->rowCount() == 0) {
	die("ERROR");
}
}
// Check if the comment in question exists.
$parentcomment_exists = $conn->prepare("SELECT cid FROM comments WHERE cid = :comment_id");
$parentcomment_exists->execute([
	":comment_id" => $_POST['reply_parent_id']
]);

if($parentcomment_exists->rowCount() == 0) {
	die("ERROR");
}

// Check if the comment in question is a master comment.
$master_comment = $conn->prepare("SELECT master_comment FROM comments WHERE cid = :comment_id");
$master_comment->execute([
	":comment_id" => $_POST['reply_parent_id']
]);
$master_comment = $master_comment->fetchColumn();

if(empty($master_comment)) {
	$master_comment = $_POST['reply_parent_id'];
}

// Check if the user has already commented on this video within the past 5 minutes.
$comment_exists = $conn->prepare("SELECT cid FROM comments WHERE uid = :uid AND vidon = :video_id AND post_date > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$comment_exists->execute([
	":uid" => $session['uid'],
	":video_id" => $_POST['video_id']
]);

if($comment_exists->rowCount() > 4) {
	die("ERROR");
}
$comments_disabled = $conn->prepare("SELECT * FROM videos WHERE vid = :video_id AND converted = 1");
$comments_disabled->execute([
	":video_id" => $_POST['video_id']
]);

if($comments_disabled->rowCount() == 0) {
	die("ERROR");
} else {
	$comments_disabled = $comments_disabled ->fetch(PDO::FETCH_ASSOC);
}
$author = $comments_disabled['uid'];
$comments_disabled = $comments_disabled['comms_allow'];

if($comments_disabled < 1 && $session['uid'] != $author) { die("ERROR"); }
// Post that comment!
$post_comment = $conn->prepare("INSERT INTO comments (cid, vidon, vid, uid, body, is_reply, reply_to, master_comment) VALUES (:comment_id, :video_id, :referenced, :uid, :body, :is_reply, :reply_to, :master_comment)");
$post_comment->execute([
	":comment_id" => generateId(),
	":video_id" => $_POST['video_id'],
    ":referenced" => $_POST['field_reference_video'],
	":uid" => $session['uid'],
	":body" => trim($_POST['comment']),
	":is_reply" => 1,
	":reply_to" => $_POST['reply_parent_id'],
	":master_comment" => $master_comment
]);

$master_author = $conn->prepare("SELECT uid FROM comments WHERE cid = :cid");
$master_author->execute([
	":cid" => $_POST['reply_parent_id']
]);
$master_author = $master_author->fetch(PDO::FETCH_ASSOC);
$pmid = generateId();
$sssdsd = $conn->prepare("INSERT INTO messages (pmid, subject, sender, receiver, body) VALUES (:pmid, :subject, :sender, :receiver, :body)");
$sssdsd->execute([
	":pmid" => trim($pmid),
	":subject" => encrypt('I have replied to your comment on a video'),
	":sender" => $session['uid'],
    ":receiver" => $master_author['uid'],
	":body" => encrypt('Check it out! https://www.kamtape.com/?v='.$_POST['video_id'])
]);

echo htmlspecialchars($_POST["form_id"]);
} else {
die("ERROR");
}
} elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_comment'])) {
$comment = $conn->prepare("SELECT * FROM comments WHERE cid = ?");
$comment->execute([$_POST['comment_id']]);
$comment = $comment->fetch(PDO::FETCH_ASSOC);

$video = $conn->prepare("SELECT * FROM videos WHERE vid = ?");
$video->execute([$comment['vidon']]);
$video = $video->fetch(PDO::FETCH_ASSOC);

$uploader = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$uploader->execute([$video['uid']]);
$uploader = $uploader->fetch(PDO::FETCH_ASSOC);
// if ($uploader['uid'] == $session['uid'] || $comment['uid'] == $session['uid'] || $session['staff'] == 1 && $comment['uid'] != NULL) {
if ($uploader['uid'] == $session['uid'] || $session['staff'] == 1 && $comment['uid'] != NULL) {
    $remove_video = $conn->prepare("UPDATE comments SET removed = 1 WHERE cid = :cid");
    $remove_video->execute([
        ":cid" => $_POST['comment_id']
    ]);
    $add_cnt = $conn->prepare("UPDATE videos SET comm_count = comm_count - 1 WHERE vid = ?");
    $add_cnt->execute([$_POST['video_id']]);
    echo htmlspecialchars($_POST['comment_id']);
} else {
die("ERROR");
}
} else {
die("ERROR");
}
?>