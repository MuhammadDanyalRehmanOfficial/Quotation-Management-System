<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
$pageTitle = "Add Terms";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("INSERT INTO terms(title, body) VALUES(?, ?)");
    $stmt->bind_param('ss', $_POST['title'], $_POST['body']);
    if ($stmt->execute()) {
        $msg = '✅ Terms added successfully!';
    }
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h3 class="mb-4 text-center fw-bold">📜 Add Terms & Conditions</h3>

          <?php if ($msg): ?>
            <div class="alert alert-success text-center"><?= esc($msg) ?></div>
          <?php endif; ?>

          <form method="post" class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
                <label for="title">Title</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea name="body" class="form-control" id="body" placeholder="Terms body" style="height: 150px" required></textarea>
                <label for="body">Body</label>
              </div>
            </div>
            <div class="col-12 d-flex justify-content-between mt-3">
              <a href="manage_terms.php" class="btn btn-outline-secondary">← Back</a>
              <button class="btn btn-primary px-4">💾 Save Terms</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
