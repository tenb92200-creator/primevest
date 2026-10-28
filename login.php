<?php
$pageTitle = 'Login';
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = sanitize($_POST['login'] ?? ''); // username or email
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Please enter username/email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>

<div class="container">
    <div class="card auth-card shadow">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Welcome Back</h3>
                <p class="text-muted">Login to your PrimeVest account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="login" class="form-control" required autofocus value="<?php echo isset($_POST['login']) ? sanitize($_POST['login']) : ''; ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
            </form>

            <p class="text-center mt-4 mb-0 text-muted">
                Don't have an account? <a href="register.php" class="text-primary">Register</a>
            </p>

            <div class="alert alert-info mt-4 small">
                <strong>Demo:</strong> Create a new account or use any registered user.<br>
                After registration you start with $0 balance — use Deposit to add virtual funds.
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
