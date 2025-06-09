<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../config/db.php');

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM purchases ORDER BY purchase_date DESC, id DESC");
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายการวัตถุดิบที่ซื้อ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 รายการวัตถุดิบที่ซื้อ</h3>
    <a href="index.php" class="btn btn-success">➕ เพิ่มรายการใหม่</a>
  </div>

  <?php if (count($purchases) > 0): ?>
    <div class="table-responsive shadow rounded-4">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>#</th>
            <th>ชื่อวัตถุดิบ</th>
            <th>จำนวน</th>
            <th>หน่วย</th>
            <th>ราคา (บาท)</th>
            <th>วันที่ซื้อ</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($purchases as $index => $row): ?>
            <tr>
              <td class="text-center"><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($row['item_name']) ?></td>
              <td class="text-center"><?= $row['quantity'] ?></td>
              <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
              <td class="text-end"><?= number_format($row['price'], 2) ?></td>
              <td class="text-center"><?= date('d/m/Y', strtotime($row['purchase_date'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">ยังไม่มีรายการซื้อวัตถุดิบ</div>
  <?php endif; ?>
</div>

</body>
</html>
