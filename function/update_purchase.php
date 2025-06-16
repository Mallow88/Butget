<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../config/db.php';

$id = $_POST['id'];
$item_name = $_POST['item_name'];
$quantity = $_POST['quantity'];
$unit = $_POST['unit'];
$unit_sub = !empty($_POST['unit_sub']) ? $_POST['unit_sub'] : null;
$price = $_POST['price'];
$purchase_date = $_POST['purchase_date'];
$status = $_POST['status'];

try {
    $sql = "UPDATE purchases SET 
                item_name = :item_name,
                quantity = :quantity,
                unit = :unit,
                unit_sub = :unit_sub,
                price = :price,
                purchase_date = :purchase_date,
                status = :status
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':unit', $unit);
    $stmt->bindParam(':unit_sub', $unit_sub);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    echo "<script>alert('บันทึกการแก้ไขเรียบร้อยแล้ว'); window.location.href='../page/list.php';</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
