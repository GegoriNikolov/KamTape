<?php
require "needed/start.php";
?>
<div class="tableSubTitle">Developers</div>

<span class="highlight">Introduction</span>

<div style="margin-left: 10px; margin-right: 10px; padding-top: 10px; padding-bottom: 10px;">
	KamTape is very excited to announce the availability of our XML APIs.
	<br><br>
	Are you a developer? Do you wish to integrate online videos into your application?
	<br>
	<br>
	With the set of KamTape XML APIs, developers can integrate applications directly into KamTape's growing repository of videos. The API calls will allow you to conduct tag searches, fetch video details, list videos by user, etc. Furthermore, it is a fundamental belief of ours that web services and web-enabled desktop applications will be paving the road of future software. Along these beliefs, we will be putting a tremendous amount of effort into enhancing our APIs as well as assisting 3rd party developers in every possible way.
</div>

<span class="highlight">Getting Started</span>
<div style="margin-left: 10px; margin-right: 10px; padding-top: 10px; padding-bottom: 10px;">
	To get started, you will first have to create an account. After doing so, you must register for developer access through the <a href="my_profile_dev.php">developer profile</a>. 
	Upon completion of the developer registration process, you will be given a developer id.  This developer id will be sent along for all your XML API requests.
</div>

<span class="highlight">Documentation and Sample Code</span>
<div style="margin-left: 10px; margin-right: 10px; padding-top: 10px; padding-bottom: 10px;">
	To make an API call, you will need to send an XML request to this URL:<br/>
	<br>
	<div class="codeArea">http://www.kamtape.com/api/ut_api</div>

	There are currently two API calls: <i>sequence_request</i> and <i>get_video_details</i>
	<div class="apiLabel">
		<span class="nav">sequence_request</span>
		<br>
		<br>
		The sequence request call is used to retrieve a sequence of videos. This is the typical entry point into the system. You can conduct a search by tag or username.
	</div>

	<div class="codeArea">
	&lt;ut_request&gt;<br/>
	&nbsp;&nbsp;&lt;request_type&gt;get_video_details_request&lt;/request_type&gt;<br/>
	&nbsp;&nbsp;&lt;requester&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;type&gt;dev&lt;/type&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;YOUR_DEVELOPER_ID&lt;/id&gt;<br/>
	&nbsp;&nbsp;&lt;/requester&gt;<br/>
	&nbsp;&nbsp;&lt;request&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;type&gt;tag|user&lt;/type&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;query&gt;tag|username&lt;/query&gt;<br/>
	&nbsp;&nbsp;&lt;/request&gt;<br/>
	&lt;/ut_request&gt;<br/>
	</div>

	<div class="apiLabel">
		<span class="nav">sequence_response</span>
		<br>
		<br>
		The sequence response represents a sequence of videos that meet the sequence request query. All sequence requests, regardless of request type, will have its videos encapsulated in a sequence response. With the video id that is passed back, you can hit the KamTape website at http://www.kamtape.com/watch.php?v=VIDEO_ID replacing VIDEO_ID with the video id returned; this will enable you to play the video through a browser.
	</div>

	<div class="codeArea">
	&lt;ut_response&gt;<br/>
	&nbsp;&nbsp;&lt;response_type&gt;sequence_response&lt;/response_type&gt;<br/>
	&nbsp;&nbsp;&lt;response&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;sequence_overview&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;sequence&nbsp;title&lt;/title&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length&gt;number&nbsp;of&nbsp;items&nbsp;in&nbsp;sequence&lt;/length&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;md5_sum&gt;md5&nbsp;sum&nbsp;of&nbsp;the&nbsp;sequence&lt;/md5_sum&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/sequence_overview&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;sequence_items&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;sequence_item&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;video&nbsp;id&lt;/id&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;author&nbsp;name&lt;/author&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;movie&nbsp;title&lt;/title&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;keywords&gt;video&nbsp;tags&lt;/keywords&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;video&nbsp;description&lt;/description&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;date_uploaded&gt;upload&nbsp;date&lt;/date_uploaded&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;number&nbsp;of&nbsp;times&nbsp;viewed&lt;/view_count&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment_count&gt;number&nbsp;of&nbsp;comments&lt;/comment_count&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/sequence_item&gt;<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/sequence_items&gt;<br/>
	&nbsp;&nbsp;&lt;/response&gt;<br/>
	&lt;/ut_response&gt;<br/>
	</div>

	<div class="apiLabel">
		<span class="nav">get_video_details_request</span>
		<br>
		<br>
	The get video details request is used to fetch all the details about a given video id.  Generally, you'd use the <i>sequence request</i> call to fetch the videos of interest. Then, you'd turn around and call the <i>get video details</i> to fetch the details of a video.
	</div>

	<div class="codeArea">
	&lt;ut_request&gt;<br>
	&nbsp;&nbsp;&lt;request_type&gt;get_video_details_request&lt;/request_type&gt;<br>
	&nbsp;&nbsp;&lt;requester&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;type&gt;dev&lt;/type&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;YOUR_DEVELOPER_ID&lt;/id&gt;<br>
	&nbsp;&nbsp;&lt;/requester&gt;<br>
	&nbsp;&nbsp;&lt;request&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_id&gt;video&nbsp;id&lt;/video_id&gt;<br>
	&nbsp;&nbsp;&lt;/request&gt;<br>
	&lt;/ut_request&gt;<br>
	</div>

	<div class="apiLabel">
		<span class="nav">get_video_details_response</span>
		<br>
		<br>
	The get video details response will return all the public information about a given video.
	</div>

	<div class="codeArea">
	&lt;ut_response&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;response_type&gt;get_video_details&lt;/response_type&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;response&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;public&gt;&lt;/public&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;view_count&gt;&lt;/view_count&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;vote_count&gt;&lt;/vote_count&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;vote_sum&gt;&lt;/vote_sum&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;related_videos_by_author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_id&gt;other&nbsp;video&nbsp;by&nbsp;author&nbsp;id&nbsp;1&lt;/video_id&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_id&gt;other&nbsp;video&nbsp;by&nbsp;author&nbsp;id&nbsp;2&lt;/video_id&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video_id&gt;other&nbsp;video&nbsp;by&nbsp;author&nbsp;id&nbsp;3&lt;/video_id&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/related_videos_by_author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;related_tags&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tag&gt;related&nbsp;tag&nbsp;1&lt;/tag&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tag&gt;related&nbsp;tag&nbsp;2&lt;/tag&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/related_tags&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;username&gt;author username&lt;/username&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;video&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;movie&nbsp;title&lt;/title&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;length&gt;length in seconds&lt;/length&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;description&gt;this&nbsp;is&nbsp;the&nbsp;description&nbsp;for&nbsp;the&nbsp;video&lt;/description&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;time_created&gt;July 13, 2005&lt;/time_created&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/video&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comments&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;time_created&gt;July 14, 2005&lt;/time_created&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;username&lt;/author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;this&nbsp;is&nbsp;a&nbsp;comment&lt;/comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;time_created&gt;July 15, 2005&lt;/time_created&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;username&lt;/author&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;this&nbsp;is&nbsp;a&nbsp;comment&lt;/comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/comment&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/comments&gt;<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;/response&gt;<br>
	&lt;/ut_response&gt;<br>
	</div>
</div>

<br><br><br><span class="highlight">Contact KamTape</span>
<br><br>If you have any questions regarding the developer program, please contact us <a href="contact.php">here</a>. Also, please <a href="contact.php">contact</a> us if you have suggestions on how to improve our API services.

		</div>
		</td>
	</tr>
</table>
<?php
require "needed/end.php";
?>