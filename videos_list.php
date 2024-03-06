<?php
require "needed/scripts.php";
if (!empty($_GET['tag'])) {
    $search = str_replace(" ", "|", $_GET['tag']);
    $search = preg_quote($search); // Escape special characters for regular expression
    $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0  ORDER BY (INSTR(LOWER(description), LOWER(?)) > 0) DESC");
    $videos->execute([$search, $search, $search, $search, $search]);
    $rlcount = $videos->rowCount();
    $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0  ORDER BY (INSTR(LOWER(description), LOWER(?)) > 0) DESC LIMIT 50");
    $videos->execute([$search, $search, $search, $search, $search]);
} else if (!empty($_GET['user'])) {
    $search = preg_quote($_GET['user']); // Escape special characters for regular expression
     $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 ORDER BY uploaded DESC");
     $rlcount = $videos->rowCount();
     $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 ORDER BY uploaded DESC LIMIT 50");
    $videos->execute([$search]);
} else if (!empty($_GET['id'])) {
  $singular = 'true';
  $video = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE vid = ? AND converted = 1 AND privacy = 1");
  $video->execute([$_GET['id']]);

if($video->rowCount() == 0) {
	die("No data found.");
} else {
	$video = $video->fetch(PDO::FETCH_ASSOC);
}
}
if ($search == NULL && $singular != 'true') {
    error();
}
if (!empty($_GET['tag'])) {
    $see_moar = "http://www.kamtape.com/results.php?search=".$_GET['tag'];
} else if (!empty($_GET['user'])) {
  $see_moar = "http://www.kamtape.com/profile_videos?user=".$_GET['user'];  
}
if($singular != 'true') {
$video_howmuch = $videos->rowCount();
if ($video_howmuch == 0) { $video_howmuch = "No"; }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>KamTape - Your Old School Video Repository</title>
<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color:#DDDDDD">
<div style="font-weight: bold; padding: 3px; margin-left: 5px; margin-right: 5px; border-bottom: 1px dashed #999;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td> <?php if ($singular != 'true') { echo 'My Videos'; }else{ echo "KamTape";} ?> // <span style="text-transform: capitalize;"><?php echo !empty($_GET['tag']) ? htmlspecialchars($_GET['tag']) : (!empty($_GET['user']) ? htmlspecialchars($_GET['user']) : (!empty($_GET['id']) ? htmlspecialchars($video['title']) : "")); ?>
</span><?php if ($singular != 'true' && $rlcount < 50) { echo ' // (' . $video_howmuch . ' ' . ($videos->rowCount() === 1 ? 'video' : 'videos') . ')'; } ?><?php if ($singular != 'true' && $rlcount > 50) { ?> // (over <?= $rlcount ?> videos, <a href="<?php echo htmlspecialchars($see_moar); ?>">see more</a>)<? } ?></td>
		<td align="right"><div style="vertical-align: text-bottom; text-align: right; padding: 2px;">
		<a href="http://www.kamtape.com" target="_blank"><img src="img/logo_sm.gif" width="38" height="15" border="0"></a>
	</tr>
</table>
</div>
<?php if ($singular != 'true') { 
    foreach($videos as $video) {
    ; ?>
<div class="moduleEntry">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td><a href="watch.php?v=<?php echo htmlspecialchars($video['vid']); ?>" class="bold" target="_parent"><img src="/get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>" class="moduleEntryThumb" width="80" height="60"></a></td>
		<td width="100%"><div class="moduleFrameTitle"><a href="watch.php?v=<?php echo htmlspecialchars($video['vid']); ?>" target="_parent"><?php echo htmlspecialchars($video['title']); ?></a></div>
		<div class="moduleFrameDetails">Added: <?php echo retroDate($video['uploaded']); ?>		<br>by <a href="/profile.php?user=<?php echo htmlspecialchars($video['username']); ?>" target="_parent"><?php echo htmlspecialchars($video['username']); ?></a></div>
		<div class="moduleFrameDetails">Views: <?php echo $video['views']; ?> <br> Comments: 0</div>
		</td>    
	</tr>
</table>
</div>
 <?php } ?>
        <?php } else { ?>
<div class="moduleEntry">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td><a href="watch.php?v=<?php echo htmlspecialchars($video['vid']); ?>" class="bold" target="_parent"><img src="/get_still.php?video_id=<?php echo htmlspecialchars($video['vid']); ?>" class="moduleEntryThumb" width="80" height="60"></a></td>
		<td width="100%"><div class="moduleFrameTitle"><a href="watch.php?v=<?php echo htmlspecialchars($video['vid']); ?>" target="_parent"><?php echo htmlspecialchars($video['title']); ?></a></div>
		<div class="moduleFrameDetails">Added: <?php echo retroDate($video['uploaded']); ?>		<br>by <a href="/profile?user=<?php echo htmlspecialchars($video['username']); ?>" target="_parent"><?php echo htmlspecialchars($video['username']); ?></a></div>
		<div class="moduleFrameDetails">Views: <?php echo $video['views']; ?> <br> Comments: 0</div>
		</td>    
	</tr>
</table>
</div>
 <?php } ?>

</body>

