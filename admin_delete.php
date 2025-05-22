<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$applicant_id = $_GET['id'] ?? 0;

if ($applicant_id) {
    $stmt = $conn->prepare("DELETE FROM applicants WHERE id = ?");
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: admin.php');
exit;
?>