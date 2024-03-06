<?php 
require_once __DIR__ . '/../needed/start.php';

force_login();

if(!isset($session['staff']) || $session['staff'] != 1) {
	redirect("Location: /index.php"); 
    exit;
}
$inbox = $conn->prepare("SELECT * FROM messages WHERE receiver = ? AND isRead = 0 ORDER BY created DESC");
$inbox->execute([$session['uid']]);
$inbox = $inbox->rowCount();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<table>
<table cellspacing="0"><tbody>
<tr>
<td style="background: #96ff88; font-weight: bold; padding: 9px; text-align: center;">ManagerTape</td>
<td style="background: #ff8888; font-weight: bold; padding: 9px; text-align: center;"><?= retroDate("now", "l") ?></td>
<td style="background: #ff8888; font-weight: bold; padding: 9px; text-align: center;"><?= retroDate("now", "F j, Y") ?></td>
<td style="background: #ff8888; font-weight: bold; padding: 9px; text-align: center;"><?= retroDate("now", "h:i:s") ?> <?= retroDate("now", 'I') ? 'PDT' : 'PST'; ?></td>
</tr>
<tr>
<td style="width: 113px;height: 44px;background: #96ff88; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="index.php">Home</a></td>
<td style="width: 113px;height: 44px;background: #FFF; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="people.php">People</a></td>
<td style="width: 113px;height: 44px;background: #96ff88; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="uploads.php">Uploads</a></td>
<td style="width: 113px;height: 44px;background: #FFF; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="settings.php">Settings</a></td>
<td style="width: 113px;height: 44px;background: #96ff88; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="tickets.php">Support</a></td>
<td style="width: 113px;height: 44px;background: #FFF; font-size: 20px; font-weight: bold; padding: 1px 12px;text-align: center; border: 1px dashed #339950; border-left: none;"><a style="text-decoration: none;" href="more.php">More!</a></td>

	</tr></tbody></table>
    <p>