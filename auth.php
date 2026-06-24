<?php
// auth.php
// Helper Autentikasi dan Manajemen Sesi Admin

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Mengecek apakah admin sudah login.
 * Jika belum, redirect ke halaman login.
 */
function check_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Melakukan proses login admin.
 */
function login($username, $password, $conn) {
    $username = mysqli_real_escape_string($conn, $username);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND role = 'admin'");
    
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);
        // SQL dump menggunakan password polos (tidak dihash) 'admin123'
        // Kita bandingkan secara langsung sesuai dengan struktur database bawaan
        if ($password === $user['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id_user'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_nama'] = $user['nama'];
            return true;
        }
    }
    return false;
}

/**
 * Melakukan logout admin.
 */
function logout() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
