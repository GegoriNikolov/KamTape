<?php
function handle_fatal_error() {
    $error = error_get_last();
    if (is_array($error)) {
        $errorCode = $error['type'] ?? 0;
        $errorMsg = $error['message'] ?? '';
        $file = $error['file'] ?? '';
        $line = $error['line'] ?? null;

        if ($errorCode == E_ERROR) {
            handle_error();
        }
    }
}
function handle_error() {
    header("Location: /error.html");
}
set_error_handler("handle_error", E_ERROR);
  register_shutdown_function('handle_fatal_error');
shit();
?>
hello world
