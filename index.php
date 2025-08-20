<?php
// index.php
require __DIR__ . '/config/db.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare("SELECT id, name, email, password FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($u = $res->fetch_assoc()) {
        if (password_verify($pass, $u['password'])) {
            $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email']];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Invalid email or password';
}
$pageTitle = "Login - QMS";
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">🔑 QMS Login</h2>
                <p class="text-muted">Welcome back! Please sign in.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= esc($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required value="<?= esc($_POST['email'] ?? '') ?>">
                    <label for="email">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>

                <button class="btn btn-primary w-100 py-2 fw-semibold rounded-3">Login</button>
            </form>

            <div class="text-center mt-4 small">
                <span class="text-muted">Don't have an account?</span>
                <a href="register.php" class="fw-semibold">Register here</a>
            </div>

            <div class="mt-3 small text-muted text-center">
                Demo: <code>admin@example.com / admin123</code>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
