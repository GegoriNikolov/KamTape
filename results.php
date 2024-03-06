<?php
require "needed/start.php";
$related_tags = [];
// I took like 6 hours modernizing this and improving pagination. Shocking, eh?
// WARNING: Code below is probably shit. I can't for sure say how it'd look to someone other than me, but I imagine would not be too beautiful.
$start_time = microtime(true);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = 10;
$offs = ($page - 1) * $ppv;
if(!isset($_GET['search_users'])) {
if (!empty($_GET['search']) ||!empty($_GET['related'])) {
    $res_title = "Search";
    $res_rlted = "Results";
    
    if(!empty($_GET['search'])) {
    $real_search = htmlspecialchars(trim($_GET['search']));
    $search = preg_quote($_GET['search']); // Escape special characters for regular expression
    $search = str_replace(" ", "|", $search);   
    }else{
    $search = preg_quote($_GET['related']); // Escape special characters for regular expression
    $search = str_replace(" ", "|", $search);    
    $real_search = htmlspecialchars(trim($_GET['search']));
    }
    $_GET['search'] = $_GET['related'];
    $vidocount = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND videos.rejected = 0 AND users.termination = 0  ORDER BY uploaded DESC");
    $vidocount->execute([$search, $search, $search, $search]);
    $vidocount = $vidocount->rowCount();

    $relatedt = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 AND videos.rejected = 0  ORDER BY (INSTR(LOWER(description), LOWER(?)) > 0) DESC LIMIT $ppv OFFSET $offs");
    $relatedt->execute([$search, $search, $search, $search, $search]);

    foreach($relatedt as $newtag) {
    $related_tags = array_merge($related_tags, explode(" ", $newtag['tags']));
    }
    if(empty($_GET['search'])) {
    $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 AND videos.rejected = 0   ORDER BY (INSTR(LOWER(description), LOWER(?)) > 0) DESC LIMIT $ppv OFFSET $offs");
    $videos->execute([$search, $search, $search, $search, $search]);
    }else{
    $res_title = "Tag";
    $res_rlted = "Related results";
    $real_search = htmlspecialchars(trim($_GET['related']));
    $videos = $conn->prepare("SELECT * FROM videos LEFT JOIN users ON users.uid = videos.uid WHERE (videos.tags REGEXP ? OR videos.description REGEXP ? OR videos.title REGEXP ? OR users.username REGEXP ?) AND videos.privacy = 1 AND videos.converted = 1 AND users.termination = 0 AND videos.rejected = 0   ORDER BY (INSTR(LOWER(tags), LOWER(?)) > 0) DESC LIMIT $ppv OFFSET $offs");
    $videos->execute([$search, $search, $search, $search, $search]);
    }
} else {
    $res_title = "Tag";
    class videos { // Placeholder class when search is empty
        function rowCount() {
            return 0;
        }
    }
    $videos = new videos;
}
}

if($_GET['search_users'] == 'Search Users') {
    $profile = $conn->prepare("SELECT * FROM users WHERE users.username = ?");
    $profile->execute([$_GET['search']]);

    if($profile->rowCount() == 0) {
    //user search idk
    } else {
    redirect("/profile?user=".$_GET['search']);    
    }
}


