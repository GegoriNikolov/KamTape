<?php
// Set the HTTP response code to 404 Not Found
http_response_code(404);
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL <?php echo $_SERVER['REQUEST_URI']; ?> was not found on this server.</p>
<hr>
<address>Apache Server at <?php echo $_SERVER['HTTP_HOST']; ?> Port <?php echo $_SERVER['SERVER_PORT']; ?></address>
</body></html>
