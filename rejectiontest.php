<?php
require "needed/scripts.php";
$testid = "---test";
$remove_fav = $conn->prepare("INSERT INTO `rejections` SELECT * FROM videos WHERE vid = ?");
$remove_fav->execute([$testid]);