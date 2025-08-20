<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: manage_terms.php'); exit; }

$pageTitle = "Edit Term";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
$stmt = $mysqli->prepare("SELECT * FROM terms WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$term = $stmt->get_result()->fetch_assoc();

if (!$term) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Term not found.</div></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("UPDATE terms SET title=?, body=? WHERE id=?");
    $stmt->bind_param('ssi', $_POST['title'], $_POST['body'], $id);
    if ($stmt->execute()) {
        $msg = '✅ Term updated successfully!';
        $term = array_merge($term, $_POST);
    }
}
?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h3 class="mb-4 text-center fw-bold">✏️ Edit Term</h3>

          <?php if ($msg): ?><div class="alert alert-success text-center"><?= esc($msg) ?></div><?php endif; ?>

          <form method="post" class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" name="title" class="form-control" id="title" value="<?= esc($term['title']) ?>" required>
                <label for="title">Title</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea name="body" class="form-control" id="body" style="height: 150px" required><?= esc($term['body']) ?></textarea>
                <label for="body">Body</label>
              </div>
            </div>

            <div class="col-12 d-flex justify-content-between mt-3">
              <a href="manage_terms.php" class="btn btn-outline-secondary">← Back</a>
              <button class="btn btn-primary px-4">💾 Update Term</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
