<?php
include '../config/db.php';

// ดึงเฉพาะรายการที่หมดแล้ว
$stmt = $conn->prepare("SELECT * FROM purchases WHERE status = 'หมดแล้ว' ORDER BY purchase_date DESC");
$stmt->execute();
$finishedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงจำนวนวันทั้งหมดที่เคยใช้วัตถุดิบ (ไม่ซ้ำกัน)
$dayCountStmt = $conn->prepare("
    SELECT COUNT(DISTINCT used_date) AS used_days,
           MIN(used_date) AS first_date,
           MAX(used_date) AS last_date
    FROM usage_logs
");
$dayCountStmt->execute();
$dayData = $dayCountStmt->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูล usage รวมทั้งหมด
$summaryStmt = $conn->prepare("
    SELECT 
        COUNT(*) AS total_meals,
        SUM(used_quantity) AS total_used,
        SUM(num_people) AS total_people
    FROM usage_logs
");
$summaryStmt->execute();
$usage = $summaryStmt->fetch(PDO::FETCH_ASSOC);

// คำนวณราคารวมเฉพาะรายการที่หมดแล้ว
$totalPrice = 0;
foreach ($finishedItems as $item) {
    $totalPrice += $item['price'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สรุปวัตถุดิบที่หมดแล้ว</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>📌 สรุปวัตถุดิบที่หมดแล้ว</h3>
      <a href="list.php" class="btn btn-secondary">ย้อนกลับ</a>
    </div>

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm text-center p-3">
          <h5>📆 ใช้วัตถุดิบทั้งหมด</h5>
          <p>🗓 <strong><?= $dayData['used_days'] ?? 0 ?> วัน</strong></p>
          <p>📍 ตั้งแต่: <?= date('d/m/Y', strtotime($dayData['first_date'])) ?> ถึง <?= date('d/m/Y', strtotime($dayData['last_date'])) ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm text-center p-3">
          <h5>🍽 สถิติการใช้</h5>
          <p>มื้อที่ใช้: <strong><?= $usage['total_meals'] ?? 0 ?></strong></p>
          <p>หน่วยย่อยที่ใช้: <strong><?= $usage['total_used'] ?? 0 ?></strong></p>
          <p>คนที่กินรวม: <strong><?= $usage['total_people'] ?? 0 ?></strong></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm text-center p-3">
          <h5>💸 ราคารวมวัตถุดิบที่หมดแล้ว</h5>
          <p><strong><?= number_format($totalPrice, 2) ?> บาท</strong></p>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mb-5">
      <div class="card-header bg-danger text-white">
        รายการวัตถุดิบที่หมดแล้ว (<?= count($finishedItems) ?> รายการ)
      </div>
      <div class="table-responsive">
        <table class="table table-bordered mb-0 text-center align-middle">
          <thead class="table-light">
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
            <?php foreach ($finishedItems as $index => $item): ?>
              <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= htmlspecialchars($item['unit']) ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= date('d/m/Y', strtotime($item['purchase_date'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($finishedItems) === 0): ?>
              <tr><td colspan="6" class="text-muted">ยังไม่มีวัตถุดิบที่หมด</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
