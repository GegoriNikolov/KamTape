<?php
require "needed/scripts.php";
$video = $conn->prepare("SELECT time FROM videos WHERE vid = ?");
$video->execute([$_GET['v']]);
if($video->rowCount() == 0) {
die();
} else {
$video = $video->fetch(PDO::FETCH_ASSOC);
}
if($_SESSION['uid'] != NULL) {
$sessionarg = "&s=".session_id();
} else {
$sessionarg = "";
}
if(isset($_GET['autoplay']) && $_GET['autoplay'] == 1) {
$aparg = "&autoplay=1";
} else {
$aparg = "";
}
header('Location: /p.swf?video_id='.htmlspecialchars($_GET["v"]).'&l='.$video["time"].$sessionarg.$aparg);