<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../config/db.php';

$item_name = $_POST['item_name'];
$quantity = $_POST['quantity'];
$unit = $_POST['unit'];
$price = $_POST['price'];
$purchase_date = $_POST['purchase_date'];
$unit_sub = !empty($_POST['unit_sub']) ? $_POST['unit_sub'] : null;
$status = 'ยังไม่หมด';

try {
    $sql = "INSERT INTO purchases (item_name, quantity, unit, price, purchase_date, status, unit_sub)
            VALUES (:item_name, :quantity, :unit, :price, :purchase_date, :status, :unit_sub)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':unit', $unit);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':purchase_date', $purchase_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':unit_sub', $unit_sub, PDO::PARAM_INT);
    $stmt->execute();

    echo "<script>alert('บันทึกรายการเรียบร้อยแล้ว'); window.location.href='../page/list.php';</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
