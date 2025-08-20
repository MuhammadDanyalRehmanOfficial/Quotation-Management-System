<?php
require __DIR__ . '/../config/db.php';

// Redirect if not authenticated
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$pageTitle = "Create Quotation";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id   = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
    $quote_date    = !empty($_POST['quote_date']) ? $_POST['quote_date'] : date('Y-m-d H:i:s');
    $validity_date = $_POST['validity_date'] ?? null;
    $note          = $_POST['note'] ?? null;

    $stmt = $mysqli->prepare("INSERT INTO quotations(user_id, customer_id, quote_date, validity_date, note, status) 
                              VALUES (?, ?, ?, ?, ?, 'draft')");
    $stmt->bind_param('iisss', $userId, $customer_id, $quote_date, $validity_date, $note);
    $stmt->execute();

    $qid = $stmt->insert_id;

    header("Location: add_items.php?id=$qid");
    exit;
}

// Fetch customers
$customers = $mysqli->query("SELECT id, name FROM customers ORDER BY name ASC");

// Include layout
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    <h3 class="fw-bold mb-2 text-center">📝 Step 1: Create Quotation</h3>
                    <p class="text-muted text-center mb-4">Fill in the basic details to start your quotation draft.</p>

                    <!-- Quotation Form -->
                    <form method="post" class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">-- Choose Customer --</option>
                                    <?php while ($c = $customers->fetch_assoc()): ?>
                                        <option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <label for="customer_id">Customer</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="datetime-local" class="form-control" id="quote_date" name="quote_date"
                                       value="<?= date('Y-m-d\TH:i') ?>" required>
                                <label for="quote_date">Date & Time</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="validity_date" name="validity_date">
                                <label for="validity_date">Validity Date</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Add notes..." id="note" name="note" style="height: 120px"></textarea>
                                <label for="note">Notes</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between mt-3">
                            <a class="btn btn-outline-secondary" href="../dashboard.php">← Cancel</a>
                            <button class="btn btn-primary px-4">Next: Add Items →</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
