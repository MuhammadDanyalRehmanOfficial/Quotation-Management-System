<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: manage_customers.php'); exit; }

$pageTitle = "Edit Customer";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
$stmt = $mysqli->prepare("SELECT * FROM customers WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$cust = $stmt->get_result()->fetch_assoc();

if (!$cust) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Customer not found.</div></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param('ssssi', $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $id);
    if ($stmt->execute()) {
        $msg = '✅ Customer updated successfully!';
        // refresh data
        $cust = array_merge($cust, $_POST);
    }
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h3 class="mb-4 text-center fw-bold">✏️ Edit Customer</h3>

          <?php if ($msg): ?>
            <div class="alert alert-success text-center"><?= esc($msg) ?></div>
          <?php endif; ?>

          <form method="post" class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input name="name" type="text" class="form-control" id="name" value="<?= esc($cust['name']) ?>" required>
                <label for="name">Full Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="email" type="email" class="form-control" id="email" value="<?= esc($cust['email']) ?>">
                <label for="email">Email address</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input name="phone" type="text" class="form-control" id="phone" value="<?= esc($cust['phone']) ?>">
                <label for="phone">Phone</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea name="address" class="form-control" id="address" style="height: 120px"><?= esc($cust['address']) ?></textarea>
                <label for="address">Address</label>
              </div>
            </div>

            <div class="col-12 d-flex justify-content-between mt-3">
              <a class="btn btn-outline-secondary" href="manage_customers.php">← Back</a>
              <button class="btn btn-primary px-4">💾 Update Customer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
