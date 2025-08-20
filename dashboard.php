<?php
require __DIR__ . '/config/db.php';
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
$pageTitle = "Dashboard";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

// Fetch counts
$count = fn($sql) => $mysqli->query($sql)->fetch_row()[0] ?? 0;
$customers = $count("SELECT COUNT(*) FROM customers");
$items     = $count("SELECT COUNT(*) FROM items");
$drafts    = $count("SELECT COUNT(*) FROM quotations WHERE status='draft'");
$finals    = $count("SELECT COUNT(*) FROM quotations WHERE status='final'");
?>

<div class="container mt-4">
  <h3 class="mb-4">👋 Welcome back, <span class="fw-bold"><?= esc($_SESSION['user']['name']) ?></span></h3>

  <!-- Stats Cards -->
  <div class="row g-3">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 bg-primary text-white text-center">
        <div class="card-body">
          <div class="display-6 fw-bold"><?= (int)$customers ?></div>
          <div class="text-uppercase small">Customers</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 bg-info text-white text-center">
        <div class="card-body">
          <div class="display-6 fw-bold"><?= (int)$items ?></div>
          <div class="text-uppercase small">Items</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 bg-secondary text-white text-center">
        <div class="card-body">
          <div class="display-6 fw-bold"><?= (int)$drafts ?></div>
          <div class="text-uppercase small">Drafts</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 bg-success text-white text-center">
        <div class="card-body">
          <div class="display-6 fw-bold"><?= (int)$finals ?></div>
          <div class="text-uppercase small">Final Quotations</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Quotations -->
  <div class="card mt-5 shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">📄 Recent Quotations</h5>
      <a class="btn btn-sm btn-primary" href="quotations/create_quotation.php">+ New Quotation</a>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Updated</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT q.id, q.status, q.updated_at, c.name AS customer_name
                  FROM quotations q LEFT JOIN customers c ON c.id=q.customer_id
                  ORDER BY q.updated_at DESC LIMIT 10";
          $res = $mysqli->query($sql);
          if ($res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
          ?>
            <tr>
              <td><?= (int)$row['id'] ?></td>
              <td><?= esc($row['customer_name'] ?? '—') ?></td>
              <td>
                <span class="badge bg-<?= $row['status']=='final'?'success':'secondary' ?>">
                  <?= ucfirst(esc($row['status'])) ?>
                </span>
              </td>
              <td><?= esc($row['updated_at']) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="quotations/add_items.php?id=<?= (int)$row['id'] ?>">Open</a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="5" class="text-center text-muted">No quotations found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
