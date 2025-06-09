<?php
include '../db/db.php';

$item_name = $_POST['item_name'];
$quantity = $_POST['quantity'];
$unit = $_POST['unit'];
$price = $_POST['price'];
$purchase_date = $_POST['purchase_date'];

$sql = "INSERT INTO purchases (item_name, quantity, unit, price, purchase_date)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sidsd", $item_name, $quantity, $unit, $price, $purchase_date);

if ($stmt->execute()) {
    echo "<script>alert('บันทึกรายการเรียบร้อยแล้ว'); window.location.href='../page/index.php';</script>";
} else {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
}

$conn->close();
?>
