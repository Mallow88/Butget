<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_id = $_POST['purchase_id'];
    $used_date = $_POST['used_date'];
    $portion_note = $_POST['portion_note'] ?? null;

    $stmt = $conn->prepare("INSERT INTO usage_logs (purchase_id, used_date, portion_note) VALUES (?, ?, ?)");
    $stmt->execute([$purchase_id, $used_date, $portion_note]);

    header("Location: ../page/use_material.php");
    exit;
}
?>
