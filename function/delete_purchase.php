<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ไม่พบรหัสรายการ'); window.location.href='../page/list.php';</script>";
    exit;
}

$id = $_GET['id'];

try {
    // ลบรายการหลักได้เลย เพราะ foreign key ตั้ง ON DELETE CASCADE แล้ว
    $stmt = $conn->prepare("DELETE FROM purchases WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo "<script>alert('ลบรายการเรียบร้อยแล้ว'); window.location.href='../page/list.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); window.location.href='../page/list.php';</script>";
}
