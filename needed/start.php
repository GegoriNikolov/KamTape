<?php 
require __DIR__ . "/scripts.php";
$inbox = $conn->prepare("SELECT * FROM messages WHERE receiver = ? AND isRead = 0 ORDER BY created DESC");
$inbox->execute([$session['uid']]);
$inbox = $inbox->rowCount();
?>
<?php if ($current_page !== 'sharing')  { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<? } ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php if ($current_page !== 'watch')  { ?>
<title>KamTape - Televise Yourself.</title>
<? } ?>
<meta name="description" content="Share your videos with friends and family">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link href="../styles.css" rel="stylesheet" type="text/css">
<link rel="alternate" type="application/rss+xml" title="KamTape "" Recently Added Videos [RSS]" href="http://www.kamtape.com/rss/global/recently_added.rss">
<script language="javascript" type="text/javascript">
	onLoadFunctionList = new Array();
	function performOnLoadFunctions()
	{
		for (var i in onLoadFunctionList)
		{
			onLoadFunctionList[i]();
		}
	}
</script>
<?php if ($current_page == 'watch') { ?>
<script type="text/javascript" src="/flashobject.js"></script>
<script type="text/javascript" src="/js/components.js"></script>
<script type="text/javascript" src="/js/AJAX.js"></script>

<script language="javascript" type="text/javascript">
	function dropdown_jumpto(x)
	{
		if (document.share_dropdown.jumpmenu.value != "null")
		{
			document.location.href = x;
		}
	}
</script>
<? } ?>
<? if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') { ?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2537513323123758"
     crossorigin="anonymous"></script>
     <? } ?>
</head>


<body onLoad="performOnLoadFunctions();">

<table width="800" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
		<td bgcolor="#FFFFFF" style="padding-bottom: 25px;">
		

