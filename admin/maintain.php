<?php
require "admin_head.php";

force_login();
$_KAMTAPECONF = $conn->prepare('UPDATE kamtape_web SET maintenance = 0 WHERE version = ?');
$_KAMTAPECONF->execute([$version_of_tape]);