<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../config/db.php');

$stmt = $conn->prepare("SELECT * FROM purchases ORDER BY purchase_date DESC, id DESC");
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_price = 0;
foreach ($purchases as $row) {
    $total_price += $row['price'];
}
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
    <div>
      <a href="add_purchase.php" class="btn btn-success">➕ เพิ่มรายการใหม่</a>
      <a href="use_material.php" class="btn btn-primary">🍳 ใช้วัตถุดิบ</a>
      <a href="summary.php" class="btn btn-primary">🍳 สรุป</a>
    </div>
  </div>

  <?php if (count($purchases) > 0): ?>
    <div class="table-responsive shadow rounded-4">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
          <tr>
            <th>#</th>
            <th>ชื่อวัตถุดิบ</th>
            <th>จำนวน</th>
            <th>หน่วยนับ</th>
            <th>หน่วยย่อย</th> <!-- ใหม่ -->
            <th>ราคา (บาท)</th>
            <th>วันที่ซื้อ</th>
            <th>สถานะ</th>
            <th>การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($purchases as $index => $row): ?>
            <tr>
              <td class="text-center"><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($row['item_name']) ?></td>
              <td class="text-center"><?= $row['quantity'] ?></td>
              <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
              <td class="text-center">
                <?= isset($row['unit_sub']) && $row['unit_sub'] !== null ? $row['unit_sub'] : '-' ?>
              </td>
              <td class="text-end"><?= number_format($row['price'], 2) ?></td>
              <td class="text-center"><?= date('d/m/Y', strtotime($row['purchase_date'])) ?></td>
              <td class="text-center">
                <?php if ($row['status'] === 'หมดแล้ว'): ?>
                  <span class="badge bg-danger">หมดแล้ว</span>
                <?php else: ?>
                  <span class="badge bg-success">ยังไม่หมด</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                <a href="../function/delete_purchase.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณต้องการลบรายการนี้หรือไม่?')">ลบ</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <hr class="my-4">
      <div class="text-end">
        <h5><span class="text-muted">รวมทั้งหมด:</span> <span class="text-success fw-bold"><?= number_format($total_price, 2) ?> บาท</span></h5>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">ยังไม่มีรายการซื้อวัตถุดิบ</div>
  <?php endif; ?>
<!-- ---------------------------------------------- -->
<?php
// ดึงข้อมูล usage_logs พร้อม JOIN กับ purchases
$usageStmt = $conn->prepare("
  SELECT 
    p.id AS purchase_id,
    p.item_name,
    p.unit,
    p.unit_sub,
    p.quantity,
    COUNT(ul.id) AS meals_used,
    SUM(ul.used_quantity) AS total_used,
    MAX(ul.used_date) AS last_used_date
  FROM purchases p
  INNER JOIN usage_logs ul ON p.id = ul.purchase_id
  GROUP BY p.id
  ORDER BY last_used_date DESC
");
$usageStmt->execute();
$usageSummaries = $usageStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<hr class="my-5">

<h3 class="mb-3">🍳 รายการวัตถุดิบที่ถูกใช้</h3>

<?php if (count($usageSummaries) > 0): ?>
  <div class="table-responsive shadow rounded-4">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-secondary text-center">
        <tr>
          <th>#</th>
          <th>ชื่อวัตถุดิบ</th>
          <th>มื้อที่ใช้ไป</th>
          <th>ใช้ไป (หน่วยย่อย)</th>
          <th>คงเหลือ (หน่วยย่อย)</th>
          <th>หน่วยหลัก</th>
          <th>หน่วยย่อย / หน่วยหลัก</th>
          <th>วันที่ที่ใช้ล่าสุด</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usageSummaries as $index => $row): 
          $total_unit_sub = $row['unit_sub'] && $row['unit_sub'] > 0 ? $row['quantity'] * $row['unit_sub'] : $row['quantity'];
          $remaining = $total_unit_sub - $row['total_used'];
          $last_used = $row['last_used_date'] ? date('d/m/Y', strtotime($row['last_used_date'])) : '-';
        ?>
          <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td class="text-center"><?= $row['meals_used'] ?></td>
            <td class="text-center"><?= $row['total_used'] ?></td>
            <td class="text-center"><?= max($remaining, 0) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
            <td class="text-center"><?= $row['unit_sub'] ?? '-' ?></td>
            <td class="text-center"><?= $last_used ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="alert alert-info text-center">ยังไม่มีข้อมูลการใช้วัตถุดิบ</div>
<?php endif; ?>


  <!-- ------------------------------------ -->
</div>



</body>
</html>
