<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../config/db.php');

// ตรวจสอบว่าได้ id มาหรือยัง
if (!isset($_GET['id'])) {
    echo "ไม่พบรหัสรายการที่จะแก้ไข";
    exit;
}

$id = $_GET['id'];

// ดึงข้อมูลจาก DB
$stmt = $conn->prepare("SELECT * FROM purchases WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$purchase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$purchase) {
    echo "ไม่พบข้อมูลรายการที่ต้องการแก้ไข";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขรายการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="mb-4">✏️ แก้ไขรายการวัตถุดิบ</h3>

    <form action="../function/update_purchase.php" method="POST" class="shadow p-4 rounded-4 bg-white">
        <input type="hidden" name="id" value="<?= $purchase['id'] ?>">

        <div class="mb-3">
            <label class="form-label">ชื่อวัตถุดิบ</label>
            <input type="text" name="item_name" class="form-control" required value="<?= htmlspecialchars($purchase['item_name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">จำนวน</label>
            <input type="number" name="quantity" class="form-control" min="1" required value="<?= $purchase['quantity'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">หน่วย (เช่น ก้อน, แพ็ค)</label>
            <input type="text" name="unit" class="form-control" required value="<?= htmlspecialchars($purchase['unit']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">หน่วยย่อย (เช่น ฟอง, ขวด)</label>
            <input type="number" name="unit_sub" class="form-control" placeholder="ไม่ระบุก็ได้" min="1"
                   value="<?= htmlspecialchars($purchase['unit_sub'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">ราคา (บาท)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?= $purchase['price'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">วันที่ซื้อ</label>
            <input type="date" name="purchase_date" class="form-control" required value="<?= $purchase['purchase_date'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">สถานะ</label>
            <select name="status" class="form-select">
                <option value="ยังไม่หมด" <?= $purchase['status'] === 'ยังไม่หมด' ? 'selected' : '' ?>>ยังไม่หมด</option>
                <option value="หมดแล้ว" <?= $purchase['status'] === 'หมดแล้ว' ? 'selected' : '' ?>>หมดแล้ว</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="list.php" class="btn btn-secondary">⬅ กลับ</a>
            <button type="submit" class="btn btn-primary">💾 บันทึกการแก้ไข</button>
        </div>
    </form>
</div>

</body>
</html>
