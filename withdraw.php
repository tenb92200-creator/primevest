<?php
$pageTitle = 'Withdraw';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount < 10) {
        $error = 'Minimum withdrawal is $10.00';
    } elseif ($amount > $user['balance']) {
        $error = 'Insufficient balance. You only have ' . formatMoney($user['balance']);
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $user['id']]);

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'withdrawal', ?, 'Virtual withdrawal', 'completed')");
            $stmt->execute([$user['id'], $amount]);

            $pdo->commit();
            $success = 'Successfully withdrew ' . formatMoney($amount) . '. Funds removed from your virtual balance.';
            $user = getCurrentUser();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Withdrawal failed. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-1">Withdraw Funds</h3>
                    <p class="text-muted mb-4">Withdraw from your virtual balance (demo mode)</p>

                    <div class="alert alert-secondary mb-4">
                        Available Balance: <strong class="text-primary"><?php echo formatMoney($user['balance']); ?></strong>
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

                    <form method="POST" data-confirm="Confirm withdrawal from your virtual balance?">
                        <div class="mb-4">
                            <label class="form-label">Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" class="form-control form-control-lg" 
                                       step="0.01" min="10" max="<?php echo $user['balance']; ?>" required 
                                       placeholder="e.g. 100.00" data-money>
                            </div>
                            <div class="form-text">Minimum $10</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="bi bi-dash-circle me-2"></i>Withdraw Now
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
