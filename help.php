<?php
require "needed/start.php";
if($session['staff'] == 1 && isset($_POST['field_qa'])) {
  $sql = "INSERT INTO questions_and_answers (question, answer) VALUES (:question, :answer)";

  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":question", $_POST['field_qa']);
  $stmt->bindParam(":answer", $_POST['field_qa_answer']);
  
  try {
    $stmt->execute();
    $posted = 1;
  } catch (PDOException $e) {
    alert("Failed to submit.", "error");
    exit;
  }
}

$stmt = $conn->query("SELECT * FROM questions_and_answers ORDER BY id ASC");
$rowCount = $stmt->rowCount(); 

if(isset($_POST['player_option'])) {
if($_POST['player_option'] == 'default') { $_SESSION['flash'] = 0; $_SESSION['js'] = 0; }
if($_POST['player_option'] == 'flash') { $_SESSION['flash'] = 1; $_SESSION['js'] = 0; }
if($_POST['player_option'] == 'js') { $_SESSION['flash'] = 0; $_SESSION['js'] = 1; }
alert("Videos will now be displayed in the '".$_POST['player_option']."' version of the player.");
}
?>

<div class="tableSubTitle">Help</div>
<?php if ($posted == 1) {
    alert("You have just answeed a question!");
}
?>

<div class="pageTable">
<?php
	// Loop through the results and display each post
	$currentRow = 1; // Variable to keep track of the current row
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$question = $row['question'];
        $answer = $row['answer'];
	?>
    <span class="highlight">Q: <?php echo $question; ?></span>

<br><br>A: <?php echo $answer; ?> 
<? if($row['id'] == 17) {?><p>
    <div class="SubTitle">Select the player that videos should be streamed on. <span style="font-size: 12px; font-weight: normal; color: #000000;">(suggested by <a href="/user/brightsoft">brightsoft</a>, <a href="/user/RysieQu">RysieQu</a>, amongst others)</span></div>
    <div class="pageTable">
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
	<form method="post">
	<tbody>
    </tr><tr>
    </tr>
	<tr><input type="radio" id="default" name="player_option" value="default"<? if ($_SESSION['flash'] != 1 && $_SESSION['js'] != 1) { echo "checked"; } ?>><label for="default">Let KamTape choose (Reccomended)</label></tr>
	<tr><br><input type="radio" id="flash" name="player_option" value="flash"<? if ($_SESSION['flash'] == 1 && $_SESSION['js'] != 1) { echo "checked"; } ?>><label for="flash">Stream videos with Macromedia Flash.</label></tr>
	<tr><br><input type="radio" id="js" name="player_option" value="js"<? if ($_SESSION['flash'] != 1 && $_SESSION['js'] == 1) { echo "checked"; } ?>><label for="js">I do not have Flash, I would like to stream videos with HTML5</label></tr>
	<tr>
		<td><input type="submit" value="Change Player"></td></form>
	</tr>
    
</tbody></table>

</div>
    <? } ?>
<?php if ($currentRow !== $rowCount) { ?>
<br/>
<br/>
	<?php }
	$currentRow++; // Increment the current row counter
	?>
    <?php }
	?>

<br><br><br><span class="highlight">Contact KamTape</span>
<br><br>If you have any account or video issues, please contact us <a href="contact.php">here</a>.
Also, if you have any ideas or suggestions to make our service better, please don't hesitate to drop us a line.
<?php if($session['staff'] == 1) { ?>
   <br><br><br>
   <div class="pageTable">
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
	<form method="post">
	<tbody>
    <td width="200" align="right"><span class="highlight">Help Answer The Community!</span></td>
    <tr>
		<td width="200" align="right"><span class="label">Q:</span></td>
		<td><input type="text" size="30" maxlength="350" name="field_qa" placeholder="How long can my video be?"></td>
	</tr>
	<tr>
		<td align="right" valign="top"><span class="label">A:</span></td>
		<td><textarea name="field_qa_answer" cols="40" rows="4" placeholder="There is no time limit on your video, but the video file you upload must be less than 100 MB in size."></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Answer Now"></td>
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