?>
<div style="color: #333; margin-bottom: 10px;">Related Tags:
		
		<?php $related_tags = array_unique($related_tags); ?>
		<?php foreach($related_tags as $tag) { ?>
		<a href="results.php?search=<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></a>,
		<?php } ?>	
		</div>
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td style="padding-right: 15px;">
		
		<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td>
				<div class="moduleTitleBar">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tbody><tr valign="top">
						<td><div class="moduleTitle"><?php echo htmlspecialchars(trim($res_title)); ?> // <?php echo $real_search; ?></div></td>
						<td align="right">
						<? if($vidocount > 0) { ?><div style="font-weight: color: #444; margin-right: 5px;"><?php echo htmlspecialchars(trim($res_rlted)); ?> <b><?php if ($offs > 0) { echo htmlspecialchars(trim($offs)); } else { echo "1"; } ?>-<? if($vidocount > $ppv) { $nextynexty = $offs + $ppv; } else {$nextynexty = $vidocount; } echo htmlspecialchars($nextynexty); ?></b> of <b><?php                               echo $vidocount; ?></b> for '<b><?php echo $real_search; ?></b>'. (<b><?php echo(number_format(microtime(true) - $start_time, 2)); ?></b> seconds)</div><? } ?>
						</td>
					</tr>
				</tbody></table>
				</div>
				<?php foreach($videos as $video) { ?>
				<?php
				?>
                <div class="moduleEntry"> 
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tbody><tr valign="top">
							<td>
							<table cellpadding="0" cellspacing="0" border="0">
								<tbody><tr>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<?php echo htmlspecialchars(urlencode($real_search)); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=1" class="moduleEntryThumb" width="100" height="75"></a></td>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<?php echo htmlspecialchars(urlencode($real_search)); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=2" class="moduleEntryThumb" width="100" height="75"></a></td>
									<td><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<?php echo htmlspecialchars(urlencode($real_search)); ?>"><img src="get_still.php?video_id=<?php echo $video['vid']; ?>&still_id=3" class="moduleEntryThumb" width="100" height="75"></a></td>
								</tr>
							</table>
							
							</td>
							<td width="100%"><div class="moduleEntryTitle" style="word-break: break-all;"><a href="watch.php?v=<?php echo $video['vid']; ?>&search=<?php echo htmlspecialchars(urlencode($real_search)); ?>"><?php echo htmlspecialchars($video['title']); ?></a></div>
							<div class="moduleEntryDescription"><?php
$description = htmlspecialchars($video['description']);
$description = (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description;
echo $description;
?></div>
							<div class="moduleEntryTags">Tags // <?php foreach(explode(" ", $video['tags']) as $tag) echo '<a href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> : '; ?></div>
							<div class="moduleEntryDetails">Added: <?php echo retroDate($video['uploaded']); ?> by <a href="profile.php?user=<?php echo htmlspecialchars($video['username']); ?>"><?php echo htmlspecialchars($video['username']); ?></a></div>
							<div class="moduleEntryDetails">Channels // <? showChannels($video['vid']); ?>
							<div class="moduleEntryDetails">Runtime: <?php echo gmdate("i:s", $video['time']); ?> | Views: <?php echo $video['views']; ?> | Comments: <?php echo $video['comm_count']; ?></div>
								
	<nobr>
							</nobr>
		
							</td>
						</tr>
					</tbody></table>
				</div>
                <?php } ?> <!-- begin paging -->
<?php if($vidocount > $ppv) { ?>
<div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Result Page:

    <?php
    $totalPages = ceil($vidocount / $ppv);
    if (empty($_GET['page'])) { $_GET['page'] = 1; }
    $pagesPerSet = 7; // Set the number of pages per group
    $startPage = floor(($page - 1) / $pagesPerSet) * $pagesPerSet + 1;
    $endPage = min($startPage + $pagesPerSet - 1, $totalPages); ?>
    <?php if ($startPage < $totalPages && $page !== 1) { ?>
    <a href="results.php?search=<?php echo $real_search; ?>&page=<?php echo $_GET['page'] - 1; ?>"> < Previous</a>
    <?php } ?>

    <?php 
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $page) {
            echo '<span style="color: #444; background-color: #FFFFFF; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;">' . $i . '</span>';
        } else {
            echo '<span style="background-color: #CCC; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><a href="results.php?search=' . $real_search . '&page=' . $i . '">' . $i . '</a></span>';
        }
    }
    ?>
    <?php if ($endPage < $totalPages) { ?>
            <a href="results.php?search=<?php echo $real_search; ?>&page=<?php echo $_GET['page'] + 1; ?>">Next ></a>
    <?php } ?>
</div>
<?php } ?>
				<!-- end paging -->

								
								
				</td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
		</table>
		</td>
		
	</tr>
</table>

<?php
if(empty($videos) || $vidocount == 0) {
?>
	<br>
	<div class="moduleTitle">
		Found no videos matching "<?php echo $real_search; ?>". Do you have one? <a href="my_videos_upload.php">Upload</a> it!
	</div><?php
}
?>

	
		</div>
		</td>
	</tr>
</table>

<?php 
require "needed/end.php";
?>

