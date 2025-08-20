<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
  header('Location: /index.php');
  exit;
}
$pageTitle = "Add Item";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $mysqli->prepare("INSERT INTO items(name,category,cost_price,sale_price,gst_percent,barcode) VALUES(?,?,?,?,?,?)");
  $stmt->bind_param('ssddds', $_POST['name'], $_POST['category'], $_POST['cost_price'], $_POST['sale_price'], $_POST['gst_percent'], $_POST['barcode']);
  if ($stmt->execute()) $msg = '✅ Item added successfully!';
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h3 class="mb-4 text-center fw-bold">📦 Add Item</h3>

          <?php if ($msg): ?><div class="alert alert-success text-center"><?= esc($msg) ?></div><?php endif; ?>

          <form method="post" class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input name="name" type="text" class="form-control" id="name" placeholder="Item Name" required>
                <label for="name">Item Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="category" type="text" class="form-control" id="category" placeholder="Category">
                <label for="category">Category</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="cost_price" type="number" step="0.01" class="form-control" id="cost_price" value="0">
                <label for="cost_price">Cost Price</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="sale_price" type="number" step="0.01" class="form-control" id="sale_price" value="0">
                <label for="sale_price">Sale Price</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="gst_percent" type="number" step="0.01" class="form-control" id="gst_percent" value="0">
                <label for="gst_percent">GST %</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="barcode" type="text" class="form-control" id="barcode" placeholder="Barcode">
                <label for="barcode">Barcode</label>
              </div>
            </div>
            <div class="col-12 d-flex justify-content-between mt-3">
              <a class="btn btn-outline-secondary" href="manage_items.php">← Back</a>
              <button class="btn btn-primary px-4">💾 Save Item</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>