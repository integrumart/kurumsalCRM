<?php
require_once 'config.php';

if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', null, null, 'Kullanıcı çıkış yaptı');
}

session_destroy();
redirect('login.php');
?>
