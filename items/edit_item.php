<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: manage_items.php'); exit; }

$pageTitle = "Edit Item";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
$stmt = $mysqli->prepare("SELECT * FROM items WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
  echo "<div class='container mt-4'><div class='alert alert-danger'>Item not found.</div></div>";
  include __DIR__ . '/../includes/footer.php';
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $mysqli->prepare("UPDATE items SET name=?, category=?, cost_price=?, sale_price=?, gst_percent=?, barcode=? WHERE id=?");
  $stmt->bind_param('ssddssi', $_POST['name'], $_POST['category'], $_POST['cost_price'], $_POST['sale_price'], $_POST['gst_percent'], $_POST['barcode'], $id);
  if ($stmt->execute()) {
    $msg = '✅ Item updated successfully!';
    $item = array_merge($item, $_POST);
  }
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h3 class="mb-4 text-center fw-bold">✏️ Edit Item</h3>

          <?php if ($msg): ?><div class="alert alert-success text-center"><?= esc($msg) ?></div><?php endif; ?>

          <form method="post" class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input name="name" type="text" class="form-control" id="name" value="<?= esc($item['name']) ?>" required>
                <label for="name">Item Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="category" type="text" class="form-control" id="category" value="<?= esc($item['category']) ?>">
                <label for="category">Category</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="cost_price" type="number" step="0.01" class="form-control" id="cost_price" value="<?= esc($item['cost_price']) ?>">
                <label for="cost_price">Cost Price</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="sale_price" type="number" step="0.01" class="form-control" id="sale_price" value="<?= esc($item['sale_price']) ?>">
                <label for="sale_price">Sale Price</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input name="gst_percent" type="number" step="0.01" class="form-control" id="gst_percent" value="<?= esc($item['gst_percent']) ?>">
                <label for="gst_percent">GST %</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="barcode" type="text" class="form-control" id="barcode" value="<?= esc($item['barcode']) ?>">
                <label for="barcode">Barcode</label>
              </div>
            </div>

            <div class="col-12 d-flex justify-content-between mt-3">
              <a class="btn btn-outline-secondary" href="manage_items.php">← Back</a>
              <button class="btn btn-primary px-4">💾 Update Item</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
