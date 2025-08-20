<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = "Print Quotation";
include __DIR__ . '/../includes/header.php';

// Get Quotation ID
$qid = (int)($_GET['id'] ?? 0);

// Fetch quotation & customer details
$stmt = $mysqli->prepare("SELECT q.*, c.name AS customer_name, c.email, c.phone, c.address
                          FROM quotations q 
                          LEFT JOIN customers c ON c.id=q.customer_id
                          WHERE q.id=?");
$stmt->bind_param("i", $qid);
$stmt->execute();
$q = $stmt->get_result()->fetch_assoc();

if (!$q) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Quotation not found.</div></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch items
$items_stmt = $mysqli->prepare("SELECT * FROM quotation_items WHERE quotation_id=?");
$items_stmt->bind_param("i", $qid);
$items_stmt->execute();
$items = $items_stmt->get_result();

// Fetch terms
$terms_stmt = $mysqli->prepare("SELECT * FROM quotation_terms WHERE quotation_id=?");
$terms_stmt->bind_param("i", $qid);
$terms_stmt->execute();
$terms = $terms_stmt->get_result();

?>
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            font-size: 14px;
        }
    }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold">Quotation</h2>
            <div class="small text-muted">Reference #: <?= (int)$qid ?></div>
        </div>
        <button class="btn btn-primary no-print" onclick="window.print()">🖨 Print</button>
    </div>
    <hr>

    <!-- Customer -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted">Customer</h6>
            <div class="fw-bold"><?= esc($q['customer_name']) ?></div>
            <div class="small"><?= esc($q['email']) ?> <?= esc($q['phone'] ? ' · ' . $q['phone'] : '') ?></div>
            <div class="small"><?= nl2br(esc($q['address'])) ?></div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="small">Date: <?= esc($q['quote_date']) ?></div>
            <div class="small">Valid till: <?= esc($q['validity_date'] ?? '—') ?></div>
        </div>
    </div>

    <!-- Items -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
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
                $grand = 0;
                while ($row = $items->fetch_assoc()):
                    $lineTotal = (float)$row['qty'] * (float)$row['price'];
                    $gstAmt = ($lineTotal * (float)$row['gst_percent']) / 100;
                    $total = $lineTotal + $gstAmt;
                    $grand += $total;
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= esc($row['name_snapshot']) ?></td>
                        <td class="text-end"><?= number_format((float)$row['qty'], 2) ?></td>
                        <td class="text-end"><?= number_format((float)$row['price'], 2) ?></td>
                        <td class="text-end"><?= number_format((float)$row['gst_percent'], 2) ?></td>
                        <td class="text-end fw-bold"><?= number_format($total, 2) ?></td>
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

    <!-- Terms -->
    <?php if ($terms->num_rows > 0): ?>
        <div class="mt-4">
            <h6 class="text-muted">Terms & Conditions</h6>
            <ul class="small">
                <?php while ($t = $terms->fetch_assoc()): ?>
                    <li><strong><?= esc($t['title_snapshot']) ?>:</strong> <?= nl2br(esc($t['body_snapshot'])) ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>