<?php
$pageTitle = 'Invest';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

$plan_id = intval($_GET['plan_id'] ?? $_POST['plan_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ? AND is_active = 1");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    header('Location: plans.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount < $plan['min_amount'] || $amount > $plan['max_amount']) {
        $error = 'Amount must be between ' . formatMoney($plan['min_amount']) . ' and ' . formatMoney($plan['max_amount']);
    } elseif ($amount > $user['balance']) {
        $error = 'Insufficient balance. Please <a href="deposit.php">deposit</a> more funds.';
    } else {
        $profit = $amount * ($plan['roi_percent'] / 100);
        $start = date('Y-m-d H:i:s');
        $end = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));

        $pdo->beginTransaction();
        try {
            // Deduct balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $user['id']]);

            // Create investment
            $stmt = $pdo->prepare("INSERT INTO investments (user_id, plan_id, amount, expected_profit, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$user['id'], $plan['id'], $amount, $profit, $start, $end]);

            // Transaction log
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'investment', ?, ?, 'completed')");
            $stmt->execute([$user['id'], $amount, 'Invested in ' . $plan['name']]);

            $pdo->commit();
            $success = 'Successfully invested ' . formatMoney($amount) . ' in ' . sanitize($plan['name']) . '! Expected profit: ' . formatMoney($profit);
            $user = getCurrentUser();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Investment failed. Please try again.';
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-1">Invest in <?php echo sanitize($plan['name']); ?></h3>
                    <p class="text-muted mb-4"><?php echo sanitize($plan['description']); ?></p>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">ROI</small>
                                <strong class="text-primary fs-4"><?php echo number_format($plan['roi_percent'], 1); ?>%</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Duration</small>
                                <strong class="fs-4"><?php echo $plan['duration_days']; ?> days</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Min Amount</small>
                                <strong><?php echo formatMoney($plan['min_amount']); ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Max Amount</small>
                                <strong><?php echo formatMoney($plan['max_amount']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-secondary">
                        Your Balance: <strong class="text-primary"><?php echo formatMoney($user['balance']); ?></strong>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                            <div class="mt-3">
                                <a href="dashboard.php" class="btn btn-sm btn-primary">Go to Dashboard</a>
                                <a href="plans.php" class="btn btn-sm btn-outline-primary">Invest More</a>
                            </div>
                        </div>
                    <?php else: ?>
                    <form method="POST" data-confirm="Confirm this investment? Your balance will be deducted immediately.">
                        <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                        <div class="mb-4">
                            <label class="form-label">Investment Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="amount" class="form-control form-control-lg" 
                                       step="0.01" min="<?php echo $plan['min_amount']; ?>" 
                                       max="<?php echo min($plan['max_amount'], $user['balance']); ?>" 
                                       required data-money placeholder="Enter amount">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-graph-up-arrow me-2"></i>Confirm Investment
                            </button>
                            <a href="plans.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