<table width="91%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="130" rowspan="2" style="padding: 0px 5px 5px 5px;"><a href="/index.php"><img src="/img/<? echo invokethConfig("logo"); ?>" width="124" height="48" alt="KamTape" border="0" style="vertical-align: right; "></a></td>
		<td valign="top">
		
		<table width="670" cellpadding="0" cellspacing="0" border="0">
			<tr valign="top">
				<td style="padding: 0px 5px 0px 5px; font-style: italic;"><? echo invokethConfig("slogan"); ?></td>
				<td align="right">
				
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
		
							
						<?php if(isset($session)) { ?>
                <td>Hello, <a href="/my_profile.php"><?php echo htmlspecialchars($session['username']) ?></a>!&nbsp;<a href="/my_messages.php"><img src="../img/mail<? if($inbox > 0) { echo '_unread'; } ?>.gif" id="mailico" border="0"></a>&nbsp;(<a href="/my_messages.php"><?php echo htmlspecialchars($inbox) ?></a>)</td>
                <? if ($session['staff'] == 1) {?><td style="padding: 0px 5px 0px 5px;">|</td>
                <td><a href="/admin/" class="bold" style="color: #006f09;">ManagerTape</a></td><? } ?>
                <td style="padding: 0px 5px 0px 5px;">|</td>
                <td><a href="/logout.php?next=<?php echo $_SERVER['REQUEST_URI'] ?>">Log Out</a></td>
            <?php } else if(!isset($session)){ ?>
                            <td><a href="/signup.php" class="bold">Sign Up</a></td>
               <td style="padding: 0px 5px 0px 5px;">|</td>
                <td><a href="/login.php">Log In</a></td>
                <?php } ?>
						<td style="padding: 0px 5px 0px 5px;">|</td>
						<td style="padding-right: 5px;"><a href="help.php">Help</a></td>
		
										
					</tr>
				</table>
				
				<!--
								
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<form method="GET" action="results.php">
						<td><input type="text" value="" name="search" size="30" maxlength="128" style="color:#ff3333; font-size: 12px; padding: 2px;"></td>
						<td><input type="submit" value="Search Videos"></td>
						</form>
					</tr>
				</table>
				
										-->
				
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr valign="bottom">
		<td>
		
		<table cellpadding="0" cellspacing="0" border="0">
			<tbody><tr>
				<td>
				<table style=" <?php if ($current_page == 'index' || $current_page == 'my_messages' || $current_page == 'outbox' || $current_page == 'my_favorites' || $current_page == 'my_videos' ||  $current_page == 'dev' || $current_page == 'dev_api_ref' || $current_page == 'dev_error_codes' || $current_page == 'dev_intro' || $current_page == 'dev_rest' || $current_page == 'dev_xmlrpc') { echo 'background-color: #DDDDDD; margin: 5px 2px 1px 0px; border-bottom: 1px solid #DDDDDD;'; } else { echo "background-color: #BECEEE; margin: 5px 2px 1px 0px;"; }?> " cellpadding="0" cellspacing="0" border="0">
					<tbody><tr>
						<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
						<td><img src="/img/pixel.gif" width="1" height="5"></td>
						<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
					</tr>
					<tr>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
						<td style="padding: 0px 20px 5px 20px; font-size: 13px; font-weight: bold;"><a href="/index.php">Home</a></td>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
					</tr>
				</tbody></table>
				</td>
				<td>
				<table style=" <?php if ($current_page == 'browse' || $current_page == 'watch') { echo 'background-color: #DDDDDD; margin: 5px 2px 1px 0px; border-bottom: 1px solid #DDDDDD;'; } else { echo "background-color: #BECEEE; margin: 5px 2px 1px 0px;"; }?> " cellpadding="0" cellspacing="0" border="0">
					<tbody><tr>
						<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
						<td><img src="/img/pixel.gif" width="1" height="5"></td>
						<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
					</tr>
					<tr>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
						<td style="padding: 0px 20px 5px 20px; font-size: 13px; font-weight: bold;"><a href="/browse.php">Videos</a></td>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
					</tr>
				</tbody></table>
				</td>
				<td>
				<table style=" <?php if ($current_page == 'channels' || $current_page == 'channels_portal') { echo 'background-color: #DDDDDD; margin: 5px 2px 1px 0px; border-bottom: 1px solid #DDDDDD;'; } else { echo "background-color: #BECEEE; margin: 5px 2px 1px 0px;"; }?> " cellpadding="0" cellspacing="0" border="0">
					<tbody><tr>
						<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
						<td><img src="/img/pixel.gif" width="1" height="5"></td>
						<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
					</tr>
					<tr>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
						<td style="padding: 0px 20px 5px 20px; font-size: 13px; font-weight: bold;"><a href="/channels.php">Channels</a></td>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
					</tr>
				</tbody></table>
				</td>
				<td>
				<table style=" <?php if ($current_page == 'my_videos_upload') { echo 'background-color: #DDDDDD; margin: 5px 2px 1px 0px; border-bottom: 1px solid #DDDDDD;'; } else { echo "background-color: #BECEEE; margin: 5px 2px 1px 0px;"; }?> " cellpadding="0" cellspacing="0" border="0">
					<tbody><tr>
						<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
						<td><img src="/img/pixel.gif" width="1" height="5"></td>
						<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
					</tr>
					<tr>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
						<td style="padding: 0px 20px 5px 20px; font-size: 13px; font-weight: bold;"><a href="/my_videos_upload.php">Upload</a></td>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
					</tr>
				</tbody></table>
				</td>
				<td>
				<table style=" <?php if ($current_page == 'my_friends_invite' || $current_page == 'my_friends') { echo 'background-color: #DDDDDD; margin: 5px 2px 1px 0px; border-bottom: 1px solid #DDDDDD;'; } else { echo "background-color: #BECEEE; margin: 5px 2px 1px 0px;"; }?> " cellpadding="0" cellspacing="0" border="0">
					<tbody><tr>
						<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
						<td><img src="/img/pixel.gif" width="1" height="5"></td>
						<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
					</tr>
					<tr>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
						<td style="padding: 0px 20px 5px 20px; font-size: 13px; font-weight: bold;"><a href="/my_friends_invite.php">Invite Friends</a></td>
						<td><img src="/img/pixel.gif" width="5" height="1"></td>
					</tr>
				</tbody></table>
				</td>
			</tr>
		</tbody></table>
		</td>
	</tr>
	
</table>

