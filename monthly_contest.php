<?php 
if(isset($_GET['v'])) {
	header("Location: watch.php?v=".$_GET['v'], true, 303);
	die();
}
require "needed/start.php";

$tags_strings = $conn->query("SELECT tags FROM videos WHERE converted = 1 AND privacy = 1 ORDER BY uploaded DESC LIMIT 30");
$tag_list = [];
foreach($tags_strings as $result) $tag_list = array_merge($tag_list, explode(" ", $result['tags']));
$tag_list = array_slice(array_count_values($tag_list), 0, 50);
$featured = $conn->query("SELECT * FROM picks LEFT JOIN videos ON videos.vid = picks.video LEFT JOIN users ON users.uid = videos.uid WHERE videos.converted = 1 AND videos.privacy = 1 ORDER BY picks.featured DESC LIMIT 10");

if ($_SESSION['uid'] != NULL) {
// ahhh!!!! logged in section
$y_views = $conn->prepare("SELECT COUNT(view_id) FROM views WHERE uid = ?");
$y_views->execute([$session['uid']]);
$y_views = $y_views->fetchColumn();
// PHP UP YOURS
$vids = $conn->prepare(
	"SELECT * FROM videos
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1
	ORDER BY videos.uploaded DESC"
);
$vids->execute([$session['uid']]);

$fans = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1
	ORDER BY favorites.fid DESC"
);
$fans->execute([$session['uid']]);

$p_views = $conn->prepare(
	"SELECT * FROM views
	LEFT JOIN videos ON views.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE videos.uid = ? AND videos.converted = 1
	ORDER BY views.view_id DESC"
);
$p_views->execute([$session['uid']]);


$favs = $conn->prepare(
	"SELECT * FROM favorites
	LEFT JOIN videos ON favorites.vid = videos.vid
	LEFT JOIN users ON users.uid = videos.uid
	WHERE favorites.uid = ? AND videos.converted = 1
	ORDER BY favorites.fid DESC"
);
$favs->execute([$session['uid']]);
// endingggg
}
?>
<div style="padding: 0px 5px 0px 5px;">


<div class="tableSubTitle">Monthly Contest</div>

<table width="790" align="center" cellpadding="0" cellspacing="0" border="0">
	<tbody><tr valign="top">
		<td style="padding-right: 15px;" width="510">
		
		<span class="highlight">What is it?</span>
		<br><br>KamTape is proud to present our first monthly video contest. Each month, users may submit videos with the contest tag of the month. Viewers may browse the contest entries by searching for the contest tag. At the end of the month, the KamTape staff will review all the submitted videos and choose the winner.
		
		<br><br><span class="highlight">This Month (August 2005)</span>
		<br><br>This month's contest is about how daily routines. As long as it has something to do with that, it doesn't matter. Comedy animation about daily routines, your actual daily routine, they'll all work. The tag of the month is <b>mymorning</b>. You can subscribe to an RSS feed for video submissions via this <a href="rss/tag/mymorning.rss">link</a>.
		
		<br><br><span class="highlight">How do I enter?</span>
		<br><br>To enter, simply record yourself dancing at a unique location and tag the video with <a href="results.php?search=mymorning">mymorning</a>.
		
		<br><br><span class="highlight">Who wins?</span>
		<br><br>The winner will be picked at the end of the month by the KamTape staff.

		<br><br><span class="highlight">The Prize?</span>
		<br><br>Fame, fortune, and the envy of all eyes.
		
		</td>
		<td width="280">
		
		<table width="280" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#f9baff">
			<tbody><tr>
				<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
				<td width="270" style="padding: 5px; text-align: center;">
				<div style="margin-bottom: 10px; font-weight: bold; font-size: 13px;">Monthly Video Contest Schedule</div>
				
				August: <a href="results.php?search=mymorning">mymorning</a>
                <br>September 2005: To Be Announced
				<br>October 2005: To Be Announced
				<br>November 2005: To Be Announced
				<br>December 2005: To Be Announced
				
				<br><br><div style="font-size: 11px; padding: 5px;">Have a suggestion for a monthly video contest? Please <a href="contact.php">tell us</a> about it.</div>
				
				</td>
				<td><img src="/img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="/img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="/img/pixel.gif" width="1" height="5"></td>
				<td><img src="/img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</tbody></table>
			
		</td>
	</tr>
</tbody></table>

		</div>
<?php 
require "needed/end.php";
?>
