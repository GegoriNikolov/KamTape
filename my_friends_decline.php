<?php
require "needed/start.php";
if(empty($_SESSION)) {
	header("Location: index.php");
}
if(isset($_GET['user'])) {
    $acceptant = $conn->prepare("SELECT uid FROM users WHERE users.username = ?");
    $acceptant->execute([$_GET['user']]);
    $acceptant = $acceptant->fetch();
    $stmt = $conn->prepare('UPDATE relationships SET accepted = 3 WHERE respondent = :respondent AND sender = :sender');
	$stmt->execute([':respondent' => $session['uid'], ':sender' => $acceptant['uid']]);
}
redirect("my_friends_accept.php");