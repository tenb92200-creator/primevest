<?php
$pageTitle = 'Deposit';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount < 10) {
        $error = 'Minimum deposit is $10.00';
    } elseif ($amount > 100000) {
        $error = 'Maximum deposit is $100,000.00 for demo purposes.';
    } else {
        // Virtual deposit - add to balance
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user['id']]);

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'deposit', ?, 'Virtual deposit', 'completed')");
            $stmt->execute([$user['id'], $amount]);

            $pdo->commit();
            $success = 'Successfully deposited ' . formatMoney($amount) . ' to your account!';
            // Refresh user data
            $user = getCurrentUser();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Deposit failed. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-1">Deposit Funds</h3>
                    <p class="text-muted mb-4">Add virtual funds to your account (demo mode — no real money)</p>

                    <div class="alert alert-secondary mb-4">
                        Current Balance: <strong class="text-primary"><?php echo formatMoney($user['balance']); ?></strong>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" data-confirm="Confirm virtual deposit?">
                        <div class="mb-4">
                            <label class="form-label">Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" class="form-control form-control-lg" 
                                       step="0.01" min="10" max="100000" required 
                                       placeholder="e.g. 500.00" data-money>
                            </div>
                            <div class="form-text">Min $10 · Max $100,000 (demo limits)</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Deposit Now
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="fw-bold">Quick Amounts</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ([50, 100, 250, 500, 1000, 5000] as $q): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="<?php echo $q; ?>">
                                $<?php echo number_format($q); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.quick-amount').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelector('input[name="amount"]').value = this.dataset.amount;
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
