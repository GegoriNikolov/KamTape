<?php 
require "needed/scripts.php";

$_CDNS = [
'14'
];
$video_CDN = array_rand($_CDNS);
$video_CDN = $_CDNS[$video_CDN];

use SoftCreatR\MimeDetector\MimeDetector;
use SoftCreatR\MimeDetector\MimeDetectorException;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['fileToUpload'])) {
    $maxFileSize = 100 * 1024 * 1024;
    $fileSize = $_FILES['fileToUpload']['size'];
    
    if ($fileSize > $maxFileSize) {
         header("Location: my_videos_upload.php");
         exit;
    }
// create an instance of the MimeDetector
$mimeDetector = new MimeDetector();

// set our file to read
try {
    $mimeDetector->setFile($_FILES['fileToUpload']['tmp_name']);
} catch (MimeDetectorException $e) {
    header("Location: my_videos_upload.php");
         exit;
}

// try to determine its mime type and the correct file extension
$type = $mimeDetector->getFileType();

$mime = strtolower($type['mime']);
$isVideo = 0;

// Check if the MIME type matches the most popular video formats
if (strpos($mime, 'video/') === 0) {
    $ok = 1;
    if ($ok == 1) {
        $cooldown = $conn->prepare(
			"SELECT * FROM videos
			WHERE uid = ? AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 1 DAY)
			ORDER BY uploaded DESC"
		);
        $cooldown->execute([$session['uid']]);
        // blazing
        // 1 = Weak
        // 2 = Moderate
        // 3 = Strict
        // >4 = stop spamming up the site dumbass

		if ($session['blazing'] > 0) {
        if ($session['blazing'] == 1) { $coolit = 8; }
        elseif ($session['blazing'] == 2) { $coolit = 5; }
        elseif ($session['blazing'] == 3) { $coolit = 3; }
        elseif ($session['blazing'] > 4) { $coolit = 1; }
        } else { $coolit = 50; }
        // I redesigned this in a way that makes it seem infinite to the average user
		if($cooldown->rowCount() > $coolit || $cooldown->rowCount() == $coolit) {
			session_error_index("You have uploaded one too many videos today! Check back tomorrow.", "error");
		}
        $my_videos = $conn->prepare(
			"SELECT * FROM videos
			WHERE uid = ?
			ORDER BY uploaded DESC"
		);
        $my_videos->execute([$session['uid']]);
// the user's first video's id *wasn't* their user id
		//if($my_videos->rowCount() > 0) {
			$video_id = generateId();
		//} else {
            //$video_id = $session['uid'];
        //}
        $field_upload_tags = trim($_POST['field_upload_tags']);
        $field_upload_tags = str_replace(',', '', $field_upload_tags); // Remove commas
        $field_upload_tags = str_replace('  ', ' ', $field_upload_tags); // Remove whitespaces
        $field_upload_tags = str_replace('#', '', $field_upload_tags); // Remove hashtags
        if(empty($field_upload_tags)) {
            session_error_index("Enter some tags for your video.", "error");
        }
         if ($_POST['field_upload_country'] == '---') {
             $_POST['field_upload_country'] = NULL;
         }
         if ($_POST['private'] == 2) {
            $remove_fav = $conn->prepare("UPDATE users SET priv_vids = priv_vids + 1 WHERE uid = ?");
            $remove_fav->execute([$session['uid']]); 
            $privacy = 2;
         } else {
            $remove_fav = $conn->prepare("UPDATE users SET pub_vids = pub_vids + 1 WHERE uid = ?");
            $remove_fav->execute([$session['uid']]);
            $privacy = 1;
         }
		$stmt = $conn->prepare("INSERT INTO videos (uid, vid, tags, title, ch1, ch2, ch3, description, file, privacy, cdn, recorddate, address, addrcountry) VALUES (:uid, :vid, :tags, :title, :ch1, :ch2, :ch3, :description, :file, :privacy, :cdn, :recorddate, :address, :country)");
$stmt->execute([
    ':uid' => $session['uid'],
    ':vid' => $video_id,
    ':tags' => $field_upload_tags,
    ':title' => $_POST['field_upload_title'],
    ':description' => $_POST['field_upload_description'],
    ':ch1' => isset($_POST['field_upload_ch0']) ? $_POST['field_upload_ch0'] : 5,
    ':ch2' => isset($_POST['field_upload_ch1']) ? $_POST['field_upload_ch1'] : null,
    ':ch3' => isset($_POST['field_upload_ch2']) ? $_POST['field_upload_ch2'] : null,
    ':file' => strip_tags($_FILES['fileToUpload']['name']),
    ':privacy' => $privacy,
    ':cdn' => $video_CDN,
    ':recorddate' => null,
    ':address' => null,
    ':country' => null
]);

    // Create a new cURL resource
    $ch = curl_init();

    // Set the request URL
    // Determine the request URL based on the protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $endpoint = 'v'.$video_CDN.'.kamtape.com/my_videos_upload_post.php';
    $url = $protocol . $endpoint;

    // Set the request URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, true);

  // Set the request payload
    $file = new \CURLFile($_FILES['fileToUpload']['tmp_name'], $_FILES['fileToUpload']['type'], $_FILES['fileToUpload']['name']);

$postData = array_merge($_POST, array(
    'fileToUpload' => $file,
    'video_id' => $video_id
));

curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
    $error = curl_error($ch);
    // Handle the error
    header("Location: my_videos_upload.php");
    }

    // Close the cURL resource
    curl_close($ch);

    $successful = "/my_videos_upload_complete.php?v=" . $video_id;

    header("Location: $successful");
    exit();

    }
}
} else {
session_error_index("Invalid file format.", "error");
}


?>