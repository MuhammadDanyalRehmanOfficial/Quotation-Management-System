<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) { header('Location: ../index.php'); exit; }
$pageTitle = "Manage Items";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $mysqli->query("DELETE FROM items WHERE id=$id");
    header('Location: manage_items.php');
    exit;
}
$res = $mysqli->query("SELECT * FROM items ORDER BY id DESC");
?>

<div class="container mt-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <h3 class="mb-3 mb-md-0">📦 Items</h3>
    <a class="btn btn-primary btn-sm" href="add_item.php">+ Add Item</a>
  </div>

  <div class="card mt-3 shadow-sm border-0 rounded-3">
    <div class="card-body table-responsive p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:50px;">#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Sale Price</th>
            <th>GST %</th>
            <th>Barcode</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res->num_rows > 0): ?>
            <?php while ($i = $res->fetch_assoc()): ?>
              <tr>
                <td><span class="badge bg-secondary"><?= (int)$i['id'] ?></span></td>
                <td><?= esc($i['name']) ?></td>
                <td><?= esc($i['category']) ?></td>
                <td>₹<?= number_format($i['sale_price'], 2) ?></td>
                <td><?= esc($i['gst_percent']) ?>%</td>
                <td><?= esc($i['barcode']) ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <a class="btn btn-outline-primary" href="edit_item.php?id=<?= (int)$i['id'] ?>">Edit</a>
                    <a class="btn btn-outline-danger" href="?delete=<?= (int)$i['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No items found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
