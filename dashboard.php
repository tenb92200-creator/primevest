<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
requireLogin();

$user = getCurrentUser();

// Fetch stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(amount),0) as invested FROM investments WHERE user_id = ? AND status = 'active'");
$stmt->execute([$user['id']]);
$investStats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(expected_profit),0) as profit FROM investments WHERE user_id = ? AND status = 'active'");
$stmt->execute([$user['id']]);
$profit = $stmt->fetch()['profit'];

// Recent transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 8");
$stmt->execute([$user['id']]);
$transactions = $stmt->fetchAll();

// Active investments
$stmt = $pdo->prepare("
    SELECT i.*, p.name as plan_name, p.roi_percent 
    FROM investments i 
    JOIN plans p ON i.plan_id = p.id 
    WHERE i.user_id = ? AND i.status = 'active' 
    ORDER BY i.end_date ASC
");
$stmt->execute([$user['id']]);
$activeInvestments = $stmt->fetchAll();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Welcome, <?php echo sanitize($user['username']); ?>!</h2>
            <p class="text-muted mb-0">Here's your investment overview</p>
        </div>
        <div>
            <a href="deposit.php" class="btn btn-primary me-2"><i class="bi bi-plus-circle me-1"></i>Deposit</a>
            <a href="plans.php" class="btn btn-outline-primary"><i class="bi bi-graph-up me-1"></i>Invest</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Available Balance</div>
                <div class="stat-value text-primary"><?php echo formatMoney($user['balance']); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #0d6efd;">
                <div class="stat-label">Active Investments</div>
                <div class="stat-value"><?php echo $investStats['total']; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #198754;">
                <div class="stat-label">Total Invested</div>
                <div class="stat-value"><?php echo formatMoney($investStats['invested']); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #ffc107;">
                <div class="stat-label">Expected Profit</div>
                <div class="stat-value text-success">+<?php echo formatMoney($profit); ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Active Investments -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Active Investments</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($activeInvestments)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                            No active investments yet.<br>
                            <a href="plans.php" class="btn btn-sm btn-primary mt-3">Browse Plans</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Profit</th>
                                        <th>Ends</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activeInvestments as $inv): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo sanitize($inv['plan_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo $inv['roi_percent']; ?>% ROI</small>
                                        </td>
                                        <td><?php echo formatMoney($inv['amount']); ?></td>
                                        <td class="text-success">+<?php echo formatMoney($inv['expected_profit']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($inv['end_date'])); ?></td>
                                        <td><span class="badge bg-success badge-status">Active</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($transactions)): ?>
                        <div class="p-4 text-center text-muted">No transactions yet.</div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($transactions as $tx): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-<?php 
                                        echo $tx['type'] === 'deposit' ? 'success' : 
                                            ($tx['type'] === 'withdrawal' ? 'danger' : 
                                            ($tx['type'] === 'profit' ? 'warning' : 'primary')); 
                                    ?> me-2"><?php echo ucfirst($tx['type']); ?></span>
                                    <small class="text-muted"><?php echo date('M d, H:i', strtotime($tx['created_at'])); ?></small>
                                </div>
                                <strong class="<?php echo in_array($tx['type'], ['deposit','profit']) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo in_array($tx['type'], ['deposit','profit']) ? '+' : '-'; ?><?php echo formatMoney($tx['amount']); ?>
                                </strong>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
