<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>บันทึกรายการซื้อวัตถุดิบ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow-lg rounded-4">
    <div class="card-body">
      <h3 class="mb-4">📦 บันทึกรายการซื้อวัตถุดิบ</h3>
      <form action="function/save_purchase.php" method="POST">
        <div class="mb-3">
          <label for="item_name" class="form-label">ชื่อวัตถุดิบ</label>
          <input type="text" class="form-control" name="item_name" required>
        </div>
        <div class="mb-3">
          <label for="quantity" class="form-label">จำนวน</label>
          <input type="number" class="form-control" name="quantity" required>
        </div>
        <div class="mb-3">
          <label for="unit" class="form-label">หน่วย (เช่น ก้อน, แพ็ค, ฟอง)</label>
          <input type="text" class="form-control" name="unit" required>
        </div>
        <div class="mb-3">
          <label for="price" class="form-label">ราคาทั้งหมด (บาท)</label>
          <input type="number" class="form-control" name="price" step="0.01" required>
        </div>
        <div class="mb-3">
          <label for="purchase_date" class="form-label">วันที่ซื้อ</label>
          <input type="date" class="form-control" name="purchase_date" value="<?= date('Y-m-d') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">บันทึก</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
