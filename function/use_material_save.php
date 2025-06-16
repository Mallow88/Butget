<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../config/db.php';

$purchase_id   = $_POST['purchase_id'];
$used_quantity = $_POST['used_quantity'];
$num_people    = $_POST['num_people'];
$used_date     = $_POST['used_date'];

try {
    // ดึงข้อมูลวัตถุดิบ
    $stmt = $conn->prepare("SELECT quantity, unit_sub FROM purchases WHERE id = :id");
    $stmt->bindParam(':id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$purchase) {
        throw new Exception("ไม่พบข้อมูลวัตถุดิบ");
    }

    $quantity  = $purchase['quantity'];
    $unit_sub  = $purchase['unit_sub'];
    $total_sub = $unit_sub ? ($quantity * $unit_sub) : $quantity;

    // ดึงจำนวนที่ใช้ไปแล้ว
    $stmtUsed = $conn->prepare("SELECT SUM(used_quantity) as total_used FROM usage_logs WHERE purchase_id = :id");
    $stmtUsed->bindParam(':id', $purchase_id, PDO::PARAM_INT);
    $stmtUsed->execute();
    $usedRow = $stmtUsed->fetch(PDO::FETCH_ASSOC);
    $used_total = $usedRow['total_used'] ?? 0;

    $remaining = $total_sub - $used_total;

    if ($used_quantity > $remaining) {
        throw new Exception("จำนวนที่ใช้เกินกว่าคงเหลือ");
    }

    // บันทึกการใช้วัตถุดิบ
    $insert = $conn->prepare("
        INSERT INTO usage_logs (purchase_id, used_quantity, num_people, used_date)
        VALUES (:purchase_id, :used_quantity, :num_people, :used_date)
    ");
    $insert->bindParam(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $insert->bindParam(':used_quantity', $used_quantity, PDO::PARAM_INT);
    $insert->bindParam(':num_people', $num_people, PDO::PARAM_INT);
    $insert->bindParam(':used_date', $used_date);
    $insert->execute();

    // ตรวจสอบว่าวัตถุดิบหมดหรือยัง
    $new_used_total = $used_total + $used_quantity;
    if ($new_used_total >= $total_sub) {
        $updateStatus = $conn->prepare("UPDATE purchases SET status = 'หมดแล้ว' WHERE id = :id");
        $updateStatus->bindParam(':id', $purchase_id, PDO::PARAM_INT);
        $updateStatus->execute();
    }

    echo "<script>alert('บันทึกการใช้วัตถุดิบเรียบร้อยแล้ว'); window.location.href='../page/use_material.php';</script>";

} catch (Exception $e) {
    echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); history.back();</script>";
}
?>
