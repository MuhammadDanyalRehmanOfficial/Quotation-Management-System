<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
  header('Location: ../index.php');
  exit;
}
$pageTitle = "Manage Terms";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $mysqli->query("DELETE FROM terms WHERE id=$id");
  header('Location: manage_terms.php');
  exit;
}
$res = $mysqli->query("SELECT * FROM terms ORDER BY id DESC");
?>

<div class="container mt-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <h3 class="mb-3 mb-md-0">📜 Terms & Conditions</h3>
    <a class="btn btn-primary btn-sm" href="add_terms.php">+ Add Terms</a>
  </div>

  <div class="card mt-3 shadow-sm border-0 rounded-3">
    <div class="card-body table-responsive p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:50px;">#</th>
            <th>Title</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res->num_rows > 0): ?>
            <?php while ($t = $res->fetch_assoc()): ?>
              <tr>
                <td><span class="badge bg-secondary"><?= (int)$t['id'] ?></span></td>
                <td><?= esc($t['title']) ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-primary" href="edit_terms.php?id=<?= (int)$t['id'] ?>">Edit</a>
                    <a class="btn btn-outline-danger" href="?delete=<?= (int)$t['id'] ?>" onclick="return confirm('Delete this term?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-center text-muted py-4">No terms found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>