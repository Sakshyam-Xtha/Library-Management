<?php
session_start();

// Check if the theme was sent via the POST request
if (isset($_POST['theme'])) {
    // Save 'light' or 'dark' into the session array
    $_SESSION['theme'] = $_POST['theme'];
}
?>