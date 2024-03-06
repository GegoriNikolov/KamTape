<?php 
require "needed/start.php";
$tags_strings = $conn->query("SELECT tags FROM videos WHERE converted = 1 ORDER BY uploaded DESC LIMIT 100");
$tag_list = [];
foreach($tags_strings as $result) $tag_list = array_merge($tag_list, explode(" ", $result['tags']));
$tag_list = array_slice(array_count_values($tag_list), 0, 100);

$tags_strings_pop = $conn->query("SELECT videos.vid, videos.title, videos.description, videos.tags, users.username, COUNT(views.view_id) AS view_count FROM views LEFT JOIN videos ON videos.vid = views.vid LEFT JOIN users ON users.uid = videos.uid WHERE videos.converted = 1 AND videos.privacy = 1 AND videos.uploaded > DATE_SUB(NOW(), INTERVAL 5 DAY) GROUP BY views.vid ORDER BY view_count DESC LIMIT 100; ");
$tag_list_pop = [];
foreach ($tags_strings_pop as $result_pop) {
  $tag_list_pop = array_merge($tag_list_pop, explode(" ", $result_pop['tags']));
}
$tag_list_pop = array_slice(array_count_values($tag_list_pop), 0, 100);

?>
<div class="tableSubTitle">Tags</div>

<div class="pageTable">

<div style="font-size: 14px; font-weight: bold; color: #666666; margin-bottom: 10px;">Latest Tags //</div>

<div style="margin-bottom: 20px;">

	<?php foreach($tag_list as $tag => $frequency	) {
        $freqindex = $frequency*2;
        $freqindex = $freqindex+10;
        if ($freqindex > 28) {
            $freqindex = 28;
        }
					echo '<a style="font-size: '.htmlspecialchars($freqindex).'px;" href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> :'."\r\n";
				} ?>

	</div>

<div style="font-size: 16px; font-weight: bold; color: #666666; margin-bottom: 10px;">Most Popular Tags //</div>

	<?php foreach($tag_list_pop as $tag => $frequency	) {
        $freqindex = $frequency*2;
        $freqindex = $freqindex+10;
        if ($freqindex > 28) {
            $freqindex = 28;
        }
					echo '<a style="font-size: '.htmlspecialchars($freqindex).'px;" href="results.php?search='.htmlspecialchars($tag).'">'.htmlspecialchars($tag).'</a> :'."\r\n";
				} ?>

	</div>

<?php 
require "needed/end.php";
?>