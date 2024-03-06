<?php 
require "needed/start.php";
email_confirm();

if($_SERVER["REQUEST_METHOD"] != "POST") { header("Location: my_videos_upload.php"); }
if (!isset($_POST['field_upload_description'], $_POST['field_upload_tags'], $_POST['field_upload_title'])) { header("Location: my_videos_upload.php"); }
$word_count = unique_word_count($_POST['field_upload_tags']);
if ($word_count < 1) {
  redirect("my_videos_upload.php?tags");
}

if ($word_count < 2 && strlen($_POST['field_upload_tags']) > 28) {
  redirect("my_videos_upload.php?tags");
}

if ($word_count > 2) {
  $tags = explode(' ', $_POST['field_upload_tags']);

foreach ($tags as $tag) {
    if (strlen($tag) > 35) {
        redirect("my_videos_upload.php?tags");
    }
}
}


if (strlen($_POST["field_upload_title"]) < 2) {
   redirect("my_videos_upload.php?title"); 
}

if (strlen($_POST["field_upload_title"]) > 100) {
   redirect("my_videos_upload.php?title"); 
}

if (empty($_POST["field_upload_description"])) {
   redirect("my_videos_upload.php?desc"); 
}

if (strlen($_POST["field_upload_description"]) > 1000) {
   redirect("my_videos_upload.php?title"); 
}

if (isset($_POST['chlist'])) {
        $checked_channels = $_POST['chlist'];
        $checked_channels = array_slice($checked_channels, 0,3);
    } else {
        redirect("my_videos_upload.php?tags");
    }
if(isset($_POST['addr_yr'], $_POST['addr_day'], $_POST['addr_month'])) {

    // Validate input values
    if (is_numeric($_POST['addr_yr']) && is_numeric($_POST['addr_day']) && is_numeric($_POST['addr_month'])) {
        // Check if the input values represent a valid date
        if (checkdate($_POST['addr_month'], $_POST['addr_day'], $_POST['addr_yr'])) {
            // Create a DateTime object with the given values
            $date = DateTime::createFromFormat('Y-m-d', $_POST['addr_yr'] . '-' . $_POST['addr_month'] . '-' . $_POST['addr_day']);
            
            if ($date !== false) {
                $formattedDate = $date->format('Y-m-d');
            } else {
               header("Location: my_videos_upload.php");
            }
        } else {
            header("Location: my_videos_upload.php");
        }
    } else {
    }
}
?>
<script>
function UploadHandler()
{
	var upload_button = document.uploader.upload;

    var fileInput = document.getElementById("fileToUpload");
    
    var go_ahead = 1;

    if (fileInput.files.length === 0) {
        var go_ahead = 0;
        alert("It looks like you didn't choose any file.");
    }
    if (go_ahead === 1) {
	upload_button.disabled='true';
	upload_button.value='Uploading...';

	return true;
    } else {
    upload_button.value='Try Again';
    return false; // Make sure it doesn't process the request
    }
}
</script>

	<div class="tableSubTitle">Video Upload (Step 2 of 2)</div>
	<div class="pageTable">
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<form method="post" action="my_videos_post.php" enctype="multipart/form-data" id="uploader" name="uploader" onsubmit="return UploadHandler();">

<input type="hidden" name="field_upload_title" value="<?php echo htmlspecialchars($_POST["field_upload_title"]); ?>" hidden>
<input type="hidden" name="field_upload_description" value="<?php echo htmlspecialchars($_POST["field_upload_description"]); ?>" hidden>
<input type="hidden" name="field_upload_tags" value="<?php echo htmlspecialchars($_POST["field_upload_tags"]); ?>" hidden>
<?php if(isset($formattedDate)) { ?>
<input type="hidden" name="addr_date" value="<?php echo htmlspecialchars($formattedDate); ?>" hidden>
<?php } ?>
<? if(isset($_POST['field_upload_country'])) { ?>
<input type="hidden" name="field_upload_country" value="<?php echo htmlspecialchars($_POST['field_upload_country']); ?>" hidden>
<?php } ?>
<?php if(isset($_POST['field_upload_country'])) { ?>
<input type="hidden" name="field_upload_address" value="<?php echo htmlspecialchars($_POST['field_upload_address']); ?>" hidden>
<?php } ?>
<? foreach ($checked_channels as $index => $channel) {
    $channel = str_replace("ch", "", $channel);
    if($channel < 22) {
    echo '<input type="hidden" name="field_upload_ch'.$index.'" value="'.$channel.'" hidden>';
    }
        } ?>
<div style="display: none">
	<tr>
		<td width="200" align="right" valign="top"><span class="label">File:</span></td>
		<td>
		<div width="595" height="20" cellpadding="0" border="0" bgcolor="#E5ECF9" class="formHighlight">
			<input type="file" style="margin-bottom: 3px" id="fileToUpload" name="fileToUpload" accept="video/mp4,video/x-m4v,video/*"><br>
			<span class="formHighlightText"><b>Max file size: 100 MB. No copyrighted or obscene material.</b></span><br>
			<span class="formHighlightText">After uploading, you can edit or remove this video at anytime under the "My Videos" link on top of the page.</span>
		</div>

	<tr>
		<td width="200" align="right" valign="top"><span class="label">Broadcast:</span></td>
		<td>

                <input type="radio" name="private" value="1" checked>
                <label for="1"><strong>Public</strong>: Share your video with the world! (Reccomended)</label><br>
                <input type="radio" name="private" value="2">
                <label for="2"><strong>Private</strong>: Only viewable by you and the people you specify.</label><br>
		</td>
</table>
<br>
<div style="margin-left: 220px">
	<b>PLEASE BE PATIENT, THIS MAY TAKE SEVERAL MINUTES. <br> ONCE COMPLETED, YOU WILL SEE A CONFIRMATION MESSAGE.</b>
	<br><br>
	<input type="submit" value="Upload Video" name="upload" id="upload">
</div>
<br><br>
</form>
		</table>
	</div>
</div>
<?php 
require "needed/end.php";
?>