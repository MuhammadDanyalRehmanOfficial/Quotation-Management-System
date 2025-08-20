<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}
$pageTitle = "Manage Customers";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $mysqli->query("DELETE FROM customers WHERE id=$id");
    header('Location: manage_customers.php');
    exit;
}
$res = $mysqli->query("SELECT * FROM customers ORDER BY id DESC");
?>

<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <h3 class="mb-3 mb-md-0">👥 Customers</h3>
        <div class="d-flex gap-2">
            <a class="btn btn-primary btn-sm" href="add_customer.php">+ Add Customer</a>
        </div>
    </div>

    <div class="card mt-3 shadow-sm border-0 rounded-3">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res->num_rows > 0): ?>
                        <?php while ($c = $res->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= (int)$c['id'] ?></span></td>
                                <td><?= esc($c['name']) ?></td>
                                <td><?= esc($c['email']) ?></td>
                                <td><?= esc($c['phone']) ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-primary" href="edit_customer.php?id=<?= (int)$c['id'] ?>">Edit</a>
                                        <a class="btn btn-outline-danger" href="?delete=<?= (int)$c['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
