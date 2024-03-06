<?php
require "needed/start.php";
?>
<div class="tableSubTitle">About Us</div>

<div class="pageTable">
<span class="highlight">About KamTape</span>
<br><br>
KamTape is our love letter to the original version of the red video sharing website. Founded in mid 2005 by people who wanted to recreate the experience of the early red video sharing website, KamTape allows you to 
easily upload, tag, and share personal video clips through <a href="/">www.KamTape.com</a>, and share it across the Internet on other sites, blogs and through e-mail, as well as to create your own personal video network.
<p>We've attempted to recreate the original experience down to a T with missed features such as Favorites, Star Ratings, Messaging, and more. In fact, for a truly authentic experience, we've managed to patch up the original flash files they've used, including the addressbook, and video player.
<br>Welcome home, it's just like when you first found it. With support from the people who have found this site along their way, and donations from supporters, and the utmost dedication to usability and accuracy, KamTape is set to become the Internet's premier retro video service.
<br><br>
<span class="highlight">What is KamTape?</span>

<br><br>
KamTape is the way to get your videos to the people who matter to you. With KamTape you can:

<ul>
<li> Show off your favorite videos to the world
<li> Take videos of your dogs, cats, and other pets
<li> Blog the videos you take with your digital camera or cell phone
<li> Securely and privately show videos to your friends and family around the world
<li> ... and much, much more!
</ul>
<?php if(empty($_SESSION['uid'])) { ?>
<br><span class="highlight"><a href="signup.php">Sign up now</a> and open a free account.</span>
<br><br> <?php } ?><br>

To learn more about our service, please see our <a href="help.php">Help</a> section.<br><br>

Please feel free to <a href="contact.php">contact us</a>.

<br><br><span class="highlight">Thank You!</span>
<ul>
<li><strong><a href="/profile.php?user=jr">jr</a></strong> - Current owner and main developer of the website.</li>
<li><strong><a href="profile.php?user=pi">pi</a></strong> - Second main developer and is responsible for the API and by extension most of the patched Flash stuff.</li>
<li><strong><a href="profile.php?user=BoredWithADHD">BoredWithADHD</a></strong> & <strong><a href="profile.php?user=furgotten12">furgotten12</a></strong> - BoredWithADHD envisioned most of the branding you still see today + furgotten12 made a bunch of high quality logos!</li>
<li><strong><a href="profile.php?user=purpleblaze">purpleblaze</a></strong> - Recreates the Flash player very accurately for people who do not have Flash.</li>
</ul>

<?php
require "needed/end.php";
?>