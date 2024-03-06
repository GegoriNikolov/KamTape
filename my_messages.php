<?php
require "needed/start.php";

force_login();
$msgcount = $conn->prepare("SELECT * FROM messages WHERE receiver = ? ORDER BY created DESC");
$msgcount->execute([$session['uid']]);
$msgcount = $msgcount->rowCount();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = 35;
$offs = ($page - 1) * $ppv;
$inbox = $conn->prepare("SELECT * FROM messages LEFT JOIN users ON users.uid = messages.sender WHERE receiver = ? ORDER BY created DESC LIMIT $ppv OFFSET $offs");
$inbox->execute([$session['uid']]);

?>

<div class="tableSubTitle">Inbox Messages</div>
<table width="45%" align="center" cellpadding="5" cellspacing="0" border="0">
         <tr align="center">
		 <td align="center" colspan="3">
                <a href="/my_messages.php" class="bold">Inbox Messages</a> | <a href="/outbox.php">Sent Messages</a>
            </td></tr>
            </table>
<table width="91%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
	<tbody>
	<tr>
		<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
		<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
		<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		<td>
		<div class="moduleTitleBar">
		<div class="moduleTitle"><? if($msgcount > 0) { ?><div style="float: right; padding: 1px 5px 0px 0px; font-size: 12px;">Messages <?php if ($offs > 0) { echo htmlspecialchars(trim($offs)); } else { echo "1"; } ?>-<? if($vidocount > $ppv) { $nextynexty = $offs + $ppv; } else {$nextynexty = $msgcount; } echo htmlspecialchars($nextynexty); ?> of <?php echo $inbox->rowCount(); ?></div><? } ?>
		Message Center
		</div>
		</div>
				
	<table width="100%" cellpadding="3" cellspacing="0" table="table" align="center">
                <tbody><tr><td colspan="5" height="10"></td></tr>
                                <tr>
                        <td width="20">&nbsp;</td>
                        <td><b>Subject</b></td>
                        <td width="70"><b>From<b></b></b></td>
                        <td width="160"><b>Date</b></td>
                        <td width="20">&nbsp;</td>
                </tr>
                <?php if($inbox->rowCount() > 0) {
				foreach($inbox as $message) { ?>
                 <tr bgcolor="<?php if($message['isRead'] == '0') { echo'#FFCC66'; } else { echo '#eeeeee'; } ?>">
                        <td width="5"><img src="/img/mail<?php if($message['isRead'] == '0') { echo'_unread'; } ?>.gif"></td>
                        <td><a href="/read_msg.php?id=<?php echo htmlspecialchars($message['pmid']);?>&amp;s=" <?php if($message['isRead'] == '0') { echo'class=bold'; } ?>><?php echo decrypt($message['subject']); ?></a></td>
                        <td><a href="/user/<?php echo htmlspecialchars($message['username']);?>"><?php echo htmlspecialchars($message['username']);?></a></td>
                        <td><?php echo retroDate($message['created'], "l, F j, Y"); ?>
                        <td width="5"><?php if(!empty($message['attached'])) { ?><img src="/img/tv<?php if($message['isRead'] == '0') { echo'_unread'; } ?>.gif"><? } ?></td>
                </tr>
                <? } ?>
                <? } ?>
                                                </tbody></table><!-- begin paging -->
				<?php if($msgcount > $ppv) { ?><div style="font-size: 13px; font-weight: bold; color: #444; text-align: right; padding: 5px 0px 5px 0px;">Browse Pages:
				
					<?php
    $totalPages = ceil($msgcount / $ppv);
    if (empty($_GET['page'])) { $_GET['page'] = 1; }
    $pagesPerSet = 10; // Set the number of pages per group
    $startPage = floor(($page - 1) / $pagesPerSet) * $pagesPerSet + 1;
    $endPage = min($startPage + $pagesPerSet - 1, $totalPages); ?>
    <?php if ($startPage < $totalPages && $page !== 1) { ?>
    <a href="my_favorites.php?page=<?php echo $_GET['page'] - 1; ?>"> < Previous</a>
    <?php } ?>

    <?php 
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $page) {
            echo '<span style="color: #444; background-color: #FFFFFF; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;">' . $i . '</span>';
        } else {
            echo '<span style="background-color: #CCC; padding: 1px 4px 1px 4px; border: 1px solid #999; margin-right: 5px;"><a href="my_messages.php?page=' . $i . '">' . $i . '</a></span>';
        }
    }
    ?>
    <!-- Add "Next" link if there are more pages -->
    <?php if ($endPage < $totalPages) { ?>
            <a href="my_messages.php?page=<?php echo $_GET['page'] + 1; ?>">Next ></a>
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
</tbody></table>

<?php
require "needed/end.php";
?>