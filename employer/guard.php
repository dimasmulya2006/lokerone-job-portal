<?php
// Shared guard for all employer pages
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'company') {
    header("Location: ../login.php");
    exit;
}

// Fetch company linked to this user
$stmt = $pdo->prepare("SELECT c.*, c.NAME as name FROM companies c LEFT JOIN users u ON c.id = u.company_id WHERE u.id = :uid");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$company = $stmt->fetch();

if (!$company) {
    die("Error: Akun perusahaan Anda belum terhubung. Silakan hubungi admin.");
}
$companyId = $company['id'];
