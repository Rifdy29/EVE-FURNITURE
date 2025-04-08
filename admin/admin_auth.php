<?php
function initAdminSession($userId, $userName) {
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $userName;
    $_SESSION['user_type'] = 'admin';
    session_regenerate_id(true); // Prevent session fixation
}

function isAdminLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_type']) && 
           $_SESSION['user_type'] === 'admin';
}

function adminLogout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>