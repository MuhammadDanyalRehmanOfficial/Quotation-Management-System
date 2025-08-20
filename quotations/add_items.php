<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
$pageTitle = "Add Items";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$qid = (int)($_GET['id'] ?? 0);
if ($qid <= 0) {
    header('Location: ../dashboard.php');
    exit;
}

$q = $mysqli->query("SELECT q.*, c.name AS customer_name, c.email, c.phone, c.address
                     FROM quotations q LEFT JOIN customers c ON c.id=q.customer_id
                     WHERE q.id=$qid");
$quote = $q->fetch_assoc();
if (!$quote) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Quotation not found.</div></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Handle add item by ID or barcode (server-side fallback for non-JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item_id'])) {
    $itemId = (int)$_POST['add_item_id'];
    $i = $mysqli->query("SELECT * FROM items WHERE id=$itemId")->fetch_assoc();
    if ($i) {
        $price = (float)$i['sale_price'];
        $gst   = (float)$i['gst_percent'];
        $name  = $i['name'];
        $qty   = 1;
        $total = $price * $qty;
        $stmt = $mysqli->prepare("INSERT INTO quotation_items(quotation_id,item_id,name_snapshot,price,qty,gst_percent,total) VALUES(?,?,?,?,?,?,?)");
        $stmt->bind_param('iissddd', $qid, $itemId, $name, $price, $qty, $gst, $total);
        $stmt->execute();
    }
    header("Location: add_items.php?id=" . $qid);
    exit;
}

// Fetch items for listing
$items = $mysqli->query("SELECT id,name,barcode,sale_price,gst_percent FROM items ORDER BY name ASC");

// Fetch quote items
$qi = $mysqli->query("SELECT qi.*, i.barcode FROM quotation_items qi LEFT JOIN items i ON i.id=qi.item_id WHERE quotation_id=$qid ORDER BY qi.id DESC");
?>
<div class="container mt-5">
    <!-- Step Title -->
    <h3 class="fw-bold mb-4 text-center">📦 Step 2: Add Items to Quotation</h3>

    <!-- Customer & Quotation Info -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Customer</h6>
                    <div class="fw-bold"><?= esc($quote['customer_name'] ?? '—') ?></div>
                    <div class="small"><?= esc($quote['email'] ?? '') ?> <?= esc($quote['phone'] ? '· ' . $quote['phone'] : '') ?></div>
                    <div class="small"><?= nl2br(esc($quote['address'] ?? '')) ?></div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small text-muted">Quote #<?= (int)$qid ?></div>
                    <div class="small">Date: <?= esc($quote['quote_date']) ?></div>
                    <div class="small">Valid till: <?= esc($quote['validity_date'] ?? '—') ?></div>
                    <span class="badge bg-<?= $quote['status'] == 'final' ? 'success' : 'secondary' ?>"><?= esc(ucfirst($quote['status'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Section -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-light fw-bold">➕ Add Item</div>
        <div class="card-body p-4">
            <form method="post" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search by Name</label>
                    <select class="form-select" name="add_item_id">
                        <?php while ($it = $items->fetch_assoc()): ?>
                            <option value="<?= (int)$it['id'] ?>">
                                <?= esc($it['name']) ?> (<?= esc($it['barcode']) ?>) — ₹<?= esc($it['sale_price']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Add</button>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scan / Enter Barcode</label>
                    <input id="barcodeInput" class="form-control" placeholder="Type barcode & press Enter">
                </div>
            </form>
        </div>
    </div>

    <!-- Quotation Items Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">🧾 Quotation Items</span>
            <a class="btn btn-sm btn-success" href="select_terms.php?id=<?= (int)$qid ?>">Next: Terms →</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Barcode</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">GST %</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="qItemsTbody">
                        <?php
                        $grand = 0.0;
                        while ($row = $qi->fetch_assoc()):
                            $grand += (float)$row['total'];
                        ?>
                            <tr data-id="<?= (int)$row['id'] ?>">
                                <td><?= (int)$row['id'] ?></td>
                                <td><?= esc($row['name_snapshot']) ?></td>
                                <td><?= esc($row['barcode']) ?></td>
                                <td class="text-end">
                                    <input class="form-control form-control-sm text-end qtyIn" type="number" step="0.01" value="<?= esc($row['qty']) ?>">
                                </td>
                                <td class="text-end">
                                    <input class="form-control form-control-sm text-end priceIn" type="number" step="0.01" value="<?= esc($row['price']) ?>">
                                </td>
                                <td class="text-end">
                                    <input class="form-control form-control-sm text-end gstIn" type="number" step="0.01" value="<?= esc($row['gst_percent']) ?>">
                                </td>
                                <td class="text-end totalCell"><?= number_format((float)$row['total'], 2) ?></td>
                                <td><button class="btn btn-sm btn-outline-danger delBtn">✖</button></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="6" class="text-end">Grand Total</td>
                            <td class="text-end" id="grandCell"><?= number_format($grand, 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="p-3 small text-muted border-top">
                💾 Draft auto-saves every 10s and on tab close.
            </div>
        </div>
    </div>
</div>

<script>
    async function postJSON(url, data) {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        });
        return res.json();
    }

    const tbody = document.getElementById('qItemsTbody');
    const grandCell = document.getElementById('grandCell');

    // Recalculate grand total
    function recalc() {
        let grand = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qtyIn').value || 0);
            const price = parseFloat(tr.querySelector('.priceIn').value || 0);
            const gst = parseFloat(tr.querySelector('.gstIn').value || 0);
            const subtotal = qty * price;
            const totalWithGst = subtotal + (subtotal * gst / 100);
            tr.querySelector('.totalCell').innerText = totalWithGst.toFixed(2);
            grand += totalWithGst;
        });
        grandCell.innerText = grand.toFixed(2);
    }

    // Barcode quick add (injects row)
    document.getElementById('barcodeInput').addEventListener('keydown', async (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = e.target.value.trim();
            if (barcode) {
                const res = await postJSON('ajax_actions.php', {
                    action: 'add_by_barcode',
                    qid: <?= $qid ?>,
                    barcode
                });
                if (res.ok && res.item) {
                    addRow(res.item);
                    recalc();
                    e.target.value = '';
                } else {
                    alert(res.error || 'Failed to add.');
                }
            }
        }
    });

    // Function to add a new row dynamically
    function addRow(item) {
        const tr = document.createElement('tr');
        tr.dataset.id = item.id;
        tr.innerHTML = `
        <td>${item.id}</td>
        <td>${item.name}</td>
        <td>${item.barcode || ''}</td>
        <td class="text-end"><input class="form-control form-control-sm w-75 ms-auto qtyIn" type="number" step="0.01" value="${item.qty}"></td>
        <td class="text-end"><input class="form-control form-control-sm w-75 ms-auto priceIn" type="number" step="0.01" value="${item.price}"></td>
        <td class="text-end"><input class="form-control form-control-sm w-75 ms-auto gstIn" type="number" step="0.01" value="${item.gst}"></td>
        <td class="text-end totalCell">${item.total.toFixed(2)}</td>
        <td><button class="btn btn-sm btn-outline-danger delBtn">Delete</button></td>
    `;
        tbody.prepend(tr);
    }

    // Qty/Price/GST change handlers
    tbody.addEventListener('input', e => {
        if (e.target.matches('.qtyIn,.priceIn,.gstIn')) {
            recalc();
            debouncedSave();
        }
    });

    // Delete row (AJAX + UI remove)
    tbody.addEventListener('click', async (e) => {
        if (e.target.matches('.delBtn')) {
            const tr = e.target.closest('tr');
            const id = tr.dataset.id;
            const res = await postJSON('ajax_actions.php', {
                action: 'delete_item',
                id
            });
            if (res.ok) {
                tr.remove();
                recalc();
            } else {
                alert(res.error || 'Delete failed');
            }
        }
    });

    // Debounced autosave
    let saveTimer = null;

    function debouncedSave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(doAutosave, 1000);
    }
    async function collectRows() {
        return Array.from(tbody.querySelectorAll('tr')).map(tr => ({
            id: parseInt(tr.dataset.id),
            qty: parseFloat(tr.querySelector('.qtyIn').value || 0),
            price: parseFloat(tr.querySelector('.priceIn').value || 0),
            gst: parseFloat(tr.querySelector('.gstIn').value || 0)
        }));
    }
    async function doAutosave() {
        const payload = {
            action: 'autosave',
            qid: <?= $qid ?>,
            items: await collectRows()
        };
        await postJSON('ajax_actions.php', payload);
    }

    // Periodic autosave
    setInterval(doAutosave, 10000);

    // Save on close
    window.addEventListener('beforeunload', (e) => {
        const data = JSON.stringify({
            action: 'autosave',
            qid: <?= $qid ?>,
            items: Array.from(tbody.querySelectorAll('tr')).map(tr => ({
                id: parseInt(tr.dataset.id),
                qty: parseFloat(tr.querySelector('.qtyIn').value || 0),
                price: parseFloat(tr.querySelector('.priceIn').value || 0),
                gst: parseFloat(tr.querySelector('.gstIn').value || 0)
            }))
        });
        navigator.sendBeacon('ajax_beacon.php', data);
    });
</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>