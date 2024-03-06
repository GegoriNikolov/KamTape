<?php 
require "needed/start.php";
if($_SESSION['uid'] == NULL) {
	header("Location: login.php");
}
?>
<div class="tableSubTitle">Coming Soon</div>
Sorry, no proper friends system yet. Should be coming in a few weeks though.
<?php 
require "needed/end.php";
?>