<?php
require __DIR__ . '/config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (!$name || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check for duplicate email
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email is already registered.';
        } else {
            // All good, insert new user
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insert = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert->bind_param('sss', $name, $email, $hashedPassword);
            if ($insert->execute()) {
                header('Location: index.php?registered=1');
                exit;
            } else {
                $error = 'Error registering user.';
            }
        }
    }
}

$pageTitle = "Register - QMS";
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 450px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">📝 Create Account</h2>
                <p class="text-muted">Join QMS to manage your quotations</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= esc($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= esc($success) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="John Doe" required value="<?= esc($_POST['name'] ?? '') ?>">
                    <label for="name">Full Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required value="<?= esc($_POST['email'] ?? '') ?>">
                    <label for="email">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <label for="confirm_password">Confirm Password</label>
                </div>

                <button class="btn btn-success w-100 py-2 fw-semibold rounded-3">Register</button>
            </form>

            <div class="text-center mt-4 small">
                <span class="text-muted">Already have an account?</span>
                <a href="index.php" class="fw-semibold">Login here</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>