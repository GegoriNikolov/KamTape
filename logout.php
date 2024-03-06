<?php
//how come this has gone unnoticed all this time
session_start();
session_destroy();
if(isset($_GET['next']) && !empty($_GET['next'])) {
    if (strpos($_GET['next'], "/admin/") === 0) {
    header("Location: /");
    } else {
    header("Location: ".$_GET['next']);    
    }    
} else {
    header("Location: /");
}

?>