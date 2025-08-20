<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
$pageTitle = "Finalize Quotation";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$qid = (int)($_GET['id'] ?? 0);
if ($qid <= 0) {
    header('Location: ../dashboard.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark final
    $mysqli->query("UPDATE quotations SET status='final' WHERE id=$qid");
    header("Location: print.php?id=" . $qid);
    exit;
}

$q = $mysqli->query("SELECT q.*, c.name AS customer_name, c.email, c.phone, c.address
                     FROM quotations q LEFT JOIN customers c ON c.id=q.customer_id
                     WHERE q.id=$qid")->fetch_assoc();
$items = $mysqli->query("SELECT * FROM quotation_items WHERE quotation_id=$qid");
$terms = $mysqli->query("SELECT * FROM quotation_terms WHERE quotation_id=$qid");
$grand = 0;
while ($r = $items->fetch_assoc()) {
    $grand += (float)$r['total'];
    $list[] = $r;
}
$items->data_seek(0);
?>
<div class="container mt-5">
    <h3 class="fw-bold mb-4 text-center">✅ Step 5: Review & Finalize Quotation</h3>

    <!-- Customer Info -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Customer</h6>
                    <div class="fw-bold"><?= esc($q['customer_name'] ?? '—') ?></div>
                    <div class="small"><?= esc($q['email'] ?? '') ?> <?= esc($q['phone'] ? ' · ' . $q['phone'] : '') ?></div>
                    <div class="small"><?= nl2br(esc($q['address'] ?? '')) ?></div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small">Quote #: <strong><?= (int)$qid ?></strong></div>
                    <div class="small">Date: <?= esc($q['quote_date']) ?></div>
                    <div class="small">Valid till: <?= esc($q['validity_date'] ?? '—') ?></div>
                    <span class="badge bg-<?= $q['status'] == 'final' ? 'success' : 'secondary' ?>"><?= esc(ucfirst($q['status'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-light fw-bold">📦 Quotation Items</div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">GST %</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $items->data_seek(0);
                    while ($it = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= esc($it['name_snapshot']) ?></td>
                            <td class="text-end"><?= esc($it['qty']) ?></td>
                            <td class="text-end"><?= number_format((float)$it['price'], 2) ?></td>
                            <td class="text-end"><?= esc($it['gst_percent']) ?></td>
                            <td class="text-end fw-bold"><?= number_format((float)$it['total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Grand Total</th>
                        <th class="text-end text-success fs-5 fw-bold"><?= number_format($grand, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Terms -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-light fw-bold">📑 Terms & Conditions</div>
        <div class="card-body">
            <ul class="small mb-0">
                <?php while ($t = $terms->fetch_assoc()): ?>
                    <li><strong><?= esc($t['title_snapshot']) ?>:</strong> <?= nl2br(esc($t['body_snapshot'])) ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Actions -->
    <form method="post" class="d-flex justify-content-between">
        <a class="btn btn-outline-secondary" href="select_terms.php?id=<?= (int)$qid ?>">⬅ Back</a>
        <button class="btn btn-success">💾 Finalize & Print</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>