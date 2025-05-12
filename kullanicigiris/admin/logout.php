<?php
session_start();

// Oturum verilerini temizle
$_SESSION = array();

// Oturum çerezini temizle
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Oturumu sonlandır
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendir
header('Location: ../admin-login.php');
exit;
?> 