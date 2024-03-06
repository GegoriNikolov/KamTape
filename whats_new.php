<?php
require "needed/start.php";
require "includes/Parsedown.php";

if($session['staff'] == 1 && isset($_POST['field_blog_content'])) {
  // Prepare the SQL query to insert a new blog post
    $no_hook_html = "";
    $_POST['field_blog_content'] = preg_replace_callback('/<no_hook>(.*?)<\/no_hook>/s', function($matches) use (&$no_hook_html) {
    $no_hook_html = $matches[1];
    return "";
    }, $_POST['field_blog_content']);

    $sql = "INSERT INTO blog (title, content, author, id) VALUES (:title, :content, :author, :id)";

    $Parsedown = new Parsedown();
    $content_html = $Parsedown->text($_POST['field_blog_content']);
    $content_html .= $no_hook_html;
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":title", $_POST['field_blog']);
    $stmt->bindParam(":content", $content_html);
    $stmt->bindParam(":author", $session['username']);
    $stmt->bindParam(":id", generateId());
  
  // Execute the prepared statement
  try {
    $stmt->execute();
    $posted = 1;
  } catch (PDOException $e) {
    alert("Was unable to blog.", "error");
  }
}

$stmt = $conn->query("SELECT * FROM blog ORDER BY posted DESC");

if($session['staff'] == 1 && isset($_GET['stuff_todelte'])) {
$poof= $conn->prepare("DELETE FROM blog WHERE id = :id");
$poof->execute([
		":id" => $_GET['stuff_todelte'],
	]);
 alert("Post deleted.", "error");
 }
?>

<?php if ($posted == 1) {
if(invokethConfig("maintenance") == 1) {
$role_ping = "nothing";
} else {
$role_ping = "<@&1095508549242716181> ";
}
$contents = strip_tags($_POST['field_blog_content']);
$contents = preg_replace('/(?<!\\\\)\^/', '-', $contents);
$contents = str_replace('\\^', '^', $contents);
if(empty($_POST['field_blog'])) {
$msg = $role_ping." *Updates from the KamTape Blog*\n".$contents;
} else {
$msg = $role_ping.$_POST['field_blog']."\n".$contents;
}

$json_data = array ('content'=>"$msg");
$make_json = json_encode($json_data);
$ch = curl_init("https://discord.com/api/webhooks/1181024608862863400/WJq733VxDE63HCIBsrwCUffoYodApH6afFURRD1fGyRevl5laCfyNEmOpCR5-39CCkAJ");
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec( $ch );
alert("You've just blogged!");
    
}
?>

<div class="pageTable">
<?php
	// Loop through the results and display each post
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
		$title = $row['title'];
        $content = $row['content'];
        $date = $row['posted'];
        $author = $row['author'];
        $content = preg_replace('/(?<!\\\\)\^/', '<li>', $content);
        $content = str_replace('\\^', '^', $content);
	?>
   <div class="tableSubTitle"><?php echo retroDate($date); ?></div>
<div style="padding: 0px 15px 30px 15px;">
<? if (!empty($title)) { ?>
<br>
<b><?php echo htmlspecialchars($title); ?></b>
<br>
<br>
<? } ?>
<?php echo nl2br($content); ?><? if($session['staff'] == 1) {?><p>(<a href="/whats_new?stuff_todelte=<?= $id ?>">Delete?</a>)<? } ?>
</div> 
<? } ?>
<?php if($session['staff'] == 1) { ?>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let TypedContent = document.getElementsByName('field_blog_content')[0];

    let previewDiv = document.getElementById('preview_blog_text');

    TypedContent.addEventListener('input', function () {
        let content = TypedContent.value;
        content = content.replace(/(?<!\\)\^/, '<li>');
        content = content.replace(/\\(\^)/, '^');
        content = content.replace(/\n/g, '<br>');
        content = marked.parse(content);
        if(!content) {
        content = 'As you type into the textbox, the update will appear here.';
        }
        previewDiv.innerHTML = content;
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let inputField = document.getElementsByName('field_blog')[0];
    let previewDiv = document.getElementById('preview_blog_title');
    inputField.addEventListener('input', function () {
        let title = inputField.value;
        previewDiv.innerText = title;
    });
});
</script>
   <div class="tableSubTitle">Update</div>
   <div class="pageTable">
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
    <strong>Tips:</strong>
    <li>Blog posts use Markdown (for webhook compatibility.)</li>
    <li>However, to create a list bullet, you should put in a <a href="https://en.wikipedia.org/wiki/Caret?useskin=monobook">caret</a> (<strong>^</strong>).</li>
    <li>To escape the bullet like in ":^)", just put a backslash before the caret.</li>
    <li>Then :\^) will turn into :^) when the blog is published.</li>
    <li>As you type, the "Preview Blog Post" box below will show your blog post exactly how it will be upon publication on the website.</li>
    <li>Save discord the trouble and if you really have to use js and html, encase them in &lt;no_hook&gt; tags :P</li>
    <p>
    <div class="codeArea">
    <div class="highlight">Preview Blog Post
    </div>
    <p><strong><span id='preview_blog_title'></span></strong>
    <div id="preview_blog_text">As you type into the textbox, this box will show how your update will look after being published on the site.</div></div>
	<form method="post">
	<tbody>
    <tr>
		<td width="200" align="right"><span class="label">Title:</span></td>
		<td><input type="text" size="30" maxlength="60" name="field_blog" placeholder="What's popping?"></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label"><span style="color:#f22b33;">*</span>&ensp;Content:</span></td>
		<td><textarea name="field_blog_content" cols="40" rows="4" placeholder="Tell us what's new."></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Post Blog"></td>
	</tr>
    </form>
</tbody></table>

</div>
    </div>
  <?php } ?>			
</div>
<?php
require "needed/end.php";
?>