<?php
require "../needed/start.php";
?>
<p class="tableSubTitle">Developer API</p>

<span class="highlight">Introduction</span>

<div class="devIndent">
	<p>KamTape is excited to announce the availability of our new APIs, with enhanced usability and functionality.  Using our APIs, you can easily integrate online videos from KamTape's rapidly growing repository of videos into your application.  The APIs currently allow read-only access to key parts of the KamTape video respository and user community.  If you haven't already, create a KamTape account and create a KamTape <a href="/my_profile_dev.php">developer profile</a>.  After you've created a developer profile, you should be ready to tap into the power of KamTape.</p>
</div>

<table width="80%" align="center"><tr>
	<td align="50%" valign="top"> <!-- left column -->
		<span class="highlight">Must Read</span>

		<ul>
			<li><a href="dev_intro">Introduction</a></li>
		</ul>

		<br>

		<span class="highlight">API Call Interfaces</span>

		<ul>
			<li><a href="dev_rest">REST Interface</a></li>
			<li><a href="dev_xmlrpc">XML-RPC Interface</a></li>
		</ul>

		<br>

		<span class="highlight">Error Codes</span>

		<ul>
			<li><a href="dev_error_codes">List of error codes</a></li>
		</ul>
	</td> <!-- left column -->
	<td align="50%" valign="top"> <!-- right column -->
		<div class="apiShadedBox">

			<span class="apiHeader">API Functions</span><br><br>

			<span class="highlight">Users</span>
	
			<ul>
				<li><a href="dev_api_ref?m=kamtape.users.get_profile">kamtape.users.get_profile</a></li>
				<li><a href="dev_api_ref?m=kamtape.users.list_favorite_videos">kamtape.users.list_favorite_videos</a></li>
				<li><a href="dev_api_ref?m=kamtape.users.list_friends">kamtape.users.list_friends</a></li>
			</ul>

			<span class="highlight">Videos</span>
	
			<ul>
				<li><a href="dev_api_ref?m=kamtape.videos.get_details">kamtape.videos.get_details</a></li>
				<li><a href="dev_api_ref?m=kamtape.videos.list_by_tag">kamtape.videos.list_by_tag</a></li>
				<li><a href="dev_api_ref?m=kamtape.videos.list_by_user">kamtape.videos.list_by_user</a></li>
				<li><a href="dev_api_ref?m=kamtape.videos.list_featured">kamtape.videos.list_featured</a></li>
			</ul>
		</div>
	</td> <!-- right column -->
</tr></table>

<br>

<span class="highlight">Questions? Issues? Enhancement requests?</span>
<div class="devIndent">
<p>Please send these our way through our <a href="/contact.php">contact form</a>, with the subject "Developer Questions."</p>

<p>Users of the older API can refer to the <a href="/api_v1.php">old API docs</a>.</p>
</div>



		</td>
	</tr>
</table>
<?php
require "../needed/end.php";
?>