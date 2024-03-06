<?php 
require "needed/start.php";
ob_clean();
if(!isset($_GET['video_id'])) {
die();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? if(isset($_GET['title'])){ ?><?= htmlspecialchars($_GET['title']) ?><? } else { ?>KamTape - Televise Yourself.<? } ?></title>
<style>#flashcontent {position:absolute; left:0; top:0;right:0;bottom:0}</style>
</head>

<script type="text/javascript" src="swfobject.js"></script>
<script>
function closeFull()
{
	window.close();
}

function fillFull()
{
	window.moveTo(0,0);
	window.resizeTo(screen.availWidth, screen.availHeight)
}
</script>


<body onload="fillFull();">
	<div id="flashcontent" style="position:absolute; left:0; top:0;right:0;bottom:0">
					Hello, you either have JavaScript turned off or an old version of Macromedia's Flash Player, <a href="http://www.macromedia.com/go/getflashplayer/">click here</a> to get the latest flash player.
	</div>
		
	<script type="text/javascript">
		if(swfobject.hasFlashPlayerVersion("7")) {
		swfobject.embedSWF("/player.swf<?php if($_SESSION['uid'] != NULL) { echo "?s=".session_id()."&"; } else { echo "?"; } ?>video_id=<?= htmlspecialchars($_GET['video_id']) ?><? if(isset($_GET['l'])){ ?>&l=<?= htmlspecialchars($_GET['l']) ?><? } ?>&fs=1<? if(isset($_GET['title'])){ ?>&title=<?= urlencode($_GET['title']) ?><? } ?>", "flashcontent", "100%", "100%", 7);
		}
	</script>
</body>
