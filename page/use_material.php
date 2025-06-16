<?php
include '../config/db.php';

$stmt = $conn->prepare("SELECT * FROM purchases WHERE status = 'ยังไม่หมด' ORDER BY purchase_date DESC");
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usageStmt = $conn->prepare("
  SELECT purchase_id, 
         COUNT(*) AS meals_used, 
         SUM(used_quantity) AS total_used,
         SUM(num_people) AS total_people
  FROM usage_logs 
  GROUP BY purchase_id
");
$usageStmt->execute();
$usageData = $usageStmt->fetchAll(PDO::FETCH_ASSOC);

$usageSummary = [];
foreach ($usageData as $u) {
  $usageSummary[$u['purchase_id']] = $u;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ใช้วัตถุดิบ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
    }
    .card-custom {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .section-title {
      font-size: 1.8rem;
      font-weight: 600;
    }
    .label-title {
      font-weight: 500;
      color: #555;
    }
    .form-section {
      background: #ffffff;
      padding: 1.5rem;
      border-radius: 0.75rem;
    }
    .back-btn {
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="section-title">🍳 ใช้วัตถุดิบ</h3>
    <a href="list.php" class="btn btn-outline-dark back-btn">🔙 กลับหน้าเดิม</a>
  </div>

  <?php foreach ($purchases as $item): 
    $used = $usageSummary[$item['id']]['total_used'] ?? 0;
    $meals = $usageSummary[$item['id']]['meals_used'] ?? 0;
    $people = $usageSummary[$item['id']]['total_people'] ?? 0;

    $unit_sub = $item['unit_sub'] ?? null;
    $total_sub = $unit_sub ? $item['quantity'] * $unit_sub : $item['quantity'];
    $remaining = $total_sub;
  ?>

  <div class="card card-custom mb-4">
    <div class="card-body">

      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h5 class="mb-1"><?= htmlspecialchars($item['item_name']) ?></h5>
          <small class="text-muted">
            <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>
            <?= $unit_sub ? " × {$unit_sub} หน่วยย่อย" : "" ?>
          </small>
        </div>
        <div>
          <?= $item['status'] === 'หมดแล้ว' 
            ? '<span class="badge bg-danger">หมดแล้ว</span>' 
            : '<span class="badge bg-success">ยังไม่หมด</span>' ?>
        </div>
      </div>

      <div class="row text-muted mb-3">
        <div class="col-md-3 col-6 mb-2">🗓 <span class="label-title">ซื้อเมื่อ:</span> <?= date('d/m/Y', strtotime($item['purchase_date'])) ?></div>
        <div class="col-md-3 col-6 mb-2">📦 <span class="label-title">หน่วยย่อยทั้งหมด:</span> <?= $remaining ?></div>
        <div class="col-md-3 col-6 mb-2">📊 <span class="label-title">ใช้แล้ว:</span> <?= $used ?></div>
        <div class="col-md-3 col-6 mb-2">👥 <span class="label-title">คนที่กิน:</span> <?= $people ?> | 🍽 <?= $meals ?> มื้อ</div>
      </div>

      <form method="POST" action="../function/use_material_save.php" class="form-section">
        <input type="hidden" name="purchase_id" value="<?= $item['id'] ?>">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">จำนวนหน่วยย่อยที่ใช้</label>
            <input type="number" name="used_quantity" class="form-control" min="1" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">จำนวนคนที่กิน</label>
            <input type="number" name="num_people" class="form-control" min="1" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">ใช้เมื่อ (วันที่)</label>
            <input type="date" name="used_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
        </div>
        <div class="text-end mt-3">
          <button type="submit" class="btn btn-primary px-4">✅ บันทึกการใช้</button>
        </div>
      </form>

    </div>
  </div>
  <?php endforeach; ?>

  <?php if (count($purchases) === 0): ?>
    <div class="alert alert-info text-center">ไม่มีวัตถุดิบที่ยังไม่หมด</div>
  <?php endif; ?>

</div>

</body>
</html>
