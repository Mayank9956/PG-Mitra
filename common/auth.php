<?php

// Start session automatically
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load DB
require_once __DIR__ . '/db_connect.php';

// Initialize DB connection
$database = new Database();
$conn = $database->getConnection();


// ==============================
// GET AUTH USER
// ==============================
function getAuthUser($conn) {

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // User not found (deleted case)
    if ($result->num_rows === 0) {
        session_unset();
        session_destroy();

        header("Location: /login.php");
        exit;
    }

    return $result->fetch_assoc();
}


// ==============================
// REQUIRE LOGIN (PROTECTED PAGES)
// ==============================
function requireAuth($conn) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }

    return getAuthUser($conn);
}


// ==============================
// GUEST ONLY (LOGIN / REGISTER)
// ==============================
function redirectIfLoggedIn() {

    if (isset($_SESSION['user_id'])) {
        header("Location: /home.php");
        exit;
    }
}


// ==============================
// OPTIONAL HELPER (CLEAN NAME)
// ==============================
function guestOnly() {
    redirectIfLoggedIn();
}


// ==============================
// AUTO GLOBAL USER (OPTIONAL)
// ==============================
$user = null;

if (isset($_SESSION['user_id'])) {
    $user = getAuthUser($conn);
}

?>