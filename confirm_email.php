<? require "needed/start.php";
force_login();
if(isset($_GET['next'])) {
$redirect = $_GET['next'];    
} else {
$redirect = '/index.php';
}

$fuckyou = $session['confirm_expire'];
$opening_date = new DateTime($session['confirm_expire']);
$current_date = new DateTime();

if($session['em_confirmation'] == 'false') {
if ($opening_date > $current_date) {
    if($_GET['cid'] == $session['confirm_id']) {
    $confirm_you = $conn->prepare("UPDATE users SET em_confirmation = 'true' WHERE uid = ?");
    $confirm_you->execute([$session['uid']]);
    session_error_index("Your email has been confirmed", "success", $redirect);
    } else {
    session_error_index("This confirmation link is no longer valid.", "error");
    }
} else {
session_error_index("This confirmation link is no longer valid.", "error");
} 
} else {
session_error_index("This confirmation link is no longer valid.", "error");    
}