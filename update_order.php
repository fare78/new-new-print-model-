<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
header("Location: login.php");
exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$orderId = $_POST['order_id'];
$newStatus = $_POST['new_status'];

// Update the order status
$stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $newStatus, $orderId);
$stmt->execute();
$stmt->close();

header("Location: admin.php"); // Redirect back to admin page
exit();
}
