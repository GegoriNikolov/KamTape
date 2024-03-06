<?php
function isValidUrl($url) {
    // Use filter_var to validate the URL
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

if (isset($_GET['u'])) {
    $targetUrl = $_GET['u'];

    // Validate the URL
    if (isValidUrl($targetUrl)) {
        // Sanitize the URL for use in the Location header
        $sanitizedUrl = htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8');

        // Perform the redirect
        header("Location: $sanitizedUrl");
        exit();
    } else {
        die();
    }
} else {
    die();
}
?>
