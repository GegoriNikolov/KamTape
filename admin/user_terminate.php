<?php
require "admin_head.php";
    $terminatehim = $conn->prepare("UPDATE users SET termination = 1 WHERE uid = ?");
	$terminatehim->execute([$_GET['user_id']]);
    session_error_index("Terminated!", "error");