<table align="center" width="800" bgcolor="#DDDDDD" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 10px;">
	<tr>
		<td><img src="/img/box_login_tl.gif" width="5" height="5"></td>
		<td><img src="/img/pixel.gif" width="1" height="5"></td>
		<td><img src="/img/box_login_tr.gif" width="5" height="5"></td>
	</tr>
	<tr>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
		<td width="790" align="center" style="padding: 2px;">

		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td style="font-size: 10px;">&nbsp;</td>
				
				<?php if ($current_page == 'index' || $current_page == 'my_videos' || $current_page == 'my_favorites' || $current_page == 'my_messages' || $current_page == 'outbox' || $current_page == 'my_profile'  || $current_page == 'my_friends' ||  $current_page == 'dev' || $current_page == 'dev_api_ref' || $current_page == 'dev_error_codes' || $current_page == 'dev_intro' || $current_page == 'dev_rest' || $current_page == 'dev_xmlrpc') { ?>				
				<td style=" <? if ($current_page == 'my_videos') { echo "font-weight: bold;"; } ?> "><a href="/my_videos.php">My Videos</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($current_page == 'my_favorites') { echo "font-weight: bold;"; } ?> "><a href="/my_favorites.php">My Favorites</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($current_page == 'my_friends') { echo "font-weight: bold;"; } ?> "><a href="/my_friends.php">My Friends</a></td>
				<!-- <td>&nbsp;|&nbsp;</td>
				<td style="  "><a href="my_groups.php">My Groups</a></td> -->
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($current_page == 'my_messages' || $current_page == 'outbox') { echo "font-weight: bold;"; } ?> "><a href="/my_messages.php">My Messages</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($current_page == 'my_profile') { echo "font-weight: bold;"; } ?> "><a href="/my_profile.php">My Profile</a></td>
				<? } else if ($current_page == 'browse' || $current_page == 'watch') { ?>		
				<td style=" <? if ($current_page == 'browse') { if (empty($_GET['s']) || $_GET['s'] == 'mr') { echo "font-weight: bold;"; } } ?> "><a href="browse.php?s=mr">Most Recent</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'mp') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=mp">Most Viewed</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'md') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=md">Most Discussed</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'mf') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=mf">Top Favorites</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'rf') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=rf">Recently Featured</a></td>
				<td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'r') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=r">Random</a></td>
                <td style="padding: 0px 10px 0px 10px;">|</td>
				<td style=" <? if ($_GET['s'] == 'tr') { echo "font-weight: bold;"; } ?> "><a href="browse.php?s=tr">Top Rated</a></td>
	
	
				
								
				<td style="font-size: 10px;">&nbsp;</td>
                <? } ?>
								
								
								
				<td style="font-size: 10px;">&nbsp;</td>
			</tr>
		</table>
			
		</td>
		<td><img src="img/pixel.gif" width="5" height="1"></td>
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #FFFFFF"><img src="/img/box_login_bl.gif" width="5" height="5"></td>
		<td style="border-bottom: 1px solid #BBBBBB"><img src="/img/pixel.gif" width="1" height="5"></td>
		<td style="border-bottom: 1px solid #FFFFFF"><img src="/img/box_login_br.gif" width="5" height="5"></td>
	</tr>
</table>

<div style="padding: 0px 5px 0px 5px;">

<table align="center" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 10px;">
	<tr> <?php if ($current_page == 'index' || $current_page == 'results' || $current_page == 'watch' || $current_page == 'browse') { ?>
		<form method="GET" action="results.php">
		<td style="padding-right: 5px;"><input type="text" value="<?php if(isset($_GET['search'])) { echo htmlspecialchars($_GET['search']); } ?>" name="search" maxlength="128" style="color:#84d162; font-size: 12px; width: 300px;"></td>
		<td><input type="submit" value="Search Videos"></td>
		</form><? } ?>
	</tr>
</table>
<? if(!empty(invokethConfig("notice"))) { alert("Notice: ".invokethConfig("notice")); } 
if (isset($_COOKIE['hates__dwntime']) && invokethConfig("maintenance") == 1){
    alert("The website is currently in maintenance.");
    }
    if (isset($_GET['session'])) {
	require_once "needed/phpickle/phpickle.php";
	$base64urltobase64 = strtr($_GET['session'], '-_', '+/');
	$string = phpickle::loads(base64_decode($base64urltobase64));
    if (implode(" ",$string['messages']) != NULL) {
	$type = "success";
	$message = $string['messages']['0'];
    } elseif (implode(" ",$string['errors']) != NULL) {
	$type = "error";
	$message = $string['errors']['0'];
    }
    alert(htmlspecialchars($message), htmlspecialchars($type));
    }

?>
