<?php
/*
require "needed/scripts.php";
header('Content-Type: image/jpeg');
if(!isset($_GET['still_id'])) $_GET['still_id'] = 2;
if(!isset($_GET['video_id'])) $_GET['video_id'] = false;
$video_info = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video_info->execute([$_GET['video_id']]);
if ($video_info->rowCount() == 0) {
    $getvideo = "http://v14.kamtape.com/get_still.php?video_id=" . $_GET['video_id'] . "&still_id=". $_GET['still_id'];
    header('HTTP/1.1 404 Not Found');
} else {
    $video_info = $video_info->fetch(PDO::FETCH_ASSOC);
    $video_id = $video_info['vid'];
    $getvideo = "http://v" . $video_info['cdn'] . ".kamtape.com/get_still.php?video_id=" . $video_id . "&still_id=". $_GET['still_id'];
    header('HTTP/1.1 200 OK');
    
}
$ch = curl_init();

// Set the request URL
curl_setopt($ch, CURLOPT_URL, $getvideo);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    // Handle the error
    die();
}

// Close the cURL resource
curl_close($ch);
exit();
*/
require "needed/scripts.php";
$video_info = $conn->prepare("SELECT * FROM videos WHERE vid = ? AND converted = 1");
$video_info->execute([$_GET['video_id']]);
if ($video_info->rowCount() == 0) {
    header("Content-Type: image/jpg");
	$resource = fopen(__DIR__ . "/unavail.jpg", 'rb');
	fpassthru($resource);
    header('HTTP/1.1 404 Not Found');
    die();
} else {
    $video_info = $video_info->fetch(PDO::FETCH_ASSOC);
    $video_id = $video_info['vid'];
    $getvideo = "http://v" . $video_info['cdn'] . ".kamtape.com/get_still.php?video_id=" . $video_id . "&still_id=". $_GET['still_id'];
    header('HTTP/1.1 200 OK');
    
}
if(!isset($_GET['still_id'])) $_GET['still_id'] = 2;
if(isset($_GET['still_id']) && $_GET['still_id'] == 0) $_GET['still_id'] = 2; // todo: should we find a way to determine which default thumbnail the user picked when we get to the later years?
if(!isset($_GET['video_id'])) $_GET['video_id'] = false;
if($video_info['cdn'] == 14) {
$still_file = __DIR__ . "/../v14.kamtape.com/data/thmbs/".$_GET['video_id']."_".$_GET['still_id'].".jpg";

if(isset($_GET['video_id']) && file_exists($still_file)) {
	header("Content-Type: ".mime_content_type($still_file));
	$resource = fopen($still_file, "rb");
	fpassthru($resource);
} else {
	header("Content-Type: image/jpg");
	$resource = fopen(__DIR__ . "/unavail.jpg", 'rb');
	fpassthru($resource);
	header('HTTP/1.1 404 Not Found');
}
} else {
    $ch = curl_init();

// Set the request URL
curl_setopt($ch, CURLOPT_URL, $getvideo);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    // Handle the error
    die();
}

// Close the cURL resource
curl_close($ch);
exit();
}
?>


