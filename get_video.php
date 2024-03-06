<?php
if(empty($_GET['video_id'])) { die();}
require "needed/scripts.php";
ini_set('display_errors', 1); ini_set('display_startup_errors', 1);
$video_info = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video_info->execute([$_GET['video_id']]);
if($video_info->rowCount() === 0) {
    die();
} else {
    $video_info = $video_info->fetch(PDO::FETCH_ASSOC);
    $video_id = $video_info['vid'];
    if (isset($_GET['format']) && $_GET['format'] == "webm") {
        $getvideo = "http://v" . $video_info['cdn'] . ".kamtape.com/get_video.mp4?video_id=" . $video_id . "&format=webm";
    } else {
        header("Content-Type: video/x-flv");
        $getvideo = "http://v" . $video_info['cdn'] . ".kamtape.com/get_video.flv?video_id=" . $video_id . "&format=flv";
    }
    header('Location: ' . $getvideo);
    //header('HTTP/1.1 200 OK');
    //header('HTTP/1.1 302 Found');
    header('HTTP/1.1 303 See Other');
}
?>


