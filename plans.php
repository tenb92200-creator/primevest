<?php
$pageTitle = 'Investment Plans';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY min_amount ASC");
$plans = $stmt->fetchAll();
?>

<div class="container">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Investment Plans</h2>
        <p class="text-muted">Select a plan that fits your investment goals. All returns are calculated automatically.</p>
    </div>

    <div class="row g-4">
        <?php foreach ($plans as $i => $plan): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card card-plan h-100 <?php echo $i === 1 ? 'featured' : ''; ?>">
                <div class="card-body p-4 d-flex flex-column">
                    <h4 class="fw-bold"><?php echo sanitize($plan['name']); ?></h4>
                    <p class="text-muted small flex-grow-1"><?php echo sanitize($plan['description']); ?></p>
                    
                    <div class="my-3">
                        <span class="display-5 fw-bold text-primary"><?php echo number_format($plan['roi_percent'], 1); ?>%</span>
                        <div class="text-muted small">Return on Investment</div>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-cash-stack text-primary me-2"></i>
                            <?php echo formatMoney($plan['min_amount']); ?> – <?php echo formatMoney($plan['max_amount']); ?>
                        </li>
                        <li class="mb-2"><i class="bi bi-calendar-check text-primary me-2"></i>
                            <?php echo $plan['duration_days']; ?> days duration
                        </li>
                        <li class="mb-2"><i class="bi bi-percent text-primary me-2"></i>
                            Profit paid at maturity
                        </li>
                    </ul>

                    <?php if (isLoggedIn()): ?>
                        <a href="invest.php?plan_id=<?php echo $plan['id']; ?>" class="btn btn-primary w-100 mt-auto">
                            Invest Now
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-outline-primary w-100 mt-auto">
                            Register to Invest
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="alert alert-info mt-5">
        <i class="bi bi-info-circle me-2"></i>
        <strong>How it works:</strong> Choose a plan → Enter amount within the range → Confirm. 
        Your balance is deducted immediately. At the end of the duration your capital + profit is returned (in this demo the maturity is simulated for testing).
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
