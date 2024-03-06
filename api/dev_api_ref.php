<?php
require "../needed/start.php";
if(isset($_GET['m']) && in_array($_GET['m'], ["kamtape.users.get_profile", "kamtape.users.list_favorite_videos", "kamtape.users.list_friends", "kamtape.videos.get_details", "kamtape.videos.list_by_tag", "kamtape.videos.list_by_user", "kamtape.videos.list_featured"])) {
	$method = $_GET['m'];
} else {
	header("Location: dev");
	die();
}
require "needed/methods.php";
?>

<p class="tableSubTitle">	<?php echo $method; ?>
 (API Function Reference)</p>

<span class="apiHeader">Description</span>
<div class="devIndent">	<?php echo $description; ?>
</div>

<span class="apiHeader">Parameters</span>
<div class="devIndent">
		<b>method:</b> 	<?php echo $method; ?>
 (only needed as an explicit parameter for REST calls)<br>
	<b>dev_id:</b> Developer ID.  Please <a href="/my_profile_dev">request one</a> if you don't already have one.<br>

<?php echo $parameters; ?>

</div>

<span class="apiHeader">Example Response</span>
<div class="devIndent"><div class="codeArea"><tt>
<?php echo $response; ?>

</tt></div></div>

<span class="apiHeader">Error Codes</span>
<div class="devIndent">
	<a href="dev_error_codes">Standard error codes</a><br><br>
<?php echo $errorcodes; ?>

</div>
<?php
require "../needed/end.php";
?>