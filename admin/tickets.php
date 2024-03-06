<?php
require "admin_head.php";

force_login();
$msgcount = $conn->query("SELECT * FROM tickets WHERE resolved = 0 ORDER BY submitted ASC");
$msgcount = $msgcount->rowCount();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$ppv = 35;
$offs = ($page - 1) * $ppv;
$inbox = $conn->query("SELECT * FROM tickets WHERE resolved = 0 ORDER BY submitted ASC LIMIT $ppv OFFSET $offs");

?>

<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#CCCCCC">
			<tbody><tr>
				<td><img src="img/box_login_tl.gif" width="5" height="5"></td>
				<td width="100%"><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_tr.gif" width="5" height="5"></td>
			</tr>
			<tr>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
				<td width="585">
				<div class="moduleTitleBar">
				<div class="moduleTitle">
				Unresolved Tickets<br><span style="font-weight:normal; font-size: 11px;">To resolve a ticket, click on the ticket's email.</span>
				</div>
				</div>
		
				
                                             
                <?php if($inbox->rowCount() > 0) {
				foreach($inbox as $message) { ?>
                 <div class="moduleEntry">
					<table width="565" cellpadding="0" cellspacing="0" border="0">
						<tbody><tr valign="top">
                            <td width="100%"><div class="moduleEntryTitle"><a href="tickets_resolve.php?n=<?= htmlspecialchars($message['ticket']) ?>"><?= htmlspecialchars($message['sender']) ?></a></div><br>
							<div class="mailMessageArea"><?= decrypt($message['message']) ?></div></td>
							</td>
						</tr>
					</tbody></table>
					</div>
                <? } ?>
                <? } ?>
                </td>
				<td><img src="img/pixel.gif" width="5" height="1"></td>
			</tr>
			<tr>
				<td><img src="img/box_login_bl.gif" width="5" height="5"></td>
				<td><img src="img/pixel.gif" width="1" height="5"></td>
				<td><img src="img/box_login_br.gif" width="5" height="5"></td>
			</tr>
            <!-- begin paging -->
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
		</tbody></table>

<?php
require "needed/end.php";
?>