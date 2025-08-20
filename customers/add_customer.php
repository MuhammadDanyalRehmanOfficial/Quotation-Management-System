<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}
$pageTitle = "Add Customer";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $mysqli->prepare("INSERT INTO customers(name,email,phone,address) VALUES(?,?,?,?)");
    $stmt->bind_param('ssss', $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address']);
    if ($stmt->execute()) {
        $msg = '✅ Customer added successfully!';
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h3 class="mb-4 text-center fw-bold">➕ Add Customer</h3>

                    <?php if ($msg): ?>
                        <div class="alert alert-success text-center"><?= esc($msg) ?></div>
                    <?php endif; ?>

                    <form method="post" class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input name="name" type="text" class="form-control" id="name" placeholder="John Doe" required>
                                <label for="name">Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input name="email" type="email" class="form-control" id="email" placeholder="name@example.com">
                                <label for="email">Email address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input name="phone" type="text" class="form-control" id="phone" placeholder="1234567890">
                                <label for="phone">Phone</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="address" class="form-control" id="address" placeholder="Customer address" style="height: 120px"></textarea>
                                <label for="address">Address</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between mt-3">
                            <a class="btn btn-outline-secondary" href="manage_customers.php">← Back</a>
                            <button class="btn btn-primary px-4">💾 Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
