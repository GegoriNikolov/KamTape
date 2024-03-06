<?php
require "needed/scripts.php";
ob_get_clean();

if(empty($_SESSION)) {
	header("Location: index.php");
}
$msg = $conn->prepare("SELECT * FROM messages LEFT JOIN users ON users.uid = messages.receiver WHERE pmid = ?");
$msg->execute([$_GET['msg']]);

if($msg->rowCount() == 0) {
	header("Location: /my_messages.php");
	die();
} else {
	$msg = $msg->fetch(PDO::FETCH_ASSOC);
}
if ($msg['sender'] == $session['uid'] || $msg['receiver'] == $session['uid']) {
    $remove_video = $conn->prepare("DELETE FROM messages WHERE pmid = :pmid");
	$remove_video->execute([
		":pmid" => $_GET['msg']
	]);
    header("Location: my_messages.php");
}else{ header("Location: outbox.php"); }
?>