<?php
switch($method) {
case 'kamtape.users.get_profile':
$description = "Retrieves the public parts of a user profile.";
$parameters = "	<b>user:</b> The user to retrieve the profile for. This is the same as the name that shows up on the KamTape website.";
$response = "&lt;user_profile&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;first_name&gt;Bob&lt;/first_name&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;last_name&gt;Jones&lt;/last_name&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;about_me&gt;This is my profile&lt;/about_me&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;age&gt;29&lt;/age&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_upload_count&gt;7&lt;/video_upload_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_watch_count&gt;16&lt;/video_watch_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;homepage&gt;http://www.myhomepage.com/&lt;/homepage&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;hometown&gt;Los Angeles, CA&lt;/hometown&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;gender&gt;m&lt;/gender&gt; <b>&lt;!-- m or f --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;occupations&gt;Abstract Artist&lt;/occupations&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;companies&gt;KamTape&lt;/companies&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;city&gt;San Francisco, CA&lt;/city&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;country&gt;US&lt;/country&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;books&gt;Learning Python&lt;/books&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;hobbies&gt;KamTape, KamTape, KamTape&lt;/hobbies&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;movies&gt;Star Wars Original Trilogy&lt;/movies&gt; <br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;relationship&gt;taken&lt;/relationship&gt;  <b>&lt;!-- single, taken, or open --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;friend_count&gt;5&lt;/friend_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;favorite_video_count&gt;15&lt;/favorite_video_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;currently_on&gt;false&lt;/currently_on&gt;  <br>
&lt;/user_profile&gt;<br>";
$errorcodes = "		<b>101</b>: No user was found with the specified username.";
break;
case 'kamtape.users.list_favorite_videos':
$description = "Lists a user's favorite videos.";
$parameters = "	<b>user:</b> The user to retrieve the favorite videos for.  This is the same as the name that shows up on the KamTape website.";
$response = "&lt;video_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;kamtaperocks&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;k0gEeue2sLk&lt;/id&gt;   <b>&lt;!-- this ID can be used with kamtape.videos.get_details --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;My First Motion Picture&lt;/title&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length_seconds&gt;16&lt;/length_seconds&gt;  <b>&lt;!-- length of video --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_avg&gt;3.75&lt;/rating_avg&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_count&gt;10&lt;/rating_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;This is the video description shown on the KamTape site.&lt;/description&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;170&lt;/view_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;upload_time&gt;1121398533&lt;/upload_time&gt;  <b>&lt;!-- UNIX-style time, secs since 1/1/1970 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_count&gt;1&lt;/comment_count&gt;  <b>&lt;!-- how many comments does this video have? --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tags&gt;feature film documentary&lt;/tags&gt; <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;url&gt;http://www.kamtape.com/watch.php?v=k04Eeue24Lk&lt;/url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;thumbnail_url&gt;http://www.kamtape.com/get_still.php?video_id=k04Eeue24Lk&lt;/thumbnail_url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>... another video ...</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&lt;/video_list&gt;<br>
";
$errorcodes = "		<b>101</b>: No user was found with the specified username.";
break;
case 'kamtape.users.list_friends':
$description = "Lists a user's friends.";
$parameters = "	<b>user:</b> The user to retrieve the friends for.  This is the same as the name that shows up on the KamTape website.";
$response = "&lt;friend_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;friend&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;user&gt;username1&lt;/user&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_upload_count&gt;1&lt;/video_upload_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;favorite_count&gt;2&lt;/favorite_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;friend_count&gt;3&lt;/friend_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/friend&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;friend&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;user&gt;username2&lt;/user&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_upload_count&gt;5&lt;/video_upload_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;favorite_count&gt;3&lt;/favorite_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;friend_count&gt;2&lt;/friend_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/friend&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;<b>... and more ...</b><br>
&lt;/friend_list&gt;<br>";
$errorcodes = "		<b>101</b>: No user was found with the specified username.";
break;
case 'kamtape.videos.get_details':
$description = "Displays the details for a video.";
$parameters = "	<b>video_id:</b> The ID of the video to get details for.  This is the ID that's returned by the list";
$response = "&lt;video_details&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;kamtapeuser&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;My Trip to California&lt;/title&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_avg&gt;3.25&lt;/rating_avg&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_count&gt;10&lt;/rating_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;tags&gt;california trip redwoods&lt;/tags&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;This video shows some highlights of my trip to California last year.&lt;/description&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;update_time&gt;1129803584&lt;/update_time&gt;  <b>&lt;!-- UNIX time, secs since 1/1/70 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;7&lt;/view_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;upload_time&gt;1127760809&lt;/upload_time&gt;  <b>&lt;!-- UNIX time, secs since 1/1/70 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;length_seconds&gt;8&lt;/length_seconds&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;recording_date&gt;None&lt;/recording_date&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;recording_location/&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;recording_country/&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;steve&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;text&gt;asdfasdf&lt;/text&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;time&gt;1129773022&lt;/time&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/comment&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/comment_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;channel_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;channel&gt;Humor&lt;/channel&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;channel&gt;Odd &amp; Outrageous&lt;/channel&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/channel_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;thumbnail_url&gt;http://www.kamtape.com/get_still.php?video_id=bkZHmZmZUJk&lt;/thumbnail_url&gt;<br>
&lt;/video_details&gt;<br>";
$errorcodes = "		<b>102</b>: No video was found with the specified ID.";
break;
case 'kamtape.videos.list_by_tag':
$description = "Lists all videos that have the specified tag.";
$parameters = '	<b>tag:</b> the tag to search for<br />
	(optional) <b>page:</b> the "page" of results you want to retrieve (e.g. 1, 2, 3)<br />
	(optional) <b>per_page:</b> the number of results you want to retrieve per page (default 20, maximum 100)';
$response = "&lt;video_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;kamtaperocks&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;k0gEeue2sLk&lt;/id&gt;   <b>&lt;!-- this ID can be used with kamtape.videos.get_details --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;My First Motion Picture&lt;/title&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length_seconds&gt;16&lt;/length_seconds&gt;  <b>&lt;!-- length of video --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_avg&gt;3.75&lt;/rating_avg&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_count&gt;10&lt;/rating_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;This is the video description shown on the KamTape site.&lt;/description&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;170&lt;/view_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;upload_time&gt;1121398533&lt;/upload_time&gt;  <b>&lt;!-- UNIX-style time, secs since 1/1/1970 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_count&gt;1&lt;/comment_count&gt;  <b>&lt;!-- how many comments does this video have? --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tags&gt;feature film documentary&lt;/tags&gt; <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;url&gt;http://www.kamtape.com/watch.php?v=k04Eeue24Lk&lt;/url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;thumbnail_url&gt;http://www.kamtape.com/get_still.php?video_id=k04Eeue24Lk&lt;/thumbnail_url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>... another video ...</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&lt;/video_list&gt;<br>
";
$errorcodes = "";
break;
case 'kamtape.videos.list_by_user':
$description = "Lists all videos that were uploaded by the specified user";
$parameters = "	<b>user:</b> user whose videos you want to list";
$response = "&lt;video_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;kamtaperocks&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;k0gEeue2sLk&lt;/id&gt;   <b>&lt;!-- this ID can be used with kamtape.videos.get_details --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;My First Motion Picture&lt;/title&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length_seconds&gt;16&lt;/length_seconds&gt;  <b>&lt;!-- length of video --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_avg&gt;3.75&lt;/rating_avg&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_count&gt;10&lt;/rating_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;This is the video description shown on the KamTape site.&lt;/description&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;170&lt;/view_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;upload_time&gt;1121398533&lt;/upload_time&gt;  <b>&lt;!-- UNIX-style time, secs since 1/1/1970 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_count&gt;1&lt;/comment_count&gt;  <b>&lt;!-- how many comments does this video have? --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tags&gt;feature film documentary&lt;/tags&gt; <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;url&gt;http://www.kamtape.com/watch.php?v=k04Eeue24Lk&lt;/url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;thumbnail_url&gt;http://www.kamtape.com/get_still.php?video_id=k04Eeue24Lk&lt;/thumbnail_url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>... another video ...</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&lt;/video_list&gt;<br>
";
$errorcodes = "		<b>101</b>: No user was found with the specified username.";
break;
case 'kamtape.videos.list_featured':
$description = "Lists the most recent 25 videos that have been featured on the front page of the KamTape site.";
$parameters = "";
$response = "&lt;video_list&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;kamtaperocks&lt;/author&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;k0gEeue2sLk&lt;/id&gt;   <b>&lt;!-- this ID can be used with kamtape.videos.get_details --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;My First Motion Picture&lt;/title&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length_seconds&gt;16&lt;/length_seconds&gt;  <b>&lt;!-- length of video --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_avg&gt;3.75&lt;/rating_avg&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;rating_count&gt;10&lt;/rating_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;This is the video description shown on the KamTape site.&lt;/description&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;170&lt;/view_count&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;upload_time&gt;1121398533&lt;/upload_time&gt;  <b>&lt;!-- UNIX-style time, secs since 1/1/1970 --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_count&gt;1&lt;/comment_count&gt;  <b>&lt;!-- how many comments does this video have? --&gt;</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tags&gt;feature film documentary&lt;/tags&gt; <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;url&gt;http://www.kamtape.com/watch.php?v=k04Eeue24Lk&lt;/url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;thumbnail_url&gt;http://www.kamtape.com/get_still.php?video_id=k04Eeue24Lk&lt;/thumbnail_url&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>... another video ...</b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
&lt;/video_list&gt;<br>
";
$errorcodes = "";
break;
}
?>